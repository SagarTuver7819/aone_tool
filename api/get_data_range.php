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

// Core sources (exclude transaction edge-dates like Sep 01 with no full month)
$core = ['min_date' => null, 'max_date' => null];
foreach (['ads', 'brand', 'ops', 'detail', 'business'] as $key) {
    $core = merge_range($core, $ranges[$key]);
}

// If overall max is only the 1st of a month (common txn spillover) and that month
// has no ads/business/detail coverage, snap filter end to previous month-end / core max.
if (!empty($overall['max_date'])) {
    $max_ts = strtotime($overall['max_date']);
    $day_num = (int) date('j', $max_ts);
    $month_start = date('Y-m-01', $max_ts);
    $core_max = $core['max_date'] ?? null;

    $month_has_core = false;
    if ($core_max && $core_max >= $month_start) {
        $month_has_core = true;
    }

    if ($day_num === 1 && !$month_has_core) {
        // Confirm this month is basically empty beyond day 1 in transactions
        $where_cid = ($customer_id > 0) ? "customer_id = $customer_id AND" : '';
        $month_end = date('Y-m-t', $max_ts);
        $sql_edge = "SELECT COUNT(DISTINCT DATE(date_time)) AS days_c,
                            SUM(CASE WHEN type='Order' THEN product_sales ELSE 0 END) AS sales
                     FROM amazon_transaction_report
                     WHERE $where_cid date_time BETWEEN '{$month_start} 00:00:00' AND '{$month_end} 23:59:59'";
        $edge = $conn->query($sql_edge);
        $edge_row = $edge ? $edge->fetch_assoc() : null;
        $days_c = intval($edge_row['days_c'] ?? 0);
        $sales_c = floatval($edge_row['sales'] ?? 0);

        // Snap away from empty/near-empty month (e.g. only Sep 01 crumbs)
        if ($days_c <= 1 || $sales_c < 500) {
            $snap = date('Y-m-d', strtotime($month_start . ' -1 day'));
            if ($core_max && $core_max > $snap) {
                $snap = $core_max;
            }
            $overall['max_date'] = $snap;
        }
    }
}

$ranges['overall'] = $overall;
$ranges['core'] = $core;

// Friendly month labels for UI (no design change — available for tooltips/debug)
$ranges['from_month'] = $overall['min_date'] ? date('M Y', strtotime($overall['min_date'])) : null;
$ranges['to_month'] = $overall['max_date'] ? date('M Y', strtotime($overall['max_date'])) : null;

echo json_encode($ranges);
