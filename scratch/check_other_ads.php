<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
echo "SB types:\n";
$r = $c->query("SELECT DISTINCT report_type FROM amazon_advertising_sb");
while($row=$r->fetch_assoc()) echo $row['report_type'] . "\n";
echo "SD types:\n";
$r = $c->query("SELECT DISTINCT report_type FROM amazon_advertising_sd");
while($row=$r->fetch_assoc()) echo $row['report_type'] . "\n";
?>
