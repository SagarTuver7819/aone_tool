<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

function normalizeDate($date) {
    if (empty($date)) return date('Y-m-d');
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
        $parts = explode('/', $date);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return $date;
}

try {
    $customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
    $from_date = normalizeDate($_GET['from_date'] ?? date('Y-m-01', strtotime('-30 days')));
    $to_date = normalizeDate($_GET['to_date'] ?? date('Y-m-d'));

    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

    $sql = "SELECT
        report_date,
        SUM(ordered_product_sales) as sales,
        SUM(ordered_product_sales_b2b) as b2b_sales,
        SUM(units_ordered) as units,
        SUM(sessions_total) as sessions,
        SUM(page_views_total) as page_views,
        AVG(unit_session_percentage) as conversion,
        SUM(units_refunded) as refunds,
        AVG(refund_rate) as refund_rate,
        SUM(shipped_product_sales) as shipped_sales,
        SUM(units_shipped) as units_shipped,
        SUM(orders_shipped) as orders_shipped
    FROM amazon_business_report
    WHERE $where_customer AND report_date BETWEEN ? AND ?
    GROUP BY report_date
    ORDER BY report_date ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res) throw new Exception("Get result failed: " . $stmt->error);

    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $conversion = (float)($r['conversion'] ?? 0);
        $refund_rate = (float)($r['refund_rate'] ?? 0);
        // If stored as 0.x, normalize to percent.
        if ($conversion > 0 && $conversion <= 1.2) $conversion *= 100;
        if ($refund_rate > 0 && $refund_rate <= 1.2) $refund_rate *= 100;

        $rows[] = [
            'report_date' => $r['report_date'],
            'sales' => (float)($r['sales'] ?? 0),
            'b2b_sales' => (float)($r['b2b_sales'] ?? 0),
            'units' => (int)($r['units'] ?? 0),
            'sessions' => (int)($r['sessions'] ?? 0),
            'page_views' => (int)($r['page_views'] ?? 0),
            'conversion' => $conversion,
            'refunds' => (int)($r['refunds'] ?? 0),
            'refund_rate' => $refund_rate,
            'shipped_sales' => (float)($r['shipped_sales'] ?? 0),
            'units_shipped' => (int)($r['units_shipped'] ?? 0),
            'orders_shipped' => (int)($r['orders_shipped'] ?? 0)
        ];
    }

    $json = json_encode([
        'success' => true,
        'customer_id' => $customer_id,
        'from_date' => $from_date,
        'to_date' => $to_date,
        'rows' => $rows
    ], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);

    if ($json === false) throw new Exception('JSON encode failed: ' . json_last_error_msg());
    echo $json;
} catch (Throwable $t) {
    http_response_code(500);
    $json = json_encode(['error' => $t->getMessage()], JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    echo ($json === false) ? '{"error":"Unknown backend error"}' : $json;
}

