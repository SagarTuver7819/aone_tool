<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

// Check auth
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$user_role = $_SESSION['role'] ?? 'customer';
if ($user_role !== 'admin') {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

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

$all_customers = get_all_customers();
$page_title = ($id > 0 ? 'Modify' : 'Provision') . " Amazon Profile";
include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    body {
        background-color: #F8FAFC !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        color: #0F172A;
    }

    .top-header {
        display: none !important;
    }

    .main-wrapper {
        padding-top: 0 !important;
    }

    .cm-container {
        padding: 1.25rem 2rem 3rem 2rem;
        width: 100%;
        max-width: 100%;
        margin: 0;
        box-sizing: border-box;
    }

    /* Topbar */
    .cm-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #EAECEF;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
        width: 100%;
    }

    .cm-topbar-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .cm-profile-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .cm-profile-select {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        height: 38px;
        padding: 0 32px 0 12px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #0F172A;
        outline: none;
        cursor: pointer;
        min-width: 170px;
        appearance: none;
        -webkit-appearance: none;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .cm-breadcrumb {
        font-size: 0.84rem;
        color: #64748B;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cm-topbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-cm-primary {
        background: #4362CE;
        color: #FFFFFF !important;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 8px 18px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        box-shadow: 0 1px 2px rgba(67, 98, 206, 0.2);
        transition: all 0.15s ease;
    }

    .btn-cm-primary:hover {
        background: #3451B2;
        color: #FFFFFF !important;
    }

    .btn-cm-outline {
        background: #FFFFFF;
        color: #0F172A;
        border: 1px solid #E2E8F0;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-cm-outline:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
    }

    .btn-cm-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-cm-icon-box:hover {
        background: #F8FAFC;
        color: #0F172A;
    }

    /* Page Header */
    .cm-page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .cm-page-title h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .cm-page-title p {
        font-size: 0.82rem;
        color: #64748B;
        font-weight: 500;
        margin: 3px 0 0 0;
    }

    .btn-cm-return {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        color: #0F172A;
        padding: 8px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        transition: all 0.15s ease;
    }

    .btn-cm-return:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
    }

    /* Form Layout */
    .cm-form-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 1.5rem;
        align-items: start;
        width: 100%;
    }

    .cm-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        padding: 1.5rem 1.75rem;
        box-sizing: border-box;
    }

    .cm-card-head {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0F172A;
        margin-bottom: 1.5rem;
    }

    .cm-inner-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }

    .cm-form-group {
        display: flex;
        flex-direction: column;
    }

    .cm-form-group.full-width {
        grid-column: span 2;
    }

    .cm-form-group label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #0F172A;
        margin-bottom: 6px;
    }

    .cm-input {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        height: 42px;
        padding: 0 14px;
        font-size: 0.84rem;
        font-weight: 500;
        color: #0F172A;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        font-family: inherit;
        transition: all 0.15s ease;
    }

    .cm-input:focus {
        background: #FFFFFF;
        border-color: #4362CE;
        box-shadow: 0 0 0 3px rgba(67, 98, 206, 0.1);
    }

    .cm-select-wrap {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .cm-select {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        height: 42px;
        padding: 0 36px 0 14px;
        font-size: 0.84rem;
        font-weight: 500;
        color: #0F172A;
        outline: none;
        width: 100%;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        box-sizing: border-box;
        font-family: inherit;
        transition: all 0.15s ease;
    }

    .cm-select:focus {
        background: #FFFFFF;
        border-color: #4362CE;
        box-shadow: 0 0 0 3px rgba(67, 98, 206, 0.1);
    }

    .cm-subtext {
        font-size: 0.72rem;
        color: #64748B;
        margin-top: 6px;
    }

    /* Actions Footer */
    .cm-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.5rem;
        width: 100%;
    }

    .btn-cm-commit {
        background: #4362CE;
        color: #FFFFFF !important;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 10px 26px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(67, 98, 206, 0.2);
        transition: all 0.15s ease;
    }

    .btn-cm-commit:hover {
        background: #3451B2;
        box-shadow: 0 4px 8px rgba(67, 98, 206, 0.3);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .cm-form-grid {
            grid-template-columns: 1fr;
        }

        .cm-topbar {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .cm-topbar-left, .cm-topbar-right {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 768px) {
        .cm-container {
            padding: 0.75rem 0.75rem 100px 0.75rem !important;
        }

        .cm-inner-grid {
            grid-template-columns: 1fr;
        }

        .cm-form-group.full-width {
            grid-column: span 1;
        }
    }
</style>

<div class="cm-container">

    <!-- Global Topbar (Figma Matching) -->
    <div class="cm-topbar">
        <div class="cm-topbar-left">
            <div class="cm-profile-select-wrap">
                <select class="cm-profile-select">
                    <option value="">All Amazon Profiles</option>
                    <?php if ($all_customers): while ($c = $all_customers->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($id == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['customer_name']); ?>
                        </option>
                    <?php endwhile; endif; ?>
                </select>
                <i class="fas fa-chevron-down" style="position: absolute; right: 12px; pointer-events: none; font-size: 0.7rem; color: #64748B;"></i>
            </div>
            <div class="cm-breadcrumb">
                <span>Dashboard</span>
                <i class="fas fa-circle" style="font-size: 0.25rem; color: #CBD5E1;"></i>
                <span>Profit &amp; Loss Analysis</span>
            </div>
        </div>

        <div class="cm-topbar-right">
            <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-cm-primary">
                <i class="fas fa-plus"></i> New Upload
            </a>
            <a href="index.php" class="btn-cm-outline">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1.5V10.5M8 10.5L5 7.5M8 10.5L11 7.5" stroke="#0F172A" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12V13.5C2 14.0523 2.44772 14.5 3 14.5H13C13.5523 14.5 14 14.0523 14 13.5V12" stroke="#0F172A" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
                Export CSV
            </a>
            <button type="button" class="btn-cm-icon-box" title="Search">
                <i class="fas fa-search" style="font-size: 0.85rem;"></i>
            </button>
            <button type="button" class="btn-cm-icon-box" title="Notifications">
                <i class="fas fa-bell" style="font-size: 0.85rem;"></i>
            </button>
        </div>
    </div>

    <!-- Page Header (Figma Matching) -->
    <div class="cm-page-head">
        <div class="cm-page-title">
            <h2><?php echo $id > 0 ? 'Modify' : 'Provision'; ?> Amazon Profile</h2>
            <p>Configure identity and access credentials for the seller account.</p>
        </div>
        <div>
            <a href="index.php" class="btn-cm-return">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 3.5L1.5 8M1.5 8L6 12.5M1.5 8H10.5C12.7091 8 14.5 9.79086 14.5 12V13.5" stroke="#0F172A" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Return To List
            </a>
        </div>
    </div>

    <form method="POST">
        <div class="cm-form-grid">
            
            <!-- Left Card: Identity Details -->
            <div class="cm-card">
                <div class="cm-card-head">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="6" r="4" stroke="#0F172A" stroke-width="1.5"/>
                        <path d="M3.5 17.5C3.5 14.1863 6.41015 11.5 10 11.5C13.5899 11.5 16.5 14.1863 16.5 17.5" stroke="#0F172A" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span>Identity Details</span>
                </div>

                <div class="cm-inner-grid">
                    <div class="cm-form-group">
                        <label>Seller Profile Name</label>
                        <input type="text" name="customer_name" class="cm-input" value="<?php echo $customer ? htmlspecialchars($customer['customer_name']) : ''; ?>" required placeholder="e.g. sagar Ocean infotech">
                    </div>

                    <div class="cm-form-group">
                        <label>Company/Legal Entity</label>
                        <input type="text" name="company_name" class="cm-input" value="<?php echo $customer ? htmlspecialchars($customer['company_name']) : ''; ?>" placeholder="Ocean infotech">
                    </div>

                    <div class="cm-form-group">
                        <label>Contact Email</label>
                        <input type="email" name="email" class="cm-input" value="<?php echo $customer ? htmlspecialchars($customer['email']) : ''; ?>" placeholder="sagar@gmail.com">
                    </div>

                    <div class="cm-form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" class="cm-input" value="<?php echo $customer ? htmlspecialchars($customer['mobile']) : ''; ?>" placeholder="08849967672">
                    </div>

                    <div class="cm-form-group full-width">
                        <label>Operational Status</label>
                        <div class="cm-select-wrap">
                            <select name="status" class="cm-select">
                                <option value="Active" <?php echo ($customer && $customer['status'] == 'Active') ? 'selected' : ''; ?>>Active (Syncing)</option>
                                <option value="Inactive" <?php echo ($customer && $customer['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive (Paused)</option>
                            </select>
                            <i class="fas fa-chevron-down" style="position: absolute; right: 14px; pointer-events: none; font-size: 0.75rem; color: #64748B;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: Access Credentials -->
            <div class="cm-card">
                <div class="cm-card-head">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.5 6.5C13.5 8.98528 11.4853 11 9 11C6.51472 11 4.5 8.98528 4.5 6.5C4.5 4.01472 6.51472 2 9 2C11.4853 2 13.5 4.01472 13.5 6.5Z" stroke="#0F172A" stroke-width="1.5"/>
                        <path d="M9 11V18L11 16M9 14.5L11 13" stroke="#0F172A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Access Credentials</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div class="cm-form-group">
                        <label>Login Username</label>
                        <input type="text" name="login_username" class="cm-input" value="<?php echo $user ? htmlspecialchars($user['username']) : ''; ?>" required placeholder="username">
                    </div>

                    <div class="cm-form-group">
                        <label>Access Password <?php echo $id > 0 ? '(Leave blank to keep current)' : ''; ?></label>
                        <input type="password" name="login_password" class="cm-input" <?php echo $id > 0 ? '' : 'required'; ?> placeholder="••••••••">
                        <div class="cm-subtext">
                            <?php echo $id > 0 ? 'Updating this will override the current password.' : 'Default password: 123456'; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="cm-form-actions">
            <button type="submit" class="btn-cm-commit">
                Committing Changes
            </button>
        </div>
    </form>

</div>

<?php include '../../includes/footer.php'; ?>
