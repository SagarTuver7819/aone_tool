<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
$r = $c->query("SELECT SUM(total_sales) FROM amazon_advertising_sp WHERE report_type = 'general'");
$row = $r->fetch_row();
echo "Total SP Sales (general): " . $row[0] . "\n";

$r = $c->query("SELECT SUM(total_sales) FROM amazon_advertising_sp");
$row = $r->fetch_row();
echo "Total SP Sales (all): " . $row[0] . "\n";
?>
