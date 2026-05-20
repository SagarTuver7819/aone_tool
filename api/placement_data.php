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

    $sql = "SELECT 
                placement,
                SUM(impressions) as impressions,
                SUM(clicks) as clicks,
                SUM(spend) as spend,
                SUM(total_sales) as sales,
                SUM(total_orders) as orders
            FROM (
                SELECT placement, impressions, clicks, spend, total_sales, total_orders FROM amazon_advertising_sp WHERE $where_customer AND report_date BETWEEN ? AND ?
                UNION ALL
                SELECT placement, impressions, clicks, spend, total_sales, total_orders FROM amazon_advertising_sb WHERE $where_customer AND report_date BETWEEN ? AND ?
                UNION ALL
                SELECT placement, impressions, clicks, spend, total_sales, total_orders FROM amazon_advertising_sd WHERE $where_customer AND report_date BETWEEN ? AND ?
            ) as combined
            GROUP BY placement
            ORDER BY spend DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $from_date, $to_date, $from_date, $to_date, $from_date, $to_date);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    while ($row = $res->fetch_assoc()) {
        $row['ctr'] = $row['impressions'] > 0 ? ($row['clicks'] / $row['impressions']) * 100 : 0;
        $row['cpc'] = $row['clicks'] > 0 ? ($row['spend'] / $row['clicks']) : 0;
        $row['acos'] = $row['sales'] > 0 ? ($row['spend'] / $row['sales']) : 0;
        $row['roas'] = $row['spend'] > 0 ? ($row['sales'] / $row['spend']) : 0;
        $data[] = $row;
    }

    echo json_encode(['data' => $data]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
