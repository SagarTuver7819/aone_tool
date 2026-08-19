<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = 'Reimbursement Center';
$page_subtitle = 'AI-powered Amazon reimbursement & revenue recovery intelligence';

$customers = get_all_customers();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
.reimb-page .kpi-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.reimb-page .kpi-card {
    background: #ffffff;
    border: 1px solid #e7e8e9;
    border-radius: 16px;
    padding: 1.25rem !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    min-height: 130px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.reimb-page .kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}
.reimb-page .kpi-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}
.reimb-page .kpi-label {
    font-size: 0.7rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.reimb-page .kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    background: #f1f5f9;
    color: #475569;
}
.reimb-page .kpi-value {
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -0.02em;
}
.reimb-page .kpi-sub {
    font-size: 0.75rem;
    font-weight: 700;
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}
.reimb-page .kpi-sub.up { color: #10b981; }
.reimb-page .kpi-sub.down { color: #ef4444; }
.reimb-page .kpi-sub.neutral { color: #64748b; }
.reimb-page .kpi-sub.warning { color: #f97316; }

/* AI Border with Premium Gradient matching project theme (brand blue) */
.ai-insights-panel {
    position: relative;
    background: linear-gradient(135deg, #0d1b3e 0%, #0a2252 40%, #0f3460 100%);
    border: none;
    border-radius: 16px;
    padding: 1.75rem 1.5rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(13, 27, 62, 0.25);
}
.ai-insights-panel::before {
    content: "";
    position: absolute;
    top: -40%;
    right: -5%;
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(0, 81, 213, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.ai-insights-panel h3,
.ai-insights-panel p,
.ai-insights-panel li,
.ai-insights-panel span:not(.badge-priority) {
    color: #e2e8f0 !important;
}
.ai-insights-panel .ai-sparkle-icon { color: #60a5fa !important; }
.ai-sparkle-icon {
    font-size: 1.5rem;
    color: var(--secondary);
    vertical-align: middle;
    margin-right: 0.5rem;
}

/* Two Column Layout */
.reimb-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.reimb-card {
    background: #ffffff;
    border: 1px solid #e7e8e9;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}
.reimb-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.reimb-card-title {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.01em;
}

/* Donut & Legend styling */
.reimb-donut-wrap {
    position: relative;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.reimb-donut-center {
    position: absolute;
    text-align: center;
    pointer-events: none;
}
.reimb-donut-center .total-val {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
}
.reimb-donut-center .total-lbl {
    font-size: 0.7rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
}

/* Funnel bars */
.funnel-stage {
    margin-bottom: 1rem;
}
.funnel-label-wrap {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 0.25rem;
}
.funnel-progress-bar {
    height: 8px;
    background: #f1f5f9;
    border-radius: 99px;
    overflow: hidden;
}
.funnel-fill {
    height: 100%;
    background: var(--secondary);
    border-radius: 99px;
    transition: width 0.5s ease;
}
.funnel-fill.recovered {
    background: #10b981;
    box-shadow: 0 0 8px rgba(16, 185, 129, 0.4);
}

/* Radial Progress charts */
.radial-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.radial-card {
    background: #ffffff;
    border: 1px solid #e7e8e9;
    border-radius: 16px;
    padding: 1.25rem;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}
.radial-label {
    font-size: 0.7rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.75rem;
}
.radial-svg-wrap {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.radial-svg-text {
    position: absolute;
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
}
.radial-subtext {
    font-size: 0.75rem;
    font-weight: 700;
}

/* Leaderboard Details */
.product-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.product-cell .name {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.85rem;
}
.product-cell .sku-lbl {
    font-size: 0.7rem;
    color: #64748b;
    font-weight: 500;
}
.product-thumb {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #e2e8f0, #f8fafc);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 0.85rem;
    flex-shrink: 0;
}

@media (max-width: 1200px) {
    .reimb-page .kpi-grid-4 { grid-template-columns: repeat(2, 1fr); }
    .reimb-grid-2 { grid-template-columns: 1fr; }
    .radial-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .reimb-page .kpi-grid-4 { grid-template-columns: 1fr; }
    .radial-grid { grid-template-columns: 1fr; }
}
</style>

<style>.top-header { display: none !important; } .main-wrapper { padding-top: 1.25rem !important; }</style>
<div class="reimb-page">
    <!-- Figma-style Top Bar -->
    <div class="figma-page-topbar">
        <div class="figma-page-topbar-left">
            <select id="filter_customer" style="min-width:180px; padding:0.5rem 0.85rem; border:1px solid #e2e8f0; border-radius:10px; font-size:0.82rem; font-weight:600; color:#334155; background:#fff;" <?php echo (($_SESSION['role'] ?? '') === 'customer') ? 'disabled' : ''; ?>>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <option value="">All Amazon Profiles</option>
                <?php endif; ?>
                <?php $customers->data_seek(0); while ($row = $customers->fetch_assoc()):
                    $selected = (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) == $row['id']) ? 'selected' : '';
                    if (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) != $row['id']) continue;
                ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($row['customer_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <span class="figma-page-breadcrumb">Dashboard <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i> <strong>Reimbursement</strong></span>
        </div>
        <div class="figma-page-topbar-right">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-figma-primary"><i class="fas fa-plus"></i> New Upload</a>
            <?php endif; ?>
            <button type="button" class="btn-figma-outline-sm"><i class="fas fa-file-export"></i> Export CSV</button>
            <button type="button" class="btn-figma-icon-sm"><i class="fas fa-search"></i></button>
        </div>
    </div>

    <!-- Page Title & Date Range -->
    <div class="figma-page-head">
        <div>
            <h2>Reimbursement</h2>
            <p>AI-powered Amazon reimbursement & revenue recovery intelligence</p>
        </div>
        <div class="figma-date-bar">
            <i class="far fa-calendar-alt" style="color: #64748b; font-size: 0.85rem; margin-left: 4px;"></i>
            <input type="date" id="filter_from" value="">
            <span class="date-sep">-</span>
            <input type="date" id="filter_to" value="">
            <button type="button" class="btn-refresh-icon" id="apply_filters" title="Refresh"><i class="fas fa-sync-alt"></i></button>
        </div>
    </div>

    <!-- Executive Overview Row (4 cards) -->
    <div class="kpi-grid-4">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Total Reimbursement</span>
                <div class="kpi-icon" style="background:#e0f2fe; color:#0369a1;"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="kpi-value" id="kpi_total_reimb">0</div>
            <div class="kpi-sub up" id="cmp_total_reimb"><i class="fas fa-arrow-up"></i> — vs LW</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Units Recovered</span>
                <div class="kpi-icon" style="background:#dcfce7; color:#15803d;"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="kpi-value" id="kpi_units_recovered">0</div>
            <div class="kpi-sub up" id="cmp_units_recovered"><i class="fas fa-arrow-up"></i> — vs LW</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Recovery Rate %</span>
                <div class="kpi-icon" style="background:#f3e8ff; color:#7e22ce;"><i class="fas fa-percentage"></i></div>
            </div>
            <div class="kpi-value" id="kpi_recovery_rate">0%</div>
            <div class="kpi-sub up" id="cmp_recovery_rate" style="color:#10b981;"><i class="fas fa-check-circle"></i> Optimized efficiency</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Est. Pending / Outstanding</span>
                <div class="kpi-icon" style="background:#ffe4e6; color:#be123c;"><i class="fas fa-hourglass-half"></i></div>
            </div>
            <div class="kpi-value" id="kpi_pending_value">$0</div>
            <div class="kpi-sub warning" id="cmp_pending_claims"><i class="fas fa-exclamation-triangle"></i> Pending claims check</div>
        </div>
    </div>

    <!-- Reimbursement Value Trend Chart -->
    <div class="reimb-card" style="margin-bottom:1.5rem;">
        <div class="reimb-card-header">
            <div>
                <span class="reimb-card-title" style="display:block; font-size:1.1rem;">Reimbursement Value Trend</span>
                <span style="font-size:0.75rem; color:#64748b;">Daily aggregate of successfully processed claims (USD)</span>
            </div>
            <div style="display: flex; gap: 0.35rem; background: #f1f5f9; padding: 3px; border-radius: 8px;">
                <button class="trend-toggle-btn btn btn-sm active" data-trend="daily" style="font-size:0.7rem; font-weight:800; padding:0.25rem 0.6rem; border:none; background:#fff; border-radius:6px;">Daily</button>
                <button class="trend-toggle-btn btn btn-sm" data-trend="monthly" style="font-size:0.7rem; font-weight:800; padding:0.25rem 0.6rem; border:none; background:transparent; color:#64748b; border-radius:6px;">Monthly</button>
            </div>
        </div>
        <div style="height: 280px; position: relative;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- AI Recovery Insights Panel -->
    <div class="ai-insights-panel">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem; flex-wrap:wrap; gap:0.5rem;">
            <div style="display:flex; align-items:center;">
                <i class="fas fa-magic ai-sparkle-icon"></i>
                <h3 style="font-size:1.1rem; font-weight:800; margin:0; color:#ffffff;">AI Recovery Insights</h3>
            </div>
            <span style="background:rgba(96, 165, 250, 0.25); color:#93c5fd; font-size:0.7rem; font-weight:800; padding:0.2rem 0.6rem; border-radius:99px; text-transform:uppercase; border: 1px solid rgba(96, 165, 250, 0.4);">High Priority</span>
        </div>
        <div class="row align-items-center">
            <div class="col-md-8 mb-3 mb-md-0">
                <p style="font-size:0.95rem; font-weight:700; color:#e2e8f0; margin-bottom:0.75rem;">
                    "Estimated <span style="color:#60a5fa; font-weight:900;" id="ai_highlight_val">$0</span> in unclaimed FBA reimbursements identified from operational and return discrepancies."
                </p>
                <ul style="list-style:none; padding-left:0; margin-bottom:0;">
                    <li style="font-size:0.8rem; font-weight:700; color:#94a3b8; margin-bottom:0.4rem; display:flex; align-items:center; gap:6px;">
                        <i class="fas fa-check-circle" style="color:#34d399;"></i> Auto-detecting reconciliation gaps across active SKUs.
                    </li>
                    <li style="font-size:0.8rem; font-weight:700; color:#94a3b8; display:flex; align-items:center; gap:6px;">
                        <i class="fas fa-exclamation-triangle" style="color:#fbbf24;"></i> Action recommended: Ensure files are regularly uploaded to capture maximum recovery.
                    </li>
                </ul>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-primary btn-lg" id="bulk_file_claims_btn" style="padding:0.75rem 1.5rem; font-weight:800; border-radius:12px; font-size:0.9rem; width:100%; max-width:260px;">
                    Export Claims Report <i class="fas fa-download" style="margin-left:0.5rem;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Two Column Grid -->
    <div class="reimb-grid-2">
        <!-- Reason Analysis -->
        <div class="reimb-card">
            <h3 class="reimb-card-title" style="margin-bottom:1.5rem;">Reimbursement Reason Analysis</h3>
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="reimb-donut-wrap">
                        <canvas id="reasonsChart"></canvas>
                        <div class="reimb-donut-center">
                            <div class="total-val" id="reasons_total_val">$0</div>
                            <div class="total-lbl">Total</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div id="reasons_legend" style="display:flex; flex-direction:column; gap:0.5rem; font-size:0.75rem; font-weight:700; color:#475569;">
                        <!-- Dynamically filled -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Recovery Funnel -->
        <div class="reimb-card">
            <h3 class="reimb-card-title" style="margin-bottom:1.5rem;">Recovery Funnel</h3>
            <div style="display:flex; flex-direction:column; gap:1.25rem;" id="funnel_container">
                <div class="funnel-stage">
                    <div class="funnel-label-wrap">
                        <span>Inventory Loss Detected</span>
                        <span id="funnel_loss_detected">$0</span>
                    </div>
                    <div class="funnel-progress-bar">
                        <div class="funnel-fill" style="width: 100%;"></div>
                    </div>
                </div>
                <div class="funnel-stage">
                    <div class="funnel-label-wrap">
                        <span>Claim Submitted</span>
                        <span id="funnel_claim_submitted">$0</span>
                    </div>
                    <div class="funnel-progress-bar">
                        <div class="funnel-fill" id="funnel_submitted_bar" style="width: 90%;"></div>
                    </div>
                </div>
                <div class="funnel-stage">
                    <div class="funnel-label-wrap">
                        <span>Approved</span>
                        <span id="funnel_approved">$0</span>
                    </div>
                    <div class="funnel-progress-bar">
                        <div class="funnel-fill" id="funnel_approved_bar" style="width: 80%;"></div>
                    </div>
                </div>
                <div class="funnel-stage">
                    <div class="funnel-label-wrap">
                        <span>Cash Recovered</span>
                        <span id="funnel_recovered" style="color:#10b981;">$0</span>
                    </div>
                    <div class="funnel-progress-bar">
                        <div class="funnel-fill recovered" id="funnel_recovered_bar" style="width: 77%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Recovery Leaderboard -->
    <div class="reimb-card" style="margin-bottom:1.5rem;">
        <div class="reimb-card-header">
            <div>
                <span class="reimb-card-title" style="font-size:1.1rem; display:block;">Product Recovery Leaderboard</span>
                <span style="font-size:0.75rem; color:#64748b;">Top SKUs by total reimbursement value this period</span>
            </div>
            <button id="export_leaderboard" class="btn btn-outline-secondary btn-sm" style="font-weight:700;">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
        <div class="table-scroll">
            <table class="report-table" id="leaderboard_table">
                <thead>
                    <tr>
                        <th style="text-align:left !important;">Product Details</th>
                        <th>Units Recovered</th>
                        <th>Total Value</th>
                        <th>Recovery Efficiency</th>
                    </tr>
                </thead>
                <tbody id="leaderboard_body">
                    <tr><td colspan="4" class="text-center" style="padding:2rem; color:#94a3b8; font-weight:600;">Loading leaderboard...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Opportunity & Risk Score (Radial indicators) -->
    <div class="radial-grid">
        <div class="radial-card">
            <div class="radial-label">Recovery Efficiency</div>
            <div class="radial-svg-wrap">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="8" fill="none"></circle>
                    <circle cx="50" cy="50" r="40" stroke="#10b981" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="25.1" stroke-linecap="round"></circle>
                </svg>
                <span class="radial-svg-text">90%</span>
            </div>
            <span class="radial-subtext" style="color:#10b981;">Optimal performance</span>
        </div>
        <div class="radial-card">
            <div class="radial-label">Inventory Risk</div>
            <div class="radial-svg-wrap">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="8" fill="none"></circle>
                    <circle cx="50" cy="50" r="40" stroke="#f97316" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="175.8" stroke-linecap="round"></circle>
                </svg>
                <span class="radial-svg-text">30%</span>
            </div>
            <span class="radial-subtext" style="color:#64748b;">Low risk detected</span>
        </div>
        <div class="radial-card">
            <div class="radial-label">Financial Leakage</div>
            <div class="radial-svg-wrap">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="8" fill="none"></circle>
                    <circle cx="50" cy="50" r="40" stroke="var(--secondary)" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="221.0" stroke-linecap="round"></circle>
                </svg>
                <span class="radial-svg-text">12%</span>
            </div>
            <span class="radial-subtext" style="color:#64748b;">Minimal leakage</span>
        </div>
        <div class="radial-card">
            <div class="radial-label">Ops Health</div>
            <div class="radial-svg-wrap">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="8" fill="none"></circle>
                    <circle cx="50" cy="50" r="40" stroke="#10b981" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="12.5" stroke-linecap="round"></circle>
                </svg>
                <span class="radial-svg-text">95%</span>
            </div>
            <span class="radial-subtext" style="color:#10b981;">Superior health</span>
        </div>
    </div>

    <!-- Case Recovery Tracker (Table with DataTables) -->
    <div class="reimb-card">
        <div class="reimb-card-header">
            <div>
                <span class="reimb-card-title" style="font-size:1.1rem; display:block;">Case Recovery Tracker</span>
                <span style="font-size:0.75rem; color:#64748b;">Live feed of individual reimbursement claims and statuses</span>
            </div>
            <button id="export_cases" class="btn btn-outline-secondary btn-sm" style="font-weight:700;">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle" id="cases_table">
                <thead>
                    <tr>
                        <th>CASE / ORDER ID</th>
                        <th>REASON</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th>DATE</th>
                    </tr>
                </thead>
                <tbody id="cases_body">
                    <tr><td colspan="5" class="text-center" style="padding:2rem; color:#94a3b8; font-weight:600;">Loading claims...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="loading_overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner" style="width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top-color: var(--secondary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
    <p style="margin-top: 1rem; font-weight: 700; color: #1e293b;">Loading reimbursements...</p>
</div>

<script>
$(document).ready(function() {
    let trendChart = null;
    let reasonsChart = null;
    let trendMode = 'daily';
    let lastData = null;
    let casesDataTable = null;

    function formatCurrency(n) {
        return '$' + Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatNumber(n) {
        return Number(n).toLocaleString();
    }

    function showLoader() { $('#loading_overlay').css('display', 'flex'); }
    function hideLoader() { $('#loading_overlay').css('display', 'none'); }

    function setCmp(el, cmp) {
        const $el = $(el);
        if (!cmp || cmp.dir === 'none') {
            $el.removeClass('up down neutral').addClass('neutral').html('Stable');
            return;
        }
        const isUp = cmp.dir === 'up';
        const cls = isUp ? 'up' : 'down';
        const icon = isUp ? 'fa-arrow-up' : 'fa-arrow-down';
        const sign = isUp ? '+' : '-';
        $el.removeClass('up down neutral').addClass(cls)
            .html(`<i class="fas ${icon}"></i> ${sign}${cmp.pct}% vs LW`);
    }

    function renderKpis(kpis) {
        $('#kpi_total_reimb').text(formatCurrency(kpis.total_reimbursement));
        $('#kpi_units_recovered').text(formatNumber(kpis.units_recovered));
        $('#kpi_recovery_rate').text(kpis.recovery_rate + '%');
        $('#kpi_pending_value').text(formatCurrency(kpis.pending_claims));

        setCmp('#cmp_total_reimb', kpis.comparison.total_reimbursement);
        setCmp('#cmp_units_recovered', kpis.comparison.units_recovered);

        // Update AI Insights highlighted val
        $('#ai_highlight_val').text(formatCurrency(kpis.pending_claims));
    }

    function renderTrendChart(trend) {
        const el = document.getElementById('trendChart');
        if (!el || typeof Chart === 'undefined') return;
        if (trendChart) trendChart.destroy();

        const data = trend[trendMode] || { labels: [], data: [] };

        trendChart = new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.labels.length ? data.labels : ['—'],
                datasets: [
                    {
                        label: 'Reimbursed Amount ($)',
                        data: data.data.length ? data.data : [0],
                        borderColor: '#0051d5',
                        backgroundColor: 'rgba(0, 81, 213, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#0051d5',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        padding: 12,
                        callbacks: {
                            label: (ctx) => ` Reimbursed: ${formatCurrency(ctx.parsed.y)}`
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: '700' } } },
                    y: { grid: { color: 'rgba(148, 163, 184, 0.15)' }, beginAtZero: true, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    function renderReasonsChart(reasons, total) {
        const el = document.getElementById('reasonsChart');
        if (!el || typeof Chart === 'undefined') return;
        if (reasonsChart) reasonsChart.destroy();

        $('#reasons_total_val').text(formatCurrency(total));

        if (!reasons || reasons.length === 0) {
            reasonsChart = new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: { labels: ['No Data'], datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderWidth: 0 }] },
                options: { cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
            $('#reasons_legend').html('<div class="text-center" style="color:#94a3b8;">No data available</div>');
            return;
        }

        reasonsChart = new Chart(el.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: reasons.map(r => r.label),
                datasets: [{
                    data: reasons.map(r => r.amount),
                    backgroundColor: reasons.map(r => r.color),
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        padding: 12,
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${formatCurrency(ctx.parsed)} (${reasons[ctx.dataIndex].pct}%)`
                        }
                    }
                }
            }
        });

        let legendHtml = '';
        reasons.forEach(r => {
            legendHtml += `<div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                <div style="display:flex; align-items:center; min-width:0; flex:1;">
                    <span style="width:8px; height:8px; border-radius:50%; background:${r.color}; display:inline-block; margin-right:8px; flex-shrink:0;"></span>
                    <span style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${r.label}</span>
                </div>
                <span>${r.pct}%</span>
            </div>`;
        });
        $('#reasons_legend').html(legendHtml);
    }

    function renderFunnel(funnel) {
        $('#funnel_loss_detected').text(formatCurrency(funnel.detected));
        $('#funnel_claim_submitted').text(formatCurrency(funnel.submitted));
        $('#funnel_approved').text(formatCurrency(funnel.approved));
        $('#funnel_recovered').text(formatCurrency(funnel.recovered));

        // Calculate visual percentages
        const maxVal = Math.max(1, funnel.detected);
        const subPct = (funnel.submitted / maxVal) * 100;
        const appPct = (funnel.approved / maxVal) * 100;
        const recPct = (funnel.recovered / maxVal) * 100;

        $('#funnel_submitted_bar').css('width', subPct + '%');
        $('#funnel_approved_bar').css('width', appPct + '%');
        $('#funnel_recovered_bar').css('width', recPct + '%');
    }

    function renderLeaderboard(leaderboard) {
        if (!leaderboard || leaderboard.length === 0) {
            $('#leaderboard_body').html('<tr><td colspan="4" class="text-center" style="padding:2rem; color:#94a3b8; font-weight:600;">No product leaderboards found.</td></tr>');
            return;
        }

        let html = '';
        leaderboard.forEach(item => {
            html += `<tr>
                <td style="text-align:left !important;">
                    <div class="product-cell">
                        <div class="product-thumb"><i class="fas fa-box"></i></div>
                        <div>
                            <div class="name">${escapeHtml(item.title)}</div>
                            <div class="sku-lbl">SKU: ${escapeHtml(item.sku)} | ASIN: ${escapeHtml(item.asin)}</div>
                        </div>
                    </div>
                </td>
                <td>${formatNumber(item.units_recovered)}</td>
                <td style="font-weight:800; color:#0f172a;">${formatCurrency(item.total_value)}</td>
                <td>
                    <div style="display:flex; align-items:center; gap:8px; justify-content:center;">
                        <div class="progress" style="width:80px; height:6px; background:#e2e8f0; border-radius:99px; margin-bottom:0; overflow:hidden;">
                            <div class="progress-bar" role="progressbar" style="width:${item.efficiency}%; background:#10b981; border-radius:99px; height:100%; border:none;"></div>
                        </div>
                        <span style="font-size:0.75rem; font-weight:800; color:#10b981; min-width:32px; text-align:right;">${item.efficiency}%</span>
                    </div>
                </td>
            </tr>`;
        });
        $('#leaderboard_body').html(html);
    }

    function renderCasesTable(cases) {
        if (casesDataTable) {
            casesDataTable.destroy();
        }

        if (!cases || cases.length === 0) {
            $('#cases_body').html('<tr><td colspan="5" class="text-center" style="padding:2rem; color:#94a3b8; font-weight:600;">No cases tracked.</td></tr>');
            return;
        }

        let html = '';
        cases.forEach(c => {
            let statusBadge = '';
            const status = String(c.status).toLowerCase();
            if (status.indexOf('approved') !== -1 || status.indexOf('processed') !== -1 || status === 'approved') {
                statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill font-weight-700">Approved</span>';
            } else if (status.indexOf('pending') !== -1 || status.indexOf('review') !== -1) {
                statusBadge = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill font-weight-700">Pending Review</span>';
            } else {
                statusBadge = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill font-weight-700">${escapeHtml(c.status || 'Active')}</span>`;
            }

            html += `<tr>
                <td style="font-weight:700; color:var(--secondary);">${escapeHtml(c.case_id || '#N/A')}</td>
                <td>${escapeHtml(c.reason || 'Warehouse Damaged')}</td>
                <td style="font-weight:800; color:#0f172a;">${formatCurrency(c.amount)}</td>
                <td>${statusBadge}</td>
                <td>${c.report_date}</td>
            </tr>`;
        });

        $('#cases_body').html(html);

        // Reinitialize DataTable
        if ($.fn.DataTable) {
            casesDataTable = $('#cases_table').DataTable({
                pageLength: 10,
                ordering: true,
                searching: true,
                lengthChange: true,
                info: true,
                language: {
                    paginate: {
                        next: '<i class="fas fa-chevron-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>'
                    }
                }
            });
        }
    }

    function escapeHtml(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function loadData() {
        const customerId = $('#filter_customer').val() || '';
        const from = $('#filter_from').val();
        const to = $('#filter_to').val();

        showLoader();
        $.ajax({
            url: '<?php echo BASE_URL; ?>api/reimbursements_data.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            dataType: 'json',
            success: function(res) {
                hideLoader();
                if (res.error) {
                    alert(res.error);
                    return;
                }
                lastData = res;
                renderKpis(res.kpis || {});
                renderTrendChart(res.trend || {});
                renderReasonsChart(res.reasons || [], (res.kpis || {}).total_reimbursement || 0);
                renderFunnel(res.funnel || {});
                renderLeaderboard(res.leaderboard || []);
                renderCasesTable(res.cases || []);
            },
            error: function() {
                hideLoader();
                alert('Failed to load reimbursement data.');
            }
        });
    }

    function initDates() {
        $.getJSON('<?php echo BASE_URL; ?>api/get_data_range.php', function(ranges) {
            const ops = ranges.ops || {};
            const trans = ranges.trans || {};
            let from = '2026-01-01';
            let to = '2026-02-28';

            if (ops.min_date && ops.min_date !== '0000-00-00' && ops.max_date && ops.max_date !== '0000-00-00') {
                from = ops.min_date;
                to = ops.max_date;
            } else if (trans.min_date) {
                from = String(trans.min_date).split(' ')[0];
                to = String(trans.max_date || trans.min_date).split(' ')[0];
            }

            $('#filter_from').val(from);
            $('#filter_to').val(to);
            loadData();
        }).fail(function() {
            $('#filter_from').val('2026-01-01');
            $('#filter_to').val('2026-02-28');
            loadData();
        });
    }

    $('#apply_filters').on('click', loadData);

    $('.trend-toggle-btn').on('click', function() {
        $('.trend-toggle-btn').removeClass('active').css('background', 'transparent').css('color', '#64748b');
        $(this).addClass('active').css('background', '#fff').css('color', '#0f172a');
        trendMode = $(this).data('trend');
        if (lastData) renderTrendChart(lastData.trend || {});
    });

    $('#bulk_file_claims_btn').on('click', function() {
        if (!lastData || !lastData.cases || lastData.cases.length === 0) {
            alert('No claims to export.');
            return;
        }
        $('#export_cases').click();
    });

    $('#export_leaderboard').on('click', function() {
        if (!lastData || !lastData.leaderboard || lastData.leaderboard.length === 0) {
            alert('No leaderboard data to export.');
            return;
        }
        const headers = ['SKU', 'Title', 'ASIN', 'Units Recovered', 'Total Value ($)', 'Efficiency (%)'];
        const lines = [headers.join(',')];
        lastData.leaderboard.forEach(p => {
            lines.push([
                '"' + p.sku + '"',
                '"' + p.title.replace(/"/g, '""') + '"',
                '"' + p.asin + '"',
                p.units_recovered,
                p.total_value,
                p.efficiency
            ].join(','));
        });
        const blob = new Blob([lines.join('\n')], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'reimbursement_product_leaderboard.csv';
        a.click();
    });

    $('#export_cases').on('click', function() {
        if (!lastData || !lastData.cases || lastData.cases.length === 0) {
            alert('No cases to export.');
            return;
        }
        const headers = ['Case / Order ID', 'Reason', 'Amount ($)', 'Status', 'Date'];
        const lines = [headers.join(',')];
        lastData.cases.forEach(c => {
            lines.push([
                '"' + (c.case_id || '') + '"',
                '"' + (c.reason || '').replace(/"/g, '""') + '"',
                c.amount,
                '"' + (c.status || '') + '"',
                c.report_date
            ].join(','));
        });
        const blob = new Blob([lines.join('\n')], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'reimbursements_case_tracker.csv';
        a.click();
    });

    initDates();
});
</script>

<?php include '../../includes/footer.php'; ?>
