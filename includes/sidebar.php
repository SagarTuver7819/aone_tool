<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-chart-pie" style="color: var(--primary); font-size: 1rem;"></i>
        </div>
        <span style="font-size: 1.125rem; font-weight: 800; letter-spacing: -0.5px;">AOne <span style="color: var(--accent);">Intelligence</span></span>
    </div>
    
    <div class="sidebar-nav-container">
        <ul class="nav-links">
          

            <!-- Module 2: Dashboard Overview -->
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=kpi" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'kpi') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Overview</span>
                </a>
            </li>

            <!-- <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=traffic" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'traffic') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Sales & Traffic</span>
                </a>
            </li> -->
             <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=financial" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'financial') ? 'active' : ''; ?>">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Profit & Fees</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=products" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'products') ? 'active' : ''; ?>">
                    <i class="fas fa-boxes"></i>
                    <span>Product Performance</span>
                </a>
            </li>

            <li class="nav-header" style="padding: 1rem 1rem 0.5rem; font-size: 0.65rem; color: #64748b; font-weight: 800; text-transform: capitalize; letter-spacing: 0.05em;">Advertising (PPC)</li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'amazon_ads/index') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-bullseye"></i>
                    <span>Advertising Overview</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/campaign_performance.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'campaign_performance') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-layer-group"></i>
                    <span>Campaign & Target</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/brand_analytics.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'brand_analytics') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Brand Analytics</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/pro_keywords.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'pro_keywords') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-key"></i>
                    <span>Search Term & Keywords</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/sqp.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'sqp') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-search-dollar"></i>
                    <span>Search Query (SQP)</span>
                    <span class="badge bg-primary" style="font-size: 0.5rem; margin-left: auto;">V2</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/placement.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'placement') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Placement Analysis</span>
                    <span class="badge bg-primary" style="font-size: 0.5rem; margin-left: auto;">V2</span>
                </a>
            </li>

            <li class="nav-header" style="padding: 1rem 1rem 0.5rem; font-size: 0.65rem; color: #64748b; font-weight: 800; text-transform: capitalize; letter-spacing: 0.05em;">Operations & Finance</li>

           

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ops/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'amazon_ops/index') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-undo-alt"></i>
                    <span>Returns & Reimbursements</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ops/loyalty.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'loyalty') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-user-check"></i>
                    <span>Repeat Purchase</span>
                    <span class="badge bg-primary" style="font-size: 0.5rem; margin-left: auto;">V3</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/action_center/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'action_center') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-magic"></i>
                    <span>Action Center</span>
                    <span class="badge bg-danger" style="font-size: 0.6rem; margin-left: auto;">HOT</span>
                </a>
            </li>

            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/customer/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'customer') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Client Management</span>
                </a>
            </li>
            <!-- <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dynamic_excel/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dynamic_excel') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-table"></i>
                    <span>Dynamic Excel</span>
                </a>
            </li> -->
              <!-- Module 1: Report Upload Center -->
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'report_upload') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Report Upload Center</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="sidebar-footer-user">
        <div class="user-profile-summary">
            <div class="user-avatar-circle">
                <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
            </div>
            <div class="user-details">
                <p class="user-role-label"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Guest'); ?></p>
                <p class="user-name-label"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></p>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>logout.php" class="sidebar-logout-link">
            <i class="fas fa-sign-out-alt"></i> <span class="logout-text">SIGN OUT</span>
        </a>
    </div>
</aside>

<script>
function toggleSidebar() {
    document.body.classList.toggle('sidebar-collapsed');
    if(document.body.classList.contains('sidebar-collapsed')) {
        localStorage.setItem('sidebarState', 'collapsed');
    } else {
        localStorage.setItem('sidebarState', 'expanded');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if(localStorage.getItem('sidebarState') === 'collapsed') {
        document.body.classList.add('sidebar-collapsed');
    }
});
</script>

<div class="main-wrapper">
    <header class="top-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="header-toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-title">
                <h1><?php echo htmlspecialchars($page_title ?? 'Amazon Intelligence Dashboard'); ?></h1>
                <span><?php echo htmlspecialchars($page_subtitle ?? 'Performance tracking & financial insights'); ?></span>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="text-align: right;">
                <p style="font-size: 0.875rem; font-weight: 600;"><?php echo date('l, d M Y'); ?></p>
                <p style="font-size: 0.75rem; opacity: 0.8;">Data Engine Active</p>
            </div>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> NEW UPLOAD
            </a>
            <?php endif; ?>
        </div>
    </header>
