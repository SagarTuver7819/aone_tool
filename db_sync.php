<?php
/**
 * Live DB Sync — permissions + staff (manager) role support.
 * Run after code push:
 *   http://aone-tool.oceanhub.co.in/db_sync.php?key=AONE_SYNC_2026
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');

$SYNC_KEY = 'AONE_SYNC_2026';
$key = $_GET['key'] ?? '';

if (!hash_equals($SYNC_KEY, (string) $key)) {
    http_response_code(403);
    echo '<h2>Forbidden</h2><p>Invalid or missing sync key.</p>';
    exit;
}

$results = [];
$ok = true;

function sync_step($label, $success, $detail = '') {
    global $results, $ok;
    if (!$success) {
        $ok = false;
    }
    $results[] = [
        'label' => $label,
        'success' => $success,
        'detail' => $detail,
    ];
}

// 1) customer_permissions table (module View/Add/Edit/Delete)
$sql = "CREATE TABLE IF NOT EXISTS customer_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    module_key VARCHAR(64) NOT NULL,
    can_view TINYINT(1) NOT NULL DEFAULT 1,
    can_add TINYINT(1) NOT NULL DEFAULT 0,
    can_edit TINYINT(1) NOT NULL DEFAULT 0,
    can_delete TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_customer_module (customer_id, module_key),
    KEY idx_customer (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$created = $conn->query($sql);
sync_step('Create table customer_permissions', (bool) $created, $created ? 'OK / already exists' : ('ERROR: ' . $conn->error));

// 2) Upgrade CRUD columns if missing
$cols = [];
$res = $conn->query('SHOW COLUMNS FROM customer_permissions');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $cols[strtolower($row['Field'])] = true;
    }
}

$added = [];
foreach ([
    'can_view' => 'TINYINT(1) NOT NULL DEFAULT 1',
    'can_add' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'can_edit' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'can_delete' => 'TINYINT(1) NOT NULL DEFAULT 0',
] as $col => $def) {
    if (empty($cols[$col])) {
        $q = $conn->query("ALTER TABLE customer_permissions ADD COLUMN {$col} {$def}");
        $added[] = $col . ($q ? ':OK' : (':FAIL ' . $conn->error));
        if (!$q) {
            $ok = false;
        }
    }
}
$crudFail = false;
foreach ($added as $a) {
    if (strpos($a, 'FAIL') !== false) {
        $crudFail = true;
    }
}
sync_step(
    'CRUD columns (can_view / can_add / can_edit / can_delete)',
    !$crudFail,
    $added ? implode(', ', $added) : 'already present'
);

if (!empty($added) && !$crudFail) {
    $conn->query("UPDATE customer_permissions
        SET can_view = 1, can_add = 1, can_edit = 1, can_delete = 1
        WHERE module_key <> '__none__'");
    sync_step('Legacy permission rows upgraded to full CRUD', true, 'done');
}

// 3) users.role must allow manager (Company Staff)
$roleCol = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
$roleInfo = $roleCol ? $roleCol->fetch_assoc() : null;
$roleType = strtolower((string) ($roleInfo['Type'] ?? ''));

if ($roleType === '') {
    sync_step('users.role column', false, 'Column not found');
} elseif (strpos($roleType, 'enum') !== false) {
    if (strpos($roleType, 'manager') === false) {
        $q = $conn->query("ALTER TABLE users MODIFY role ENUM('admin','manager','customer') NOT NULL DEFAULT 'customer'");
        sync_step('Allow role = manager (Staff)', (bool) $q, $q ? 'ENUM updated' : ('ERROR: ' . $conn->error));
    } else {
        sync_step('Allow role = manager (Staff)', true, 'already supports manager');
    }
} else {
    // varchar/text — no alter needed
    sync_step('Allow role = manager (Staff)', true, 'role type is ' . $roleType . ' (ok)');
}

// 4) Verify
$check = $conn->query("SHOW TABLES LIKE 'customer_permissions'");
sync_step('Verify customer_permissions exists', $check && $check->num_rows > 0, 'FOUND');

$colList = [];
$desc = $conn->query('DESCRIBE customer_permissions');
if ($desc) {
    while ($row = $desc->fetch_assoc()) {
        $colList[] = $row['Field'];
    }
}
sync_step('Permission table columns', !empty($colList), implode(', ', $colList));

$cntRow = $conn->query('SELECT COUNT(*) AS c FROM customer_permissions');
$cnt = $cntRow ? (int) $cntRow->fetch_assoc()['c'] : 0;
sync_step('Existing permission rows', true, (string) $cnt);

$dbName = defined('DB_NAME') ? DB_NAME : '(unknown)';
$host = $_SERVER['HTTP_HOST'] ?? 'cli';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Sync | AOne</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; padding: 2rem; }
        .box { max-width: 780px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 1.5rem; }
        h1 { margin: 0 0 0.5rem; font-size: 1.25rem; }
        .meta { color: #94a3b8; font-size: 0.85rem; margin-bottom: 1.25rem; }
        .row { padding: 0.75rem 0; border-bottom: 1px solid #334155; }
        .ok { color: #4ade80; font-weight: 700; }
        .fail { color: #f87171; font-weight: 700; }
        .detail { color: #cbd5e1; font-size: 0.85rem; margin-top: 0.25rem; }
        .banner { margin-top: 1.25rem; padding: 0.9rem 1rem; border-radius: 8px; font-weight: 700; }
        .banner.ok { background: #052e16; color: #4ade80; }
        .banner.fail { background: #450a0a; color: #f87171; }
        .warn { margin-top: 1rem; font-size: 0.8rem; color: #fbbf24; }
        code { background: #334155; padding: 0.1rem 0.35rem; border-radius: 4px; }
    </style>
</head>
<body>
<div class="box">
    <h1>AOne DB Sync</h1>
    <div class="meta">
        Host: <?php echo htmlspecialchars($host); ?> ·
        DB: <?php echo htmlspecialchars($dbName); ?> ·
        <?php echo date('Y-m-d H:i:s'); ?>
    </div>

    <?php foreach ($results as $r): ?>
        <div class="row">
            <div class="<?php echo $r['success'] ? 'ok' : 'fail'; ?>">
                <?php echo $r['success'] ? 'OK' : 'FAIL'; ?> — <?php echo htmlspecialchars($r['label']); ?>
            </div>
            <?php if ($r['detail'] !== ''): ?>
                <div class="detail"><?php echo htmlspecialchars($r['detail']); ?></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="banner <?php echo $ok ? 'ok' : 'fail'; ?>">
        <?php echo $ok ? 'DB sync completed successfully.' : 'DB sync finished with errors. Check details above.'; ?>
    </div>
    <p class="warn">
        Sync covers: <code>customer_permissions</code> + CRUD columns + <code>users.role = manager</code> (Staff).<br>
        After success, delete <code>db_sync.php</code> from live for security.
    </p>
</div>
</body>
</html>
