<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Advertising Intelligence";
$page_subtitle = "Sponsored Products, Brands & Display Analytics";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 16px;">
    <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 250px;">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Amazon Profile</label>
            <select id="filter_customer" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                <option value="">All Amazon Profiles</option>
                <?php 
                $customers = get_all_customers();
                while ($row = $customers->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['customer_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Period</label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="date" id="filter_from" class="form-control" value="2026-01-01" style="padding: 0.6rem; border-radius: 8px;">
                <span style="color: #94a3b8;">to</span>
                <input type="date" id="filter_to" class="form-control" value="2026-03-31" style="padding: 0.6rem; border-radius: 8px;">
            </div>
        </div>
        <button id="refresh_ads" class="btn btn-primary" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-sync-alt"></i> REFRESH
        </button>
    </div>
</div>

<!-- ==================== DEMO LAYOUT SWITCHER BAR ==================== -->
<div class="card" style="margin-bottom: 2rem; padding: 0.75rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(0,0,0,0.02); flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 8px;">
        <span style="font-weight: 800; color: #475569; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;"><i class="fas fa-sliders-h" style="color: #6366f1;"></i> Presentational Engine:</span>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-grow: 1; justify-content: flex-end; max-width: 600px;">
        <button class="demo-switch-btn active" data-view="1" style="flex: 1; padding: 0.65rem 1rem; border-radius: 10px; border: none; background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #1d4ed8; font-weight: 800; border-bottom: 3px solid #3b82f6; cursor: pointer; transition: all 0.2s; outline: none; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 6px;">
            <i class="fas fa-th-list"></i> Demo 1: Executive Grid
        </button>
        <button class="demo-switch-btn" data-view="2" style="flex: 1; padding: 0.65rem 1rem; border-radius: 10px; border: none; background: white; color: #64748b; font-weight: 700; cursor: pointer; transition: all 0.2s; outline: none; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 6px;">
            <i class="fas fa-chart-bar"></i> Demo 2: Chart Analyzer
        </button>
        <button class="demo-switch-btn" data-view="3" style="flex: 1; padding: 0.65rem 1rem; border-radius: 10px; border: none; background: white; color: #64748b; font-weight: 700; cursor: pointer; transition: all 0.2s; outline: none; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 6px;">
            <i class="fas fa-table"></i> Demo 3: Pivot Matrix Sheet
        </button>
    </div>
</div>

<!-- =================================================================== -->
<!-- ==================== DEMO 1: EXECUTIVE FINANCIAL GRID ============ -->
<!-- =================================================================== -->
<div id="demo_view_1" class="demo-view-pane">
    <!-- Header Matrix Title -->
    <section class="bento-card overflow-hidden mb-8" style="background:#ffffff; border-radius:16px; border: 1px solid #c6c6cd; overflow:hidden; margin-bottom: 2rem;">
        <div class="border-b border-outline-variant flex justify-between items-center" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #c6c6cd; padding: 24px 32px;">
            <h3 class="font-headline-md text-headline-md text-primary" style="font-size: 22px; font-weight: 700; color: #000000; margin: 0;">Unified Portfolio Channels Performance Grid</h3>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; margin: 0;">
                <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em;">Ad Channel</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Spend</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Sales</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ACoS</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ROAS</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Clicks</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Impressions</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">CTR</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Avg. CPC</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">CvR</th>
                    </tr>
                </thead>
                <tbody id="demo1_channel_matrix_body" style="background:#ffffff;">
                    <!-- Dynamically populated from JS -->
                </tbody>
            </table>
        </div>
    </section>

    <!-- Match Types & Diagnostics Grid -->
    <div style="display: flex; flex-direction: column; gap: 2rem; margin-bottom: 2rem;">
        <!-- Match Type Analysis Matrix -->
        <section class="bento-card overflow-hidden mb-8" style="background:#ffffff; border-radius:16px; border: 1px solid #c6c6cd; overflow:hidden;">
            <div class="border-b border-outline-variant flex justify-between items-center" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #c6c6cd; padding: 24px 32px;">
                <h3 class="font-headline-md text-headline-md text-primary" style="font-size: 22px; font-weight: 700; color: #000000; margin: 0;"><i class="fas fa-tags" style="color: #8b5cf6; margin-right: 8px;"></i> Match Type Performance Matrix</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                        <tr>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left;">Match Type</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Spend</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Sales</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ACoS</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ROAS</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">CTR</th>
                        </tr>
                    </thead>
                    <tbody id="demo1_match_type_body" style="background:#ffffff;">
                        <!-- JS populated -->
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Bidding & Placements Matrix -->
        <section class="bento-card overflow-hidden mb-8" style="background:#ffffff; border-radius:16px; border: 1px solid #c6c6cd; overflow:hidden;">
            <div class="border-b border-outline-variant flex justify-between items-center" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #c6c6cd; padding: 24px 32px;">
                <h3 class="font-headline-md text-headline-md text-primary" style="font-size: 22px; font-weight: 700; color: #000000; margin: 0;"><i class="fas fa-bullseye" style="color: #10b981; margin-right: 8px;"></i> Placement & Bidding Top Share</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                        <tr>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left;">Target Segment / Strategy</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Spend</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Sales</th>
                            <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ROAS</th>
                        </tr>
                    </thead>
                    <tbody id="demo1_placement_bid_body" style="background:#ffffff;">
                        <!-- JS populated -->
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Top Performing Search Terms / Keywords Grid -->
    <!-- Top Performing Search Terms / Keywords Grid -->
    <section class="bento-card overflow-hidden mb-8" style="background:#ffffff; border-radius:16px; border: 1px solid #c6c6cd; overflow:hidden; margin-bottom: 2rem;">
        <div class="border-b border-outline-variant flex justify-between items-center" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #c6c6cd; padding: 24px 32px;">
            <h3 class="font-headline-md text-headline-md text-primary" style="font-size: 22px; font-weight: 700; color: #000000; margin: 0;"><i class="fas fa-search" style="color: #6366f1; margin-right: 8px;"></i> Top Performing Search Terms & Keywords</h3>
            <div class="flex items-center gap-4" style="display: flex; align-items: center; gap: 16px;">
                <div class="relative" style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #45464d; pointer-events: none;"></i>
                    <input type="text" id="demo1_kw_search" style="padding-left: 36px; padding-right: 16px; padding-top: 8px; padding-bottom: 8px; border: 1px solid #c6c6cd; border-radius: 8px; outline: none; background: #f2f4f6; font-size: 14px; font-weight: 600; width: 220px;" placeholder="Search keywords...">
                </div>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                    <tr>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left;">Keyword / Query</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">Match Type</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">Ad Type</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Spend</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Sales</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ACoS</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ROAS</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Clicks</th>
                        <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">CTR</th>
                    </tr>
                </thead>
                <tbody id="demo1_kw_body" style="background:#ffffff;">
                    <!-- Dynamically populated -->
                </tbody>
            </table>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; background: #f2f4f6; border-top: 1px solid #c6c6cd; padding: 16px 32px; box-sizing: border-box;">
            <p style="font-size: 12px; color: #45464d; margin: 0;" id="demo1_kw_info">Showing 1 to 10 of 0 entries</p>
            <div style="display: flex; gap: 8px;" id="demo1_kw_pagination"></div>
        </div>
    </section>

    <!-- Product Diagnostic Grid Switcher -->
    <section class="bento-card overflow-hidden mb-8" style="background:#ffffff; border-radius:16px; border: 1px solid #c6c6cd; overflow:hidden; margin-bottom: 2rem;">
        <div class="border-b border-outline-variant flex justify-between items-center" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #c6c6cd; padding: 24px 32px; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3 class="font-headline-md text-headline-md text-primary" style="font-size: 22px; font-weight: 700; color: #000000; margin: 0;"><i class="fas fa-box" style="color: #3b82f6; margin-right: 8px;"></i> SKU Performance</h3>
                <div style="display: flex; background: #f2f4f6; padding: 3px; border-radius: 8px; border: 1px solid #c6c6cd;">
                    <button id="demo1_sku_sp_btn" class="active" style="border: none; background: white; color: #0051d5; font-weight: 800; font-size: 13px; padding: 6px 14px; border-radius: 6px; cursor: pointer;">SP SKUs</button>
                    <button id="demo1_sku_sb_btn" style="border: none; background: transparent; color: #45464d; font-weight: 700; font-size: 13px; padding: 6px 14px; border-radius: 6px; cursor: pointer;">SB Halo ASINs</button>
                </div>
            </div>
            <div class="flex items-center gap-4" style="display: flex; align-items: center; gap: 16px;">
                <div class="relative" style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #45464d; pointer-events: none;"></i>
                    <input type="text" id="demo1_sku_search" style="padding-left: 36px; padding-right: 16px; padding-top: 8px; padding-bottom: 8px; border: 1px solid #c6c6cd; border-radius: 8px; outline: none; background: #f2f4f6; font-size: 14px; font-weight: 600; width: 220px;" placeholder="Search SKU or ASIN...">
                </div>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead id="demo1_sku_head" style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                    <!-- Dynamic headers -->
                </thead>
                <tbody id="demo1_sku_body" style="background:#ffffff;">
                    <!-- Dynamically populated -->
                </tbody>
            </table>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; background: #f2f4f6; border-top: 1px solid #c6c6cd; padding: 16px 32px; box-sizing: border-box;">
            <p style="font-size: 12px; color: #45464d; margin: 0;" id="demo1_sku_info">Showing 1 to 10 of 0 entries</p>
            <div style="display: flex; gap: 8px;" id="demo1_sku_pagination"></div>
        </div>
    </section>
</div>

<!-- =================================================================== -->
<!-- ==================== DEMO 2: ANALYTICAL CHART ANALYZER ============ -->
<!-- =================================================================== -->
<div id="demo_view_2" class="demo-view-pane" style="display: none;">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <!-- Left Pane: Big Chart Analyzer -->
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden;">
            <div style="background: #ffffff; padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <span style="font-weight: 900; color: #1e293b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;"><i class="fas fa-chart-line" style="color: #6366f1;"></i> Advanced Multi-axis Analytics</span>
                <div style="display: flex; background: #f1f5f9; padding: 3px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <button class="demo2-chart-type-btn active" data-chart="spend_sales" style="border: none; background: white; color: #1d4ed8; font-weight: 800; font-size: 11px; padding: 5px 12px; border-radius: 6px; cursor: pointer;">Spend vs Sales</button>
                    <button class="demo2-chart-type-btn" data-chart="match_types" style="border: none; background: transparent; color: #64748b; font-weight: 700; font-size: 11px; padding: 5px 12px; border-radius: 6px; cursor: pointer;">Match Types</button>
                    <button class="demo2-chart-type-btn" data-chart="shares" style="border: none; background: transparent; color: #64748b; font-weight: 700; font-size: 11px; padding: 5px 12px; border-radius: 6px; cursor: pointer;">Channel Share</button>
                </div>
            </div>
            <div style="padding: 1.5rem; height: 360px;">
                <canvas id="demo2_main_chart"></canvas>
            </div>
        </div>

        <!-- Right Pane: Flat Data Stream Diagnostic Sidebar -->
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 1.5rem; background: white; display: flex; flex-direction: column; justify-content: space-between;">
            <h4 style="font-weight: 900; color: #1e293b; font-size: 0.85rem; margin: 0 0 1rem 0; text-transform: uppercase; letter-spacing: 0.05em;"><i class="fas fa-microchip" style="color: #10b981;"></i> Real-Time Efficiency Stream</h4>
            <div id="demo2_metrics_container" style="display: flex; flex-direction: column; gap: 14px; flex-grow: 1;">
                <!-- Metric items populated from JS -->
            </div>
        </div>
    </div>

    <!-- Keywords & Target breakdown stacked under -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden;">
            <div style="background: white; padding: 1.25rem; border-bottom: 1px solid #f1f5f9; font-weight: 900; color: #1e293b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;"><i class="fas fa-keyboard" style="color: #ec4899;"></i> Top 5 Keywords by Spend</div>
            <div style="padding: 0;">
                <table class="table align-middle" style="width:100%; margin: 0;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b;">Keyword</th>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b; text-align:right;">Spend</th>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b; text-align:center;">ACoS</th>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b; text-align:center;">ROAS</th>
                        </tr>
                    </thead>
                    <tbody id="demo2_top5_kws" style="font-size:12.5px; font-weight: 600;"></tbody>
                </table>
            </div>
        </div>
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden;">
            <div style="background: white; padding: 1.25rem; border-bottom: 1px solid #f1f5f9; font-weight: 900; color: #1e293b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;"><i class="fas fa-boxes" style="color: #3b82f6;"></i> Top 5 SKUs by Spend</div>
            <div style="padding: 0;">
                <table class="table align-middle" style="width:100%; margin: 0;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b;">SKU</th>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b; text-align:right;">Spend</th>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b; text-align:center;">ACoS</th>
                            <th style="padding: 8px 12px; font-size:11px; color:#64748b; text-align:center;">ROAS</th>
                        </tr>
                    </thead>
                    <tbody id="demo2_top5_skus" style="font-size:12.5px; font-weight: 600;"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- =================================================================== -->
<!-- ==================== DEMO 3: UNIFIED MATRIX PIVOT SHEET ========== -->
<!-- =================================================================== -->
<div id="demo_view_3" class="demo-view-pane" style="display: none;">
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 25px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 2rem;">
        <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 900; color: #1e293b; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px;"><i class="fas fa-table" style="color: #10b981;"></i> Advertising Channels Interactive Tree-Pivot Sheet</span>
            <span style="font-size: 11px; font-weight: 800; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 6px;">Excel Pivot Grid Format</span>
        </div>
        <div style="padding: 0; overflow-x: auto;">
            <table class="table align-middle" style="width: 100%; border-collapse: collapse; margin: 0; border: 1px solid #cbd5e1;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #94a3b8; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase;">
                        <th style="padding: 10px 14px; width: 30%; border: 1px solid #cbd5e1;">Hierarchy (Channel -> Campaign -> Target)</th>
                        <th style="padding: 10px 14px; text-align: right; border: 1px solid #cbd5e1; width: 10%;">Spend</th>
                        <th style="padding: 10px 14px; text-align: right; border: 1px solid #cbd5e1; width: 10%;">Sales</th>
                        <th style="padding: 10px 14px; text-align: center; border: 1px solid #cbd5e1; width: 10%;">ACoS</th>
                        <th style="padding: 10px 14px; text-align: center; border: 1px solid #cbd5e1; width: 10%;">ROAS</th>
                        <th style="padding: 10px 14px; text-align: right; border: 1px solid #cbd5e1; width: 10%;">Clicks</th>
                        <th style="padding: 10px 14px; text-align: right; border: 1px solid #cbd5e1; width: 10%;">Impressions</th>
                        <th style="padding: 10px 14px; text-align: center; border: 1px solid #cbd5e1; width: 10%;">CTR</th>
                    </tr>
                </thead>
                <tbody id="demo3_pivot_body" style="font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; font-weight: 700;">
                    <!-- Populated dynamically with collapsible rows -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let demo2MainChart = null;
    let rawApiResponse = null;

    // Cache pagination and query states for Demo 1 KeyWords
    let demo1KwPage = 1;
    let demo1KwQuery = "";
    let demo1SkuPage = 1;
    let demo1SkuQuery = "";
    let demo1SkuTab = "sp"; // sp or sb
    const ROWS_PER_PAGE = 10;

    window.changeKwPage = function(page) {
        demo1KwPage = page;
        renderDemo1KwGrid();
    };
    window.changeSkuPage = function(page) {
        demo1SkuPage = page;
        renderDemo1SkuGrid();
    };

    function renderTablePagination(containerId, totalItems, currentPage, itemsPerPage, onPageChangeName) {
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
        let html = '';
        
        // Previous Button
        const prevDisabled = currentPage === 1;
        html += `<button class="page-btn" ${prevDisabled ? 'disabled' : ''} onclick="window.${onPageChangeName}(${currentPage - 1})" style="
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: ${prevDisabled ? '#f8fafc' : '#ffffff'};
            color: ${prevDisabled ? '#cbd5e1' : '#64748b'};
            font-weight: 600;
            cursor: ${prevDisabled ? 'not-allowed' : 'pointer'};
            transition: all 0.2s;
            outline: none;
            padding: 0;
        ">&lsaquo;</button>`;

        // Page Numbers
        let startPage = 1;
        let endPage = totalPages;
        if (totalPages > 7) {
            if (currentPage <= 4) {
                endPage = 5;
            } else if (currentPage >= totalPages - 3) {
                startPage = totalPages - 4;
            } else {
                startPage = currentPage - 2;
                endPage = currentPage + 2;
            }
        }

        if (startPage > 1) {
            html += `<button class="page-btn" onclick="window.${onPageChangeName}(1)" style="
                display: flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                background: #ffffff;
                color: #64748b;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                outline: none;
                padding: 0;
            ">1</button>`;
            if (startPage > 2) {
                html += `<span style="display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; color: #94a3b8; font-weight: 600;">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            html += `<button class="page-btn ${isActive ? 'active' : ''}" onclick="window.${onPageChangeName}(${i})" style="
                display: flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                border-radius: 8px;
                border: 1px solid ${isActive ? '#4b5563' : '#e2e8f0'};
                background: ${isActive ? '#4b5563' : '#ffffff'};
                color: ${isActive ? '#ffffff' : '#64748b'};
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                outline: none;
                padding: 0;
            ">${i}</button>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<span style="display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; color: #94a3b8; font-weight: 600;">...</span>`;
            }
            html += `<button class="page-btn" onclick="window.${onPageChangeName}(${totalPages})" style="
                display: flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                background: #ffffff;
                color: #64748b;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
                outline: none;
                padding: 0;
            ">${totalPages}</button>`;
        }

        // Next Button
        const nextDisabled = currentPage === totalPages;
        html += `<button class="page-btn" ${nextDisabled ? 'disabled' : ''} onclick="window.${onPageChangeName}(${currentPage + 1})" style="
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: ${nextDisabled ? '#f8fafc' : '#ffffff'};
            color: ${nextDisabled ? '#cbd5e1' : '#64748b'};
            font-weight: 600;
            cursor: ${nextDisabled ? 'not-allowed' : 'pointer'};
            transition: all 0.2s;
            outline: none;
            padding: 0;
        ">&rsaquo;</button>`;

        $(`#${containerId}`).html(html);
    }


    // Expandable tree states for Demo 3
    let expandedChannels = { sp: false, sb: false, sd: false };

    // Dynamic Switch between Demos
    $('.demo-switch-btn').click(function() {
        $('.demo-switch-btn').removeClass('active').css('background', 'white').css('color', '#64748b').css('border-bottom', 'none');
        $(this).addClass('active').css('background', 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)').css('color', '#1d4ed8').css('border-bottom', '3px solid #3b82f6');
        
        const viewId = $(this).data('view');
        $('.demo-view-pane').hide();
        $('#demo_view_' + viewId).show();
        
        // Resize chart if showing Demo 2
        if (viewId === 2) {
            setTimeout(renderDemo2Chart, 100);
        }
    });

    $.get('../../api/get_data_range.php', function(ranges) {
        if (ranges.ads && ranges.ads.min_date) {
            $('#filter_from').val(ranges.ads.min_date);
            $('#filter_to').val(ranges.ads.max_date);
            loadAdData();
        }
    });

    function formatCurrency(v) {
        return '$' + parseFloat(v || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    function formatNumber(v) {
        return parseInt(v || 0).toLocaleString();
    }

    function loadAdData() {
        const customerId = $('#filter_customer').val();
        const fromDate = $('#filter_from').val();
        const toDate = $('#filter_to').val();

        if (!fromDate || !toDate) return;

        $('#refresh_ads').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

        $.get('../../api/advertising_data.php', {
            customer_id: customerId,
            from_date: fromDate,
            to_date: toDate
        }, function(data) {
            $('#refresh_ads').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> REFRESH');
            
            rawApiResponse = data;

            // RENDER ALL 3 DEMOS SIMULTANEOUSLY FOR INSTANT SWITCHING
            renderDemo1();
            renderDemo2();
            renderDemo3();
        });
    }

    // ===================================================================
    // ==================== DEMO 1 PRESENTATION ENGINE ==================
    // ===================================================================
    function renderDemo1() {
        const d = rawApiResponse;
        
        // 1. Channel Matrix Table
        let ch_html = '';
        
        const sp = d.summary.sp;
        const sb = d.summary.sb;
        const sd = d.summary.sd;
        
        const channels = [
            { name: 'Sponsored Products (SP)', data: sp, style: 'color: #1e3a8a; background: #eff6ff;' },
            { name: 'Sponsored Brands (SB)', data: sb, style: 'color: #581c87; background: #faf5ff;' },
            { name: 'Sponsored Display (SD)', data: sd, style: 'color: #881337; background: #fff1f2;' },
            { name: 'Total Portfolio Summary', data: {
                spend: d.summary.total_spend,
                sales: d.summary.total_sales,
                clicks: d.summary.total_clicks,
                impressions: sp.impressions + sb.impressions + sd.impressions,
                orders: d.summary.total_orders
            }, style: 'font-weight: 900; background: #f8fafc; border-top: 2px solid #475569; border-bottom: 2px solid #475569; color: #0f172a;' }
        ];

        channels.forEach(ch => {
            const spend = parseFloat(ch.data.spend || 0);
            const sales = parseFloat(ch.data.sales || 0);
            const clicks = parseInt(ch.data.clicks || 0);
            const imps = parseInt(ch.data.impressions || 0);
            const orders = parseInt(ch.data.orders || 0);
            
            const acos = sales > 0 ? (spend / sales * 100).toFixed(2) + '%' : '0.00%';
            const roas = spend > 0 ? (sales / spend).toFixed(2) + 'x' : '0.00x';
            const ctr = imps > 0 ? (clicks / imps * 100).toFixed(2) + '%' : '0.00%';
            const cpc = clicks > 0 ? formatCurrency(spend / clicks) : '$0.00';
            const cvr = clicks > 0 ? (orders / clicks * 100).toFixed(2) + '%' : '0.00%';

            ch_html += `<tr style="${ch.style} border-bottom: 1px solid #c6c6cd;">
                <td style="padding: 16px 24px; font-size: 16px;">${ch.name}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: right;">${formatCurrency(spend)}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #10b981; font-weight: 700;">${formatCurrency(sales)}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: center;">${acos}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #2563eb; font-weight: 700;">${roas}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: right;">${formatNumber(clicks)}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: right;">${formatNumber(imps)}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: center;">${ctr}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: right;">${cpc}</td>
                <td style="padding: 16px 24px; font-size: 16px; text-align: center;">${cvr}</td>
            </tr>`;
        });
        $('#demo1_channel_matrix_body').html(ch_html);

        // 2. Match Type Matrix
        let mt_html = '';
        if (d.match_types && d.match_types.length > 0) {
            d.match_types.forEach(m => {
                mt_html += `<tr style="border-bottom: 1px solid #c6c6cd;">
                    <td style="padding: 16px 24px; font-size: 16px; text-transform: uppercase; color: #1c1b1f; font-weight: 600;">${m.match_type}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatCurrency(m.spend)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #10b981; font-weight: 700;">${formatCurrency(m.sales)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: ${m.acos > 35 ? '#ef4444' : '#10b981'}; font-weight: 700;">${parseFloat(m.acos).toFixed(2)}%</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #2563eb; font-weight: 700;">${parseFloat(m.roas).toFixed(2)}x</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #1c1b1f;">${parseFloat(m.ctr).toFixed(2)}%</td>
                </tr>`;
            });
        } else {
            mt_html = `<tr><td colspan="6" style="padding: 24px; text-align: center; color: #45464d;">No Match Type breakdown data found.</td></tr>`;
        }
        $('#demo1_match_type_body').html(mt_html);

        // 3. Placement & Bidding
        let pb_html = '';
        if (d.placements && d.placements.length > 0) {
            d.placements.slice(0, 3).forEach(p => {
                const roas = p.spend > 0 ? (p.sales / p.spend).toFixed(2) + 'x' : '0.00x';
                pb_html += `<tr style="border-bottom: 1px solid #c6c6cd;">
                    <td style="padding: 16px 24px; font-size: 16px; color: #1c1b1f; font-weight: 600;">[Placement] ${p.placement}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatCurrency(p.spend)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatCurrency(p.sales)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #2563eb; font-weight: 700;">${roas}</td>
                </tr>`;
            });
        }
        if (d.bidding && d.bidding.length > 0) {
            d.bidding.slice(0, 2).forEach(b => {
                const roas = b.spend > 0 ? (b.sales / b.spend).toFixed(2) + 'x' : '0.00x';
                pb_html += `<tr style="border-bottom: 1px solid #c6c6cd;">
                    <td style="padding: 16px 24px; font-size: 16px; color: #1c1b1f; font-weight: 600;">[Strategy] ${b.bidding_strategy}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatCurrency(b.spend)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatCurrency(b.sales)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #2563eb; font-weight: 700;">${roas}</td>
                </tr>`;
            });
        }
        $('#demo1_placement_bid_body').html(pb_html || '<tr><td colspan="4" class="text-center" style="padding: 24px;">No segments found</td></tr>');

        // 4. Initialise Paginated Tables
        demo1KwPage = 1;
        renderDemo1KwGrid();
        demo1SkuPage = 1;
        renderDemo1SkuGrid();
    }

    // Keyword rendering inside Demo 1
    function renderDemo1KwGrid() {
        const d = rawApiResponse;
        if (!d.top_keywords) return;

        const query = demo1KwQuery.toLowerCase().trim();
        const filtered = d.top_keywords.filter(k => 
            (k.keyword || '').toLowerCase().includes(query) || (k.match_type || '').toLowerCase().includes(query)
        );

        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / ROWS_PER_PAGE) || 1;
        if (demo1KwPage > totalPages) demo1KwPage = totalPages;

        const startIndex = (demo1KwPage - 1) * ROWS_PER_PAGE;
        const pageItems = filtered.slice(startIndex, startIndex + ROWS_PER_PAGE);

        let html = '';
        if (pageItems.length > 0) {
            pageItems.forEach(k => {
                const acos = parseFloat(k.acos || 0);
                const roas = parseFloat(k.roas || 0);
                const ctr = parseFloat(k.ctr || 0);
                
                html += `<tr style="border-bottom: 1px solid #c6c6cd;">
                    <td style="padding: 16px 24px; font-size: 16px; color: #1c1b1f; font-weight: 600;">${k.keyword}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center;"><span style="font-size: 12px; background:#f2f4f6; color: #45464d; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; font-weight: 700;">${k.match_type}</span></td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center;"><span class="badge" style="background:#eff6ff; color:#0051d5; font-size: 12px; padding: 4px 8px; border-radius: 6px;">${k.ad_type}</span></td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatCurrency(k.spend)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color:#10b981; font-weight: 700;">${formatCurrency(k.sales)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color:${acos > 35 ? '#ef4444' : '#10b981'}; font-weight: 700;">${acos.toFixed(2)}%</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color:#2563eb; font-weight: 700;">${roas.toFixed(2)}x</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatNumber(k.clicks)}</td>
                    <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #1c1b1f;">${ctr.toFixed(2)}%</td>
                </tr>`;
            });
        } else {
            html = `<tr><td colspan="9" style="padding: 24px; text-align: center; color: #45464d; font-weight: 700;">No matching search terms found.</td></tr>`;
        }
 
        $('#demo1_kw_body').html(html);
        const end = Math.min(startIndex + ROWS_PER_PAGE, totalItems);
        $('#demo1_kw_info').text(totalItems > 0 ? `Showing ${startIndex + 1} to ${end} of ${totalItems} entries` : 'Showing 0 entries');
 
        renderTablePagination('demo1_kw_pagination', totalItems, demo1KwPage, ROWS_PER_PAGE, 'changeKwPage');
    }
 
    // SKU rendering inside Demo 1
    function renderDemo1SkuGrid() {
        const d = rawApiResponse;
        let dataset = (demo1SkuTab === 'sp') ? (d.sp_skus || []) : (d.sb_skus || []);
        
        const query = demo1SkuQuery.toLowerCase().trim();
        const filtered = dataset.filter(p => 
            (p.sku || '').toLowerCase().includes(query) || (p.asin || '').toLowerCase().includes(query) || (p.campaign_name || '').toLowerCase().includes(query)
        );
 
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / ROWS_PER_PAGE) || 1;
        if (demo1SkuPage > totalPages) demo1SkuPage = totalPages;
 
        const startIndex = (demo1SkuPage - 1) * ROWS_PER_PAGE;
        const pageItems = filtered.slice(startIndex, startIndex + ROWS_PER_PAGE);
 
        let head_html = '';
        let body_html = '';
 
        if (demo1SkuTab === 'sp') {
            head_html = `<tr>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; width: 30%;">Product / SKU ID</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Spend</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Sales</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ACoS</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">ROAS</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Clicks</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">CTR</th>
            </tr>`;
 
            if (pageItems.length > 0) {
                pageItems.forEach(p => {
                    const acos = parseFloat(p.acos || 0);
                    const roas = parseFloat(p.roas || 0);
                    const ctr = parseFloat(p.ctr || 0);
                    body_html += `<tr style="border-bottom: 1px solid #c6c6cd;">
                        <td style="padding: 16px 24px; font-size: 16px;">
                            <div style="font-weight: 700; color: #1c1b1f;">${p.sku}</div>
                            <div style="font-size: 12px; color: #45464d; margin-top: 2px;">ASIN: ${p.asin}</div>
                        </td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatCurrency(p.spend)}</td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: right; color:#10b981; font-weight: 700;">${formatCurrency(p.sales)}</td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: center; color:${acos > 35 ? '#ef4444' : '#10b981'}; font-weight: 700;">${acos.toFixed(2)}%</td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: center; color:#2563eb; font-weight: 700;">${roas.toFixed(2)}x</td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: right; color: #1c1b1f;">${formatNumber(p.clicks)}</td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #1c1b1f;">${ctr.toFixed(2)}%</td>
                    </tr>`;
                });
            } else {
                body_html = `<tr><td colspan="7" style="padding: 24px; text-align: center; color: #45464d; font-weight: 700;">No matching SP product records found.</td></tr>`;
            }
        } else {
            // Sponsored Brands Halo
            head_html = `<tr>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 20%;">Purchased ASIN</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 20%;">Estimated SKU</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 15%;">Halo Sales</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center; width: 10%;">Orders</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center; width: 10%;">Units</th>
                <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 25%;">Origin Campaign</th>
            </tr>`;
 
            if (pageItems.length > 0) {
                pageItems.forEach(p => {
                    body_html += `<tr style="border-bottom: 1px solid #c6c6cd;">
                        <td style="padding: 16px 24px; font-size: 16px; color:#1c1b1f; font-weight:700;">${p.asin}</td>
                        <td style="padding: 16px 24px; font-size: 16px;"><span class="badge ${p.sku === 'ASIN Lookup Needed' ? 'bg-light text-muted' : 'badge-sp'}" style="font-size: 12px; padding: 4px 8px; border-radius: 6px;">${p.sku}</span></td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: right; color:#10b981; font-weight: 700;">${formatCurrency(p.sales)}</td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #1c1b1f;">${p.orders}</td>
                        <td style="padding: 16px 24px; font-size: 16px; text-align: center; color: #1c1b1f;">${p.units}</td>
                        <td style="padding: 16px 24px; font-size: 16px; color:#45464d;">${p.campaign_name}</td>
                    </tr>`;
                });
            } else {
                body_html = `<tr><td colspan="6" style="padding: 24px; text-align: center; color: #45464d; font-weight: 700;">No matching SB Halo records found.</td></tr>`;
            }
        }

        $('#demo1_sku_head').html(head_html);
        $('#demo1_sku_body').html(body_html);
        
        const end = Math.min(startIndex + ROWS_PER_PAGE, totalItems);
        $('#demo1_sku_info').text(totalItems > 0 ? `Showing ${startIndex + 1} to ${end} of ${totalItems} entries` : 'Showing 0 entries');

        renderTablePagination('demo1_sku_pagination', totalItems, demo1SkuPage, ROWS_PER_PAGE, 'changeSkuPage');
    }

    // ===================================================================
    // ==================== DEMO 2 PRESENTATION ENGINE ==================
    // ===================================================================
    function renderDemo2() {
        const d = rawApiResponse;
        
        // 1. Load Metric Stream (Diagnostic Stream on Right Panel)
        const totalSpend = d.summary.total_spend;
        const totalSales = d.summary.total_sales;
        const totalClicks = d.summary.total_clicks;
        const totalOrders = d.summary.total_orders;
        const totalImpressions = (parseInt(d.summary.sp.impressions) || 0) + (parseInt(d.summary.sb.impressions) || 0) + (parseInt(d.summary.sd.impressions) || 0);

        const acos = totalSales > 0 ? (totalSpend / totalSales * 100) : 0;
        const roas = totalSpend > 0 ? (totalSales / totalSpend) : 0;
        const ctr = totalImpressions > 0 ? (totalClicks / totalImpressions * 100) : 0;
        const cpc = totalClicks > 0 ? (totalSpend / totalClicks) : 0;
        const cvr = totalClicks > 0 ? (totalOrders / totalClicks * 100) : 0;

        const streamMetrics = [
            { label: 'Total Portfolio Spend', val: formatCurrency(totalSpend), pctColor: '#ef4444', score: Math.min((totalSpend / 10000) * 100, 100) },
            { label: 'Total Portfolio Sales', val: formatCurrency(totalSales), pctColor: '#10b981', score: Math.min((totalSales / 40000) * 100, 100) },
            { label: 'Overall Portfolio ACoS', val: acos.toFixed(2) + '%', pctColor: acos > 35 ? '#ef4444' : '#10b981', score: acos },
            { label: 'Overall Portfolio ROAS', val: roas.toFixed(2) + 'x', pctColor: '#3b82f6', score: Math.min((roas / 6) * 100, 100) },
            { label: 'Global Portfolio CTR', val: ctr.toFixed(2) + '%', pctColor: '#f59e0b', score: Math.min((ctr / 1.5) * 100, 100) },
            { label: 'Avg Portfolio CPC', val: formatCurrency(cpc), pctColor: '#ec4899', score: Math.min((cpc / 2) * 100, 100) }
        ];

        let str_html = '';
        streamMetrics.forEach(m => {
            str_html += `<div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2px;">
                    <span style="font-size: 11px; color:#64748b; font-weight:800; text-transform:uppercase;">${m.label}</span>
                    <span style="font-size: 13.5px; font-weight:900; color:${m.pctColor}">${m.val}</span>
                </div>
                <div style="width:100%; height:4px; background:#f1f5f9; border-radius:2px; overflow:hidden;">
                    <div style="width: ${m.score}%; height:100%; background:${m.pctColor}; border-radius:2px;"></div>
                </div>
            </div>`;
        });
        $('#demo2_metrics_container').html(str_html);

        // 2. Load Top 5 Keywords
        let kw5_html = '';
        if (d.top_keywords && d.top_keywords.length > 0) {
            d.top_keywords.slice(0, 5).forEach(k => {
                kw5_html += `<tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding: 8px 12px; color:#1e293b;">${k.keyword} <span style="font-size: 9px; color:#94a3b8; text-transform:uppercase;">(${k.match_type})</span></td>
                    <td style="padding: 8px 12px; text-align:right;">${formatCurrency(k.spend)}</td>
                    <td style="padding: 8px 12px; text-align:center; color:${k.acos > 35 ? '#ef4444' : '#10b981'};">${parseFloat(k.acos).toFixed(1)}%</td>
                    <td style="padding: 8px 12px; text-align:center; color:#3b82f6;">${parseFloat(k.roas).toFixed(1)}x</td>
                </tr>`;
            });
        }
        $('#demo2_top5_kws').html(kw5_html || '<tr><td colspan="4" class="text-center">No Keywords</td></tr>');

        // 3. Load Top 5 SKUs
        let sku5_html = '';
        if (d.sp_skus && d.sp_skus.length > 0) {
            d.sp_skus.slice(0, 5).forEach(p => {
                sku5_html += `<tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding: 8px 12px; color:#1e293b;">${p.sku}</td>
                    <td style="padding: 8px 12px; text-align:right;">${formatCurrency(p.spend)}</td>
                    <td style="padding: 8px 12px; text-align:center; color:${p.acos > 35 ? '#ef4444' : '#10b981'};">${parseFloat(p.acos).toFixed(1)}%</td>
                    <td style="padding: 8px 12px; text-align:center; color:#3b82f6;">${parseFloat(p.roas).toFixed(1)}x</td>
                </tr>`;
            });
        }
        $('#demo2_top5_skus').html(sku5_html || '<tr><td colspan="4" class="text-center">No SKUs</td></tr>');

        // 4. Initialise Demo 2 Dynamic Chart Canvas
        renderDemo2Chart();
    }

    function renderDemo2Chart() {
        const d = rawApiResponse;
        if (!d) return;

        if (demo2MainChart) demo2MainChart.destroy();

        const activeChartType = $('.demo2-chart-type-btn.active').data('chart');
        const ctx = document.getElementById('demo2_main_chart').getContext('2d');

        if (activeChartType === 'spend_sales') {
            const spendGradient = ctx.createLinearGradient(0, 0, 0, 400);
            spendGradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
            spendGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            const salesGradient = ctx.createLinearGradient(0, 0, 0, 400);
            salesGradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
            salesGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            demo2MainChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: d.daily_trend.labels,
                    datasets: [
                        {
                            label: 'Total spend',
                            data: d.daily_trend.spend,
                            borderColor: '#6366f1',
                            backgroundColor: spendGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0
                        },
                        {
                            label: 'Total sales',
                            data: d.daily_trend.sales,
                            borderColor: '#10b981',
                            backgroundColor: salesGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top', align: 'end', labels: { boxWidth: 8, font: { weight: '800' } } }
                    },
                    scales: {
                        y: { grid: { color: '#f1f5f9' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                }
            });
        } else if (activeChartType === 'match_types') {
            // Group match_types daily
            let exactData = [];
            let broadData = [];
            let phraseData = [];
            let labels = [];

            // Unique dates sorted
            const uniqueDates = [...new Set(d.match_types_daily.map(x => x.report_date))].sort();
            labels = uniqueDates.map(x => dateToLabel(x));

            uniqueDates.forEach(date => {
                let e = d.match_types_daily.find(x => x.report_date === date && x.match_type === 'exact');
                let b = d.match_types_daily.find(x => x.report_date === date && x.match_type === 'broad');
                let p = d.match_types_daily.find(x => x.report_date === date && x.match_type === 'phrase');

                exactData.push(parseFloat(e ? e.spend : 0));
                broadData.push(parseFloat(b ? b.spend : 0));
                phraseData.push(parseFloat(p ? p.spend : 0));
            });

            demo2MainChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Exact Spend', data: exactData, borderColor: '#3b82f6', tension: 0.4, fill: false, borderWidth: 3, pointRadius: 0 },
                        { label: 'Broad Spend', data: broadData, borderColor: '#a855f7', tension: 0.4, fill: false, borderWidth: 3, pointRadius: 0 },
                        { label: 'Phrase Spend', data: phraseData, borderColor: '#ec4899', tension: 0.4, fill: false, borderWidth: 3, pointRadius: 0 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top', align: 'end', labels: { boxWidth: 8, font: { weight: '800' } } }
                    },
                    scales: {
                        y: { grid: { color: '#f1f5f9' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                }
            });
        } else if (activeChartType === 'shares') {
            demo2MainChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sponsored Products', 'Sponsored Brands', 'Sponsored Display'],
                    datasets: [{
                        data: [d.summary.sp.spend, d.summary.sb.spend, d.summary.sd.spend],
                        backgroundColor: ['#3b82f6', '#a855f7', '#f43f5e']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { boxWidth: 12, font: { weight: '700', size: 12 } } }
                    }
                }
            });
        }
    }

    function dateToLabel(dateStr) {
        return dateStr ? new Date(dateStr).toLocaleDateString('en-US', { day: 'numeric', month: 'short' }) : '';
    }

    $('.demo2-chart-type-btn').click(function() {
        $('.demo2-chart-type-btn').removeClass('active').css('background', 'transparent').css('color', '#64748b');
        $(this).addClass('active').css('background', 'white').css('color', '#1d4ed8');
        renderDemo2Chart();
    });

    // ===================================================================
    // ==================== DEMO 3 PRESENTATION ENGINE ==================
    // ===================================================================
    function renderDemo3() {
        const d = rawApiResponse;
        let html = '';

        // Row generator with hierarchy indentation, Excel Pivot Style
        const renderChannelRow = (id, label, data, hasChildren) => {
            const spend = parseFloat(data.spend || 0);
            const sales = parseFloat(data.sales || 0);
            const clicks = parseInt(data.clicks || 0);
            const imps = parseInt(data.impressions || 0);
            
            const acos = sales > 0 ? (spend / sales * 100).toFixed(2) + '%' : '0.00%';
            const roas = spend > 0 ? (sales / spend).toFixed(2) : '0.00';
            const ctr = imps > 0 ? (clicks / imps * 100).toFixed(2) + '%' : '0.00%';

            // Conditional formatting for ROAS cells
            let roasBg = 'transparent';
            let roasFg = '#1e293b';
            if (roas > 4) { roasBg = 'rgba(16, 185, 129, 0.15)'; roasFg = '#10b981'; }
            else if (roas > 2) { roasBg = 'rgba(245, 158, 11, 0.1)'; roasFg = '#d97706'; }
            else if (roas > 0) { roasBg = 'rgba(239, 68, 68, 0.1)'; roasFg = '#ef4444'; }

            const expandIcon = hasChildren ? (expandedChannels[id] ? '<i class="fas fa-minus-square"></i>' : '<i class="fas fa-plus-square"></i>') : '';

            return `<tr style="background: #f8fafc; border-bottom: 2px solid #cbd5e1; height: 38px;">
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; color:#0f172a; font-weight:800; cursor:pointer;" class="demo3-toggle-row" data-channel="${id}">
                    <span style="color:#6366f1; margin-right:6px; font-size:14px;">${expandIcon}</span>
                    ${label}
                </td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align:right;">${formatCurrency(spend)}</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align:right; color:#10b981;">${formatCurrency(sales)}</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align:center;">${acos}</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align:center; background:${roasBg}; color:${roasFg}; font-weight:800;">${roas}</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align:right;">${formatNumber(clicks)}</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align:right;">${formatNumber(imps)}</td>
                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-align:center;">${ctr}</td>
            </tr>`;
        };

        const renderCampaignOrSkuRow = (label, data, isSku) => {
            const spend = parseFloat(data.spend || 0);
            const sales = parseFloat(data.sales || 0);
            const clicks = parseInt(data.clicks || 0);
            const imps = parseInt(data.impressions || 0);

            const acos = sales > 0 ? (spend / sales * 100).toFixed(2) + '%' : '0.00%';
            const roas = spend > 0 ? (sales / spend).toFixed(2) : '0.00';
            const ctr = imps > 0 ? (clicks / imps * 100).toFixed(2) + '%' : '0.00%';

            const prefix = isSku ? '└─ SKU: ' : '├─ Cam: ';
            const color = isSku ? '#64748b' : '#334155';

            return `<tr style="border-bottom: 1px solid #e2e8f0; height: 32px;">
                <td style="padding: 6px 12px 6px 28px; border: 1px solid #cbd5e1; color:${color}; font-weight:600;">
                    ${prefix}${label}
                </td>
                <td style="padding: 6px 12px; border: 1px solid #cbd5e1; text-align:right;">${formatCurrency(spend)}</td>
                <td style="padding: 6px 12px; border: 1px solid #cbd5e1; text-align:right; color:#10b981;">${formatCurrency(sales)}</td>
                <td style="padding: 6px 12px; border: 1px solid #cbd5e1; text-align:center;">${acos}</td>
                <td style="padding: 6px 12px; border: 1px solid #cbd5e1; text-align:center; font-weight:800;">${roas}</td>
                <td style="padding: 6px 12px; border: 1px solid #cbd5e1; text-align:right;">${formatNumber(clicks)}</td>
                <td style="padding: 6px 12px; border: 1px solid #cbd5e1; text-align:right;">${formatNumber(imps)}</td>
                <td style="padding: 6px 12px; border: 1px solid #cbd5e1; text-align:center;">${ctr}</td>
            </tr>`;
        };

        // Render SP Channel
        html += renderChannelRow('sp', 'Sponsored Products (SP)', d.summary.sp, true);
        if (expandedChannels.sp) {
            // list top campaigns or top SKUs under SP
            if (d.sp_skus && d.sp_skus.length > 0) {
                d.sp_skus.slice(0, 8).forEach(sku => {
                    html += renderCampaignOrSkuRow(sku.sku, sku, true);
                });
            }
        }

        // Render SB Channel
        html += renderChannelRow('sb', 'Sponsored Brands (SB)', d.summary.sb, true);
        if (expandedChannels.sb) {
            if (d.sb_skus && d.sb_skus.length > 0) {
                d.sb_skus.slice(0, 6).forEach(sku => {
                    html += renderCampaignOrSkuRow(`${sku.asin} (${sku.sku})`, sku, true);
                });
            }
        }

        // Render SD Channel
        html += renderChannelRow('sd', 'Sponsored Display (SD)', d.summary.sd, false);

        // Portfolio Sum Row
        const totalSum = {
            spend: d.summary.total_spend,
            sales: d.summary.total_sales,
            clicks: d.summary.total_clicks,
            impressions: d.summary.sp.impressions + d.summary.sb.impressions + d.summary.sd.impressions
        };
        const totalAcos = totalSum.sales > 0 ? (totalSum.spend / totalSum.sales * 100).toFixed(2) + '%' : '0.00%';
        const totalRoas = totalSum.spend > 0 ? (totalSum.sales / totalSum.spend).toFixed(2) : '0.00';
        const totalCtr = totalSum.impressions > 0 ? (totalSum.clicks / totalSum.impressions * 100).toFixed(2) + '%' : '0.00%';

        html += `<tr style="background: #e2e8f0; border-top: 2px solid #64748b; height: 40px; font-weight: 800; font-size:13.5px;">
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; color:#0f172a;">GRAND PORTFOLIO TOTAL</td>
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; text-align:right;">${formatCurrency(totalSum.spend)}</td>
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; text-align:right; color:#10b981;">${formatCurrency(totalSum.sales)}</td>
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; text-align:center;">${totalAcos}</td>
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; text-align:center; color:#2563eb;">${totalRoas}</td>
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; text-align:right;">${formatNumber(totalSum.clicks)}</td>
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; text-align:right;">${formatNumber(totalSum.impressions)}</td>
            <td style="padding: 10px 14px; border: 1px solid #cbd5e1; text-align:center;">${totalCtr}</td>
        </tr>`;

        $('#demo3_pivot_body').html(html);

        // Bind expand/collapse events inside Demo 3
        $('.demo3-toggle-row').click(function() {
            const ch = $(this).data('channel');
            expandedChannels[ch] = !expandedChannels[ch];
            renderDemo3();
        });
    }

    // ===================================================================
    // ==================== PAGINATION & FILTERS BINDING =================
    // ===================================================================
    
    // Keywords Search & Pagination (Demo 1)
    $('#demo1_kw_search').on('keyup', function() {
        demo1KwQuery = $(this).val();
        demo1KwPage = 1;
        renderDemo1KwGrid();
    });

    // SKU Products Tab, Search & Pagination (Demo 1)
    $('#demo1_sku_sp_btn').click(function() {
        $('#demo1_sku_sp_btn').addClass('active').css('background', 'white').css('color', '#1d4ed8');
        $('#demo1_sku_sb_btn').removeClass('active').css('background', 'transparent').css('color', '#64748b');
        demo1SkuTab = 'sp';
        demo1SkuPage = 1;
        renderDemo1SkuGrid();
    });
    $('#demo1_sku_sb_btn').click(function() {
        $('#demo1_sku_sb_btn').addClass('active').css('background', 'white').css('color', '#1d4ed8');
        $('#demo1_sku_sp_btn').removeClass('active').css('background', 'transparent').css('color', '#64748b');
        demo1SkuTab = 'sb';
        demo1SkuPage = 1;
        renderDemo1SkuGrid();
    });
    $('#demo1_sku_search').on('keyup', function() {
        demo1SkuQuery = $(this).val();
        demo1SkuPage = 1;
        renderDemo1SkuGrid();
    });

    $('#refresh_ads').click(loadAdData);
    $('#filter_customer').change(loadAdData);
});
</script>




