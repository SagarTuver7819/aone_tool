<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Campaign & Target Performance";
$page_subtitle = "Detailed analysis of campaigns, ad groups, and targeting efficiency";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<!-- Premium Custom Styling for Campaign & Target Performance (Figma Pixel-Perfect) -->
<style>
    body {
        background-color: #F8FAFC !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        color: #1E293B;
    }

    .ct-dashboard-container {
        padding: 0;
        max-width: 100%;
        margin: 0 auto;
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
    .figma-page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .figma-page-head h2 {
        font-family: 'Inter', sans-serif;
        font-size: 1.35rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .figma-page-head p {
        font-size: 0.78rem;
        color: #64748B;
        margin: 2px 0 0 0;
        font-weight: 500;
    }

    .figma-filters-bar {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .figma-date-bar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 0.35rem 0.65rem;
    }

    .figma-date-bar input[type="date"] {
        border: none;
        outline: none;
        font-size: 0.78rem;
        font-weight: 600;
        color: #1E293B;
        background: transparent;
        width: 110px;
    }

    .btn-refresh-icon {
        border: none;
        background: transparent;
        color: #64748B;
        cursor: pointer;
        padding: 2px 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Section Bento Cards */
    .ct-bento-section {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.5rem;
    }

    .ct-bento-head {
        margin-bottom: 1.25rem;
    }

    .ct-bento-head h3 {
        font-family: 'Inter', sans-serif;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .ct-bento-head p {
        font-size: 0.75rem;
        color: #64748B;
        margin: 2px 0 0 0;
        font-weight: 500;
    }

    .ct-columns-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .ct-col-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.85rem;
    }

    .ct-col-title {
        font-size: 0.88rem;
        font-weight: 800;
        color: #0F172A;
    }

    .ct-badge-roas {
        background: #EEF2FF;
        color: #4362CE;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        letter-spacing: 0.04em;
    }

    .ct-badge-budget {
        background: #FEF0EF;
        color: #EE473D;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        letter-spacing: 0.04em;
    }

    /* List Item Cards */
    .ct-perf-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        transition: all 0.15s ease;
    }

    .ct-perf-card:hover {
        border-color: #CBD5E1;
        background: #FAFAFC;
    }

    .ct-perf-left {
        flex: 1;
        min-width: 0;
    }

    .ct-perf-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0F172A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ct-perf-sub {
        font-size: 0.75rem;
        color: #64748B;
        margin-top: 3px;
        font-weight: 500;
    }

    .ct-perf-right {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-shrink: 0;
    }

    /* Figma Roas Badge Box */
    .ct-roas-badge-box {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 5px 14px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        min-width: 76px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .ct-roas-badge-box .label {
        font-size: 0.75rem;
        font-weight: 800;
        color: #0F172A;
        line-height: 1.2;
        margin-bottom: 2px;
    }

    .ct-roas-badge-box .val.up {
        font-size: 0.82rem;
        font-weight: 800;
        color: #029153;
        line-height: 1.2;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    .ct-roas-badge-box .val.down {
        font-size: 0.82rem;
        font-weight: 800;
        color: #EE473D;
        line-height: 1.2;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    /* Spend Box */
    .ct-spend-box {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        min-width: 65px;
    }

    .ct-spend-box .label {
        font-size: 0.72rem;
        font-weight: 500;
        color: #64748B;
        margin-bottom: 2px;
        line-height: 1.2;
    }

    .ct-spend-box .val {
        font-family: 'Inter', sans-serif;
        font-size: 0.98rem;
        font-weight: 800;
        color: #0F172A;
        line-height: 1.2;
    }

    /* ==========================================================================
       Responsive Rules for Campaign & Target Performance
       ========================================================================== */
    @media (max-width: 1440px) {
        .ct-columns-grid {
            grid-template-columns: 1fr !important;
            gap: 1.25rem !important;
        }

        .ct-perf-card {
            padding: 10px 14px;
        }
    }

    @media (min-width: 1441px) {
        .ct-perf-name {
            max-width: 220px;
        }
    }

    .ct-perf-name {
        max-width: 100%;
    }

    @media (max-width: 768px) {
        .ct-dashboard-container {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .figma-page-topbar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
            padding: 0.5rem 0 !important;
        }

        .figma-page-topbar-left {
            width: 100% !important;
        }

        .figma-page-topbar-left .figma-select-wrapper,
        .figma-page-topbar-left select {
            width: 100% !important;
            min-width: 0 !important;
        }

        .figma-page-breadcrumb {
            display: none !important;
        }

        .figma-page-topbar-right {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }

        .figma-page-topbar-right .btn-figma-icon-sm {
            display: none !important;
        }

        .figma-page-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
            margin-bottom: 0.85rem !important;
        }

        .figma-filters-bar {
            width: 100% !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
        }

        .figma-filters-bar .figma-select-wrapper,
        .figma-filters-bar select {
            width: 100% !important;
            min-width: 0 !important;
        }

        .figma-filters-bar .d-flex {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .figma-filters-bar .figma-date-picker-wrap {
            flex: 1 !important;
            min-width: 0 !important;
            width: auto !important;
            padding: 0.45rem 0.65rem !important;
        }

        .figma-filters-bar .figma-date-picker-wrap input.flatpickr-range-input {
            width: 100% !important;
            min-width: 0 !important;
            font-size: 0.76rem !important;
        }

        .ct-bento-section {
            padding: 1rem !important;
            border-radius: 14px !important;
            margin-bottom: 1rem !important;
        }

        .ct-perf-card {
            padding: 8px 12px !important;
            gap: 10px !important;
        }

        .ct-perf-name {
            font-size: 0.82rem !important;
            max-width: 140px !important;
        }

        .ct-perf-right {
            gap: 10px !important;
        }

        .ct-roas-badge-box {
            padding: 4px 8px !important;
            min-width: 60px !important;
        }

        .ct-spend-box {
            min-width: 50px !important;
        }
    }
</style>

<style>
    .top-header {
        display: none !important;
    }

    .main-wrapper {
        padding: 1.25rem 2rem 2rem 2rem !important;
    }

    @media (max-width: 1024px) {
        .main-wrapper {
            padding: 0.75rem 0.75rem 100px 0.75rem !important;
        }
    }
</style>

<div class="ct-dashboard-container">
    <!-- Figma-style Top Bar -->
    <div class="figma-page-topbar">
        <div class="figma-page-topbar-left">
            <div class="figma-select-wrapper">
                <select id="filter_customer">
                    <option value="">All Amazon Profiles</option>
                    <?php
                    $customers = get_all_customers();
                    while ($row = $customers->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['customer_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>
            <span class="figma-page-breadcrumb">Dashboard <span class="breadcrumb-dot">•</span> <strong>Campaign &
                    Target</strong></span>
        </div>
        <div class="figma-page-topbar-right">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-figma-primary"><i
                        class="fas fa-plus"></i> New Upload</a>
            <?php endif; ?>
            <button type="button" class="btn-figma-outline-sm" onclick="exportCSV()"><i class="fas fa-file-export"></i>
                Export CSV</button>
            <button type="button" class="btn-figma-icon-sm" title="Search"><i class="fas fa-search"></i></button>
            <button type="button" class="btn-figma-icon-sm" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge"></span>
            </button>
        </div>
    </div>

    <!-- Page Title & Inline Filters Bar -->
    <div class="figma-page-head">
        <div>
            <h2>Campaign & Target Performance</h2>
            <p>Detailed analysis of campaigns, ad groups, and targeting efficiency</p>
        </div>
        <div class="figma-filters-bar">
            <!-- Brand Name Filter -->
            <div class="figma-select-wrapper">
                <select id="filter_brand">
                    <option value="">Brand Name</option>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>

            <!-- Traffic Type Filter -->
            <div class="figma-select-wrapper">
                <select id="filter_traffic_type">
                    <option value="all">Traffic Type</option>
                    <option value="branded">Branded</option>
                    <option value="non_branded">Non-Branded</option>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>

            <!-- Date Range Bar -->
            <div class="d-flex align-items-center gap-2">
                <div class="figma-date-picker-wrap">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.6666 1.3335V4.00016M5.33325 1.3335V4.00016" stroke="#363B4F" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M8.66667 2.6665H7.33333C4.81917 2.6665 3.5621 2.6665 2.78105 3.44755C2 4.2286 2 5.48568 2 7.99984V9.33317C2 11.8473 2 13.1044 2.78105 13.8854C3.5621 14.6665 4.81917 14.6665 7.33333 14.6665H8.66667C11.1808 14.6665 12.4379 14.6665 13.2189 13.8854C14 13.1044 14 11.8473 14 9.33317V7.99984C14 5.48568 14 4.2286 13.2189 3.44755C12.4379 2.6665 11.1808 2.6665 8.66667 2.6665Z"
                            stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 6.6665H14" stroke="#363B4F" stroke-width="1.4" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <input type="text" class="flatpickr-range-input date-range-picker" id="date_range_picker_ct"
                        placeholder="Select date range" readonly>
                    <input type="hidden" id="filter_from" value="2026-01-01">
                    <input type="hidden" id="filter_to" value="2026-03-31">
                </div>
                <button type="button" class="btn-figma-refresh" id="refresh_campaigns" title="Analyze">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.1115 0.666504L10.5101 1.41169C10.7796 1.91548 10.9143 2.16738 10.8253 2.27526C10.7361 2.38314 10.4427 2.29601 9.85573 2.12176C9.26893 1.94754 8.64593 1.85381 8.00033 1.85381C4.50252 1.85381 1.66699 4.60548 1.66699 7.99987C1.66699 9.11927 1.97541 10.1689 2.51428 11.0729M5.88921 15.3332L5.49057 14.588C5.22105 14.0842 5.08629 13.8323 5.17539 13.7244C5.26451 13.6165 5.55799 13.7037 6.14492 13.8779C6.73173 14.0521 7.35473 14.1459 8.00033 14.1459C11.4981 14.1459 14.3337 11.3942 14.3337 7.99987C14.3337 6.8804 14.0253 5.83082 13.4864 4.92682"
                            stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Section 1: Campaign Performance Overview Card -->
    <div class="ct-bento-section">
        <div class="ct-bento-head">
            <h3>Campaign Performance Overview</h3>
            <p>Top and bottom performing campaigns in this range</p>
        </div>
        <div class="ct-columns-grid">
            <!-- Top 5 Performing Campaigns Column -->
            <div>
                <div class="ct-col-header">
                    <span class="ct-col-title">Top 5 Performing Campaigns</span>
                    <span class="ct-badge-roas">HIGH ROAS</span>
                </div>
                <div id="top-campaigns-body">
                    <div style="text-align: center; padding: 2rem; color: #94A3B8;">Loading campaigns...</div>
                </div>
            </div>

            <!-- Bottom 5 Low-Performing Campaigns Column -->
            <div>
                <div class="ct-col-header">
                    <span class="ct-col-title">Bottom 5 Low-Performing Campaigns</span>
                    <span class="ct-badge-budget">CHECK BUDGET</span>
                </div>
                <div id="bottom-campaigns-body">
                    <div style="text-align: center; padding: 2rem; color: #94A3B8;">Loading campaigns...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Keywords Performance Overview Card -->
    <div class="ct-bento-section">
        <div class="ct-bento-head">
            <h3>Keywords Performance Overview</h3>
            <p>Top and bottom performing keywords in this range</p>
        </div>
        <div class="ct-columns-grid">
            <!-- Top 10 Performing Keywords Column -->
            <div>
                <div class="ct-col-header">
                    <span class="ct-col-title">Top 10 Performing Keywords</span>
                    <span class="ct-badge-roas">HIGH ROAS</span>
                </div>
                <div id="top-keywords-body">
                    <div style="text-align: center; padding: 2rem; color: #94A3B8;">Loading keywords...</div>
                </div>
            </div>

            <!-- Worst 10 Low-Performing Keywords Column -->
            <div>
                <div class="ct-col-header">
                    <span class="ct-col-title">Worst 10 Low-Performing Keywords</span>
                    <span class="ct-badge-budget">CHECK BUDGET</span>
                </div>
                <div id="bottom-keywords-body">
                    <div style="text-align: center; padding: 2rem; color: #94A3B8;">Loading keywords...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Match Type Performance -->
    <div class="ct-bento-section">
        <div class="ct-bento-head">
            <h3>Match Type Performance</h3>
            <p>Compare spend efficiency across Broad, Phrase, and Exact match types</p>
        </div>
        <div class="ct-columns-grid">
            <!-- Sponsored Products (SP) Match Type -->
            <div>
                <div class="ct-col-header"
                    style="flex-direction: column; align-items: flex-start; gap: 2px; margin-bottom: 0.75rem;">
                    <span class="ct-col-title">Sponsored Products (SP) Match Type</span>
                    <span style="font-size: 0.72rem; color: #64748B;">Performance metrics grouped by SP keyword match
                        types</span>
                </div>
                <div id="match-types-sp-body">
                    <div style="text-align: center; padding: 2rem; color: #94A3B8;">Loading match types...</div>
                </div>
            </div>

            <!-- Sponsored Brands (SB) Match Type -->
            <div>
                <div class="ct-col-header"
                    style="flex-direction: column; align-items: flex-start; gap: 2px; margin-bottom: 0.75rem;">
                    <span class="ct-col-title">Sponsored Brands (SB) Match Type</span>
                    <span style="font-size: 0.72rem; color: #64748B;">Performance metrics grouped by SB keyword match
                        types</span>
                </div>
                <div id="match-types-sb-body">
                    <div style="text-align: center; padding: 2rem; color: #94A3B8;">Loading match types...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4: Placement Analysis Report - SP & SB -->
    <div class="ct-bento-section">
        <div class="ct-bento-head">
            <h3>Placement Analysis Report - SP & SB</h3>
            <p>Analyze placement breakdown across Sponsored Products & Sponsored Brands</p>
        </div>
        <div class="ct-columns-grid">
            <!-- Sponsored Products Placement -->
            <div>
                <div class="ct-col-header"
                    style="flex-direction: column; align-items: flex-start; gap: 2px; margin-bottom: 0.75rem;">
                    <span class="ct-col-title">Sponsored Products</span>
                    <span style="font-size: 0.72rem; color: #64748B;">Individual Listing Promotions</span>
                </div>
                <div id="sp-placements-body">
                    <div style="text-align: center; padding: 2rem; color: #94A3B8;">Loading placements...</div>
                </div>
            </div>

            <!-- Sponsored Brands Placement -->
            <div>
                <div class="ct-col-header"
                    style="flex-direction: column; align-items: flex-start; gap: 2px; margin-bottom: 0.75rem;">
                    <span class="ct-col-title">Sponsored Brands</span>
                    <span style="font-size: 0.72rem; color: #64748B;">Brand Store & Headline Ads</span>
                </div>
                <div id="sb-placements-body">
                    <div
                        style="text-align: center; padding: 2.5rem 1rem; color: #94A3B8; font-size: 0.82rem; font-weight: 500;">
                        No SB placements found.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 5: Detailed Breakdown Table -->
    <div class="ct-bento-section">
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0;">Campaign Performance
                    Breakdown</h3>
                <p style="margin: 2px 0 0 0; font-size: 0.75rem; color: #64748B; font-weight: 500;">Cross-channel
                    advertising efficiency metrics</p>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="position: relative;">
                    <i class="fas fa-search"
                        style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.75rem; pointer-events: none;"></i>
                    <input id="campaign_search_input"
                        style="padding-left: 28px; padding-right: 12px; padding-top: 6px; padding-bottom: 6px; border: 1px solid #E2E8F0; border-radius: 8px; outline: none; background: #F8FAFC; font-size: 0.8rem; font-weight: 500; width: 220px;"
                        placeholder="Search files (Name / ID)" type="text" />
                </div>
                <span
                    style="background: #EEF8F1; color: #029153; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">SP,
                    SB, SD ACTIVE</span>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table id="campaignTable" style="width: 100%; border-collapse: collapse; font-size: 0.82rem;">
                <thead style="background: #FFFFFF; border-bottom: 1px solid #E2E8F0;">
                    <tr>
                        <th
                            style="padding: 12px 14px; font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: center; width: 6%;">
                            Rank</th>
                        <th
                            style="padding: 12px 14px; font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: left; width: 34%;">
                            Campaign & Ad Group</th>
                        <th
                            style="padding: 12px 14px; font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: left; width: 14%;">
                            Spend</th>
                        <th
                            style="padding: 12px 14px; font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: left; width: 14%;">
                            Sales</th>
                        <th
                            style="padding: 12px 14px; font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: left; width: 11%;">
                            ACoS</th>
                        <th
                            style="padding: 12px 14px; font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: left; width: 11%;">
                            ROAS</th>
                        <th
                            style="padding: 12px 14px; font-size: 0.72rem; font-weight: 700; color: #64748B; text-transform: uppercase; text-align: center; width: 10%;">
                            Bid Action</th>
                    </tr>
                </thead>
                <tbody id="campaign_body" style="background:#FFFFFF;"></tbody>
            </table>
        </div>
    </div>

    <!-- Section 6: Bidding Strategy Efficiency -->
    <div class="ct-bento-section">
        <div style="margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0;">Bidding Strategy Efficiency
            </h3>
            <p style="margin: 2px 0 0 0; font-size: 0.75rem; color: #64748B; font-weight: 500;">Performance metrics
                analyzed by bid strategy type</p>
        </div>
        <div id="bidding_strategy_body" style="display: flex; flex-direction: column; gap: 10px;">
            <div style="text-align: center; padding: 2rem; color: #94A3B8; font-size: 0.82rem;">Loading bidding strategy
                data...</div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        let matchTypeChart = null;
        function formatCurrency(v) {
            return '$' + parseFloat(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });
        }

        function loadBrands(callback) {
            const customerId = $('#filter_customer').val();
            $.get('../../api/get_brands.php', { customer_id: customerId }, function (brands) {
                let html = '<option value="">All Brands</option>';
                brands.forEach(b => {
                    html += `<option value="${b}">${b}</option>`;
                });
                $('#filter_brand').html(html);
                if (callback) callback();
            });
        }

        function initCampaignDatePicker() {
            if (typeof flatpickr !== 'undefined') {
                flatpickr("#date_range_picker_ct", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "M d, Y",
                    defaultDate: [$('#filter_from').val() || "2026-01-01", $('#filter_to').val() || "2026-03-31"],
                    onChange: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            const from = instance.formatDate(selectedDates[0], "Y-m-d");
                            const to = instance.formatDate(selectedDates[1], "Y-m-d");
                            $('#filter_from').val(from);
                            $('#filter_to').val(to);
                            loadCampaignData();
                        }
                    }
                });
            }
        }

        $.get('../../api/get_data_range.php', function (ranges) {
            if (ranges.ads && ranges.ads.min_date) {
                $('#filter_from').val(ranges.ads.min_date);
                $('#filter_to').val(ranges.ads.max_date);
            }
            initCampaignDatePicker();
            loadBrands(loadCampaignData);
        });

        // Auto-refresh when filters change
        $('#filter_customer').on('change', function () {
            loadBrands();
            loadCampaignData();
        });
        $('#filter_brand, #filter_traffic_type, #filter_from, #filter_to').on('change', loadCampaignData);

        function loadCampaignData() {
            const customerId = $('#filter_customer').val();
            const fromDate = $('#filter_from').val();
            const toDate = $('#filter_to').val();
            const brand = $('#filter_brand').val();
            const trafficType = $('#filter_traffic_type').val();

            $('#refresh_campaigns').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            $('#campaign_body, #placement_body, #bidding_body').css('opacity', '0.5');

            $.get('<?php echo BASE_URL; ?>api/advertising_data.php', {
                customer_id: customerId,
                from_date: fromDate,
                to_date: toDate,
                brand: brand,
                traffic_type: trafficType
            }, function (data) {
                $('#refresh_campaigns').prop('disabled', false).html('<i class="fas fa-sync-alt"></i>');
                $('#campaign_body, #placement_body, #bidding_body').css('opacity', '1');
                // Top/Bottom Campaigns Population
                const allCampaigns = data.campaigns || [];

                // Top 5 Performing
                const topCampaigns = [...allCampaigns]
                    .filter(c => parseFloat(c.spend) > 0)
                    .sort((a, b) => (parseFloat(b.sales) / parseFloat(b.spend)) - (parseFloat(a.sales) / parseFloat(a.spend)))
                    .slice(0, 5);

                let topHtml = '';
                if (topCampaigns.length > 0) {
                    topCampaigns.forEach(c => {
                        const spend = parseFloat(c.spend || 0);
                        const sales = parseFloat(c.sales || 0);
                        const cRoas = spend > 0 ? (sales / spend) : 0;
                        const typeLabel = c.type === 'SP' ? 'Sponsored Products' : (c.type === 'SB' ? 'Sponsored Brands' : 'Sponsored Display');
                        topHtml += `
                        <div class="ct-perf-card">
                            <div class="ct-perf-left">
                                <div class="ct-perf-name" title="${c.campaign_name}">${c.campaign_name}</div>
                                <div class="ct-perf-sub">${typeLabel}</div>
                            </div>
                            <div class="ct-perf-right">
                                <div class="ct-roas-badge-box">
                                    <span class="label">Roas</span>
                                    <span class="val up">${cRoas.toFixed(2)}x ↑</span>
                                </div>
                                <div class="ct-spend-box">
                                    <span class="label">Spend</span>
                                    <span class="val">${formatCurrency(spend)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    topHtml = `<div style="text-align: center; padding: 2rem; color: #94A3B8; font-size: 0.82rem;">No high performing campaigns found in this range.</div>`;
                }
                $('#top-campaigns-body').html(topHtml);

                // Bottom 5 Low Performing
                const bottomCampaigns = [...allCampaigns]
                    .filter(c => parseFloat(c.spend) > 10)
                    .sort((a, b) => {
                        const acosA = parseFloat(a.sales) > 0 ? (parseFloat(a.spend) / parseFloat(a.sales)) : 999;
                        const acosB = parseFloat(b.sales) > 0 ? (parseFloat(b.spend) / parseFloat(b.sales)) : 999;
                        return acosB - acosA;
                    })
                    .slice(0, 5);

                let bottomHtml = '';
                if (bottomCampaigns.length > 0) {
                    bottomCampaigns.forEach(c => {
                        const spend = parseFloat(c.spend || 0);
                        const sales = parseFloat(c.sales || 0);
                        const cAcos = sales > 0 ? (spend / sales * 100) : (spend > 0 ? 100 : 0);
                        const typeLabel = c.type === 'SP' ? 'Sponsored Products' : (c.type === 'SB' ? 'Sponsored Brands' : 'Sponsored Display');
                        bottomHtml += `
                        <div class="ct-perf-card">
                            <div class="ct-perf-left">
                                <div class="ct-perf-name" title="${c.campaign_name}">${c.campaign_name}</div>
                                <div class="ct-perf-sub">${typeLabel}</div>
                            </div>
                            <div class="ct-perf-right">
                                <div class="ct-roas-badge-box">
                                    <span class="label">Roas</span>
                                    <span class="val down">${cAcos.toFixed(1)}% ↓</span>
                                </div>
                                <div class="ct-spend-box">
                                    <span class="label">Spend</span>
                                    <span class="val">${formatCurrency(spend)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    bottomHtml = `<div style="text-align: center; padding: 2rem; color: #94A3B8; font-size: 0.82rem;">No low performing campaigns found.</div>`;
                }
                $('#bottom-campaigns-body').html(bottomHtml);

                // Keywords Performance Overview Population
                const keywords = data.top_keywords || [];

                // Top 10 Performing Keywords: spend > 0, sorted by ROAS desc
                const topKeywordsList = [...keywords]
                    .filter(k => parseFloat(k.spend) > 0)
                    .sort((a, b) => {
                        const roasA = parseFloat(a.spend) > 0 ? (parseFloat(a.sales) / parseFloat(a.spend)) : 0;
                        const roasB = parseFloat(b.spend) > 0 ? (parseFloat(b.sales) / parseFloat(b.spend)) : 0;
                        return roasB - roasA;
                    })
                    .slice(0, 10);

                let topKwHtml = '';
                if (topKeywordsList.length > 0) {
                    topKeywordsList.forEach(k => {
                        const spend = parseFloat(k.spend || 0);
                        const sales = parseFloat(k.sales || 0);
                        const kRoas = spend > 0 ? (sales / spend) : 0;
                        const typeLabel = k.ad_type === 'SP' ? 'Sponsored Products' : 'Sponsored Brands';
                        const matchTypeLabel = k.match_type ? `[${k.match_type.toUpperCase()}]` : '';

                        topKwHtml += `
                        <div class="ct-perf-card">
                            <div class="ct-perf-left">
                                <div class="ct-perf-name" title="${k.keyword}">${k.keyword} <span style="font-size:0.72rem; color:#64748B; font-weight:600;">${matchTypeLabel}</span></div>
                                <div class="ct-perf-sub">${typeLabel}</div>
                            </div>
                            <div class="ct-perf-right">
                                <div class="ct-roas-badge-box">
                                    <span class="label">Roas</span>
                                    <span class="val up">${kRoas.toFixed(2)}x ↑</span>
                                </div>
                                <div class="ct-spend-box">
                                    <span class="label">Spend</span>
                                    <span class="val">${formatCurrency(spend)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    topKwHtml = `<div style="text-align: center; padding: 2rem; color: #94A3B8; font-size: 0.82rem;">No high performing keywords found.</div>`;
                }
                $('#top-keywords-body').html(topKwHtml);

                // Worst 10 Performing Keywords: spend > 0, sorted by ROAS asc (or ACOS desc)
                const worstKeywordsList = [...keywords]
                    .filter(k => parseFloat(k.spend) > 0)
                    .sort((a, b) => {
                        const salesA = parseFloat(a.sales || 0);
                        const salesB = parseFloat(b.sales || 0);
                        const spendA = parseFloat(a.spend || 0);
                        const spendB = parseFloat(b.spend || 0);

                        if (salesA === 0 && salesB === 0) {
                            return spendB - spendA;
                        }
                        if (salesA === 0) return -1;
                        if (salesB === 0) return 1;

                        const roasA = salesA / spendA;
                        const roasB = salesB / spendB;
                        return roasA - roasB;
                    })
                    .slice(0, 10);

                let bottomKwHtml = '';
                if (worstKeywordsList.length > 0) {
                    worstKeywordsList.forEach(k => {
                        const spend = parseFloat(k.spend || 0);
                        const sales = parseFloat(k.sales || 0);
                        const kAcos = sales > 0 ? (spend / sales * 100) : 100.0;
                        const typeLabel = k.ad_type === 'SP' ? 'Sponsored Products' : 'Sponsored Brands';
                        const matchTypeLabel = k.match_type ? `[${k.match_type.toUpperCase()}]` : '';

                        bottomKwHtml += `
                        <div class="ct-perf-card">
                            <div class="ct-perf-left">
                                <div class="ct-perf-name" title="${k.keyword}">${k.keyword} <span style="font-size:0.72rem; color:#64748B; font-weight:600;">${matchTypeLabel}</span></div>
                                <div class="ct-perf-sub">${typeLabel}</div>
                            </div>
                            <div class="ct-perf-right">
                                <div class="ct-roas-badge-box">
                                    <span class="label">Roas</span>
                                    <span class="val down">${kAcos.toFixed(1)}% ↓</span>
                                </div>
                                <div class="ct-spend-box">
                                    <span class="label">Spend</span>
                                    <span class="val">${formatCurrency(spend)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                } else {
                    bottomKwHtml = `<div style="text-align: center; padding: 2rem; color: #94A3B8; font-size: 0.82rem;">No low performing keywords found.</div>`;
                }
                $('#bottom-keywords-body').html(bottomKwHtml);

                // Match Type Performance Card Population (SP & SB)
                function populateMatchTypeCards(targetId, list) {
                    let html = '';
                    if (list && list.length > 0) {
                        list.forEach(m => {
                            const rawName = m.match_type || '-';
                            const displayName = rawName === '-' ? '-' : (rawName.charAt(0).toUpperCase() + rawName.slice(1));
                            const spend = parseFloat(m.spend || 0);
                            const sales = parseFloat(m.sales || 0);
                            const roas = parseFloat(m.roas || 0);
                            const acos = parseFloat(m.acos || 0);

                            html += `
                                <div class="ct-perf-card">
                                    <div class="ct-perf-left">
                                        <div class="ct-perf-name" style="font-size: 0.9rem;">${displayName}</div>
                                    </div>
                                    <div class="ct-perf-right" style="gap: 16px;">
                                        <div class="ct-roas-badge-box">
                                            <span class="label">Roas</span>
                                            <span class="val up">${roas.toFixed(2)}x ↑</span>
                                        </div>
                                        <div class="ct-roas-badge-box">
                                            <span class="label">ACoS</span>
                                            <span class="val down">${acos.toFixed(1)}% ↓</span>
                                        </div>
                                        <div class="ct-spend-box">
                                            <span class="label">Sales</span>
                                            <span class="val">${formatCurrency(sales)}</span>
                                        </div>
                                        <div class="ct-spend-box">
                                            <span class="label">Spend</span>
                                            <span class="val">${formatCurrency(spend)}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = `<div style="text-align: center; padding: 2rem; color: #94A3B8; font-size: 0.82rem;">No match type data found.</div>`;
                    }
                    $(targetId).html(html);
                }

                populateMatchTypeCards('#match-types-sp-body', data.match_types_sp || []);
                populateMatchTypeCards('#match-types-sb-body', data.match_types_sb || []);

                // SP & SB PLACEMENT ANALYSIS REPORT POPULATION
                function getPlacementWeight(name) {
                    const lower = name.toLowerCase();
                    if (lower.includes('top of search')) return 1;
                    if (lower.includes('rest of search')) return 2;
                    if (lower.includes('product pages')) return 3;
                    return 4;
                }

                const placementsSp = (data.placements_sp || [])
                    .filter(p => parseFloat(p.spend) > 0 || parseFloat(p.sales) > 0)
                    .sort((a, b) => getPlacementWeight(a.placement) - getPlacementWeight(b.placement));

                const placementsSb = (data.placements_sb || [])
                    .filter(p => parseFloat(p.spend) > 0 || parseFloat(p.sales) > 0)
                    .sort((a, b) => getPlacementWeight(a.placement) - getPlacementWeight(b.placement));

                function getPlacementDetails(name) {
                    const lower = name.toLowerCase();
                    if (lower.includes('top of search')) {
                        return { label: 'Top of Search' };
                    } else if (lower.includes('rest of search')) {
                        return { label: 'Rest of Search' };
                    } else if (lower.includes('product pages')) {
                        return { label: 'Product Pages' };
                    } else {
                        return { label: 'Other Placements' };
                    }
                }

                // Populate SP placements
                function populatePlacementCards(targetId, list, isSb) {
                    let html = '';
                    if (list && list.length > 0) {
                        let maxRoas = Math.max(...list.map(p => parseFloat(p.spend) > 0 ? (parseFloat(p.sales) / parseFloat(p.spend)) : 0)) || 1;
                        maxRoas = Math.max(maxRoas, 6.0);

                        list.forEach(p => {
                            const spend = parseFloat(p.spend || 0);
                            const sales = parseFloat(p.sales || 0);
                            const roas = spend > 0 ? (sales / spend) : 0;
                            const details = getPlacementDetails(p.placement);
                            const healthPercent = Math.min(100, (roas / maxRoas) * 100);
                            let healthColor = '#4362CE';
                            if (roas < 2.0) healthColor = '#EE473D';
                            else if (roas < 4.0) healthColor = '#F59E0B';

                            let labelDisplay = details.label;
                            if (details.label === 'Top of Search') labelDisplay = 'Top<br>of Search';
                            else if (details.label === 'Rest of Search') labelDisplay = 'Rest<br>of Search';
                            else if (details.label === 'Product Pages') labelDisplay = 'Product<br>Pages';

                            html += `
                                <div class="ct-perf-card">
                                    <div class="ct-perf-left" style="min-width: 80px; max-width: 95px; flex: unset;">
                                        <div class="ct-perf-name" style="font-size: 0.84rem; font-weight: 700; line-height: 1.25; color: #0F172A; white-space: normal;">${labelDisplay}</div>
                                    </div>
                                    <div class="ct-perf-right" style="flex: 1; justify-content: flex-end; gap: 16px;">
                                        <div class="ct-roas-badge-box">
                                            <span class="label">Roas</span>
                                            <span class="val up">${roas.toFixed(2)}x ↑</span>
                                        </div>
                                        <div class="ct-spend-box">
                                            <span class="label">Sales</span>
                                            <span class="val">${formatCurrency(sales)}</span>
                                        </div>
                                        <div class="ct-spend-box">
                                            <span class="label">Spend</span>
                                            <span class="val">${formatCurrency(spend)}</span>
                                        </div>
                                        <div class="ct-spend-box" style="min-width: 55px;">
                                            <span class="label">Health</span>
                                            <div style="width: 48px; height: 5px; background: #E2E8F0; border-radius: 3px; overflow: hidden; margin-top: 4px;">
                                                <div style="width: ${healthPercent}%; height: 100%; background: ${healthColor}; border-radius: 3px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = `<div style="min-height: 220px; height: 100%; display: flex; align-items: center; justify-content: center; background: #FAFAFC; border: 1px solid #EAECEF; border-radius: 12px; color: #94A3B8; font-size: 0.85rem; font-weight: 500;">No ${isSb ? 'SB' : 'SP'} placements found.</div>`;
                    }
                    $(targetId).html(html);
                }

                populatePlacementCards('#sp-placements-body', placementsSp, false);
                populatePlacementCards('#sb-placements-body', placementsSb, true);

                // Campaigns Table
                let html = '';
                const rawCampaigns = data.campaigns || [];
                const activeCampaigns = rawCampaigns
                    .filter(c => parseFloat(c.spend || 0) > 0 || parseInt(c.clicks || 0) > 0 || parseFloat(c.sales || 0) > 0)
                    .reduce((map, c) => {
                        const key = c.campaign_name || `${c.type}-${c.ad_group_name}-${c.targeting}`;
                        const existing = map.get(key);
                        if (!existing) {
                            map.set(key, {
                                ...c,
                                spend: parseFloat(c.spend || 0),
                                sales: parseFloat(c.sales || 0),
                                clicks: parseInt(c.clicks || 0),
                                impressions: parseInt(c.impressions || 0),
                                orders: parseInt(c.orders || 0)
                            });
                        } else {
                            existing.spend += parseFloat(c.spend || 0);
                            existing.sales += parseFloat(c.sales || 0);
                            existing.clicks += parseInt(c.clicks || 0);
                            existing.impressions += parseInt(c.impressions || 0);
                            existing.orders += parseInt(c.orders || 0);
                            existing.ad_group_name = existing.ad_group_name || c.ad_group_name;
                            existing.targeting = existing.targeting || c.targeting;
                            existing.match_type = existing.match_type || c.match_type;
                        }
                        return map;
                    }, new Map());
                const campaigns = Array.from(activeCampaigns.values()).sort((a, b) => parseFloat(b.spend || 0) - parseFloat(a.spend || 0));
                if (campaigns.length > 0) {
                    campaigns.forEach((c, idx) => {
                        const acosVal = parseFloat(c.sales > 0 ? (c.spend / c.sales * 100) : 0);
                        const roasVal = parseFloat(c.spend > 0 ? (c.sales / c.spend) : 0);
                        const spend = parseFloat(c.spend || 0);
                        const sales = parseFloat(c.sales || 0);

                        let bidAction = 'SCALE UP';
                        let bidStyle = 'background: #EEF8F1; color: #029153; border: 1px solid #C3EEDA;';

                        if (sales > 0) {
                            if (acosVal < 15) { bidAction = 'SCALE UP'; bidStyle = 'background: #EEF8F1; color: #029153; border: 1px solid #C3EEDA;'; }
                            else if (acosVal < 25) { bidAction = 'MAINTAIN'; bidStyle = 'background: #EFF6FF; color: #3B82F6; border: 1px solid #BFDBFE;'; }
                            else if (acosVal < 35) { bidAction = 'OPTIMIZE'; bidStyle = 'background: #FFF7ED; color: #F59E0B; border: 1px solid #FDE68A;'; }
                            else { bidAction = 'REDUCE BID'; bidStyle = 'background: #FEF0EF; color: #EE473D; border: 1px solid #FCDAD7;'; }
                        } else if (spend > 10) {
                            bidAction = 'REDUCE BID';
                            bidStyle = 'background: #FEF0EF; color: #EE473D; border: 1px solid #FCDAD7;';
                        } else {
                            bidAction = 'MAINTAIN';
                            bidStyle = 'background: #EFF6FF; color: #3B82F6; border: 1px solid #BFDBFE;';
                        }

                        let rankBadgeHtml = '';
                        if (idx === 0) {
                            rankBadgeHtml = `<span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #3B82F6; color: #FFFFFF; border-radius: 50%; font-size: 11px; font-weight: 700;">1</span>`;
                        } else if (idx === 1) {
                            rankBadgeHtml = `<span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #60A5FA; color: #FFFFFF; border-radius: 50%; font-size: 11px; font-weight: 700;">2</span>`;
                        } else if (idx === 2) {
                            rankBadgeHtml = `<span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #93C5FD; color: #FFFFFF; border-radius: 50%; font-size: 11px; font-weight: 700;">3</span>`;
                        } else {
                            rankBadgeHtml = `<span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #FFFFFF; border: 1.5px solid #E2E8F0; color: #64748B; border-radius: 50%; font-size: 11px; font-weight: 700;">${idx + 1}</span>`;
                        }

                        html += `
                            <tr style="border-bottom: 1px solid #F1F5F9; background: #FFFFFF; transition: background 0.15s ease;">
                                <td style="padding: 12px 14px; text-align: center; vertical-align: middle;">
                                    ${rankBadgeHtml}
                                </td>
                                <td style="padding: 12px 14px; text-align: left; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #0F172A; font-size: 0.85rem; line-height: 1.3;" title="${c.campaign_name}">${c.campaign_name}</div>
                                    <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;" title="${c.ad_group_name || 'N/A'}">
                                        <i class="fas fa-layer-group" style="font-size: 0.65rem; color: #94A3B8; margin-right: 4px;"></i> ${c.ad_group_name || 'N/A'}
                                    </div>
                                </td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0F172A; text-align: left; vertical-align: middle; font-variant-numeric: tabular-nums;">${formatCurrency(spend)}</td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0F172A; text-align: left; vertical-align: middle; font-variant-numeric: tabular-nums;">${formatCurrency(sales)}</td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0F172A; text-align: left; vertical-align: middle; font-variant-numeric: tabular-nums;">${acosVal.toFixed(2)}%</td>
                                <td style="padding: 12px 14px; font-weight: 700; color: #0F172A; text-align: left; vertical-align: middle; font-variant-numeric: tabular-nums;">${roasVal.toFixed(2)}x</td>
                                <td style="padding: 12px 14px; text-align: center; vertical-align: middle;">
                                    <span style="${bidStyle} font-size: 0.7rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; letter-spacing: 0.03em; display: inline-block;">${bidAction}</span>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#campaign_body').html(html || '<tr><td colspan="7" class="text-center" style="padding: 3rem; color: #94a3b8; font-weight: 600;">No targeting data found for the selected criteria.</td></tr>');

                if ($.fn.DataTable.isDataTable('#campaignTable')) {
                    $('#campaignTable').DataTable().destroy();
                }
                const table = $('#campaignTable').DataTable({
                    dom: 'rtip',
                    order: [[2, 'desc']],
                    pageLength: 10
                });
                $('#campaign_search_input').off('keyup').on('keyup', function () {
                    table.search(this.value).draw();
                });

                // Placements
                let p_html = '';
                const totalPlacementSpend = data.placements.reduce((acc, p) => acc + parseFloat(p.spend), 0);
                data.placements.forEach(p => {
                    const acos = p.sales > 0 ? (p.spend / p.sales * 100).toFixed(2) : '0.00';
                    const roas = p.spend > 0 ? (p.sales / p.spend).toFixed(2) : '0.00';
                    const ctr = p.impressions > 0 ? (p.clicks / p.impressions * 100).toFixed(2) : '0.00';
                    const share = totalPlacementSpend > 0 ? (p.spend / totalPlacementSpend * 100).toFixed(1) : '0.0';

                    p_html += `<tr>
                    <td style="font-weight: 800; color: #1e293b;">${p.placement}</td>
                    <td style="text-align: right; font-weight: 700;">${formatCurrency(p.spend)} <span style="font-size: 0.6rem; color: #94a3b8;">(${share}%)</span></td>
                    <td style="text-align: center; font-weight: 800; color: ${acos > 35 ? '#ef4444' : '#10b981'};">${acos}%</td>
                    <td style="text-align: center; font-weight: 800; color: #3b82f6;">${roas}</td>
                    <td style="text-align: center; color: #64748b;">${ctr}%</td>
                </tr>`;
                });
                $('#placement_body').html(p_html || '<tr><td colspan="5" class="text-center">No data</td></tr>');

                // Bidding Strategy Efficiency (Figma Pixel-Perfect Cards)
                let b_html = '';
                const bidList = (data.bidding || []).filter(b => b.bidding_strategy && b.bidding_strategy !== 'Other / Auto' && b.bidding_strategy !== 'N/A');

                const standardOrder = ['Dynamic Bids - Down Only', 'Fixed Bids', 'Dynamic Bids - Up and Down'];
                let processedStrategies = [];

                standardOrder.forEach(stratName => {
                    const cleanTarget = stratName.toLowerCase().replace(/[^a-z]/g, '');
                    const found = bidList.find(b => {
                        const cleanSource = (b.bidding_strategy || '').toLowerCase().replace(/[^a-z]/g, '');
                        return cleanSource.includes(cleanTarget) || cleanTarget.includes(cleanSource);
                    });
                    if (found) {
                        processedStrategies.push({
                            name: stratName,
                            spend: parseFloat(found.spend || 0),
                            sales: parseFloat(found.sales || 0)
                        });
                    } else {
                        processedStrategies.push({
                            name: stratName,
                            spend: 0.00,
                            sales: 0.00
                        });
                    }
                });

                // Include any other dynamic strategies
                bidList.forEach(b => {
                    const cleanSource = (b.bidding_strategy || '').toLowerCase().replace(/[^a-z]/g, '');
                    const isAlready = processedStrategies.some(p => {
                        const cleanP = p.name.toLowerCase().replace(/[^a-z]/g, '');
                        return cleanP.includes(cleanSource) || cleanSource.includes(cleanP);
                    });
                    if (!isAlready) {
                        processedStrategies.push({
                            name: b.bidding_strategy,
                            spend: parseFloat(b.spend || 0),
                            sales: parseFloat(b.sales || 0)
                        });
                    }
                });

                // Sort strategies by spend desc so highest spend comes first
                processedStrategies.sort((a, b) => b.spend - a.spend);

                processedStrategies.forEach((b, idx) => {
                    const spend = b.spend;
                    const sales = b.sales;
                    const roas = spend > 0 ? (sales / spend) : 0;

                    let rankBadgeStyle = 'background: #E2E8F0; color: #475569;';
                    if (idx === 0) rankBadgeStyle = 'background: #2563EB; color: #FFFFFF;';
                    else if (idx === 1) rankBadgeStyle = 'background: #60A5FA; color: #FFFFFF;';
                    else if (idx === 2) rankBadgeStyle = 'background: #CBD5E1; color: #475569;';

                    const isPositive = roas > 0;
                    const roasColor = isPositive ? '#10B981' : '#EE473D';

                    b_html += `
                        <div class="ct-perf-card" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; gap: 20px; margin: 0; transition: all 0.15s ease;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <span style="width: 28px; height: 28px; border-radius: 50%; ${rankBadgeStyle} display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0;">${idx + 1}</span>
                                <div>
                                    <div style="font-size: 0.72rem; color: #64748B; font-weight: 500;">Strategy</div>
                                    <div style="font-size: 0.92rem; font-weight: 700; color: #0F172A; margin-top: 1px;">${b.name}</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 36px;">
                                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; padding: 6px 16px; min-width: 80px; text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                    <span style="display: block; font-size: 0.7rem; color: #64748B; font-weight: 600;">Roas</span>
                                    <span style="display: block; font-size: 0.82rem; font-weight: 700; color: ${roasColor}; font-variant-numeric: tabular-nums;">${roas.toFixed(2)}x ↑</span>
                                </div>
                                <div style="min-width: 95px; text-align: right;">
                                    <span style="display: block; font-size: 0.7rem; color: #64748B; font-weight: 600;">Spend</span>
                                    <span style="display: block; font-size: 0.95rem; font-weight: 800; color: #0F172A; font-variant-numeric: tabular-nums;">${formatCurrency(spend)}</span>
                                </div>
                                <div style="min-width: 95px; text-align: right;">
                                    <span style="display: block; font-size: 0.7rem; color: #64748B; font-weight: 600;">Sales</span>
                                    <span style="display: block; font-size: 0.95rem; font-weight: 800; color: #0F172A; font-variant-numeric: tabular-nums;">${formatCurrency(sales)}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#bidding_strategy_body').html(b_html || '<div style="text-align: center; padding: 2rem; color: #94A3B8; font-size: 0.82rem;">No bidding strategy data found.</div>');
                $('#bidding_body').html(b_html);
            }).fail(function () {
                $('#refresh_campaigns').prop('disabled', false).html('<i class="fas fa-sync-alt"></i>');
                $('#campaign_body').html('<tr><td colspan="8" class="text-center text-danger py-5">Error loading data.</td></tr>');
            });
        }

        $('#refresh_campaigns').click(loadCampaignData);
    });
</script>

<style>
    /* Figma campaignTable DataTables styling */
    #campaignTable {
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        width: 100% !important;
        table-layout: auto !important;
    }

    #campaignTable th {
        background: #FFFFFF !important;
        border-bottom: 1px solid #E2E8F0 !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        color: #64748B !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 12px 16px !important;
        vertical-align: middle !important;
        border-top: none !important;
    }

    #campaignTable th.sorting::after,
    #campaignTable th.sorting_asc::after,
    #campaignTable th.sorting_desc::after,
    #campaignTable th.sorting::before,
    #campaignTable th.sorting_asc::before,
    #campaignTable th.sorting_desc::before {
        display: none !important;
    }

    #campaignTable td {
        border-bottom: 1px solid #F1F5F9 !important;
        padding: 12px 16px !important;
        vertical-align: middle !important;
    }

    #campaignTable th:nth-child(1),
    #campaignTable td:nth-child(1) {
        width: 50px !important;
        text-align: center !important;
        padding: 12px 8px !important;
    }

    #campaignTable th:nth-child(2),
    #campaignTable td:nth-child(2) {
        text-align: left !important;
        padding: 12px 16px !important;
    }

    #campaignTable td:nth-child(2) * {
        text-align: left !important;
    }

    #campaignTable th:nth-child(3),
    #campaignTable td:nth-child(3),
    #campaignTable th:nth-child(4),
    #campaignTable td:nth-child(4),
    #campaignTable th:nth-child(5),
    #campaignTable td:nth-child(5),
    #campaignTable th:nth-child(6),
    #campaignTable td:nth-child(6) {
        text-align: left !important;
        padding: 12px 16px !important;
    }

    #campaignTable th:nth-child(7),
    #campaignTable td:nth-child(7) {
        text-align: center !important;
        padding: 12px 16px !important;
    }

    #campaignTable tr:hover td {
        background: #F8FAFC !important;
    }

    /* Datatable control styles */
    .dataTables_wrapper {
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    #campaignTable_wrapper>.row:first-child,
    #campaignTable_wrapper>div:first-child,
    .dataTables_wrapper>.row:first-child,
    .dataTables_wrapper>div:first-child {
        display: none !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
    }

    #campaignTable {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    .dataTables_wrapper>.row:last-child {
        background: transparent !important;
        border-top: 1px solid #F1F5F9 !important;
        padding: 14px 8px 4px 8px !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
    }

    .dataTables_wrapper>.row:last-child>div {
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        flex: 1 !important;
        display: flex !important;
        align-items: center !important;
    }

    .dataTables_wrapper>.row:last-child>div:first-child {
        justify-content: flex-start !important;
        max-width: 40% !important;
    }

    .dataTables_wrapper>.row:last-child>div:last-child {
        justify-content: flex-end !important;
        max-width: 60% !important;
    }

    .dataTables_wrapper .dataTables_info {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        font-size: 0.78rem !important;
        font-weight: 500 !important;
        color: #64748B !important;
        width: auto !important;
        float: none !important;
    }

    .dataTables_wrapper .dataTables_paginate {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: auto !important;
        float: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 4px !important;
    }

    /* Style datatable pagination buttons */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 10px !important;
        margin: 0 !important;
        border-radius: 6px !important;
        border: 1px solid #E2E8F0 !important;
        background: #FFFFFF !important;
        color: #64748B !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        font-size: 0.78rem !important;
        transition: all 0.15s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 28px !important;
        height: 28px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #4362CE !important;
        color: #FFFFFF !important;
        border-color: #4362CE !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #F8FAFC !important;
        color: #0F172A !important;
        border-color: #CBD5E1 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
        background: #FFFFFF !important;
        color: #94A3B8 !important;
        border-color: #E2E8F0 !important;
    }
</style>

<?php include '../../includes/footer.php'; ?>