<?php
require_once '../config.php';

$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'top';
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Detail report rows are stored monthly (report_date = YYYY-MM-01). Normalize any date range to month buckets.
$from_bucket = date('Y-m-01', strtotime($from_date));
$to_bucket = date('Y-m-01', strtotime($to_date));

$where_customer = ($customer_id > 0) ? "customer_id = ? AND" : "";

if ($type === 'refunds') {
    $sql_base = "SELECT
            asin,
            MAX(title) as title,
            SUM(units_refunded) as refunded_units,
            AVG(refund_rate) as refund_rate,
            SUM(units_ordered) as units,
            SUM(ordered_product_sales) as revenue
        FROM amazon_detail_report
        WHERE %s report_date BETWEEN ? AND ?
        GROUP BY asin
        ORDER BY refunded_units DESC";
} else {
    $sql_base = "SELECT
            asin,
            MAX(title) as title,
            SUM(ordered_product_sales) as revenue,
            SUM(units_ordered) as units,
            SUM(total_order_items) as orders,
            SUM(units_refunded) as refunded_units,
            AVG(refund_rate) as refund_rate,
            SUM(sessions_total) as sessions,
            SUM(page_views_total) as page_views,
            AVG(buy_box_percentage) as buy_box_percentage,
            AVG(unit_session_percentage) as unit_session_percentage
        FROM amazon_detail_report
        WHERE %s report_date BETWEEN ? AND ?
        GROUP BY asin
        ORDER BY revenue DESC";
}

$sql = sprintf($sql_base, $where_customer);
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Prepare failed: " . $conn->error;
    exit;
}

if ($customer_id > 0) {
    $stmt->bind_param("iss", $customer_id, $from_bucket, $to_bucket);
} else {
    $stmt->bind_param("ss", $from_bucket, $to_bucket);
}

$stmt->execute();
$res = $stmt->get_result();

$filename = "product_analytics_" . $type . "_" . ($customer_id > 0 ? ("customer_" . $customer_id . "_") : "all_") . $from_bucket . "_to_" . $to_bucket . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');

if ($type === 'refunds') {
    fputcsv($out, ['asin', 'title', 'refunded_units', 'refund_rate', 'units', 'revenue']);
    while ($row = $res->fetch_assoc()) {
        fputcsv($out, [
            $row['asin'] ?? '',
            $row['title'] ?? '',
            (int)($row['refunded_units'] ?? 0),
            (float)($row['refund_rate'] ?? 0),
            (int)($row['units'] ?? 0),
            (float)($row['revenue'] ?? 0),
        ]);
    }
} else {
    fputcsv($out, ['asin', 'title', 'revenue', 'units', 'orders', 'refunded_units', 'refund_rate', 'sessions', 'page_views', 'buy_box_percentage', 'unit_session_percentage']);
    while ($row = $res->fetch_assoc()) {
        fputcsv($out, [
            $row['asin'] ?? '',
            $row['title'] ?? '',
            (float)($row['revenue'] ?? 0),
            (int)($row['units'] ?? 0),
            (int)($row['orders'] ?? 0),
            (int)($row['refunded_units'] ?? 0),
            (float)($row['refund_rate'] ?? 0),
            (int)($row['sessions'] ?? 0),
            (int)($row['page_views'] ?? 0),
            (float)($row['buy_box_percentage'] ?? 0),
            (float)($row['unit_session_percentage'] ?? 0),
        ]);
    }
}

fclose($out);
exit;

