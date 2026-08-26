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

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?msg=Account Purged Successfully");
    exit();
}

// Pagination & Search
$limit = isset($_GET['limit']) ? max(5, intval($_GET['limit'])) : 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_sql = "";
if ($search !== '') {
    $esc_search = $conn->real_escape_string($search);
    $where_sql = "WHERE customer_name LIKE '%$esc_search%' OR company_name LIKE '%$esc_search%' OR email LIKE '%$esc_search%'";
}

$count_sql = "SELECT COUNT(*) as total FROM customers $where_sql";
$count_res = $conn->query($count_sql);
$total_rows = $count_res ? $count_res->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_rows / $limit);

$sql = "SELECT * FROM customers $where_sql ORDER BY id ASC LIMIT $limit OFFSET $offset";
$customers_result = $conn->query($sql);
$all_customers = get_all_customers();

$page_title = "Account Management";
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

    .btn-provision-account {
        background: #4362CE;
        color: #FFFFFF !important;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(67, 98, 206, 0.2);
        transition: all 0.15s ease;
    }

    .btn-provision-account:hover {
        background: #3451B2;
        color: #FFFFFF !important;
        box-shadow: 0 4px 8px rgba(67, 98, 206, 0.3);
    }

    /* Main Table Card */
    .cm-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        width: 100%;
    }

    .cm-alert {
        background: #EFFDF5;
        border-left: 4px solid #10B981;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        color: #065F46;
        font-weight: 600;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Table */
    .cm-table-wrap {
        overflow-x: auto;
        width: 100%;
        -webkit-overflow-scrolling: touch;
    }

    .cm-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.84rem;
    }

    .cm-table thead th {
        background: #FFFFFF;
        border-bottom: 1px solid #EAECEF;
        padding: 16px 20px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .cm-table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        color: #0F172A;
    }

    .cm-table tbody tr:last-child td {
        border-bottom: none;
    }

    .cm-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .cm-ref-id {
        font-weight: 600;
        color: #64748B;
        font-size: 0.84rem;
    }

    .cm-seller-name {
        font-weight: 600;
        color: #0F172A;
        font-size: 0.85rem;
    }

    .cm-company-name {
        font-weight: 500;
        color: #0F172A;
        font-size: 0.85rem;
    }

    .cm-email {
        font-weight: 700;
        color: #0F172A;
        font-size: 0.85rem;
    }

    .cm-connectivity {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #4362CE;
        font-weight: 600;
        font-size: 0.82rem;
    }

    .cm-status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .cm-status-active {
        background: #EFFDF5;
        color: #10B981;
    }

    .cm-status-inactive {
        background: #FFF1F2;
        color: #F43F5E;
    }

    .cm-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cm-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.15s ease;
        border: none;
        cursor: pointer;
    }

    .btn-cm-edit {
        background: transparent;
        color: #64748B;
    }

    .btn-cm-edit:hover {
        background: #F1F5F9;
        color: #0F172A;
    }

    .btn-cm-delete {
        background: #FFF1F2;
        color: #F43F5E;
    }

    .btn-cm-delete:hover {
        background: #FFE4E6;
        color: #E11D48;
    }

    /* Footer Pagination */
    .cm-table-foot {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #FFFFFF;
        border-top: 1px solid #F1F5F9;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .cm-foot-left {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.8rem;
        color: #64748B;
        font-weight: 500;
    }

    .cm-entries-select {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #0F172A;
        outline: none;
        cursor: pointer;
    }

    .cm-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .cm-page-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748B;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all 0.15s ease;
    }

    .cm-page-btn:hover {
        background: #F1F5F9;
        color: #0F172A;
    }

    .cm-page-btn.active {
        background: #4362CE;
        color: #FFFFFF;
        font-weight: 700;
    }

    .cm-page-btn.disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .cm-topbar {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .cm-topbar-left,
        .cm-topbar-right {
            width: 100%;
            justify-content: space-between;
        }

        .cm-page-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }

    @media (max-width: 768px) {
        .cm-container {
            padding: 0.75rem 0.75rem 100px 0.75rem !important;
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }

        .cm-table-foot {
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
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
                    <?php if ($all_customers):
                        while ($c = $all_customers->fetch_assoc()): ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['customer_name']); ?>
                            </option>
                        <?php endwhile; endif; ?>
                </select>
                <i class="fas fa-chevron-down"
                    style="position: absolute; right: 12px; pointer-events: none; font-size: 0.7rem; color: #64748B;"></i>
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
            <button type="button" class="btn-cm-outline" onclick="exportCustomerCSV()">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1.5V10.5M8 10.5L5 7.5M8 10.5L11 7.5" stroke="#0F172A" stroke-width="1.4"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2 12V13.5C2 14.0523 2.44772 14.5 3 14.5H13C13.5523 14.5 14 14.0523 14 13.5V12"
                        stroke="#0F172A" stroke-width="1.4" stroke-linecap="round" />
                </svg>
                Export CSV
            </button>
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
            <h2>Account Management</h2>
            <p>Manage individual Amazon Seller Profiles and synchronization settings.</p>
        </div>
        <div>
            <a href="manage.php" class="btn-provision-account">
                <i class="fas fa-plus"></i> Provision New Account
            </a>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="cm-alert">
            <i class="fas fa-check-circle" style="color: #10B981;"></i>
            <span><?php echo htmlspecialchars($_GET['msg']); ?></span>
        </div>
    <?php endif; ?>

    <!-- Main Card: Table -->
    <div class="cm-card">
        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Ref ID</th>
                        <th style="width: 22%;">Seller Profile</th>
                        <th style="width: 22%;">Entity/Company</th>
                        <th style="width: 22%;">Email Contact</th>
                        <th style="width: 12%;">Connectivity</th>
                        <th style="width: 7%;">Status</th>
                        <th style="width: 5%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($customers_result && $customers_result->num_rows > 0): ?>
                        <?php while ($row = $customers_result->fetch_assoc()): ?>
                            <tr>
                                <!-- Ref ID -->
                                <td>
                                    <span class="cm-ref-id">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                </td>

                                <!-- Seller Profile -->
                                <td>
                                    <div class="cm-seller-name"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                                </td>

                                <!-- Entity/Company -->
                                <td>
                                    <div class="cm-company-name"><?php echo htmlspecialchars($row['company_name']); ?></div>
                                </td>

                                <!-- Email Contact -->
                                <td>
                                    <div class="cm-email"><?php echo htmlspecialchars($row['email']); ?></div>
                                </td>

                                <!-- Connectivity -->
                                <td>
                                    <span class="cm-connectivity">
                                        <svg width="15" height="15" viewBox="0 0 16 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.5 9.5L9.5 6.5" stroke="#4362CE" stroke-width="1.3"
                                                stroke-linecap="round" />
                                            <path
                                                d="M7.5 4.5L9 3C10.1046 1.89543 11.8954 1.89543 13 3C14.1046 4.10457 14.1046 5.89543 13 7L11.5 8.5M8.5 11.5L7 13C5.89543 14.1046 4.10457 14.1046 3 13C1.89543 11.8954 1.89543 10.1046 3 9L4.5 7.5"
                                                stroke="#4362CE" stroke-width="1.3" stroke-linecap="round" />
                                        </svg>
                                        Live
                                    </span>
                                </td>

                                <!-- Status -->
                                <td>
                                    <span
                                        class="cm-status-badge <?php echo strtolower($row['status']) === 'active' ? 'cm-status-active' : 'cm-status-inactive'; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="cm-actions">
                                        <a href="manage.php?id=<?php echo $row['id']; ?>" class="btn-cm-action btn-cm-edit"
                                            title="Settings">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn-cm-action btn-cm-delete"
                                            title="Purge Account"
                                            onclick="return confirm('WARNING: Purging an account will delete all associated analytics. Proceed?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding: 4rem 1.5rem; text-align: center; color: #94A3B8;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1"
                                    stroke-width="1.5" style="margin-bottom: 8px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <div style="font-weight: 600; font-size: 0.9rem;">No seller accounts provisioned yet</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="cm-table-foot">
            <div class="cm-foot-left">
                <span>Show</span>
                <select class="cm-entries-select" onchange="changeLimit(this.value)">
                    <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                </select>
                <span>Entries</span>
                <span style="margin-left: 12px;">
                    Showing <?php echo $total_rows > 0 ? ($offset + 1) : 0; ?> to
                    <?php echo min($offset + $limit, $total_rows); ?> of <?php echo $total_rows; ?> entries
                </span>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="cm-pagination">
                    <!-- Prev -->
                    <a href="?page=<?php echo max(1, $page - 1); ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>"
                        class="cm-page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-chevron-left" style="font-size: 0.7rem;"></i>
                    </a>

                    <!-- Page Numbers -->
                    <?php
                    $start_p = max(1, $page - 2);
                    $end_p = min($total_pages, $start_p + 4);
                    if ($end_p - $start_p < 4) {
                        $start_p = max(1, $end_p - 4);
                    }
                    for ($p = $start_p; $p <= $end_p; $p++):
                        ?>
                        <a href="?page=<?php echo $p; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>"
                            class="cm-page-btn <?php echo $p == $page ? 'active' : ''; ?>">
                            <?php echo $p; ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Next -->
                    <a href="?page=<?php echo min($total_pages, $page + 1); ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>"
                        class="cm-page-btn <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
    function changeLimit(limitVal) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limitVal);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function exportCustomerCSV() {
        let csv = [];
        const rows = document.querySelectorAll(".cm-table tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            // Skip action column in export
            for (let j = 0; j < cols.length - 1; j++) {
                row.push('"' + cols[j].innerText.replace(/"/g, '""').replace(/\n/g, ' ') + '"');
            }
            csv.push(row.join(","));
        }
        const blob = new Blob([csv.join("\n")], { type: "text/csv" });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.setAttribute("href", url);
        a.setAttribute("download", "client_management_accounts.csv");
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>

<?php include '../../includes/footer.php'; ?>