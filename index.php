<?php
require_once 'config.php';
require_once 'includes/permissions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . first_allowed_module_url());
} else {
    header("Location: login.php");
}
exit();
?>
