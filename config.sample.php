<?php
// Auto-detect Local vs. Live Environment
$isLocal = false;
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
if ($httpHost === 'localhost' || $httpHost === '127.0.0.1' || strpos($httpHost, '192.168.') === 0 || strpos($httpHost, '10.') === 0 || php_sapi_name() === 'cli') {
    $isLocal = true;
}

if ($isLocal) {
    // Local Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'ocean_crm_amazon');
} else {
    // Live Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'your_live_db_user');
    define('DB_PASS', 'your_live_db_password');
    define('DB_NAME', 'your_live_db_name');
}

// Establish Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select Database
$conn->select_db(DB_NAME);


// Set Charset
$conn->set_charset("utf8mb4");

// Global Paths
if (!defined('BASE_URL')) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');

    // If running from /modules/* or /api/*, trim back to the app root.
    $basePath = preg_replace('#/(modules|api|admin)(/.*)?$#', '', $scriptName);
    if ($basePath === $scriptName) {
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    }
    if ($basePath === '.' || $basePath === '/') {
        $basePath = '';
    }

    define('BASE_URL', $scheme . '://' . $host . $basePath . '/');
}
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Session start
if (php_sapi_name() !== 'cli' && php_sapi_name() !== 'phpdbg' && session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
