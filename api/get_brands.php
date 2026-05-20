<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$customerId = $_GET['customer_id'] ?? 0;
$where = ($customerId > 0) ? "WHERE customer_id = $customerId" : "";

$sql = "SELECT DISTINCT campaign_name FROM (
    SELECT campaign_name FROM amazon_advertising_sp $where
    UNION
    SELECT campaign_name FROM amazon_advertising_sb $where
    UNION
    SELECT campaign_name FROM amazon_advertising_sd $where
) as c";

$res = $conn->query($sql);
$brands = [];

while($row = $res->fetch_assoc()) {
    $name = $row['campaign_name'];
    // Try to extract brand: part before first space, dash or underscore
    // But many brands have spaces. Let's try to get the part before ' SP', ' SB', ' SD'
    $brand = $name;
    if (preg_match('/^(.*?)\s*(SP|SB|SD|_SP|_SB|_SD|- SP|- SB|- SD)/i', $name, $matches)) {
        $brand = trim($matches[1]);
    } else {
        // Fallback: first segment before dash or underscore
        $parts = preg_split('/[\-_]/', $name);
        $brand = trim($parts[0]);
    }
    
    if ($brand && !in_array($brand, $brands)) {
        $brands[] = $brand;
    }
}

sort($brands);
echo json_encode($brands);
?>
