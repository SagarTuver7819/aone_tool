<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

function normalizeDate($date) {
    if (empty($date)) return date('Y-m-d');
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
        $parts = explode('/', $date);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return $date;
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
    $selected_sku = $_GET['sku'] ?? '';
    $selected_months = isset($_GET['months']) ? explode(',', $_GET['months']) : [];

    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";
    $where_sku = "1=1";
    if (!empty($selected_sku)) $where_sku = "sku = '" . $conn->real_escape_string($selected_sku) . "'";

    $where_month = "1=1";
    if (!empty($selected_months)) {
        $clean_months = array_map(function($m) use ($conn) { return "'" . $conn->real_escape_string(trim($m)) . "'"; }, $selected_months);
        $where_month = "DATE_FORMAT(date_time, '%b') IN (" . implode(',', $clean_months) . ")";
    }

    $where_clause = "$where_customer AND $where_sku AND $where_month";

    // 1. Fee Breakdown (Granular)
    $sql_fees = "SELECT 'Selling Fees' as label, SUM(ABS(selling_fees)) as total_amount 
                 FROM amazon_transaction_report 
                 WHERE $where_clause AND date_time BETWEEN ? AND ? 
                 AND selling_fees != 0 AND type != 'Transfer'
                 UNION ALL
                 SELECT 'FBA Fees' as label, SUM(ABS(fba_fees)) as total_amount 
                 FROM amazon_transaction_report 
                 WHERE $where_clause AND date_time BETWEEN ? AND ? 
                 AND fba_fees != 0 AND type != 'Transfer'
                 UNION ALL
                 SELECT 'Promotional Rebates' as label, SUM(ABS(promotional_rebates)) as total_amount 
                 FROM amazon_transaction_report 
                 WHERE $where_clause AND date_time BETWEEN ? AND ? 
                 AND promotional_rebates != 0 AND type != 'Transfer'
                 UNION ALL
                 SELECT 'Adjustments' as label, SUM(ABS(total)) as total_amount 
                 FROM amazon_transaction_report 
                 WHERE $where_clause AND date_time BETWEEN ? AND ? 
                 AND type = 'Adjustment'
                 UNION ALL
                 SELECT 'Service Fees' as label, SUM(ABS(total)) as total_amount 
                 FROM amazon_transaction_report 
                 WHERE $where_clause AND date_time BETWEEN ? AND ? 
                 AND type = 'Service Fee'
                 UNION ALL
                 SELECT 'Inventory Fees' as label, SUM(ABS(total)) as total_amount 
                 FROM amazon_transaction_report 
                 WHERE $where_clause AND date_time BETWEEN ? AND ? 
                 AND type = 'FBA Inventory Fee'
                 UNION ALL
                 SELECT 'Other Fees' as label, SUM(ABS(other_transaction_fees + other)) as total_amount 
                 FROM amazon_transaction_report 
                 WHERE $where_clause AND date_time BETWEEN ? AND ? 
                 AND (other_transaction_fees + other) != 0 AND type NOT IN ('Adjustment', 'Service Fee', 'FBA Inventory Fee', 'Transfer')";
    
    $stmt = $conn->prepare($sql_fees);
    $dt_start = $from_date . " 00:00:00";
    $dt_end = $to_date . " 23:59:59";
    // 7 pairs of dates
    $stmt->bind_param("ssssssssssssss", 
        $dt_start, $dt_end, $dt_start, $dt_end, $dt_start, $dt_end, 
        $dt_start, $dt_end, $dt_start, $dt_end, $dt_start, $dt_end,
        $dt_start, $dt_end
    );
    $stmt->execute();
    $res_fees = $stmt->get_result();
    
    $fee_labels = [];
    $fee_values = [];
    while ($row = $res_fees->fetch_assoc()) {
        if ($row['total_amount'] > 0) {
            $fee_labels[] = $row['label'];
            $fee_values[] = floatval($row['total_amount']);
        }
    }
 
    // Fetch SKU info and stocks from amazon_inventory (latest report date)
    $sku_info = [];
    $inv_sql = "SELECT sku, asin, product_name, afn_fulfillable_quantity, your_price 
                FROM amazon_inventory 
                WHERE (sku, report_date) IN (SELECT sku, MAX(report_date) FROM amazon_inventory GROUP BY sku)";
    $inv_res = $conn->query($inv_sql);
    if ($inv_res) {
        while ($row = $inv_res->fetch_assoc()) {
            $sku_info[$row['sku']] = [
                'asin' => $row['asin'] ?: 'N/A',
                'name' => $row['product_name'] ?: $row['sku'],
                'stock' => intval($row['afn_fulfillable_quantity']),
                'price' => floatval($row['your_price'])
            ];
        }
    }

    // 2. Sales by Province (Using order_state/order_city)
    $sql_province = "SELECT order_state, 
                            SUM(product_sales) as total_sales, 
                            COUNT(DISTINCT order_id) as order_count,
                            SUM(quantity) as units_sold,
                            SUM(selling_fees) as selling_fees,
                            SUM(fba_fees) as fba_fees,
                            SUM(CASE WHEN type = 'Adjustment' THEN total ELSE 0 END) as adjustments,
                            SUM(CASE WHEN description LIKE '%Customer Return%Fee%' THEN total ELSE 0 END) as return_fees,
                            SUM(CASE WHEN type = 'Service Fee' THEN total ELSE 0 END) as service_fees,
                            SUM(cogs) as total_cogs,
                            SUM(CASE WHEN type = 'Refund' THEN total ELSE 0 END) as refunds
                     FROM amazon_transaction_report 
                     WHERE $where_clause AND date_time BETWEEN ? AND ? 
                       AND order_state IS NOT NULL AND order_state != ''
                     GROUP BY order_state 
                     HAVING total_sales > 0
                     ORDER BY total_sales DESC";
    
    $stmt_prov = $conn->prepare($sql_province);
    $stmt_prov->bind_param("ss", $dt_start, $dt_end);
    $stmt_prov->execute();
    $res_prov = $stmt_prov->get_result();
    
    $provinces = [];
    $province_map = [];
    while ($row = $res_prov->fetch_assoc()) {
        $state = $row['order_state'];
        $rev = floatval($row['total_sales']);
        $fees = floatval($row['selling_fees']) + floatval($row['fba_fees']) + floatval($row['adjustments']) + floatval($row['return_fees']) + floatval($row['service_fees']);
        $cogs_val = floatval($row['total_cogs']);
        $refund_cost = floatval($row['refunds']);
        $gross_profit = $rev + $fees - $cogs_val + $refund_cost;

        $provinces[$state] = [
            'province' => $state,
            'total_sales' => $rev,
            'order_count' => intval($row['order_count']),
            'units_sold' => intval($row['units_sold']),
            'fees' => $fees,
            'cogs' => $cogs_val,
            'refunds' => $refund_cost,
            'gross_profit' => $gross_profit,
            'skus' => []
        ];
        $province_map[] = $state;
    }

    if (!empty($province_map)) {
        $sql_prov_sku = "SELECT 
                            order_state,
                            sku,
                            SUM(product_sales) as total_sales, 
                            COUNT(DISTINCT order_id) as order_count,
                            SUM(quantity) as units_sold,
                            SUM(selling_fees) as selling_fees,
                            SUM(fba_fees) as fba_fees,
                            SUM(CASE WHEN type = 'Adjustment' THEN total ELSE 0 END) as adjustments,
                            SUM(CASE WHEN description LIKE '%Customer Return%Fee%' THEN total ELSE 0 END) as return_fees,
                            SUM(CASE WHEN type = 'Service Fee' THEN total ELSE 0 END) as service_fees,
                            SUM(cogs) as total_cogs,
                            SUM(CASE WHEN type = 'Refund' THEN total ELSE 0 END) as refunds
                         FROM amazon_transaction_report 
                         WHERE $where_clause AND date_time BETWEEN ? AND ? 
                           AND order_state IS NOT NULL AND order_state != ''
                           AND sku IS NOT NULL AND sku != ''
                         GROUP BY order_state, sku
                         ORDER BY order_state ASC, total_sales DESC";
        $stmt_prov_sku = $conn->prepare($sql_prov_sku);
        $stmt_prov_sku->bind_param("ss", $dt_start, $dt_end);
        $stmt_prov_sku->execute();
        $res_prov_sku = $stmt_prov_sku->get_result();

        while ($row = $res_prov_sku->fetch_assoc()) {
            $state = $row['order_state'];
            if (!isset($provinces[$state])) continue;

            $sku = $row['sku'];
            $sku_rev = floatval($row['total_sales']);
            $sku_fees = floatval($row['selling_fees']) + floatval($row['fba_fees']) + floatval($row['adjustments']) + floatval($row['return_fees']) + floatval($row['service_fees']);
            $sku_cogs = floatval($row['total_cogs']);
            $sku_refunds = floatval($row['refunds']);
            $sku_gross = $sku_rev + $sku_fees - $sku_cogs + $sku_refunds;

            // Info from amazon_inventory
            $info = $sku_info[$sku] ?? [
                'asin' => 'N/A',
                'name' => $sku,
                'stock' => 0,
                'price' => 0
            ];

            $provinces[$state]['skus'][] = [
                'sku' => $sku,
                'asin' => $info['asin'],
                'product_name' => $info['name'],
                'stock' => $info['stock'],
                'price' => $info['price'] ?: ($row['units_sold'] > 0 ? $sku_rev / $row['units_sold'] : 0),
                'order_count' => intval($row['order_count']),
                'units_sold' => intval($row['units_sold']),
                'sales' => $sku_rev,
                'fees' => $sku_fees,
                'cogs' => $sku_cogs,
                'refunds' => $sku_refunds,
                'gross_profit' => $sku_gross
            ];
        }
    }

    $provinces = array_values($provinces);
 
    // 3. Transaction Type Summary
    $sql_txn = "SELECT type, SUM(total) as total_amount, COUNT(*) as txn_count 
                FROM amazon_transaction_report 
                WHERE $where_clause AND date_time BETWEEN ? AND ? 
                GROUP BY type 
                ORDER BY total_amount DESC";
    
    $stmt_txn = $conn->prepare($sql_txn);
    $stmt_txn->bind_param("ss", $dt_start, $dt_end);
    $stmt_txn->execute();
    $res_txn = $stmt_txn->get_result();
    
    $txn_summary = [];
    while ($row = $res_txn->fetch_assoc()) {
        $type = $row['type'] ?: 'Unknown';
        $txn_summary[$type] = [
            'total_amount' => floatval($row['total_amount']),
            'total_count' => intval($row['txn_count']),
            'details' => [[
                'type' => $type,
                'amount_type' => 'Total',
                'description' => 'Summary',
                'amount' => floatval($row['total_amount']),
                'count' => intval($row['txn_count'])
            ]]
        ];
    }

    // Financial Insights
    $insights = [];
    $total_revenue = 0;
    $sql_rev = "SELECT SUM(product_sales) FROM amazon_transaction_report WHERE $where_clause AND date_time BETWEEN ? AND ?";
    $stmt_rev = $conn->prepare($sql_rev);
    $stmt_rev->bind_param("ss", $dt_start, $dt_end);
    $stmt_rev->execute();
    $stmt_rev->bind_result($total_revenue);
    $stmt_rev->fetch();
    $stmt_rev->close();

    if ($total_revenue > 0) {
        $total_fba = 0;
        foreach ($fee_labels as $i => $label) {
            if (stripos($label, 'FBA') !== false || stripos($label, 'FBAPerUnit') !== false) {
                $total_fba += $fee_values[$i];
            }
        }
        $fba_ratio = ($total_fba / $total_revenue) * 100;
        if ($fba_ratio > 20) {
            $insights[] = [
                'title' => 'High FBA Fees',
                'text' => "FBA fees are " . number_format($fba_ratio, 1) . "% of revenue ($" . number_format($total_fba, 2) . "). Consider product packaging optimization.",
                'type' => 'warning',
                'icon' => 'local_shipping'
            ];
        }
    }

    $payload = [
        'has_data' => count($txn_summary) > 0,
        'fee_breakdown' => [
            'labels' => $fee_labels,
            'values' => $fee_values
        ],
        'province_breakdown' => $provinces,
        'transaction_summary' => $txn_summary,
        'insights' => $insights
    ];

    echo json_encode($payload);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => $t->getMessage()]);
}
?>
