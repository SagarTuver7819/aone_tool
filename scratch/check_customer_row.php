<?php
require_once 'config.php';
require_once 'includes/functions.php';
$c = get_all_customers();
if ($c && $row = $c->fetch_assoc()) {
    print_r($row);
}
?>
