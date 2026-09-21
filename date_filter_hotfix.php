<?php
/**
 * One-time live hotfix: date filter missing #filter_from on KPI/Financial + auto data range.
 * Upload this file to live root, then open:
 *   http://aone-tool.oceanhub.co.in/date_filter_hotfix.php?key=AONE_DATE_FIX_2026
 * Delete this file after success.
 */
header('Content-Type: text/html; charset=utf-8');

$key = $_GET['key'] ?? '';
if (!hash_equals('AONE_DATE_FIX_2026', (string) $key)) {
    http_response_code(403);
    echo '<h2>Forbidden</h2>';
    exit;
}

$root = __DIR__;
$results = [];

function patch_file($path, $search, $replace, $label) {
    global $results;
    if (!is_file($path)) {
        $results[] = ['ok' => false, 'label' => $label, 'detail' => 'File missing: ' . $path];
        return false;
    }
    $src = file_get_contents($path);
    if ($src === false) {
        $results[] = ['ok' => false, 'label' => $label, 'detail' => 'Cannot read file'];
        return false;
    }
    if (strpos($src, $search) === false) {
        // Already patched?
        if (strpos($src, 'bootDashboardDates') !== false || strpos($src, 'Shared date state for Overview') !== false) {
            $results[] = ['ok' => true, 'label' => $label, 'detail' => 'Already patched (skip)'];
            return true;
        }
        $results[] = ['ok' => false, 'label' => $label, 'detail' => 'Search block not found'];
        return false;
    }
    $new = str_replace($search, $replace, $src);
    if ($new === $src) {
        $results[] = ['ok' => false, 'label' => $label, 'detail' => 'No change applied'];
        return false;
    }
    $ok = file_put_contents($path, $new) !== false;
    $results[] = ['ok' => $ok, 'label' => $label, 'detail' => $ok ? 'Patched' : 'Write failed'];
    return $ok;
}

// 1) get_data_range.php — full replace
$getDataRange = <<<'PHP'
<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

$tables = [
    'ads' => ['table' => 'amazon_advertising_sp', 'date_col' => 'report_date'],
    'trans' => ['table' => 'amazon_transaction_report', 'date_col' => 'date_time'],
    'brand' => ['table' => 'amazon_brand_reports', 'date_col' => 'report_date'],
    'ops' => ['table' => 'amazon_returns_reimbursements', 'date_col' => 'report_date'],
];

function normalize_ymd($value) {
    if ($value === null || $value === '') {
        return null;
    }
    $ts = strtotime((string) $value);
    if ($ts === false) {
        return null;
    }
    return date('Y-m-d', $ts);
}

$ranges = [];
$overall_min = null;
$overall_max = null;

foreach ($tables as $key => $meta) {
    $table = $meta['table'];
    $date_col = $meta['date_col'];
    $where = ($customer_id > 0) ? "WHERE customer_id = $customer_id" : '';

    $sql = "SELECT MIN($date_col) AS min_date, MAX($date_col) AS max_date FROM `$table` $where";
    $res = $conn->query($sql);
    $row = $res ? $res->fetch_assoc() : ['min_date' => null, 'max_date' => null];

    $min = normalize_ymd($row['min_date'] ?? null);
    $max = normalize_ymd($row['max_date'] ?? null);

    $ranges[$key] = [
        'min_date' => $min,
        'max_date' => $max,
    ];

    if ($min && ($overall_min === null || $min < $overall_min)) {
        $overall_min = $min;
    }
    if ($max && ($overall_max === null || $max > $overall_max)) {
        $overall_max = $max;
    }
}

$ranges['overall'] = [
    'min_date' => $overall_min,
    'max_date' => $overall_max,
];

echo json_encode($ranges);
PHP;

$apiPath = $root . '/api/get_data_range.php';
$ok = @file_put_contents($apiPath, $getDataRange) !== false;
$results[] = ['ok' => $ok, 'label' => 'api/get_data_range.php', 'detail' => $ok ? 'Replaced' : 'Write failed'];

$dash = $root . '/modules/dashboard/index.php';

// 2) Add missing hidden filter inputs on KPI/Financial
patch_file(
    $dash,
    "<?php if (\$active_tab === 'kpi' || \$active_tab === 'financial'): ?>\n    <!-- Figma toolbar lives inside tab -->\n<?php elseif (\$active_tab === 'products'): ?>",
    "<?php if (\$active_tab === 'kpi' || \$active_tab === 'financial'): ?>\n    <!-- Shared date state for Overview / P&L (pickers write here; must always exist) -->\n    <input type=\"hidden\" id=\"filter_from\" value=\"\">\n    <input type=\"hidden\" id=\"filter_to\" value=\"\">\n<?php elseif (\$active_tab === 'products'): ?>",
    'dashboard: add #filter_from/#filter_to'
);

