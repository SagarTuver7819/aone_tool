<?php
require __DIR__ . '/../config.php';
$r = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
$row = $r->fetch_assoc();
echo ($row['Type'] ?? 'missing') . "\n";
