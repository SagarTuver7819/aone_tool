<?php
$c = new mysqli("localhost", "root", "", "ocean_crm_amazon");
$tabs = ["amazon_advertising_sp", "amazon_advertising_sb", "amazon_advertising_sd"];
foreach ($tabs as $t) {
    $r = $c->query("DESCRIBE $t");
    if ($r) {
        $cols = [];
        while($row=$r->fetch_assoc()) $cols[]=$row['Field'];
        echo "$t columns: " . implode(", ", $cols) . "\n";
    } else {
        echo "$t: table not found\n";
    }
}
?>
