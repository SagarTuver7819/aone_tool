<?php
require_once __DIR__ . '/../config.php';

function arg_value(string $name, ?string $default = null): ?string {
    global $argv;
    foreach ($argv as $arg) {
        if (str_starts_with($arg, $name . '=')) {
            return substr($arg, strlen($name) + 1);
        }
    }
    return $default;
}

function tool_slugify(string $text): string {
    $text = preg_replace('/^\xEF\xBB\xBF/', '', $text); // UTF-8 BOM
    return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $text));
}

function read_csv_header_slugs(string $filePath): array {
    $h = fopen($filePath, 'r');
    if (!$h) return [];
    $raw = fgetcsv($h);
    fclose($h);
    if (!$raw) return [];
    $slugs = [];
    foreach ($raw as $hdr) {
        $slugs[tool_slugify((string)$hdr)] = (string)$hdr;
    }
    return $slugs;
}

function extract_slugify_literals(string $phpSource, string $fnName): array {
    $start = strpos($phpSource, "function $fnName");
    if ($start === false) return [];
    $rest = substr($phpSource, $start);
    // naive function-body slice (good enough for this file)
    $end = strpos($rest, "function ", 10);
    $slice = $end === false ? $rest : substr($rest, 0, $end);

    preg_match_all("/slugify\\('([^']+)'\\)/", $slice, $m);
    $labels = $m[1] ?? [];
    $slugs = [];
    foreach ($labels as $lbl) {
        $slugs[tool_slugify($lbl)] = $lbl;
    }
    return $slugs;
}

$customerId = (int) (arg_value('--customer_id', '1'));
$month = arg_value('--month', '2026-01'); // YYYY-MM
$doImport = (arg_value('--import', '1') !== '0');

if ($customerId <= 0) {
    fwrite(STDERR, "ERROR: pass --customer_id=ID (ID must be > 0)\n");
    exit(2);
}

if (!preg_match('/^\\d{4}-\\d{2}$/', (string)$month)) {
    fwrite(STDERR, "ERROR: invalid --month format, expected YYYY-MM\n");
    exit(2);
}

$reportDate = $month . '-01';
$monthStart = date('Y-m-01', strtotime($reportDate));
$monthEnd = date('Y-m-t', strtotime($reportDate));

$businessPath = realpath(__DIR__ . '/../excel/BusinessReport-Jan.csv');
$detailPath = realpath(__DIR__ . '/../excel/Detail Page Sales and Traffic By Child Item_Jan.csv');

if (!$businessPath || !is_file($businessPath)) {
    fwrite(STDERR, "ERROR: missing file excel/BusinessReport-Jan.csv\n");
    exit(2);
}
if (!$detailPath || !is_file($detailPath)) {
    fwrite(STDERR, "ERROR: missing file excel/Detail Page Sales and Traffic By Child Item_Jan.csv\n");
    exit(2);
}

echo "Customer: $customerId\n";
echo "Month: $monthStart to $monthEnd\n";
echo "Business CSV: $businessPath\n";
echo "Detail CSV: $detailPath\n\n";

$businessHeader = read_csv_header_slugs($businessPath);
$detailHeader = read_csv_header_slugs($detailPath);

$reportUploadPath = realpath(__DIR__ . '/../modules/report_upload/index.php');
$src = $reportUploadPath ? file_get_contents($reportUploadPath) : '';

$expectedBusiness = $src ? extract_slugify_literals($src, 'parseBusinessCSV') : [];
$expectedDetail = $src ? extract_slugify_literals($src, 'parseDetailCSV') : [];

function print_missing(string $title, array $expected, array $header): int {
    echo "== $title ==\n";
    $missing = [];
    foreach ($expected as $slug => $label) {
        if (!isset($header[$slug])) $missing[] = $label;
    }
    if (!$expected) {
        echo "WARN: could not extract expected columns from modules/report_upload/index.php\n\n";
        return 0;
    }
    if (!$missing) {
        echo "OK: all expected columns present (" . count($expected) . " checks)\n\n";
        return 0;
    }
    echo "MISSING (" . count($missing) . "):\n";
    foreach ($missing as $m) echo "- $m\n";
    echo "\n";
    return count($missing);
}

$missingCount = 0;
$missingCount += print_missing('Business CSV vs DB ingest mapping', $expectedBusiness, $businessHeader);
$missingCount += print_missing('Detail CSV vs DB ingest mapping', $expectedDetail, $detailHeader);

