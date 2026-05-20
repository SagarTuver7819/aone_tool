<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$customer_id = $_SESSION['customer_id'] ?? 0;

if ($action === 'list_all') {
    $products = $conn->query("SELECT * FROM pro_product_config WHERE customer_id = $customer_id")->fetch_all(MYSQLI_ASSOC);
    $history = $conn->query("SELECT * FROM pro_cogs_history WHERE customer_id = $customer_id ORDER BY sku, start_date DESC")->fetch_all(MYSQLI_ASSOC);
    $rules = $conn->query("SELECT * FROM pro_expense_rules WHERE customer_id = $customer_id AND is_active = 1")->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'products' => $products,
        'history' => $history,
        'rules' => $rules
    ]);
}
?>
