<?php
require_once 'config.php';
$sql = "SELECT count(*) as count FROM amazon_business_report WHERE report_date BETWEEN '2026-01-01' AND '2026-03-31'";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
echo "Business Report count: " . $row['count'] . "\n";

$sql = "SELECT count(*) as count FROM amazon_advertising_sp WHERE report_date BETWEEN '2026-01-01' AND '2026-03-31'";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
echo "Advertising SP count: " . $row['count'] . "\n";
?>
