<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

function normalizeDate($date) {
    if (empty($date)) return date('Y-m-d');
    // Handle DD/MM/YYYY
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $m)) {
        return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
    }
    // Handle MM/DD/YYYY
    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $date, $m)) {
         return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);
    }
    return $date;
}

function truncateUtf8($value, $maxChars) {
    $s = (string)($value ?? '');
    if ($maxChars <= 0) return '';

    // Prefer multibyte-safe trimming to avoid breaking UTF-8 sequences (which can make json_encode() return false).
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($s, 'UTF-8') > $maxChars) {
            return mb_substr($s, 0, max(0, $maxChars - 3), 'UTF-8') . '...';
        }
        return $s;
    }

    if (function_exists('iconv_strlen') && function_exists('iconv_substr')) {
        $len = @iconv_strlen($s, 'UTF-8');
        if ($len !== false && $len > $maxChars) {
            $cut = @iconv_substr($s, 0, max(0, $maxChars - 3), 'UTF-8');
            return ($cut === false ? '' : $cut) . '...';
        }
        return $s;
    }

    // Byte-based fallback: trim then ensure resulting string is valid UTF-8.
    if (strlen($s) > $maxChars) {
        $cut = substr($s, 0, max(0, $maxChars - 3));
        if (!preg_match('//u', $cut)) {
            // Strip trailing UTF-8 continuation bytes, then back off until valid.
            $cut = preg_replace('/[\x80-\xBF]+$/', '', $cut);
            while ($cut !== '' && !preg_match('//u', $cut)) {
                $cut = substr($cut, 0, -1);
            }
        }
        return $cut . '...';
    }

    return $s;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $session_customer_id = $_SESSION['customer_id'] ?? 0;
    $requested_customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
    
    // Enforce role-based restriction
    if (($_SESSION['role'] ?? '') === 'customer') {
        $customer_id = $session_customer_id;
    } else {
        $customer_id = $requested_customer_id;
    }

    $from_date = normalizeDate($_GET['from_date'] ?? '');
    $to_date = normalizeDate($_GET['to_date'] ?? '');

    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

    // 1. Daily Data
    $sql_daily = "SELECT 
        report_date,
        SUM(ordered_product_sales) as sales,
        SUM(units_ordered) as units,
        SUM(sessions_total) as sessions,
        SUM(total_order_items) as orders,
        SUM(page_views_total) as page_views,
        AVG(buy_box_percentage) as buy_box,
        AVG(unit_session_percentage) as conversion,
        SUM(units_refunded) as refunds,
        AVG(refund_rate) as refund_rate,
        SUM(ordered_product_sales_b2b) as b2b_sales,
        SUM(sessions_mobile_app) as sessions_mobile,
        SUM(sessions_browser) as sessions_browser,
        SUM(page_views_mobile_app) as page_views_mobile,
        SUM(page_views_browser) as page_views_browser,
        SUM(shipped_product_sales) as shipped_sales,
        SUM(feedback_received) as feedback,
        SUM(atoz_claims_granted) as atoz
        FROM amazon_business_report 
        WHERE $where_customer AND report_date BETWEEN ? AND ?
        GROUP BY report_date ORDER BY report_date ASC";

    $stmt = $conn->prepare($sql_daily);
    if (!$stmt) throw new Exception("Prepare failed (Daily): " . $conn->error);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $res_daily = $stmt->get_result();
    if ($res_daily === false) throw new Exception("Get result failed (Daily): " . $stmt->error);

    $charts = ['labels' => [], 'sales' => [], 'units' => [], 'sessions' => [], 'orders' => [], 'page_views' => [], 'buy_box' => [], 'conversion' => [], 'refunds' => [], 'refund_rate' => [], 'b2b_sales' => [], 'sessions_mobile' => [], 'sessions_browser' => [], 'page_views_mobile' => [], 'page_views_browser' => [], 'shipped_sales' => [], 'feedback' => [], 'atoz' => []];

    while ($row = $res_daily->fetch_assoc()) {
        $charts['labels'][] = date('d M', strtotime($row['report_date']));
        $charts['sales'][] = floatval($row['sales']);
        $charts['units'][] = intval($row['units']);
        $charts['sessions'][] = intval($row['sessions']);
        $charts['orders'][] = intval($row['orders']);
        $charts['page_views'][] = intval($row['page_views']);
        $charts['buy_box'][] = floatval($row['buy_box']);
        $charts['conversion'][] = floatval($row['conversion']);
        $charts['refunds'][] = intval($row['refunds']);
        $charts['refund_rate'][] = floatval($row['refund_rate']);
        $charts['b2b_sales'][] = floatval($row['b2b_sales']);
        $charts['sessions_mobile'][] = intval($row['sessions_mobile']);
        $charts['sessions_browser'][] = intval($row['sessions_browser']);
        $charts['page_views_mobile'][] = intval($row['page_views_mobile']);
        $charts['page_views_browser'][] = intval($row['page_views_browser']);
        $charts['shipped_sales'][] = floatval($row['shipped_sales']);
        $charts['feedback'][] = intval($row['feedback']);
        $charts['atoz'][] = intval($row['atoz']);
    }

    // 2. Totals
    // Base Totals from Business Report
    $sql_totals = "SELECT SUM(ordered_product_sales) as total_sales, SUM(units_ordered) as total_units, SUM(sessions_total) as total_sessions, SUM(page_views_total) as total_page_views, AVG(unit_session_percentage) as avg_conversion, SUM(total_order_items) as total_orders, SUM(ordered_product_sales_b2b) as b2b_sales, AVG(buy_box_percentage) as buy_box FROM amazon_business_report WHERE $where_customer AND report_date BETWEEN ? AND ?";
    $stmt_f = $conn->prepare($sql_totals);
    if (!$stmt_f) throw new Exception("Prepare failed (Totals): " . $conn->error);
    $stmt_f->bind_param("ss", $from_date, $to_date);
    $stmt_f->execute();
    $totals_full = $stmt_f->get_result()->fetch_assoc() ?: [];

    // Accurate Refunds from Transaction Report
    $sql_refund_total = "SELECT COUNT(*) as total_refunds, (COUNT(*) / NULLIF((SELECT SUM(units_ordered) FROM amazon_business_report WHERE $where_customer AND report_date BETWEEN ? AND ?), 0)) * 100 as avg_refund_rate FROM amazon_transaction_report WHERE $where_customer AND type = 'Refund' AND date_time BETWEEN ? AND ?";
    $stmt_ref = $conn->prepare($sql_refund_total);
    $stmt_ref->bind_param("ssss", $from_date, $to_date, $from_date, $to_date);
    $stmt_ref->execute();
    $ref_res = $stmt_ref->get_result()->fetch_assoc();
    $totals_full['total_refunds'] = $ref_res['total_refunds'] ?? 0;
    $totals_full['avg_refund_rate'] = $ref_res['avg_refund_rate'] ?? 0;

    // --- NEW: Previous Period Comparison ---
    $d_start = new DateTime($from_date);
    $d_end = new DateTime($to_date);
    $diff_days = $d_start->diff($d_end)->days + 1;

    $prev_to = date('Y-m-d', strtotime($from_date . ' -1 day'));
    $prev_from = date('Y-m-d', strtotime($prev_to . ' -' . ($diff_days - 1) . ' days'));
    $cmp_start_1 = $prev_from;
    $cmp_end_1 = $prev_to;
    $cmp_start_2 = $from_date;
    $cmp_end_2 = $to_date;

    $stmt_f->bind_param("ss", $prev_from, $prev_to);
    $stmt_f->execute();
    $totals_prev = $stmt_f->get_result()->fetch_assoc() ?: [];

    // Fallback: If previous period has NO data, compare the last half of current period with the first half
    $has_prev_data = ($totals_prev['total_sales'] ?? 0) > 0 || ($totals_prev['total_units'] ?? 0) > 0;
    if (!$has_prev_data && $diff_days >= 2) {
        $mid_days = floor($diff_days / 2);
        $mid_date = date('Y-m-d', strtotime($from_date . " + $mid_days days"));
        $split_start_end = date('Y-m-d', strtotime($mid_date . " - 1 day"));
        $cmp_start_1 = $from_date;
        $cmp_end_1 = $split_start_end;
        $cmp_start_2 = $mid_date;
        $cmp_end_2 = $to_date;

        // First half
        $stmt_f->bind_param("ss", $from_date, $split_start_end);
        $stmt_f->execute();
        $totals_prev = $stmt_f->get_result()->fetch_assoc() ?: [];

        // Second half (Current)
        $stmt_f->bind_param("ss", $mid_date, $to_date);
        $stmt_f->execute();
        $totals_full_new = $stmt_f->get_result()->fetch_assoc() ?: [];
        
        // Note: We don't overwrite $totals_full because that's for the WHOLE range.
        // We only use this split for the comparison indicators.
        $cmp_current = $totals_full_new;
    } else {
        $cmp_current = $totals_full;
    }

    function getComparison($curr, $prev) {
        $c = floatval($curr ?? 0);
        $p = floatval($prev ?? 0);
        if ($p <= 0) return ['pct' => 0, 'dir' => 'none'];
        $diff = $c - $p;
        return [
            'pct' => number_format(abs(($diff / $p) * 100), 1),
            'dir' => $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'none')
        ];
    }

    
    // --- Additional Advertising & Transaction Data for Comparisons ---
    $sql_ads_cmp = "SELECT SUM(spend) as spend, SUM(total_sales) as ad_sales 
                    FROM (
                        SELECT spend, total_sales FROM amazon_advertising_sp WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'general'
                        UNION ALL
                        SELECT spend, total_sales FROM amazon_advertising_sb WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                        UNION ALL
                        SELECT spend, total_sales FROM amazon_advertising_sd WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                    ) as t";
    $stmt_ads_cmp = $conn->prepare($sql_ads_cmp);
    
    $stmt_ads_cmp->bind_param("ssssss", $cmp_start_1, $cmp_end_1, $cmp_start_1, $cmp_end_1, $cmp_start_1, $cmp_end_1);
    $stmt_ads_cmp->execute();
    $ads_prev = $stmt_ads_cmp->get_result()->fetch_assoc();
    
    $stmt_ads_cmp->bind_param("ssssss", $cmp_start_2, $cmp_end_2, $cmp_start_2, $cmp_end_2, $cmp_start_2, $cmp_end_2);
    $stmt_ads_cmp->execute();
    $ads_curr = $stmt_ads_cmp->get_result()->fetch_assoc();

    $ad_spend_prev = floatval($ads_prev['spend'] ?? 0);
    $ad_sales_prev = floatval($ads_prev['ad_sales'] ?? 0);
    $ad_spend_curr = floatval($ads_curr['spend'] ?? 0);
    $ad_sales_curr = floatval($ads_curr['ad_sales'] ?? 0);

    $sales_prev = floatval($totals_prev['total_sales'] ?? 0);
    $sales_curr = floatval($cmp_current['total_sales'] ?? 0);

    $organic_prev = max(0, $sales_prev - $ad_sales_prev);
    $organic_curr = max(0, $sales_curr - $ad_sales_curr);

    $acos_prev = $ad_sales_prev > 0 ? ($ad_spend_prev / $ad_sales_prev) * 100 : 0;
    $acos_curr = $ad_sales_curr > 0 ? ($ad_spend_curr / $ad_sales_curr) * 100 : 0;

    $tacos_prev = $sales_prev > 0 ? ($ad_spend_prev / $sales_prev) * 100 : 0;
    $tacos_curr = $sales_curr > 0 ? ($ad_spend_curr / $sales_curr) * 100 : 0;

    $roas_prev = $ad_spend_prev > 0 ? ($ad_sales_prev / $ad_spend_prev) : 0;
    $roas_curr = $ad_spend_curr > 0 ? ($ad_sales_curr / $ad_spend_curr) : 0;
    
    // Reverse direction for ACoS and TACoS (lower is better)
    function getComparisonRev($curr, $prev) {
        $c = floatval($curr ?? 0);
        $p = floatval($prev ?? 0);
        if ($p <= 0) return ['pct' => 0, 'dir' => 'none'];
        $diff = $c - $p;
        return [
            'pct' => number_format(abs(($diff / $p) * 100), 1),
            'dir' => $diff < 0 ? 'up' : ($diff > 0 ? 'down' : 'none')
        ];
    }

    $comparisons = [
        'sales' => getComparison($cmp_current['total_sales'], $totals_prev['total_sales']),
        'orders' => getComparison($cmp_current['total_orders'], $totals_prev['total_orders']),
        'units' => getComparison($cmp_current['total_units'], $totals_prev['total_units']),
        'sessions' => getComparison($cmp_current['total_sessions'] ?? 0, $totals_prev['total_sessions'] ?? 0),
        'dsr' => getComparison($cmp_current['total_sales'], $totals_prev['total_sales']), // Identical percentage to sales
        'ad_sales' => getComparison($ad_sales_curr, $ad_sales_prev),
        'organic' => getComparison($organic_curr, $organic_prev),
        'spend' => getComparisonRev($ad_spend_curr, $ad_spend_prev), // Lower spend is technically better, or up if spend increases? Usually spend up is 'down' in green/red context
        'acos' => getComparisonRev($acos_curr, $acos_prev),
        'tacos' => getComparisonRev($tacos_curr, $tacos_prev),
        'roas' => getComparison($roas_curr, $roas_prev),
        'conv' => getComparison($cmp_current['avg_conversion'] ?? 0, $totals_prev['avg_conversion'] ?? 0),
        'refunds' => getComparisonRev($cmp_current['avg_refund_rate'] ?? 0, $totals_prev['avg_refund_rate'] ?? 0),
        'b2b' => getComparison($cmp_current['b2b_sales'] ?? 0, $totals_prev['b2b_sales'] ?? 0)
    ];

    // Net Profit Comparison
    $sql_trans_cmp = "SELECT SUM(total) as net FROM amazon_transaction_report WHERE $where_customer AND date_time BETWEEN ? AND ? AND type != 'Transfer'";
    $stmt_trans_cmp = $conn->prepare($sql_trans_cmp);
    
    $dt_start_1 = $cmp_start_1 . ' 00:00:00';
    $dt_end_1 = $cmp_end_1 . ' 23:59:59';
    $stmt_trans_cmp->bind_param("ss", $dt_start_1, $dt_end_1);
    $stmt_trans_cmp->execute();
    $net_prev = floatval($stmt_trans_cmp->get_result()->fetch_assoc()['net'] ?? 0);
    
    $dt_start_2 = $cmp_start_2 . ' 00:00:00';
    $dt_end_2 = $cmp_end_2 . ' 23:59:59';
    $stmt_trans_cmp->bind_param("ss", $dt_start_2, $dt_end_2);
    $stmt_trans_cmp->execute();
    $net_curr = floatval($stmt_trans_cmp->get_result()->fetch_assoc()['net'] ?? 0);
    
    // Net profit roughly = trans total - cogs - ad spend
    // We already have ad_spend_prev and ad_spend_curr. We ignore COGS for rough comparison if it's monthly.
    $net_profit_prev = $net_prev - $ad_spend_prev;
    $net_profit_curr = $net_curr - $ad_spend_curr;
    
    $comparisons['net_profit'] = getComparison($net_profit_curr, $net_profit_prev);

    // ----------------------------------------

    // 3. Financials
    // 3. Financials (Aggregated for the date range)
    $month_start = date('Y-m-01', strtotime($from_date));
    $month_end = date('Y-m-01', strtotime($to_date));
    $sql_fin = "SELECT SUM(cogs) as cogs, SUM(ad_spend) as ad_spend, SUM(other_fees) as other_fees 
                FROM financial_settings 
                WHERE $where_customer AND report_month BETWEEN ? AND ?";
    $stmt_fin = $conn->prepare($sql_fin);
    $stmt_fin->bind_param("ss", $month_start, $month_end);
    $stmt_fin->execute();
    $fin_settings = $stmt_fin->get_result()->fetch_assoc() ?: ['cogs' => 0, 'ad_spend' => 0, 'other_fees' => 0];

    // --- UPDATED: P&L Logic based on Sellerboard Standards ---
    // Gross = Product Sales - Service (Ads/Sub) + Adjustments - Inventory Fees - Return Fees
    $sql_trans = "SELECT 
        SUM(product_sales) as product_sales_only,
        SUM(shipping_credits) as shipping_credits,
        SUM(gift_wrap_credits) as gift_wrap_credits,
        SUM(product_sales + shipping_credits + gift_wrap_credits) as gross_revenue,
        SUM(CASE WHEN type IN ('Service Fee', 'ServiceFee') THEN total ELSE 0 END) as service_fees,
        SUM(CASE WHEN type IN ('Adjustment', 'Reimbursement') THEN total ELSE 0 END) as adjustments,
        SUM(CASE WHEN type IN ('FBA Inventory Fee', 'Inventory Fee') THEN total ELSE 0 END) as inventory_fees,
        SUM(CASE WHEN type LIKE '%Return%Fee%' OR description LIKE '%Customer Return%Fee%' THEN total ELSE 0 END) as return_fees,
        SUM(selling_fees) as selling_fees,
        SUM(fba_fees) as fba_fees,
        SUM(total) as total_settlement,
        SUM(cogs) as auto_cogs
        FROM amazon_transaction_report 
        WHERE $where_customer 
          AND date_time BETWEEN ? AND ?
          AND type != 'Transfer'";
    
    $stmt_trans = $conn->prepare($sql_trans);
    $dt_start = $from_date . " 00:00:00";
    $dt_end = $to_date . " 23:59:59";
    $stmt_trans->bind_param("ss", $dt_start, $dt_end);
    $stmt_trans->execute();
    $trans_res = $stmt_trans->get_result()->fetch_assoc();

    // Use the accurate revenue from Business Report (to match KPI cards)
    $revenue = floatval($totals_full['total_sales'] ?? 0);
    
    // Accurate PL revenue from Transaction Report
    $pl_revenue = floatval($trans_res['gross_revenue'] ?? 0);
    
    // Total Amazon Fees (all categories from Transaction Report)
    $selling_fees = floatval($trans_res['selling_fees'] ?? 0);
    $fba_fees = floatval($trans_res['fba_fees'] ?? 0);
    $serv = floatval($trans_res['service_fees'] ?? 0); 
    $adj = floatval($trans_res['adjustments'] ?? 0);  
    $inv = floatval($trans_res['inventory_fees'] ?? 0); 
    $ret = floatval($trans_res['return_fees'] ?? 0);    
    
    $total_amazon_fees = $selling_fees + $fba_fees + $serv + $adj + $inv + $ret;
    
    // Calculated Gross Profit: PL Revenue - All Fees
    $pl_gross_profit = $pl_revenue + $total_amazon_fees; // Fees are already negative
    $gross_calc = $pl_gross_profit; // Align main calculations with accurate transaction-based gross profit
    
    // 3.1 Advertising Data
    $sql_ads = "SELECT SUM(spend) as total_spend, SUM(total_sales) as total_ad_sales, SUM(total_orders) as total_ad_orders 
                FROM (
                    SELECT spend, total_sales, total_orders FROM amazon_advertising_sp WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'general'
                    UNION ALL
                    SELECT spend, total_sales, total_orders FROM amazon_advertising_sb WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                    UNION ALL
                    SELECT spend, total_sales, total_orders FROM amazon_advertising_sd WHERE $where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                ) as t";
    $stmt_ads = $conn->prepare($sql_ads);
    $stmt_ads->bind_param("ssssss", $from_date, $to_date, $from_date, $to_date, $from_date, $to_date);
    $stmt_ads->execute();
    $ads_res = $stmt_ads->get_result()->fetch_assoc();
    
    $ad_spend = floatval($ads_res['total_spend'] ?? 0);
    $ad_sales = floatval($ads_res['total_ad_sales'] ?? 0);
    $organic_sales = max(0, $revenue - $ad_sales);
    
    $acos = $ad_sales > 0 ? ($ad_spend / $ad_sales) * 100 : 0;
    $roas = $ad_spend > 0 ? ($ad_sales / $ad_spend) : 0;
    $tacos = $revenue > 0 ? ($ad_spend / $revenue) * 100 : 0;
    
    $num_days = max(1, $diff_days);
    $dsr = $revenue / $num_days;
    
    // AUTO COGS from Excel
    $auto_cogs = floatval($trans_res['auto_cogs'] ?? 0);
    $cogs = $auto_cogs > 0 ? $auto_cogs : floatval($fin_settings['cogs'] ?? 0);
    
    // Final Net Profit: Gross Profit - Product Costs
    $net_profit = $gross_calc - $cogs;
    $net_margin = $pl_revenue > 0 ? ($net_profit / $pl_revenue) * 100 : 0;

    $financials_payload = [
        'revenue' => $pl_revenue,
        'product_sales' => floatval($trans_res['product_sales_only'] ?? 0),
        'shipping_credits' => floatval($trans_res['shipping_credits'] ?? 0),
        'gift_wrap_credits' => floatval($trans_res['gift_wrap_credits'] ?? 0),
        'selling_fees' => floatval($trans_res['selling_fees'] ?? 0),
        'fba_fees' => floatval($trans_res['fba_fees'] ?? 0),
        'adjustments' => $adj,
        'service_fees' => $serv,
        'inventory_fees' => $inv,
        'return_fees' => $ret,
        'cogs' => $cogs,
        'net_profit' => $net_profit,
        'net_margin' => $net_margin,
        'gross_profit' => $gross_calc,
        'total_settlement' => floatval($trans_res['total_settlement'] ?? 0)
    ];

    // 4. Trends
    $trend_data = [];
    $target_month = date('Y-m-01', strtotime($to_date));
    for ($i = 2; $i >= 0; $i--) {
        $m = date('Y-m-01', strtotime("$target_month -$i months"));
        $m_end = date('Y-m-t', strtotime($m));
        $sql_m = "SELECT SUM(ordered_product_sales) as sales, SUM(total_order_items) as orders, SUM(units_ordered) as units, SUM(page_views_total) as page_views, AVG(unit_session_percentage) as conv, (SUM(ordered_product_sales_b2b) / NULLIF(SUM(ordered_product_sales), 0)) * 100 as b2b_share, AVG(refund_rate) as refund FROM amazon_business_report WHERE $where_customer AND report_date BETWEEN ? AND ?";
        $stmt_m = $conn->prepare($sql_m);
        if (!$stmt_m) throw new Exception("Prepare failed (Trend): " . $conn->error);
        $stmt_m->bind_param("ss", $m, $m_end);
        $stmt_m->execute();
        $res_m_obj = $stmt_m->get_result();
        $res_m = $res_m_obj ? $res_m_obj->fetch_assoc() : null;
        $trend_data[date('M Y', strtotime($m))] = $res_m ?: ['sales'=>0,'orders'=>0,'units'=>0,'page_views'=>0,'conv'=>0,'b2b_share'=>0,'refund'=>0];
    }

    // 5. Products
    // Build ASIN -> SKU mapping dynamically from all available dynamic tables
    $sku_mapping = [];
    $dyn_tables = [];
    $res_t = $conn->query("SHOW TABLES LIKE 'dyn_%'");
    while($t_row = $res_t->fetch_array()) {
        $t_name = $t_row[0];
        $res_c = $conn->query("DESCRIBE `$t_name` ");
        $cols = [];
        while($c_row = $res_c->fetch_assoc()) $cols[] = strtolower($c_row['Field']);
        if (in_array('asin', $cols) && in_array('sku', $cols)) $dyn_tables[] = $t_name;
    }

    if (!empty($dyn_tables)) {
        $unions = [];
        foreach($dyn_tables as $dt) $unions[] = "SELECT asin, sku FROM `$dt` WHERE asin IS NOT NULL AND sku IS NOT NULL";
        $map_res = $conn->query("SELECT DISTINCT asin, sku FROM (" . implode(" UNION ", $unions) . ") as m");
        if ($map_res) {
            while($m = $map_res->fetch_assoc()) $sku_mapping[$m['asin']] = $m['sku'];
        }
    }

    $sql_products = "SELECT 
        d.asin, 
        MAX(d.title) as name, 
        SUM(d.ordered_product_sales) as revenue, 
        SUM(d.units_ordered) as units,
        SUM(d.total_order_items) as total_orders,
        SUM(d.page_views_total) as page_views_total,
        SUM(d.page_views_mobile_app) as page_views_mobile,
        SUM(d.page_views_browser) as page_views_browser,
        SUM(d.sessions_total) as sessions_total,
        SUM(d.sessions_mobile_app) as sessions_mobile,
        SUM(d.sessions_browser) as sessions_browser,
        AVG(d.unit_session_percentage) as conv,
        AVG(d.buy_box_percentage) as buy_box,
        SUM(d.units_refunded) as refunds
        FROM amazon_detail_report d
        WHERE $where_customer AND d.report_date BETWEEN ? AND ?
        GROUP BY d.asin 
        ORDER BY revenue DESC 
        LIMIT 50";
    $from_bucket = date('Y-m-01', strtotime($from_date));
    $to_bucket = date('Y-m-01', strtotime($to_date));
    $stmt_p = $conn->prepare($sql_products);
    if (!$stmt_p) throw new Exception("Prepare failed (Products): " . $conn->error);
    $stmt_p->bind_param("ss", $from_bucket, $to_bucket);
    $stmt_p->execute();
    $res_p_obj = $stmt_p->get_result();
    $products_data = [];
    
    // Fee subquery helper
    $fee_sql = "SELECT SUM(selling_fees + fba_fees + other_transaction_fees + other + promotional_rebates) as total_fees 
                FROM amazon_transaction_report 
                WHERE $where_customer AND sku = ? AND date_time BETWEEN ? AND ?";
    $stmt_fee = $conn->prepare($fee_sql);

    // PPC subquery helper
    $ppc_sql = "SELECT SUM(spend) as spend, SUM(total_sales) as ad_sales 
                FROM amazon_advertising_sp 
                WHERE $where_customer AND advertised_sku = ? AND report_date BETWEEN ? AND ?";
    $stmt_ppc = $conn->prepare($ppc_sql);

    // Refund subquery helper (Accurate from Transaction Report)
    $refund_sql = "SELECT COUNT(*) as refunds FROM amazon_transaction_report 
                   WHERE $where_customer AND sku = ? AND type = 'Refund' AND date_time BETWEEN ? AND ?";
    $stmt_refund = $conn->prepare($refund_sql);

    if ($res_p_obj) {
        while ($row = $res_p_obj->fetch_assoc()) {
            $asin = $row['asin'];
            $sku = $sku_mapping[$asin] ?? $asin;
            $name = ($row['name'] ?? '') !== '' ? $row['name'] : 'N/A';
            
            // Fetch fees using SKU mapping
            $stmt_fee->bind_param("sss", $sku, $dt_start, $dt_end);
            $stmt_fee->execute();
            $fee_res = $stmt_fee->get_result()->fetch_assoc();
            $fees = floatval($fee_res['total_fees'] ?? 0);

            $rev = floatval($row['revenue']);
            $products_data[] = [
                'asin' => $asin,
                'sku' => $sku,
                'name' => truncateUtf8($name, 80),
                'revenue' => $rev,
                'units' => intval($row['units']),
                'total_orders' => intval($row['total_orders']),
                'page_views_total' => intval($row['page_views_total']),
                'page_views_mobile' => intval($row['page_views_mobile'] ?? 0),
                'page_views_browser' => intval($row['page_views_browser'] ?? 0),
                'sessions_total' => intval($row['sessions_total'] ?? 0),
                'sessions_mobile' => intval($row['sessions_mobile'] ?? 0),
                'sessions_browser' => intval($row['sessions_browser'] ?? 0),
                'conv' => floatval($row['conv'] ?? 0),
                'buy_box' => floatval($row['buy_box'] ?? 0),
                'refunds' => 0, // Placeholder, updated below
                'refund_rate' => 0,
                'fees' => $fees
            ];

            // Fetch accurate refunds for this product
            $stmt_refund->bind_param("sss", $sku, $from_date, $to_date);
            $stmt_refund->execute();
            $p_refund_res = $stmt_refund->get_result()->fetch_assoc();
            $p_refunds = intval($p_refund_res['refunds'] ?? 0);
            $products_data[count($products_data)-1]['refunds'] = $p_refunds;
            $products_data[count($products_data)-1]['refund_rate'] = intval($row['units']) > 0 ? ($p_refunds / intval($row['units'])) * 100 : 0;

            // PPC Data for this product
            $stmt_ppc->bind_param("sss", $sku, $from_date, $to_date);
            $stmt_ppc->execute();
            $ppc_res = $stmt_ppc->get_result()->fetch_assoc();
            $p_spend = floatval($ppc_res['spend'] ?? 0);
            $p_ad_sales = floatval($ppc_res['ad_sales'] ?? 0);
            
            $products_data[count($products_data)-1]['ad_spend'] = $p_spend;
            $products_data[count($products_data)-1]['ad_sales'] = $p_ad_sales;
            $products_data[count($products_data)-1]['acos'] = $p_ad_sales > 0 ? ($p_spend / $p_ad_sales) * 100 : 0;
            $products_data[count($products_data)-1]['ad_dep'] = $rev > 0 ? ($p_ad_sales / $rev) * 100 : 0;
        }
    }

    // 6. SKU-wise P&L (Dedicated from Transaction Report)
    $sql_sku_pl = "SELECT 
        sku,
        SUM(product_sales) as revenue,
        SUM(selling_fees) as selling_fees,
        SUM(fba_fees) as fba_fees,
        SUM(CASE WHEN type = 'Adjustment' THEN total ELSE 0 END) as adjustments,
        SUM(CASE WHEN description LIKE '%Customer Return%Fee%' THEN total ELSE 0 END) as return_fees,
        SUM(CASE WHEN type = 'Service Fee' THEN total ELSE 0 END) as service_fees,
        SUM(cogs) as total_cogs,
        SUM(quantity) as units
        FROM amazon_transaction_report 
        WHERE $where_customer 
          AND date_time BETWEEN ? AND ?
          AND type != 'Transfer'
          AND sku IS NOT NULL AND sku != ''
        GROUP BY sku 
        ORDER BY revenue DESC";
    $stmt_sku = $conn->prepare($sql_sku_pl);
    $stmt_sku->bind_param("ss", $dt_start, $dt_end);
    $stmt_sku->execute();
    $res_sku = $stmt_sku->get_result();
    $sku_pl_data = [];
    while($row = $res_sku->fetch_assoc()) {
        $rev = floatval($row['revenue']);
        $comp_fees = floatval($row['selling_fees']) + floatval($row['fba_fees']) + floatval($row['adjustments']) + floatval($row['return_fees']) + floatval($row['service_fees']);
        $cogs_val = floatval($row['total_cogs']);
        
        $gross = $rev + $comp_fees; // fees are negative
        $net = $gross - $cogs_val;
        $margin = $rev > 0 ? ($net / $rev) * 100 : 0;
        $roi = $cogs_val > 0 ? ($net / $cogs_val) * 100 : 0;

        $sku_pl_data[] = [
            'sku' => $row['sku'],
            'units' => intval($row['units']),
            'revenue' => $rev,
            'selling_fees' => floatval($row['selling_fees']),
            'fba_fees' => floatval($row['fba_fees']),
            'adjustments' => floatval($row['adjustments']),
            'return_fees' => floatval($row['return_fees']),
            'service_fees' => floatval($row['service_fees']),
            'fees' => $comp_fees,
            'gross' => $gross,
            'cogs' => $cogs_val,
            'net' => $net,
            'margin' => $margin,
            'roi' => $roi,
            'name' => $sku_mapping[$row['sku']] ?? $row['sku']
        ];
    }

    $payload = [
        'kpis' => [
            'total_sales' => format_currency($revenue),
            'total_units' => format_number($totals_full['total_units'] ?? 0),
            'total_sessions' => format_number($totals_full['total_sessions'] ?? 0),
            'avg_conversion' => format_percent($totals_full['avg_conversion'] ?? 0),
            'total_orders' => format_number($totals_full['total_orders'] ?? 0),
            'total_page_views' => format_number($totals_full['total_page_views'] ?? 0),
            'total_refunds' => format_number($totals_full['total_refunds'] ?? 0),
            'refund_rate' => format_percent($totals_full['avg_refund_rate'] ?? 0),
            'net_profit' => format_currency($net_profit),
            'dsr' => format_currency($dsr),
            'ad_sales' => format_currency($ad_sales),
            'organic_sales' => format_currency($organic_sales),
            'ad_spend' => format_currency($ad_spend),
            'acos' => format_percent($acos),
            'roas' => number_format($roas, 2),
            'tacos' => format_percent($tacos),
            'aov' => format_currency(($totals_full['total_orders'] ?? 0) > 0 ? $revenue / $totals_full['total_orders'] : 0),
            'avg_units_per_day' => number_format(($totals_full['total_units'] ?? 0) / max(1, $num_days), 1),
            'b2b_share' => format_percent($revenue > 0 ? (($totals_full['b2b_sales'] ?? 0) / $revenue) * 100 : 0),
            'net_margin' => format_percent($net_margin),
            'revenue_cmp' => $comparisons['sales']['pct'],
            'revenue_cmp_status' => $comparisons['sales']['dir'],
            'units_cmp' => $comparisons['units']['pct'],
            'units_cmp_status' => $comparisons['units']['dir'],
            'sessions_cmp' => $comparisons['sessions']['pct'],
            'sessions_cmp_status' => $comparisons['sessions']['dir'],
            'conv_cmp' => $comparisons['conv']['pct'],
            'conv_cmp_status' => $comparisons['conv']['dir'],
            'refunds_cmp' => $comparisons['refunds']['pct'],
            'refunds_cmp_status' => $comparisons['refunds']['dir']
        ],
        'financials' => $financials_payload,
        'charts' => $charts,
        'trends' => $trend_data,
        'products' => $products_data,
        'sku_pl' => $sku_pl_data,
        'monthly_products' => fetchMonthlyProducts($conn, $customer_id, $from_date, $to_date),
        'comparisons' => $comparisons
    ];

    $json = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    if ($json === false) {
        throw new Exception('JSON encode failed: ' . json_last_error_msg());
    }
    echo $json;
} catch (Throwable $t) {
    http_response_code(500);
    $json = json_encode(
        ['error' => $t->getMessage(), 'trace' => $t->getTraceAsString()],
        JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
    echo ($json === false) ? '{"error":"Unknown backend error"}' : $json;
}
function fetchMonthlyProducts($conn, $customer_id, $from, $to) {
    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";
    $sql = "SELECT 
                DATE_FORMAT(report_date, '%Y-%m') as month,
                asin,
                SUM(ordered_product_sales) as revenue,
                SUM(units_ordered) as units,
                SUM(sessions_total) as sessions,
                SUM(total_order_items) as orders,
                SUM(page_views_total) as page_views,
                AVG(unit_session_percentage) as conv
            FROM amazon_detail_report
            WHERE $where_customer AND report_date BETWEEN ? AND ?
            GROUP BY month, asin
            ORDER BY month DESC, revenue DESC";
    $from_bucket = date('Y-m-01', strtotime($from));
    $to_bucket = date('Y-m-01', strtotime($to));
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from_bucket, $to_bucket);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
