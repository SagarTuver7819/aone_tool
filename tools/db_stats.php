<?php
require_once __DIR__ . '/../config.php';

function q(mysqli $conn, string $sql, array $params = [], string $types = ''): array {
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($res && ($row = $res->fetch_assoc())) $rows[] = $row;
    return $rows;
}

echo "DB: " . DB_NAME . PHP_EOL;

$rows = q($conn, "SELECT COUNT(*) as c, MIN(report_date) as min_d, MAX(report_date) as max_d FROM amazon_business_report");
echo "amazon_business_report: rows=" . ($rows[0]['c'] ?? 0) . " range=" . ($rows[0]['min_d'] ?? '-') . " to " . ($rows[0]['max_d'] ?? '-') . PHP_EOL;

$rows = q($conn, "SELECT customer_id, COUNT(*) as c, MIN(report_date) as min_d, MAX(report_date) as max_d
                  FROM amazon_business_report GROUP BY customer_id ORDER BY customer_id");
echo "By customer:" . PHP_EOL;
foreach ($rows as $r) {
    echo "- customer_id={$r['customer_id']} rows={$r['c']} range={$r['min_d']}..{$r['max_d']}" . PHP_EOL;
}

$rows = q($conn, "SELECT COUNT(*) as c, MIN(report_date) as min_d, MAX(report_date) as max_d FROM amazon_detail_report");
echo PHP_EOL . "amazon_detail_report: rows=" . ($rows[0]['c'] ?? 0) . " range=" . ($rows[0]['min_d'] ?? '-') . " to " . ($rows[0]['max_d'] ?? '-') . PHP_EOL;

