<?php
require_once '../config.php';

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

$where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

$sql = "SELECT
    report_date,
    SUM(ordered_product_sales) as ordered_product_sales,
    SUM(ordered_product_sales_b2b) as ordered_product_sales_b2b,
    SUM(units_ordered) as units_ordered,
    SUM(total_order_items) as total_order_items,
    SUM(page_views_total) as page_views_total,
    SUM(page_views_mobile_app) as page_views_mobile_app,
    SUM(page_views_browser) as page_views_browser,
    SUM(sessions_total) as sessions_total,
    SUM(sessions_mobile_app) as sessions_mobile_app,
    SUM(sessions_browser) as sessions_browser,
    AVG(order_item_session_percentage) as order_item_session_percentage,
    AVG(unit_session_percentage) as unit_session_percentage,
    SUM(units_refunded) as units_refunded,
    AVG(refund_rate) as refund_rate,
    SUM(feedback_received) as feedback_received,
    SUM(negative_feedback_received) as negative_feedback_received,
    SUM(atoz_claims_granted) as atoz_claims_granted,
    SUM(claims_amount) as claims_amount,
    SUM(shipped_product_sales) as shipped_product_sales,
    SUM(units_shipped) as units_shipped,
    SUM(orders_shipped) as orders_shipped,
    AVG(buy_box_percentage) as buy_box_percentage
FROM amazon_business_report
WHERE $where_customer AND report_date BETWEEN ? AND ?
GROUP BY report_date
ORDER BY report_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from_date, $to_date);
$stmt->execute();
$res = $stmt->get_result();

$filename = "business_daily_" . ($customer_id > 0 ? ("customer_" . $customer_id . "_") : "all_") . $from_date . "_to_" . $to_date . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fputcsv($out, [
    'report_date',
    'ordered_product_sales',
    'ordered_product_sales_b2b',
    'units_ordered',
    'total_order_items',
    'page_views_total',
    'page_views_mobile_app',
    'page_views_browser',
    'sessions_total',
    'sessions_mobile_app',
    'sessions_browser',
    'order_item_session_percentage',
    'unit_session_percentage',
    'units_refunded',
    'refund_rate',
    'buy_box_percentage'
]);

while ($row = $res->fetch_assoc()) {
    // Normalize percent values if stored as 0.x
    foreach (['order_item_session_percentage', 'unit_session_percentage', 'refund_rate', 'buy_box_percentage'] as $k) {
        $v = floatval($row[$k] ?? 0);
        if ($v > 0 && $v <= 1.2) $v *= 100;
        $row[$k] = $v;
    }
    fputcsv($out, [
        $row['report_date'],
        (float)($row['ordered_product_sales'] ?? 0),
        (float)($row['ordered_product_sales_b2b'] ?? 0),
        (int)($row['units_ordered'] ?? 0),
        (int)($row['total_order_items'] ?? 0),
        (int)($row['page_views_total'] ?? 0),
        (int)($row['page_views_mobile_app'] ?? 0),
        (int)($row['page_views_browser'] ?? 0),
        (int)($row['sessions_total'] ?? 0),
        (int)($row['sessions_mobile_app'] ?? 0),
        (int)($row['sessions_browser'] ?? 0),
        (float)($row['order_item_session_percentage'] ?? 0),
        (float)($row['unit_session_percentage'] ?? 0),
        (int)($row['units_refunded'] ?? 0),
        (float)($row['refund_rate'] ?? 0),
        (float)($row['buy_box_percentage'] ?? 0),
    ]);
}

fclose($out);
exit();

