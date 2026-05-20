<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
$r = $c->query("DESCRIBE amazon_detail_report");
if ($r) {
    while($row=$r->fetch_assoc()) echo $row['Field'] . " ";
}
?>
