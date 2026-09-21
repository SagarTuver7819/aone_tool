<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

$cid = isset($argv[1]) ? intval($argv[1]) : 1;
echo "DB=" . DB_NAME . " customer_id=$cid\n\n";

function run($conn, $sql, $label) {
    echo "==== $label ====\n";
    $r = $conn->query($sql);
    if (!$r) {
        echo "ERR: " . $conn->error . "\n\n";
        return;
    }
    $n = 0;
    while ($row = $r->fetch_assoc()) {
        echo json_encode($row) . "\n";
        $n++;
    }
    if ($n === 0) echo "(empty)\n";
    echo "\n";
}

$where = $cid > 0 ? "customer_id = $cid" : "1=1";

run($conn, "SELECT DATE_FORMAT(report_date,'%Y-%m') m, COUNT(*) rows_c, ROUND(SUM(ordered_product_sales),2) sales, SUM(units_ordered) units FROM amazon_business_report WHERE $where AND report_date BETWEEN '2026-06-01' AND '2026-08-31' GROUP BY m ORDER BY m", 'business_report Jun-Aug');

run($conn, "SELECT DATE_FORMAT(date_time,'%Y-%m') m, COUNT(*) rows_c, ROUND(SUM(CASE WHEN type='Order' THEN product_sales ELSE 0 END),2) order_sales, SUM(CASE WHEN type='Order' THEN quantity ELSE 0 END) units FROM amazon_transaction_report WHERE $where AND date_time BETWEEN '2026-06-01 00:00:00' AND '2026-08-31 23:59:59' GROUP BY m ORDER BY m", 'transaction_report Jun-Aug');

run($conn, "SELECT DATE_FORMAT(report_date,'%Y-%m') m, COUNT(*) rows_c, ROUND(SUM(spend),2) spend, ROUND(SUM(total_sales),2) ad_sales FROM amazon_advertising_sp WHERE $where AND report_date BETWEEN '2026-06-01' AND '2026-08-31' GROUP BY m ORDER BY m", 'advertising_sp Jun-Aug');

run($conn, "SELECT DATE_FORMAT(report_date,'%Y-%m') m, COUNT(*) rows_c, ROUND(SUM(ordered_product_sales),2) sales FROM amazon_detail_report WHERE $where AND report_date BETWEEN '2026-06-01' AND '2026-08-31' GROUP BY m ORDER BY m", 'detail_report Jun-Aug');

run($conn, "SELECT DATE_FORMAT(report_date,'%Y-%m') m, COUNT(*) rows_c FROM amazon_brand_reports WHERE $where AND report_date BETWEEN '2026-06-01' AND '2026-08-31' GROUP BY m ORDER BY m", 'brand_reports Jun-Aug');

run($conn, "SELECT DATE_FORMAT(report_date,'%Y-%m') m, type, COUNT(*) rows_c FROM amazon_returns_reimbursements WHERE $where AND report_date BETWEEN '2026-06-01' AND '2026-08-31' GROUP BY m, type ORDER BY m, type", 'returns_reimb Jun-Aug');

run($conn, "SELECT MIN(report_date) mn, MAX(report_date) mx, COUNT(*) c FROM amazon_business_report WHERE $where", 'business overall');
run($conn, "SELECT MIN(date_time) mn, MAX(date_time) mx, COUNT(*) c FROM amazon_transaction_report WHERE $where", 'transactions overall');
run($conn, "SELECT MIN(report_date) mn, MAX(report_date) mx, COUNT(*) c FROM amazon_advertising_sp WHERE $where", 'ads overall');
run($conn, "SELECT MIN(report_date) mn, MAX(report_date) mx, COUNT(*) c FROM amazon_detail_report WHERE $where", 'detail overall');
