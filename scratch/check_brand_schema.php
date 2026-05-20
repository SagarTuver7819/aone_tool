<?php
require_once 'config.php';
$res = $conn->query("DESCRIBE amazon_brand_reports");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}
?>
