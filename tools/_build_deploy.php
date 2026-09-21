<?php
$files = [
    'api/product_analytics.php',
    'api/dashboard_data.php',
    'api/get_data_range.php',
];
$payload = [];
foreach ($files as $f) {
    $payload[$f] = file_get_contents(__DIR__ . '/../' . $f);
}
$json = json_encode($payload);
$b64 = base64_encode($json);

$out = <<<'PHP'
<?php
header('Content-Type: text/html; charset=utf-8');
if (!hash_equals('AONE_DATE_FIX_2026', (string)($_GET['key'] ?? ''))) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}
$root = __DIR__;
$ok = true;
$log = [];
$files = json_decode(base64_decode('__PAYLOAD__'), true);
if (!is_array($files)) {
    echo '<h1>Bad payload</h1>';
    exit;
}
foreach ($files as $rel => $body) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        $ok = false;
        $log[] = "mkdir fail $rel";
        continue;
    }
    $w = file_put_contents($path, $body);
    $log[] = (($w === false) ? 'FAIL' : 'OK') . " $rel (" . strlen($body) . ' bytes)';
    if ($w === false) $ok = false;
}

// Patch dashboard fake placeholders if present
$dash = $root . '/modules/dashboard/index.php';
if (is_file($dash)) {
    $src = file_get_contents($dash);
    $orig = $src;
    if (strpos($src, 'Shared date state for Overview') === false && strpos($src, '<!-- Figma toolbar lives inside tab -->') !== false) {
        $src = str_replace(
            "<?php if (\$active_tab === 'kpi' || \$active_tab === 'financial'): ?>\n    <!-- Figma toolbar lives inside tab -->\n<?php elseif (\$active_tab === 'products'): ?>",
            "<?php if (\$active_tab === 'kpi' || \$active_tab === 'financial'): ?>\n    <input type=\"hidden\" id=\"filter_from\" value=\"\">\n    <input type=\"hidden\" id=\"filter_to\" value=\"\">\n<?php elseif (\$active_tab === 'products'): ?>",
            $src
        );
    }
    $src = str_replace(
        "const sessionsDisplay = totalProdSessions > 0 ? totalProdSessions : 12482;\n                    $('#prod_meta_sessions').text(sessionsDisplay.toLocaleString());\n\n                    // Set ROAS with dynamic calc (fallback to mockup 4.2x if zero)\n                    const roasDisplay = totalProdAdSpend > 0 ? (totalProdRevenue / totalProdAdSpend) : 4.2;",
        "$('#prod_meta_sessions').text(totalProdSessions.toLocaleString());\n                    const roasDisplay = totalProdAdSpend > 0 ? (totalProdRevenue / totalProdAdSpend) : 0;",
        $src
    );
    if ($src !== $orig) {
        $w = file_put_contents($dash, $src);
        $log[] = (($w === false) ? 'FAIL' : 'OK') . ' modules/dashboard/index.php patch';
        if ($w === false) $ok = false;
    } else {
        $log[] = 'SKIP modules/dashboard/index.php (already patched or different markup)';
    }
}

echo '<h1>' . ($ok ? 'Deploy OK' : 'Deploy failed') . '</h1><pre>' . htmlspecialchars(implode("\n", $log)) . '</pre>';
echo '<p>Hard refresh Product Performance. Delete <code>deploy_product_date_fix.php</code>.</p>';
PHP;

$out = str_replace('__PAYLOAD__', $b64, $out);
file_put_contents(__DIR__ . '/../deploy_product_date_fix.php', $out);
echo 'Wrote deploy_product_date_fix.php size=' . filesize(__DIR__ . '/../deploy_product_date_fix.php') . PHP_EOL;
