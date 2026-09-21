<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'auth']); exit; }
$cid = intval($_GET['customer_id'] ?? 1);
$out = [];
$queries = [
  'business_by_month' => "SELECT DATE_FORMAT(report_date,'%Y-%m') m, COUNT(*) c, ROUND(SUM(ordered_product_sales),2) s FROM amazon_business_report WHERE customer_id=$cid GROUP BY m ORDER BY m",
  'business_sample' => "SELECT report_date, ordered_product_sales, units_ordered FROM amazon_business_report WHERE customer_id=$cid ORDER BY report_date LIMIT 15",
  'txn_by_month' => "SELECT DATE_FORMAT(date_time,'%Y-%m') m, COUNT(*) c, ROUND(SUM(CASE WHEN type='Order' THEN product_sales ELSE 0 END),2) s FROM amazon_transaction_report WHERE customer_id=$cid GROUP BY m ORDER BY m",
  'ads_by_month' => "SELECT DATE_FORMAT(report_date,'%Y-%m') m, COUNT(*) c, ROUND(SUM(spend),2) spend, ROUND(SUM(total_sales),2) sales FROM amazon_advertising_sp WHERE customer_id=$cid GROUP BY m ORDER BY m",
  'detail_by_month' => "SELECT DATE_FORMAT(report_date,'%Y-%m') m, COUNT(*) c, ROUND(SUM(ordered_product_sales),2) s FROM amazon_detail_report WHERE customer_id=$cid GROUP BY m ORDER BY m",
];
foreach ($queries as $k=>$sql) {
  $r = $conn->query($sql);
  $rows = [];
  if ($r) while ($row=$r->fetch_assoc()) $rows[]=$row;
  else $rows = ['error'=>$conn->error];
  $out[$k]=$rows;
}
echo json_encode($out, JSON_PRETTY_PRINT);
