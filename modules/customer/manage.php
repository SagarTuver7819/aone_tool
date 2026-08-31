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
        padding: 1.25rem 2rem 2rem 2rem !important;
        overflow-x: hidden;
    }

    .cm-container {
        padding: 0;
        width: 100%;
        max-width: 100%;
        margin: 0;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    /* Topbar styling - Clean Transparent Header matching Figma */
    .figma-page-topbar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 0.75rem !important;
        flex-wrap: nowrap !important;
        padding: 0.25rem 0 1rem 0 !important;
        background: transparent !important;
        border: none !important;
        border-bottom: 1px solid #EAECEF !important;
        border-radius: 0 !important;
        margin-bottom: 1.25rem !important;
        box-shadow: none !important;
        width: 100%;
    }

    .figma-page-topbar-left {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .figma-select-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .figma-select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        min-width: 170px;
        padding: 0.45rem 2.2rem 0.45rem 0.85rem;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1E2238;
        background: #FFFFFF;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .figma-select-wrapper select:focus {
        border-color: #4362CE;
    }

    .figma-select-wrapper .select-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        width: 12px;
        height: 12px;
    }

    .figma-page-breadcrumb {
        font-size: 0.82rem;
        font-weight: 500;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .figma-page-breadcrumb .breadcrumb-dot {
        margin: 0 3px;
        opacity: 0.4;
        font-size: 0.9rem;
    }

    .figma-page-breadcrumb strong {
        color: #1E293B;
        font-weight: 600;
    }

    .figma-page-topbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-figma-primary {
        background: #4362CE !important;
        color: #FFFFFF !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1.15rem !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        box-shadow: 0px 4px 10px rgba(67, 98, 206, 0.2) !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-primary:hover {
        background: #3452BA !important;
        transform: translateY(-1px);
        color: #FFFFFF !important;
    }

    .btn-figma-outline-sm {
        background: #F1F4F9 !important;
        color: #363B4F !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1.05rem !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-outline-sm:hover {
        background: #E2E8F0 !important;
        color: #0F172A !important;
    }

    .btn-figma-icon-sm {
        width: 38px !important;
        height: 38px !important;
        border-radius: 50% !important;
        background: #F1F4F9 !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #475569 !important;
        cursor: pointer !important;
        position: relative !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-icon-sm:hover {
        background: #E2E8F0 !important;
        color: #0F172A !important;
    }

    .btn-figma-icon-sm .notif-badge {
        position: absolute;
        top: 9px;
        right: 9px;
        width: 6px;
        height: 6px;
        background: #EE473D;
        border-radius: 50%;
        border: 1.5px solid #F1F4F9;
    }

    /* Page Header */
    .cm-page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1.25rem;
        width: 100%;
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
            grid-template-columns: 1fr !important;
            gap: 1.25rem !important;
        }

        .cm-page-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }

    @media (max-width: 768px) {
        .main-wrapper {
            padding: 0.75rem 12px 90px 12px !important;
            overflow-x: hidden !important;
        }

        .cm-container {
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .figma-page-topbar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            padding-bottom: 0.75rem !important;
        }

        .figma-page-topbar-left {
            width: 100% !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 8px !important;
        }

        .figma-select-wrapper,
        .figma-select-wrapper select {
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .figma-page-breadcrumb {
            display: none !important;
        }

        .figma-page-topbar-right {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            width: 100% !important;
        }

        .figma-page-topbar-right .btn-figma-primary,
        .figma-page-topbar-right .btn-figma-outline-sm {
            flex: 1 !important;
            justify-content: center !important;
            text-align: center !important;
            padding: 0.5rem 0.6rem !important;
            font-size: 0.78rem !important;
        }

        .figma-page-topbar-right .btn-figma-icon-sm {
            flex-shrink: 0 !important;
        }

        .cm-form-grid {
            grid-template-columns: 1fr !important;
            gap: 1.25rem !important;
        }

        .cm-card {
            padding: 1.25rem 1rem !important;
            border-radius: 14px !important;
        }

        .cm-inner-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }

        .cm-form-group.full-width {
            grid-column: span 1 !important;
        }

        .cm-form-actions {
            justify-content: stretch !important;
        }

        .btn-cm-commit {
            width: 100% !important;
            justify-content: center !important;
            padding: 12px 20px !important;
        }
    }
