<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
$r = $c->query("SELECT report_type, COUNT(*) FROM amazon_advertising_sp WHERE advertised_sku IS NOT NULL AND advertised_sku != '' GROUP BY report_type");
while($row=$r->fetch_assoc()) echo $row['report_type'] . ": " . $row['COUNT(*)'] . "\n";
?>
