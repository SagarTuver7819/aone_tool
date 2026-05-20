<?php
require_once 'config.php';
$res = $conn->query("SELECT report_type, COUNT(*) as count FROM amazon_brand_reports GROUP BY report_type");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
