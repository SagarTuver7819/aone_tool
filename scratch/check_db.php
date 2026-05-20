<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
if ($c->connect_error) die("Connection failed: " . $c->connect_error);
$tabs = ["amazon_business_report", "amazon_detail_report", "amazon_transaction_report", "amazon_advertising_sp"];
foreach ($tabs as $t) {
    $r = $c->query("SELECT COUNT(*) FROM $t");
    if ($r) {
        $row = $r->fetch_row();
        echo "$t: $row[0]\n";
    } else {
        echo "$t: error (" . $c->error . ")\n";
    }
}
$r = $c->query("SELECT MIN(report_date), MAX(report_date) FROM amazon_business_report");
if ($r) {
    $row = $r->fetch_row();
    echo "Date Range: $row[0] to $row[1]\n";
}
?>