// 3) Stop hardcoding Jan–Mar boot
$oldBoot = <<<'JS'
        // Initialize Flatpickr for Range Selection matching Figma UI
        function initDashboardDatePickers() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr(".date-range-picker", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "M d, Y",
                    defaultDate: [$('#filter_from').val() || "2026-01-01", $('#filter_to').val() || "2026-03-31"],
                    onChange: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            const from = instance.formatDate(selectedDates[0], "Y-m-d");
                            const to = instance.formatDate(selectedDates[1], "Y-m-d");
                            $('#filter_from').val(from);
                            $('#filter_to').val(to);
                            loadDashboard();
                        }
                    }
                });
            }
        }

        // Dashboard Initialization
        $('#filter_from').val('2026-01-01');
        $('#filter_to').val('2026-03-31');
        initDashboardDatePickers();
        loadDashboard();
JS;

$newBoot = <<<'JS'
        // Initialize Flatpickr for Range Selection matching Figma UI
        let dashboardDatePickers = [];
        function initDashboardDatePickers(fromDate, toDate) {
            if (typeof flatpickr === 'undefined') return;
            const from = fromDate || $('#filter_from').val();
            const to = toDate || $('#filter_to').val();
            if (!from || !to) return;

            dashboardDatePickers.forEach(function (fp) {
                try { fp.destroy(); } catch (e) { /* ignore */ }
            });
            dashboardDatePickers = [];

            document.querySelectorAll('.date-range-picker').forEach(function (el) {
                const fp = flatpickr(el, {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "M d, Y",
                    defaultDate: [from, to],
                    onChange: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            const nextFrom = instance.formatDate(selectedDates[0], "Y-m-d");
                            const nextTo = instance.formatDate(selectedDates[1], "Y-m-d");
                            $('#filter_from, .filter-from-input').val(nextFrom);
                            $('#filter_to, .filter-to-input').val(nextTo);
                            loadDashboard();
                        }
                    }
                });
                dashboardDatePickers.push(fp);
            });
        }

        function resolveDashboardDateRange(ranges) {
            const preferred = (ranges && (ranges.trans || ranges.overall || ranges.ads || ranges.brand || ranges.ops)) || null;
            const overall = (ranges && ranges.overall) || preferred;
            let from = overall && overall.min_date ? String(overall.min_date).substring(0, 10) : '';
            let to = overall && overall.max_date ? String(overall.max_date).substring(0, 10) : '';
            if (!from || !to) {
                const now = new Date();
                to = now.toISOString().slice(0, 10);
                from = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0, 10);
            }
            return { from, to };
        }

        // Dashboard Initialization — load real available data range first
        (function bootDashboardDates() {
            const customerId = $('#customer_id_hidden').length
                ? $('#customer_id_hidden').val()
                : ($('#filter_customer').val() || $('.filter-customer-select').val() || '');

            $.get('<?php echo BASE_URL; ?>api/get_data_range.php', { customer_id: customerId || 0 })
                .done(function (ranges) {
                    const span = resolveDashboardDateRange(ranges);
                    $('#filter_from, .filter-from-input').val(span.from);
                    $('#filter_to, .filter-to-input').val(span.to);
                    initDashboardDatePickers(span.from, span.to);
                    loadDashboard();
                })
                .fail(function () {
                    const span = resolveDashboardDateRange(null);
                    $('#filter_from, .filter-from-input').val(span.from);
                    $('#filter_to, .filter-to-input').val(span.to);
                    initDashboardDatePickers(span.from, span.to);
                    loadDashboard();
                });
        })();
JS;

patch_file($dash, $oldBoot, $newBoot, 'dashboard: boot from get_data_range');

// 4) loadDashboard fallbacks — stop forcing Jan–Mar when inputs empty
patch_file(
    $dash,
    "            let from = \$('#filter_from').val() || \$('.filter-from-input').val() || '2026-01-01';\n            let to = \$('#filter_to').val() || \$('.filter-to-input').val() || '2026-03-31';",
    "            let from = \$('#filter_from').val() || \$('.filter-from-input').val() || '';\n            let to = \$('#filter_to').val() || \$('.filter-to-input').val() || '';\n            if (!from || !to) {\n                dashboardLoadInProgress = false;\n                hideLoader();\n                return;\n            }",
    'dashboard: loadDashboard empty-date guard'
);

$allOk = true;
foreach ($results as $r) {
    if (!$r['ok']) $allOk = false;
}
?>
<!DOCTYPE html>
<html><head><title>Date Filter Hotfix</title>
<style>
body{font-family:system-ui,sans-serif;max-width:720px;margin:40px auto;padding:0 16px}
.ok{color:#059669}.bad{color:#dc2626}
li{margin:8px 0}
</style></head><body>
<h1><?php echo $allOk ? 'Date filter hotfix applied' : 'Hotfix finished with errors'; ?></h1>
<ul>
<?php foreach ($results as $r): ?>
<li class="<?php echo $r['ok'] ? 'ok' : 'bad'; ?>">
<strong><?php echo htmlspecialchars($r['label']); ?></strong> — <?php echo htmlspecialchars($r['detail']); ?>
</li>
<?php endforeach; ?>
</ul>
<p>Next: open <a href="modules/dashboard/index.php?tab=financial">Profit &amp; Fees</a> — date range should auto-load to your real data (e.g. Jun–Aug 2026), not Jan–Mar.</p>
<p><strong>Delete this file now:</strong> <code>date_filter_hotfix.php</code></p>
</body></html>
