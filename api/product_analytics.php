<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Normalizing for Detail Report (usually stored monthly as YYYY-MM-01)
$from_bucket = date('Y-m-01', strtotime($from_date));
$to_bucket = date('Y-m-01', strtotime($to_date));

$where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

try {
    // 1. Build ASIN -> SKU mapping dynamically
    $sku_mapping = [];
    $res_t = $conn->query("SHOW TABLES LIKE 'dyn_%'");
    while($t_row = $res_t->fetch_array()) {
        $t_name = $t_row[0];
        $res_c = $conn->query("DESCRIBE `$t_name` ");
        $cols = [];
        if($res_c) {
            while($c_row = $res_c->fetch_assoc()) $cols[] = strtolower($c_row['Field']);
            if (in_array('asin', $cols) && in_array('sku', $cols)) {
                $m_res = $conn->query("SELECT DISTINCT asin, sku FROM `$t_name` WHERE asin IS NOT NULL AND sku IS NOT NULL");
                if ($m_res) {
                    while($m = $m_res->fetch_assoc()) $sku_mapping[trim($m['asin'])] = trim($m['sku']);
                }
            }
        }
    }

    // 2. Fetch Advertising Data (Daily granularity is better for join)
    $sql_ads = "SELECT 
                    advertised_asin, 
                    SUM(spend) as ad_spend, 
                    SUM(total_sales) as ad_sales, 
                    SUM(total_orders) as ad_orders, 
                    SUM(total_units) as ad_units,
                    SUM(clicks) as ad_clicks,
                    SUM(impressions) as ad_impr
                FROM (
                    SELECT advertised_asin, spend, total_sales, total_orders, total_units, clicks, impressions FROM amazon_advertising_sp WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'advertised_product'
                    UNION ALL
                    SELECT advertised_asin, spend, total_sales, total_orders, total_units, clicks, impressions FROM amazon_advertising_sb WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'advertised_product'
                    UNION ALL
                    SELECT advertised_asin, spend, total_sales, total_orders, total_units, clicks, impressions FROM amazon_advertising_sd WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'advertised_product'
                ) t GROUP BY advertised_asin";
    
    $stmt_ads = $conn->prepare($sql_ads);
    if (!$stmt_ads) throw new Exception("Ads Prepare failed: " . $conn->error);
    $stmt_ads->bind_param("ssssss", $from_date, $to_date, $from_date, $to_date, $from_date, $to_date);
    $stmt_ads->execute();
    $ads_res = $stmt_ads->get_result();
    $ad_data = [];
    while($a = $ads_res->fetch_assoc()) {
        $ad_data[trim($a['advertised_asin'])] = $a;
    }

    // 3. Main Product Data from Detail Report
    $sql_top = "SELECT
            asin,
            MAX(title) as title,
            SUM(ordered_product_sales) as revenue,
            SUM(units_ordered) as units,
            SUM(total_order_items) as orders,
            SUM(units_refunded) as refunded_units,
            AVG(refund_rate) as refund_rate,
            SUM(sessions_total) as sessions,
            SUM(sessions_mobile_app) as sessions_mobile,
            SUM(sessions_browser) as sessions_browser,
            SUM(page_views_total) as page_views,
            SUM(page_views_mobile_app) as page_views_mobile,
            SUM(page_views_browser) as page_views_browser,
            AVG(buy_box_percentage) as buy_box_percentage,
            AVG(unit_session_percentage) as unit_session_percentage
        FROM amazon_detail_report
        WHERE $where_customer AND report_date BETWEEN ? AND ?
        GROUP BY asin
        ORDER BY revenue DESC
        LIMIT 50";

    $stmt_p = $conn->prepare($sql_top);
    if (!$stmt_p) throw new Exception("Products Prepare failed: " . $conn->error);
    $stmt_p->bind_param("ss", $from_bucket, $to_bucket);
    $stmt_p->execute();
    $res_p = $stmt_p->get_result();

    $top_products = [];
    while ($r = $res_p->fetch_assoc()) {
        $asin = trim($r['asin']);
        $sku = $sku_mapping[$asin] ?? $asin;
        $rev = (float)$r['revenue'];
        
        $ads = $ad_data[$asin] ?? ['ad_spend'=>0,'ad_sales'=>0,'ad_orders'=>0,'ad_units'=>0,'ad_clicks'=>0,'ad_impr'=>0];
        $ad_sales = (float)$ads['ad_sales'];
        $ad_spend = (float)$ads['ad_spend'];

        $top_products[] = [
            'asin' => $asin,
            'sku' => $sku,
            'name' => $r['title'] ?? 'N/A',
            'revenue' => $rev,
            'units' => (int)$r['units'],
            'orders' => (int)$r['orders'],
            'refunded_units' => (int)$r['refunded_units'],
            'refund_rate' => (float)$r['refund_rate'],
            'sessions' => (int)$r['sessions'],
            'sessions_mobile' => (int)$r['sessions_mobile'],
            'sessions_browser' => (int)$r['sessions_browser'],
            'page_views' => (int)$r['page_views'],
            'page_views_mobile' => (int)$r['page_views_mobile'],
            'page_views_browser' => (int)$r['page_views_browser'],
            'buy_box_percentage' => (float)$r['buy_box_percentage'],
            'conv' => (float)$r['unit_session_percentage'],
            'ad_spend' => $ad_spend,
            'ad_sales' => $ad_sales,
            'acos' => $ad_sales > 0 ? ($ad_spend / $ad_sales) * 100 : 0,
            'ad_dependency' => $rev > 0 ? ($ad_sales / $rev) * 100 : 0
        ];
    }

    echo json_encode([
        'success' => true,
        'from' => $from_date,
        'to' => $to_date,
        'top_products' => $top_products
    ]);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $t->getMessage()]);
}
