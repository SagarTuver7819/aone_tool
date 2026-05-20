<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
$r = $c->query("SELECT DISTINCT report_type FROM amazon_advertising_sp");
while($row=$r->fetch_assoc()) echo $row['report_type'] . "\n";
?>
