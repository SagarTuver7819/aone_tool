<?php
require_once __DIR__ . '/../config.php';

$r = $conn->query("SELECT type, COUNT(*) c FROM amazon_transaction_report GROUP BY type ORDER BY c DESC");
echo "Transaction types:\n";
while ($row = $r->fetch_assoc()) echo "  {$row['type']}: {$row['c']}\n";

$r = $conn->query("SELECT COUNT(*) c FROM amazon_transaction_report WHERE type='Refund' AND DATE(date_time) BETWEEN '2026-01-01' AND '2026-02-28'");
echo "\nRefunds Jan-Feb 2026: " . $r->fetch_assoc()['c'] . "\n";

$r = $conn->query("SELECT sku, COUNT(*) c, SUM(ABS(quantity)) q FROM amazon_transaction_report WHERE type='Refund' AND DATE(date_time) BETWEEN '2026-01-01' AND '2026-02-28' GROUP BY sku ORDER BY c DESC LIMIT 10");
echo "\nTop refund SKUs:\n";
while ($row = $r->fetch_assoc()) echo "  {$row['sku']}: {$row['c']} txns, {$row['q']} units\n";

$r = $conn->query("SELECT description, COUNT(*) c FROM amazon_transaction_report WHERE type='Refund' GROUP BY description ORDER BY c DESC LIMIT 5");
echo "\nRefund descriptions (sample):\n";
while ($row = $r->fetch_assoc()) echo "  [{$row['c']}] " . substr($row['description'], 0, 80) . "\n";

// Return fee lines
$r = $conn->query("SELECT type, description, COUNT(*) c FROM amazon_transaction_report WHERE description LIKE '%Return%' OR type LIKE '%Return%' GROUP BY type, description LIMIT 15");
echo "\nReturn-related lines:\n";
while ($row = $r->fetch_assoc()) echo "  {$row['type']} | {$row['description']} | {$row['c']}\n";

// Business report refunds
$r = $conn->query("SELECT SUM(units_refunded) u, AVG(refund_rate) r FROM amazon_business_report WHERE report_date BETWEEN '2026-01-01' AND '2026-02-28'");
$row = $r->fetch_assoc();
echo "\nBusiness report units_refunded: {$row['u']}, avg refund_rate: {$row['r']}\n";

$r = $conn->query("SELECT report_date, units_refunded FROM amazon_business_report WHERE units_refunded > 0 LIMIT 10");
echo "Daily refunds from business:\n";
while ($row = $r->fetch_assoc()) echo "  {$row['report_date']}: {$row['units_refunded']}\n";

// file upload log
$r = $conn->query("SELECT * FROM amazon_transaction_report WHERE description LIKE '%Customer Return%' LIMIT 3");
echo "\nCustomer Return adjustments:\n";
while ($row = $r->fetch_assoc()) {
    echo "  sku={$row['sku']} qty={$row['quantity']} type={$row['type']} order={$row['order_id']}\n";
}
