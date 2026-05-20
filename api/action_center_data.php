<?php
require_once '../config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

$where_customer = ($customer_id > 0) ? "customer_id = ? AND" : "";

// 1. High Spend, Zero Orders (Bleeding)
// Spend > $20 and Orders = 0 in the period
$sql_bleeding = "SELECT campaign_name, targeting, SUM(spend) as spend, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr
    FROM amazon_advertising_sp
    WHERE $where_customer report_date BETWEEN ? AND ? AND report_type = 'targeting'
    GROUP BY campaign_name, targeting
    HAVING SUM(spend) > 20 AND SUM(total_orders) = 0
    ORDER BY spend DESC
    LIMIT 20";

$stmt = $conn->prepare($sql_bleeding);
if ($customer_id > 0) {
    $stmt->bind_param("iss", $customer_id, $from_date, $to_date);
} else {
    $stmt->bind_param("ss", $from_date, $to_date);
}
$stmt->execute();
$bleeding = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 2. High ACOS (> 50%)
$sql_acos = "SELECT campaign_name, SUM(spend) as spend, SUM(total_sales) as total_sales, SUM(total_orders) as total_orders, AVG(acos) as acos, AVG(roas) as roas
    FROM amazon_advertising_sp
    WHERE $where_customer report_date BETWEEN ? AND ? AND report_type = 'targeting'
    GROUP BY campaign_name
    HAVING SUM(total_sales) > 0 AND (SUM(spend) / SUM(total_sales)) * 100 > 50
    ORDER BY acos DESC
    LIMIT 20";

$stmt = $conn->prepare($sql_acos);
if ($customer_id > 0) {
    $stmt->bind_param("iss", $customer_id, $from_date, $to_date);
} else {
    $stmt->bind_param("ss", $from_date, $to_date);
}
$stmt->execute();
$high_acos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 3. High Impressions, Low CTR (Targeting Issue)
$sql_ctr = "SELECT targeting, campaign_name, SUM(impressions) as impressions, SUM(clicks) as clicks, (SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100 as ctr
    FROM amazon_advertising_sp
    WHERE $where_customer report_date BETWEEN ? AND ? AND report_type = 'targeting'
    GROUP BY targeting, campaign_name
    HAVING SUM(impressions) > 1000 AND (SUM(clicks) / SUM(impressions)) * 100 < 0.2
    ORDER BY impressions DESC
    LIMIT 20";
$stmt = $conn->prepare($sql_ctr);
if ($customer_id > 0) $stmt->bind_param("iss", $customer_id, $from_date, $to_date);
else $stmt->bind_param("ss", $from_date, $to_date);
$stmt->execute();
$low_ctr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 4. Low Buy Box % (Operational Issue)
$sql_bb = "SELECT asin, MAX(title) as title, AVG(buy_box_percentage) as buy_box
    FROM amazon_detail_report
    WHERE $where_customer report_date BETWEEN ? AND ?
    GROUP BY asin
    HAVING AVG(buy_box_percentage) < 70
    ORDER BY buy_box ASC
    LIMIT 20";
$stmt = $conn->prepare($sql_bb);
if ($customer_id > 0) $stmt->bind_param("iss", $customer_id, $from_date, $to_date);
else $stmt->bind_param("ss", $from_date, $to_date);
$stmt->execute();
$low_bb = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// 5. High ROAS Keywords (Scaling Opportunity)
$sql_roas = "SELECT targeting, campaign_name, SUM(spend) as spend, SUM(total_sales) as total_sales, (SUM(total_sales) / NULLIF(SUM(spend), 0)) as roas
    FROM amazon_advertising_sp
    WHERE $where_customer report_date BETWEEN ? AND ? AND report_type = 'targeting'
    GROUP BY targeting, campaign_name
    HAVING SUM(total_sales) > 50 AND (SUM(total_sales) / SUM(spend)) > 4
    ORDER BY roas DESC
    LIMIT 20";
$stmt = $conn->prepare($sql_roas);
if ($customer_id > 0) $stmt->bind_param("iss", $customer_id, $from_date, $to_date);
else $stmt->bind_param("ss", $from_date, $to_date);
$stmt->execute();
$high_roas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'bleeding' => $bleeding,
    'high_acos' => $high_acos,
    'low_ctr' => $low_ctr,
    'low_bb' => $low_bb,
    'high_roas' => $high_roas
]);
