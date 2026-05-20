<?php
require_once 'config.php';
$res = $conn->query("SELECT data_json FROM amazon_brand_reports WHERE report_type = 'repeat_purchase' LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    echo $row['data_json'];
}
?>
