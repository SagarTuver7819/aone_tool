<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Advertising Overview";
$page_subtitle = "Sponsored Products, Brands & Display Analytics";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<!-- Premium Custom Styling for Advertising Overview Dashboard (Figma Pixel-Perfect) -->
<style>
    body {
        background-color: #F8FAFC !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        color: #1E293B;
    }

    .ad-dashboard-container {
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
        min-width: 190px;
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

    .figma-date-bar {
        display: flex;
        align-items: center;
        gap: 8px;
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

    /* 4 Top KPI Cards */
    .ad-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .ad-kpi-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 16px;
        padding: 1.25rem 1.4rem 1rem 1.4rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }

    .ad-kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .ad-kpi-title {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748B;
    }

    .ad-kpi-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #EEF2FF;
        color: #4362CE;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    .ad-kpi-value {
        font-family: 'Inter', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        color: #0F172A;
        line-height: 1;
        margin: 0.25rem 0 0.5rem 0;
    }

    .ad-kpi-trend {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
    }

    .ad-kpi-badge {
        padding: 2px 6px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    .ad-kpi-badge.up {
        background: #EEF8F1;
        color: #029153;
    }

    .ad-kpi-badge.down {
        background: #FEF0EF;
        color: #EE473D;
    }

    .ad-kpi-sub {
        color: #64748B;
        font-weight: 500;
    }

    .ad-kpi-sparkline {
        height: 38px;
        width: 100%;
        margin-top: 0.75rem;
    }

    /* Main 2-Column Grid */
    .ad-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 20px;
        align-items: start;
    }

    .ad-left-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
    }

    .ad-right-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 350px;
        min-width: 350px;
    }

    .ad-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        width: 100%;
    }

    .ad-section-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #0F172A !important;
        margin: 0 !important;
        line-height: 100% !important;
    }

    .ad-card {
        background: #FFFFFF;
        border: 1px solid #E8EAF2;
        border-radius: 14px;
        padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        box-sizing: border-box;
    }

    /* Left Chart Cards Heights */
    .ad-chart-card-1 {
        min-height: 381.5px;
        border-radius: 14px;
        border: 1px solid #E8EAF2;
        padding: 20px 24px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .ad-chart-card-2 {
        min-height: 389.5px;
        border-radius: 14px;
        border: 1px solid #E8EAF2;
        padding: 20px 24px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Right Ad Type Performance Card */
    .ad-type-card {
        border-radius: 14px;
        border: 1px solid #E8EAF2;
        background: #FFFFFF;
        padding: 20px 20px !important;
        box-sizing: border-box;
    }

    .ad-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        width: 100%;
    }

    .ad-card-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #0F172A !important;
        margin: 0;
        line-height: 100% !important;
    }

    .ad-card-sub {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px !important;
        color: #64748B !important;
        margin: 3px 0 0 0 !important;
        font-weight: 400 !important;
        line-height: 100% !important;
    }

    /* Ad Type Performance Sections */
    .ad-type-sec {
        padding-bottom: 20px !important;
        margin-bottom: 20px !important;
        border-bottom: 1px solid #F1F3F6;
    }

    .ad-type-sec:last-child {
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
        border-bottom: none;
    }

    .ad-type-head {
        margin-bottom: 20px !important;
    }

    .ad-type-head-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #1A1A1A !important;
        margin: 0;
        line-height: 100% !important;
    }

    .ad-type-head-sub {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px !important;
        color: #64748B !important;
        margin: 2px 0 0 0 !important;
        font-weight: 400 !important;
        line-height: 100% !important;
    }

    .ad-type-2x2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px !important;
    }

    .ad-mini-box {
        background: #F8FAFC;
        border: 1px solid #F1F5F9;
        border-radius: 10px;
        height: 68px !important;
        min-height: 68px !important;
        padding: 12px 14px !important;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .ad-mini-box .label {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        color: #64748B !important;
        margin-bottom: 10px !important;
        line-height: 100% !important;
    }

    .ad-mini-box .val {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #0F172A !important;
        margin: 0 !important;
        line-height: 18px !important;
        font-variant-numeric: tabular-nums !important;
    }

    .ad-mini-box .val.blue {
        color: #4362CE !important;
    }

    .ad-mini-box .val.red {
        color: #EE473D !important;
    }

    /* 3-Month Comparison Table */
    .kpi-trend-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }

    .kpi-trend-table th {
        padding: 10px 12px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748B;
        border-bottom: 1px solid #F1F5F9;
        text-align: right;
    }

    .kpi-trend-table th:first-child {
        text-align: left;
    }

    .kpi-trend-table td {
        padding: 12px;
        border-bottom: 1px solid #F8FAFC;
        text-align: right;
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        color: #0F172A;
    }

    .kpi-trend-table td:first-child {
        text-align: left;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: #475569;
    }

    /* Pill Filter Toggle (Figma Pixel-Perfect Pill with Active Arrow) */
    .ad-pill-group {
        display: inline-flex;
        align-items: center;
        background: #F1F4F9 !important;
        padding: 4px 6px;
        border-radius: 999px;
        gap: 0.25rem;
        box-shadow: none !important;
        border: none !important;
        overflow: visible !important;
    }

    /* ==========================================================================
       Responsive Rules for Advertising Overview
       ========================================================================== */
    @media (max-width: 1400px) {
        .ad-main-grid {
            grid-template-columns: 1fr !important;
            gap: 1.25rem !important;
        }

        .ad-kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 1rem !important;
        }

        .ad-right-col {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            min-width: 0 !important;
            gap: 1.25rem !important;
        }

        .ad-chart-card-1,
        .ad-chart-card-2 {
            min-height: 0 !important;
            height: auto !important;
        }
    }

    @media (max-width: 992px) {
        .ad-right-col {
            width: 100% !important;
        }
    }

    @media (max-width: 768px) {
        .ad-dashboard-container {
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
            box-sizing: border-box !important;
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

        .figma-page-head>.d-flex {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .figma-page-head .figma-date-picker-wrap {
            flex: 1 !important;
            min-width: 0 !important;
            width: auto !important;
            padding: 0.45rem 0.65rem !important;
        }

        .figma-page-head .figma-date-picker-wrap input.flatpickr-range-input {
            width: 100% !important;
            min-width: 0 !important;
            font-size: 0.76rem !important;
        }

        .ad-kpi-grid {
            grid-template-columns: 1fr !important;
            gap: 0.65rem !important;
            width: 100% !important;
        }

        .ad-kpi-card {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .ad-main-grid {
            width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }

        .ad-left-col,
        .ad-right-col {
            width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 1rem !important;
        }

        .ad-card {
            padding: 1rem !important;
            border-radius: 14px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }

        .ad-card-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.65rem !important;
        }

        .ad-card-head .ad-pill-group {
            width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            border-radius: 999px !important;
            padding: 3px 4px !important;
        }

        .ad-card-head .ad-pill-group::-webkit-scrollbar {
            display: none !important;
        }

        .ad-pill-btn {
            flex: 1 0 auto !important;
            text-align: center !important;
            padding: 5px 12px !important;
            font-size: 0.75rem !important;
            border-radius: 999px !important;
        }

        .ad-pill-btn.active::after {
            display: none !important;
        }

        .kpi-trend-table-wrap {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .kpi-trend-table {
            min-width: 440px !important;
        }

        .ad-card canvas {
            max-width: 100% !important;
            width: 100% !important;
        }
    }

    .ad-pill-btn {
        border: none;
        background: transparent;
        color: #475569;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .ad-pill-btn:hover {
        color: #0f172a;
    }

    .ad-pill-btn.active {
        background: #4362CE !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        padding: 5px 16px !important;
        border-radius: 999px !important;
        box-shadow: 0 4px 10px rgba(67, 98, 206, 0.25) !important;
        position: relative !important;
    }

    .ad-pill-btn.active::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid #4362CE;
        display: block !important;
    }
</style>

<style>
    .top-header {
        display: none !important;
    }

    .main-wrapper {
        padding-top: 1.25rem !important;
    }

    @media (max-width: 1024px) {
        .main-wrapper {
            padding: 0.75rem 0.75rem 100px 0.75rem !important;
            margin-left: 0 !important;
        }

        .ad-dashboard-container {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .ad-kpi-grid {
            grid-template-columns: 1fr !important;
            gap: 0.75rem !important;
        }

        .ad-card {
            padding: 1rem !important;
            border-radius: 14px !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .ad-card-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.65rem !important;
        }

        .ad-card-head .ad-pill-group {
            width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            border-radius: 999px !important;
            padding: 3px 4px !important;
        }

        .ad-card-head .ad-pill-group::-webkit-scrollbar {
            display: none !important;
        }

        .ad-pill-btn {
            flex: 1 0 auto !important;
            text-align: center !important;
            padding: 5px 12px !important;
            font-size: 0.75rem !important;
            border-radius: 999px !important;
        }

        .ad-pill-btn.active::after {
            display: none !important;
        }

        .kpi-trend-table-wrap {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .kpi-trend-table {
            min-width: 440px !important;
        }
    }
</style>
<div class="ad-dashboard-container">
    <!-- Figma-style Top Bar -->
    <div class="figma-page-topbar">
        <div class="figma-page-topbar-left">
            <div class="figma-select-wrapper">
                <select id="filter_customer">
                    <option value="">All Amazon Profiles</option>
                    <?php
                    $customers = get_all_customers();
                    while ($row = $customers->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['customer_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>
            <span class="figma-page-breadcrumb">Dashboard <span class="breadcrumb-dot">•</span> <strong>Advertising
                    Overview</strong></span>
        </div>
        <div class="figma-page-topbar-right">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-figma-primary"><i
                        class="fas fa-plus"></i> New Upload</a>
            <?php endif; ?>
            <button type="button" class="btn-figma-outline-sm" id="export_ads_csv"><i class="fas fa-file-export"></i>
                Export CSV</button>
            <button type="button" class="btn-figma-icon-sm" title="Search"><i class="fas fa-search"></i></button>
            <button type="button" class="btn-figma-icon-sm" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge"></span>
            </button>
        </div>
    </div>

    <!-- Page Title & Date Range -->
    <div class="figma-page-head">
        <div>
            <h2>Advertising Overview</h2>
            <p>Sponsored Products, Brands & Display Analytics</p>
        </div>
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
                <input type="text" class="flatpickr-range-input date-range-picker" id="date_range_picker_ads"
                    placeholder="Select date range" readonly>
                <input type="hidden" id="filter_from" value="2026-01-01">
                <input type="hidden" id="filter_to" value="2026-03-31">
            </div>
            <button type="button" class="btn-figma-refresh" id="refresh_ads" title="Refresh">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10.1115 0.666504L10.5101 1.41169C10.7796 1.91548 10.9143 2.16738 10.8253 2.27526C10.7361 2.38314 10.4427 2.29601 9.85573 2.12176C9.26893 1.94754 8.64593 1.85381 8.00033 1.85381C4.50252 1.85381 1.66699 4.60548 1.66699 7.99987C1.66699 9.11927 1.97541 10.1689 2.51428 11.0729M5.88921 15.3332L5.49057 14.588C5.22105 14.0842 5.08629 13.8323 5.17539 13.7244C5.26451 13.6165 5.55799 13.7037 6.14492 13.8779C6.73173 14.0521 7.35473 14.1459 8.00033 14.1459C11.4981 14.1459 14.3337 11.3942 14.3337 7.99987C14.3337 6.8804 14.0253 5.83082 13.4864 4.92682"
                        stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Top 4 KPI Bento Cards -->
    <div class="ad-kpi-grid">
        <!-- Card 1: TOTAL SALES -->
        <div class="ad-kpi-card">
            <div class="ad-kpi-header">
                <span class="ad-kpi-title">Total Sales</span>
                <div class="ad-kpi-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="ad-kpi-value" id="sales-value">$0.00</div>
            <div class="ad-kpi-trend" id="sales-trend-container">
                <span class="ad-kpi-badge up" id="sales-trend">89.1% ↑</span>
                <span class="ad-kpi-sub">vs previous period</span>
            </div>
            <div class="ad-kpi-sparkline" id="sales-sparkline">
                <svg viewBox="0 0 250 38" width="100%" height="100%" preserveAspectRatio="none">
                    <path id="sales-sparkpath" d="M 0 28 Q 125 28 250 28" fill="none" stroke="#4362CE" stroke-width="2"
                        stroke-linecap="round"></path>
                    <path id="sales-sparkfill" d="M 0 28 Q 125 28 250 28 L 250 38 L 0 38 Z"
                        fill="rgba(67, 98, 206, 0.08)"></path>
                </svg>
            </div>
        </div>

        <!-- Card 2: TOTAL SPEND -->
        <div class="ad-kpi-card">
            <div class="ad-kpi-header">
                <span class="ad-kpi-title">Total Spend</span>
                <div class="ad-kpi-icon">
                    <i class="fas fa-leaf"></i>
                </div>
            </div>
            <div class="ad-kpi-value" id="spend-value">$0.00</div>
            <div class="ad-kpi-trend" id="spend-trend-container">
                <span class="ad-kpi-badge up" id="spend-trend">66.1% ↑</span>
                <span class="ad-kpi-sub">vs previous period</span>
            </div>
            <div class="ad-kpi-sparkline" id="spend-sparkline">
                <svg viewBox="0 0 250 38" width="100%" height="100%" preserveAspectRatio="none">
                    <path id="spend-sparkpath" d="M 0 28 Q 125 28 250 28" fill="none" stroke="#64748B" stroke-width="2"
                        stroke-linecap="round"></path>
                    <path id="spend-sparkfill" d="M 0 28 Q 125 28 250 28 L 250 38 L 0 38 Z"
                        fill="rgba(100, 116, 139, 0.08)"></path>
                </svg>
            </div>
        </div>

        <!-- Card 3: TACOS -->
        <div class="ad-kpi-card">
            <div class="ad-kpi-header">
                <span class="ad-kpi-title">Tacos</span>
                <div class="ad-kpi-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
            </div>
            <div class="ad-kpi-value" id="tacos-value">0.00%</div>
            <div class="ad-kpi-trend" id="tacos-trend-container">
                <span class="ad-kpi-badge up" id="tacos-trend">3.3% ↓</span>
                <span class="ad-kpi-sub">vs previous period</span>
            </div>
            <div class="ad-kpi-sparkline" id="tacos-sparkline">
                <svg viewBox="0 0 250 38" width="100%" height="100%" preserveAspectRatio="none">
                    <path id="tacos-sparkpath" d="M 0 28 Q 125 28 250 28" fill="none" stroke="#4362CE" stroke-width="2"
                        stroke-linecap="round"></path>
                    <path id="tacos-sparkfill" d="M 0 28 Q 125 28 250 28 L 250 38 L 0 38 Z"
                        fill="rgba(67, 98, 206, 0.08)"></path>
                </svg>
            </div>
        </div>

        <!-- Card 4: ROAS -->
        <div class="ad-kpi-card">
            <div class="ad-kpi-header">
                <span class="ad-kpi-title">Roas</span>
                <div class="ad-kpi-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="ad-kpi-value" id="roas-value">0.00x</div>
            <div class="ad-kpi-trend" id="roas-trend-container">
                <span class="ad-kpi-badge up" id="roas-trend">0.51x ↑</span>
                <span class="ad-kpi-sub">vs previous period</span>
            </div>
            <div class="ad-kpi-sparkline" id="roas-sparkline">
                <svg viewBox="0 0 250 38" width="100%" height="100%" preserveAspectRatio="none">
                    <path id="roas-sparkpath" d="M 0 28 Q 125 28 250 28" fill="none" stroke="#4362CE" stroke-width="2"
                        stroke-linecap="round"></path>
                    <path id="roas-sparkfill" d="M 0 28 Q 125 28 250 28 L 250 38 L 0 38 Z"
                        fill="rgba(67, 98, 206, 0.08)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Grid (Figma) -->
    <div class="ad-main-grid">
        <!-- LEFT COLUMN: Charts & Heatmap -->
        <div class="ad-left-col">
            <!-- 1. Total Ad Spend vs Total Sales Line Chart Card -->
            <div class="ad-card ad-chart-card-1">
                <div class="ad-card-head">
                    <div>
                        <h3 class="ad-card-title">Total Ad Spend vs Total Sales</h3>
                        <p class="ad-card-sub">Comparison of Advertising Spend vs. Total Sales</p>
                    </div>
                </div>
                <div style="height: 295px; position: relative; width: 100%;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- 2. Sales / Ad Spend / ROAS Bar Chart Card -->
            <div class="ad-card ad-chart-card-2">
                <div class="ad-card-head">
                    <div>
                        <h3 class="ad-card-title">Sales / Ad Spend / ROAS Bar Chart</h3>
                        <p class="ad-card-sub">Grouped daily comparison of Sales, Spend, and Return on Ad Spend (ROAS)
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <div
                            style="font-size: 0.72rem; color: #64748B; font-weight: 500; display: flex; align-items: center; gap: 4px;">
                            <span
                                style="width: 6px; height: 6px; background: #10B981; border-radius: 50%; display: inline-block;"></span>
                            <span id="bar-chart-sync-text">Data synced today at 04:04 PM</span>
                        </div>
                        <div class="ad-pill-group" id="bar-chart-metric-toggles">
                            <button type="button" class="ad-pill-btn active" data-metric="all">All Metrics</button>
                            <button type="button" class="ad-pill-btn" data-metric="sales">Sales</button>
                            <button type="button" class="ad-pill-btn" data-metric="spend">Ad Spend</button>
                            <button type="button" class="ad-pill-btn" data-metric="roas">ROAS</button>
                        </div>
                    </div>
                </div>
                <div style="height: 295px; position: relative; width: 100%;">
                    <canvas id="salesSpendRoasBarChart"></canvas>
                </div>
            </div>

            <!-- 3. Spends vs Sales Heatmap Card -->
            <div class="ad-card">
                <div class="ad-card-head">
                    <div>
                        <h3 class="ad-card-title">Spends vs Sales Heatmap</h3>
                        <p class="ad-card-sub">Spends vs Sales intensity by Day of Week vs. Hour of Day</p>
                    </div>
                </div>
                <div style="overflow-x: auto; width: 100%;">
                    <div
                        style="min-width: 760px; display: grid; grid-template-columns: 50px repeat(12, 1fr); gap: 6px; align-items: center;">
                        <!-- Column Headers (Hours) -->
                        <div></div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">00-02
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">02-04
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">04-06
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">06-08
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">08-10
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">10-12
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">12-14
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">14-16
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">16-18
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">18-20
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">20-22
                        </div>
                        <div style="font-size: 0.68rem; font-weight: 700; color: #64748B; text-align: center;">22-24
                        </div>

                        <!-- Dynamic grid rows loaded from JS -->
                        <div id="heatmap-grid-rows" style="display: contents;">
                            <!-- JS populated cells -->
                        </div>
                    </div>
                </div>

                <!-- Heatmap Legend -->
                <div
                    style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 1.25rem;">
                    <span style="font-size: 0.72rem; font-weight: 600; color: #64748B;">Lower Intensity</span>
                    <div style="display: flex; gap: 4px;">
                        <div style="width: 14px; height: 14px; border-radius: 3px; background: #EEF2F6;"></div>
                        <div style="width: 14px; height: 14px; border-radius: 3px; background: #C7D2FE;"></div>
                        <div style="width: 14px; height: 14px; border-radius: 3px; background: #818CF8;"></div>
                        <div style="width: 14px; height: 14px; border-radius: 3px; background: #4362CE;"></div>
                    </div>
                    <span style="font-size: 0.72rem; font-weight: 600; color: #64748B;">Higher Intensity</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Ad Type Performance & KPI Trend Table -->
        <div class="ad-right-col">
            <!-- 1. Ad Type Performance Section Header (Above Card) -->
            <div class="ad-section-head">
                <h3 class="ad-section-title">Ad Type Performance</h3>
                <div class="figma-select-wrapper" style="position: relative;">
                    <select
                        style="min-width: 85px !important; width: auto !important; padding: 4px 22px 4px 10px !important; font-size: 0.75rem !important; border-radius: 6px !important; background: #fff !important; border: 1px solid #E2E8F0 !important; font-weight: 500 !important; height: 28px !important;">
                        <option>7 Days</option>
                        <option>30 Days</option>
                    </select>
                    <i class="fas fa-chevron-down select-icon" style="font-size: 0.6rem; color: #94A3B8;"></i>
                </div>
            </div>

            <!-- Ad Type Performance Card -->
            <div class="ad-card ad-type-card">

                <!-- Sponsored Products -->
                <div class="ad-type-sec">
                    <div class="ad-type-head">
                        <p class="ad-type-head-title">Sponsored Products</p>
                        <p class="ad-type-head-sub">Primary Traffic Driver</p>
                    </div>
                    <div class="ad-type-2x2">
                        <div class="ad-mini-box">
                            <div class="label">Ad Sales</div>
                            <div class="val" id="sp-sales">$0.00</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Spend</div>
                            <div class="val" id="sp-spend">$0.00</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Acos</div>
                            <div class="val blue" id="sp-acos">0.00%</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Roas</div>
                            <div class="val" id="sp-roas">0.00x</div>
                        </div>
                    </div>
                </div>

                <!-- Sponsored Brands -->
                <div class="ad-type-sec">
                    <div class="ad-type-head">
                        <p class="ad-type-head-title">Sponsored Brands</p>
                        <p class="ad-type-head-sub">Brand Awareness</p>
                    </div>
                    <div class="ad-type-2x2">
                        <div class="ad-mini-box">
                            <div class="label">Ad Sales</div>
                            <div class="val" id="sb-sales">$0.00</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Spend</div>
                            <div class="val" id="sb-spend">$0.00</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Acos</div>
                            <div class="val blue" id="sb-acos">0.00%</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Roas</div>
                            <div class="val" id="sb-roas">0.00x</div>
                        </div>
                    </div>
                </div>

                <!-- Sponsored Display -->
                <div class="ad-type-sec">
                    <div class="ad-type-head">
                        <p class="ad-type-head-title">Sponsored Display</p>
                        <p class="ad-type-head-sub">Remarketing Focus</p>
                    </div>
                    <div class="ad-type-2x2">
                        <div class="ad-mini-box">
                            <div class="label">Ad Sales</div>
                            <div class="val" id="sd-sales">$0.00</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Spend</div>
                            <div class="val" id="sd-spend">$0.00</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Acos</div>
                            <div class="val red" id="sd-acos">0.00%</div>
                        </div>
                        <div class="ad-mini-box">
                            <div class="label">Roas</div>
                            <div class="val" id="sd-roas">0.00x</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. KPI Trend - 3-Month Comparison Card -->
            <div class="ad-card">
                <div class="ad-card-head">
                    <div>
                        <h3 class="ad-card-title">KPI Trend - 3-Month Comparison</h3>
                        <p class="ad-card-sub">Cleaned and integrated table</p>
                    </div>
                </div>
                <div class="kpi-trend-table-wrap"
                    style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%;">
                    <table class="kpi-trend-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Jan 2026</th>
                                <th>Feb 2026</th>
                                <th>Mar 2026</th>
                            </tr>
                        </thead>
                        <tbody id="kpi-3month-body">
                            <tr>
                                <td>
                                    <span
                                        style="width: 22px; height: 22px; border-radius: 6px; background: #EEF2FF; color: #4362CE; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;"><i
                                            class="fas fa-dollar-sign"></i></span>
                                    <span>Sales</span>
                                </td>
                                <td id="kpi3-sales-1">$10,930.27</td>
                                <td id="kpi3-sales-2">$2,725.80</td>
                                <td id="kpi3-sales-3">$6,930.27</td>
                            </tr>
                            <tr>
                                <td>
                                    <span
                                        style="width: 22px; height: 22px; border-radius: 6px; background: #EEF2FF; color: #4362CE; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;"><i
                                            class="fas fa-leaf"></i></span>
                                    <span>Spend</span>
                                </td>
                                <td id="kpi3-spend-1">$10,930.27</td>
                                <td id="kpi3-spend-2">$2,725.80</td>
                                <td id="kpi3-spend-3">$6,930.27</td>
                            </tr>
                            <tr>
                                <td>
                                    <span
                                        style="width: 22px; height: 22px; border-radius: 6px; background: #EEF2FF; color: #4362CE; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;"><i
                                            class="fas fa-bullhorn"></i></span>
                                    <span>Acos</span>
                                </td>
                                <td><span class="ad-kpi-badge up">3.3% ↑</span></td>
                                <td><span class="ad-kpi-badge up">3.3% ↑</span></td>
                                <td>4.01x</td>
                            </tr>
                            <tr>
                                <td>
                                    <span
                                        style="width: 22px; height: 22px; border-radius: 6px; background: #EEF2FF; color: #4362CE; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;"><i
                                            class="fas fa-chart-line"></i></span>
                                    <span>Roas</span>
                                </td>
                                <td><span class="ad-kpi-badge down">0.00% ↓</span></td>
                                <td><span class="ad-kpi-badge up">0.51x ↑</span></td>
                                <td>4.00x</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let trendChart = null;
        let barChart = null;
        let matchTypeChart = null;

        // Daily Report state variables matching SKU pagination style
        let globalDailyTrend = [];
        let dailyTrendData = null;
        let reportCurrentPage = 1;
        const REPORT_ITEMS_PER_PAGE = 10;
        let reportSearchQuery = "";

        // Load initial date ranges
        $.get('../../api/get_data_range.php', function (ranges) {
            if (ranges.ads && ranges.ads.min_date) {
                $('#filter_from').val(ranges.ads.min_date);
                $('#filter_to').val(ranges.ads.max_date);
                loadAdData();
            }
        });

        function formatCurrency(v) {
            return '$' + parseFloat(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatNumber(v) {
            return parseInt(v || 0).toLocaleString();
        }

        // SVG sparkline path generator
        function generateSparklinePath(data, width, height) {
            if (!data || data.length === 0) return `M 0 ${height / 2} L ${width} ${height / 2}`;
            const max = Math.max(...data) || 1;
            const min = Math.min(...data) || 0;
            const range = max - min || 1;

            let points = [];
            const step = width / (data.length - 1 || 1);
            for (let i = 0; i < data.length; i++) {
                const x = i * step;
                const y = height - ((data[i] - min) / range) * (height - 10) - 5;
                points.push({ x, y });
            }

            let path = `M ${points[0].x} ${points[0].y}`;
            for (let i = 0; i < points.length - 1; i++) {
                const p0 = points[i];
                const p1 = points[i + 1];
                const cpX1 = p0.x + step / 2;
                const cpY1 = p0.y;
                const cpX2 = p1.x - step / 2;
                const cpY2 = p1.y;
                path += ` C ${cpX1} ${cpY1}, ${cpX2} ${cpY2}, ${p1.x} ${p1.y}`;
            }
            return path;
        }

        function loadAdData() {
            const customerId = $('#filter_customer').val();
            const fromDate = $('#filter_from').val();
            const toDate = $('#filter_to').val();

            if (!fromDate || !toDate) return;

            $('#refresh_ads').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

            $.get('../../api/advertising_data.php', {
                customer_id: customerId,
                from_date: fromDate,
                to_date: toDate
            }, function (data) {
                $('#refresh_ads').prop('disabled', false).html('<i class="fas fa-sync-alt"></i>');

                // 1. Calculate dynamic Spend and Sales from SP + SB + SD to ensure perfect alignment
                const spSales = parseFloat(data.summary.sp.sales || 0);
                const spSpend = parseFloat(data.summary.sp.spend || 0);
                const sbSales = parseFloat(data.summary.sb.sales || 0);
                const sbSpend = parseFloat(data.summary.sb.spend || 0);
                const sdSales = parseFloat(data.summary.sd.sales || 0);
                const sdSpend = parseFloat(data.summary.sd.spend || 0);

                const totalSpend = spSpend + sbSpend + sdSpend;
                const totalSales = spSales + sbSales + sdSales;
                const tacos = totalSales > 0 ? (totalSpend / totalSales) * 100 : 0;
                const roas = totalSpend > 0 ? (totalSales / totalSpend) : 0;

                $('#sales-value').text(formatCurrency(totalSales));
                $('#spend-value').text(formatCurrency(totalSpend));
                $('#tacos-value').text(tacos.toFixed(2) + '%');
                $('#roas-value').text(roas.toFixed(2) + 'x');

                // Calculate growth trends from prev_summary (dynamic comparison)
                let prevSales = data.prev_summary ? parseFloat(data.prev_summary.total_sales || 0) : 0;
                let prevSpend = data.prev_summary ? parseFloat(data.prev_summary.total_spend || 0) : 0;
                let prevTacos = data.prev_summary ? parseFloat(data.prev_summary.tacos || 0) : 0;
                let prevRoas = data.prev_summary ? parseFloat(data.prev_summary.roas || 0) : 0;

                let compSales = totalSales;
                let compSpend = totalSpend;
                let compTacos = tacos;
                let compRoas = roas;

                // Fallback: If database has no data prior to start date (prev period is 0),
                // split the current period in half to compare second half vs first half.
                if (prevSales === 0 && data.daily_trend && data.daily_trend.sales && data.daily_trend.sales.length > 1) {
                    const len = data.daily_trend.sales.length;
                    const half = Math.floor(len / 2);

                    let sales1 = 0, sales2 = 0;
                    let spend1 = 0, spend2 = 0;

                    for (let i = 0; i < len; i++) {
                        if (i < half) {
                            sales1 += data.daily_trend.sales[i];
                            spend1 += data.daily_trend.spend[i];
                        } else {
                            sales2 += data.daily_trend.sales[i];
                            spend2 += data.daily_trend.spend[i];
                        }
                    }

                    prevSales = sales1;
                    prevSpend = spend1;
                    prevTacos = sales1 > 0 ? (spend1 / sales1) * 100 : 0;
                    prevRoas = spend1 > 0 ? (sales1 / spend1) : 0;

                    compSales = sales2;
                    compSpend = spend2;
                    compTacos = sales2 > 0 ? (spend2 / sales2) * 100 : 0;
                    compRoas = spend2 > 0 ? (sales2 / spend2) : 0;
                }

                // Sales Trend
                const salesGrowth = prevSales > 0 ? ((compSales - prevSales) / prevSales) * 100 : 0;
                if (salesGrowth >= 0) {
                    $('#sales-trend').html(`<i class="fas fa-arrow-up" style="margin-right: 2px;"></i>${salesGrowth.toFixed(1)}%`);
                    $('#sales-trend-container').removeClass('down neutral').addClass('up').css('color', '#10b981');
                } else {
                    $('#sales-trend').html(`<i class="fas fa-arrow-down" style="margin-right: 2px;"></i>${Math.abs(salesGrowth).toFixed(1)}%`);
                    $('#sales-trend-container').removeClass('up neutral').addClass('down').css('color', '#ef4444');
                }
                $('#sales-trend-container .trend-label').text('vs previous period');

                // Spend Trend
                const spendGrowth = prevSpend > 0 ? ((compSpend - prevSpend) / prevSpend) * 100 : 0;
                if (spendGrowth >= 0) {
                    $('#spend-trend').html(`<i class="fas fa-arrow-up" style="margin-right: 2px;"></i>${spendGrowth.toFixed(1)}%`);
                    $('#spend-trend-container').removeClass('down neutral').addClass('up').css('color', '#10b981');
                } else {
                    $('#spend-trend').html(`<i class="fas fa-arrow-down" style="margin-right: 2px;"></i>${Math.abs(spendGrowth).toFixed(1)}%`);
                    $('#spend-trend-container').removeClass('up neutral').addClass('down').css('color', '#ef4444');
                }
                $('#spend-trend-container .trend-label').text('vs previous period');

                // TACOS Trend
                const tacosGrowth = compTacos - prevTacos;
                if (tacosGrowth >= 0) {
                    $('#tacos-trend').html(`<i class="fas fa-arrow-up" style="margin-right: 2px;"></i>${tacosGrowth.toFixed(1)}%`);
                    $('#tacos-trend-container').removeClass('down neutral').addClass('up').css('color', '#10b981');
                    $('#tacos-trend-container .trend-label').text('vs previous period');
                } else {
                    $('#tacos-trend').html(`<i class="fas fa-arrow-down" style="margin-right: 2px;"></i>${Math.abs(tacosGrowth).toFixed(1)}%`);
                    $('#tacos-trend-container').removeClass('up neutral').addClass('down').css('color', '#ef4444');
                    $('#tacos-trend-container .trend-label').text('vs previous period');
                }

                // ROAS Trend
                const roasGrowth = compRoas - prevRoas;
                if (roasGrowth >= 0) {
                    $('#roas-trend').html(`<i class="fas fa-arrow-up" style="margin-right: 2px;"></i>${roasGrowth.toFixed(2)}x`);
                    $('#roas-trend-container').removeClass('down neutral').addClass('up').css('color', '#10b981');
                } else {
                    $('#roas-trend').html(`<i class="fas fa-arrow-down" style="margin-right: 2px;"></i>${Math.abs(roasGrowth).toFixed(2)}x`);
                    $('#roas-trend-container').removeClass('up neutral').addClass('down').css('color', '#ef4444');
                }
                $('#roas-trend-container .trend-label').text('vs previous period');

                // Populate global daily trend for the report table
                globalDailyTrend = data.daily_trend || { labels: [], spend: [], sales: [] };
                dailyTrendData = data.daily_trend;
                renderReportTable();

                // 2. Generate Real SVG Sparklines
                if (data.daily_trend && data.daily_trend.spend && data.daily_trend.spend.length > 1) {
                    // Spend sparkline
                    const spendPath = generateSparklinePath(data.daily_trend.spend, 250, 45);
                    $('#spend-sparkpath').attr('d', spendPath);
                    $('#spend-sparkfill').attr('d', spendPath + ' L 250 45 L 0 45 Z');

                    // Sales sparkline
                    const salesPath = generateSparklinePath(data.daily_trend.sales, 250, 45);
                    $('#sales-sparkpath').attr('d', salesPath);
                    $('#sales-sparkfill').attr('d', salesPath + ' L 250 45 L 0 45 Z');

                    // Tacos sparkline
                    const tacosDaily = data.daily_trend.sales.map((sales, idx) => {
                        const spend = data.daily_trend.spend[idx] || 0;
                        return sales > 0 ? (spend / sales) * 100 : 0;
                    });
                    const tacosPath = generateSparklinePath(tacosDaily, 250, 45);
                    $('#tacos-sparkpath').attr('d', tacosPath);
                    $('#tacos-sparkfill').attr('d', tacosPath + ' L 250 45 L 0 45 Z');

                    // ROAS sparkline
                    const roasDaily = data.daily_trend.spend.map((spend, idx) => {
                        const sales = data.daily_trend.sales[idx] || 0;
                        return spend > 0 ? (sales / spend) : 0;
                    });
                    const roasPath = generateSparklinePath(roasDaily, 250, 45);
                    $('#roas-sparkpath').attr('d', roasPath);
                    $('#roas-sparkfill').attr('d', roasPath + ' L 250 45 L 0 45 Z');
                }

                // 3. Campaigns List
                const campaigns = data.campaigns || [];

                // Top 5 Performing
                const topCampaigns = [...campaigns]
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
                        <tr>
                            <td>
                                <p class="campaign-name" title="${c.campaign_name}">${c.campaign_name}</p>
                                <p class="campaign-sub">${typeLabel}</p>
                            </td>
                            <td class="campaign-spend">${formatCurrency(spend)}</td>
                            <td class="campaign-metric roas">${cRoas.toFixed(2)}x</td>
                        </tr>
                    `;
                    });
                } else {
                    topHtml = `<tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">No high performing campaigns found in this range.</td></tr>`;
                }
                $('#top-campaigns-body').html(topHtml);

                // Bottom 5 Low Performing
                const bottomCampaigns = [...campaigns]
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
                        const cAcos = sales > 0 ? (spend / sales) * 100 : (spend > 0 ? 100 : 0);
                        const typeLabel = c.type === 'SP' ? 'Sponsored Products' : (c.type === 'SB' ? 'Sponsored Brands' : 'Sponsored Display');
                        bottomHtml += `
                        <tr>
                            <td>
                                <p class="campaign-name" title="${c.campaign_name}">${c.campaign_name}</p>
                                <p class="campaign-sub">${typeLabel}</p>
                            </td>
                            <td class="campaign-spend">${formatCurrency(spend)}</td>
                            <td class="campaign-metric acos">${cAcos.toFixed(1)}%</td>
                        </tr>
                    `;
                    });
                } else {
                    bottomHtml = `<tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">No low performing campaigns found.</td></tr>`;
                }
                $('#bottom-campaigns-body').html(bottomHtml);

                // 4. Ad Type Performance Column Values (Fully display dynamic currency with cents)
                $('#sp-sales').text(formatCurrency(spSales));
                $('#sp-spend').text(formatCurrency(spSpend));
                $('#sp-acos').text((spSales > 0 ? (spSpend / spSales * 100) : 0).toFixed(2) + '%');
                $('#sp-roas').text((spSpend > 0 ? (spSales / spSpend) : 0).toFixed(2) + 'x');

                $('#sb-sales').text(formatCurrency(sbSales));
                $('#sb-spend').text(formatCurrency(sbSpend));
                $('#sb-acos').text((sbSales > 0 ? (sbSpend / sbSales * 100) : 0).toFixed(2) + '%');
                $('#sb-roas').text((sbSpend > 0 ? (sbSales / sbSpend) : 0).toFixed(2) + 'x');

                $('#sd-sales').text(formatCurrency(sdSales));
                $('#sd-spend').text(formatCurrency(sdSpend));
                $('#sd-acos').text((sdSales > 0 ? (sdSpend / sdSales * 100) : 0).toFixed(2) + '%');
                $('#sd-roas').text((sdSpend > 0 ? (sdSales / sdSpend) : 0).toFixed(2) + 'x');

                // 5. Render beautiful line/area chart for Total Ad Spend vs Total Sales
                if (data.daily_trend && data.daily_trend.spend) {
                    if (trendChart) trendChart.destroy();

                    const ctx = document.getElementById('trendChart').getContext('2d');

                    // Spend line gradient (Green)
                    const spendGrad = ctx.createLinearGradient(0, 0, 0, 300);
                    spendGrad.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
                    spendGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                    // Sales line gradient (Royal Blue)
                    const salesGrad = ctx.createLinearGradient(0, 0, 0, 300);
                    salesGrad.addColorStop(0, 'rgba(67, 98, 206, 0.15)');
                    salesGrad.addColorStop(1, 'rgba(67, 98, 206, 0.0)');

                    trendChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.daily_trend.labels,
                            datasets: [
                                {
                                    label: 'Total Ad Spend ($)',
                                    data: data.daily_trend.spend,
                                    borderColor: '#10B981',
                                    backgroundColor: spendGrad,
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 2.5,
                                    pointRadius: 0,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: '#10B981',
                                    yAxisID: 'y1'
                                },
                                {
                                    label: 'Total Sales ($)',
                                    data: data.daily_trend.sales,
                                    borderColor: '#4362CE',
                                    backgroundColor: salesGrad,
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 2.5,
                                    pointRadius: 0,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: '#4362CE',
                                    yAxisID: 'y'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    align: 'center',
                                    labels: {
                                        boxWidth: 10,
                                        boxHeight: 10,
                                        usePointStyle: false,
                                        padding: 16,
                                        font: {
                                            family: 'Inter',
                                            weight: '600',
                                            size: 12
                                        },
                                        color: '#64748B'
                                    }
                                },
                                tooltip: {
                                    padding: 12,
                                    backgroundColor: '#0F172A',
                                    cornerRadius: 8,
                                    titleFont: { family: 'Inter', weight: '700' },
                                    bodyFont: { family: 'Inter' },
                                    callbacks: {
                                        label: function (context) {
                                            return context.dataset.label + ': ' + formatCurrency(context.raw);
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    grid: {
                                        color: '#F1F5F9'
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            family: 'Inter',
                                            weight: '600'
                                        },
                                        color: '#4362CE',
                                        callback: function (value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            family: 'Inter',
                                            weight: '600'
                                        },
                                        color: '#10B981',
                                        callback: function (value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    border: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            family: 'Inter',
                                            weight: '600'
                                        },
                                        color: '#64748B',
                                        maxTicksLimit: 8
                                    }
                                }
                            }
                        }
                    });
                }

                // 5.5. Render grouped bar + line combo chart for Sales, Ad Spend, and ROAS
                renderSalesSpendRoasBarChart($('#bar-chart-metric-toggles .ad-pill-btn.active').data('metric') || 'all');

                // 6. Populate Spends vs Sales Heatmap (Day of Week vs Hour of Day)
                const heatmapData = data.heatmap || [];

                const daysOrder = [
                    { dayNum: 2, label: 'Mon' },
                    { dayNum: 3, label: 'Tue' },
                    { dayNum: 4, label: 'Wed' },
                    { dayNum: 5, label: 'Thu' },
                    { dayNum: 6, label: 'Fri' },
                    { dayNum: 7, label: 'Sat' },
                    { dayNum: 1, label: 'Sun' }
                ];

                const hoursLabels = ['00-02', '02-04', '04-06', '06-08', '08-10', '10-12', '12-14', '14-16', '16-18', '18-20', '20-22', '22-24'];
                const hourlyCurve = [0.12, 0.06, 0.08, 0.22, 0.58, 0.88, 1.0, 0.92, 0.78, 0.84, 0.76, 0.38];

                let maxDaySpend = 1;
                daysOrder.forEach(d => {
                    const found = heatmapData.find(item => parseInt(item.day_num) === d.dayNum);
                    if (found) {
                        const spend = parseFloat(found.spend || 0);
                        if (spend > maxDaySpend) maxDaySpend = spend;
                    }
                });

                let heatmapHtml = '';
                daysOrder.forEach(day => {
                    heatmapHtml += `<div style="font-size: 0.72rem; font-weight: 700; color: #64748B; padding-right: 0.25rem;">${day.label}</div>`;

                    const dayItem = heatmapData.find(item => parseInt(item.day_num) === day.dayNum);
                    const daySpend = dayItem ? parseFloat(dayItem.spend || 0) : 0;
                    const daySales = dayItem ? parseFloat(dayItem.sales || 0) : 0;
                    const dayIntensityFactor = maxDaySpend > 0 ? (daySpend / maxDaySpend) : 0;

                    for (let h = 0; h < 12; h++) {
                        const combinedIntensity = dayIntensityFactor * hourlyCurve[h];

                        let bg = '#EEF2F6';
                        if (combinedIntensity > 0.75) {
                            bg = '#4362CE';
                        } else if (combinedIntensity > 0.45) {
                            bg = '#818CF8';
                        } else if (combinedIntensity > 0.15) {
                            bg = '#C7D2FE';
                        } else {
                            bg = '#EEF2F6';
                        }

                        const estimatedHourSpend = daySpend * hourlyCurve[h] * 0.12;
                        const estimatedHourSales = daySales * hourlyCurve[h] * 0.12;
                        const cellTitle = `Day: ${day.label}\nHour: ${hoursLabels[h]}\nEst. Spend: ${formatCurrency(estimatedHourSpend)}\nEst. Sales: ${formatCurrency(estimatedHourSales)}`;

                        heatmapHtml += `<div class="heatmap-cell" data-day="${day.label}" data-hour="${hoursLabels[h]}" data-spend="${formatCurrency(estimatedHourSpend)}" data-sales="${formatCurrency(estimatedHourSales)}" style="height: 20px; border-radius: 4px; background: ${bg}; transition: transform 0.15s; cursor: pointer;" title="${cellTitle}"></div>`;
                    }
                });

                $('#heatmap-grid-rows').html(heatmapHtml);

                // 7.5. Populate Placement Performance Tables
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

                // Icon and clean label mapper for placements
                function getPlacementDetails(name) {
                    const lower = name.toLowerCase();
                    if (lower.includes('top of search')) {
                        return {
                            label: 'Top of Search',
                            icon: '<i class="fas fa-arrow-up" style="color: #2563eb;"></i>'
                        };
                    } else if (lower.includes('rest of search')) {
                        return {
                            label: 'Rest of Search',
                            icon: '<i class="fas fa-search" style="color: #64748b;"></i>'
                        };
                    } else if (lower.includes('product pages')) {
                        return {
                            label: 'Product Pages',
                            icon: '<i class="far fa-file-alt" style="color: #64748b;"></i>'
                        };
                    } else {
                        return {
                            label: 'Other Placements',
                            icon: '<i class="fas fa-cubes" style="color: #64748b;"></i>'
                        };
                    }
                }

                // Populate SP placements
                let spPlHtml = '';
                let totalSpSales = 0;
                let tosSpSales = 0;

                placementsSp.forEach(p => {
                    const spend = parseFloat(p.spend || 0);
                    const sales = parseFloat(p.sales || 0);
                    totalSpSales += sales;
                    if (p.placement.toLowerCase().includes('top of search')) {
                        tosSpSales = sales;
                    }
                });

                // Find maximum ROAS in SP to normalize health bar
                let maxSpRoas = Math.max(...placementsSp.map(p => parseFloat(p.spend) > 0 ? (parseFloat(p.sales) / parseFloat(p.spend)) : 0)) || 1;
                maxSpRoas = Math.max(maxSpRoas, 6.0);

                if (placementsSp.length > 0) {
                    placementsSp.forEach(p => {
                        const spend = parseFloat(p.spend || 0);
                        const sales = parseFloat(p.sales || 0);
                        const roas = spend > 0 ? (sales / spend) : 0;
                        const details = getPlacementDetails(p.placement);

                        const healthPercent = Math.min(100, (roas / maxSpRoas) * 100);
                        let healthColor = '#2563eb'; // High ROAS (Blue)
                        if (roas < 2.0) {
                            healthColor = '#ef4444'; // Red
                        } else if (roas < 4.0) {
                            healthColor = '#64748b'; // Slate
                        }

                        spPlHtml += `
                        <tr style="border-bottom: 1px solid #c6c6cd; background: #ffffff;">
                            <td style="padding: 14px 24px; font-weight: 700; color: #000000; text-align: left; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 16px; width: 20px; display: inline-block; text-align: center;">${details.icon}</span>
                                ${details.label}
                            </td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 600; color: #45464d; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums;">${formatCurrency(spend)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #0051d5; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums; background: rgba(219,225,255,0.05);">${formatCurrency(sales)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #009668; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums; background: rgba(111,251,190,0.02);">${roas.toFixed(2)}x</td>
                            <td style="padding: 14px 24px; text-align: center; vertical-align: middle;">
                                <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin: 0 auto;">
                                    <div style="width: ${healthPercent}%; height: 100%; background: ${healthColor}; border-radius: 3px; transition: width 0.3s ease;"></div>
                                </div>
                            </td>
                        </tr>
                    `;
                    });
                } else {
                    spPlHtml = `<tr><td colspan="5" class="text-center" style="padding:2rem; color:#64748b;">No SP placements found.</td></tr>`;
                }
                $('#sp-placements-body').html(spPlHtml);

                // Update dynamic SP Placement Insight
                if (totalSpSales > 0 && tosSpSales > 0) {
                    const pct = ((tosSpSales / totalSpSales) * 100).toFixed(0);
                    $('#sp-placement-insight').text(`Top of Search generates ${pct}% of SP conversion volume.`);
                } else {
                    $('#sp-placement-insight').text(`Top of Search generates optimal conversion volume for SP.`);
                }

                // Populate SB placements
                let sbPlHtml = '';
                let totalSbSales = 0;
                let tosSbSales = 0;

                placementsSb.forEach(p => {
                    const spend = parseFloat(p.spend || 0);
                    const sales = parseFloat(p.sales || 0);
                    totalSbSales += sales;
                    if (p.placement.toLowerCase().includes('top of search')) {
                        tosSbSales = sales;
                    }
                });

                let maxSbRoas = Math.max(...placementsSb.map(p => parseFloat(p.spend) > 0 ? (parseFloat(p.sales) / parseFloat(p.spend)) : 0)) || 1;
                maxSbRoas = Math.max(maxSbRoas, 6.0);

                if (placementsSb.length > 0) {
                    placementsSb.forEach(p => {
                        const spend = parseFloat(p.spend || 0);
                        const sales = parseFloat(p.sales || 0);
                        const roas = spend > 0 ? (sales / spend) : 0;
                        const details = getPlacementDetails(p.placement);

                        const healthPercent = Math.min(100, (roas / maxSbRoas) * 100);
                        let healthColor = '#2563eb';
                        if (roas < 2.0) {
                            healthColor = '#ef4444';
                        } else if (roas < 4.0) {
                            healthColor = '#64748b';
                        }

                        sbPlHtml += `
                        <tr style="border-bottom: 1px solid #c6c6cd; background: #ffffff;">
                            <td style="padding: 14px 24px; font-weight: 700; color: #000000; text-align: left; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 16px; width: 20px; display: inline-block; text-align: center;">${details.icon}</span>
                                ${details.label}
                            </td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 600; color: #45464d; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums;">${formatCurrency(spend)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #0051d5; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums; background: rgba(219,225,255,0.05);">${formatCurrency(sales)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #009668; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums; background: rgba(111,251,190,0.02);">${roas.toFixed(2)}x</td>
                            <td style="padding: 14px 24px; text-align: center; vertical-align: middle;">
                                <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin: 0 auto;">
                                    <div style="width: ${healthPercent}%; height: 100%; background: ${healthColor}; border-radius: 3px; transition: width 0.3s ease;"></div>
                                </div>
                            </td>
                        </tr>
                    `;
                    });
                } else {
                    sbPlHtml = `<tr><td colspan="5" class="text-center" style="padding:2rem; color:#64748b;">No SB placements found.</td></tr>`;
                }
                $('#sb-placements-body').html(sbPlHtml);

                // Update dynamic SB Placement Insight
                if (totalSbSales > 0 && tosSbSales > 0) {
                    const pct = ((tosSbSales / totalSbSales) * 100).toFixed(0);
                    $('#sb-placement-insight').text(`Top of Search generates ${pct}% of SB sales volume.`);
                } else {
                    $('#sb-placement-insight').text(`Focus budget on Top of Search for optimal brand halo impact.`);
                }

                // 8. Populate Keywords Table
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
                        <tr>
                            <td>
                                <p class="campaign-name" title="${k.keyword}">${k.keyword} <span style="font-size:0.75rem; color:#64748b; font-weight:600;">${matchTypeLabel}</span></p>
                                <p class="campaign-sub">${typeLabel}</p>
                            </td>
                            <td class="campaign-spend">${formatCurrency(spend)}</td>
                            <td class="campaign-metric roas">${kRoas.toFixed(2)}x</td>
                        </tr>
                    `;
                    });
                } else {
                    topKwHtml = `<tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">No high performing keywords found.</td></tr>`;
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

                        // If both have 0 sales, sort by spend descending (highest spend is worst)
                        if (salesA === 0 && salesB === 0) {
                            return spendB - spendA;
                        }
                        // If one has 0 sales, it is worse
                        if (salesA === 0) return -1;
                        if (salesB === 0) return 1;

                        // Otherwise, sort by ROAS ascending (lowest ROAS is worst)
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
                        <tr>
                            <td>
                                <p class="campaign-name" title="${k.keyword}">${k.keyword} <span style="font-size:0.75rem; color:#64748b; font-weight:600;">${matchTypeLabel}</span></p>
                                <p class="campaign-sub">${typeLabel}</p>
                            </td>
                            <td class="campaign-spend">${formatCurrency(spend)}</td>
                            <td class="campaign-metric acos" style="color: #ef4444; font-weight:800;">${kAcos.toFixed(1)}%</td>
                        </tr>
                    `;
                    });
                } else {
                    bottomKwHtml = `<tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">No low performing keywords found.</td></tr>`;
                }
                $('#bottom-keywords-body').html(bottomKwHtml);

                // 9. Populate Match Types Performance Table & Chart
                const matchTypes = data.match_types || [];
                let matchHtml = '';
                let chartLabels = [];
                let chartSpendData = [];
                let chartColors = [];

                const matchColorsMap = {
                    'exact': '#3b82f6',   // Blue
                    'phrase': '#10b981',  // Emerald Green
                    'broad': '#f59e0b',   // Amber Yellow
                    'other': '#64748b'    // Slate Grey
                };

                if (matchTypes.length > 0) {
                    matchTypes.forEach(m => {
                        const name = m.match_type.toLowerCase();
                        const displayName = m.match_type.charAt(0).toUpperCase() + m.match_type.slice(1);
                        const spend = parseFloat(m.spend || 0);
                        const sales = parseFloat(m.sales || 0);
                        const roas = parseFloat(m.roas || 0);
                        const acos = parseFloat(m.acos || 0);

                        matchHtml += `
                        <tr style="border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                            <td style="padding: 12px 16px; font-weight: 700; color: #0f172a; text-align: left; font-size: 13px;">
                                ${displayName}
                            </td>
                            <td style="padding: 12px 12px; font-size: 13px; font-weight: 600; color: #475569; text-align: right; font-family: 'Inter', sans-serif;">${formatCurrency(spend)}</td>
                            <td style="padding: 12px 12px; font-size: 13px; font-weight: 700; color: #0051d5; text-align: right; font-family: 'Inter', sans-serif;">${formatCurrency(sales)}</td>
                            <td style="padding: 12px 12px; font-size: 13px; font-weight: 600; color: #ef4444; text-align: right; font-family: 'Inter', sans-serif;">${acos.toFixed(1)}%</td>
                            <td style="padding: 12px 16px; font-size: 13px; font-weight: 700; color: #009668; text-align: right; font-family: 'Inter', sans-serif;">${roas.toFixed(2)}x</td>
                        </tr>
                    `;

                        chartLabels.push(displayName);
                        chartSpendData.push(spend);
                        chartColors.push(matchColorsMap[name] || matchColorsMap['other']);
                    });
                } else {
                    matchHtml = `<tr><td colspan="5" class="text-center" style="padding:2rem; color:#64748b;">No match type data found.</td></tr>`;
                }
                $('#match-types-body').html(matchHtml);

                // Render/Update Match Type Share Doughnut Chart
                if (matchTypeChart) matchTypeChart.destroy();

                if (chartSpendData.length > 0 && chartSpendData.reduce((a, b) => a + b, 0) > 0) {
                    const doughnutCtx = document.getElementById('matchTypeDoughnutChart').getContext('2d');
                    matchTypeChart = new Chart(doughnutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                data: chartSpendData,
                                backgroundColor: chartColors,
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 12,
                                        font: { family: 'Inter', weight: '600', size: 11 },
                                        color: '#475569'
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const label = context.label || '';
                                            const val = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const pct = ((val / total) * 100).toFixed(1);
                                            return `${label}: ${formatCurrency(val)} (${pct}%)`;
                                        }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                } else {
                    const doughnutCtx = document.getElementById('matchTypeDoughnutChart').getContext('2d');
                    doughnutCtx.clearRect(0, 0, 200, 200);
                }
            });
        }

        function renderReportTable() {
            let filtered = [];
            if (globalDailyTrend && globalDailyTrend.spend) {
                for (let i = 0; i < globalDailyTrend.spend.length; i++) {
                    const dateVal = globalDailyTrend.labels[i];
                    const spendVal = parseFloat(globalDailyTrend.spend[i] || 0);
                    const salesVal = parseFloat(globalDailyTrend.sales[i] || 0);

                    if (!reportSearchQuery || dateVal.toLowerCase().includes(reportSearchQuery)) {
                        filtered.push({
                            date: dateVal,
                            spend: spendVal,
                            sales: salesVal
                        });
                    }
                }
            }

            // Reverse to show most recent first
            filtered.reverse();

            const totalItems = filtered.length;
            const totalPages = Math.ceil(totalItems / REPORT_ITEMS_PER_PAGE) || 1;

            if (reportCurrentPage > totalPages) {
                reportCurrentPage = totalPages;
            }
            if (reportCurrentPage < 1) {
                reportCurrentPage = 1;
            }

            const startIndex = (reportCurrentPage - 1) * REPORT_ITEMS_PER_PAGE;
            const endIndex = Math.min(startIndex + REPORT_ITEMS_PER_PAGE, totalItems);

            let reportHtml = '';
            if (totalItems > 0) {
                for (let i = startIndex; i < endIndex; i++) {
                    const item = filtered[i];
                    const roasVal = item.spend > 0 ? (item.sales / item.spend) : 0;
                    const tacosVal = item.sales > 0 ? (item.spend / item.sales * 100) : 0;

                    reportHtml += `
                    <tr class="report-row" style="border-bottom: 1px solid #c6c6cd; background: #ffffff;">
                        <td style="padding: 16px 32px; font-size: 14px; font-weight: 700; color: #000000; text-align: left;">${item.date}</td>
                        <td style="padding: 16px 24px; font-size: 14px; font-weight: 600; color: #45464d; text-align: right;">${formatCurrency(item.spend)}</td>
                        <td style="padding: 16px 24px; font-size: 14px; font-weight: 700; color: #0051d5; text-align: right; background: rgba(219,225,255,0.05);">${formatCurrency(item.sales)}</td>
                        <td style="padding: 16px 24px; font-size: 14px; font-weight: 700; color: #45464d; text-align: right;">${roasVal.toFixed(2)}x</td>
                        <td style="padding: 16px 32px; font-size: 14px; font-weight: 700; color: #009668; text-align: right; background: rgba(111,251,190,0.02);">${tacosVal.toFixed(2)}%</td>
                    </tr>
                `;
                }
            } else {
                reportHtml = `<tr><td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">No data matching your search query.</td></tr>`;
            }

            $('#report-table-body').html(reportHtml);

            const showingFrom = totalItems > 0 ? startIndex + 1 : 0;
            $('#report_showing_text').text(`Showing ${showingFrom} to ${endIndex} of ${totalItems} entries`);

            // Render Pagination Buttons
            const paginationHtml = renderReportPagination(totalItems, reportCurrentPage, REPORT_ITEMS_PER_PAGE);
            $('#report_pagination').html(paginationHtml);
        }

        function renderReportPagination(totalItems, currentPage, itemsPerPage) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            if (totalPages <= 1) return '';

            let paginationHtml = '';

            // Chevron Left
            const prevDisabled = currentPage === 1 ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : '';
            paginationHtml += `<button class="p-2 rounded border border-outline-variant hover:bg-white transition-colors flex items-center justify-center bg-white" ${prevDisabled} onclick="window.onReportPageClick(${currentPage - 1})" style="padding: 6px 12px; border: 1px solid #c6c6cd; border-radius: 6px; cursor: pointer; background: #ffffff;">
            <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
        </button>`;

            // Page Numbers (Up to 5 page buttons)
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let page = startPage; page <= endPage; page++) {
                if (page === currentPage) {
                    paginationHtml += `<button class="px-3 py-1 rounded text-white" style="background: #0051d5; color: #ffffff; font-weight: 700; border: none; cursor: default; padding: 6px 12px; border-radius: 6px; font-size: 14px;">${page}</button>`;
                } else {
                    paginationHtml += `<button class="px-3 py-1 rounded border border-outline-variant hover:bg-white transition-colors bg-white" style="cursor: pointer; padding: 6px 12px; border: 1px solid #c6c6cd; border-radius: 6px; background: #ffffff; font-size: 14px;" onclick="window.onReportPageClick(${page})">${page}</button>`;
                }
            }

            // Chevron Right
            const nextDisabled = currentPage === totalPages ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : '';
            paginationHtml += `<button class="p-2 rounded border border-outline-variant hover:bg-white transition-colors flex items-center justify-center bg-white" ${nextDisabled} onclick="window.onReportPageClick(${currentPage + 1})" style="padding: 6px 12px; border: 1px solid #c6c6cd; border-radius: 6px; cursor: pointer; background: #ffffff;">
            <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
        </button>`;

            return paginationHtml;
        }

        window.onReportPageClick = function (page) {
            reportCurrentPage = page;
            renderReportTable();
        };

        window.filterReportTable = function () {
            reportSearchQuery = $('#report-search').val().toLowerCase().trim();
            reportCurrentPage = 1;
            renderReportTable();
        };

        window.exportReportToCSV = function () {
            const customerName = $('#filter_customer option:selected').text().trim().replace(/[^a-z0-9]/gi, '_').toLowerCase();
            const fromDate = $('#filter_from').val();
            const toDate = $('#filter_to').val();

            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Date,Ad Spend ($),Total Sales ($),ROAS,TACOS (%)\n";

            let filtered = [];
            if (globalDailyTrend && globalDailyTrend.spend) {
                for (let i = 0; i < globalDailyTrend.spend.length; i++) {
                    const dateVal = globalDailyTrend.labels[i];
                    const spendVal = parseFloat(globalDailyTrend.spend[i] || 0);
                    const salesVal = parseFloat(globalDailyTrend.sales[i] || 0);

                    if (!reportSearchQuery || dateVal.toLowerCase().includes(reportSearchQuery)) {
                        filtered.push({
                            date: dateVal,
                            spend: spendVal,
                            sales: salesVal
                        });
                    }
                }
            }
            filtered.reverse();

            filtered.forEach(item => {
                const roasVal = item.spend > 0 ? (item.sales / item.spend) : 0;
                const tacosVal = item.sales > 0 ? (item.spend / item.sales * 100) : 0;
                csvContent += `"${item.date}",${item.spend},${item.sales},${roasVal.toFixed(2)},${tacosVal.toFixed(2)}\n`;
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `ad_spends_vs_sales_report_${customerName}_${fromDate}_to_${toDate}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        // Metric Toggle Click Handler for Bar Chart
        $('#bar-chart-metric-toggles .ad-pill-btn').click(function () {
            $('#bar-chart-metric-toggles .ad-pill-btn').removeClass('active');
            $(this).addClass('active');

            const metric = $(this).data('metric');
            renderSalesSpendRoasBarChart(metric);
        });

        function renderSalesSpendRoasBarChart(activeMetric = 'all') {
            if (!dailyTrendData || !dailyTrendData.spend) return;
            if (barChart) barChart.destroy();

            const barCtx = document.getElementById('salesSpendRoasBarChart').getContext('2d');

            // ROAS daily trend array
            const roasDaily = dailyTrendData.spend.map((spend, idx) => {
                const sales = dailyTrendData.sales[idx] || 0;
                return spend > 0 ? (sales / spend) : 0;
            });

            // Update last sync time
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            $('#bar-chart-sync-text').text(`Data synced today at ${timeStr}`);

            // Base y axis configuration
            let yAxesConfig = {
                y: {
                    display: true,
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' },
                    border: { display: false },
                    ticks: {
                        font: { family: 'Inter', weight: '600' },
                        color: '#64748B',
                        padding: 8,
                        callback: function (value) { return '$' + value.toLocaleString(); }
                    }
                },
                y1: { display: false }
            };

            let datasets = [];

            if (activeMetric === 'sales') {
                datasets = [{
                    type: 'bar',
                    label: 'Total Sales ($)',
                    data: dailyTrendData.sales,
                    backgroundColor: '#4362CE',
                    borderRadius: 4,
                    yAxisID: 'y'
                }];
            } else if (activeMetric === 'spend') {
                datasets = [{
                    type: 'bar',
                    label: 'Ad Spend ($)',
                    data: dailyTrendData.spend,
                    backgroundColor: '#10B981',
                    borderRadius: 4,
                    yAxisID: 'y'
                }];
            } else if (activeMetric === 'roas') {
                datasets = [{
                    type: 'line',
                    label: 'ROAS (x)',
                    data: roasDaily,
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#F59E0B',
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y'
                }];
                yAxesConfig.y.ticks.callback = function (value) { return value.toFixed(1) + 'x'; };
            } else {
                datasets = [
                    {
                        type: 'bar',
                        label: 'Ad Spend ($)',
                        data: dailyTrendData.spend,
                        backgroundColor: '#10B981',
                        borderRadius: 4,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8,
                        yAxisID: 'y'
                    },
                    {
                        type: 'bar',
                        label: 'Total Sales ($)',
                        data: dailyTrendData.sales,
                        backgroundColor: '#4362CE',
                        borderRadius: 4,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Roas (x)',
                        data: roasDaily,
                        borderColor: '#F59E0B',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        pointRadius: 3,
                        pointBackgroundColor: '#F59E0B',
                        fill: false,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ];
                yAxesConfig.y1 = {
                    display: true,
                    beginAtZero: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    border: { display: false },
                    ticks: {
                        font: { family: 'Inter', weight: '600' },
                        color: '#F59E0B',
                        padding: 8,
                        callback: function (value) { return value.toFixed(1) + 'x'; }
                    }
                };
            }

            barChart = new Chart(barCtx, {
                data: {
                    labels: dailyTrendData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: 'center',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: false,
                                padding: 16,
                                font: { family: 'Inter', weight: '600', size: 12 },
                                color: '#64748B'
                            }
                        },
                        tooltip: {
                            padding: 12,
                            backgroundColor: '#0F172A',
                            cornerRadius: 8,
                            titleFont: { family: 'Inter', weight: '700' },
                            bodyFont: { family: 'Inter' },
                            callbacks: {
                                label: function (context) {
                                    if (context.dataset.label.includes('Roas') || context.dataset.label.includes('ROAS')) {
                                        return ' ' + context.dataset.label + ': ' + parseFloat(context.raw).toFixed(2) + 'x';
                                    }
                                    return ' ' + context.dataset.label + ': ' + formatCurrency(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        y: yAxesConfig.y,
                        y1: yAxesConfig.y1,
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                font: { family: 'Inter', weight: '600' },
                                color: '#64748B',
                                maxTicksLimit: 12,
                                padding: 6
                            }
                        }
                    }
                }
            });
        }

        // Initialize Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#date_range_picker_ads", {
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
                        loadAdData();
                    }
                }
            });
        }

        $('#refresh_ads').click(loadAdData);
        $('#filter_customer').change(loadAdData);

        // Heatmap custom premium tooltip hover handler
        const heatmapTooltip = $('<div id="heatmap-tooltip" style="position: absolute; display: none; background: #0f172a; color: #ffffff; padding: 10px 14px; border-radius: 8px; font-family: \'Inter\', sans-serif; font-size: 0.8rem; z-index: 9999; box-shadow: 0 4px 15px rgba(0,0,0,0.15); pointer-events: none; line-height: 1.4; border: 1px solid rgba(255,255,255,0.1);"></div>').appendTo('body');

        $(document).on('mouseenter', '.heatmap-cell', function (e) {
            const day = $(this).data('day');
            const hour = $(this).data('hour');
            const spend = $(this).data('spend');
            const sales = $(this).data('sales');

            const content = `
            <div style="font-weight: 800; margin-bottom: 4px; color: #94a3b8;">Day: ${day} - Hour: ${hour}</div>
            <div style="display: flex; justify-content: space-between; gap: 15px;">
                <span>Est. Spend:</span>
                <span style="font-weight: 700; color: #cbd5e1;">${spend}</span>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 15px;">
                <span>Est. Sales:</span>
                <span style="font-weight: 700; color: #60a5fa;">${sales}</span>
            </div>
        `;

            heatmapTooltip.html(content).show();
        });

        $(document).on('mousemove', '.heatmap-cell', function (e) {
            const tooltipWidth = heatmapTooltip.outerWidth();
            const tooltipHeight = heatmapTooltip.outerHeight();

            heatmapTooltip.css({
                left: (e.pageX + 15) + 'px',
                top: (e.pageY - tooltipHeight - 15) + 'px'
            });
        });

        $(document).on('mouseleave', '.heatmap-cell', function () {
            heatmapTooltip.hide();
        });
    });
</script>
</div> <!-- Closing the main-wrapper opened in sidebar.php -->
</body>

</html>