<?php
require __DIR__ . '/../config.php';
foreach (['amazon_advertising_sp','amazon_advertising_sb','amazon_advertising_sd'] as $t) {
    echo "==== $t ====\n";
    $r = $conn->query("SHOW COLUMNS FROM `$t`");
    if (!$r) { echo $conn->error."\n"; continue; }
    while ($c = $r->fetch_assoc()) {
        if (stripos($c['Field'], 'sku') !== false || stripos($c['Field'], 'asin') !== false) {
            echo $c['Field']."\n";
        }
    }
}
