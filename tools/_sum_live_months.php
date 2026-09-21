<?php
$months = [
    'Jun' => ['live_m6.json', '2026-06-01', '2026-06-30'],
    'Jul' => ['live_m7.json', '2026-07-01', '2026-07-31'],
    'Aug' => ['live_m8.json', '2026-08-01', '2026-08-31'],
];
foreach ($months as $label => $info) {
    $f = $info[0];
    if (!file_exists($f)) {
        echo "$label MISSING $f\n";
        continue;
    }
    $j = json_decode(file_get_contents($f), true);
    $k = $j['kpis'] ?? [];
    $fin = $j['financials'] ?? [];
    echo "$label | sales=" . ($k['total_sales'] ?? '?')
        . " | units=" . ($k['total_units'] ?? '?')
        . " | orders=" . ($k['total_orders'] ?? '?')
        . " | ads=" . ($k['ad_spend'] ?? '?')
        . " | net=" . ($k['net_profit'] ?? '?')
        . " | sku_pl=" . count($j['sku_pl'] ?? [])
        . " | products=" . count($j['products'] ?? [])
        . " | txn_sales=" . ($fin['product_sales'] ?? '?')
        . PHP_EOL;
}
