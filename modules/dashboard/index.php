<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$customers = get_all_customers();
$active_tab = $_GET['tab'] ?? 'kpi';

$page_title = "Diamond OS Dashboard";
$page_subtitle = "Real-time Amazon Business Intelligence & Analytics";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
/* Global Premium Styles */
:root {
    --card-shadow: 0 10px 30px rgba(0,0,0,0.04);
    --hover-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.kpi-card { 
    background: #ffffff;
    border-radius: 16px;
    padding: 1.25rem !important;
    border: none;
    box-shadow: var(--card-shadow);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 140px;
    opacity: 0;
    transform: translateY(20px);
}

.kpi-card.visible { opacity: 1; transform: translateY(0); }
.kpi-card:hover { transform: translateY(-8px); box-shadow: var(--hover-shadow); }

.kpi-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }

.cmp-tag {
    font-size: 0.65rem; font-weight: 800; padding: 4px 10px; border-radius: 50px;
    display: flex; align-items: center; gap: 4px; color: white;
}
.cmp-tag.up { background: #22c55e; }
.cmp-tag.down { background: #ef4444; }
.cmp-tag.none { background: #94a3b8; }

.kpi-icon { 
    width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; color: #fff;
}

/* Specific Card Themes based on Screenshot 2 - Premium Gradient Style */
.kpi-card.blue-theme { 
    background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%); 
    border-bottom: 3px solid #3b82f6;
}
.kpi-card.blue-theme .kpi-icon { background: #3b82f6; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3); }

.kpi-card.indigo-theme { 
    background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 100%); 
    border-bottom: 3px solid #6366f1;
}
.kpi-card.indigo-theme .kpi-icon { background: #6366f1; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); }

.kpi-card.teal-theme { 
    background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 100%); 
    border-bottom: 3px solid #14b8a6;
}
.kpi-card.teal-theme .kpi-icon { background: #14b8a6; box-shadow: 0 8px 20px rgba(20, 184, 166, 0.3); }

.kpi-card.green-theme { 
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); 
    border-bottom: 3px solid #22c55e;
}
.kpi-card.green-theme .kpi-icon { background: #22c55e; box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3); }

.kpi-card.emerald-theme { 
    background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%); 
    border-bottom: 3px solid #10b981;
}
.kpi-card.emerald-theme .kpi-icon { background: #10b981; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }

.kpi-card.rose-theme { 
    background: linear-gradient(135deg, #fff1f2 0%, #ffffff 100%); 
    border-bottom: 3px solid #f43f5e;
}
.kpi-card.rose-theme .kpi-icon { background: #f43f5e; box-shadow: 0 8px 20px rgba(244, 63, 94, 0.3); }

.kpi-card.purple-theme { 
    background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%); 
    border-bottom: 3px solid #a855f7;
}
.kpi-card.purple-theme .kpi-icon { background: #a855f7; box-shadow: 0 8px 20px rgba(168, 85, 247, 0.3); }

.kpi-card.yellow-theme { 
    background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%); 
    border-bottom: 3px solid #f59e0b;
}
.kpi-card.yellow-theme .kpi-icon { background: #f59e0b; box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3); }

.kpi-card.cyan-theme { 
    background: linear-gradient(135deg, #ecfeff 0%, #ffffff 100%); 
    border-bottom: 3px solid #06b6d4;
}
.kpi-card.cyan-theme .kpi-icon { background: #06b6d4; box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3); }

.kpi-body h3 { font-size: 1.65rem; font-weight: 900; margin: 0.25rem 0; color: #1e293b; letter-spacing: -0.02em; }
.kpi-body p { font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: capitalize; letter-spacing: 0.05em; margin: 0; line-height: 1.2; }

.kpi-footer { 
    font-size: 0.85rem; font-weight: 800; color: #1e293b; margin-top: 0; 
    display: flex; align-items: center; gap: 6px; 
    background: rgba(241, 245, 249, 0.5); padding: 6px 10px; border-radius: 8px;
    width: fit-content; border: 1px solid rgba(226, 232, 240, 0.8);
}
.kpi-footer i { opacity: 0.8; font-size: 0.85rem; }

/* Financial P&L Styles */
.pl-card { border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.pl-header { padding: 1.5rem 2rem; }
.pl-header h3 { margin: 0; color: #fff; font-weight: 800; text-transform: capitalize; letter-spacing: 0.05em; font-size: 1rem; }
.pl-row { display: flex; justify-content: space-between; padding: 1.25rem 0; border-bottom: 1px solid #f1f5f9; align-items: center; }
.pl-row:last-child { border-bottom: none; }
.pl-row label { font-weight: 700; color: #334155; font-size: 0.95rem; }
.pl-row span { font-weight: 800; font-size: 1.1rem; }
.pl-row.sub { padding: 0.85rem 0 0.85rem 1.5rem; font-size: 0.85rem; border-bottom: 1px dashed #f1f5f9; }
.pl-row.sub label { color: #64748b; font-weight: 600; }
.pl-row.total { padding: 1.5rem; margin-top: 1rem; border-radius: 12px; }

.expense-progress { height: 6px; background: #f1f5f9; border-radius: 10px; width: 80px; overflow: hidden; margin-top: 4px; }
.expense-progress-bar { height: 100%; border-radius: 10px; transition: width 1s cubic-bezier(0.16, 1, 0.3, 1); }

/* Product Gallery Styles */
#product_list { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
.product-item {
    background: #ffffff; border-radius: 16px; padding: 1.5rem; position: relative; border: 1px solid #f1f5f9;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;
}
.product-item:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border-color: #6366f1; }
.product-item::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #6366f1, #a855f7); opacity: 0; transition: opacity 0.3s; }
.product-item:hover::before { opacity: 1; }

.product-rank {
    position: absolute; top: 1rem; right: 1rem; width: 32px; height: 32px; background: #f1f5f9; color: #64748b; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; border: 2px solid #fff; z-index: 2;
}
.product-item:nth-child(1) .product-rank { background: #fef3c7; color: #92400e; border-color: #fbbf24; }
.product-item:nth-child(2) .product-rank { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
.product-item:nth-child(3) .product-rank { background: #ffedd5; color: #9a3412; border-color: #fdba74; }

.product-sku-tag { font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: capitalize; letter-spacing: 0.05em; margin-bottom: 0.75rem; display: block; }
.product-card-title { font-size: 0.95rem; font-weight: 700; color: #1e293b; line-height: 1.5; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; min-height: 4.5rem; }
.product-metrics-pill { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; }
.metric-col { text-align: center; }
.metric-col label { display: block; font-size: 0.6rem; font-weight: 700; color: #94a3b8; text-transform: capitalize; margin-bottom: 0.25rem; }
.metric-col span { display: block; font-size: 0.9rem; font-weight: 800; color: #0f172a; }
.metric-col.revenue span { color: #6366f1; }

/* Premium Analysis Table Styles */
.analysis-table-container { background: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9; overflow: auto; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
.analysis-table { width: 100%; border-collapse: collapse !important; border-spacing: 0; }
.analysis-table th {
    background: #f8fafc; padding: 12px 16px; font-size: 0.7rem; font-weight: 800; text-transform: capitalize; letter-spacing: 0.05em; color: #64748b;
    border: 1px solid #e2e8f0; white-space: nowrap; position: sticky; top: 0; z-index: 10;
    vertical-align: middle; text-align: center;
}
.analysis-table th.group-header { 
    background: #f1f5f9; color: #1e293b; font-size: 0.75rem; 
    border-bottom: 2px solid #e2e8f0; font-weight: 900;
}
.analysis-table th:first-child { border-top-left-radius: 16px; }
.analysis-table th:last-child { border-top-right-radius: 16px; }

.analysis-table th {
    text-align: center !important;
    vertical-align: middle !important;
    font-weight: 800 !important;
    text-transform: capitalize;
    letter-spacing: 0.05em;
    padding: 12px 8px !important;
}

/* Group specific colors - Premium Gradient Style */
.analysis-table th.sales-metrics-group, 
.analysis-table th.sales-group { 
    background: linear-gradient(180deg, #4f46e5 0%, #3730a3 100%) !important; 
    color: #fff !important; border-color: #312e81 !important; 
}

.analysis-table th.ads-spend-group { 
    background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%) !important; 
    color: #fff !important; border-color: #991b1b !important; 
}

.analysis-table th.acos-group { 
    background: linear-gradient(180deg, #f97316 0%, #c2410c 100%) !important; 
    color: #fff !important; border-color: #9a3412 !important; 
}

.analysis-table th.ad-dep-group { 
    background: linear-gradient(180deg, #8b5cf6 0%, #6d28d9 100%) !important; 
    color: #fff !important; border-color: #5b21b6 !important; 
}

.analysis-table th.traffic-sess-group { 
    background: linear-gradient(180deg, #0ea5e9 0%, #0369a1 100%) !important; 
    color: #fff !important; border-color: #075985 !important; 
}

.analysis-table th.conv-group { 
    background: linear-gradient(180deg, #10b981 0%, #047857 100%) !important; 
    color: #fff !important; border-color: #065f46 !important; 
}

.analysis-table th.refund-group { 
    background: linear-gradient(180deg, #f43f5e 0%, #be123c 100%) !important; 
    color: #fff !important; border-color: #9f1239 !important; 
}

.analysis-table th.buy-box-group { 
    background: linear-gradient(180deg, #475569 0%, #1e293b 100%) !important; 
    color: #fff !important; border-color: #0f172a !important; 
}

.analysis-table td { 
    padding: 1.25rem 1rem; font-size: 0.9rem; color: #1e293b; 
    border-bottom: 1px solid #f1f5f9; vertical-align: middle; 
    text-align: center !important; 
}
.analysis-table tr:hover td { background: #f8fafc; }

.status-pill { padding: 4px 10px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: capitalize; }
.status-pill.star { background: #dcfce7; color: #15803d; }
.status-pill.risk { background: #fee2e2; color: #b91c1c; }
.status-pill.ad-dep { background: #ffedd5; color: #9a3412; }

.mini-bar-container { width: 50px; height: 5px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin-top: 6px; }
.mini-bar-fill { height: 100%; border-radius: 10px; }

/* Trend Table & Chart Tab Enhancements */
.trend-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.trend-table th { 
    background: linear-gradient(135deg, var(--bg-main) 0%, var(--accent-soft) 100%); color: var(--primary); 
    padding: 1rem; font-size: 0.75rem; text-transform: capitalize; letter-spacing: 0.05em;
    border-bottom: 2px solid var(--primary-light); text-align: right;
}
.trend-table th:first-child { border-top-left-radius: 12px; text-align: left !important; }
.trend-table th:last-child { border-top-right-radius: 12px; }
.trend-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #475569; text-align: right; }
.trend-table td:first-child { text-align: left !important; }
.trend-table td.highlight-col { background: #f8fafc; color: #1e293b; font-weight: 800; }
.trend-table tr:hover td { background: #f1f5f9; }

.chart-tabs { display: flex; gap: 0.75rem; padding: 0.5rem; background: #f1f5f9; border-radius: 50px; width: fit-content; margin-bottom: 2rem; }
.chart-tab-btn { 
    padding: 8px 20px; border-radius: 50px; cursor: pointer; font-size: 0.75rem; font-weight: 800; 
    color: #64748b; transition: all 0.3s; border: none; background: transparent; text-transform: capitalize;
}
.chart-tab-btn:hover { color: #1e293b; background: rgba(255,255,255,0.5); }
.chart-tab-btn.active { 
    background: #0f172a; color: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
    transform: scale(1.02);
}

.section-title { 
    display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; 
    font-size: 1.25rem; font-weight: 800; color: #0f172a; 
}
/* Ensure Profit & Loss tables under financial tab are fully structured grids with 33.33% equal column widths */
.pl-section-card table.analysis-table {
  border-collapse: collapse !important;
  width: 100% !important;
  border-top: 1px solid rgba(226, 232, 240, 0.6) !important;
  border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
  border-left: none !important;
  border-right: none !important;
}

.pl-section-card table.analysis-table th,
.pl-section-card table.analysis-table td {
  position: static !important; /* Disable broken sticky positioning */
  left: auto !important; /* Reset sticky left offset */
  width: 33.33% !important;
  max-width: 33.33% !important;
  min-width: 33.33% !important;
  border-top: 1px solid rgba(226, 232, 240, 0.6) !important;
  border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
  border-left: none !important;
  border-right: none !important;
  padding: 12px 16px !important;
  box-sizing: border-box !important;
}

/* Align text to left in the first column, numbers to right in second/third columns */
.pl-section-card table.analysis-table th:first-child,
.pl-section-card table.analysis-table td:first-child {
  text-align: left !important;
}
.pl-section-card table.analysis-table th:nth-child(2),
.pl-section-card table.analysis-table td:nth-child(2),
.pl-section-card table.analysis-table th:nth-child(3),
.pl-section-card table.analysis-table td:nth-child(3) {
  text-align: right !important;
}
.pl-section-card table.analysis-table td:first-child > div {
  justify-content: flex-start !important;
}
</style>

<!-- Filter Section -->
<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
    <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 280px;">
            <label style="font-weight: 700; color: #4b5563; margin-bottom: 8px; display: block; font-size: 0.75rem; text-transform: capitalize;">Account Selection</label>
            <select id="filter_customer" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #e5e7eb;" <?php echo (($_SESSION['role'] ?? '') === 'customer') ? 'disabled' : ''; ?>>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <option value="">All Amazon Profiles</option>
                <?php endif; ?>
                <?php $customers->data_seek(0); while ($row = $customers->fetch_assoc()): ?>
                <?php 
                    $selected = (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) == $row['id']) ? 'selected' : '';
                    if (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) != $row['id']) continue;
                ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($row['customer_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <?php if (($_SESSION['role'] ?? '') === 'customer'): ?>
                <input type="hidden" id="customer_id_hidden" value="<?php echo $_SESSION['customer_id'] ?? 0; ?>">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label style="font-weight: 700; color: #4b5563; margin-bottom: 8px; display: block; font-size: 0.75rem; text-transform: capitalize;">Date Range</label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="date" id="filter_from" value="" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                <span style="color: #9ca3af;">to</span>
                <input type="date" id="filter_to" value="" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
            </div>
        </div>
        <button id="apply_filters" class="btn btn-primary" style="height: 42px; font-weight: 800; padding: 0 24px;">
            <i class="fas fa-sync-alt"></i> REFRESH ANALYSIS
        </button>
        <button id="export_csv" class="btn btn-outline" style="height: 42px; font-weight: 600; padding: 0 24px;">
            <i class="fas fa-file-csv"></i> EXPORT CSV
        </button>
    </div>
</div>


<!-- Loading State -->
<div id="loading_overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner" style="width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top-color: #bef264; border-radius: 50%; animation: spin 1s linear infinite;"></div>
    <p style="margin-top: 1rem; font-weight: 700; color: #064e3b;">Syncing Amazon Reports...</p>
</div>

<!-- KPI TAB -->
<div id="tab_kpi" class="tab-content" <?php echo ($active_tab !== 'kpi') ? 'style="display: none;"' : ''; ?>>
    
    <!-- Revenue Breakdown Section -->
    <div style="text-transform: capitalize; font-size: 1.15rem; font-weight: 800; color: #475569; letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem 0.5rem; display: flex; align-items: center; gap: 8px;"><i class="fas fa-chart-line" style="color: var(--primary-light);"></i> Revenue Breakdown</div>
    <div class="kpi-grid">
        <div class="card kpi-card blue-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-money-bill-wave"></i><span id="kpi_sales_sub">Total Revenue</span></div>
            <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_sales">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_sales" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card emerald-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-seedling"></i><span id="kpi_organic_sub">Organic Sales</span></div>
            <div class="kpi-icon"><i class="fas fa-leaf"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_organic">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_organic" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card purple-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-bullhorn"></i><span id="kpi_ad_sales_sub">Ad Sales</span></div>
            <div class="kpi-icon"><i class="fas fa-ad"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_ad_sales">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_ad_sales" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card green-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-tachometer-alt"></i><span id="kpi_dsr_sub">Daily Sales Rate</span></div>
            <div class="kpi-icon"><i class="fas fa-calendar-day"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_dsr">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_dsr" class="cmp-tag"></span>
        </div>
        </div>
    </div>
    
    <!-- Advertising Performance Section -->
    <div style="text-transform: capitalize; font-size: 1.15rem; font-weight: 800; color: #475569; letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem 0.5rem; display: flex; align-items: center; gap: 8px;"><i class="fas fa-bullseye" style="color: var(--primary-light);"></i> Advertising Performance</div>
    <div class="kpi-grid">
        <div class="card kpi-card rose-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-file-invoice-dollar"></i><span id="kpi_spend_sub">Ad Spend</span></div>
            <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_spend">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_spend" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card cyan-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-reply-all"></i><span id="kpi_roas_sub">ROAS</span></div>
            <div class="kpi-icon"><i class="fas fa-chart-area"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_roas">0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_roas" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card yellow-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-stethoscope"></i><span id="kpi_acos_sub">ACOS</span></div>
            <div class="kpi-icon"><i class="fas fa-percent"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_acos">0.00%</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_acos" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card purple-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-calculator"></i><span id="kpi_tacos_sub">TACOS</span></div>
            <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_tacos">0.00%</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_tacos" class="cmp-tag"></span>
        </div>
        </div>
    </div>
    
    <!-- Traffic and Conversion Section -->
    <div style="text-transform: capitalize; font-size: 1.15rem; font-weight: 800; color: #475569; letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem 0.5rem; display: flex; align-items: center; gap: 8px;"><i class="fas fa-users" style="color: var(--primary-light);"></i> Traffic And Conversion</div>
    <div class="kpi-grid">
        <div class="card kpi-card blue-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-globe"></i><span id="kpi_sessions_t_sub">Sessions</span></div>
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_sessions_t">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_sessions_t" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card indigo-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-check-circle"></i><span id="kpi_orders_sub">Orders</span></div>
            <div class="kpi-icon"><i class="fas fa-box"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_orders">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_orders" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card teal-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-shopping-cart"></i><span id="kpi_units_sub">Units Sold</span></div>
            <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_units">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_units" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card teal-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-percentage"></i><span id="kpi_conversion_sub">Conversion Rate</span></div>
            <div class="kpi-icon"><i class="fas fa-rocket"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_conversion">0.00%</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_conv" class="cmp-tag"></span>
        </div>
        </div>
    </div>
    <!-- KPI Trend Comparison (Moved Up) -->
    <div class="card">
        <div class="section-title"><i class="fas fa-history"></i> <span>KPI Trend - 3-Month Comparison</span></div>
        <div class="table-container">
            <table class="trend-table">
                <thead><tr id="trend_head"></tr></thead>
                <tbody id="trend_body"></tbody>
            </table>
        </div>
    </div>

    <div class="card" style="padding-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e293b;">Daily performance trends</h3>
                <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: #64748b; font-weight: 600;">Pick a metric to inspect day by day</p>
            </div>
            <div class="chart-tabs" style="margin-bottom: 0;">
                <div class="chart-tab-btn active" data-chart="sales">Sales</div>
                <div class="chart-tab-btn" data-chart="units_orders">Orders vs Units</div>
                <div class="chart-tab-btn" data-chart="page_views">Page Views</div>
                <div class="chart-tab-btn" data-chart="sessions">Sessions</div>
                <div class="chart-tab-btn" data-chart="conversion">Conversion</div>
                <div class="chart-tab-btn" data-chart="refund_rate">Refunds</div>
            </div>
        </div>
        <div style="height: 480px;"><canvas id="mainChart"></canvas></div>
    </div>
</div>

<!-- SALES & TRAFFIC TAB -->
<div id="tab_traffic" class="tab-content" <?php echo ($active_tab !== 'traffic') ? 'style="display: none;"' : ''; ?>>
    <div class="kpi-grid">

        <div class="card kpi-card indigo-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-file-alt"></i><span id="">Product Detail Views</span></div>
            <div class="kpi-icon"><i class="fas fa-eye"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_pv_t">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_pv_t" class="cmp-tag"></span>
        </div>
        </div>
        <div class="card kpi-card teal-theme">
            <div class="kpi-header"><div class="kpi-icon"><i class="fas fa-rocket"></i></div></div>
            <div class="kpi-body"><h3><span id="kpi_conv_t">0.00%</span></h3></div>
            <div class="kpi-footer"><i class="fas fa-percentage"></i><span>Units / Session</span></div>
        </div>
        <div class="card kpi-card green-theme">
            <div class="kpi-header"><div class="kpi-icon"><i class="fas fa-box-open"></i></div></div>
            <div class="kpi-body"><h3><span id="kpi_buybox_t">0%</span></h3></div>
            <div class="kpi-footer"><i class="fas fa-shopping-bag"></i><span>Market Visibility</span></div>
        </div>
        <div class="card kpi-card rose-theme">
            <div class="kpi-header"><div class="kpi-icon"><i class="fas fa-mobile-alt"></i></div></div>
            <div class="kpi-body"><h3><span id="kpi_mobile_t">0%</span></h3></div>
            <div class="kpi-footer"><i class="fas fa-app-store"></i><span>App vs Browser Traffic</span></div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 2rem;">
        <div class="section-title"><i class="fas fa-chart-area"></i> <span>Traffic vs Page Views Trend</span></div>
        <div style="height: 480px;"><canvas id="trafficTrendChart"></canvas></div>
    </div>

    <div class="card">
        <div class="section-title"><i class="fas fa-table"></i> <span>Daily Traffic Breakdown</span></div>
        <div class="analysis-table-container">
            <table id="traffic_daily_table" class="analysis-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="background: #f8fafc !important; color: #1e293b !important; border: 1px solid #e2e8f0;">DATE</th>
                        <th colspan="2" class="group-header traffic-sess-group" style="border: 1px solid #075985;">TRAFFIC VOLUME</th>
                        <th colspan="1" class="group-header buy-box-group" style="border: 1px solid #0f172a;">MARKET</th>
                        <th colspan="2" class="group-header sales-metrics-group" style="border: 1px solid #312e81;">ACTIVITY</th>
                        <th colspan="1" class="group-header conv-group" style="border: 1px solid #065f46;">PERFORMANCE</th>
                    </tr>
                    <tr>
                        <th class="traffic-sess-group" style="opacity: 0.9; border: 1px solid #075985;">Sessions</th>
                        <th class="traffic-sess-group" style="opacity: 0.9; border: 1px solid #075985;">Page Views</th>
                        <th class="buy-box-group" style="opacity: 0.9; border: 1px solid #0f172a;">Buy Box %</th>
                        <th class="sales-metrics-group" style="opacity: 0.9; border: 1px solid #312e81;">Units</th>
                        <th class="sales-metrics-group" style="opacity: 0.9; border: 1px solid #312e81;">Orders</th>
                        <th class="conv-group" style="opacity: 0.9; border: 1px solid #065f46;">Conversion %</th>
                    </tr>
                </thead>
                <tbody id="traffic_daily_body">
                    <tr><td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">Syncing traffic data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- FINANCIAL TAB -->
<div id="tab_financial" class="tab-content" <?php echo ($active_tab !== 'financial') ? 'style="display: none;"' : ''; ?>>
    
    <!-- P&L Header Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 900; color: #0f172a; margin: 0;">Profit & Loss Analysis</h2>
            <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: #64748b; font-weight: 600;">Complete waterfall breakdown of your shop parameters</p>
        </div>
        <div style="text-align: right; display: flex; align-items: center; gap: 1rem;">
            <span style="font-size: 0.75rem; background: #f0fdf4; color: #166534; padding: 6px 14px; border-radius: 50px; font-weight: 800; border: 1px solid #bbf7d0;">
                <i class="fas fa-check-circle"></i> Verified Against Settlement
            </span>
            <span id="pl_date_range" style="font-size: 0.75rem; background: #f1f5f9; color: #475569; padding: 6px 14px; border-radius: 50px; font-weight: 800; border: 1px solid #e2e8f0;">
                -- --, 2026
            </span>
        </div>
    </div>

    <!-- Card 1: Gross Revenue -->
    <div class="card pl-section-card" style="border-radius: 20px; border: 1px solid #e2e8f0; background: #fff; margin-bottom: 2rem; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <!-- Header Block -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #1e293b;">Gross Revenue</h3>
                    <p style="margin: 2px 0 0 0; font-size: 0.75rem; color: #64748b; font-weight: 600;">All sales channels before deductions</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <span style="font-size: 0.75rem; background: #f1f5f9; color: #475569; padding: 6px 14px; border-radius: 50px; font-weight: 800; border: 1px solid #e2e8f0;">
                    100% Total
                </span>
                <h2 id="pl_rev_header_val" style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0; letter-spacing: -0.02em;">$0.00</h2>
            </div>
        </div>
        <!-- Table Body -->
        <div style="padding: 0; overflow-x: auto;">
            <table class="analysis-table" style="width: 100%; border-collapse: collapse !important; margin: 0;">
                <thead>
                    <tr>
                        <th style="width: 33.33% !important; text-align: left !important; padding: 1.25rem 2rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Source Channel</th>
                        <th style="width: 33.33% !important; text-align: right !important; padding: 1.25rem 2rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Revenue Value</th>
                        <th style="width: 33.33% !important; text-align: right !important; padding: 1.25rem 2rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Contribution %</th>
                    </tr>
                </thead>
                <tbody id="pl_revenue_rows">
                    <!-- populated by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Card 2: Expenses & Fees -->
    <div class="card pl-section-card" style="border-radius: 20px; border: 1px solid #e2e8f0; background: #fff; margin-bottom: 2rem; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <!-- Header Block -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%); color: #f43f5e; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.1);">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #1e293b;">Expenses & Fees</h3>
                    <p style="margin: 2px 0 0 0; font-size: 0.75rem; color: #64748b; font-weight: 600;">Deductions, fees, and operational burn</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <span id="pl_fees_share_badge" style="font-size: 0.75rem; background: #fff1f2; color: #e11d48; padding: 6px 14px; border-radius: 50px; font-weight: 800; border: 1px solid #fecdd3;">
                    0.0% of Revenue
                </span>
                <h2 id="pl_fees_header_val" style="font-size: 1.75rem; font-weight: 900; color: #e11d48; margin: 0; letter-spacing: -0.02em;">$0.00</h2>
            </div>
        </div>
        <!-- Table Body -->
        <div style="padding: 0; overflow-x: auto;">
            <table class="analysis-table" style="width: 100%; border-collapse: collapse !important; margin: 0;">
                <thead>
                    <tr>
                        <th style="width: 33.33% !important; text-align: left !important; padding: 1.25rem 2rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Expense Classification</th>
                        <th style="width: 33.33% !important; text-align: right !important; padding: 1.25rem 2rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Amount</th>
                        <th style="width: 33.33% !important; text-align: right !important; padding: 1.25rem 2rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Revenue Share</th>
                    </tr>
                </thead>
                <tbody id="pl_expenses_rows">
                    <!-- populated by JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Card 3: Net Profit Analysis -->
    <div class="card pl-section-card" style="border-radius: 20px; border: 1px solid #e2e8f0; background: #fff; margin-bottom: 2rem; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <!-- Header Block -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #e6fcf5 0%, #c3fae8 100%); color: #0ca678; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(12, 166, 120, 0.1);">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #1e293b;">Net Profit Analysis</h3>
                    <p style="margin: 2px 0 0 0; font-size: 0.75rem; color: #64748b; font-weight: 600;">Final bottom line and performance metrics</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <span id="pl_net_margin_badge" style="font-size: 0.75rem; background: #e6fcf5; color: #0ca678; padding: 6px 14px; border-radius: 50px; font-weight: 800; border: 1px solid #c3fae8;">
                    0.0% Margin
                </span>
                <h2 id="pl_net_header_val" style="font-size: 1.75rem; font-weight: 900; color: #0ca678; margin: 0; letter-spacing: -0.02em;">$0.00</h2>
            </div>
        </div>
        
        <!-- Table & Efficiency Score Layout -->
        <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 2rem; padding: 2rem;">
            <!-- Left: Breakdown Table -->
            <div style="overflow-x: auto;">
                <table class="analysis-table" style="width: 100%; border-collapse: collapse !important; margin: 0;">
                    <thead>
                        <tr>
                            <th style="width: 33.33% !important; text-align: left !important; padding: 1.25rem 1rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Key Performance Indicator</th>
                            <th style="width: 33.33% !important; text-align: right !important; padding: 1.25rem 1rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Current Period</th>
                            <th style="width: 33.33% !important; text-align: right !important; padding: 1.25rem 1rem !important; background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 0.95rem !important;">Revenue Share</th>
                        </tr>
                    </thead>
                    <tbody id="pl_net_profit_rows">
                        <!-- populated by JS -->
                    </tbody>
                </table>
            </div>
            
            <!-- Right: Efficiency Card -->
            <div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-radius: 16px; border: 1.5px solid #a7f3d0; padding: 2rem; position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 10px 25px rgba(12, 166, 120, 0.05);">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(12, 166, 120, 0.06); border-radius: 50%; pointer-events: none;"></div>
                <div>
                    <p style="font-size: 0.7rem; color: #065f46; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">EFFICIENCY SCORE</p>
                    <div style="display: flex; align-items: baseline; gap: 4px;">
                        <span id="pl_efficiency_score" style="font-size: 4rem; font-weight: 900; line-height: 1; color: #047857;">94</span>
                        <span style="font-size: 1.5rem; color: #065f46; font-weight: 700; opacity: 0.6;">/100</span>
                    </div>
                </div>
                <div style="margin-top: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: #065f46;">Performance Status</span>
                        <span id="pl_efficiency_status" style="font-size: 0.75rem; background: #10b981; color: white; padding: 4px 10px; border-radius: 50px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em;">EXCELLENT</span>
                    </div>
                    <p id="pl_efficiency_desc" style="font-size: 0.75rem; color: #065f46; font-weight: 600; opacity: 0.8; margin: 0; line-height: 1.4;">Your sales are highly optimized with controlled advertising spillover.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 2rem;">
        <div class="section-title"><i class="fas fa-boxes" style="background: #6366f1; color: white;"></i> <span>SKU Wise P&L Performance</span></div>
        <div class="analysis-table-container" style="max-height: 800px;">
            <table id="sku_pl_table" class="analysis-table">
                <thead>
                    <tr>
                        <th style="background: #f8fafc !important; border: 1px solid #e2e8f0; text-align: center;">Rank</th>
                        <th style="background: #f8fafc !important; border: 1px solid #e2e8f0; text-align: left;">Seller SKU</th>
                        <th style="background: #f8fafc !important; border: 1px solid #e2e8f0; text-align: right;">Units Sold</th>
                        <th style="background: #eff6ff !important; border: 1px solid #e2e8f0; text-align: right;">Revenue</th>
                        <th style="background: #f0fdf4 !important; border: 1px solid #e2e8f0; text-align: right;">Net Profit</th>
                        <th style="background: #f0fdf4 !important; border: 1px solid #e2e8f0; text-align: right;">Net Profit%</th>
                        <th style="background: #eff6ff !important; border: 1px solid #e2e8f0; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody id="sku_pl_body">
                    <tr><td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">Loading SKU data...</td></tr>
                </tbody>
                <tfoot id="sku_pl_foot" style="background: #f8fafc; border-top: 2px solid #e2e8f0; font-weight: 800;">
                    <!-- JS Populated -->
                </tfoot>
            </table>
        </div>
    </div>

    <!-- SECTION 3: Geographic Sales Distribution (Full Width Premium Map & SKU Table) -->
    <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="padding: 1.25rem 2rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);"><i class="fas fa-map-marked-alt"></i></div>
                <div>
                    <span style="font-weight: 800; color: #1e293b; font-size: 1.1rem; display: block;">Geographic Sales Distribution</span>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Interactive state-level performance heatmap with dynamic SKU breakdowns</span>
                </div>
            </div>
            <span style="font-size: 0.75rem; background: #fef3c7; color: #b45309; padding: 6px 14px; border-radius: 50px; font-weight: 800; border: 1px solid #fde68a;">
                <i class="fas fa-globe-americas"></i> USA Sales Coverage
            </span>
        </div>
        
        <div style="padding: 2rem;">
            <!-- Map Container -->
            <div id="us_leaflet_map" style="height: 480px; width: 100%; background: #f8fafc; border-radius: 16px; margin-bottom: 2rem; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); position: relative; z-index: 1;">
                <!-- Fallback Map Indicator -->
                <div id="map_fallback_info" style="position: absolute; bottom: 15px; left: 15px; background: rgba(255,255,255,0.9); padding: 8px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; color: #475569; z-index: 1000; border: 1px solid #e2e8f0; pointer-events: none; display: none;">
                    Using offline fallback visualizer
                </div>
            </div>
            
            <!-- Table Container -->
            <div class="analysis-table-container" style="border: 1px solid #94a3b8; border-radius: 12px; overflow: hidden; background: #fff;">
                <table id="geo_sales_table" class="analysis-table" style="width: 100%; border-collapse: collapse; margin: 0; font-size: 0.95rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #94a3b8;">
                            <th style="border: 1px solid #94a3b8 !important; padding: 1.25rem !important; text-align: center !important; color: #1e293b !important; font-weight: 800 !important; width: 25% !important;">State / Region</th>
                            <th style="border: 1px solid #94a3b8 !important; padding: 1.25rem !important; text-align: center !important; color: #1e293b !important; font-weight: 800 !important; width: 10% !important;">Orders</th>
                            <th style="border: 1px solid #94a3b8 !important; padding: 1.25rem !important; text-align: center !important; color: #1e293b !important; font-weight: 800 !important; width: 10% !important;">Units Sold</th>
                            <th style="border: 1px solid #94a3b8 !important; padding: 1.25rem !important; text-align: center !important; color: #1e293b !important; font-weight: 800 !important; width: 14% !important;">Sales</th>
                            <th style="border: 1px solid #94a3b8 !important; padding: 1.25rem !important; text-align: center !important; color: #1e293b !important; font-weight: 800 !important; width: 14% !important;">Amazon Fees</th>
                            <th style="border: 1px solid #94a3b8 !important; padding: 1.25rem !important; text-align: center !important; color: #1e293b !important; font-weight: 800 !important; width: 14% !important;">Refund Cost</th>
                            <th style="border: 1px solid #94a3b8 !important; padding: 1.25rem !important; text-align: center !important; color: #1e293b !important; font-weight: 800 !important; width: 13% !important;">Gross Profit</th>
                        </tr>
                    </thead>
                    <tbody id="region_sales_body">
                        <tr><td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8; font-weight: 700;">Loading regional insights...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 4: Fees Breakdown (Full Width Platform Fee Analysis) -->
    <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="padding: 1.25rem 2rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #f43f5e, #e11d48); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.2);"><i class="fas fa-receipt"></i></div>
            <div>
                <span style="font-weight: 800; color: #1e293b; font-size: 1.1rem; display: block;">Amazon Platform Fee Analysis</span>
                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Granular breakdown of Amazon platform charges and adjustment categories</span>
            </div>
        </div>
        <div style="padding: 2rem; display: grid; grid-template-columns: 1fr 1.5fr; gap: 3rem; align-items: center;">
            <div style="height: 280px; display: flex; align-items: center; justify-content: center; position: relative;">
                <canvas id="feesChart"></canvas>
                <div id="fee_chart_center_text" style="position: absolute; text-align: center; pointer-events: none;">
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Fees</div>
                    <div id="total_fees_donut_val" style="font-size: 1.5rem; font-weight: 900; color: #1e293b;">$0.00</div>
                </div>
            </div>
            <div id="fees_breakdown_list" style="max-height: 280px; overflow-y: auto; padding-right: 10px;">
                <div style="text-align: center; padding: 2rem; color: #94a3b8; font-weight: 700;">Analyzing fee categories...</div>
            </div>
        </div>
    </div>

</div>

<!-- PRODUCTS TAB -->
<div id="tab_products" class="tab-content" <?php echo ($active_tab !== 'products') ? 'style="display: none;"' : ''; ?>>
    <div class="card">
        <h3 style="margin-bottom: 2rem;"><i class="fas fa-trophy"></i> Top Performing Products</h3>
        <div class="product-grid" id="product_list"></div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 1.5rem;"><i class="fas fa-list-alt"></i> Product Performance Analysis</h3>
        <div class="analysis-table-container">
            <table id="product_perf_table" class="analysis-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 50px; background: #f8fafc !important; color: #1e293b !important; border: 1px solid #e2e8f0;">RANK</th>
                        <th rowspan="2" style="min-width: 250px; background: #f8fafc !important; color: #1e293b !important; border: 1px solid #e2e8f0;">PRODUCT NAME / SKU</th>
                        <th colspan="3" class="group-header sales-metrics-group" style="border: 1px solid #312e81;">SALES METRICS</th>
                        <th colspan="1" class="group-header ads-spend-group" style="border: 1px solid #991b1b;">ADS</th>
                        <th colspan="1" class="group-header acos-group" style="border: 1px solid #9a3412;">EFFICIENCY</th>
                        <th colspan="1" class="group-header ad-dep-group" style="border: 1px solid #5b21b6;">DEPENDENCY</th>
                        <th colspan="1" class="group-header traffic-sess-group" style="border: 1px solid #075985;">TRAFFIC</th>
                        <th colspan="1" class="group-header conv-group" style="border: 1px solid #065f46;">CONVERSION</th>
                        <th colspan="2" class="group-header refund-group" style="border: 1px solid #9f1239;">RETURNS</th>
                        <th colspan="1" class="group-header buy-box-group" style="border: 1px solid #0f172a;">MARKET</th>
                    </tr>
                    <tr>
                        <th class="sales-group" style="opacity: 0.9; border: 1px solid #312e81; width: 120px;">SALES</th>
                        <th class="sales-group" style="opacity: 0.9; border: 1px solid #312e81; width: 80px;">UNITS</th>
                        <th class="sales-group" style="opacity: 0.9; border: 1px solid #312e81; width: 80px;">ORDERS</th>
                        <th class="ads-spend-group" style="opacity: 0.9; border: 1px solid #991b1b;">Ad Spend</th>
                        <th class="acos-group" style="opacity: 0.9; border: 1px solid #9a3412;">ACoS %</th>
                        <th class="ad-dep-group" style="opacity: 0.9; border: 1px solid #5b21b6;">Ad Dep.</th>
                        <th class="traffic-sess-group" style="opacity: 0.9; border: 1px solid #075985;">Sessions</th>
                        <th class="conv-group" style="opacity: 0.9; border: 1px solid #065f46;">Conv %</th>
                        <th class="refund-group" style="opacity: 0.9; border: 1px solid #9f1239;">Refunds</th>
                        <th class="refund-group" style="opacity: 0.9; border: 1px solid #9f1239;">Refund %</th>
                        <th class="buy-box-group" style="opacity: 0.9; border: 1px solid #0f172a;">Buy Box %</th>
                    </tr>
                </thead>
                <tbody id="product_analysis_body"></tbody>
            </table>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; background: #6366f1; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-chart-pie"></i></div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b;">Product Revenue Market Share</h3>
            <span style="font-size: 0.75rem; background: #e0e7ff; padding: 4px 10px; border-radius: 20px; font-weight: 700; color: #4338ca; margin-left: auto;">Top SKU Distribution</span>
        </div>
        <div style="padding: 2rem;">
            <div style="height: 400px;"><canvas id="productRevenueShareChart"></canvas></div>
            <div style="margin-top: 1rem; text-align: center; font-size: 0.8rem; color: #64748b;">This chart illustrates the percentage contribution of each SKU to your total store revenue.</div>
        </div>
    </div>

    <!-- NEW: Monthly SKU-wise Matrix -->
    <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; background: #10b981; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-calendar-alt"></i></div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b;">Monthly SKU Performance Matrix</h3>
            <span style="font-size: 0.75rem; background: #dcfce7; padding: 4px 10px; border-radius: 20px; font-weight: 700; color: #166534; margin-left: auto;">Historical Analysis</span>
        </div>
        <div style="padding: 1rem; overflow-x: auto;">
            <table class="analysis-table" style="width: 100%;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th>Month</th>
                        <th>ASIN</th>
                        <th style="text-align: right;">Sales</th>
                        <th style="text-align: center;">Units</th>
                        <th style="text-align: center;">Sessions</th>
                        <th style="text-align: center;">Conv %</th>
                    </tr>
                </thead>
                <tbody id="monthly_sku_body">
                    <tr><td colspan="6" class="text-center">Loading monthly historical data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 2: UNIFIED PRODUCT INTELLIGENCE COMBO (Full Width Bottom) -->
    <div class="card" style="border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; background: #fff; margin-bottom: 2rem;">
        <div style="padding: 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px;">
            <div style="width: 32px; height: 32px; background: #4f46e5; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-chart-line"></i></div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b;">Ultimate Product Performance Intelligence</h3>
            <div style="margin-left: auto; display: flex; gap: 1.5rem; font-size: 0.75rem; font-weight: 700;">
                <span style="color: #93B9BD; display: flex; align-items: center; gap: 6px;"><i class="fas fa-square" style="font-size: 0.9rem;"></i> Page Views</span>
                <span style="color: #8B5CF6; display: flex; align-items: center; gap: 6px;"><i class="fas fa-square" style="font-size: 0.9rem;"></i> Sessions</span>
                <span style="color: #4F46E5; display: flex; align-items: center; gap: 6px;"><i class="fas fa-minus" style="font-weight: 900;"></i> Revenue ($)</span>
                <span style="color: #10B981; display: flex; align-items: center; gap: 6px;"><i class="fas fa-ellipsis-h"></i> Conv %</span>
            </div>
        </div>
        <div style="padding: 2.5rem;">
            <div style="height: 550px;"><canvas id="productComboChart"></canvas></div>
            <div style="margin-top: 2rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;">
                <div style="padding: 1.5rem; background: #f8fafc; border-radius: 16px; border-left: 5px solid #4f46e5; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <h5 style="margin: 0 0 0.75rem 0; font-size: 0.9rem; color: #1e293b; font-weight: 800;">Strategic Correlation</h5>
                    <p style="margin: 0; font-size: 0.8rem; color: #64748b; line-height: 1.6;">Analyze the relationship between traffic (bars) and financial outcomes (lines). High traffic with low revenue indicates listing optimization is needed.</p>
                </div>
                <div style="padding: 1.5rem; background: #f0fdf4; border-radius: 16px; border-left: 5px solid #10b981; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <h5 style="margin: 0 0 0.75rem 0; font-size: 0.9rem; color: #166534; font-weight: 800;">Actionable Insight</h5>
                    <p style="margin: 0; font-size: 0.8rem; color: #15803d; line-height: 1.6;">Prioritize products where the green dashed line (Conv %) is trending upwards, as these are your most efficient growth opportunities.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let mainChart, expenseChart;
    let provinceChart;
    let productComboChart;
    let trafficTrendChart;
    let globalData = null;
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function updateComparison(selector, cmp) {
        const $el = $(selector);
        $el.removeClass('up down none show');
        
        if (!cmp || cmp.dir === 'none') {
            $el.addClass('none').html('--%');
        } else {
            const icon = cmp.dir === 'up' ? 'fa-arrow-up' : 'fa-arrow-down';
            $el.addClass(cmp.dir).html(`<i class="fas ${icon}"></i> ${cmp.pct}%`);
        }
        
        // Always show the tag to indicate comparison is active
        setTimeout(() => $el.addClass('show'), 600);
    }

    function staggerIn(selector, baseDelay = 0, step = 50) {
        $(selector).each(function(i) {
            const $el = $(this);
            $el.removeClass('visible');
            setTimeout(() => $el.addClass('visible'), baseDelay + (i * step));
        });
    }

    function showLoader() {
        const $o = $('#loading_overlay');
        $('.main-content').addClass('loading-blur');
        $o.css('display', 'flex').hide().fadeIn(200);
    }

    function hideLoader() {
        const $o = $('#loading_overlay');
        $('.main-content').removeClass('loading-blur');
        $o.fadeOut(200);
    }

    function toNumber(v) {
        const n = Number(v);
        return Number.isFinite(n) ? n : 0;
    }

    function parseNumberFromText(text) {
        const s = (text || '').toString();
        const cleaned = s.replace(/[^0-9.+-]/g, '');
        const n = Number(cleaned);
        return Number.isFinite(n) ? n : 0;
    }

    function animateNumber($el, target, formatFn, durationMs) {
        const el = $el && $el[0];
        if (!el) return;

        const to = toNumber(target);
        const from = toNumber($el.data('num'));
        $el.data('num', to);

        if (prefersReducedMotion || !durationMs || durationMs <= 0) {
            $el.text(formatFn(to));
            return;
        }

        const start = performance.now();
        const dur = Math.max(120, durationMs);

        function step(t) {
            const p = Math.min(1, (t - start) / dur);
            const eased = 1 - Math.pow(1 - p, 3);
            const v = from + (to - from) * eased;
            $el.text(formatFn(v));
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    function setMoneyAnimated(selector, moneyText, sign) {
        const $el = $(selector);
        const n = parseNumberFromText(moneyText) * (sign || 1);
        animateNumber(
            $el,
            n,
            (v) => {
                const abs = Math.abs(v);
                const prefix = v < 0 ? '-' : '';
                return prefix + '$' + abs.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },
            780
        );
    }

    function setMoneySuffixAnimated(selector, moneyText, suffix, sign) {
        const $el = $(selector);
        const sfx = suffix || '';
        const n = parseNumberFromText(moneyText) * (sign || 1);
        animateNumber(
            $el,
            n,
            (v) => {
                const abs = Math.abs(v);
                const prefix = v < 0 ? '-' : '';
                return prefix + '$' + abs.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + sfx;
            },
            720
        );
    }

    function setIntAnimated(selector, text) {
        const $el = $(selector);
        animateNumber($el, parseNumberFromText(text), (v) => Math.round(v).toLocaleString(), 720);
    }

    function setIntSuffixAnimated(selector, text, suffix) {
        const $el = $(selector);
        const sfx = suffix || '';
        animateNumber($el, parseNumberFromText(text), (v) => Math.round(v).toLocaleString() + sfx, 640);
    }

    function setFloatSuffixAnimated(selector, text, suffix, decimals) {
        const $el = $(selector);
        const sfx = suffix || '';
        const d = (decimals == null) ? 1 : decimals;
        animateNumber($el, parseNumberFromText(text), (v) => toNumber(v).toFixed(d) + sfx, 640);
    }

    function setPercentAnimated(selector, text, decimals) {
        const $el = $(selector);
        const d = (decimals == null) ? 2 : decimals;
        animateNumber($el, parseNumberFromText(text), (v) => toNumber(v).toFixed(d) + '%', 720);
    }

    function setPercentSuffixAnimated(selector, text, suffix, decimals) {
        const $el = $(selector);
        const d = (decimals == null) ? 2 : decimals;
        const sfx = suffix || '';
        animateNumber($el, parseNumberFromText(text), (v) => toNumber(v).toFixed(d) + '%' + sfx, 720);
    }

    function setFloatAnimated(selector, val, decimals) {
        const $el = $(selector);
        const d = decimals || 0;
        animateNumber($el, parseNumberFromText(val), (v) => toNumber(v).toFixed(d), 720);
    }

    function setCmpTag(selector, pct, dir) {
        const $el = $(selector);
        if (!$el.length) return;
        const p = parseFloat(pct || 0);
        const color = dir === 'up' ? '#10b981' : (dir === 'down' ? '#ef4444' : '#64748b');
        const icon = dir === 'up' ? 'fa-arrow-up' : (dir === 'down' ? 'fa-arrow-down' : '');
        const bg = dir === 'up' ? '#f0fdf4' : (dir === 'down' ? '#fef2f2' : '#f8fafc');
        
        $el.html(`<span style="background:${bg}; color:${color}; padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:800; display:inline-flex; align-items:center; gap:4px;">
            ${icon ? `<i class="fas ${icon}"></i>` : ''} ${p}%
        </span>`);
    }

    function updateComparison(selector, data) {
        if (!data) return;
        setCmpTag(selector, data.pct, data.dir);
    }

    function staggerIn(selector, baseDelayMs, stepMs) {
        if (prefersReducedMotion) return;
        const base = baseDelayMs || 0;
        const step = stepMs || 70;
        $(selector).each(function(i) {
            const el = this;
            const delay = base + i * step;
            if (el && typeof el.animate === 'function') {
                el.animate(
                    [
                        { opacity: 0, transform: 'translateY(12px)' },
                        { opacity: 1, transform: 'translateY(0)' }
                    ],
                    { duration: 520, delay, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'both' }
                );
            } else {
                el.classList.remove('anim-in');
                el.style.setProperty('--d', delay + 'ms');
                void el.offsetWidth;
                el.classList.add('anim-in');
            }
        });
    }

    function formatAbbrev(n) {
        const num = toNumber(n);
        const abs = Math.abs(num);
        if (abs >= 1e9) return (num / 1e9).toFixed(1).replace(/\.0$/, '') + 'B';
        if (abs >= 1e6) return (num / 1e6).toFixed(1).replace(/\.0$/, '') + 'M';
        if (abs >= 1e3) return (num / 1e3).toFixed(1).replace(/\.0$/, '') + 'K';
        return num.toLocaleString();
    }

    function makeFillGradient(ctx, color) {
        const g = ctx.createLinearGradient(0, 0, 0, 420);
        g.addColorStop(0, color + '33');
        g.addColorStop(1, color + '00');
        return g;
    }

    function renderChart(type) {
        if (!globalData || !globalData.charts) return;
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded; skipping chart render.');
            return;
        }
        const ctx = document.getElementById('mainChart').getContext('2d');
        if (mainChart) mainChart.destroy();

        const labels = Array.isArray(globalData.charts.labels) ? globalData.charts.labels : [];
        if (labels.length === 0) {
            hideLoader();
            return;
        }

        const isMoneyChart = ['sales', 'shipped', 'b2b_sales'].includes(type);
        let config = {
            type: 'line',
            data: { labels, datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: prefersReducedMotion ? false : {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { 
                        display: true, 
                        position: 'top', 
                        align: 'end',
                        labels: { 
                            boxWidth: 10, 
                            boxHeight: 10, 
                            usePointStyle: true, 
                            font: { size: 14, weight: '800' },
                            color: '#1e293b',
                            padding: 20
                        } 
                    },
                    tooltip: { 
                        padding: 16, 
                        backgroundColor: 'rgba(15, 23, 42, 0.95)', 
                        backdropFilter: 'blur(4px)',
                        titleFont: { size: 14, weight: '700' },
                        bodyFont: { size: 13 },
                        cornerRadius: 12,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    const isMoneyLabel = ['Revenue', 'Shipped Sales', 'B2B Sales'].includes(context.dataset.label);
                                    if (isMoneyLabel) {
                                        label += '$' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2});
                                    } else {
                                        label += context.parsed.y.toLocaleString();
                                        if (context.dataset.label.includes('%')) label += '%';
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(148, 163, 184, 0.08)', drawBorder: false },
                        ticks: { 
                            color: '#94a3b8', 
                            font: { weight: '600' }, 
                            callback: (v) => (isMoneyChart ? '$' : '') + formatAbbrev(v), 
                            padding: 10 
                        }
                    },
                    y1: {
                        display: false,
                        position: 'right',
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { weight: '600' }, callback: (v) => formatAbbrev(v) }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { weight: '600' }, maxRotation: 0, autoSkip: true, maxTicksLimit: 10, padding: 10 }
                    }
                }
            }
        };

        const map = {
            'sales': { label: 'Revenue', data: globalData.charts.sales, color: '#10B981' },
            'orders': { label: 'Orders', data: globalData.charts.orders, color: '#6366f1' },
            'units': { label: 'Units', data: globalData.charts.units, color: '#10b981' },
            'page_views': { label: 'Page Views', data: globalData.charts.page_views, color: '#8b5cf6' },
            'sessions': { label: 'Sessions', data: globalData.charts.sessions, color: '#f59e0b' },
            'buy_box': { label: 'Buy Box %', data: globalData.charts.buy_box, color: '#f59e0b' },
            'conversion': { label: 'Conv Rate %', data: globalData.charts.conversion, color: '#f43f5e' },
            'refund_rate': { label: 'Refund Rate %', data: globalData.charts.refund_rate, color: '#84cc16' },
            'b2b_sales': { label: 'B2B Sales', data: globalData.charts.b2b_sales, color: '#10b981' },
            'shipped': { label: 'Shipped Sales', data: globalData.charts.shipped_sales, color: '#1e293b' },
            'feedback': { label: 'Feedback', data: globalData.charts.feedback, color: '#fbbf24' },
            'atoz': { label: 'A-to-Z', data: globalData.charts.atoz, color: '#ef4444' }
        };

        if (type === 'refund_rate') {
            const refunded = (globalData.charts.refunds || []).map(toNumber);
            const rate = (globalData.charts.refund_rate || []).map(toNumber);
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Refunded Units', data: refunded, backgroundColor: '#ef4444cc', borderRadius: 8, maxBarThickness: 40 },
                { label: 'Refund Rate %', data: rate, type: 'line', yAxisID: 'y1', borderColor: '#10B981', borderWidth: 3, fill: false, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderWidth: 2 }
            ];
            config.options.scales.y1.display = true;
            config.options.scales.y1.ticks.callback = (v) => toNumber(v).toFixed(0) + '%';
        } else if (type === 'units_orders') {
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Units', data: (globalData.charts.units || []).map(toNumber), backgroundColor: '#10B98133', borderColor: '#10B981', borderWidth: 2, borderRadius: 8, maxBarThickness: 40 },
                { label: 'Orders', data: (globalData.charts.orders || []).map(toNumber), type: 'line', yAxisID: 'y1', borderColor: '#6366f1', borderWidth: 3, fill: true, backgroundColor: makeFillGradient(ctx, '#6366f1'), tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderWidth: 2 }
            ];
            config.options.scales.y1.display = true;
            config.options.scales.y1.title = { display: true, text: 'Orders' };
            config.options.scales.y.title = { display: true, text: 'Units' };
        } else if (type === 'page_views') {
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Browser PV', data: (globalData.charts.page_views_browser || []).map(toNumber), backgroundColor: '#f59e0b', borderRadius: 6, maxBarThickness: 30 },
                { label: 'Mobile App PV', data: (globalData.charts.page_views_mobile || []).map(toNumber), backgroundColor: '#8b5cf6', borderRadius: 6, maxBarThickness: 30 }
            ];
            config.options.scales.x.stacked = true;
            config.options.scales.y.stacked = true;
            config.options.scales.y.title = { display: true, text: 'Page Views' };
        } else if (type === 'sessions') {
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Browser Sessions', data: (globalData.charts.sessions_browser || []).map(toNumber), backgroundColor: '#f59e0b', borderRadius: 6, maxBarThickness: 30 },
                { label: 'Mobile App Sessions', data: (globalData.charts.sessions_mobile || []).map(toNumber), backgroundColor: '#6366f1', borderRadius: 6, maxBarThickness: 30 }
            ];
            config.options.scales.x.stacked = true;
            config.options.scales.y.stacked = true;
            config.options.scales.y.title = { display: true, text: 'Sessions' };
        } else if (type === 'conversion') {
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Conversion Rate %', data: (globalData.charts.conversion || []).map(toNumber), backgroundColor: '#f43f5e', borderRadius: 8, maxBarThickness: 50, borderColor: '#be123c', borderWidth: 1 }
            ];
            config.options.scales.y.ticks.callback = (v) => v + '%';
            config.options.scales.y.title = { display: true, text: 'Conversion Percentage (%)' };
        } else {
            const d = map[type] || map['sales'];
            const data = (d.data || []).map(toNumber);
            config.type = 'line';
            config.data.datasets = [{ 
                label: d.label, 
                data: data, 
                borderColor: d.color, 
                backgroundColor: makeFillGradient(ctx, d.color), 
                fill: true, 
                tension: 0.4, 
                borderWidth: 3, 
                pointRadius: 0, 
                pointHoverRadius: 6 
            }];
        }
        mainChart = new Chart(ctx, config);
    }

    function renderTrafficTrendChart() {
        if (!globalData || !globalData.charts) return;
        const ctx = document.getElementById('trafficTrendChart').getContext('2d');
        if (trafficTrendChart) trafficTrendChart.destroy();

        const labels = globalData.charts.labels || [];
        trafficTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Page Views',
                        data: (globalData.charts.page_views || []).map(toNumber),
                        borderColor: '#6366f1',
                        backgroundColor: makeFillGradient(ctx, '#6366f1'),
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Sessions',
                        data: (globalData.charts.sessions || []).map(toNumber),
                        borderColor: '#10b981',
                        backgroundColor: makeFillGradient(ctx, '#10b981'),
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, font: { weight: '600' } } }
                },
                scales: {
                    y: { grid: { color: 'rgba(148, 163, 184, 0.08)' }, ticks: { font: { weight: '600' }, callback: v => formatAbbrev(v) } },
                    x: { grid: { display: false }, ticks: { font: { weight: '600' }, maxTicksLimit: 12 } }
                }
            }
        });
    }

    function renderTrends(trends) {
        if (!trends || typeof trends !== 'object') return;
        const months = Object.keys(trends);
        if (months.length === 0) return;
        let headHtml = '<th style="text-align: left;">KPI Metrics</th>';
        months.forEach((m, i) => headHtml += `<th class="${i === 2 ? 'highlight-col' : ''}">${m}</th>`);
        $('#trend_head').html(headHtml);

        const rows = [
            { label: 'Total Sales', key: 'sales', icon: 'fa-dollar-sign', isMoney: true },
            { label: 'Total Orders', key: 'orders', icon: 'fa-shopping-basket' },
            { label: 'Total Unit Sold', key: 'units', icon: 'fa-shopping-cart' },
            { label: 'Page Views', key: 'page_views', icon: 'fa-eye' },
            { label: 'Conversion Rate', key: 'conv', icon: 'fa-percentage', isRate: true }
        ];

        let bodyHtml = '';
        rows.forEach(r => {
            bodyHtml += `<tr><td><i class="fas ${r.icon}" style="opacity:0.4; width: 1.5rem; text-align: center;"></i> ${r.label}</td>`;
            let v1 = 0, v2 = 0;
            let prevVal = null;
            months.forEach((m, i) => {
                const raw = (trends[m] && trends[m][r.key] != null) ? trends[m][r.key] : 0;
                let n = Number(raw);
                if (!Number.isFinite(n)) n = 0;
                if (i === 1) v1 = n;
                if (i === 2) v2 = n;
                
                let displayVal = n.toLocaleString();
                if (r.isRate) displayVal = n.toFixed(1) + '%';
                else if (r.isMoney) displayVal = '$' + n.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                let changeHtml = '';
                if (prevVal !== null && prevVal !== 0) {
                    const diff = n - prevVal;
                    const pct = (diff / prevVal) * 100;
                    if (Math.abs(pct) >= 0.1) {
                        const isUp = pct > 0;
                        const color = isUp ? '#10b981' : '#ef4444';
                        const icon = isUp ? 'fa-caret-up' : 'fa-caret-down';
                        changeHtml = `<span style="font-size: 0.75rem; font-weight: 700; color: ${color}; margin-right: 6px; display: inline-flex; align-items: center; gap: 2px;">
                            <i class="fas ${icon}"></i> ${Math.abs(pct).toFixed(1)}%
                        </span>`;
                    }
                }
                prevVal = n;

                bodyHtml += `<td class="${i === 2 ? 'highlight-col' : ''}" style="white-space: nowrap;">${changeHtml}${displayVal}</td>`;
            });
            bodyHtml += `</tr>`;
        });
        $('#trend_body').html(bodyHtml);
    }

    function animateCurrentTab() {
        const tab = '<?php echo $active_tab; ?>';
        const $content = $('#tab_' + tab);
        $content.addClass('animating');
        setTimeout(() => $content.removeClass('animating'), prefersReducedMotion ? 0 : 260);

        if (tab === 'kpi') {
            staggerIn('#tab_kpi .kpi-card', 0, 70);
            staggerIn('#tab_kpi > .card', 260, 120);
        } else if (tab === 'financial') {
            staggerIn('#tab_financial .card', 0, 100);
        } else if (tab === 'products') {
            staggerIn('#tab_products .card', 0, 90);
            staggerIn('#product_list .product-item', 180, 60);
            staggerIn('#product_analysis_body tr', 260, 40);
        } else if (tab === 'traffic') {
            staggerIn('#tab_traffic .kpi-card', 0, 70);
            staggerIn('#tab_traffic .card', 200, 100);
        }
    }

    function loadDashboard() {
        showLoader();
        const customerId = $('#customer_id_hidden').length ? $('#customer_id_hidden').val() : $('#filter_customer').val();
        let from = $('#filter_from').val() || '2026-01-01';
        let to = $('#filter_to').val() || '2026-03-31';

        $.ajax({
            url: '<?php echo BASE_URL; ?>api/dashboard_data.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            dataType: 'json',
            success: function(res) {
                if (!res || !res.kpis) return;
                
                // Load product-specific charts
                loadProductAnalytics(customerId, from, to);
                loadSettlementAnalytics(customerId, from, to);

                globalData = res;
                const k = res.kpis;
                const f = res.financials;
                
                setMoneyAnimated('#kpi_sales', k.total_sales, 1);
                setCmpTag('#cmp_sales', k.revenue_cmp, k.revenue_cmp_status);

                setIntAnimated('#kpi_orders', k.total_orders);
                // setCmpTag('#cmp_orders', k.orders_cmp, k.orders_cmp_status);

                setIntAnimated('#kpi_units', k.total_units);
                setCmpTag('#cmp_units', k.units_cmp, k.units_cmp_status);

                setMoneyAnimated('#kpi_dsr', k.dsr, 1);
                // setCmpTag('#cmp_dsr', k.dsr_cmp, k.dsr_cmp_status);

                setMoneyAnimated('#kpi_ad_sales', k.ad_sales, 1);
                // setCmpTag('#cmp_ad_sales', k.ad_sales_cmp, k.ad_sales_cmp_status);

                setMoneyAnimated('#kpi_organic', k.organic_sales, 1);
                // setCmpTag('#cmp_organic', k.organic_cmp, k.organic_cmp_status);

                setMoneyAnimated('#kpi_spend', k.ad_spend, 1);
                // setCmpTag('#cmp_spend', k.spend_cmp, k.spend_cmp_status);

                setPercentAnimated('#kpi_acos', k.acos, 2);
                // setCmpTag('#cmp_acos', k.acos_cmp, k.acos_cmp_status);

                setPercentAnimated('#kpi_tacos', k.tacos, 2);
                // setCmpTag('#cmp_tacos', k.tacos_cmp, k.tacos_cmp_status);

                setFloatAnimated('#kpi_roas', k.roas, 2);
                // setCmpTag('#cmp_roas', k.roas_cmp, k.roas_cmp_status);

                setIntAnimated('#kpi_sessions', k.total_sessions);
                setCmpTag('#cmp_sessions', k.sessions_cmp, k.sessions_cmp_status);

                setPercentAnimated('#kpi_conversion', k.avg_conversion, 2);
                setCmpTag('#cmp_conv', k.conv_cmp, k.conv_cmp_status);

                setIntAnimated('#kpi_refunds', k.total_refunds);
                setCmpTag('#cmp_refunds', k.refunds_cmp, k.refunds_cmp_status);

                setMoneyAnimated('#kpi_net_profit', k.net_profit, 1);
                // setCmpTag('#cmp_net', k.net_cmp, k.net_cmp_status);
                setPercentAnimated('#kpi_roi', k.roi, 1);

                if (res.comparisons) {
                    updateComparison('#cmp_sales', res.comparisons.sales);
                    updateComparison('#cmp_orders', res.comparisons.orders);
                    updateComparison('#cmp_units', res.comparisons.units);
                    updateComparison('#cmp_dsr', res.comparisons.dsr);
                    updateComparison('#cmp_ad_sales', res.comparisons.ad_sales);
                    updateComparison('#cmp_organic', res.comparisons.organic);
                    updateComparison('#cmp_spend', res.comparisons.spend);
                    updateComparison('#cmp_acos', res.comparisons.acos);
                    updateComparison('#cmp_tacos', res.comparisons.tacos);
                    updateComparison('#cmp_roas', res.comparisons.roas);
                    updateComparison('#cmp_conv', res.comparisons.conv);
                    updateComparison('#cmp_refunds', res.comparisons.refunds);
                    updateComparison('#cmp_b2b', res.comparisons.b2b);
                    if (res.comparisons.net_profit) updateComparison('#cmp_net', res.comparisons.net_profit);
                    
                    // Tab Traffic Comparisons
                    updateComparison('#cmp_sessions_t', res.comparisons.sessions);
                    updateComparison('#cmp_pv_t', res.comparisons.page_views);
                }

                // Traffic Tab Population
                setIntAnimated('#kpi_sessions_t', k.total_sessions);
                setIntAnimated('#kpi_pv_t', k.total_page_views);
                setPercentAnimated('#kpi_conv_t', k.avg_conversion, 2);
                setPercentAnimated('#kpi_buybox_t', k.buy_box, 0);
                
                // Mobile Share Calc
                const totalPV = toNumber(k.total_page_views);
                const mobilePV = res.charts.page_views_mobile.reduce((a, b) => a + b, 0);
                const mobileShare = totalPV > 0 ? (mobilePV / totalPV) * 100 : 0;
                setPercentAnimated('#kpi_mobile_t', mobileShare, 1);

                // Daily Traffic Table
                let trafficHtml = '';
                if (res.charts && res.charts.labels) {
                    res.charts.labels.forEach((label, i) => {
                        trafficHtml += `<tr>
                            <td style="font-weight: 700; color: #64748b;">${label}</td>
                            <td style="font-weight: 800; color: #1e293b;">${res.charts.sessions[i].toLocaleString()}</td>
                            <td style="font-weight: 800; color: #1e293b;">${res.charts.page_views[i].toLocaleString()}</td>
                            <td style="text-align: center;">${res.charts.buy_box[i].toFixed(0)}%</td>
                            <td style="text-align: center;">${res.charts.units[i].toLocaleString()}</td>
                            <td style="text-align: center;">${res.charts.orders[i].toLocaleString()}</td>
                            <td style="text-align: right;"><span style="background: #eff6ff; color: #1e40af; padding: 4px 8px; border-radius: 6px; font-weight: 800;">${res.charts.conversion[i].toFixed(2)}%</span></td>
                        </tr>`;
                    });
                }
                $('#traffic_daily_body').html(trafficHtml || '<tr><td colspan="7" class="text-center">No traffic data for this range.</td></tr>');
                
                if ($.fn.DataTable.isDataTable('#traffic_daily_table')) $('#traffic_daily_table').DataTable().destroy();
                $('#traffic_daily_table').DataTable({ pageLength: 10, order: [[0, 'desc']], language: { search: "_INPUT_", searchPlaceholder: "Search daily traffic..." } });

                renderTrafficTrendChart();

                setMoneyAnimated('#pl_total_rev', f.revenue, 1);
                const totalAmazonFees = f.selling_fees + f.fba_fees + f.service_fees + f.adjustments + f.inventory_fees + f.return_fees;
                const totalOut = Math.abs(totalAmazonFees) + Math.abs(f.cogs);
                
                setMoneyAnimated('#pl_total_out', totalOut, -1);
                setMoneyAnimated('#pl_total_net', f.net_profit, 1);
                setPercentAnimated('#pl_total_margin', f.net_margin, 1);
                $('#pl_date_range').text(`${new Date(from).toLocaleDateString('en-US', {month:'short', day:'2-digit'})} - ${new Date(to).toLocaleDateString('en-US', {month:'short', day:'2-digit', year:'numeric'})}`);

                // --- FINANCIAL DRILLDOWN POPULATION ---
                // 1. Gross Revenue
                const revItems = [
                    { label: 'Product Sales', val: f.product_sales, trend: '+12%' },
                    { label: 'Shipping Credits', val: f.shipping_credits, trend: '+4%' },
                    { label: 'Gift Wrap Credits', val: f.gift_wrap_credits, trend: 'Stable' }
                ];
                let plRevRowsHtml = '';
                revItems.forEach((item, index) => {
                    const contrib = f.revenue > 0 ? ((item.val / f.revenue) * 100).toFixed(1) : '0.0';
                    const colors = ['#2563eb', '#3b82f6', '#60a5fa'];
                    const dotColor = colors[index] || '#94a3b8';
                    const trendColor = item.trend.includes('+') ? '#10b981' : '#94a3b8';
                    const trendIcon = item.trend.includes('+') ? '<i class="fas fa-arrow-trend-up"></i> ' : '';
                    
                    plRevRowsHtml += `
                        <tr>
                            <td style="width: 33.33% !important; text-align: left !important; padding: 1.5rem 2rem !important; font-weight: 700 !important; color: #0f172a !important; font-size: 1.05rem !important;">
                                <div style="display: flex; align-items: center; justify-content: flex-start; gap: 10px;">
                                    <span style="width: 10px; height: 10px; background: ${dotColor}; border-radius: 50%; display: inline-block;"></span>
                                    <span>${item.label}</span>
                                </div>
                            </td>
                            <td style="width: 33.33% !important; text-align: right !important; padding: 1.5rem 2rem !important; font-weight: 800 !important; color: #0f172a !important; font-size: 1.05rem !important;">
                                $${item.val.toLocaleString(undefined, {minimumFractionDigits: 2})}
                            </td>
                            <td style="width: 33.33% !important; text-align: right !important; padding: 1.5rem 2rem !important; font-weight: 800 !important; color: #2563eb !important; font-size: 1.05rem !important;">
                                <span style="background: #eff6ff; color: #2563eb; padding: 6px 12px; border-radius: 50px; font-size: 0.85rem !important; font-weight: 800 !important;">${contrib}%</span>
                            </td>
                        </tr>
                    `;
                });
                $('#pl_revenue_rows').html(plRevRowsHtml);
                $('#pl_rev_header_val').text('$' + f.revenue.toLocaleString(undefined, {minimumFractionDigits: 2}));

                // 2. Expenses & Fees
                const feeItems = [
                    { label: 'Cost of Goods Sold (COGS)', val: -f.cogs },
                    { label: 'Selling Fees', val: f.selling_fees },
                    { label: 'FBA Fees', val: f.fba_fees },
                    { label: 'Service Fees (Ads/Sub)', val: f.service_fees },
                    { label: 'Adjustments (Credits)', val: f.adjustments },
                    { label: 'FBA Inventory Fees', val: f.inventory_fees },
                    { label: 'Customer Return Fees', val: f.return_fees }
                ];
                
                let plExpRowsHtml = '';
                feeItems.forEach((item, index) => {
                    const absVal = Math.abs(item.val);
                    const share = f.revenue > 0 ? ((absVal / f.revenue) * 100).toFixed(1) : '0.0';
                    let status = 'NORMAL';
                    let statusBg = '#f1f5f9';
                    let statusColor = '#475569';
                    
                    if (item.label.includes('COGS')) {
                        status = 'OPTIMIZED';
                        statusBg = '#e6fcf5';
                        statusColor = '#0ca678';
                    } else if (share > 10) {
                        status = 'ATTENTION';
                        statusBg = '#fff5f5';
                        statusColor = '#ff6b6b';
                    }
                    
                    plExpRowsHtml += `
                        <tr>
                            <td style="width: 33.33% !important; text-align: left !important; padding: 1.5rem 2rem !important; font-weight: 700 !important; color: #0f172a !important; font-size: 1.05rem !important;">
                                ${item.label}
                            </td>
                            <td style="width: 33.33% !important; text-align: right !important; padding: 1.5rem 2rem !important; font-weight: 800 !important; color: #0f172a !important; font-size: 1.05rem !important;">
                                $${absVal.toLocaleString(undefined, {minimumFractionDigits: 2})}
                            </td>
                            <td style="width: 33.33% !important; text-align: right !important; padding: 1.5rem 2rem !important; font-weight: 800 !important; color: #e11d48 !important; font-size: 1.05rem !important;">
                                <span style="background: #fff1f2; color: #e11d48; padding: 6px 12px; border-radius: 50px; font-size: 0.85rem !important; font-weight: 800 !important;">${share}%</span>
                            </td>
                        </tr>
                    `;
                });
                $('#pl_expenses_rows').html(plExpRowsHtml);
                const feesShare = f.revenue > 0 ? ((totalOut / f.revenue) * 100).toFixed(1) : '0.0';
                
                $('#pl_fees_share_badge').text(`${feesShare}% of Revenue`);
                $('#pl_fees_header_val').text(`($${totalOut.toLocaleString(undefined, {minimumFractionDigits: 2})})`);

                // 3. Net Profit Analysis
                const profitItems = [
                    { label: 'Calculated Gross Profit', val: f.gross_profit },
                    { label: 'Manual COGS (-)', val: -f.cogs },
                    { label: 'Final Net Profit', val: f.net_profit }
                ];
                
                let plNetRowsHtml = '';
                profitItems.forEach(item => {
                    const absVal = Math.abs(item.val);
                    const share = f.revenue > 0 ? ((absVal / f.revenue) * 100).toFixed(1) : '0.0';
                    const color = item.val >= 0 ? '#0ca678' : '#e11d48';
                    const bg = item.val >= 0 ? '#e6fcf5' : '#fff1f2';
                    const sign = item.val < 0 ? '-' : '';
                    
                    plNetRowsHtml += `
                        <tr>
                            <td style="width: 33.33% !important; text-align: left !important; padding: 1.5rem 1rem !important; font-weight: 700 !important; color: #0f172a !important; font-size: 1.05rem !important;">
                                ${item.label}
                            </td>
                            <td style="width: 33.33% !important; text-align: right !important; padding: 1.5rem 1rem !important; font-weight: 800 !important; color: ${color} !important; font-size: 1.05rem !important;">
                                ${sign}$${absVal.toLocaleString(undefined, {minimumFractionDigits: 2})}
                            </td>
                            <td style="width: 33.33% !important; text-align: right !important; padding: 1.5rem 1rem !important; font-weight: 800 !important;">
                                <span style="background: ${bg}; color: ${color}; padding: 6px 12px; border-radius: 50px; font-size: 0.85rem !important; font-weight: 800 !important;">${share}%</span>
                            </td>
                        </tr>
                    `;
                });
                $('#pl_net_profit_rows').html(plNetRowsHtml);
                $('#pl_net_margin_badge').text(`${f.net_margin.toFixed(1)}% Margin`);
                $('#pl_net_header_val').text('$' + f.net_profit.toLocaleString(undefined, {minimumFractionDigits: 2}));
                
                // Efficiency Score
                const efficiency = f.net_margin > 30 ? 94 : f.net_margin > 20 ? 82 : f.net_margin > 10 ? 71 : 58;
                let statusLabel = 'POOR';
                let statusBgColor = '#ef4444';
                let descText = 'High operational cost and low margins. Immediate optimization required.';
                
                if (efficiency >= 90) {
                    statusLabel = 'EXCELLENT';
                    statusBgColor = '#10b981';
                    descText = 'Your sales are highly optimized with controlled advertising and operational spillover.';
                } else if (efficiency >= 80) {
                    statusLabel = 'GOOD';
                    statusBgColor = '#3b82f6';
                    descText = 'Healthy margin with stable parameters. Look into PPC optimization.';
                } else if (efficiency >= 70) {
                    statusLabel = 'STABLE';
                    statusBgColor = '#f59e0b';
                    descText = 'Moderate performance. COGS reduction or fee optimization can boost bottom line.';
                }
                
                $('#pl_efficiency_score').text(efficiency);
                $('#pl_efficiency_status').text(statusLabel).css('background', statusBgColor);
                $('#pl_efficiency_desc').text(descText);

                renderTrends(res.trends);
                renderChart($('.chart-tab-btn.active').data('chart'));
                
                let prodHtml = '';
                let tableHtml = '';
                let skuPlHtml = '';
                // Robust parsing for total units to avoid NaN/Infinity
                const totalUnitsRaw = k.total_units || '0';
                const totalUnitsParsed = typeof totalUnitsRaw === 'string' ? parseNumberFromText(totalUnitsRaw) : toNumber(totalUnitsRaw);
                const avgCogsPerUnit = (totalUnitsParsed > 0) ? (toNumber(f.cogs) / totalUnitsParsed) : 0;
                
                let tUnits = 0, tRev = 0, tsFee = 0, tfFee = 0, toFee = 0, tGross = 0, tNet = 0, tCogs = 0;

                (res.sku_pl || []).forEach((p, i) => {
                    const productRevenue = toNumber(p.revenue || 0);
                    const productNet = toNumber(p.net || 0);
                    const productUnits = toNumber(p.units || 0);
                    const productMargin = toNumber(p.margin || 0);
                    const productName = p.name || 'Unknown Product';

                    tUnits += productUnits; tRev += productRevenue; tNet += productNet;
                    
                    let statusBadge = '';
                    if (productMargin > 20) {
                        statusBadge = '<span style="background:#e6fffa; color:#0d9488; border: 1px solid #99f6e4; font-size:0.75rem; padding:4px 12px; border-radius:50px; font-weight:800; display:inline-block;">HIGH PROFIT</span>';
                    } else if (productMargin > 0) {
                        statusBadge = '<span style="background:#f0fdf4; color:#16a34a; border: 1px solid #bbf7d0; font-size:0.75rem; padding:4px 12px; border-radius:50px; font-weight:800; display:inline-block;">PROFITABLE</span>';
                    } else {
                        statusBadge = '<span style="background:#fef2f2; color:#dc2626; border: 1px solid #fecaca; font-size:0.75rem; padding:4px 12px; border-radius:50px; font-weight:800; display:inline-block;">LOSS MAKING</span>';
                    }

                    skuPlHtml += `<tr>
                        <td style="padding: 1.25rem 0.75rem; text-align: left; font-size: 1.1rem; font-weight: 700;">${i+1}</td>
                        <td style="max-width:250px; font-weight:700; padding: 1.25rem 0.75rem; text-align: left;">
                            <div style="font-size:1.1rem; color:#1e293b; font-weight: 800; margin-bottom: 2px;">${p.sku}</div>
                            <div style="font-size:0.85rem; color:#94a3b8; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${productName}">${productName}</div>
                        </td>
                        <td style="font-size: 1.1rem; font-weight: 700; text-align: left; padding-left: 1.25rem;">${productUnits.toLocaleString()}</td>
                        <td style="font-weight:800; color:#1e40af; font-size: 1.1rem; text-align: left; padding-left: 1.25rem;">$${productRevenue.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                        <td style="font-weight:900; color:#10b981; font-size: 1.2rem; text-align: left; padding-left: 1.25rem;">$${productNet.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                        <td style="text-align: left; padding-left: 1.25rem;"><span style="background:#f0fdf4; color:#166534; padding:6px 10px; border-radius:8px; font-weight:900; font-size: 1rem;">${productMargin.toFixed(1)}%</span></td>
                        <td style="text-align:left; padding: 1.25rem 0.75rem;">${statusBadge}</td>
                    </tr>`;
                });

                const tMargin = tRev > 0 ? (tNet / tRev) * 100 : 0;
                let statusBadgeSummary = '';
                if (tMargin > 20) {
                    statusBadgeSummary = '<span style="background:#0d9488; color:#ffffff; font-size:0.75rem; padding:4px 12px; border-radius:50px; font-weight:800;">HIGH PROFIT</span>';
                } else if (tMargin > 0) {
                    statusBadgeSummary = '<span style="background:#16a34a; color:#ffffff; font-size:0.75rem; padding:4px 12px; border-radius:50px; font-weight:800;">PROFITABLE</span>';
                } else {
                    statusBadgeSummary = '<span style="background:#dc2626; color:#ffffff; font-size:0.75rem; padding:4px 12px; border-radius:50px; font-weight:800;">LOSS MAKING</span>';
                }

                const skuPlFootHtml = `<tr>
                    <td colspan="2" style="text-align: left; font-weight: 800; font-size: 1rem; padding: 1.25rem 0.75rem;">TOTAL SUMMARY</td>
                    <td style="text-align: left; font-size: 1.1rem; font-weight: 800; padding-left: 1.25rem;">${tUnits.toLocaleString()}</td>
                    <td style="color: #1e40af; text-align: left; font-size: 1.1rem; font-weight: 800; padding-left: 1.25rem;">$${tRev.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                    <td style="color: #10b981; font-weight: 900; text-align: left; font-size: 1.2rem; padding-left: 1.25rem;">$${tNet.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                    <td style="text-align: left; padding-left: 1.25rem;"><span style="background: #10b98122; padding: 4px 10px; border-radius: 6px; font-size: 1rem; font-weight: 800;">${tMargin.toFixed(1)}%</span></td>
                    <td style="text-align: center;">${statusBadgeSummary}</td>
                </tr>`;
                $('#sku_pl_foot').html(skuPlFootHtml);

                (res.products || []).forEach((p, i) => {
                    const productRevenue = toNumber(p.revenue || p.sales || 0);
                    const productFullTitle = p.name || p.title || 'Unknown Product';
                    const productName = p.sku || productFullTitle; // Use SKU as the primary name
                    const productConv = toNumber(p.conv || p.conv_rate || 0);
                    const productUnits = toNumber(p.units || 0);
                    // Top Grid Cards
                    if (i < 8) {
                        prodHtml += `
                        <div class="product-item">
                            <div class="product-rank">${i+1}</div>
                            <span class="product-sku-tag">SKU: ${p.sku}</span>
                            <h5 class="product-card-title" title="${productFullTitle}">${p.sku}</h5>
                            <div class="product-metrics-pill">
                                <div class="metric-col revenue">
                                    <label>Revenue</label>
                                    <span>$${productRevenue.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span>
                                </div>
                                <div class="metric-col">
                                    <label>Conv</label>
                                    <span>${productConv.toFixed(1)}%</span>
                                </div>
                                <div class="metric-col">
                                    <label>Units</label>
                                    <span>${toNumber(p.units).toLocaleString()}</span>
                                </div>
                            </div>
                        </div>`;
                    }

                    // Detailed Analysis Table
                    const convVal = toNumber(p.conv || p.conv_rate || 0);
                    const refundRate = toNumber(p.refund_rate || 0);
                    const adSpend = toNumber(p.ad_spend || 0);
                    const acosVal = toNumber(p.acos || 0);
                    const adDepVal = toNumber(p.ad_dep || 0);
                    const buyBoxVal = toNumber(p.buy_box || 0);
                    const sessVal = toNumber(p.sessions_total || 0);

                    const convColor = convVal >= 10 ? '#10b981' : (convVal < 3 ? '#f59e0b' : '#64748b');
                    const refundColor = refundRate >= 5 ? '#ef4444' : '#64748b';

                    let statusHtml = '';
                    if (convVal > 15 && refundRate < 1) statusHtml = '<span class="status-pill status-star">STAR</span>';
                    else if (refundRate > 8) statusHtml = '<span class="status-pill status-risk">RISK</span>';
                    else if (adDepVal > 60) statusHtml = '<span class="status-pill status-ad">AD DEP</span>';

                    const maxSess = toNumber(res.products[0]?.sessions_total) || 1;
                    const sessPct = (sessVal / maxSess) * 100;

                    tableHtml += `<tr>
                        <td style="text-align:center;"><div class="rank-badge">${i+1}</div></td>
                        <td style="max-width:300px; white-space:normal; padding: 1.25rem 0.75rem;">
                            <div style="font-weight:800; color:#0f172a; margin-bottom:4px; font-size: 1.1rem; display:flex; align-items:center;">${p.sku} ${statusHtml}</div>
                            <div style="font-size:0.8rem; color:#64748b; font-weight:500; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="${productFullTitle}">${productFullTitle}</div>
                        </td>
                        <td style="font-weight:800; color: #1e40af; font-size: 1.1rem;">$${productRevenue.toLocaleString()}</td>
                        <td style="font-weight:800; font-size: 1.1rem; color: #1e293b;">${productUnits.toLocaleString()}</td>
                        <td style="color:#64748b; font-size:0.95rem; font-weight: 700;">${toNumber(p.total_orders).toLocaleString()}</td>
                        
                        <td style="font-weight:700; color: #f43f5e;">$${adSpend.toLocaleString()}</td>
                        <td style="text-align:center;"><span style="background: ${acosVal > 35 ? '#fef2f2' : '#f0fdf4'}; color: ${acosVal > 35 ? '#ef4444' : '#10b981'}; padding: 4px 10px; border-radius: 6px; font-weight:800; font-size:0.9rem;">${acosVal.toFixed(1)}%</span></td>
                        <td style="text-align:center;"><span style="color: ${adDepVal > 50 ? '#f59e0b' : '#64748b'}; font-weight:700;">${adDepVal.toFixed(1)}%</span></td>
                        
                        <td style="min-width: 120px;">
                            <div style="font-weight:700; color:#1e293b; margin-bottom:4px;">${sessVal.toLocaleString()}</div>
                            <div class="progress-container"><div class="progress-bar indigo" style="width: ${sessPct}%"></div></div>
                        </td>
                        <td style="text-align:center;"><span class="status-pill" style="background: ${convColor}22; color: ${convColor};">${convVal.toFixed(1)}%</span></td>
                        <td style="font-weight:700; color: #ef4444;">${toNumber(p.refunds).toLocaleString()}</td>
                        <td style="text-align:center;"><span class="status-pill" style="background: ${refundColor}22; color: ${refundColor};">${refundRate.toFixed(1)}%</span></td>
                        <td style="text-align:center; font-weight:800; color:#1e293b;">${buyBoxVal.toFixed(0)}%</td>
                    </tr>`;
                });
                $('#product_list').html(prodHtml);
                $('#product_analysis_body').html(tableHtml);
                $('#sku_pl_body').html(skuPlHtml || '<tr><td colspan="7" style="text-align:center;">No SKU data found.</td></tr>');

                // Populate Monthly SKU Matrix
                let mHtml = '';
                if (res.monthly_products && res.monthly_products.length > 0) {
                    res.monthly_products.forEach(m => {
                        mHtml += `<tr>
                            <td style="font-weight: 700; color: #64748b;">${m.month}</td>
                            <td style="font-weight: 800; color: #1e293b;">${m.asin}</td>
                            <td style="text-align: right; font-weight: 800; color: #4f46e5;">$${toNumber(m.revenue).toLocaleString()}</td>
                            <td style="text-align: center; font-weight: 700;">${toNumber(m.units).toLocaleString()}</td>
                            <td style="text-align: center;">${toNumber(m.sessions).toLocaleString()}</td>
                            <td style="text-align: center;"><span style="background: #eff6ff; padding: 4px 8px; border-radius: 6px; font-weight: 800; color: #3b82f6;">${toNumber(m.conv).toFixed(1)}%</span></td>
                        </tr>`;
                    });
                } else {
                    mHtml = '<tr><td colspan="6" class="text-center">No monthly historical data found.</td></tr>';
                }
                $('#monthly_sku_body').html(mHtml);

                // SKU P&L Table initialization
                if ($.fn.DataTable.isDataTable('#sku_pl_table')) $('#sku_pl_table').DataTable().destroy();
                $('#sku_pl_table').DataTable({
                    pageLength: 10,
                    order: [[2, 'desc']],
                    language: { search: "_INPUT_", searchPlaceholder: "Search SKU P&L..." }
                });

                loadSettlementAnalytics(customerId, from, to);
                loadProductAnalytics(customerId, from, to);
                animateCurrentTab();
            },
            complete: () => hideLoader()
        });
    }

    // Global Map reference
    let regionalMap = null;
    let mapMarkers = [];
    let geoJsonLayer = null;

    function toggleGeoSkuRow(state) {
        const row = $('#row-child-' + state.replace(/\s+/g, '_'));
        const chev = $('#chevron-' + state.replace(/\s+/g, '_'));
        if (row.is(':visible')) {
            row.fadeOut(200);
            chev.css('transform', 'rotate(0deg)');
        } else {
            row.fadeIn(200);
            chev.css('transform', 'rotate(90deg)');
        }
    }

    function initRegionalMap(regionList) {
        if (!$('link[href*="leaflet.css"]').length) {
            $('head').append('<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>');
        }
        if (typeof L === 'undefined') {
            $.getScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', function() {
                buildLeafletMap(regionList);
            });
        } else {
            buildLeafletMap(regionList);
        }
    }

    function buildLeafletMap(regionList) {
        if (regionalMap) {
            regionalMap.off();
            regionalMap.remove();
            regionalMap = null;
        }

        const stateCenters = {
            "Alabama": [32.806671, -86.791130], "Alaska": [61.370716, -152.404419], "Arizona": [33.729759, -111.431221], "Arkansas": [34.969704, -92.373123],
            "California": [36.116203, -119.681564], "Colorado": [39.059811, -105.311104], "Connecticut": [41.597782, -72.755371], "Delaware": [39.318523, -75.507141],
            "Florida": [27.766279, -81.686783], "Georgia": [33.040619, -83.643074], "Hawaii": [21.094318, -157.498337], "Idaho": [44.240459, -114.478828],
            "Illinois": [40.349457, -88.986137], "Indiana": [39.849426, -86.258278], "Iowa": [42.011539, -93.210526], "Kansas": [38.526600, -96.726486],
            "Kentucky": [37.668140, -84.670067], "Louisiana": [31.169546, -91.867805], "Maine": [44.693947, -69.381927], "Maryland": [39.063946, -76.802101],
            "Massachusetts": [42.230171, -71.530106], "Michigan": [43.326618, -84.536095], "Minnesota": [45.694454, -93.900192], "Mississippi": [32.741646, -89.678696],
            "Missouri": [38.456085, -92.288368], "Montana": [46.921925, -110.454353], "Nebraska": [41.125370, -98.268082], "Nevada": [38.313515, -117.055374],
            "New Hampshire": [43.452492, -71.563896], "New Jersey": [40.298904, -74.521011], "New Mexico": [34.840515, -106.248482], "New York": [42.165726, -74.948051],
            "North Carolina": [35.630066, -79.806419], "North Dakota": [47.528912, -99.784012], "Ohio": [40.388783, -82.764915], "Oklahoma": [35.565342, -96.928917],
            "Oregon": [44.572021, -122.070938], "Pennsylvania": [40.590752, -77.209755], "Rhode Island": [41.680893, -71.511780], "South Carolina": [33.856890, -80.945007],
            "South Dakota": [44.299782, -99.438828], "Tennessee": [35.747845, -86.692345], "Texas": [31.054487, -97.563461], "Utah": [40.150032, -111.862434],
            "Vermont": [44.045876, -72.710686], "Virginia": [37.769337, -78.169968], "Washington": [47.400902, -121.490494], "West Virginia": [38.491226, -80.954453],
            "Wisconsin": [44.268543, -89.616508], "Wyoming": [42.755966, -107.302490],
            "Quebec": [52.0, -72.0], "Ontario": [50.0, -85.0], "British Columbia": [53.72, -122.64], "Alberta": [55.0, -115.0],
            "Saskatchewan": [55.0, -106.0], "Manitoba": [55.0, -97.0], "Nova Scotia": [45.0, -63.0], "Newfoundland": [53.13, -57.66],
            "Newfoundland and Labrador": [53.13, -57.66], "New Brunswick": [46.56, -66.46], "Prince Edward Island": [46.51, -63.41],
            "Yukon": [64.0, -135.0], "Yukon Territory": [64.0, -135.0], "Northwest Territories": [65.0, -120.0], "Nunavut": [64.29, -98.10]
        };

        regionalMap = L.map('us_leaflet_map', {
            center: [55.0, -96.0],
            zoom: 3.5,
            zoomControl: true,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(regionalMap);

        const maxSales = Math.max(...regionList.map(r => r.total_sales)) || 1;

        const distinctColors = {
            "Quebec": "#6366f1",         // Indigo
            "Ontario": "#a855f7",        // Bright Purple
            "British Columbia": "#ec4899", // Sunset Pink
            "Alberta": "#f97316",        // Glowing Orange
            "Saskatchewan": "#10b981",   // Rich Emerald Green
            "Manitoba": "#f59e0b",       // Warm Amber Gold
            "Nova Scotia": "#06b6d4"     // Bright Electric Cyan
        };
        const getDistinctColor = (name) => {
            const clean = name.trim();
            if (distinctColors[clean]) return distinctColors[clean];
            let hash = 0;
            for (let i = 0; i < clean.length; i++) {
                hash = clean.charCodeAt(i) + ((hash << 5) - hash);
            }
            const colors = ['#6366f1', '#a855f7', '#ec4899', '#f97316', '#10b981', '#f59e0b', '#06b6d4', '#ef4444', '#3b82f6', '#f43f5e'];
            return colors[Math.abs(hash) % colors.length];
        };

        const stateAbbr = {
            "Alabama": "AL", "Alaska": "AK", "Arizona": "AZ", "Arkansas": "AR", "California": "CA", "Colorado": "CO", "Connecticut": "CT", "Delaware": "DE",
            "Florida": "FL", "Georgia": "GA", "Hawaii": "HI", "Idaho": "ID", "Illinois": "IL", "Indiana": "IN", "Iowa": "IA", "Kansas": "KS",
            "Kentucky": "KY", "Louisiana": "LA", "Maine": "ME", "Maryland": "MD", "Massachusetts": "MA", "Michigan": "MI", "Minnesota": "MN", "Mississippi": "MS",
            "Missouri": "MO", "Montana": "MT", "Nebraska": "NE", "Nevada": "NV", "New Hampshire": "NH", "New Jersey": "NJ", "New Mexico": "NM", "New York": "NY",
            "North Carolina": "NC", "North Dakota": "ND", "Ohio": "OH", "Oklahoma": "OK", "Oregon": "OR", "Pennsylvania": "PA", "Rhode Island": "RI", "South Carolina": "SC",
            "South Dakota": "SD", "Tennessee": "TN", "Texas": "TX", "Utah": "UT", "Vermont": "VT", "Virginia": "VA", "Washington": "WA", "West Virginia": "WV",
            "Wisconsin": "WI", "Wyoming": "WY", "Quebec": "QC", "Ontario": "ON", "British Columbia": "BC", "Alberta": "AB", "Manitoba": "MB", "Saskatchewan": "SK", "Nova Scotia": "NS"
        };

        Promise.all([
            fetch('https://raw.githubusercontent.com/PublicaMundi/MappingAPI/master/data/geojson/us-states.json').then(r => r.json()),
            fetch('https://raw.githubusercontent.com/codeforgermany/click_that_hood/main/public/data/canada.geojson').then(r => r.json())
        ]).then(([usData, caData]) => {
            const combinedData = {
                type: "FeatureCollection",
                features: [...usData.features, ...caData.features]
            };

            geoJsonLayer = L.geoJson(combinedData, {
                style: function(feature) {
                    const stateName = feature.properties.name;
                    const match = regionList.find(r => {
                        const prov = r.province.toLowerCase();
                        const feat = stateName.toLowerCase();
                        return feat === prov || feat.includes(prov) || prov.includes(feat);
                    });
                    const fillColor = !match ? '#f8fafc' : getDistinctColor(match.province);
                    
                    return {
                        fillColor: fillColor,
                        weight: 2,
                        opacity: 1,
                        color: '#ffffff',
                        fillOpacity: match ? 0.85 : 0.15
                    };
                },
                onEachFeature: function(feature, layer) {
                    const stateName = feature.properties.name;
                    const match = regionList.find(r => {
                        const prov = r.province.toLowerCase();
                        const feat = stateName.toLowerCase();
                        return feat === prov || feat.includes(prov) || prov.includes(feat);
                    });
                    const matchColor = match ? getDistinctColor(match.province) : '#4f46e5';
                    
                    let tooltipContent = `<div style="font-family: 'Inter', sans-serif; padding: 4px 8px;">
                        <div style="font-weight: 800; font-size: 0.85rem; color: #1e293b; margin-bottom: 2px;">${stateName}</div>`;
                    
                    if (match) {
                        tooltipContent += `
                            <div style="font-weight: 700; color: ${matchColor}; font-size: 0.8rem;">Sales: $${match.total_sales.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 600;">Orders: ${match.order_count.toLocaleString()} | Units: ${match.units_sold.toLocaleString()}</div>
                            <div style="font-size: 0.65rem; color: #10b981; font-weight: 700; margin-top: 4px;"><i class="fas fa-mouse-pointer"></i> Click to drilldown</div>
                        `;
                    } else {
                        tooltipContent += `<div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">No sales recorded</div>`;
                    }
                    tooltipContent += `</div>`;
                    
                    layer.bindTooltip(tooltipContent, { sticky: true });
                    
                    layer.on({
                        mouseover: function(e) {
                            const l = e.target;
                            l.setStyle({
                                fillOpacity: 0.95,
                                weight: 3.5,
                                color: '#1e293b'
                            });
                        },
                        mouseout: function(e) {
                            geoJsonLayer.resetStyle(e.target);
                        },
                        click: function() {
                            if (match) {
                                scrollToStateRow(match.province);
                            }
                        }
                    });
                }
            }).addTo(regionalMap);
        })
        .catch(err => {
            console.warn("GeoJSON load failed, falling back to dynamic coordinates bubbles.", err);
            $('#map_fallback_info').show();
        });

        regionList.forEach(p => {
            const coords = stateCenters[p.province];
            if (coords) {
                const stateColor = getDistinctColor(p.province);

                const abbr = stateAbbr[p.province] || p.province.substring(0, 2).toUpperCase();
                const salesVal = p.total_sales >= 1000 ? `$${(p.total_sales/1000).toFixed(1)}k` : `$${p.total_sales.toFixed(0)}`;

                const customLabelIcon = L.divIcon({
                    html: `<div style="background: ${stateColor}; color: #ffffff; padding: 6px 12px; border-radius: 50px; font-weight: 900; font-size: 0.75rem; white-space: nowrap; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.25); border: 2.5px solid #ffffff; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; gap: 4px; transition: transform 0.2s;" class="map-label-hover">
                        <span style="font-weight: 900; opacity: 0.95; font-size: 0.65rem; background: rgba(255,255,255,0.25); padding: 1px 4px; border-radius: 4px;">${abbr}</span>
                        <span>${salesVal}</span>
                    </div>`,
                    className: 'custom-state-label',
                    iconSize: [85, 32],
                    iconAnchor: [42, 16]
                });

                const marker = L.marker(coords, { icon: customLabelIcon }).addTo(regionalMap);

                let tooltipContent = `<div style="font-family: 'Inter', sans-serif; padding: 4px 8px; text-align: center;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: #1e293b; margin-bottom: 2px;">${p.province}</div>
                    <div style="font-weight: 900; color: ${stateColor}; font-size: 1.05rem; margin: 4px 0;">Sales: $${p.total_sales.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 4px;">Orders: ${p.order_count.toLocaleString()} | Units: ${p.units_sold.toLocaleString()}</div>
                    <div style="font-size: 0.65rem; color: #10b981; font-weight: 700; margin-top: 4px;"><i class="fas fa-mouse-pointer"></i> Click to drilldown</div>
                </div>`;
                
                marker.bindTooltip(tooltipContent, { sticky: true });

                let popupContent = `<div style="font-family: 'Inter', sans-serif; padding: 4px 8px; text-align: center;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: #1e293b; margin-bottom: 2px;">${p.province}</div>
                    <div style="font-weight: 900; color: ${stateColor}; font-size: 1.1rem; margin: 4px 0;">$${p.total_sales.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 4px;">${p.order_count.toLocaleString()} Orders | ${p.units_sold.toLocaleString()} Units</div>
                    <button class="btn btn-xs btn-primary" style="padding: 2px 6px; font-size: 0.65rem; font-weight: 800; border-radius: 4px;" onclick="scrollToStateRow('${p.province}')">View SKU Breakdown</button>
                </div>`;
                
                marker.bindPopup(popupContent);
                marker.on('click', function() {
                    scrollToStateRow(p.province);
                });

                mapMarkers.push(marker);
            }
        });
    }

    function scrollToStateRow(state) {
        const row = $(`.geo-parent-row[data-state="${state}"]`);
        if (row.length) {
            $('html, body').animate({
                scrollTop: row.offset().top - 200
            }, 600);
            
            toggleGeoSkuRow(state);
            
            row.css('background', '#fef3c7');
            setTimeout(() => {
                row.css('background', '');
            }, 2500);
        }
    }

    window.toggleGeoSkuRow = toggleGeoSkuRow;
    window.scrollToStateRow = scrollToStateRow;

    function loadSettlementAnalytics(customerId, from, to) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>api/transaction_analytics.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            dataType: 'json',
            success: function(res) {
                if (!res) return;
                
                // Populating Fee Breakdown List (Premium Styled)
                if (res.fee_breakdown) {
                    const colors = ['#f43f5e', '#8b5cf6', '#3b82f6', '#f59e0b', '#10b981', '#6366f1', '#94a3b8'];
                    let feeHtml = '<div style="display: flex; flex-direction: column; gap: 0.75rem;">';
                    const totalFeesVal = res.fee_breakdown.values.reduce((a, b) => a + b, 0);
                    
                    res.fee_breakdown.labels.forEach((label, idx) => {
                        const val = res.fee_breakdown.values[idx];
                        const pct = totalFeesVal > 0 ? ((val / totalFeesVal) * 100).toFixed(1) : 0;
                        const color = colors[idx % colors.length];
                        
                        feeHtml += `
                            <div style="padding: 1rem; background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; transition: all 0.3s; cursor: default; position: relative; overflow: hidden;">
                                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: ${color};"></div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">${label}</span>
                                    <span style="font-weight: 900; color: ${color};">$${val.toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1; height: 6px; background: #f1f5f9; border-radius: 10px;">
                                        <div style="height: 100%; width: ${pct}%; background: ${color}; border-radius: 10px;"></div>
                                    </div>
                                    <span style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; min-width: 35px;">${pct}%</span>
                                </div>
                            </div>
                        `;
                    });
                    feeHtml += '</div>';
                    $('#fees_breakdown_list').html(feeHtml);
                    $('#total_fees_donut_val').text('$' + totalFeesVal.toLocaleString(undefined, {maximumFractionDigits: 0}));
                    renderFeeChart(res.fee_breakdown);
                }

                // Populating Region Table with SKU Breakdown
                if (res.province_breakdown) {
                    let regionList = res.province_breakdown;
                    regionList.sort((a, b) => b.total_sales - a.total_sales);
                    
                    let regHtml = '';
                    regionList.forEach((p, idx) => {
                        const grossProfitColor = p.gross_profit >= 0 ? '#10b981' : '#f43f5e';
                        const grossProfitSign = p.gross_profit >= 0 ? '' : '-';
                        const formattedGross = p.gross_profit >= 0 ? p.gross_profit : Math.abs(p.gross_profit);

                        regHtml += `
                            <tr class="geo-parent-row" data-state="${p.province}" style="cursor: pointer; transition: background 0.2s; border-bottom: 1px solid #cbd5e1;" onclick="toggleGeoSkuRow('${p.province}')">
                                <td style="border: 1px solid #94a3b8; padding: 1rem; text-align: center; font-weight: 800; color: #1e293b;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                        <i class="fas fa-chevron-right geo-chevron" id="chevron-${p.province.replace(/\s+/g, '_')}" style="font-size: 0.75rem; color: #94a3b8; transition: transform 0.2s;"></i>
                                        <span>${p.province}</span>
                                    </div>
                                </td>
                                <td style="border: 1px solid #94a3b8; padding: 1rem; text-align: center; color: #475569; font-weight: 700;">${p.order_count.toLocaleString()}</td>
                                <td style="border: 1px solid #94a3b8; padding: 1rem; text-align: center; color: #475569; font-weight: 700;">${p.units_sold.toLocaleString()}</td>
                                <td style="border: 1px solid #94a3b8; padding: 1rem; text-align: center; color: #1e293b; font-weight: 800;">$${p.total_sales.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td style="border: 1px solid #94a3b8; padding: 1rem; text-align: center; color: #ef4444; font-weight: 700;">-$${Math.abs(p.fees).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td style="border: 1px solid #94a3b8; padding: 1rem; text-align: center; color: #ef4444; font-weight: 700;">$${p.refunds.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td style="border: 1px solid #94a3b8; padding: 1rem; text-align: center; color: ${grossProfitColor}; font-weight: 900;">${grossProfitSign}$${formattedGross.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            </tr>
                            <tr class="geo-child-row" id="row-child-${p.province.replace(/\s+/g, '_')}" style="display: none; background: #f8fafc;">
                                <td colspan="7" style="padding: 1.5rem 2.5rem; border: 1px solid #94a3b8;">
                                    <div style="font-weight: 800; color: #475569; margin-bottom: 0.75rem; text-align: left; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-box-open" style="color: #6366f1;"></i>
                                        <span>SKU Performance Breakdown in ${p.province}</span>
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #94a3b8; background: #fff; border-radius: 8px; overflow: hidden;">
                                        <thead>
                                            <tr style="background: #f1f5f9; border-bottom: 2px solid #94a3b8;">
                                                <th style="border: 1px solid #94a3b8; padding: 0.75rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569;">Product SKU</th>
                                                <th style="border: 1px solid #94a3b8; padding: 0.75rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569;">Orders</th>
                                                <th style="border: 1px solid #94a3b8; padding: 0.75rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569;">Units Sold</th>
                                                <th style="border: 1px solid #94a3b8; padding: 0.75rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569;">Sales</th>
                                                <th style="border: 1px solid #94a3b8; padding: 0.75rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569;">Amazon Fees</th>
                                                <th style="border: 1px solid #94a3b8; padding: 0.75rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569;">Refund Cost</th>
                                                <th style="border: 1px solid #94a3b8; padding: 0.75rem; text-align: center; font-size: 0.8rem; font-weight: 800; color: #475569;">Gross Profit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${p.skus.length === 0 ? 
                                                `<tr><td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8; font-weight: 700;">No product sales in this region.</td></tr>` : 
                                                p.skus.map(s => {
                                                    const sGrossColor = s.gross_profit >= 0 ? '#10b981' : '#f43f5e';
                                                    const sGrossSign = s.gross_profit >= 0 ? '' : '-';
                                                    const sFormattedGross = s.gross_profit >= 0 ? s.gross_profit : Math.abs(s.gross_profit);
                                                    return `
                                                        <tr style="transition: background 0.15s;">
                                                            <td style="border: 1px solid #cbd5e1; padding: 0.75rem; text-align: center;">
                                                                <div style="font-weight: 800; color: #1e293b; font-size: 0.8rem;">${s.sku}</div>
                                                                <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;" title="${s.product_name}">${s.product_name}</div>
                                                            </td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 0.75rem; text-align: center; color: #475569; font-weight: 700; font-size: 0.8rem;">${s.order_count.toLocaleString()}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 0.75rem; text-align: center; color: #475569; font-weight: 700; font-size: 0.8rem;">${s.units_sold.toLocaleString()}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 0.75rem; text-align: center; color: #1e293b; font-weight: 800; font-size: 0.8rem;">$${s.sales.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 0.75rem; text-align: center; color: #ef4444; font-weight: 700; font-size: 0.8rem;">-$${Math.abs(s.fees).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 0.75rem; text-align: center; color: #ef4444; font-weight: 700; font-size: 0.8rem;">$${s.refunds.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 0.75rem; text-align: center; color: ${sGrossColor}; font-weight: 800; font-size: 0.8rem;">${sGrossSign}$${sFormattedGross.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                        </tr>
                                                    `;
                                                }).join('')
                                            }
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        `;
                    });
                    
                    $('#region_sales_body').html(regHtml || '<tr><td colspan="7" style="text-align:center; padding: 2rem; color: #94a3b8; font-weight: 700;">No regional data.</td></tr>');
                    
                    // Initialize Regional Map visuals
                    initRegionalMap(regionList);
                }

                renderFinancialInsights(res.insights);
            }
        });
    }

    function renderFeeChart(feeData) {
        const ctx = document.getElementById('feesChart').getContext('2d');
        if (window.feesChartInst) window.feesChartInst.destroy();
        window.feesChartInst = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: feeData.labels,
                datasets: [{
                    data: feeData.values,
                    backgroundColor: ['#f43f5e', '#8b5cf6', '#3b82f6', '#f59e0b', '#10b981', '#6366f1', '#94a3b8'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: { cutout: '60%', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    function renderFeeDonut(feeData) {
        const ctx = document.getElementById('expenseChart').getContext('2d');
        if (expenseChart) expenseChart.destroy();
        
        expenseChart = new Chart(ctx, {
            type: 'bar',
            data: { 
                labels: feeData.labels, 
                datasets: [{ 
                    data: feeData.values, 
                    backgroundColor: ['#f43f5e', '#8b5cf6', '#3b82f6', '#f59e0b', '#10b981', '#6366f1', '#94a3b8'], 
                    borderRadius: 8,
                    maxBarThickness: 50
                }] 
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => ' Amount: $' + context.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { weight: '600', size: 10 } } },
                    y: { 
                        grid: { color: '#f1f5f9' }, 
                        ticks: { callback: v => '$' + formatAbbrev(v) } 
                    }
                }
            }
        });
        const total = feeData.values.reduce((a, b) => a + b, 0);
        $('#fee_breakdown_note').html(`
            <div style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin-top: 1rem;">$${total.toLocaleString()}</div>
            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Total Platform Fees</div>
        `);
    }

    function renderProvinceBars(provinces) {
        const ctx = document.getElementById('provinceChart').getContext('2d');
        if (provinceChart) provinceChart.destroy();

        // Sort and group for a clean distribution view
        const sorted = [...provinces].sort((a, b) => b.total_sales - a.total_sales);
        const topCount = 8;
        const top = sorted.slice(0, topCount);
        const others = sorted.slice(topCount);
        
        let finalData = top.map(p => ({ label: p.province, value: p.total_sales }));
        if (others.length > 0) {
            const othersSum = others.reduce((sum, p) => sum + p.total_sales, 0);
            finalData.push({ label: 'Other Provinces', value: othersSum });
        }

        provinceChart = new Chart(ctx, {
            type: 'doughnut',
            data: { 
                labels: finalData.map(d => d.label), 
                datasets: [{ 
                    data: finalData.map(d => d.value), 
                    backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#94a3b8'],
                    hoverOffset: 20,
                    borderWidth: 5,
                    borderColor: '#ffffff'
                }] 
            },
            options: { 
                cutout: '65%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { 
                            usePointStyle: true, 
                            padding: 20,
                            font: { size: 11, weight: '600' },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => ` $${context.parsed.toLocaleString()}`
                        }
                    }
                }
            }
        });
    }

    function renderTxnSummary(summary) {
        let html = '';
        const types = summary || {};
        if (Object.keys(types).length === 0) {
            html = '<div style="text-align: center; color: #94a3b8; padding: 3rem;">No transactions found for this period.</div>';
        } else {
            html = '<div style="display: grid; gap: 1rem;">';
            for (let type in types) {
                const s = types[type];
                const isPositive = s.total_amount >= 0;
                const accentColor = isPositive ? '#10b981' : '#f43f5e';
                
                html += `
                <div style="background: #ffffff; border: 1px solid #f1f5f9; border-left: 4px solid ${accentColor}; padding: 1.25rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <span style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">${type}</span>
                        <span style="font-weight: 800; color: ${accentColor}; font-size: 1.1rem;">
                            ${isPositive ? '' : '-'}$${Math.abs(s.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2})}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: capitalize; letter-spacing: 0.05em;">${s.total_count} Transactions</span>
                        <div style="width: 32px; height: 32px; background: ${accentColor}15; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: ${accentColor};">
                            <i class="fas ${isPositive ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'}" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>
                </div>`;
            }
            html += '</div>';
        }
        $('#txn_summary_list').html(html);
    }

    function renderFinancialInsights(insights) {
        let html = '';
        (insights || []).forEach(ins => {
            html += `<div style="background:#f0f9ff; border-left:4px solid #7dd3fc; padding:1rem; border-radius:8px; margin-bottom:1rem;">
                <h4 style="font-size:0.9rem; font-weight:700;">${ins.title}</h4><p style="font-size:0.8rem;">${ins.text}</p>
            </div>`;
        });
        $('#financial_insights_container').html(html);
    }

    function loadProductAnalytics(customerId, from, to) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>api/product_analytics.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            dataType: 'json',
            success: function(res) {
                if (!res || !res.success) return;
                
                // 1. Revenue Share by SKU (Doughnut)
                const ctxRev = document.getElementById('productRevenueShareChart').getContext('2d');
                if (window.productRevenueShareChartInst) window.productRevenueShareChartInst.destroy();
                window.productRevenueShareChartInst = new Chart(ctxRev, {
                    type: 'doughnut',
                    data: {
                        labels: res.top_products.slice(0,8).map(p => p.sku),
                        datasets: [{
                            data: res.top_products.slice(0,8).map(p => p.revenue),
                            backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e', '#f97316', '#eab308'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: { 
                        cutout: '75%', 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { 
                            legend: { position: 'right', labels: { boxWidth: 12, usePointStyle: true, font: { size: 11, weight: '600' } } },
                            tooltip: { backgroundColor: '#0f172a', padding: 12 }
                        } 
                    }
                });

                // 2. ULTIMATE COMBO REPORT (One report for everything)
                const ctxCombo = document.getElementById('productComboChart').getContext('2d');
                if (window.productComboChartInst) window.productComboChartInst.destroy();
                
                const labels = res.top_products.slice(0, 12).map(p => p.sku); 
                
                window.productComboChartInst = new Chart(ctxCombo, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Page Views',
                                data: res.top_products.slice(0, 12).map(p => p.page_views),
                                backgroundColor: 'rgba(147, 185, 189, 0.4)',
                                borderColor: '#93B9BD',
                                borderWidth: 1,
                                yAxisID: 'yVolume',
                                order: 4
                            },
                            {
                                label: 'Sessions',
                                data: res.top_products.slice(0, 12).map(p => p.sessions),
                                backgroundColor: 'rgba(139, 92, 246, 0.5)',
                                borderColor: '#8B5CF6',
                                borderWidth: 1,
                                yAxisID: 'yVolume',
                                order: 3
                            },
                            {
                                label: 'Revenue ($)',
                                data: res.top_products.slice(0, 12).map(p => p.revenue),
                                type: 'line',
                                borderColor: '#4F46E5',
                                backgroundColor: '#4F46E5',
                                borderWidth: 3,
                                pointRadius: 4,
                                tension: 0.3,
                                yAxisID: 'yRevenue',
                                order: 1
                            },
                            {
                                label: 'Conv %',
                                data: res.top_products.slice(0, 12).map(p => p.conv),
                                type: 'line',
                                borderColor: '#10B981',
                                backgroundColor: '#10B981',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                pointRadius: 4,
                                yAxisID: 'yPercent',
                                order: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.dataset.yAxisID === 'yRevenue') label += '$' + context.parsed.y.toLocaleString();
                                        else if (context.dataset.yAxisID === 'yPercent') label += context.parsed.y.toFixed(2) + '%';
                                        else label += context.parsed.y.toLocaleString();
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { weight: '600' } } },
                            yVolume: {
                                type: 'linear',
                                position: 'left',
                                title: { display: true, text: 'Traffic Volume', font: { weight: '700' } },
                                grid: { color: '#f1f5f9' }
                            },
                            yRevenue: {
                                type: 'linear',
                                position: 'right',
                                title: { display: true, text: 'Revenue ($)', font: { weight: '700' } },
                                grid: { display: false },
                                ticks: { callback: v => '$' + formatAbbrev(v) }
                            },
                            yPercent: {
                                type: 'linear',
                                position: 'right',
                                title: { display: true, text: 'Conv %', font: { weight: '700' } },
                                grid: { display: false },
                                ticks: { callback: v => v + '%' },
                                min: 0,
                                display: false
                            }
                        }
                    }
                });

                // Product Performance Table initialization
                if ($.fn.DataTable.isDataTable('#product_perf_table')) $('#product_perf_table').DataTable().destroy();
                $('#product_perf_table').DataTable({
                    pageLength: 10,
                    order: [[2, 'desc']],
                    language: { search: "_INPUT_", searchPlaceholder: "Search Products..." }
                });
            }
        });
    }

    // Tab switching handled via sidebar navigation (page reload)

    $('.chart-tab-btn').click(function() {
        $('.chart-tab-btn').removeClass('active'); $(this).addClass('active');
        renderChart($(this).data('chart'));
    });

    // Dashboard Initialization - Manual mode enabled. 
    // Data only loads after clicking REFRESH ANALYSIS.
    // Dashboard Initialization
    $('#filter_from').val('2026-01-01');
    $('#filter_to').val('2026-03-31');
    loadDashboard();

    $('#save_financials_new').click(function() {
        const customerId = $('#customer_id_hidden').length ? $('#customer_id_hidden').val() : $('#filter_customer').val();
        const from = $('#filter_from').val();
        if (!from) { alert('Please select a date range first'); return; }
        const reportMonth = from.substring(0, 7); // YYYY-MM
        
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> SAVING...');

        $.ajax({
            url: '<?php echo BASE_URL; ?>api/save_financials.php',
            method: 'POST',
            data: {
                customer_id: customerId,
                report_month: reportMonth,
                cogs: $('#cogs_override_new').val(),
                ad_spend: 0,
                other_fees: $('#other_fees_override_new').val()
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Saved!', text: 'Financial settings updated for ' + reportMonth, timer: 1500 });
                    loadDashboard();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.error });
                }
            },
            complete: () => btn.prop('disabled', false).html('SAVE SETTINGS')
        });
    });

    $('#apply_filters').click(loadDashboard);
});
</script>

<?php include '../../includes/footer.php'; ?>
