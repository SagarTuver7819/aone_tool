<?php
$files = [
    'api/dashboard_data.php',
    'api/get_data_range.php',
    'api/product_analytics.php',
    'assets/js/aone-date-range.js',
];
$payload = [];
foreach ($files as $f) {
    $payload[$f] = file_get_contents(__DIR__ . '/../' . $f);
}
$b64 = base64_encode(json_encode($payload));
$tpl = <<<'PHP'
<?php
header('Content-Type: text/html; charset=utf-8');
if (!hash_equals('AONE_DATE_FIX_2026', (string)($_GET['key'] ?? ''))) { http_response_code(403); echo 'Forbidden'; exit; }
$root = __DIR__; $ok = true; $log = [];
$files = json_decode(base64_decode('__PAYLOAD__'), true);
foreach ($files as $rel => $body) {
  $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
  $dir = dirname($path);
  if (!is_dir($dir) && !@mkdir($dir, 0755, true)) { $ok=false; $log[]="mkdir fail $rel"; continue; }
  $w = file_put_contents($path, $body);
  $log[] = (($w===false)?'FAIL':'OK') . " $rel (" . strlen($body) . ' bytes)';
  if ($w===false) $ok=false;
}
echo '<h1>'.($ok?'Deploy OK':'Deploy failed').'</h1><pre>'.htmlspecialchars(implode("\n",$log)).'</pre><p>Hard refresh Overview. Delete this file.</p>';
PHP;
file_put_contents(__DIR__ . '/../deploy_kpi_fix.php', str_replace('__PAYLOAD__', $b64, $tpl));
echo 'Wrote deploy_kpi_fix.php size=' . filesize(__DIR__ . '/../deploy_kpi_fix.php') . PHP_EOL;
