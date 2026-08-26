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

function pct_change($current, $previous)
{
    $current = floatval($current);
    $previous = floatval($previous);
    if ($previous == 0) {
        return $current > 0 ? ['pct' => 100, 'dir' => 'up'] : ['pct' => 0, 'dir' => 'none'];
    }
    $pct = (($current - $previous) / abs($previous)) * 100;
    return [
        'pct' => round(abs($pct), 1),
        'dir' => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'none'),
    ];
}

function is_sellable_status($status)
{
    $s = strtolower(trim((string) $status));
    return $s !== '' && (strpos($s, 'sellable') !== false || strpos($s, 'inventory') !== false);
}

function is_damaged_status($status, $reason = '')
{
    $s = strtolower(trim((string) $status));
    $r = strtolower(trim((string) $reason));
    if ($s !== '' && strpos($s, 'damaged') !== false)
        return true;
    return strpos($r, 'damaged') !== false;
}

function is_defect_reason($reason)
{
    $r = strtolower(trim((string) $reason));
    return $r !== '' && (strpos($r, 'defect') !== false || strpos($r, 'defective') !== false);
}

function infer_reason_from_text($text)
{
    $d = strtolower(trim((string) $text));
    if ($d === '')
        return 'CUSTOMER_REFUND';
    if (preg_match('/defect|defective|broken|malfunction|quality issue/', $d))
        return 'DEFECTIVE';
    if (preg_match('/damaged.{0,20}transit|shipping damage|carrier damage|damaged in/', $d))
        return 'DAMAGED_TRANSIT';
    if (preg_match('/wrong size|wrong item|unwanted|no longer|changed mind|not as described|ordered by mistake/', $d))
        return 'UNWANTED_ITEM';
    if (preg_match('/damaged/', $d))
        return 'DAMAGED_TRANSIT';
    return 'CUSTOMER_REFUND';
}

function fetch_damaged_keys(mysqli $conn, $where_customer, $from_date, $to_date)
{
    $sql = "SELECT order_id, sku
            FROM amazon_transaction_report
            WHERE $where_customer
            AND type = 'Adjustment'
            AND description LIKE '%Customer Return%'
            AND DATE(date_time) BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $from_date, $to_date);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $keys = [];
    foreach ($rows as $row) {
        $order = trim((string) ($row['order_id'] ?? ''));
        $sku = trim((string) ($row['sku'] ?? ''));
        if ($order !== '' && $sku !== '') {
            $keys[$order . '|' . $sku] = true;
        }
    }
    return $keys;
}

function fetch_returns_reimbursement_rows(mysqli $conn, $where_customer, $from_date, $to_date)
{
    $sql = "SELECT quantity, reason, status, sku, asin, report_date, '' AS product_name, '' AS order_id
            FROM amazon_returns_reimbursements
            WHERE $where_customer AND type = 'Return'
            AND report_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $from_date, $to_date);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetch_transaction_refund_rows(mysqli $conn, $where_customer, $from_date, $to_date)
{
    $damaged_keys = fetch_damaged_keys($conn, $where_customer, $from_date, $to_date);

    $sql = "SELECT quantity, sku, description, date_time, order_id
            FROM amazon_transaction_report
            WHERE $where_customer AND type = 'Refund'
            AND DATE(date_time) BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $from_date, $to_date);
    $stmt->execute();
    $raw = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $rows = [];
    foreach ($raw as $row) {
        $sku = trim((string) ($row['sku'] ?? ''));
        $order = trim((string) ($row['order_id'] ?? ''));
        $description = trim((string) ($row['description'] ?? ''));
        $key = $order . '|' . $sku;
        $is_damaged = isset($damaged_keys[$key]);

        $rows[] = [
            'quantity' => max(1, abs(intval($row['quantity'] ?? 1))),
            'reason' => infer_reason_from_text($description),
            'status' => $is_damaged ? 'Damaged' : 'Sellable',
            'sku' => $sku,
            'asin' => '',
            'report_date' => date('Y-m-d', strtotime($row['date_time'])),
            'product_name' => $description,
            'order_id' => $order,
        ];
    }
    return $rows;
}

