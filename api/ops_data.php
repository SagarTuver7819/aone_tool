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

    // 1. Returns Summary
    $sql_returns = "SELECT * FROM amazon_returns_reimbursements WHERE $where_customer AND type = 'Return' AND report_date BETWEEN ? AND ? ORDER BY report_date DESC LIMIT 100";
    $stmt = $conn->prepare($sql_returns);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $returns = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 2. Reimbursements Summary
    $sql_reimb = "SELECT * FROM amazon_returns_reimbursements WHERE $where_customer AND type = 'Reimbursement' AND report_date BETWEEN ? AND ? ORDER BY report_date DESC LIMIT 100";
    $stmt = $conn->prepare($sql_reimb);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $reimbursements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 3. Stats
    $sql_stats = "SELECT 
        SUM(CASE WHEN type = 'Return' THEN 1 ELSE 0 END) as total_returns,
        SUM(CASE WHEN type = 'Return' THEN quantity ELSE 0 END) as return_units,
        SUM(CASE WHEN type = 'Reimbursement' THEN amount ELSE 0 END) as total_reimbursed
        FROM amazon_returns_reimbursements WHERE $where_customer AND report_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql_stats);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        'returns' => $returns,
        'reimbursements' => $reimbursements,
        'stats' => $stats
    ]);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => $t->getMessage()]);
}
