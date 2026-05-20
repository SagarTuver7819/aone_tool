<?php
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_GET['customer_id'] = 0;
$_GET['from_date'] = '2026-01-01';
$_GET['to_date'] = '2026-05-15';
include 'api/brand_data.php';
?>