function fetch_return_rows(mysqli $conn, $where_customer, $from_date, $to_date)
{
    $reimb_rows = fetch_returns_reimbursement_rows($conn, $where_customer, $from_date, $to_date);
    if (!empty($reimb_rows)) {
        return ['source' => 'returns_report', 'rows' => $reimb_rows];
    }
    return ['source' => 'transaction_refunds', 'rows' => fetch_transaction_refund_rows($conn, $where_customer, $from_date, $to_date)];
}

function aggregate_stats(array $rows)
{
    $total_units = 0;
    $sellable_units = 0;
    $damaged_units = 0;
    $defect_units = 0;
    $reason_counts = [];
    $sku_counts = [];
    $sku_names = [];
    $daily = [];
    $monthly = [];

    foreach ($rows as $row) {
        $qty = max(1, intval($row['quantity'] ?? 1));
        $total_units += $qty;

        if (is_sellable_status($row['status'] ?? '')) {
            $sellable_units += $qty;
        }
        if (is_damaged_status($row['status'] ?? '', $row['reason'] ?? '')) {
            $damaged_units += $qty;
        }
        if (is_defect_reason($row['reason'] ?? '')) {
            $defect_units += $qty;
        }

        $reason = trim((string) ($row['reason'] ?? 'Unknown'));
        if ($reason === '')
            $reason = 'Unknown';
        $reason_counts[$reason] = ($reason_counts[$reason] ?? 0) + $qty;

        $sku = trim((string) ($row['sku'] ?? ''));
        if ($sku !== '') {
            $sku_counts[$sku] = ($sku_counts[$sku] ?? 0) + $qty;
            if (!empty($row['product_name'])) {
                $sku_names[$sku] = $row['product_name'];
            }
        }

        $date = $row['report_date'] ?? '';
        if ($date !== '') {
            $daily[$date] = $daily[$date] ?? ['sellable' => 0, 'damaged' => 0, 'total' => 0];
            $daily[$date]['total'] += $qty;
            if (is_sellable_status($row['status'] ?? '')) {
                $daily[$date]['sellable'] += $qty;
            }
            if (is_damaged_status($row['status'] ?? '', $row['reason'] ?? '')) {
                $daily[$date]['damaged'] += $qty;
            }

            $month_key = date('Y-m', strtotime($date));
            $monthly[$month_key] = $monthly[$month_key] ?? ['sellable' => 0, 'damaged' => 0, 'total' => 0];
            $monthly[$month_key]['total'] += $qty;
            if (is_sellable_status($row['status'] ?? '')) {
                $monthly[$month_key]['sellable'] += $qty;
            }
            if (is_damaged_status($row['status'] ?? '', $row['reason'] ?? '')) {
                $monthly[$month_key]['damaged'] += $qty;
            }
        }
    }

    arsort($reason_counts);
    arsort($sku_counts);

    $top_reason = '—';
    $top_reason_pct = 0;
    if (!empty($reason_counts)) {
        $top_reason = array_key_first($reason_counts);
        $top_reason_pct = $total_units > 0 ? round((reset($reason_counts) / $total_units) * 100) : 0;
    }

    $top_sku = '—';
    $top_sku_name = '—';
    if (!empty($sku_counts)) {
        $top_sku = array_key_first($sku_counts);
        $top_sku_name = $sku_names[$top_sku] ?? $top_sku;
        if (strlen($top_sku_name) > 40) {
            $top_sku_name = substr($top_sku_name, 0, 37) . '...';
        }
    }

    return [
        'return_units' => $total_units,
        'sellable_pct' => $total_units > 0 ? round(($sellable_units / $total_units) * 100, 1) : 0,
        'damaged_pct' => $total_units > 0 ? round(($damaged_units / $total_units) * 100, 1) : 0,
        'defect_rate' => $total_units > 0 ? round(($defect_units / $total_units) * 100, 2) : 0,
        'top_reason' => $top_reason,
        'top_reason_pct' => $top_reason_pct,
        'top_sku' => $top_sku,
        'top_sku_name' => $top_sku_name,
        'reason_counts' => $reason_counts,
        'daily' => $daily,
        'monthly' => $monthly,
    ];
}

