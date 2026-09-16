<?php
/**
 * Module registry + CRUD permission helpers (View / Add / Edit / Delete).
 */

if (!function_exists('app_modules')) {

function permission_actions() {
    return ['view', 'add', 'edit', 'delete'];
}

function app_modules() {
    return [
        'overview' => [
            'label' => 'Overview',
            'group' => 'Dashboard',
            'url' => 'modules/dashboard/index.php?tab=kpi',
            'admin_only' => false,
        ],
        'profit_fees' => [
            'label' => 'Profit & Fees',
            'group' => 'Dashboard',
            'url' => 'modules/dashboard/index.php?tab=financial',
            'admin_only' => false,
        ],
        'product_performance' => [
            'label' => 'Product Performance',
            'group' => 'Dashboard',
            'url' => 'modules/dashboard/index.php?tab=products',
            'admin_only' => false,
        ],
        'advertising_overview' => [
            'label' => 'Advertising Overview',
            'group' => 'Advertising (PPC)',
            'url' => 'modules/amazon_ads/index.php',
            'admin_only' => false,
        ],
        'campaign_target' => [
            'label' => 'Campaign & Target',
            'group' => 'Advertising (PPC)',
            'url' => 'modules/amazon_ads/campaign_performance.php',
            'admin_only' => false,
        ],
        'brand_analytics' => [
            'label' => 'Brand Analytics',
            'group' => 'Advertising (PPC)',
            'url' => 'modules/amazon_ads/brand_analytics.php',
            'admin_only' => false,
        ],
        'returns' => [
            'label' => 'Return Page',
            'group' => 'Operations',
            'url' => 'modules/returns/index.php',
            'admin_only' => false,
        ],
        'reimbursements' => [
            'label' => 'Reimbursement',
            'group' => 'Operations',
            'url' => 'modules/reimbursements/index.php',
            'admin_only' => false,
        ],
        'data_source_tracking' => [
            'label' => 'Data Source Tracking',
            'group' => 'Operations',
            'url' => 'modules/report_upload/tracking.php',
            'admin_only' => false,
        ],
        // Assignable to company users (managers) by Super Admin
        'client_management' => [
            'label' => 'Client Management',
            'group' => 'Admin',
            'url' => 'modules/customer/index.php',
            'admin_only' => false,
            'staff_module' => true,
        ],
        'report_upload' => [
            'label' => 'Report Upload Center',
            'group' => 'Admin',
            'url' => 'modules/report_upload/index.php',
            'admin_only' => false,
            'staff_module' => true,
        ],
    ];
}

function ensure_permissions_schema() {
    global $conn;
    static $ready = false;
    if ($ready || !isset($conn) || !($conn instanceof mysqli)) {
        return;
    }

    $conn->query("CREATE TABLE IF NOT EXISTS customer_permissions (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Upgrade older installs that only had module_key
    $cols = [];
    $res = $conn->query("SHOW COLUMNS FROM customer_permissions");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $cols[strtolower($row['Field'])] = true;
        }
    }
    foreach (['can_view' => "TINYINT(1) NOT NULL DEFAULT 1", 'can_add' => "TINYINT(1) NOT NULL DEFAULT 0", 'can_edit' => "TINYINT(1) NOT NULL DEFAULT 0", 'can_delete' => "TINYINT(1) NOT NULL DEFAULT 0"] as $col => $def) {
        if (empty($cols[$col])) {
            $conn->query("ALTER TABLE customer_permissions ADD COLUMN {$col} {$def}");
        }
    }

    // Older rows (CRUD columns newly added with defaults): treat existing module grants as full access
    if (!empty($cols) && empty($cols['can_add'])) {
        $conn->query("UPDATE customer_permissions
            SET can_view = 1, can_add = 1, can_edit = 1, can_delete = 1
            WHERE module_key <> '__none__'");
    }

    // Ensure Staff role is allowed
    $roleCol = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
    $roleInfo = $roleCol ? $roleCol->fetch_assoc() : null;
    $roleType = strtolower((string) ($roleInfo['Type'] ?? ''));
    if (strpos($roleType, 'enum') !== false && strpos($roleType, 'manager') === false) {
        $conn->query("ALTER TABLE users MODIFY role ENUM('admin','manager','customer') NOT NULL DEFAULT 'customer'");
    }

    $ready = true;
}

function assignable_module_keys() {
    return array_keys(app_modules());
}

function customer_facing_module_keys() {
    // Backward-compatible alias: all assignable modules except pure sentinel handling
    return assignable_module_keys();
}

function default_crud_for_module($module_key) {
    return [
        'view' => 1,
        'add' => 1,
        'edit' => 1,
        'delete' => 1,
    ];
}

function empty_permissions_map() {
    return [];
}

function full_permissions_map($keys = null) {
    $keys = $keys ?: assignable_module_keys();
    $map = [];
    foreach ($keys as $key) {
        $map[$key] = default_crud_for_module($key);
    }
    return $map;
}

function is_admin_user() {
    return (($_SESSION['role'] ?? '') === 'admin');
}

function is_manager_user() {
    return (($_SESSION['role'] ?? '') === 'manager');
}

function can_select_any_customer() {
    return is_admin_user() || is_manager_user() || user_can('client_management', 'view');
}

function get_customer_permission_rows($customer_id) {
    global $conn;
    ensure_permissions_schema();
    $customer_id = intval($customer_id);
    if ($customer_id <= 0) {
        return [];
    }

    $stmt = $conn->prepare("SELECT module_key, can_view, can_add, can_edit, can_delete FROM customer_permissions WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Returns map: module_key => ['view'=>0|1, 'add'=>..., 'edit'=>..., 'delete'=>...]
 */
function get_customer_permissions_map($customer_id) {
    $rows = get_customer_permission_rows($customer_id);
    if (empty($rows)) {
        // Legacy / new form default: seller modules full CRUD (no staff modules)
        $map = [];
        foreach (app_modules() as $key => $mod) {
            if (!empty($mod['staff_module'])) {
                continue;
            }
            $map[$key] = default_crud_for_module($key);
        }
        return $map;
    }

    $map = [];
    foreach ($rows as $row) {
        $key = $row['module_key'];
        if ($key === '__none__') {
            return [];
        }
        if (!isset(app_modules()[$key])) {
            continue;
        }
        $map[$key] = [
            'view' => !empty($row['can_view']) ? 1 : 0,
            'add' => !empty($row['can_add']) ? 1 : 0,
            'edit' => !empty($row['can_edit']) ? 1 : 0,
            'delete' => !empty($row['can_delete']) ? 1 : 0,
        ];
        // View implied if any write flag set
        if ($map[$key]['add'] || $map[$key]['edit'] || $map[$key]['delete']) {
            $map[$key]['view'] = 1;
        }
    }
    return $map;
}

function get_user_permissions_map() {
    if (is_admin_user()) {
        return full_permissions_map();
    }
    $customer_id = intval($_SESSION['customer_id'] ?? 0);
    return get_customer_permissions_map($customer_id);
}

function get_user_allowed_modules() {
    $map = get_user_permissions_map();
    $allowed = [];
    foreach ($map as $key => $crud) {
        if (!empty($crud['view'])) {
            $allowed[] = $key;
        }
    }
    return $allowed;
}

function user_can_access_module($module_key) {
    return user_can($module_key, 'view');
}

function user_can($module_key, $action = 'view') {
    $action = strtolower((string) $action);
    if (!in_array($action, permission_actions(), true)) {
        return false;
    }
    if (is_admin_user()) {
        return true;
    }
    $map = get_user_permissions_map();
    if (empty($map[$module_key])) {
        return false;
    }
    return !empty($map[$module_key][$action]);
}

/**
 * Normalize POST perm[module][view|add|edit|delete] into map.
 */
function normalize_permissions_from_post($post_perm) {
    $valid_modules = assignable_module_keys();
    $map = [];

    if (!is_array($post_perm)) {
        return $map;
    }

    foreach ($post_perm as $module_key => $actions) {
        if (!in_array($module_key, $valid_modules, true) || !is_array($actions)) {
            continue;
        }
        $crud = [
            'view' => !empty($actions['view']) ? 1 : 0,
            'add' => !empty($actions['add']) ? 1 : 0,
            'edit' => !empty($actions['edit']) ? 1 : 0,
            'delete' => !empty($actions['delete']) ? 1 : 0,
        ];
        if ($crud['add'] || $crud['edit'] || $crud['delete']) {
            $crud['view'] = 1;
        }
        if ($crud['view'] || $crud['add'] || $crud['edit'] || $crud['delete']) {
            // Non-super-admin cannot grant staff modules
            $mod = app_modules()[$module_key];
            if (!empty($mod['staff_module']) && !is_admin_user()) {
                continue;
            }
            $map[$module_key] = $crud;
        }
    }
    return $map;
}

function save_customer_permissions($customer_id, $module_keys_or_map) {
    global $conn;
    ensure_permissions_schema();
    $customer_id = intval($customer_id);
    if ($customer_id <= 0) {
        return false;
    }

    // Back-compat: array of module keys => full CRUD
    if (is_array($module_keys_or_map) && (empty($module_keys_or_map) || array_keys($module_keys_or_map) === range(0, count($module_keys_or_map) - 1))) {
        $map = [];
        foreach ($module_keys_or_map as $key) {
            if (isset(app_modules()[$key])) {
                $map[$key] = default_crud_for_module($key);
            }
        }
    } else {
        $map = is_array($module_keys_or_map) ? $module_keys_or_map : [];
    }

    // Only Super Admin may grant staff modules
    if (!is_admin_user()) {
        foreach (array_keys($map) as $key) {
            if (!empty(app_modules()[$key]['staff_module'])) {
                unset($map[$key]);
            }
        }
    }

    $conn->query("DELETE FROM customer_permissions WHERE customer_id = " . $customer_id);

    if (empty($map)) {
        $stmt = $conn->prepare("INSERT INTO customer_permissions (customer_id, module_key, can_view, can_add, can_edit, can_delete) VALUES (?, ?, 0, 0, 0, 0)");
        $none = '__none__';
        $stmt->bind_param("is", $customer_id, $none);
        $stmt->execute();
        return true;
    }

    $stmt = $conn->prepare("INSERT INTO customer_permissions (customer_id, module_key, can_view, can_add, can_edit, can_delete) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($map as $key => $crud) {
        $v = !empty($crud['view']) ? 1 : 0;
        $a = !empty($crud['add']) ? 1 : 0;
        $e = !empty($crud['edit']) ? 1 : 0;
        $d = !empty($crud['delete']) ? 1 : 0;
        if ($a || $e || $d) {
            $v = 1;
        }
        $stmt->bind_param("isiiii", $customer_id, $key, $v, $a, $e, $d);
        $stmt->execute();
    }
    return true;
}

function get_customer_permissions_for_form($customer_id) {
    return get_customer_permissions_map($customer_id);
}

/** Decide user role from granted permissions (Super Admin only creates managers). */
function resolve_user_role_from_permissions(array $perm_map) {
    if (!empty($perm_map['client_management']['view']) || !empty($perm_map['report_upload']['view'])) {
        return 'manager';
    }
    return 'customer';
}

function detect_current_module_key() {
    $self = str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '');
    $tab = $_GET['tab'] ?? '';

    if (strpos($self, '/modules/dashboard/') !== false) {
        if ($tab === 'financial') return 'profit_fees';
        if ($tab === 'products') return 'product_performance';
        return 'overview';
    }
    if (strpos($self, '/modules/amazon_ads/campaign_performance.php') !== false) return 'campaign_target';
    if (strpos($self, '/modules/amazon_ads/brand_analytics.php') !== false) return 'brand_analytics';
    if (strpos($self, '/modules/amazon_ads/') !== false) return 'advertising_overview';
    if (strpos($self, '/modules/returns/') !== false) return 'returns';
    if (strpos($self, '/modules/reimbursements/') !== false) return 'reimbursements';
    if (strpos($self, '/modules/report_upload/tracking.php') !== false) return 'data_source_tracking';
    if (strpos($self, '/modules/report_upload/') !== false) return 'report_upload';
    if (strpos($self, '/modules/customer/') !== false) return 'client_management';
    return null;
}

function first_allowed_module_url() {
    $allowed = get_user_allowed_modules();
    $modules = app_modules();
    foreach ($allowed as $key) {
        if (isset($modules[$key])) {
            return BASE_URL . $modules[$key]['url'];
        }
    }
    return BASE_URL . 'logout.php';
}

function require_module_access($module_key = null) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
    if ($module_key === null) {
        $module_key = detect_current_module_key();
    }
    if ($module_key === null) {
        return;
    }
    if (!user_can($module_key, 'view')) {
        header('Location: ' . first_allowed_module_url());
        exit();
    }
}

function require_permission($module_key, $action = 'view') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
    if (!user_can($module_key, $action)) {
        header('Location: ' . first_allowed_module_url());
        exit();
    }
}

function require_admin() {
    if (!isset($_SESSION['user_id']) || !is_admin_user()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

function require_api_auth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
}

function resolve_request_customer_id($requested = null) {
    if ($requested === null) {
        $requested = isset($_REQUEST['customer_id']) ? intval($_REQUEST['customer_id']) : 0;
    } else {
        $requested = intval($requested);
    }
    if (can_select_any_customer()) {
        return $requested;
    }
    return intval($_SESSION['customer_id'] ?? 0);
}

} // end guard