</style>

<div class="cm-container">

    <!-- Figma-style Top Bar -->
    <div class="figma-page-topbar">
        <div class="figma-page-topbar-left">
            <div class="figma-select-wrapper">
                <select onchange="window.location.href='manage.php?id='+this.value">
                    <option value="">All Amazon Profiles</option>
                    <?php if ($all_customers):
                        while ($c = $all_customers->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($id == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['customer_name']); ?>
                            </option>
                        <?php endwhile; endif; ?>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>
            <span class="figma-page-breadcrumb">Dashboard <span class="breadcrumb-dot">•</span> <strong>Client
                    Management</strong></span>
        </div>

        <div class="figma-page-topbar-right">
            <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-figma-primary">
                <i class="fas fa-plus"></i> New Upload
            </a>
            <a href="index.php" class="btn-figma-outline-sm">
                <i class="fas fa-arrow-left"></i> Return List
            </a>
            <button type="button" class="btn-figma-icon-sm" title="Search"><i class="fas fa-search"></i></button>
            <button type="button" class="btn-figma-icon-sm" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge"></span>
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
                    <path d="M6 3.5L1.5 8M1.5 8L6 12.5M1.5 8H10.5C12.7091 8 14.5 9.79086 14.5 12V13.5" stroke="#0F172A"
                        stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
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
                        <circle cx="10" cy="6" r="4" stroke="#0F172A" stroke-width="1.5" />
                        <path d="M3.5 17.5C3.5 14.1863 6.41015 11.5 10 11.5C13.5899 11.5 16.5 14.1863 16.5 17.5"
                            stroke="#0F172A" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    <span>Identity Details</span>
                </div>

                <div class="cm-inner-grid">
                    <div class="cm-form-group">
                        <label>Seller Profile Name</label>
                        <input type="text" name="customer_name" class="cm-input"
                            value="<?php echo $customer ? htmlspecialchars($customer['customer_name']) : ''; ?>"
                            required placeholder="e.g. sagar Ocean infotech">
                    </div>

                    <div class="cm-form-group">
                        <label>Company/Legal Entity</label>
                        <input type="text" name="company_name" class="cm-input"
                            value="<?php echo $customer ? htmlspecialchars($customer['company_name']) : ''; ?>"
                            placeholder="Ocean infotech">
                    </div>

                    <div class="cm-form-group">
                        <label>Contact Email</label>
                        <input type="email" name="email" class="cm-input"
                            value="<?php echo $customer ? htmlspecialchars($customer['email']) : ''; ?>"
                            placeholder="sagar@gmail.com">
                    </div>

                    <div class="cm-form-group">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile" class="cm-input"
                            value="<?php echo $customer ? htmlspecialchars($customer['mobile']) : ''; ?>"
                            placeholder="08849967672">
                    </div>

                    <div class="cm-form-group full-width">
                        <label>Operational Status</label>
                        <div class="cm-select-wrap">
                            <select name="status" class="cm-select">
                                <option value="Active" <?php echo ($customer && $customer['status'] == 'Active') ? 'selected' : ''; ?>>Active (Syncing)</option>
                                <option value="Inactive" <?php echo ($customer && $customer['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive (Paused)</option>
                            </select>
                            <i class="fas fa-chevron-down"
                                style="position: absolute; right: 14px; pointer-events: none; font-size: 0.75rem; color: #64748B;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card: Access Credentials -->
            <div class="cm-card">
                <div class="cm-card-head">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M13.5 6.5C13.5 8.98528 11.4853 11 9 11C6.51472 11 4.5 8.98528 4.5 6.5C4.5 4.01472 6.51472 2 9 2C11.4853 2 13.5 4.01472 13.5 6.5Z"
                            stroke="#0F172A" stroke-width="1.5" />
                        <path d="M9 11V18L11 16M9 14.5L11 13" stroke="#0F172A" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span>Access Credentials</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div class="cm-form-group">
                        <label>Login Username</label>
                        <input type="text" name="login_username" class="cm-input"
                            value="<?php echo $user ? htmlspecialchars($user['username']) : ''; ?>" required
                            placeholder="username">
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