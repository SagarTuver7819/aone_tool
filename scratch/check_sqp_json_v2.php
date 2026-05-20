<?php
require_once 'config.php';
$res = $conn->query("SELECT data_json FROM amazon_brand_reports WHERE report_type = 'search_query' LIMIT 5");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['data_json'] . "\n---\n";
    }
}
?>
