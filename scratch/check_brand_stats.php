<?php
require_once 'config.php';
$res = $conn->query("SELECT report_type, COUNT(*) as count, MIN(report_date) as min_date, MAX(report_date) as max_date FROM amazon_brand_reports GROUP BY report_type");
if ($res) {
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
?>
