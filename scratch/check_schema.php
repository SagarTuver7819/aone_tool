<?php
require_once 'config.php';
function checkTable($conn, $table) {
    echo "\n--- $table ---\n";
    $res = $conn->query("DESC $table");
    if($res) while($row = $res->fetch_assoc()) echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
checkTable($conn, 'amazon_detail_report');
checkTable($conn, 'amazon_business_report');
checkTable($conn, 'pro_product_config');
?>
