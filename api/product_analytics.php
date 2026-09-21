<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

require_api_auth();

$customer_id = resolve_request_customer_id();
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Normalize
$from_ts = strtotime($from_date);
$to_ts = strtotime($to_date);
if ($from_ts === false) $from_ts = strtotime(date('Y-m-01'));
if ($to_ts === false) $to_ts = time();
$from_date = date('Y-m-d', $from_ts);
$to_date = date('Y-m-d', $to_ts);

$from_bucket = date('Y-m-01', $from_ts);
$to_bucket = date('Y-m-01', $to_ts);
$dt_start = $from_date . ' 00:00:00';
$dt_end = $to_date . ' 23:59:59';

$where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

try {
    // 1. ASIN -> SKU mapping from dyn_* tables
    $sku_mapping = [];
    $asin_by_sku = [];
    $res_t = $conn->query("SHOW TABLES LIKE 'dyn_%'");
    if ($res_t) {
        while ($t_row = $res_t->fetch_array()) {
            $t_name = $t_row[0];
            $res_c = $conn->query("DESCRIBE `$t_name`");
            $cols = [];
            if ($res_c) {
                while ($c_row = $res_c->fetch_assoc()) $cols[] = strtolower($c_row['Field']);
                if (in_array('asin', $cols) && in_array('sku', $cols)) {
                    $m_res = $conn->query("SELECT DISTINCT asin, sku FROM `$t_name` WHERE asin IS NOT NULL AND sku IS NOT NULL");
                    if ($m_res) {
                        while ($m = $m_res->fetch_assoc()) {
                            $a = trim($m['asin']);
                            $s = trim($m['sku']);
                            if ($a !== '' && $s !== '') {
                                $sku_mapping[$a] = $s;
                                $asin_by_sku[$s] = $a;
                            }
                        }
                    }
                }
            }
        }
    }

    // 2. Advertising by ASIN + SKU
    $sql_ads = "SELECT 
                    advertised_asin,
                    advertised_sku,
                    SUM(spend) as ad_spend, 
                    SUM(total_sales) as ad_sales, 
                    SUM(total_orders) as ad_orders, 
                    SUM(total_units) as ad_units,
                    SUM(clicks) as ad_clicks,
                    SUM(impressions) as ad_impr
                FROM (
                    SELECT advertised_asin, advertised_sku, spend, total_sales, total_orders, total_units, clicks, impressions
                    FROM amazon_advertising_sp
                    WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'general'
                    UNION ALL
                    SELECT advertised_asin, advertised_sku, spend, total_sales, total_orders, total_units, clicks, impressions
                    FROM amazon_advertising_sb
                    WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                    UNION ALL
                    SELECT advertised_asin, advertised_sku, spend, total_sales, total_orders, total_units, clicks, impressions
                    FROM amazon_advertising_sd
                    WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                ) t
                GROUP BY advertised_asin, advertised_sku";

    $stmt_ads = $conn->prepare($sql_ads);
    if (!$stmt_ads) throw new Exception("Ads Prepare failed: " . $conn->error);
    $stmt_ads->bind_param("ssssss", $from_date, $to_date, $from_date, $to_date, $from_date, $to_date);
    $stmt_ads->execute();
    $ads_res = $stmt_ads->get_result();
    $ad_by_asin = [];
    $ad_by_sku = [];
    while ($a = $ads_res->fetch_assoc()) {
        $asin = trim((string)($a['advertised_asin'] ?? ''));
        $sku = trim((string)($a['advertised_sku'] ?? ''));
        if ($asin !== '') {
            if (!isset($ad_by_asin[$asin])) {
                $ad_by_asin[$asin] = $a;
            } else {
                foreach (['ad_spend','ad_sales','ad_orders','ad_units','ad_clicks','ad_impr'] as $k) {
                    $ad_by_asin[$asin][$k] = floatval($ad_by_asin[$asin][$k]) + floatval($a[$k]);
                }
            }
        }
        if ($sku !== '') {
            if (!isset($ad_by_sku[$sku])) {
                $ad_by_sku[$sku] = $a;
            } else {
                foreach (['ad_spend','ad_sales','ad_orders','ad_units','ad_clicks','ad_impr'] as $k) {
                    $ad_by_sku[$sku][$k] = floatval($ad_by_sku[$sku][$k]) + floatval($a[$k]);
                }
            }
        }
    }

    // 3. Prefer detail report (try full date range, then monthly buckets)
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

    $detail_rows = [];
    foreach ([[$from_date, $to_date], [$from_bucket, $to_bucket]] as $range) {
        $stmt_p = $conn->prepare($sql_top);
        if (!$stmt_p) throw new Exception("Products Prepare failed: " . $conn->error);
        $stmt_p->bind_param("ss", $range[0], $range[1]);
        $stmt_p->execute();
        $res_p = $stmt_p->get_result();
        $detail_rows = [];
        while ($r = $res_p->fetch_assoc()) $detail_rows[] = $r;
        if (count($detail_rows) > 0) break;
    }

    $top_products = [];
    $source = 'detail';

    if (count($detail_rows) > 0) {
        foreach ($detail_rows as $r) {
            $asin = trim((string)$r['asin']);
            $sku = $sku_mapping[$asin] ?? $asin;
            $rev = (float)$r['revenue'];
            $ads = $ad_by_asin[$asin] ?? ($ad_by_sku[$sku] ?? ['ad_spend'=>0,'ad_sales'=>0,'ad_orders'=>0,'ad_units'=>0,'ad_clicks'=>0,'ad_impr'=>0]);
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
    } else {
        // 4. Fallback: transaction report SKU rollup (date-filtered)
        $source = 'transactions';
        $sql_txn = "SELECT
                sku,
                MAX(description) as title,
                SUM(CASE WHEN type = 'Order' THEN product_sales ELSE 0 END) as revenue,
                SUM(CASE WHEN type = 'Order' THEN quantity ELSE 0 END) as units,
                SUM(CASE WHEN type = 'Order' THEN 1 ELSE 0 END) as orders,
                SUM(CASE WHEN type = 'Refund' THEN ABS(quantity) ELSE 0 END) as refunded_units
            FROM amazon_transaction_report
            WHERE $where_customer
              AND date_time BETWEEN ? AND ?
              AND type IN ('Order', 'Refund')
              AND sku IS NOT NULL AND sku != ''
            GROUP BY sku
            HAVING revenue > 0 OR units > 0
            ORDER BY revenue DESC
            LIMIT 50";
        $stmt_txn = $conn->prepare($sql_txn);
        if (!$stmt_txn) throw new Exception("Txn Prepare failed: " . $conn->error);
        $stmt_txn->bind_param("ss", $dt_start, $dt_end);
        $stmt_txn->execute();
        $res_txn = $stmt_txn->get_result();

        while ($r = $res_txn->fetch_assoc()) {
            $sku = trim((string)$r['sku']);
            $asin = $asin_by_sku[$sku] ?? $sku;
            $rev = (float)$r['revenue'];
            $units = (int)$r['units'];
            $refunded = (int)$r['refunded_units'];
            $ads = $ad_by_sku[$sku] ?? ($ad_by_asin[$asin] ?? ['ad_spend'=>0,'ad_sales'=>0,'ad_orders'=>0,'ad_units'=>0,'ad_clicks'=>0,'ad_impr'=>0]);
            $ad_sales = (float)$ads['ad_sales'];
            $ad_spend = (float)$ads['ad_spend'];
            $title = trim((string)($r['title'] ?? ''));
            if ($title === '' || stripos($title, $sku) === 0) $title = $sku;

            $top_products[] = [
                'asin' => $asin,
                'sku' => $sku,
                'name' => $title,
                'revenue' => $rev,
                'units' => $units,
                'orders' => (int)$r['orders'],
                'refunded_units' => $refunded,
                'refund_rate' => $units > 0 ? ($refunded / $units) * 100 : 0,
                'sessions' => 0,
                'sessions_mobile' => 0,
                'sessions_browser' => 0,
                'page_views' => 0,
                'page_views_mobile' => 0,
                'page_views_browser' => 0,
                'buy_box_percentage' => 0,
                'conv' => 0,
                'ad_spend' => $ad_spend,
                'ad_sales' => $ad_sales,
                'acos' => $ad_sales > 0 ? ($ad_spend / $ad_sales) * 100 : 0,
                'ad_dependency' => $rev > 0 ? ($ad_sales / $rev) * 100 : 0
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'from' => $from_date,
        'to' => $to_date,
        'source' => $source,
        'top_products' => $top_products
    ]);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $t->getMessage()]);
}
