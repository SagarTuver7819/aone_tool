<?php
echo "RANGE: " . file_get_contents('live_range.json') . "\n\n";

function line($label, $file) {
    $j = json_decode(@file_get_contents($file), true);
    if (!$j) { echo "$label INVALID\n"; return; }
    if (isset($j['kpis'])) {
        echo "$label sales=" . ($j['kpis']['total_sales'] ?? '?')
            . " units=" . ($j['kpis']['total_units'] ?? '?')
            . " ads=" . ($j['kpis']['ad_spend'] ?? '?')
            . " trends=" . implode(',', array_keys($j['trends'] ?? []))
            . " chart_pts=" . count($j['charts']['labels'] ?? [])
            . " skupl=" . count($j['sku_pl'] ?? [])
            . "\n";
        return;
    }
    if (isset($j['summary']['sp'])) {
        echo "$label spend=" . ($j['summary']['sp']['spend'] ?? 0) . " sales=" . ($j['summary']['sp']['sales'] ?? 0) . "\n";
        return;
    }
    echo "$label has_data=" . (!empty($j['has_data']) ? 'Y' : 'N') . " keys=" . implode(',', array_keys($j)) . "\n";
}
line('SEP-dash', 'live_sep.json');
line('AUG-dash', 'live_aug.json');
line('SEP-ads', 'live_ads_sep.json');
line('SEP-txn', 'live_txn_sep.json');

$j = json_decode(file_get_contents('live_sep.json'), true);
echo "\nSEP trends detail:\n";
foreach (($j['trends'] ?? []) as $m => $row) {
    echo "  $m => sales=" . ($row['sales'] ?? 0) . " orders=" . ($row['orders'] ?? 0) . " units=" . ($row['units'] ?? 0) . "\n";
}
$fin = $j['financials'] ?? [];
echo "SEP fin product_sales=" . ($fin['product_sales'] ?? '?') . " revenue=" . ($fin['revenue'] ?? '?') . "\n";
