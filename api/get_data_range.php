<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$where_customer = ($customer_id > 0) ? "WHERE customer_id = $customer_id" : '';

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

function table_range($conn, $table, $date_col, $where_customer) {
    // Verify table exists
    $chk = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'");
    if (!$chk || $chk->num_rows === 0) {
        return ['min_date' => null, 'max_date' => null];
    }
    $sql = "SELECT MIN($date_col) AS min_date, MAX($date_col) AS max_date FROM `$table` $where_customer";
    $res = $conn->query($sql);
    $row = $res ? $res->fetch_assoc() : null;
    return [
        'min_date' => normalize_ymd($row['min_date'] ?? null),
        'max_date' => normalize_ymd($row['max_date'] ?? null),
    ];
}

function merge_range($a, $b) {
    $min = null;
    $max = null;
    foreach ([$a, $b] as $r) {
        if (!empty($r['min_date']) && ($min === null || $r['min_date'] < $min)) {
            $min = $r['min_date'];
        }
        if (!empty($r['max_date']) && ($max === null || $r['max_date'] > $max)) {
            $max = $r['max_date'];
        }
    }
    return ['min_date' => $min, 'max_date' => $max];
}

$ranges = [];
$ranges['ads_sp'] = table_range($conn, 'amazon_advertising_sp', 'report_date', $where_customer);
$ranges['ads_sb'] = table_range($conn, 'amazon_advertising_sb', 'report_date', $where_customer);
$ranges['ads_sd'] = table_range($conn, 'amazon_advertising_sd', 'report_date', $where_customer);
$ranges['ads'] = merge_range(merge_range($ranges['ads_sp'], $ranges['ads_sb']), $ranges['ads_sd']);

$ranges['trans'] = table_range($conn, 'amazon_transaction_report', 'date_time', $where_customer);
$ranges['brand'] = table_range($conn, 'amazon_brand_reports', 'report_date', $where_customer);
$ranges['ops'] = table_range($conn, 'amazon_returns_reimbursements', 'report_date', $where_customer);
$ranges['detail'] = table_range($conn, 'amazon_detail_report', 'report_date', $where_customer);
$ranges['business'] = table_range($conn, 'amazon_business_report', 'report_date', $where_customer);

$overall = ['min_date' => null, 'max_date' => null];
foreach (['ads', 'trans', 'brand', 'ops', 'detail', 'business'] as $key) {
    $overall = merge_range($overall, $ranges[$key]);
}
$ranges['overall'] = $overall;

// Friendly month labels for UI (no design change — available for tooltips/debug)
$ranges['from_month'] = $overall['min_date'] ? date('M Y', strtotime($overall['min_date'])) : null;
$ranges['to_month'] = $overall['max_date'] ? date('M Y', strtotime($overall['max_date'])) : null;

echo json_encode($ranges);
