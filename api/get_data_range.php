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
?>
