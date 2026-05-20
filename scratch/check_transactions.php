<?php
require_once 'config.php';
$res = $conn->query("SELECT type, count(*) as count FROM amazon_transaction_report GROUP BY type");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['type'] . ": " . $row['count'] . "\n";
    }
} else {
    echo "Query failed: " . $conn->error;
}
?>
