<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$tables = [
    'ads' => 'amazon_advertising_sp',
    'trans' => 'amazon_transaction_report',
    'brand' => 'amazon_brand_reports',
    'ops' => 'amazon_returns_reimbursements'
];

$ranges = [];
foreach ($tables as $key => $table) {
    $date_col = ($key === 'trans') ? 'date_time' : 'report_date';
    $res = $conn->query("SELECT MIN($date_col) as min_date, MAX($date_col) as max_date FROM $table");
    $row = $res->fetch_assoc();
    $ranges[$key] = $row;
}

echo json_encode($ranges);
?>
