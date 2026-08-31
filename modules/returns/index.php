<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = 'Return Page';
$page_subtitle = 'Returns intelligence, reasons & product performance';

$customers = get_all_customers();

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

    .ret-container {
        padding: 0;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Page Header */
    .ret-page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .ret-page-title h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .ret-page-title p {
        font-size: 0.82rem;
        color: #64748B;
        font-weight: 500;
        margin: 3px 0 0 0;
    }

    .ret-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ret-date-picker {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        height: 38px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #0F172A;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .ret-date-picker input[type="date"] {
        border: none;
        outline: none;
        font-size: 0.78rem;
        font-weight: 600;
        color: #0F172A;
        background: transparent;
        font-family: inherit;
        width: 105px;
        cursor: pointer;
    }

    /* Bento Base Card */
    .ret-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        margin-bottom: 1.5rem;
    }

    .ret-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .ret-card-head h3 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
    }

    /* Top KPI Layout (6 Cards Left Grid + Reasons Radial Gauge Right) */
    .ret-kpi-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 20px;
        align-items: stretch;
        margin-bottom: 20px;
    }

    /* Figma: 3 Columns x 2 Rows with exact 20px Gap */
    .ret-kpi-grid-6 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        grid-template-rows: repeat(2, minmax(140px, auto));
        gap: 20px;
    }

    /* Figma KPI Card: 296.67px Fill x 140px Hug, 14px radius, 1px border #E8EAF2, 20px padding */
    .ret-kpi-card {
        background: #FFFFFF;
        border: 1px solid #E8EAF2;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        height: 100%;
        box-sizing: border-box;
        transition: all 0.15s ease;
    }

    .ret-kpi-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    }

    .ret-kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ret-kpi-label {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
        line-height: 1.2;
    }

    .ret-kpi-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #F8FAFC;
        border: 1px solid #EFF4FE;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ret-kpi-val {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 24px;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.2;
        margin: 6px 0;
        word-break: break-word;
        font-variant-numeric: tabular-nums;
    }

    .ret-kpi-foot {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
    }

    .ret-delta-badge {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        height: 20px !important;
        padding: 0 8px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        line-height: 20px !important;
        box-sizing: border-box !important;
        font-variant-numeric: tabular-nums !important;
        letter-spacing: -0.01em !important;
    }

    .ret-delta-badge.up {
        background: #EEF8F1 !important;
        color: #029153 !important;
    }

    .ret-delta-badge.down {
        background: #FEF0EF !important;
        color: #EE473D !important;
    }

    .ret-delta-badge.neutral {
        background: #F1F5F9 !important;
        color: #64748B !important;
    }

    .ret-delta-badge svg {
        flex-shrink: 0;
    }

    .ret-delta-sub {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px;
        color: #1E293B;
        font-weight: 500;
        letter-spacing: -0.01em;
    }

    /* Top KPI Layout (6 Cards Left Grid + Reasons Radial Gauge Right) */
    .ret-kpi-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 20px;
        align-items: stretch;
        margin-bottom: 20px;
    }

    /* Return Reasons Radial Gauge Card (Figma: 280px x 300px, 14px radius) */
    .ret-reasons-card {
        background: #FFFFFF;
        border: 1px solid #E8EAF2;
        border-radius: 14px;
        padding: 24px 20px 20px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        width: 280px;
        min-width: 280px;
        height: 300px;
        min-height: 300px;
        box-sizing: border-box;
        transition: all 0.15s ease;
    }

    .ret-reasons-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .ret-reasons-card h3 {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 16px;
        font-weight: 700;
        color: #0F172A;
        text-align: center;
        margin: 0;
        line-height: 1.2;
    }

    .ret-gauge-wrap {
        position: relative;
        width: 170px;
        height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .ret-gauge-center {
        position: absolute;
        text-align: center;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .ret-gauge-center .total-val {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 32px;
        font-weight: 800;
        color: #1A1A1A;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        margin-bottom: 2px;
    }

    .ret-gauge-center .total-lbl {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        font-weight: 500;
        color: #64748B;
    }

    .ret-reasons-legend {
        border-top: none !important;
        padding-top: 0 !important;
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        width: 100%;
        box-sizing: border-box;
    }

    .ret-reasons-legend .legend-left {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .ret-reasons-legend .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        background: #4362CE;
        flex-shrink: 0;
    }

    .ret-reasons-legend .legend-title {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-weight: 500;
        color: #1E293B;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ret-reasons-legend .legend-val {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-weight: 700;
        color: #1E293B;
        font-size: 13px;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }


    /* Trend Toggle */
    .ret-trend-toggle {
        display: flex;
        background: #F1F5F9;
        padding: 3px;
        border-radius: 8px;
        gap: 2px;
    }

    .ret-trend-btn {
        border: none;
        background: transparent;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748B;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ret-trend-btn.active {
        background: #FFFFFF;
        color: #0F172A;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    /* Product Performance Table (Figma Exact) */
    .ret-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .ret-table thead th {
        background: #FFFFFF;
        color: #64748B;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0;
        padding: 12px 15px;
        border-top: none;
        border-bottom: 1px solid #E8EAF2;
        text-align: left;
        white-space: nowrap;
    }

    .ret-table thead th:last-child {
        text-align: center;
    }

    /* Figma: 1230px Fill x 62px Hug, padding 14px 15px, bottom border 1px #E8EAF2, #F7F9FE alternating */
    .ret-table tbody tr:nth-child(odd) td {
        background-color: #F7F9FE !important;
    }

    .ret-table tbody tr:nth-child(even) td {
        background-color: #FFFFFF !important;
    }

    .ret-table tbody tr {
        height: 62px;
        box-sizing: border-box;
    }

    .ret-table tbody td {
        padding: 14px 15px;
        height: 62px;
        box-sizing: border-box;
        border-top: none;
        border-bottom: 1px solid #E8EAF2;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        color: #0F172A;
        vertical-align: middle;
        transition: background 0.15s ease;
    }

    .ret-table tbody tr:hover td {
        background-color: #EEF2FD !important;
    }

    .ret-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ret-prod-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 460px;
    }

    .ret-prod-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #F8FAFC;
        border: 1px solid #E8EAF2;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ret-prod-name {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-weight: 600;
        color: #0F172A;
        font-size: 13px;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ret-ratio-bar-wrap {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        width: 170px;
    }

    .ret-ratio-bar {
        width: 100px;
        height: 4px;
        background: #F1F5F9;
        border-radius: 2px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .ret-ratio-fill {
        height: 100%;
        background: #4362CE;
        border-radius: 2px;
        transition: width 0.4s ease;
    }

    .ret-ratio-pct {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        font-weight: 700;
        color: #0F172A;
        font-variant-numeric: tabular-nums;
    }

    .ret-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 14px;
        border-radius: 6px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        border: 1px solid transparent;
        line-height: 1;
        min-width: 80px;
        box-sizing: border-box;
    }

    .ret-status-badge.optimal {
        color: #029153;
        background: #EEF8F1;
        border-color: #E2F2E7;
    }

    .ret-status-badge.watch {
        color: #D97706;
        background: #FFFBEB;
        border-color: #FDE68A;
    }

    .ret-status-badge.critical {
        color: #EE473D;
        background: #FEF0EF;
        border-color: #FDDCDA;
    }

    /* Table Foot / Pagination */
    .ret-table-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #F1F5F9;
        margin-top: 4px;
        flex-wrap: wrap;
        gap: 1rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        color: #64748B;
        font-weight: 500;
    }

    .ret-pagination {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ret-page-btn {
        min-width: 28px;
        height: 28px;
        padding: 0 6px;
        border-radius: 6px;
        border: none;
        background: transparent;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ret-page-btn:hover:not(:disabled) {
        background: #F1F5F9;
        color: #0F172A;
    }

    .ret-page-btn.active {
        background: #F1F5F9;
        color: #0F172A;
        font-weight: 700;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .ret-kpi-layout {
            grid-template-columns: 1fr !important;
            gap: 1.25rem !important;
        }

        .ret-kpi-grid-6 {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 14px !important;
        }
    }

    @media (max-width: 768px) {
        .ret-container {
            padding: 0.75rem 0.75rem 100px 0.75rem !important;
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }

        .ret-topbar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        .ret-topbar-left {
            width: 100% !important;
        }

        .ret-profile-select-wrap,
        .ret-profile-select {
            width: 100% !important;
            min-width: 0 !important;
        }

        .ret-breadcrumb {
            display: none !important;
        }

        .ret-topbar-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }

        .btn-ret-icon {
            display: none !important;
        }

        .ret-page-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
            margin-bottom: 1rem !important;
        }

        .ret-controls {
            width: 100% !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
        }

        .ret-controls .figma-date-picker-wrap {
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .ret-controls .figma-date-picker-wrap input.flatpickr-range-input {
            width: 100% !important;
            font-size: 0.76rem !important;
        }

        .ret-kpi-grid-6 {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .ret-kpi-card {
            min-height: 120px !important;
            padding: 14px 16px !important;
        }
    }
</style>

<div class="ret-container">
    <!-- Figma-style Top Bar -->
    <div class="figma-page-topbar">
        <div class="figma-page-topbar-left">
            <div class="figma-select-wrapper">
                <select id="filter_customer" <?php echo (($_SESSION['role'] ?? '') === 'customer') ? 'disabled' : ''; ?>>
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <option value="">All Amazon Profiles</option>
                    <?php endif; ?>
                    <?php
                    $customers->data_seek(0);
                    while ($row = $customers->fetch_assoc()):
                        $selected = (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) == $row['id']) ? 'selected' : '';
                        if (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) != $row['id'])
                            continue;
                        ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($row['customer_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>
            <span class="figma-page-breadcrumb">Dashboard <span class="breadcrumb-dot">•</span> <strong>Return
                    Page</strong></span>
        </div>
        <div class="figma-page-topbar-right">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-figma-primary"><i
                        class="fas fa-plus"></i> New Upload</a>
            <?php endif; ?>
            <button type="button" class="btn-figma-outline-sm" id="btn_export_csv_top"><i
                    class="fas fa-file-export"></i>
                Export CSV</button>
            <button type="button" class="btn-figma-icon-sm" title="Search"><i class="fas fa-search"></i></button>
            <button type="button" class="btn-figma-icon-sm" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge"></span>
            </button>
        </div>
    </div>

    <!-- Page Header -->
    <div class="ret-page-head">
        <div class="ret-page-title">
            <h2>Return Page</h2>
            <p>Returns intelligence, reasons & product performance</p>
        </div>
        <div class="ret-controls">
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
                <input type="text" class="flatpickr-range-input date-range-picker" id="date_range_picker_ret"
                    placeholder="Select date range" readonly>
                <input type="hidden" id="filter_from" value="2026-01-01">
                <input type="hidden" id="filter_to" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <button type="button" class="btn-figma-refresh" id="apply_filters" title="Refresh">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10.1115 0.666504L10.5101 1.41169C10.7796 1.91548 10.9143 2.16738 10.8253 2.27526C10.7361 2.38314 10.4427 2.29601 9.85573 2.12176C9.26893 1.94754 8.64593 1.85381 8.00033 1.85381C4.50252 1.85381 1.66699 4.60548 1.66699 7.99987C1.66699 9.11927 1.97541 10.1689 2.51428 11.0729M5.88921 15.3332L5.49057 14.588C5.22105 14.0842 5.08629 13.8323 5.17539 13.7244C5.26451 13.6165 5.55799 13.7037 6.14492 13.8779C6.73173 14.0521 7.35473 14.1459 8.00033 14.1459C11.4981 14.1459 14.3337 11.3942 14.3337 7.99987C14.3337 6.8804 14.0253 5.83082 13.4864 4.92682"
                        stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>

    <!-- TOP KPI & REASONS ROW -->
    <div class="ret-kpi-layout">

        <!-- Left: 6 KPI Cards Grid -->
        <div class="ret-kpi-grid-6">

            <!-- Card 1: Total Returns -->
            <div class="ret-kpi-card">
                <div class="ret-kpi-top">
                    <span class="ret-kpi-label">Total Returns</span>
                    <div class="ret-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Overview/Reload.svg"
                            style="width: 15px; height: 15px;" />
                    </div>
                </div>
                <div class="ret-kpi-val" id="kpi_total_returns">54</div>
                <div class="ret-kpi-foot">
                    <span class="ret-delta-badge down" id="cmp_total_returns">+100% <svg width="10" height="10"
                            viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 1.5V8.5M5 8.5L8 5.5M5 8.5L2 5.5" stroke="#EE473D" stroke-width="1.4"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <span class="ret-delta-sub">vs LW</span>
                </div>
            </div>

            <!-- Card 2: Sellable % -->
            <div class="ret-kpi-card">
                <div class="ret-kpi-top">
                    <span class="ret-kpi-label">Sellable %</span>
                    <div class="ret-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Sellable.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="ret-kpi-val" id="kpi_sellable_pct">94.4%</div>
                <div class="ret-kpi-foot">
                    <span class="ret-delta-badge up" id="cmp_sellable_pct">+100% <svg width="10" height="10"
                            viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 8.5V1.5M5 1.5L8 4.5M5 1.5L2 4.5" stroke="#029153" stroke-width="1.4"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <span class="ret-delta-sub">vs LW</span>
                </div>
            </div>

            <!-- Card 3: Damaged % -->
            <div class="ret-kpi-card">
                <div class="ret-kpi-top">
                    <span class="ret-kpi-label">Damaged %</span>
                    <div class="ret-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Damaged.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="ret-kpi-val" id="kpi_damaged_pct">5.6%</div>
                <div class="ret-kpi-foot">
                    <span class="ret-delta-badge down" id="cmp_damaged_pct">+100% <svg width="10" height="10"
                            viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 1.5V8.5M5 8.5L8 5.5M5 8.5L2 5.5" stroke="#EE473D" stroke-width="1.4"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <span class="ret-delta-sub">vs LW</span>
                </div>
            </div>

            <!-- Card 4: Top Reason -->
            <div class="ret-kpi-card">
                <div class="ret-kpi-top">
                    <span class="ret-kpi-label">Top Reason</span>
                    <div class="ret-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Top Reason.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="ret-kpi-val" id="kpi_top_reason"
                    style="font-size: 1.05rem; font-weight: 800; text-transform: uppercase;">CUSTOMER REFUND</div>
                <div class="ret-kpi-foot">
                    <span class="ret-delta-sub" id="kpi_top_reason_sub">100% of total</span>
                </div>
            </div>

            <!-- Card 5: Top SKU -->
            <div class="ret-kpi-card">
                <div class="ret-kpi-top">
                    <span class="ret-kpi-label">Top SKU</span>
                    <div class="ret-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Top SKU.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="ret-kpi-val" id="kpi_top_sku" style="font-size: 1.05rem; font-weight: 800;">BUNDLE-ROUL-1
                </div>
                <div class="ret-kpi-foot">
                    <span class="ret-delta-sub" id="kpi_top_sku_sub"
                        style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 180px;">LA
                        PETITE OURSE Cloth Diaper Liners -...</span>
                </div>
            </div>

            <!-- Card 6: Defect Rate -->
            <div class="ret-kpi-card">
                <div class="ret-kpi-top">
                    <span class="ret-kpi-label">Defect Rate</span>
                    <div class="ret-kpi-icon-box">
                        <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Defect Rate.svg"
                            style="width: 16px; height: 16px;" />
                    </div>
                </div>
                <div class="ret-kpi-val" id="kpi_defect_rate">0%</div>
                <div class="ret-kpi-foot">
                    <span class="ret-delta-sub" id="cmp_defect_rate">Stable</span>
                </div>
            </div>

        </div>

        <!-- Right: Return Reasons Radial Gauge Card -->
        <div class="ret-reasons-card">
            <div class="ret-card-head" style="margin-bottom: 0; justify-content: center;">
                <h3>Return Reasons</h3>
            </div>

            <div class="ret-gauge-wrap">
                <canvas id="reasonsChart"></canvas>
                <div class="ret-gauge-center">
                    <div class="total-val" id="donut_total">54</div>
                    <div class="total-lbl">Total</div>
                </div>
            </div>

            <div class="ret-reasons-legend" id="reason_legend">
                <div class="legend-left">
                    <span class="legend-dot"></span>
                    <span class="legend-title" id="lbl_top_reason">Customer Refund</span>
                </div>
                <span class="legend-val" id="lbl_top_reason_count">54 (100%)</span>
            </div>
        </div>

    </div>

    <!-- TIME TREND ANALYSIS CARD -->
    <div class="ret-card">
        <div class="ret-card-head">
            <h3>Time Trend Analysis</h3>
            <div class="ret-trend-toggle">
                <button type="button" class="ret-trend-btn active" data-trend="daily">Daily</button>
                <button type="button" class="ret-trend-btn" data-trend="monthly">Monthly</button>
            </div>
        </div>

        <div style="height: 280px; width: 100%; position: relative;">
            <canvas id="trendChart"></canvas>
        </div>

        <div
            style="display: flex; align-items: center; justify-content: center; gap: 20px; font-size: 0.78rem; font-weight: 700; margin-top: 14px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 10px; height: 10px; border-radius: 3px; background: #3B82F6;"></span>
                <span style="color: #0F172A;">Sellable Returns</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 10px; height: 10px; border-radius: 3px; background: #10B981;"></span>
                <span style="color: #0F172A;">Damaged Returns</span>
            </div>
        </div>
    </div>

    <!-- PRODUCT PERFORMANCE TABLE -->
    <div class="ret-card">
        <div class="ret-card-head" style="align-items: center; margin-bottom: 1rem;">
            <h3>Product Performance</h3>
            <button type="button" class="btn-figma-outline" id="export_csv"
                style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 13px; font-weight: 600; color: #1E293B; padding: 7px 14px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.15s ease;">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1.5V10.5M8 10.5L5 7.5M8 10.5L11 7.5" stroke="#1E293B" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2 12V13.5C2 14.0523 2.44772 14.5 3 14.5H13C13.5523 14.5 14 14.0523 14 13.5V12"
                        stroke="#1E293B" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                Export CSV
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="ret-table" id="products_table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Product Name</th>
                        <th style="width: 12%;">Return Count</th>
                        <th style="width: 18%;">Top Reason</th>
                        <th style="width: 15%;">Sellable Ratio</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody id="products_body">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8; font-weight: 600;">
                            Loading returns data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="ret-table-foot">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span>Show</span>
                <select id="dt_page_len"
                    style="border: 1px solid #E2E8F0; border-radius: 6px; padding: 3px 8px; font-size: 0.78rem; font-weight: 600; color: #0F172A; background: #FFF;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>Entries</span>
                <span style="margin-left: 12px;" id="dt_info_text">Showing 1 to 10 of 92 entries</span>
            </div>
            <div class="ret-pagination" id="dt_pagination">
                <button type="button" class="ret-page-btn">&lt;</button>
                <button type="button" class="ret-page-btn active">1</button>
                <button type="button" class="ret-page-btn">2</button>
                <button type="button" class="ret-page-btn">3</button>
                <button type="button" class="ret-page-btn">4</button>
                <button type="button" class="ret-page-btn">&gt;</button>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function () {
        let reasonsChart = null;
        let trendChart = null;
        let trendMode = 'daily';
        let productRows = [];

        function formatAbbrev(n) {
            let num = Number(n) || 0;
            if (num >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
            return num.toLocaleString();
        }

        function renderReasonsRingChart(reasons, total) {
            const el = document.getElementById('reasonsChart');
            if (!el || typeof Chart === 'undefined') return;
            if (reasonsChart) reasonsChart.destroy();

            let totalCount = total || 0;
            $('#donut_total').text(formatAbbrev(totalCount));

            let topReason = (reasons && reasons.length > 0) ? reasons[0] : { label: 'CUSTOMER REFUND', count: totalCount, pct: 100 };
            $('#lbl_top_reason').text(topReason.label.replace(/_/g, ' '));
            $('#lbl_top_reason_count').text(`${topReason.count} (${topReason.pct}%)`);

            // Segmented Rounded Ticks matching Figma
            const canvas = el;
            const ctx = canvas.getContext('2d');
            const dpr = window.devicePixelRatio || 1;
            const width = canvas.parentElement.clientWidth || 170;
            const height = canvas.parentElement.clientHeight || 170;
            canvas.width = width * dpr;
            canvas.height = height * dpr;
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
            ctx.scale(dpr, dpr);

            const centerX = width / 2;
            const centerY = height / 2;
            const totalBars = 36;
            const radius = 68;
            const barLength = 16;
            const barWidth = 3.5;
            const filledCount = totalCount > 0 ? Math.round((topReason.pct / 100) * totalBars) : 0;

            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < totalBars; i++) {
                // start from top (-90 deg)
                const angle = ((i / totalBars) * 2 * Math.PI) - (Math.PI / 2);
                const isFilled = i < filledCount;
                const color = isFilled ? '#4362CE' : '#E2E8F0';

                const x1 = centerX + Math.cos(angle) * (radius - barLength / 2);
                const y1 = centerY + Math.sin(angle) * (radius - barLength / 2);
                const x2 = centerX + Math.cos(angle) * (radius + barLength / 2);
                const y2 = centerY + Math.sin(angle) * (radius + barLength / 2);

                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.strokeStyle = color;
                ctx.lineWidth = barWidth;
                ctx.lineCap = 'round';
                ctx.stroke();
            }
        }

        function renderTrendChartData(trendData) {
            const el = document.getElementById('trendChart');
            if (!el || typeof Chart === 'undefined') return;
            if (trendChart) trendChart.destroy();

            let labels = ['Jan 05', 'Jan 06', 'Jan 07', 'Jan 08', 'Jan 09', 'Jan 10', 'Jan 11', 'Jan 12', 'Jan 13', 'Jan 14', 'Jan 20', 'Jan 22', 'Jan 23', 'Jan 28', 'Jan 29', 'Jan 30', 'Jan 31', 'Feb 05', 'Feb 06', 'Feb 08', 'Feb 09', 'Feb 11', 'Feb 12', 'Feb 14', 'Feb 15', 'Feb 17', 'Feb 20', 'Feb 21', 'Feb 23', 'Feb 24'];
            let sellablePoints = [2, 2, 2, 2.2, 4, 2.1, 2, 2, 2, 2, 2, 2, 2, 2, 4, 4, 4, 2, 0.4, 0.9, 1, 1, 1, 1, 1, 1, 1, 2, 1.2, 2];
            let damagedPoints = [0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            if (trendData && trendData.labels && trendData.labels.length > 0) {
                labels = trendData.labels;
                sellablePoints = trendData.sellable;
                damagedPoints = trendData.damaged;
            }

            const ctx = el.getContext('2d');
            const blueGrad = ctx.createLinearGradient(0, 0, 0, 260);
            blueGrad.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            blueGrad.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            const greenGrad = ctx.createLinearGradient(0, 0, 0, 260);
            greenGrad.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            greenGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            let maxVal = Math.max(4, ...sellablePoints, ...damagedPoints);

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Sellable Returns',
                            data: sellablePoints,
                            borderColor: '#3B82F6',
                            borderWidth: 2,
                            backgroundColor: blueGrad,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 2.5,
                            pointBackgroundColor: '#3B82F6',
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Damaged Returns',
                            data: damagedPoints,
                            borderColor: '#10B981',
                            borderWidth: 2,
                            backgroundColor: greenGrad,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 2.5,
                            pointBackgroundColor: '#10B981',
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#FFFFFF',
                            titleColor: '#0F172A',
                            bodyColor: '#0F172A',
                            borderColor: '#E2E8F0',
                            borderWidth: 1,
                            padding: 10,
                            boxPadding: 4,
                            usePointStyle: true
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#94A3B8' }
                        },
                        y: {
                            min: 0,
                            max: Math.ceil(maxVal * 1.15),
                            ticks: { font: { size: 10 }, color: '#94A3B8' },
                            grid: { color: '#F1F5F9' }
                        }
                    }
                }
            });
        }

        let allProducts = [];
        let currentPage = 1;
        let pageSize = 10;

        function renderPaginatedTable() {
            if (!allProducts || allProducts.length === 0) {
                $('#products_body').html('<tr><td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8; font-weight: 600;">No return records found</td></tr>');
                $('#dt_info_text').text('Showing 0 to 0 of 0 entries');
                $('#dt_pagination').html('<button type="button" class="ret-page-btn" disabled style="opacity: 0.5;">&lt;</button><button type="button" class="ret-page-btn active">1</button><button type="button" class="ret-page-btn" disabled style="opacity: 0.5;">&gt;</button>');
                return;
            }

            let totalPages = Math.ceil(allProducts.length / pageSize) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            let startIndex = (currentPage - 1) * pageSize;
            let endIndex = Math.min(startIndex + pageSize, allProducts.length);
            let pageSlice = allProducts.slice(startIndex, endIndex);

            let html = '';
            pageSlice.forEach(p => {
                let name = p.product_name || p.name || p.sku || 'Unknown Product';
                let count = p.return_count !== undefined ? p.return_count : (p.count !== undefined ? p.count : 0);
                let reason = (p.top_reason || p.reason || 'CUSTOMER REFUND').replace(/_/g, ' ');
                let ratio = p.sellable_ratio !== undefined ? p.sellable_ratio : (p.ratio !== undefined ? p.ratio : 100);
                let status = p.status || (ratio >= 85 ? 'OPTIMAL' : (ratio >= 70 ? 'WATCH' : 'CRITICAL'));
                let stCls = status.toLowerCase();

                html += `<tr>
                <td>
                    <div class="ret-prod-cell">
                        <div class="ret-prod-icon">
                            <img src="<?php echo BASE_URL; ?>assets/icons/Return Page/Product.svg" style="width: 16px; height: 16px;" />
                        </div>
                        <span class="ret-prod-name">${name}</span>
                    </div>
                </td>
                <td style="font-weight: 700; color: #0F172A;">${count}</td>
                <td style="color: #64748B; font-weight: 500;">${reason}</td>
                <td>
                    <div class="ret-ratio-bar-wrap">
                        <div class="ret-ratio-bar">
                            <div class="ret-ratio-fill" style="width: ${ratio}%;"></div>
                        </div>
                        <span class="ret-ratio-pct">${ratio}%</span>
                    </div>
                </td>
                <td>
                    <span class="ret-status-badge ${stCls}">${status}</span>
                </td>
            </tr>`;
            });

            $('#products_body').html(html);
            $('#dt_info_text').text(`Showing ${startIndex + 1} to ${endIndex} of ${allProducts.length} entries`);

            // Build Pagination Buttons
            let pagHtml = `<button type="button" class="ret-page-btn" id="btn_dt_prev" ${currentPage === 1 ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : ''}>&lt;</button>`;

            for (let i = 1; i <= totalPages; i++) {
                if (totalPages > 7) {
                    if (i !== 1 && i !== totalPages && Math.abs(i - currentPage) > 2) {
                        if (i === 2 || i === totalPages - 1) {
                            pagHtml += `<span style="padding: 0 4px; color: #94A3B8;">...</span>`;
                        }
                        continue;
                    }
                }
                pagHtml += `<button type="button" class="ret-page-btn ret-page-num ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
            }

            pagHtml += `<button type="button" class="ret-page-btn" id="btn_dt_next" ${currentPage === totalPages ? 'disabled style="opacity: 0.4; cursor: not-allowed;"' : ''}>&gt;</button>`;
            $('#dt_pagination').html(pagHtml);
        }

        // Pagination Click Events
        $(document).on('click', '.ret-page-num', function (e) {
            e.preventDefault();
            currentPage = parseInt($(this).data('page'));
            renderPaginatedTable();
        });

        $(document).on('click', '#btn_dt_prev', function (e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                renderPaginatedTable();
            }
        });

        $(document).on('click', '#btn_dt_next', function (e) {
            e.preventDefault();
            let totalPages = Math.ceil(allProducts.length / pageSize) || 1;
            if (currentPage < totalPages) {
                currentPage++;
                renderPaginatedTable();
            }
        });

        $('#dt_page_len').on('change', function () {
            pageSize = parseInt($(this).val()) || 10;
            currentPage = 1;
            renderPaginatedTable();
        });

        function setCmpBadge(el, cmp, suffix, invert) {
            const $el = $(el);
            if (!cmp || cmp.dir === 'none' || !cmp.pct) {
                $el.removeClass('up down neutral').addClass('neutral').html('Stable');
                return;
            }
            const isUp = cmp.dir === 'up';
            const good = invert ? !isUp : isUp;
            const cls = good ? 'up' : 'down';
            const color = cls === 'up' ? '#029153' : '#EE473D';
            const iconSvg = isUp
                ? `<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 8.5V1.5M5 1.5L8 4.5M5 1.5L2 4.5" stroke="${color}" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>`
                : `<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 1.5V8.5M5 8.5L8 5.5M5 8.5L2 5.5" stroke="${color}" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
            const sign = isUp ? '+' : '-';
            $el.removeClass('up down neutral').addClass(cls).html(`${sign}${cmp.pct}% ${iconSvg}`);
        }

        function fetchData() {
            const customerId = $('#filter_customer').val();
            const from = $('#filter_from').val();
            const to = $('#filter_to').val();

            $('#apply_filters').html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '../../api/returns_data.php',
                data: { customer_id: customerId, from_date: from, to_date: to },
                dataType: 'json',
                success: function (res) {
                    $('#apply_filters').html('<img src="<?php echo BASE_URL; ?>assets/icons/Overview/Reload.svg" style="width: 14px; height: 14px;" />');

                    if (res.kpis) {
                        let total = res.kpis.total_returns !== undefined ? res.kpis.total_returns : 0;
                        $('#kpi_total_returns').text(formatAbbrev(total));
                        $('#kpi_sellable_pct').text((res.kpis.sellable_pct !== undefined ? res.kpis.sellable_pct : '94.4') + '%');
                        $('#kpi_damaged_pct').text((res.kpis.damaged_pct !== undefined ? res.kpis.damaged_pct : '5.6') + '%');
                        $('#kpi_top_reason').text((res.kpis.top_reason || 'CUSTOMER REFUND').replace(/_/g, ' '));
                        $('#kpi_top_sku').text(res.kpis.top_sku || 'BUNDLE-ROUL-1');
                        $('#kpi_defect_rate').text((res.kpis.defect_rate !== undefined ? res.kpis.defect_rate : '0') + '%');

                        if (res.kpis.top_sku_name && res.kpis.top_sku_name !== '—') {
                            $('#kpi_top_sku_sub').text(res.kpis.top_sku_name);
                        }
                        if (res.kpis.top_reason_pct) {
                            $('#kpi_top_reason_sub').text(res.kpis.top_reason_pct + '% of total');
                        }

                        let cmp = res.kpis.comparison || {};
                        setCmpBadge('#cmp_total_returns', cmp.total_returns, 'vs LW', true);
                        setCmpBadge('#cmp_sellable_pct', cmp.sellable_pct, 'vs LW', false);
                        setCmpBadge('#cmp_damaged_pct', cmp.damaged_pct, 'vs LW', true);
                    }

                    renderReasonsRingChart(res.reasons, res.kpis ? res.kpis.total_returns : 0);
                    renderTrendChartData(res.trend ? res.trend[trendMode] : null);

                    allProducts = res.products || [];
                    currentPage = 1;
                    renderPaginatedTable();
                },
                error: function () {
                    $('#apply_filters').html('<img src="<?php echo BASE_URL; ?>assets/icons/Overview/Reload.svg" style="width: 14px; height: 14px;" />');
                    renderReasonsRingChart(null, 54);
                    renderTrendChartData(null);
                    allProducts = [];
                    renderPaginatedTable();
                }
            });
        }

        $('.ret-trend-btn').on('click', function () {
            $('.ret-trend-btn').removeClass('active');
            $(this).addClass('active');
            trendMode = $(this).data('trend');
            fetchData();
        });

        if (typeof flatpickr !== 'undefined') {
            flatpickr("#date_range_picker_ret", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "M d, Y",
                defaultDate: [$('#filter_from').val() || "2026-01-01", $('#filter_to').val() || "<?php echo date('Y-m-d'); ?>"],
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const from = instance.formatDate(selectedDates[0], "Y-m-d");
                        const to = instance.formatDate(selectedDates[1], "Y-m-d");
                        $('#filter_from').val(from);
                        $('#filter_to').val(to);
                        fetchData();
                    }
                }
            });
        }

        $('#apply_filters').on('click', fetchData);
        $('#filter_customer').on('change', fetchData);

        // CSV Export Function
        $('#export_csv, #btn_export_csv_top').on('click', function (e) {
            e.preventDefault();
            if (!allProducts || allProducts.length === 0) {
                alert('No return data to export');
                return;
            }
            let csv = "Product Name,Return Count,Top Reason,Sellable Ratio,Status\n";
            allProducts.forEach(p => {
                let name = `"${(p.product_name || p.sku || '').replace(/"/g, '""')}"`;
                let count = p.return_count || 0;
                let reason = `"${(p.top_reason || '').replace(/"/g, '""')}"`;
                let ratio = `${p.sellable_ratio || 0}%`;
                let status = p.status || 'OPTIMAL';
                csv += `${name},${count},${reason},${ratio},${status}\n`;
            });
            let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.setAttribute("download", `returns_report_${new Date().toISOString().slice(0, 10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        fetchData();
    });
</script>

<?php include '../../includes/footer.php'; ?>