<?php
require_once 'config.php';
$res = $conn->query("SHOW TABLES LIKE 'amazon_sqp%'");
if ($res) {
    while($row = $res->fetch_array()) {
        echo $row[0] . "\n";
    }
}
?>
