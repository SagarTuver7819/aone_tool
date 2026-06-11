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
    $session_customer_id = $_SESSION['customer_id'] ?? 0;
    $requested_customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
    
    if (($_SESSION['role'] ?? '') === 'customer') {
        $customer_id = $session_customer_id;
    } else {
        $customer_id = $requested_customer_id;
    }

    $from_date = $_GET['from_date'] ?? date('Y-m-01');
    $to_date = $_GET['to_date'] ?? date('Y-m-d');

    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

    // 1. Search Query Performance
    $sql_sqp = "SELECT * FROM amazon_brand_reports WHERE $where_customer AND report_type = 'search_query' AND report_date BETWEEN ? AND ? ORDER BY report_date DESC LIMIT 50";
    $stmt = $conn->prepare($sql_sqp);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $sqp_res = $stmt->get_result();
    $search_queries = [];
    while($row = $sqp_res->fetch_assoc()) {
        $data = json_decode($row['data_json'], true);
        $search_queries[] = array_merge(['report_date' => $row['report_date']], $data);
    }

    // 2. Repeat Purchase
    $sql_rp = "SELECT * FROM amazon_brand_reports WHERE $where_customer AND report_type = 'repeat_purchase' AND report_date BETWEEN ? AND ? ORDER BY report_date DESC LIMIT 50";
    $stmt = $conn->prepare($sql_rp);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $rp_res = $stmt->get_result();
    $repeat_purchases = [];
    while($row = $rp_res->fetch_assoc()) {
        $data = json_decode($row['data_json'], true);
        $repeat_purchases[] = array_merge(['report_date' => $row['report_date']], $data);
    }

    // Compute Funnel Metrics dynamically from the database
    // 1. Query Vol & Imps from SQP
    $sql_sqp_agg = "SELECT data_json FROM amazon_brand_reports WHERE $where_customer AND report_type = 'search_query' AND report_date BETWEEN ? AND ?";
    $stmt_sqp = $conn->prepare($sql_sqp_agg);
    $stmt_sqp->bind_param("ss", $from_date, $to_date);
    $stmt_sqp->execute();
    $res_sqp = $stmt_sqp->get_result();

    $total_volume = 0;
    $total_imps = 0;
    while($row = $res_sqp->fetch_assoc()) {
        $data = json_decode($row['data_json'], true);
        if (isset($data['brandlapetiteourse']) && $data['brandlapetiteourse'] === 'Search Query') {
            continue;
        }
        foreach($data as $k => $v) {
            if (stripos($k, 'selectyear') !== false && is_numeric($v)) {
                $total_volume += intval($v);
            }
            if ((stripos($k, 'january') !== false || stripos($k, 'february') !== false || stripos($k, 'march') !== false) && is_numeric($v)) {
                $total_imps += intval($v);
            }
        }
    }

    // 2. PPC impressions & clicks
    $sql_sp = "SELECT SUM(impressions) as imps, SUM(clicks) as clicks FROM amazon_advertising_sp WHERE $where_customer AND report_date BETWEEN ? AND ?";
    $stmt_sp = $conn->prepare($sql_sp);
    $stmt_sp->bind_param("ss", $from_date, $to_date);
    $stmt_sp->execute();
    $ad_sp = $stmt_sp->get_result()->fetch_assoc();

    $sql_sb = "SELECT SUM(impressions) as imps, SUM(clicks) as clicks FROM amazon_advertising_sb WHERE $where_customer AND report_date BETWEEN ? AND ?";
    $stmt_sb = $conn->prepare($sql_sb);
    $stmt_sb->bind_param("ss", $from_date, $to_date);
    $stmt_sb->execute();
    $ad_sb = $stmt_sb->get_result()->fetch_assoc();

    $sql_sd = "SELECT SUM(impressions) as imps, SUM(clicks) as clicks FROM amazon_advertising_sd WHERE $where_customer AND report_date BETWEEN ? AND ?";
    $stmt_sd = $conn->prepare($sql_sd);
    $stmt_sd->bind_param("ss", $from_date, $to_date);
    $stmt_sd->execute();
    $ad_sd = $stmt_sd->get_result()->fetch_assoc();

    $brand_imps = intval($ad_sp['imps'] ?? 0) + intval($ad_sb['imps'] ?? 0) + intval($ad_sd['imps'] ?? 0);
    $brand_clicks = intval($ad_sp['clicks'] ?? 0) + intval($ad_sb['clicks'] ?? 0) + intval($ad_sd['clicks'] ?? 0);

    // 3. Brand units (Purchases)
    $sql_bus = "SELECT SUM(units_ordered) as units FROM amazon_business_report WHERE $where_customer AND report_date BETWEEN ? AND ?";
    $stmt_bus = $conn->prepare($sql_bus);
    $stmt_bus->bind_param("ss", $from_date, $to_date);
    $stmt_bus->execute();
    $bus = $stmt_bus->get_result()->fetch_assoc();
    $brand_purchases = intval($bus['units'] ?? 0);

    echo json_encode([
        'search_queries' => $search_queries,
        'repeat_purchases' => $repeat_purchases,
        'trends' => fetchBrandTrends($conn, $where_customer, $from_date, $to_date),
        'funnel_metrics' => [
            'market_search_volume' => $total_volume,
            'market_impressions' => $total_imps,
            'brand_impressions' => $brand_imps,
            'brand_clicks' => $brand_clicks,
            'brand_purchases' => $brand_purchases
        ]
    ]);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => $t->getMessage()]);
}

function fetchBrandTrends($conn, $where, $from, $to) {
    $sql = "SELECT DATE_FORMAT(report_date, '%Y-%m') as month, 
                   AVG(CAST(JSON_UNQUOTE(JSON_EXTRACT(data_json, '$.brandshare')) AS DECIMAL(10,2))) as avg_share
            FROM amazon_brand_reports 
            WHERE $where AND report_type = 'search_query' AND report_date BETWEEN ? AND ?
            GROUP BY month ORDER BY month ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
