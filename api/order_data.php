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
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // Find the dynamic order report table
    $tableRes = $conn->query("SHOW TABLES LIKE 'dyn_order_report%'");
    $tableName = '';
    if ($tableRes && $row = $tableRes->fetch_array()) {
        $tableName = $row[0];
    }

    if (!$tableName) {
        echo json_encode(['orders' => [], 'total' => 0]);
        exit();
    }

    $where = "1=1";
    $params = [];
    $types = "";

    if ($search) {
        $where .= " AND (amazon_order_id LIKE ? OR sku LIKE ? OR asin LIKE ?)";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        $types .= "sss";
    }

    if ($status) {
        $where .= " AND order_status = ?";
        $params[] = $status;
        $types .= "s";
    }

    $sql = "SELECT amazon_order_id, purchase_date, sku, asin, quantity, order_status, ship_city, ship_state 
            FROM `$tableName` 
            WHERE $where 
            ORDER BY purchase_date DESC 
            LIMIT 100";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'orders' => $orders,
        'total' => count($orders)
    ]);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => $t->getMessage()]);
}
