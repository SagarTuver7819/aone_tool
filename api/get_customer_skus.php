<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$customer_id = intval($_GET['customer_id'] ?? 0);
if ($customer_id <= 0) {
    echo json_encode(['skus' => []]);
    exit;
}

$skus = [];

// Get SKUs from transaction report
$res = $conn->query("SELECT DISTINCT sku FROM amazon_transaction_report WHERE customer_id = $customer_id AND sku IS NOT NULL AND sku != '' ORDER BY sku ASC");
while($row = $res->fetch_assoc()) {
    $skus[] = $row['sku'];
}

// Also check advertising tables for any SKUs not in transactions
$res = $conn->query("SELECT DISTINCT advertised_sku FROM amazon_advertising_sp WHERE customer_id = $customer_id AND advertised_sku IS NOT NULL AND advertised_sku != ''");
while($row = $res->fetch_assoc()) {
    if (!in_array($row['advertised_sku'], $skus)) $skus[] = $row['advertised_sku'];
}

sort($skus);

echo json_encode(['skus' => $skus]);
?>
