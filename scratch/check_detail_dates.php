<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
$r = $c->query("SELECT report_date, COUNT(*) FROM amazon_detail_report GROUP BY report_date ORDER BY report_date ASC");
if ($r) {
    while($row=$r->fetch_assoc()) {
        echo $row['report_date'] . ": " . $row['COUNT(*)'] . "\n";
    }
}
?>
