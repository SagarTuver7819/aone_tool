<!-- Mobile Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebar_backdrop" onclick="toggleSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand-wrapper" onclick="toggleSidebar()" role="button" tabindex="0"
            title="Toggle Sidebar Menu">
            <div class="sidebar-brand-logo">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                </svg>
            </div>
            <div class="sidebar-brand-text">
                <span class="brand-title">A'One</span>
                <span class="brand-subtitle">Intelligence</span>
            </div>
        </div>
        <button class="sidebar-toggle-btn" onclick="toggleSidebar()" title="Collapse / Expand Sidebar"
            aria-label="Toggle Sidebar">
            <svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g opacity="0.7">
                    <path
                        d="M8.66667 2H7.33333C4.81917 2 3.5621 2 2.78105 2.78105C2 3.5621 2 4.81917 2 7.33333V8.66667C2 11.1808 2 12.4379 2.78105 13.2189C3.5621 14 4.81917 14 7.33333 14H8.66667C11.1808 14 12.4379 14 13.2189 13.2189C14 12.4379 14 11.1808 14 8.66667V7.33333C14 4.81917 14 3.5621 13.2189 2.78105C12.4379 2 11.1808 2 8.66667 2Z"
                        stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6 2V14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" />
                </g>
            </svg>
        </button>
        <button class="sidebar-mobile-close-btn" onclick="toggleSidebar()" title="Close Sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-nav-container">
        <ul class="nav-links">
            <!-- Module 2: Dashboard Overview -->
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=kpi" title="Overview"
                    class="nav-link <?php echo ((strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? 'kpi') === 'kpi') || (strpos($_SERVER['PHP_SELF'], 'index.php') !== false && empty($_GET['tab']) && strpos($_SERVER['PHP_SELF'], 'modules') === false)) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.32901 3.05957C5.0587 3.09768 4.30132 3.23907 3.77726 3.76361C3.05981 4.48173 3.05981 5.63751 3.05981 7.94907V13.2612C3.05981 15.5728 3.05981 16.7286 3.77726 17.4467C4.4947 18.1648 5.64941 18.1648 7.95881 18.1648H12.0413C14.3507 18.1648 15.5054 18.1648 16.2229 17.4467C16.9403 16.7286 16.9403 15.5728 16.9403 13.2612V7.94907C16.9403 5.63751 16.9403 4.48173 16.2229 3.76362C15.6988 3.23907 14.9414 3.09768 13.6711 3.05957"
                                stroke="currentColor" stroke-width="1.3" />
                            <path d="M6.7356 13.2661H10.0016M6.7356 9.18359H13.2676" stroke="currentColor"
                                stroke-width="1.3" stroke-linecap="round" />
                            <path
                                d="M6.32544 3.26384C6.32544 2.47469 6.96518 1.83496 7.75431 1.83496H12.2451C13.0342 1.83496 13.6739 2.47469 13.6739 3.26384C13.6739 4.05298 13.0342 4.69271 12.2451 4.69271H7.75431C6.96518 4.69271 6.32544 4.05298 6.32544 3.26384Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Overview</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=financial" title="Profit & Fees"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'financial') ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3.59835 16.4017C2.5 15.3033 2.5 13.5355 2.5 10C2.5 6.46447 2.5 4.6967 3.59835 3.59835C4.6967 2.5 6.46447 2.5 10 2.5C13.5355 2.5 15.3033 2.5 16.4017 3.59835C17.5 4.6967 17.5 6.46447 17.5 10C17.5 13.5355 17.5 15.3033 16.4017 16.4017C15.3033 17.5 13.5355 17.5 10 17.5C6.46447 17.5 4.6967 17.5 3.59835 16.4017Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M5.83398 11.6673L8.16139 9.3399C8.48682 9.01448 9.01448 9.01448 9.3399 9.3399L10.6614 10.6614C10.9868 10.9868 11.5145 10.9868 11.8399 10.6614L14.1673 8.33398"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Profit & Fees</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=products" title="Product Performance"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'products') ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.75032 17.9984C8.1702 18.3333 7.39135 18.3333 5.83366 18.3333C4.27597 18.3333 3.49712 18.3333 2.91699 17.9984C2.53694 17.779 2.22135 17.4634 2.00193 17.0833C1.66699 16.5032 1.66699 15.7243 1.66699 14.1667C1.66699 12.609 1.66699 11.8302 2.00193 11.25C2.22135 10.8699 2.53694 10.5543 2.91699 10.3349C3.49712 10 4.27597 10 5.83366 10C7.39135 10 8.1702 10 8.75032 10.3349C9.13041 10.5543 9.44599 10.8699 9.66541 11.25C10.0003 11.8302 10.0003 12.609 10.0003 14.1667C10.0003 15.7243 10.0003 16.5032 9.66541 17.0833C9.44599 17.4634 9.13041 17.779 8.75032 17.9984Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M17.0833 17.9984C16.5032 18.3333 15.7243 18.3333 14.1667 18.3333C12.609 18.3333 11.8302 18.3333 11.25 17.9984C10.8699 17.779 10.5543 17.4634 10.3349 17.0833C10 16.5032 10 15.7243 10 14.1667C10 12.609 10 11.8302 10.3349 11.25C10.5543 10.8699 10.8699 10.5543 11.25 10.3349C11.8302 10 12.609 10 14.1667 10C15.7243 10 16.5032 10 17.0833 10.3349C17.4634 10.5543 17.779 10.8699 17.9984 11.25C18.3333 11.8302 18.3333 12.609 18.3333 14.1667C18.3333 15.7243 18.3333 16.5032 17.9984 17.0833C17.779 17.4634 17.4634 17.779 17.0833 17.9984Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M12.9163 9.66492C12.3362 9.99984 11.5573 9.99984 9.99967 9.99984C8.44201 9.99984 7.66313 9.99984 7.08301 9.66492C6.70296 9.4455 6.38737 9.12992 6.16794 8.74984C5.83301 8.16971 5.83301 7.39086 5.83301 5.83317C5.83301 4.27548 5.83301 3.49663 6.16794 2.9165C6.38737 2.53645 6.70296 2.22086 7.08301 2.00144C7.66313 1.6665 8.44201 1.6665 9.99967 1.6665C11.5573 1.6665 12.3362 1.6665 12.9163 2.00144C13.2964 2.22086 13.612 2.53645 13.8314 2.9165C14.1663 3.49663 14.1663 4.27548 14.1663 5.83317C14.1663 7.39086 14.1663 8.16971 13.8314 8.74984C13.612 9.12992 13.2964 9.4455 12.9163 9.66492Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M10 1.6665V4.1665" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M5.83301 10V12.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M14.167 10V12.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Product Performance</span>
                </a>
            </li>

            <li class="nav-header">ADVERTISING (PPC)</li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/index.php" title="Advertising Overview"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'amazon_ads/index') !== false) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M17.5 17.5H8.33333C5.58347 17.5 4.20854 17.5 3.35427 16.6457C2.5 15.7914 2.5 14.4165 2.5 11.6667V2.5"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path d="M5.83301 3.3335H6.66634" stroke="currentColor" stroke-width="1.3"
                                stroke-linecap="round" />
                            <path d="M5.83301 5.8335H9.16634" stroke="currentColor" stroke-width="1.3"
                                stroke-linecap="round" />
                            <path
                                d="M4.16699 16.6668C5.05943 15.0443 6.26932 10.8492 8.58891 10.8492C10.1921 10.8492 10.6072 12.8932 12.1783 12.8932C14.8813 12.8932 14.4895 8.3335 17.5003 8.3335"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Advertising Overview</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/campaign_performance.php" title="Campaign & Target"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'campaign_performance') !== false) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.0051 4.01098C8.98832 3.55932 9.47991 3.3335 10.0003 3.3335C10.5207 3.3335 11.0123 3.55932 11.9956 4.01098L16.0731 5.88403C17.5802 6.5763 18.3337 6.92245 18.3337 7.50016C18.3337 8.07788 17.5802 8.424 16.0731 9.11633L11.9956 10.9893C11.0123 11.441 10.5207 11.6668 10.0003 11.6668C9.47991 11.6668 8.98832 11.441 8.0051 10.9893L3.92758 9.11633C2.42052 8.424 1.66699 8.07788 1.66699 7.50016C1.66699 6.92245 2.42052 6.5763 3.92758 5.88403L8.0051 4.01098Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M16.8609 11.25C17.8427 11.7183 18.3337 12.0338 18.3337 12.5001C18.3337 13.0778 17.5802 13.4239 16.0731 14.1163L11.9956 15.9893C11.0123 16.4409 10.5207 16.6667 10.0003 16.6667C9.47991 16.6667 8.98832 16.4409 8.0051 15.9893L3.92758 14.1163C2.42052 13.4239 1.66699 13.0778 1.66699 12.5001C1.66699 12.0338 2.1579 11.7183 3.13972 11.25"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Campaign & Target</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/amazon_ads/brand_analytics.php" title="Brand Analytics"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'brand_analytics') !== false) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.83398 15.0007V13.334M10.0007 15.0007V12.5007M14.1673 15.0007V10.834M2.08398 10.0007C2.08398 6.2687 2.08398 4.40273 3.24335 3.24335C4.40273 2.08398 6.2687 2.08398 10.0007 2.08398C13.7326 2.08398 15.5986 2.08398 16.758 3.24335C17.9173 4.40273 17.9173 6.2687 17.9173 10.0007C17.9173 13.7326 17.9173 15.5986 16.758 16.758C15.5986 17.9173 13.7326 17.9173 10.0007 17.9173C6.2687 17.9173 4.40273 17.9173 3.24335 16.758C2.08398 15.5986 2.08398 13.7326 2.08398 10.0007Z"
                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M4.99316 9.57208C6.78908 9.63192 10.8614 9.36083 13.1778 5.6846M11.6599 5.24046L13.2228 4.98891C13.4133 4.96465 13.693 5.11504 13.7618 5.29431L14.175 6.65968"
                                stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Brand Analytics</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/returns/index.php" title="Return Page"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'returns') !== false) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.5 10.8332V6.6665H17.5V10.8332C17.5 13.9758 17.5 15.5473 16.5237 16.5235C15.5474 17.4998 13.976 17.4998 10.8333 17.4998H9.16667C6.02397 17.4998 4.45262 17.4998 3.47631 16.5235C2.5 15.5473 2.5 13.9758 2.5 10.8332Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M2.5 6.66667L3.22115 5.0641C3.78045 3.82122 4.06009 3.19979 4.62692 2.84989C5.19376 2.5 5.92083 2.5 7.375 2.5H12.625C14.0792 2.5 14.8063 2.5 15.3731 2.84989C15.9399 3.19979 16.2196 3.82122 16.7788 5.0641L17.5 6.66667"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path d="M10 6.66667V2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path
                                d="M7.08366 11.2502H11.667C12.5875 11.2502 13.3337 11.9963 13.3337 12.9168C13.3337 13.8373 12.5875 14.5835 11.667 14.5835H10.8337M8.33366 9.5835L6.66699 11.2502L8.33366 12.9168"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Return Page</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/reimbursements/index.php" title="Reimbursement"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'reimbursements') !== false) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.0833 18.3333L11.8204 17.9843C11.2278 17.1974 11.0808 15.9953 11.4557 15"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path d="M7.91699 18.3333L8.17991 17.9843C8.77258 17.1974 8.91958 15.9953 8.54466 15"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path d="M5.83301 18.3335H14.1663" stroke="currentColor" stroke-width="1.3"
                                stroke-linecap="round" />
                            <path
                                d="M9.99967 5.83333C9.07917 5.83333 8.33301 6.39297 8.33301 7.08333C8.33301 7.77369 9.07917 8.33333 9.99967 8.33333C10.9202 8.33333 11.6663 8.893 11.6663 9.58333C11.6663 10.2737 10.9202 10.8333 9.99967 10.8333M9.99967 5.83333C10.7253 5.83333 11.3427 6.18117 11.5715 6.66667M9.99967 5.83333V5M9.99967 10.8333C9.27401 10.8333 8.65667 10.4855 8.42784 10M9.99967 10.8333V11.6667"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path
                                d="M11.667 1.6665H8.33366C5.60097 1.6665 4.23463 1.6665 3.26608 2.34469C2.90775 2.59559 2.59608 2.90726 2.34518 3.2656C1.66699 4.23414 1.66699 5.60048 1.66699 8.33317C1.66699 11.0658 1.66699 12.4322 2.34518 13.4008C2.59608 13.7591 2.90775 14.0708 3.26608 14.3217C4.23463 14.9998 5.60097 14.9998 8.33366 14.9998H11.667C14.3997 14.9998 15.766 14.9998 16.7346 14.3217C17.0929 14.0708 17.4046 13.7591 17.6555 13.4008C18.3337 12.4322 18.3337 11.0658 18.3337 8.33317C18.3337 5.60048 18.3337 4.23414 17.6555 3.2656C17.4046 2.90726 17.0929 2.59559 16.7346 2.34469C15.766 1.6665 14.3997 1.6665 11.667 1.6665Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                        </svg>
                    </span>
                    <span>Reimbursement</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>modules/report_upload/tracking.php" title="Data Source Tracking"
                    class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'report_upload/tracking') !== false) ? 'active' : ''; ?>">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.2497 10.8333L14.1663 7.5M11.6663 12.5C11.6663 13.4205 10.9202 14.1667 9.99967 14.1667C9.07917 14.1667 8.33301 13.4205 8.33301 12.5C8.33301 11.5795 9.07917 10.8333 9.99967 10.8333C10.9202 10.8333 11.6663 11.5795 11.6663 12.5Z"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path d="M5 10C5 7.23857 7.23857 5 10 5C10.9107 5 11.7646 5.24348 12.5 5.66891"
                                stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                            <path
                                d="M2.08301 10.0002C2.08301 6.26821 2.08301 4.40224 3.24237 3.24286C4.40175 2.0835 6.26772 2.0835 9.99971 2.0835C13.7316 2.0835 15.5976 2.0835 16.757 3.24286C17.9164 4.40224 17.9164 6.26821 17.9164 10.0002C17.9164 13.7321 17.9164 15.5981 16.757 16.7574C15.5976 17.9169 13.7316 17.9169 9.99971 17.9169C6.26772 17.9169 4.40175 17.9169 3.24237 16.7574C2.08301 15.5981 2.08301 13.7321 2.08301 10.0002Z"
                                stroke="currentColor" stroke-width="1.3" />
                        </svg>
                    </span>
                    <span>Data Source Tracking</span>
                </a>
            </li>

            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>modules/customer/index.php" title="Client Management"
                        class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'customer') !== false) ? 'active' : ''; ?>">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15.5128 16.666H15.9213C16.8794 16.666 17.6416 16.2294 18.3259 15.619C20.0643 14.0682 15.9778 12.4993 14.5827 12.4993M12.916 4.22332C13.1053 4.18579 13.3018 4.16602 13.5033 4.16602C15.0199 4.16602 16.2493 5.28531 16.2493 6.66602C16.2493 8.04672 15.0199 9.16602 13.5033 9.16602C13.3018 9.16602 13.1053 9.14627 12.916 9.10868"
                                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                <path
                                    d="M3.73443 13.426C2.75195 13.9525 0.175949 15.0276 1.7449 16.3728C2.51133 17.03 3.36493 17.5 4.4381 17.5H10.5619C11.6351 17.5 12.4887 17.03 13.2551 16.3728C14.8241 15.0276 12.2481 13.9525 11.2656 13.426C8.96167 12.1913 6.03833 12.1913 3.73443 13.426Z"
                                    stroke="currentColor" stroke-width="1.3" />
                                <path
                                    d="M10.8327 6.24935C10.8327 8.0903 9.34027 9.58268 7.49935 9.58268C5.6584 9.58268 4.16602 8.0903 4.16602 6.24935C4.16602 4.4084 5.6584 2.91602 7.49935 2.91602C9.34027 2.91602 10.8327 4.4084 10.8327 6.24935Z"
                                    stroke="currentColor" stroke-width="1.3" />
                            </svg>
                        </span>
                        <span>Client Management</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" title="Report Upload Center"
                        class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'report_upload/index') !== false) ? 'active' : ''; ?>">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M2.5 14.1665C2.5 14.9415 2.5 15.329 2.58518 15.6469C2.81635 16.5096 3.49023 17.1835 4.35295 17.4147C4.67087 17.4998 5.05836 17.4998 5.83333 17.4998H14.1666C14.9416 17.4998 15.3291 17.4998 15.6471 17.4147C16.5098 17.1835 17.1836 16.5096 17.4148 15.6469C17.5 15.329 17.5 14.9415 17.5 14.1665"
                                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M13.75 6.24998C13.75 6.24998 10.9882 2.50001 9.99998 2.5C9.01173 2.49999 6.25 6.25 6.25 6.25M9.99998 3.33333V13.3334"
                                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>Report Upload Center</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="sidebar-footer-container">
        <div class="sidebar-user-card">
            <div class="user-card-info">
                <div class="user-card-avatar">
                    <span
                        class="avatar-text"><?php echo strtoupper(substr($_SESSION['username'] ?? 'J', 0, 1)); ?></span>
                </div>
                <div class="user-card-meta">
                    <div class="user-card-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'John Smith'); ?>
                    </div>
                    <div class="user-card-role"><?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'Premium')); ?>
                    </div>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>logout.php" class="user-card-logout" title="Sign Out">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M5.29951 5.44922C3.88818 6.71238 3 8.38833 3 10.4314C3 14.2414 6.08857 17.33 9.89852 17.33C13.7085 17.33 16.797 14.2414 16.797 10.4314C16.797 8.38833 15.9088 6.71238 14.4975 5.44922"
                        stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M9.89843 2V8.89852M9.89843 2C9.36173 2 8.35894 3.52864 7.98218 3.91626M9.89843 2C10.4351 2 11.438 3.52864 11.8147 3.91626"
                        stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </div>
