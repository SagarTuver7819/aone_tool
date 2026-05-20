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

    echo json_encode([
        'search_queries' => $search_queries,
        'repeat_purchases' => $repeat_purchases,
        'trends' => fetchBrandTrends($conn, $where_customer, $from_date, $to_date)
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
