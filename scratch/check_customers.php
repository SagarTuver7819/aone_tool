<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
$tabs = ["amazon_business_report", "amazon_detail_report", "amazon_transaction_report", "amazon_advertising_sp"];
foreach ($tabs as $t) {
    $r = $c->query("SELECT DISTINCT customer_id FROM $t");
    if ($r) {
        $ids = [];
        while($row=$r->fetch_assoc()) $ids[]=$row['customer_id'];
        echo "$t customer_ids: " . implode(", ", $ids) . "\n";
    }
}
?>
