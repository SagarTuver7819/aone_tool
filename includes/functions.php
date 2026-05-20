<?php
require_once __DIR__ . '/../config.php';

function get_all_customers() {
    global $conn;
    $sql = "SELECT * FROM customers ORDER BY customer_name ASC";
    return $conn->query($sql);
}

function get_customer_by_id($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

function format_number($number) {
    return number_format($number);
}

function format_percent($percent) {
    return number_format($percent, 2) . '%';
}
?>
