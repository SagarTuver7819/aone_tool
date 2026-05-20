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
    $customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : ($_SESSION['customer_id'] ?? 0);
    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

    // 1. Get latest inventory date
    $res_date = $conn->query("SELECT MAX(report_date) as max_date FROM amazon_inventory WHERE $where_customer");
    $latest_date = ($res_date && $row = $res_date->fetch_assoc()) ? $row['max_date'] : null;

    if (!$latest_date) {
        echo json_encode(['data' => [], 'summary' => []]);
        exit();
    }

    // 2. Get ADS (Average Daily Sales) from Detail Report (last 30 days of available data)
    $sql_ads = "SELECT asin, SUM(units_ordered) / 30 as ads 
                FROM amazon_detail_report 
                WHERE $where_customer 
                AND report_date >= DATE_SUB((SELECT MAX(report_date) FROM amazon_detail_report WHERE $where_customer), INTERVAL 30 DAY)
                GROUP BY asin";
    $ads_res = $conn->query($sql_ads);
    $ads_map = [];
    while($row = $ads_res->fetch_assoc()) {
        $ads_map[$row['asin']] = floatval($row['ads']);
    }

    // 3. Get Inventory Data
    $sql_inv = "SELECT * FROM amazon_inventory WHERE $where_customer AND report_date = ? ORDER BY afn_fulfillable_quantity DESC";
    $stmt = $conn->prepare($sql_inv);
    $stmt->bind_param("s", $latest_date);
    $stmt->execute();
    $inv_res = $stmt->get_result();

    $data = [];
    $summary = [
        'total_skus' => 0,
        'out_of_stock' => 0,
        'low_stock' => 0, // < 15 days
        'healthy' => 0,   // 15-45 days
        'excess' => 0,    // > 45 days
        'total_value' => 0
    ];

    while($row = $inv_res->fetch_assoc()) {
        $asin = $row['asin'];
        $sku = $row['sku'];
        $stock = intval($row['afn_fulfillable_quantity']);
        $inbound = intval($row['afn_inbound_working_quantity'] + $row['afn_inbound_shipped_quantity'] + $row['afn_inbound_receiving_quantity']);
        $price = floatval($row['your_price']);
        
        $ads = $ads_map[$asin] ?? 0;
        $dos = ($ads > 0) ? ($stock / $ads) : ($stock > 0 ? 999 : 0);
        
        $status = 'Healthy';
        if ($stock <= 0) {
            $status = 'Out of Stock';
            $summary['out_of_stock']++;
        } elseif ($dos < 15) {
            $status = 'Low Stock';
            $summary['low_stock']++;
        } elseif ($dos > 60) {
            $status = 'Excess';
            $summary['excess']++;
        } else {
            $summary['healthy']++;
        }

        $summary['total_skus']++;
        $summary['total_value'] += ($stock * $price);

        $data[] = [
            'sku' => $sku,
            'asin' => $asin,
            'name' => $row['product_name'],
            'stock' => $stock,
            'inbound' => $inbound,
            'ads' => round($ads, 2),
            'dos' => round($dos, 1),
            'status' => $status,
            'value' => round($stock * $price, 2)
        ];
    }

    echo json_encode([
        'report_date' => $latest_date,
        'summary' => $summary,
        'data' => $data
    ]);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => $t->getMessage()]);
}
?>