</aside>

<!-- Native Mobile App Bottom Navigation Bar -->
<nav class="mobile-bottom-app-bar" id="mobile_bottom_bar">
    <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=kpi"
        class="mobile-bottom-nav-item <?php echo ((strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? 'kpi') === 'kpi') || (strpos($_SERVER['PHP_SELF'], 'index.php') !== false && empty($_GET['tab']) && strpos($_SERVER['PHP_SELF'], 'modules') === false)) ? 'active' : ''; ?>"
        title="Overview">
        <div class="mobile-nav-icon">
            <svg width="22" height="22" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M6.32901 3.05957C5.0587 3.09768 4.30132 3.23907 3.77726 3.76361C3.05981 4.48173 3.05981 5.63751 3.05981 7.94907V13.2612C3.05981 15.5728 3.05981 16.7286 3.77726 17.4467C4.4947 18.1648 5.64941 18.1648 7.95881 18.1648H12.0413C14.3507 18.1648 15.5054 18.1648 16.2229 17.4467C16.9403 16.7286 16.9403 15.5728 16.9403 13.2612V7.94907C16.9403 5.63751 16.9403 4.48173 16.2229 3.76362C15.6988 3.23907 14.9414 3.09768 13.6711 3.05957"
                    stroke="currentColor" stroke-width="1.3" />
                <path d="M6.7356 13.2661H10.0016M6.7356 9.18359H13.2676" stroke="currentColor" stroke-width="1.3"
                    stroke-linecap="round" />
                <path
                    d="M6.32544 3.26384C6.32544 2.47469 6.96518 1.83496 7.75431 1.83496H12.2451C13.0342 1.83496 13.6739 2.47469 13.6739 3.26384C13.6739 4.05298 13.0342 4.69271 12.2451 4.69271H7.75431C6.96518 4.69271 6.32544 4.05298 6.32544 3.26384Z"
                    stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" />
            </svg>
        </div>
        <span class="mobile-nav-label">Overview</span>
    </a>

    <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=financial"
        class="mobile-bottom-nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'financial') ? 'active' : ''; ?>"
        title="Profit & Fees">
        <div class="mobile-nav-icon">
            <svg width="22" height="22" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M3.59835 16.4017C2.5 15.3033 2.5 13.5355 2.5 10C2.5 6.46447 2.5 4.6967 3.59835 3.59835C4.6967 2.5 6.46447 2.5 10 2.5C13.5355 2.5 15.3033 2.5 16.4017 3.59835C17.5 4.6967 17.5 6.46447 17.5 10C17.5 13.5355 17.5 15.3033 16.4017 16.4017C15.3033 17.5 13.5355 17.5 10 17.5C6.46447 17.5 4.6967 17.5 3.59835 16.4017Z"
                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M5.83398 11.6673L8.16139 9.3399C8.48682 9.01448 9.01448 9.01448 9.3399 9.3399L10.6614 10.6614C10.9868 10.9868 11.5145 10.9868 11.8399 10.6614L14.1673 8.33398"
                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <span class="mobile-nav-label">Profit</span>
    </a>

    <a href="<?php echo BASE_URL; ?>modules/dashboard/index.php?tab=products"
        class="mobile-bottom-nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false && ($_GET['tab'] ?? '') === 'products') ? 'active' : ''; ?>"
        title="Products">
        <div class="mobile-nav-icon">
            <svg width="22" height="22" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M8.75032 17.9984C8.1702 18.3333 7.39135 18.3333 5.83366 18.3333C4.27597 18.3333 3.49712 18.3333 2.91699 17.9984C2.53694 17.779 2.22135 17.4634 2.00193 17.0833C1.66699 16.5032 1.66699 15.7243 1.66699 14.1667C1.66699 12.609 1.66699 11.8302 2.00193 11.25C2.22135 10.8699 2.53694 10.5543 2.91699 10.3349C3.49712 10 4.27597 10 5.83366 10C7.39135 10 8.1702 10 8.75032 10.3349C9.13041 10.5543 9.44599 10.8699 9.66541 11.25C10.0003 11.8302 10.0003 12.609 10.0003 14.1667C10.0003 15.7243 10.0003 16.5032 9.66541 17.0833C9.44599 17.4634 9.13041 17.779 8.75032 17.9984Z"
                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M17.0833 17.9984C16.5032 18.3333 15.7243 18.3333 14.1667 18.3333C12.609 18.3333 11.8302 18.3333 11.25 17.9984C10.8699 17.779 10.5543 17.4634 10.3349 17.0833C10 16.5032 10 15.7243 10 14.1667C10 12.609 10 11.8302 10.3349 11.25C10.5543 10.8699 10.8699 10.5543 11.25 10.3349C11.8302 10 12.609 10 14.1667 10C15.7243 10 16.5032 10 17.0833 10.3349C17.4634 10.5543 17.779 10.8699 17.9984 11.25C18.3333 11.8302 18.3333 12.609 18.3333 14.1667C18.3333 15.7243 18.3333 16.5032 17.9984 17.0833C17.779 17.4634 17.4634 17.779 17.0833 17.9984Z"
                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M12.9163 9.66492C12.3362 9.99984 11.5573 9.99984 9.99967 9.99984C8.44201 9.99984 7.66313 9.99984 7.08301 9.66492C6.70296 9.4455 6.38737 9.12992 6.16794 8.74984C5.83301 8.16971 5.83301 7.39086 5.83301 5.83317C5.83301 4.27548 5.83301 3.49663 6.16794 2.9165C6.38737 2.53645 6.70296 2.22086 7.08301 2.00144C7.66313 1.6665 8.44201 1.6665 9.99967 1.6665C11.5573 1.6665 12.3362 1.6665 12.9163 2.00144C13.2964 2.22086 13.612 2.53645 13.8314 2.9165C14.1663 3.49663 14.1663 4.27548 14.1663 5.83317C14.1663 7.39086 14.1663 8.16971 13.8314 8.74984C13.612 9.12992 13.2964 9.4455 12.9163 9.66492Z"
                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M10 1.6665V4.1665" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                    stroke-linejoin="round" />
                <path d="M5.83301 10V12.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                    stroke-linejoin="round" />
                <path d="M14.167 10V12.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </div>
        <span class="mobile-nav-label">Products</span>
    </a>

    <a href="<?php echo BASE_URL; ?>modules/amazon_ads/index.php"
        class="mobile-bottom-nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'amazon_ads') !== false) ? 'active' : ''; ?>"
        title="Advertising">
        <div class="mobile-nav-icon">
            <svg width="22" height="22" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M17.5 17.5H8.33333C5.58347 17.5 4.20854 17.5 3.35427 16.6457C2.5 15.7914 2.5 14.4165 2.5 11.6667V2.5"
                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                <path d="M5.83301 3.3335H6.66634" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                <path d="M5.83301 5.8335H9.16634" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                <path
                    d="M4.16699 16.6668C5.05943 15.0443 6.26932 10.8492 8.58891 10.8492C10.1921 10.8492 10.6072 12.8932 12.1783 12.8932C14.8813 12.8932 14.4895 8.3335 17.5003 8.3335"
                    stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <span class="mobile-nav-label">Ads</span>
    </a>

    <button type="button" onclick="toggleSidebar()" class="mobile-bottom-nav-item mobile-menu-btn" title="More Menus">
        <div class="mobile-nav-icon">
            <svg width="22" height="22" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2.5" y="2.5" width="6" height="6" rx="2" stroke="currentColor" stroke-width="1.4" />
                <rect x="11.5" y="2.5" width="6" height="6" rx="2" stroke="currentColor" stroke-width="1.4" />
                <rect x="2.5" y="11.5" width="6" height="6" rx="2" stroke="currentColor" stroke-width="1.4" />
                <rect x="11.5" y="11.5" width="6" height="6" rx="2" stroke="currentColor" stroke-width="1.4" />
            </svg>
        </div>
        <span class="mobile-nav-label">Menu</span>
    </button>
</nav>

<script>
    function toggleSidebar() {
        if (window.innerWidth <= 1024) {
            document.body.classList.toggle('sidebar-mobile-open');
        } else {
            document.body.classList.toggle('sidebar-collapsed');
            if (document.body.classList.contains('sidebar-collapsed')) {
                localStorage.setItem('sidebarState', 'collapsed');
            } else {
                localStorage.setItem('sidebarState', 'expanded');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.innerWidth > 1024 && localStorage.getItem('sidebarState') === 'collapsed') {
            document.body.classList.add('sidebar-collapsed');
        }

        // Auto-close mobile drawer when window resized to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 1024) {
                document.body.classList.remove('sidebar-mobile-open');
            }
        });
    });
</script>

<div class="main-wrapper">
    <header class="top-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="header-toggle-btn" onclick="toggleSidebar()" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-title">
                <h1><?php echo htmlspecialchars($page_title ?? 'Amazon Intelligence Dashboard'); ?></h1>
                <span><?php echo htmlspecialchars($page_subtitle ?? 'Performance tracking & financial insights'); ?></span>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="text-align: right;" class="d-none d-sm-block">
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