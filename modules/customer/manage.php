<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$customer = null;
$user = null;

if ($id > 0) {
    $customer = get_customer_by_id($id);
    
    // Fetch associated user
    $stmt_user = $conn->prepare("SELECT id, username FROM users WHERE customer_id = ?");
    $stmt_user->bind_param("i", $id);
    $stmt_user->execute();
    $user = $stmt_user->get_result()->fetch_assoc();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $status = $_POST['status'];
    
    $login_user = $_POST['login_username'];
    $login_pass = $_POST['login_password'];

    if ($id > 0) {
        // Update Customer
        $stmt = $conn->prepare("UPDATE customers SET customer_name = ?, company_name = ?, email = ?, mobile = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $customer_name, $company_name, $email, $mobile, $status, $id);
        $stmt->execute();
        
        // Update User Credentials
        if ($user) {
            if (!empty($login_pass)) {
                $hashed_pass = password_hash($login_pass, PASSWORD_DEFAULT);
                $stmt_u = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE customer_id = ?");
                $stmt_u->bind_param("ssi", $login_user, $hashed_pass, $id);
            } else {
                $stmt_u = $conn->prepare("UPDATE users SET username = ? WHERE customer_id = ?");
                $stmt_u->bind_param("si", $login_user, $id);
            }
            $stmt_u->execute();
        } else {
            // Create user if it doesn't exist
            $hashed_pass = password_hash($login_pass ?: '123456', PASSWORD_DEFAULT);
            $stmt_u = $conn->prepare("INSERT INTO users (username, password, role, customer_id) VALUES (?, ?, 'customer', ?)");
            $stmt_u->bind_param("ssi", $login_user, $hashed_pass, $id);
            $stmt_u->execute();
        }

        $msg = "Customer & Credentials Updated Successfully";
    } else {
        // New Customer
        $stmt = $conn->prepare("INSERT INTO customers (customer_name, company_name, email, mobile, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $customer_name, $company_name, $email, $mobile, $status);
        $stmt->execute();
        $new_customer_id = $conn->insert_id;

        // Create user
        $hashed_pass = password_hash($login_pass ?: '123456', PASSWORD_DEFAULT);
        $stmt_u = $conn->prepare("INSERT INTO users (username, password, role, customer_id) VALUES (?, ?, 'customer', ?)");
        $stmt_u->bind_param("ssi", $login_user, $hashed_pass, $new_customer_id);
        $stmt_u->execute();

        $msg = "Customer Added with Login Credentials";
    }
    header("Location: index.php?msg=" . urlencode($msg));
    exit();
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 800;"><?php echo $id > 0 ? 'Modify' : 'Provision'; ?> Amazon Profile</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Configure identity and access credentials for the seller account.</p>
    </div>
    <a href="index.php" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> RETURN TO LIST
    </a>
</div>

<form method="POST">
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; align-items: start;">
        
        <!-- Profile Info -->
        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                <i class="fas fa-user-circle" style="color: var(--primary);"></i> Identity Details
            </h3>
            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Seller Profile Name *</label>
                    <input type="text" name="customer_name" value="<?php echo $customer ? htmlspecialchars($customer['customer_name']) : ''; ?>" required placeholder="e.g. Ocean Infotech">
                </div>
                <div class="form-group">
                    <label>Company/Legal Entity</label>
                    <input type="text" name="company_name" value="<?php echo $customer ? htmlspecialchars($customer['company_name']) : ''; ?>" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" name="email" value="<?php echo $customer ? htmlspecialchars($customer['email']) : ''; ?>" placeholder="client@example.com">
                </div>
                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" value="<?php echo $customer ? htmlspecialchars($customer['mobile']) : ''; ?>" placeholder="+1...">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Operational Status</label>
                    <select name="status" style="width: 100%;">
                        <option value="Active" <?php echo ($customer && $customer['status'] == 'Active') ? 'selected' : ''; ?>>Active (Syncing)</option>
                        <option value="Inactive" <?php echo ($customer && $customer['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive (Paused)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Credentials -->
        <div class="card" style="border-top: 4px solid var(--accent);">
            <h3 style="font-size: 1rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                <i class="fas fa-key" style="color: var(--primary);"></i> Access Credentials
            </h3>
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label>Login Username *</label>
                <input type="text" name="login_username" value="<?php echo $user ? htmlspecialchars($user['username']) : ''; ?>" required placeholder="username_for_login">
            </div>
            <div class="form-group">
                <label>Access Password <?php echo $id > 0 ? '(Leave blank to keep current)' : '*'; ?></label>
                <input type="password" name="login_password" <?php echo $id > 0 ? '' : 'required'; ?> placeholder="••••••••">
                <p style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.5rem;">
                    <?php echo $id > 0 ? 'Updating this will override the current password.' : 'Default if blank: 123456'; ?>
                </p>
            </div>
        </div>
    </div>

    <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
        <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">
            <i class="fas fa-save"></i> COMMITTING CHANGES
        </button>
    </div>
</form>

<?php include '../../includes/footer.php'; ?>
