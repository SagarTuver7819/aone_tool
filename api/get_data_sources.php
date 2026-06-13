<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

function getContributedDataSummary($row, $conn) {
    $customerId = $row['customer_id'];
    $reportDate = $row['report_date'];
    $monthStart = date('Y-m-01', strtotime($reportDate));
    $monthEnd = date('Y-m-t', strtotime($reportDate));
    
    $type = $row['report_type'];
    
    if ($type === 'Business') {
        $sql = "SELECT SUM(ordered_product_sales) as sales, SUM(units_ordered) as units, SUM(total_order_items) as orders 
                FROM amazon_business_report 
                WHERE customer_id = ? AND report_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['orders'] > 0) {
            return "📈 <b>Sales:</b> $" . number_format($res['sales'] ?? 0, 2) . " | <b>Units:</b> " . number_format($res['units'] ?? 0) . " | <b>Orders:</b> " . number_format($res['orders'] ?? 0);
        }
    } 
    elseif ($type === 'Transaction') {
        $sql = "SELECT SUM(product_sales) as sales, SUM(fba_fees) as fba, SUM(selling_fees) as selling, SUM(total) as total 
                FROM amazon_transaction_report 
                WHERE customer_id = ? AND date_time BETWEEN ? AND ?";
        $monthStartDT = $monthStart . " 00:00:00";
        $monthEndDT = $monthEnd . " 23:59:59";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStartDT, $monthEndDT);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && ($res['sales'] != 0 || $res['fba'] != 0)) {
            return "💰 <b>Gross Sales:</b> $" . number_format($res['sales'] ?? 0, 2) . " | <b>FBA Fees:</b> $" . number_format($res['fba'] ?? 0, 2) . " | <b>Selling Fees:</b> $" . number_format($res['selling'] ?? 0, 2);
        }
    }
    elseif ($type === 'Detail') {
        $sql = "SELECT SUM(sessions_total) as sessions, SUM(page_views_total) as page_views 
                FROM amazon_detail_report 
                WHERE customer_id = ? AND report_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $customerId, $reportDate);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['sessions'] > 0) {
            return "👥 <b>Sessions:</b> " . number_format($res['sessions']) . " | <b>Page Views:</b> " . number_format($res['page_views']);
        }
    }
    elseif (strpos($type, 'Ads') === 0) {
        $tableName = 'amazon_advertising_sp';
        if (strpos($type, 'Ads SB') === 0) $tableName = 'amazon_advertising_sb';
        elseif (strpos($type, 'Ads SD') === 0) $tableName = 'amazon_advertising_sd';
        
        $sql = "SELECT SUM(impressions) as impr, SUM(clicks) as clicks, SUM(spend) as spend, SUM(total_sales) as sales 
                FROM `$tableName` 
                WHERE customer_id = ? AND report_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['clicks'] > 0) {
            return "📢 <b>Spend:</b> $" . number_format($res['spend'] ?? 0, 2) . " | <b>Ad Sales:</b> $" . number_format($res['sales'] ?? 0, 2) . " | <b>Clicks:</b> " . number_format($res['clicks']) . " | <b>Impr:</b> " . number_format($res['impr']);
        }
    }
    elseif ($type === 'Inventory') {
        $sql = "SELECT COUNT(DISTINCT sku) as total_skus, SUM(afn_fulfillable_quantity) as afn_qty, SUM(mfn_fulfillable_quantity) as mfn_qty 
                FROM amazon_inventory 
                WHERE customer_id = ? AND report_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $customerId, $reportDate);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['total_skus'] > 0) {
            return "📦 <b>SKUs Active:</b> " . number_format($res['total_skus']) . " | <b>Fulfillable Qty:</b> " . number_format(($res['afn_qty'] ?? 0) + ($res['mfn_qty'] ?? 0));
        }
    }
    
    return "<span style='color: #94a3b8; font-style: italic;'>Processed in database</span>";
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $session_customer_id = $_SESSION['customer_id'] ?? 0;
    $requested_customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
    
    if (($_SESSION['role'] ?? '') === 'customer') {
        $customer_id = $session_customer_id;
    } else {
        $customer_id = $requested_customer_id;
    }

    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';

    if (empty($from_date) || empty($to_date)) {
        echo json_encode([]);
        exit();
    }

    $where_customer = ($customer_id > 0) ? "f.customer_id = $customer_id" : "1=1";

    $sql = "SELECT f.*, c.customer_name, u.username 
            FROM file_upload_log f
            JOIN customers c ON f.customer_id = c.id
            LEFT JOIN users u ON f.uploaded_by = u.id
            WHERE $where_customer 
              AND f.report_date >= DATE_FORMAT(?, '%Y-%m-01') 
              AND f.report_date <= LAST_DAY(?)
            ORDER BY f.uploaded_at DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $sources = [];
    while ($row = $res->fetch_assoc()) {
        $sources[] = [
            'id' => $row['id'],
            'customer_name' => $row['customer_name'],
            'zip_filename' => $row['zip_filename'],
            'filename' => $row['filename'],
            'report_type' => $row['report_type'],
            'report_date' => date('M Y', strtotime($row['report_date'])),
            'rows_processed' => number_format($row['rows_processed']),
            'summary' => getContributedDataSummary($row, $conn),
            'uploaded_by' => $row['username'] ?? 'System',
            'uploaded_at' => date('d M Y H:i', strtotime($row['uploaded_at']))
        ];
    }
    
    echo json_encode($sources);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