function build_product_rows(mysqli $conn, $where_customer, array $rows)
{
    $products = [];
    foreach ($rows as $row) {
        $sku = trim((string) ($row['sku'] ?? ''));
        if ($sku === '')
            continue;
        $qty = max(1, intval($row['quantity'] ?? 1));

        if (!isset($products[$sku])) {
            $products[$sku] = [
                'sku' => $sku,
                'asin' => trim((string) ($row['asin'] ?? '')),
                'product_name' => trim((string) ($row['product_name'] ?? '')),
                'return_count' => 0,
                'sellable' => 0,
                'reasons' => [],
            ];
        }
        if (empty($products[$sku]['product_name']) && !empty($row['product_name'])) {
            $products[$sku]['product_name'] = trim((string) $row['product_name']);
        }
        if (empty($products[$sku]['asin']) && !empty($row['asin'])) {
            $products[$sku]['asin'] = trim((string) $row['asin']);
        }

        $products[$sku]['return_count'] += $qty;
        if (is_sellable_status($row['status'] ?? '')) {
            $products[$sku]['sellable'] += $qty;
        }
        $reason = trim((string) ($row['reason'] ?? 'Unknown'));
        if ($reason === '')
            $reason = 'Unknown';
        $products[$sku]['reasons'][$reason] = ($products[$sku]['reasons'][$reason] ?? 0) + $qty;
    }

    if (empty($products)) {
        return [];
    }

    $asins = array_values(array_filter(array_unique(array_map(function ($p) {
        return $p['asin'] ?? '';
    }, $products))));

    $names = [];
    if (!empty($asins)) {
        $placeholders = implode(',', array_fill(0, count($asins), '?'));
        $name_sql = "SELECT asin, title FROM amazon_detail_report
                     WHERE $where_customer AND asin IN ($placeholders)
                     GROUP BY asin";
        $name_stmt = $conn->prepare($name_sql);
        if ($name_stmt) {
            $types = str_repeat('s', count($asins));
            $name_stmt->bind_param($types, ...$asins);
            $name_stmt->execute();
            foreach ($name_stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $nr) {
                $names[$nr['asin']] = $nr['title'];
            }
        }
    }

    $result = [];
    foreach ($products as $sku => $p) {
        arsort($p['reasons']);
        $top_reason = array_key_first($p['reasons']);
        $sellable_ratio = $p['return_count'] > 0 ? round(($p['sellable'] / $p['return_count']) * 100) : 0;

        if ($sellable_ratio >= 85) {
            $status = 'OPTIMAL';
        } elseif ($sellable_ratio >= 70) {
            $status = 'WATCH';
        } else {
            $status = 'CRITICAL';
        }

        $product_name = $p['product_name'];
        if (empty($product_name)) {
            $product_name = $names[$p['asin']] ?? $sku;
        }
        if (strlen($product_name) > 80) {
            $product_name = substr($product_name, 0, 77) . '...';
        }

        $result[] = [
            'sku' => $sku,
            'product_name' => $product_name,
            'return_count' => $p['return_count'],
            'top_reason' => $top_reason,
            'sellable_ratio' => $sellable_ratio,
            'status' => $status,
        ];
    }

    usort($result, function ($a, $b) {
        return $b['return_count'] <=> $a['return_count'];
    });

    return $result;
}

