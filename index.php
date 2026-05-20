<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: modules/dashboard/index.php");
} else {
    header("Location: login.php");
}
exit();
?>
