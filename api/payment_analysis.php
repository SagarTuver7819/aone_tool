<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : ($_SESSION['customer_id'] ?? 0);
    $from_date = $_GET['from_date'] ?? date('Y-m-01');
    $to_date = $_GET['to_date'] ?? date('Y-m-t');

    $where = "customer_id = $customer_id AND date_time BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'";

    // 1. Overall Summary by Type
    $sql_summary = "SELECT type, 
                    SUM(product_sales) as total_product_sales,
                    SUM(promotional_rebates) as total_rebates,
                    SUM(selling_fees) as total_selling_fees,
                    SUM(fba_fees) as total_fba_fees,
                    SUM(total) as net_total,
                    COUNT(*) as count
                    FROM amazon_transaction_report 
                    WHERE $where 
                    GROUP BY type";
    $res_summary = $conn->query($sql_summary);
    
    $summary = [];
    $totals = [
        'product_sales' => 0,
        'rebates' => 0,
        'selling_fees' => 0,
        'fba_fees' => 0,
        'net_total' => 0,
        'gross_net' => 0,
        'service_fees' => 0,
        'adjustments' => 0,
        'inventory_fees' => 0,
        'return_fees' => 0
    ];

    while ($row = $res_summary->fetch_assoc()) {
        $type = $row['type'];
        $net = floatval($row['net_total']);
        
        $summary[$type] = [
            'product_sales' => floatval($row['total_product_sales']),
            'rebates' => floatval($row['total_rebates']),
            'selling_fees' => floatval($row['total_selling_fees']),
            'fba_fees' => floatval($row['total_fba_fees']),
            'total' => $net,
            'count' => intval($row['count'])
        ];
        
        if ($type !== 'Transfer') {
            $totals['product_sales'] += floatval($row['total_product_sales']);
            $totals['rebates'] += floatval($row['total_rebates']);
            $totals['selling_fees'] += floatval($row['total_selling_fees']);
            $totals['fba_fees'] += floatval($row['total_fba_fees']);
            $totals['net_total'] += $net;

            if ($type === 'Service Fee') $totals['service_fees'] += $net;
            if ($type === 'Adjustment') $totals['adjustments'] += $net;
            if ($type === 'FBA Inventory Fee') $totals['inventory_fees'] += $net;
            // Return fees are handled in the SKU query or via description check, 
            // but for summary we'll stick to types for now or add desc check if needed.
        }
    }

    // Gross Net Calculation as per user formula (Sellerboard Standard): 
    // product sales + service + adjustment + inventory + return_fees
    // Note: total already includes all these if we sum it correctly.
    // However, the user wants to see the breakdown.
    
    $gross_profit = $totals['net_total']; // already excludes Transfers and includes everything else

    // 2. SKU Level Analysis (Including Refunds for accuracy)
    $sql_sku = "SELECT sku, description, 
                SUM(quantity) as total_qty,
                SUM(product_sales) as sales,
                SUM(promotional_rebates) as rebates,
                SUM(selling_fees) as s_fees,
                SUM(fba_fees) as f_fees,
                SUM(total) as net
                FROM amazon_transaction_report 
                WHERE $where AND type IN ('Order', 'Refund')
                GROUP BY sku 
                ORDER BY sales DESC";
    $res_sku = $conn->query($sql_sku);
    $sku_data = [];
    while ($row = $res_sku->fetch_assoc()) {
        $sku_data[] = [
            'sku' => $row['sku'],
            'description' => $row['description'],
            'qty' => intval($row['total_qty']),
            'sales' => floatval($row['sales']),
            'rebates' => floatval($row['rebates']),
            'selling_fees' => floatval($row['s_fees']),
            'fba_fees' => floatval($row['f_fees']),
            'gross_net' => floatval($row['net']), // Total settlement amount for this SKU
            'net' => floatval($row['net'])
        ];
    }

    // 3. Geographic Breakdown
    $sql_geo = "SELECT order_state, order_city, SUM(product_sales) as sales, COUNT(*) as orders
                FROM amazon_transaction_report 
                WHERE $where AND type = 'Order'
                GROUP BY order_state, order_city
                ORDER BY sales DESC LIMIT 50";
    $res_geo = $conn->query($sql_geo);
    $geo_data = [];
    while ($row = $res_geo->fetch_assoc()) {
        $geo_data[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'summary' => $summary,
        'totals' => $totals,
        'net_profit' => $gross_profit, // Gross Amazon profit for period
        'sku_analysis' => $sku_data,
        'geo_analysis' => $geo_data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
