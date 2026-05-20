<?php
require_once 'config.php';
$res = $conn->query("SELECT placement, COUNT(*) FROM amazon_advertising_sp GROUP BY placement");
while($row = $res->fetch_array()) print_r($row);
?>
