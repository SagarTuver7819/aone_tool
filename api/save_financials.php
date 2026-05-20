<?php
require_once '../config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $report_month = $_POST['report_month'] . "-01";
    $cogs = floatval($_POST['cogs']);
    $ad_spend = floatval($_POST['ad_spend']);
    $other_fees = floatval($_POST['other_fees']);

    $stmt = $conn->prepare("INSERT INTO financial_settings (customer_id, report_month, cogs, ad_spend, other_fees) 
                            VALUES (?, ?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE cogs = VALUES(cogs), ad_spend = VALUES(ad_spend), other_fees = VALUES(other_fees)");
    $stmt->bind_param("isddd", $customer_id, $report_month, $cogs, $ad_spend, $other_fees);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit();
}
?>