try {
    $session_customer_id = $_SESSION['customer_id'] ?? 0;
    $requested_customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

    if (($_SESSION['role'] ?? '') === 'customer') {
        $customer_id = $session_customer_id;
    } else {
        $customer_id = $requested_customer_id;
    }

    $from_date = $_GET['from_date'] ?? date('Y-m-01');
    $to_date = $_GET['to_date'] ?? date('Y-m-d');

    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : '1=1';

    $days = max(1, (int) ((strtotime($to_date) - strtotime($from_date)) / 86400) + 1);
    $prev_to = date('Y-m-d', strtotime($from_date . ' -1 day'));
    $prev_from = date('Y-m-d', strtotime($prev_to . ' -' . ($days - 1) . ' days'));

    $current_pack = fetch_return_rows($conn, $where_customer, $from_date, $to_date);
    $previous_pack = fetch_return_rows($conn, $where_customer, $prev_from, $prev_to);

    $current = aggregate_stats($current_pack['rows']);
    $previous = aggregate_stats($previous_pack['rows']);

    $reason_chart = [];
    $palette = ['#60a5fa', '#a78bfa', '#f472b6', '#34d399', '#fbbf24', '#94a3b8'];
    $i = 0;
    foreach ($current['reason_counts'] as $reason => $count) {
        $pct = $current['return_units'] > 0 ? round(($count / $current['return_units']) * 100) : 0;
        $reason_chart[] = [
            'label' => strtoupper(str_replace(' ', '_', $reason)),
            'count' => $count,
            'pct' => $pct,
            'color' => $palette[$i % count($palette)],
        ];
        $i++;
        if ($i >= 6)
            break;
    }

    ksort($current['daily']);
    $daily_labels = [];
    $daily_sellable = [];
    $daily_damaged = [];
    foreach ($current['daily'] as $date => $vals) {
        $daily_labels[] = date('M d', strtotime($date));
        $daily_sellable[] = $vals['sellable'];
        $daily_damaged[] = $vals['damaged'];
    }

    ksort($current['monthly']);
    $monthly_labels = [];
    $monthly_sellable = [];
    $monthly_damaged = [];
    foreach ($current['monthly'] as $month => $vals) {
        $monthly_labels[] = date('M Y', strtotime($month . '-01'));
        $monthly_sellable[] = $vals['sellable'];
        $monthly_damaged[] = $vals['damaged'];
    }

    $products = build_product_rows($conn, $where_customer, $current_pack['rows']);

    echo json_encode([
        'kpis' => [
            'total_returns' => $current['return_units'],
            'sellable_pct' => $current['sellable_pct'],
            'damaged_pct' => $current['damaged_pct'],
            'top_reason' => $current['top_reason'],
            'top_reason_pct' => $current['top_reason_pct'],
            'top_sku' => $current['top_sku'],
            'top_sku_name' => $current['top_sku_name'],
            'defect_rate' => $current['defect_rate'],
            'comparison' => [
                'total_returns' => pct_change($current['return_units'], $previous['return_units']),
                'sellable_pct' => pct_change($current['sellable_pct'], $previous['sellable_pct']),
                'damaged_pct' => pct_change($current['damaged_pct'], $previous['damaged_pct']),
                'defect_rate' => pct_change($current['defect_rate'], $previous['defect_rate']),
            ],
        ],
        'reasons' => $reason_chart,
        'trend' => [
            'daily' => [
                'labels' => $daily_labels,
                'sellable' => $daily_sellable,
                'damaged' => $daily_damaged,
            ],
            'monthly' => [
                'labels' => $monthly_labels,
                'sellable' => $monthly_sellable,
                'damaged' => $monthly_damaged,
            ],
        ],
        'products' => $products,
        'meta' => [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'has_data' => $current['return_units'] > 0,
            'data_source' => $current_pack['source'],
        ],
    ]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => $t->getMessage()]);
}
