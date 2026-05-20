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

    // 1. Fetch Product Configs (for shipping costs, etc.)
    $configs = [];
    $res = $conn->query("SELECT * FROM pro_product_config WHERE customer_id = $customer_id");
    while ($row = $res->fetch_assoc()) {
        $configs[$row['sku']] = $row;
    }

    // 2. Fetch COGS History
    $cogs_history = [];
    $res = $conn->query("SELECT * FROM pro_cogs_history WHERE customer_id = $customer_id ORDER BY start_date ASC");
    while ($row = $res->fetch_assoc()) {
        $cogs_history[$row['sku']][] = $row;
    }

    // 3. Fetch Expense Rules
    $rules = [];
    $res = $conn->query("SELECT * FROM pro_expense_rules WHERE customer_id = $customer_id AND is_active = 1");
    while ($row = $res->fetch_assoc()) {
        $rules[] = $row;
    }

    // 4. Fetch Transactions
    $where = "customer_id = $customer_id AND date_time BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'";
    $sql_trans = "SELECT * FROM amazon_transaction_report WHERE $where";
    $res_trans = $conn->query($sql_trans);

    $summary = [
        'sales' => 0,
        'amazon_fees' => 0,
        'cogs' => 0,
        'shipping' => 0,
        'other_fixed' => 0,
        'variable_rules' => 0,
        'net_profit' => 0,
        'roi' => 0,
        'margin' => 0
    ];

    $sku_breakdown = [];

    while ($row = $res_trans->fetch_assoc()) {
        $sku = $row['sku'];
        $type = $row['type'];
        $date = $row['date_time'];
        $qty = intval($row['quantity']);
        $total = floatval($row['total']);
        $product_sales = floatval($row['product_sales']);
        $amazon_fees = floatval($row['selling_fees']) + floatval($row['fba_fees']) + floatval($row['other_transaction_fees']);

        if (!isset($sku_breakdown[$sku])) {
            $sku_breakdown[$sku] = [
                'sku' => $sku,
                'sales' => 0,
                'qty' => 0,
                'amazon_fees' => 0,
                'cogs' => 0,
                'shipping' => 0,
                'other_fixed' => 0,
                'net' => 0
            ];
        }

        if ($type === 'Order') {
            $sku_breakdown[$sku]['sales'] += $product_sales;
            $sku_breakdown[$sku]['qty'] += $qty;
            $summary['sales'] += $product_sales;

            // Find COGS for this date
            $current_cogs = 0;
            if (isset($cogs_history[$sku])) {
                foreach ($cogs_history[$sku] as $h) {
                    if ($date >= $h['start_date'] && (empty($h['end_date']) || $date <= $h['end_date'])) {
                        $current_cogs = floatval($h['cogs']);
                        break;
                    }
                }
            }
            
            $cogs_total = $current_cogs * $qty;
            $sku_breakdown[$sku]['cogs'] += $cogs_total;
            $summary['cogs'] += $cogs_total;

            // Shipping & Fixed Costs
            if (isset($configs[$sku])) {
                $ship = floatval($configs[$sku]['shipping_cost_per_unit']) * $qty;
                $fixed = floatval($configs[$sku]['other_fixed_cost_per_unit']) * $qty;
                $sku_breakdown[$sku]['shipping'] += $ship;
                $sku_breakdown[$sku]['other_fixed'] += $fixed;
                $summary['shipping'] += $ship;
                $summary['other_fixed'] += $fixed;
            }
        }

        $sku_breakdown[$sku]['amazon_fees'] += $amazon_fees;
        $sku_breakdown[$sku]['net'] += $total; // This includes amazon fees and sales
        $summary['amazon_fees'] += $amazon_fees;
    }

    // Apply Expense Rules (Global or Per Unit)
    foreach ($rules as $rule) {
        $val = 0;
        if ($rule['rule_type'] === 'fixed_monthly') {
            $val = floatval($rule['value']);
        } elseif ($rule['rule_type'] === 'per_unit') {
            $total_units = array_sum(array_column($sku_breakdown, 'qty'));
            $val = $total_units * floatval($rule['value']);
        } elseif ($rule['rule_type'] === 'percent_of_sales') {
            $val = $summary['sales'] * (floatval($rule['value']) / 100);
        }
        $summary['variable_rules'] += $val;
    }

    // Final Calculations
    $summary['net_profit'] = $summary['sales'] + $summary['amazon_fees'] - $summary['cogs'] - $summary['shipping'] - $summary['other_fixed'] - $summary['variable_rules'];
    
    // Note: amazon_fees is usually negative in transaction report
    // net_profit = Revenue - AmazonFees(abs) - COGS - Shipping - Rules
    // But $summary['amazon_fees'] is already negative from the DB.
    
    $summary['roi'] = $summary['cogs'] > 0 ? ($summary['net_profit'] / $summary['cogs']) * 100 : 0;
    $summary['margin'] = $summary['sales'] > 0 ? ($summary['net_profit'] / $summary['sales']) * 100 : 0;

    echo json_encode([
        'status' => 'success',
        'summary' => $summary,
        'sku_breakdown' => array_values($sku_breakdown)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
