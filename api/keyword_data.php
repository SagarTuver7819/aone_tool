<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $customer_id = $_SESSION['customer_id'] ?? 0;
    if (($_SESSION['role'] ?? '') !== 'customer' && isset($_GET['customer_id'])) {
        $customer_id = intval($_GET['customer_id']);
    }

    $from_date = $_GET['from_date'] ?? date('Y-m-01');
    $to_date = $_GET['to_date'] ?? date('Y-m-t');

    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

    $report_type = $_GET['report_type'] ?? 'targeting';
    if (!in_array($report_type, ['targeting', 'search_term'])) $report_type = 'targeting';

    $brand = mysqli_real_escape_string($conn, $_GET['brand'] ?? '');
    $traffic_type = $_GET['traffic_type'] ?? 'all';

    $where_brand = "1=1";
    if (!empty($brand)) {
        if ($traffic_type === 'non_branded') {
            $where_brand = "(targeting NOT LIKE '%$brand%' AND campaign_name NOT LIKE '%$brand%')";
        } else {
            // Default or 'branded': Show the selected brand
            $where_brand = "(targeting LIKE '%$brand%' OR campaign_name LIKE '%$brand%')";
        }
    }

    $sql = "SELECT 
                targeting,
                match_type,
                MAX(campaign_name) as campaign,
                MAX(ad_group_name) as ad_group,
                SUM(impressions) as impressions,
                SUM(clicks) as clicks,
                SUM(spend) as spend,
                SUM(total_sales) as sales,
                SUM(total_orders) as orders
            FROM (
                SELECT targeting, match_type, campaign_name, ad_group_name, impressions, clicks, spend, total_sales, total_orders FROM amazon_advertising_sp WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = ?
                UNION ALL
                SELECT targeting, match_type, campaign_name, ad_group_name, impressions, clicks, spend, total_sales, total_orders FROM amazon_advertising_sb WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = ?
                UNION ALL
                SELECT targeting, match_type, campaign_name, ad_group_name, impressions, clicks, spend, total_sales, total_orders FROM amazon_advertising_sd WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = ?
            ) as combined
            GROUP BY targeting, match_type
            ORDER BY spend DESC
            LIMIT 500";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $from_date, $to_date, $report_type, $from_date, $to_date, $report_type, $from_date, $to_date, $report_type);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $row['ctr'] = $row['impressions'] > 0 ? ($row['clicks'] / $row['impressions']) * 100 : 0;
        $row['cpc'] = $row['clicks'] > 0 ? ($row['spend'] / $row['clicks']) : 0;
        $row['acos'] = $row['sales'] > 0 ? ($row['spend'] / $row['sales']) * 100 : 0;
        $row['roas'] = $row['spend'] > 0 ? ($row['sales'] / $row['spend']) : 0;
        $data[] = $row;
    }

    echo json_encode(['data' => $data]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
