<?php
require_once 'config.php';
$res = $conn->query("SELECT customer_id, COUNT(*) as count FROM amazon_brand_reports WHERE report_type = 'search_query' GROUP BY customer_id");
if ($res) {
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
}
?>