if ($missingCount > 0) {
    fwrite(STDERR, "ERROR: CSV headers mismatch (missing columns). Fix CSV export or parser mapping.\n");
    exit(1);
}

if ($doImport) {
    // Clear month data (extra safety; the ingest already clears business + detail).
    $stmt = $conn->prepare("DELETE FROM amazon_business_report WHERE customer_id = ? AND report_date BETWEEN ? AND ?");
    $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM amazon_detail_report WHERE customer_id = ? AND report_date BETWEEN ? AND ?");
    $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM financial_settings WHERE customer_id = ? AND report_month = ?");
    $stmt->bind_param("is", $customerId, $monthStart);
    $stmt->execute();

    // Synthetic POST to reuse the web ingest logic.
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['PHP_SELF'] = '/modules/report_upload/index.php';
    $_SERVER['SCRIPT_NAME'] = '/modules/report_upload/index.php';
    $_POST = [
        'customer_id' => (string)$customerId,
        'report_month' => $month,
    ];
    $_FILES = [
        'business_report' => [
            'name' => basename($businessPath),
            'type' => 'text/csv',
            'tmp_name' => $businessPath,
            'error' => 0,
            'size' => filesize($businessPath),
        ],
        'detail_report' => [
            'name' => basename($detailPath),
            'type' => 'text/csv',
            'tmp_name' => $detailPath,
            'error' => 0,
            'size' => filesize($detailPath),
        ],
    ];

    $oldCwd = getcwd();
    chdir(__DIR__ . '/../modules/report_upload');
    ob_start();
    include 'index.php';
    ob_end_clean();
    if ($oldCwd) chdir($oldCwd);

    echo "== Import Done ==\n";
}

// Summary checks
$stmt = $conn->prepare("SELECT
    COUNT(*) as rows_cnt,
    SUM(ordered_product_sales) as total_sales,
    SUM(ordered_product_sales_b2b) as b2b_sales,
    SUM(units_ordered) as units,
    SUM(total_order_items) as orders,
    SUM(sessions_total) as sessions,
    AVG(unit_session_percentage) as avg_conv,
    SUM(units_refunded) as refunded_units,
    AVG(refund_rate) as avg_refund
    FROM amazon_business_report
    WHERE customer_id = ? AND report_date BETWEEN ? AND ?");
$stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
$stmt->execute();
$sum = $stmt->get_result()->fetch_assoc();

echo "== Business Report Summary ==\n";
echo "Rows: " . ($sum['rows_cnt'] ?? 0) . "\n";
echo "Sales: " . number_format((float)($sum['total_sales'] ?? 0), 2) . "\n";
echo "B2B Sales: " . number_format((float)($sum['b2b_sales'] ?? 0), 2) . "\n";
echo "Units: " . (int)($sum['units'] ?? 0) . "\n";
echo "Orders: " . (int)($sum['orders'] ?? 0) . "\n";
echo "Sessions: " . (int)($sum['sessions'] ?? 0) . "\n";
echo "Avg Conv %: " . number_format((float)($sum['avg_conv'] ?? 0), 2) . "\n";
echo "Refunded Units: " . (int)($sum['refunded_units'] ?? 0) . "\n";
echo "Avg Refund %: " . number_format((float)($sum['avg_refund'] ?? 0), 2) . "\n\n";

$stmt = $conn->prepare("SELECT COUNT(*) as rows_cnt FROM amazon_detail_report WHERE customer_id = ? AND report_date BETWEEN ? AND ?");
$stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
$stmt->execute();
$detailCnt = $stmt->get_result()->fetch_assoc();
echo "== Detail Report Rows ==\n";
echo "Rows: " . ($detailCnt['rows_cnt'] ?? 0) . "\n\n";

echo "== Top Products (by revenue) ==\n";
$stmt = $conn->prepare("SELECT asin, LEFT(title, 60) as title_short, ordered_product_sales, units_ordered, units_refunded, refund_rate
    FROM amazon_detail_report
    WHERE customer_id = ? AND report_date BETWEEN ? AND ?
    ORDER BY ordered_product_sales DESC
    LIMIT 10");
$stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $asin = $row['asin'] ?? '';
    $title = $row['title_short'] ?? '';
    $sales = number_format((float)($row['ordered_product_sales'] ?? 0), 2);
    $units = (int)($row['units_ordered'] ?? 0);
    $refU = (int)($row['units_refunded'] ?? 0);
    $refR = number_format((float)($row['refund_rate'] ?? 0), 2);
    echo "- $asin | $$sales | units=$units | refunds=$refU ($refR%) | $title\n";
}
