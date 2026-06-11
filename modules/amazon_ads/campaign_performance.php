<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Campaign & Target Performance";
$page_subtitle = "Detailed analysis of campaigns, ad groups, and targeting efficiency";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: #ffffff; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border-radius: 20px; border: 1px solid #f1f5f9;">
    <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap; width: 100%;">
        <div style="flex: 2; min-width: 250px; display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-weight: 800; color: #64748b; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; margin-left: 2px;">Amazon Profile</label>
            <select id="filter_customer" style="width: 100%; padding: 0.8rem 1rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 700; color: #1e293b; appearance: none; cursor: pointer; transition: all 0.2s;">
                <option value="">All Amazon Profiles</option>
                <?php 
                $customers = get_all_customers();
                while ($row = $customers->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['customer_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div style="flex: 1; min-width: 160px; display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-weight: 800; color: #64748b; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; margin-left: 2px;">Brand Name</label>
            <select id="filter_brand" style="width: 100%; padding: 0.8rem 1rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 700; color: #1e293b; appearance: none; cursor: pointer;">
                <option value="">All Brands</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 160px; display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-weight: 800; color: #64748b; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; margin-left: 2px;">Traffic Type</label>
            <select id="filter_traffic_type" style="width: 100%; padding: 0.8rem 1rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 700; color: #1e293b; appearance: none; cursor: pointer;">
                <option value="all">All Traffic</option>
                <option value="branded">Branded</option>
                <option value="non_branded">Non-Branded</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-weight: 800; color: #64748b; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; margin-left: 2px;">From Date</label>
            <input type="date" id="filter_from" class="form-control" value="2026-01-01" style="padding: 0.8rem; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; color: #1e293b;">
        </div>
        <div style="flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 0.5rem;">
            <label style="font-weight: 800; color: #64748b; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.1em; margin-left: 2px;">To Date</label>
            <input type="date" id="filter_to" class="form-control" value="2026-03-31" style="padding: 0.8rem; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 700; color: #1e293b;">
        </div>
        <div style="flex: 1.2; min-width: 200px;">
            <button id="refresh_campaigns" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; padding: 0.8rem; border-radius: 12px; font-weight: 800; color: white; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.25); height: 52px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                <i class="fas fa-search me-2"></i> ANALYZE PERFORMANCE
            </button>
        </div>
    </div>
</div>

<!-- Section Heading: Campaign Performance Overview -->
<div style="margin-top: 1rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem;">
    <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-bullseye" style="color: #2563eb;"></i> Campaign Performance Overview
    </h2>
    <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0; font-weight: 500;">Top and bottom performing campaigns in this range</p>
</div>

<!-- Campaigns performance columns -->
<div class="campaigns-grid">
    <!-- Top 5 Performing Campaigns -->
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Top 5 Performing Campaigns</div>
            <span class="status-badge high-roas">HIGH ROAS</span>
        </div>
        <table class="premium-table">
            <thead>
                <tr>
                    <th>CAMPAIGN NAME</th>
                    <th class="right">SPEND</th>
                    <th class="right">ROAS</th>
                </tr>
            </thead>
            <tbody id="top-campaigns-body">
                <tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">Loading campaigns...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Bottom 5 Low-Performing Campaigns -->
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Bottom 5 Low-Performing Campaigns</div>
            <span class="status-badge check-budget">CHECK BUDGET</span>
        </div>
        <table class="premium-table">
            <thead>
                <tr>
                    <th>CAMPAIGN NAME</th>
                    <th class="right">SPEND</th>
                    <th class="right">ACOS</th>
                </tr>
            </thead>
            <tbody id="bottom-campaigns-body">
                <tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">Loading campaigns...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Section Heading: Keywords Performance Overview -->
<div style="margin-top: 2rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem;">
    <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-key" style="color: #6366f1;"></i> Keywords Performance Overview
    </h2>
    <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0; font-weight: 500;">Top and bottom performing keywords in this range</p>
</div>

<!-- Keywords performance columns side-by-side -->
<div class="campaigns-grid" style="margin-bottom: 2rem;">
    <!-- Top 10 Performing Keywords -->
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Top 10 Performing Keywords</div>
            <span class="status-badge high-roas">HIGH ROAS</span>
        </div>
        <table class="premium-table">
            <thead>
                <tr>
                    <th>KEYWORD</th>
                    <th class="right">SPEND</th>
                    <th class="right">ROAS</th>
                </tr>
            </thead>
            <tbody id="top-keywords-body">
                <tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">Loading keywords...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Worst 10 Low-Performing Keywords -->
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Worst 10 Low-Performing Keywords</div>
            <span class="status-badge check-budget">CHECK BUDGET</span>
        </div>
        <table class="premium-table">
            <thead>
                <tr>
                    <th>KEYWORD</th>
                    <th class="right">SPEND</th>
                    <th class="right">ACoS</th>
                </tr>
            </thead>
            <tbody id="bottom-keywords-body">
                <tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">Loading keywords...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Section Heading: Match Type Performance -->
<div style="margin-top: 2rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem;">
    <h2 style="font-size: 1.5rem; font-weight: 800; color: #10b981; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-tags" style="color: #10b981;"></i> Match Type Performance
    </h2>
    <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0; font-weight: 500;">Compare spend efficiency across Broad, Phrase, and Exact match types</p>
</div>

<!-- Match Type performance columns side-by-side -->
<div class="campaigns-grid" style="margin-bottom: 2rem;">
    <!-- Match Type Performance Table Card -->
    <div class="table-card" style="padding: 1.5rem 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: #eff6ff; color: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em;">Match Type Performance</h4>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0 0; font-weight: 600;">Performance metrics grouped by keyword match types</p>
                </div>
            </div>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed; margin-bottom: 1rem;">
            <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                <tr>
                    <th style="padding: 12px 16px; font-size: 11px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 28%;">Match Type</th>
                    <th style="padding: 12px 12px; font-size: 11px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 18%;">Spend</th>
                    <th style="padding: 12px 12px; font-size: 11px; font-weight: 700; color: #0051d5; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 20%;">Sales</th>
                    <th style="padding: 12px 12px; font-size: 11px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 16%;">ACoS</th>
                    <th style="padding: 12px 16px; font-size: 11px; font-weight: 700; color: #009668; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 18%;">ROAS</th>
                </tr>
            </thead>
            <tbody id="match-types-body" style="background:#ffffff;">
                <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8;">Loading match types...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Match Type Share Doughnut Chart Card -->
    <div class="table-card" style="padding: 1.5rem 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: #ecfdf5; color: #10b981; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em;">Spend Share by Match Type</h4>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0 0; font-weight: 600;">Visualizing share of ad budget distribution</p>
                </div>
            </div>
        </div>
        
        <div style="height: 220px; position: relative; width: 100%; display: flex; justify-content: center; align-items: center;">
            <canvas id="matchTypeDoughnutChart" style="max-height: 100%; max-width: 100%;"></canvas>
        </div>
    </div>
</div>

<!-- Section Heading: Placement Analysis Report - SP & SB -->
<div style="margin-top: 2rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem;">
    <h2 style="font-size: 1.5rem; font-weight: 800; color: #0284c7; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-layer-group" style="color: #0284c7;"></i> Placement Analysis Report - SP & SB
    </h2>
    <p style="font-size: 0.85rem; color: #64748b; margin: 0.25rem 0 0 0; font-weight: 500;">Analyze placement breakdown across Sponsored Products & Sponsored Brands</p>
</div>

<!-- Placement Performance Cards Side-by-Side -->
<div class="campaigns-grid" style="margin-bottom: 2rem;">
    <!-- Sponsored Products Placement -->
    <div class="table-card" style="padding: 1.5rem 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-mouse-pointer"></i>
                </div>
                <div>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em;">Sponsored Products</h4>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0 0; font-weight: 600;">Individual Listing Promotions</p>
                </div>
            </div>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed; margin-bottom: 1rem;">
            <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                <tr>
                    <th style="padding: 12px 16px; font-size: 11px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 35%;">Placement</th>
                    <th style="padding: 12px 12px; font-size: 11px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 18%;">Spend</th>
                    <th style="padding: 12px 12px; font-size: 11px; font-weight: 700; color: #0051d5; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 18%; background: rgba(219,225,255,0.1);">Sales</th>
                    <th style="padding: 12px 12px; font-size: 11px; font-weight: 700; color: #009668; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 14%; background: rgba(111,251,190,0.05);">ROAS</th>
                    <th style="padding: 12px 24px; font-size: 11px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center; width: 15%;">Health</th>
                </tr>
            </thead>
            <tbody id="sp-placements-body" style="background:#ffffff;">
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">Loading placements...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Sponsored Brands Placement -->
    <div class="table-card" style="padding: 1.5rem 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; background: #e0f2fe; color: #0284c7; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em;">Sponsored Brands</h4>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0 0; font-weight: 600;">Brand Store & Headline Ads</p>
                </div>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed; margin-bottom: 1rem;">
            <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                <tr>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 35%;">Placement</th>
                    <th style="padding: 16px 16px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 18%;">Spend</th>
                    <th style="padding: 16px 16px; font-size: 12px; font-weight: 700; color: #0051d5; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 18%; background: rgba(219,225,255,0.1);">Sales</th>
                    <th style="padding: 16px 16px; font-size: 12px; font-weight: 700; color: #009668; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 14%; background: rgba(111,251,190,0.05);">ROAS</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center; width: 15%;">Health</th>
                </tr>
            </thead>
            <tbody id="sb-placements-body" style="background:#ffffff;">
                <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">Loading placements...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<section class="bento-card overflow-hidden mb-8" style="background:#ffffff; border-radius:16px; border: 1px solid #c6c6cd; overflow:hidden;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #c6c6cd; padding: 24px 32px; background: #ffffff;">
        <div>
            <h3 style="font-size: 22px; font-weight: 700; color: #000000; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-layer-group text-primary me-2" style="font-size: 24px; color: #0051d5 !important;"></i>
                Campaign Performance Breakdown
            </h3>
            <p style="margin: 4px 0 0 0; font-size: 12px; color: #45464d; font-weight: 600;">Cross-channel advertising efficiency metrics</p>
        </div>
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #45464d; pointer-events: none;"></i>
                <input id="campaign_search_input" style="padding-left: 36px; padding-right: 16px; padding-top: 8px; padding-bottom: 8px; border: 1px solid #c6c6cd; border-radius: 8px; outline: none; background: #f2f4f6; font-size: 14px; font-weight: 600; width: 220px;" placeholder="Search campaigns..." type="text"/>
            </div>
            <span style="background: rgba(111, 251, 190, 0.2); color: #009668; padding: 6px 12px; border-radius: 6px; font-weight: 800; font-size: 0.8rem; height: 32px; display: inline-flex; align-items: center;">SP, SB, SD ACTIVE</span>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table id="campaignTable" style="width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed; margin: 0;">
            <thead style="background: #f2f4f6; border-bottom: 1px solid #c6c6cd;">
                <tr>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center; width: 8%;">Rank</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 22%;">Campaign & Ad Group</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 14%;">Targeting</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: left; width: 10%;">Match</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 12%;">Spend</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 12%;">Sales</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 10%;">ACoS</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: right; width: 8%;">ROAS</th>
                    <th style="padding: 16px 32px; font-size: 12px; font-weight: 700; color: #45464d; text-transform: uppercase; letter-spacing: 0.1em; text-align: center; width: 12%;">Bid Action</th>
                </tr>
            </thead>
            <tbody id="campaign_body" style="background:#ffffff;"></tbody>
        </table>
    </div>
</section>

<!-- Bidding Strategy Efficiency -->
<div style="margin-bottom: 2rem; margin-top: 2rem;">
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-gavel" style="color: #f59e0b; margin-right: 0.5rem;"></i> Bidding Strategy Efficiency
        </div>
        <div style="padding: 1rem;">
            <table class="table" id="biddingTable" style="width: 100%; font-size: 0.85rem;">
                <thead style="background: #f1f5f9;">
                    <tr>
                        <th>Strategy</th>
                        <th style="text-align: right;">Spend</th>
                        <th style="text-align: center;">Sales</th>
                        <th style="text-align: center;">ROAS</th>
                    </tr>
                </thead>
                <tbody id="bidding_body"></tbody>
            </table>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    let matchTypeChart = null;
    function formatCurrency(v) {
        return '$' + parseFloat(v || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    function loadBrands(callback) {
        const customerId = $('#filter_customer').val();
        $.get('../../api/get_brands.php', { customer_id: customerId }, function(brands) {
            let html = '<option value="">All Brands</option>';
            brands.forEach(b => {
                html += `<option value="${b}">${b}</option>`;
            });
            $('#filter_brand').html(html);
            if (callback) callback();
        });
    }

    $.get('../../api/get_data_range.php', function(ranges) {
        if (ranges.ads && ranges.ads.min_date) {
            $('#filter_from').val(ranges.ads.min_date);
            $('#filter_to').val(ranges.ads.max_date);
        }
        loadBrands(loadCampaignData);
    });

    // Auto-refresh when filters change
    $('#filter_customer').on('change', function() {
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

        $('#refresh_campaigns').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ANALYZE PERFORMANCE');
        $('#campaign_body, #placement_body, #bidding_body').css('opacity', '0.5');

        $.get('<?php echo BASE_URL; ?>api/advertising_data.php', {
            customer_id: customerId,
            from_date: fromDate,
            to_date: toDate,
            brand: brand,
            traffic_type: trafficType
        }, function(data) {
            $('#refresh_campaigns').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> ANALYZE PERFORMANCE');
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
                        <tr>
                            <td style="text-align: left;">
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
                    const cAcos = sales > 0 ? (spend / sales) * 100 : (spend > 0 ? 100 : 0);
                    const typeLabel = c.type === 'SP' ? 'Sponsored Products' : (c.type === 'SB' ? 'Sponsored Brands' : 'Sponsored Display');
                    bottomHtml += `
                        <tr>
                            <td style="text-align: left;">
                                <p class="campaign-name" title="${c.campaign_name}">${c.campaign_name}</p>
                                <p class="campaign-sub">${typeLabel}</p>
                            </td>
                            <td class="campaign-spend">${formatCurrency(spend)}</td>
                            <td class="campaign-metric acos" style="color: #ef4444; font-weight:800;">${cAcos.toFixed(1)}%</td>
                        </tr>
                    `;
                });
            } else {
                bottomHtml = `<tr><td colspan="3" class="text-center" style="padding:2rem; color:#64748b;">No low performing campaigns found.</td></tr>`;
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
                        <tr>
                            <td style="text-align: left;">
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
                        <tr>
                            <td style="text-align: left;">
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

            // Match Type Performance Table & Chart Population
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
                matchHtml = `<tr><td colspan="5" class="text-center" style="padding: 2rem; color: #94a3b8;">No match type data found.</td></tr>`;
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
                                    label: function(context) {
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
                const doughnutCtx = document.getElementById('matchTypeDoughnutChart');
                if (doughnutCtx) {
                    const ctx2d = doughnutCtx.getContext('2d');
                    ctx2d.clearRect(0, 0, 200, 200);
                }
            }

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
                    return { label: 'Top of Search', icon: '<i class="fas fa-arrow-up" style="color: #2563eb;"></i>' };
                } else if (lower.includes('rest of search')) {
                    return { label: 'Rest of Search', icon: '<i class="fas fa-search" style="color: #64748b;"></i>' };
                } else if (lower.includes('product pages')) {
                    return { label: 'Product Pages', icon: '<i class="far fa-file-alt" style="color: #64748b;"></i>' };
                } else {
                    return { label: 'Other Placements', icon: '<i class="fas fa-cubes" style="color: #64748b;"></i>' };
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
                if (p.placement.toLowerCase().includes('top of search')) tosSpSales = sales;
            });

            let maxSpRoas = Math.max(...placementsSp.map(p => parseFloat(p.spend) > 0 ? (parseFloat(p.sales)/parseFloat(p.spend)) : 0)) || 1;
            maxSpRoas = Math.max(maxSpRoas, 6.0);

            if (placementsSp.length > 0) {
                placementsSp.forEach(p => {
                    const spend = parseFloat(p.spend || 0);
                    const sales = parseFloat(p.sales || 0);
                    const roas = spend > 0 ? (sales / spend) : 0;
                    const details = getPlacementDetails(p.placement);
                    const healthPercent = Math.min(100, (roas / maxSpRoas) * 100);
                    let healthColor = '#2563eb';
                    if (roas < 2.0) healthColor = '#ef4444';
                    else if (roas < 4.0) healthColor = '#64748b';

                    spPlHtml += `
                        <tr style="border-bottom: 1px solid #cbd5e1; background: #ffffff;">
                            <td style="padding: 14px 24px; font-weight: 700; color: #000000; text-align: left; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 16px; width: 20px; display: inline-block; text-align: center;">${details.icon}</span>
                                ${details.label}
                            </td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 600; color: #45464d; text-align: right; font-family: 'Inter', sans-serif;">${formatCurrency(spend)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #0051d5; text-align: right; font-family: 'Inter', sans-serif; background: rgba(219,225,255,0.05);">${formatCurrency(sales)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #009668; text-align: right; font-family: 'Inter', sans-serif; background: rgba(111,251,190,0.02);">${roas.toFixed(2)}x</td>
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

            // Populate SB placements
            let sbPlHtml = '';
            let totalSbSales = 0;
            let tosSbSales = 0;

            placementsSb.forEach(p => {
                const spend = parseFloat(p.spend || 0);
                const sales = parseFloat(p.sales || 0);
                totalSbSales += sales;
                if (p.placement.toLowerCase().includes('top of search')) tosSbSales = sales;
            });

            let maxSbRoas = Math.max(...placementsSb.map(p => parseFloat(p.spend) > 0 ? (parseFloat(p.sales)/parseFloat(p.spend)) : 0)) || 1;
            maxSbRoas = Math.max(maxSbRoas, 6.0);

            if (placementsSb.length > 0) {
                placementsSb.forEach(p => {
                    const spend = parseFloat(p.spend || 0);
                    const sales = parseFloat(p.sales || 0);
                    const roas = spend > 0 ? (sales / spend) : 0;
                    const details = getPlacementDetails(p.placement);
                    const healthPercent = Math.min(100, (roas / maxSbRoas) * 100);
                    let healthColor = '#2563eb';
                    if (roas < 2.0) healthColor = '#ef4444';
                    else if (roas < 4.0) healthColor = '#64748b';

                    sbPlHtml += `
                        <tr style="border-bottom: 1px solid #cbd5e1; background: #ffffff;">
                            <td style="padding: 14px 24px; font-weight: 700; color: #000000; text-align: left; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 16px; width: 20px; display: inline-block; text-align: center;">${details.icon}</span>
                                ${details.label}
                            </td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 600; color: #45464d; text-align: right; font-family: 'Inter', sans-serif;">${formatCurrency(spend)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #0051d5; text-align: right; font-family: 'Inter', sans-serif; background: rgba(219,225,255,0.05);">${formatCurrency(sales)}</td>
                            <td style="padding: 14px 16px; font-size: 14px; font-weight: 700; color: #009668; text-align: right; font-family: 'Inter', sans-serif; background: rgba(111,251,190,0.02);">${roas.toFixed(2)}x</td>
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
                const acos = c.sales > 0 ? (c.spend / c.sales * 100).toFixed(2) : '0.00';
                const roas = c.spend > 0 ? (c.sales / c.spend).toFixed(2) : '0.00';
                const clicks = parseInt(c.clicks || 0);
                const sales = parseFloat(c.sales || 0);
                const spend = parseFloat(c.spend || 0);
                
                let bidAction = '';
                let bidStyle = '';
                
                if (sales > 0) {
                    if (acos < 15) { bidAction = 'SCALE UP'; bidStyle = 'background: #dcfce7; color: #166534;'; }
                    else if (acos < 25) { bidAction = 'MAINTAIN'; bidStyle = 'background: #f0fdf4; color: #10b981;'; }
                    else if (acos < 35) { bidAction = 'OPTIMIZE'; bidStyle = 'background: #fffbeb; color: #b45309;'; }
                    else { bidAction = 'REDUCE BID'; bidStyle = 'background: #fff1f2; color: #ef4444;'; }
                } else if (spend > 10 || clicks > 15) {
                    bidAction = 'NEGATE / PAUSE';
                    bidStyle = 'background: #fef2f2; color: #991b1b;';
                } else {
                    bidAction = 'COLLECT DATA';
                    bidStyle = 'background: #f8fafc; color: #64748b;';
                }

                const roasVal = parseFloat(roas);
                const roasBg = roasVal >= 4.0 ? '#e6fcf5' : (roasVal > 0 ? '#fff1f2' : '#f2f4f6');
                const roasColor = roasVal >= 4.0 ? '#009668' : (roasVal > 0 ? '#ef4444' : '#45464d');
                const roasBadgeHtml = `<span style="background: ${roasBg}; color: ${roasColor}; padding: 6px 12px; border-radius: 6px; font-weight: 800; font-size: 0.9rem; display: inline-block;">${roasVal.toFixed(1)}x</span>`;

                const acosVal = parseFloat(acos);
                const acosBg = acosVal > 35 ? '#fff1f2' : (acosVal < 25 ? '#e6fcf5' : '#fffbeb');
                const acosColor = acosVal > 35 ? '#ef4444' : (acosVal < 25 ? '#009668' : '#b45309');
                const acosBadgeHtml = `<span style="background: ${acosBg}; color: ${acosColor}; padding: 6px 12px; border-radius: 6px; font-weight: 800; font-size: 0.9rem; display: inline-block;">${acosVal.toFixed(1)}%</span>`;

                html += `<tr class="hover:bg-surface-container-low transition-colors" style="border-bottom: 1px solid rgba(198,198,205,0.3); background:#ffffff;">
                    <td style="padding: 14px 24px; font-weight: 800; color: #0051d5; text-align: center;">#${idx + 1}</td>
                    <td style="padding: 14px 24px; text-align: left;">
                        <div style="display: flex; flex-direction: column; min-width: 0; flex: 1;">
                            <div style="font-weight: 800; color: #191c1e; font-size: 0.95rem; line-height: 1.35; margin-bottom: 4px; word-break: break-word; white-space: normal;" title="${c.campaign_name}">${c.campaign_name}</div>
                            <div style="font-size: 0.78rem; color: #45464d; font-weight: 500; line-height: 1.4; word-break: break-word; white-space: normal;" title="${c.ad_group_name || 'N/A'}"><i class="fas fa-layer-group me-1"></i> ${c.ad_group_name || 'N/A'}</div>
                        </div>
                    </td>
                    <td style="padding: 14px 24px; font-weight: 700; color: #45464d; font-size: 0.85rem; text-align: left; word-break: break-word; white-space: normal;" title="${c.targeting || 'N/A'}">${c.targeting || 'N/A'}</td>
                    <td style="padding: 14px 24px; text-align: left; vertical-align: middle;"><span style="background: #f2f4f6; color: #45464d; padding: 4px 8px; border-radius: 6px; font-weight: 800; font-size: 0.75rem; display: inline-block;">${c.match_type || 'N/A'}</span></td>
                    <td style="padding: 14px 24px; font-weight: 800; color: #ef4444; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums;">${formatCurrency(spend)}</td>
                    <td style="padding: 14px 24px; font-weight: 800; color: #0051d5; text-align: right; font-family: 'Inter', sans-serif; font-variant-numeric: tabular-nums; background: rgba(219,225,255,0.05);">${formatCurrency(sales)}</td>
                    <td style="padding: 14px 24px; text-align: right; vertical-align: middle;">${acosBadgeHtml}</td>
                    <td style="padding: 14px 24px; text-align: right; vertical-align: middle;">${roasBadgeHtml}</td>
                    <td style="padding: 14px 32px; text-align: center; vertical-align: middle;">
                        <span style="${bidStyle} font-size: 0.75rem; font-weight: 900; padding: 6px 12px; border-radius: 6px; letter-spacing: 0.02em; display: inline-block;">${bidAction}</span>
                    </td>
                </tr>`;
                });
            }
            $('#campaign_body').html(html || '<tr><td colspan="9" class="text-center" style="padding: 3rem; color: #94a3b8; font-weight: 600;">No targeting data found for the selected criteria.</td></tr>');

            if ($.fn.DataTable.isDataTable('#campaignTable')) {
                $('#campaignTable').DataTable().destroy();
            }
            const table = $('#campaignTable').DataTable({ 
                dom: 'rtip',
                order: [[4, 'desc']], 
                pageLength: 10
            });
            $('#campaign_search_input').off('keyup').on('keyup', function() {
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

            // Bidding
            let b_html = '';
            data.bidding.forEach(b => {
                const roas = b.spend > 0 ? (b.sales / b.spend).toFixed(2) : '0.00';
                b_html += `<tr>
                    <td style="font-weight: 800; color: #1e293b;">${b.bidding_strategy}</td>
                    <td style="text-align: right; font-weight: 700; color: #475569;">${formatCurrency(b.spend)}</td>
                    <td style="text-align: right; font-weight: 800; color: #10b981;">${formatCurrency(b.sales)}</td>
                    <td style="text-align: center; font-weight: 800; color: #3b82f6;">${roas}</td>
                </tr>`;
            });
            $('#bidding_body').html(b_html || '<tr><td colspan="4" class="text-center">No data</td></tr>');
        }).fail(function() {
            $('#refresh_campaigns').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> ANALYZE PERFORMANCE');
            $('#campaign_body').html('<tr><td colspan="8" class="text-center text-danger py-5">Error loading data.</td></tr>');
        });
    }

    $('#refresh_campaigns').click(loadCampaignData);
});
</script>

<style>
/* Premium overrides for campaignTable DataTables styling */
#campaignTable {
    border-collapse: collapse !important;
    border-spacing: 0 !important;
    width: 100% !important;
    table-layout: fixed !important;
}
#campaignTable th {
    background: #f2f4f6 !important;
    border-bottom: 1px solid #c6c6cd !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    color: #45464d !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    padding: 16px 24px !important;
    vertical-align: middle !important;
    border-top: none !important;
    position: relative !important;
}
#campaignTable th.sorting::after,
#campaignTable th.sorting_asc::after,
#campaignTable th.sorting_desc::after,
#campaignTable th.sorting::before,
#campaignTable th.sorting_asc::before,
#campaignTable th.sorting_desc::before {
    display: none !important; /* Hide ugly standard datatable arrow icons */
}
#campaignTable td {
    border-bottom: 1px solid rgba(198,198,205,0.3) !important;
    padding: 14px 24px !important;
    vertical-align: middle !important;
}
#campaignTable td:nth-child(2),
#campaignTable td:nth-child(3),
#campaignTable td:nth-child(4) {
    text-align: left !important;
}
#campaignTable td:nth-child(5),
#campaignTable td:nth-child(6),
#campaignTable td:nth-child(7),
#campaignTable td:nth-child(8) {
    text-align: right !important;
}
#campaignTable tr:hover td {
    background: #f8fafc !important;
}
/* Datatable control styles */
.dataTables_wrapper {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    padding-top: 0 !important;
    margin-top: 0 !important;
}
#campaignTable_wrapper > .row:first-child,
#campaignTable_wrapper > div:first-child,
.dataTables_wrapper > .row:first-child,
.dataTables_wrapper > div:first-child {
    display: none !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    overflow: hidden !important;
}
#campaignTable {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}.dataTables_wrapper > .row:last-child {
    background: #f2f4f6 !important;
    border-top: 1px solid #c6c6cd !important;
    padding: 16px 32px !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
}
.dataTables_wrapper > .row:last-child > div {
    padding: 0 !important;
    margin: 0 !important;
    background: transparent !important;
    flex: 1 !important;
    display: flex !important;
    align-items: center !important;
}
.dataTables_wrapper > .row:last-child > div:first-child {
    justify-content: flex-start !important;
    max-width: 35% !important;
}
.dataTables_wrapper > .row:last-child > div:last-child {
    justify-content: flex-end !important;
    max-width: 65% !important;
}
.dataTables_wrapper .dataTables_info {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    font-size: 12px !important;
    color: #45464d !important;
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
    justify-content: flex-end !important;
}
/* Style datatable pagination buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px !important;
    margin: 0 4px !important;
    border-radius: 6px !important;
    border: 1px solid #c6c6cd !important;
    background: #ffffff !important;
    color: #45464d !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    font-size: 12px !important;
    transition: all 0.2s !important;
    display: inline-block !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current, 
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #64748b !important;
    color: #ffffff !important;
    border-color: #64748b !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f2f4f6 !important;
    color: #000000 !important;
    border-color: #c6c6cd !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
    background: #ffffff !important;
    color: #45464d !important;
}

.badge-sp { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 800; }
.badge-sb { background: #faf5ff; color: #a855f7; border: 1px solid #e9d5ff; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 800; }
.badge-sd { background: #fff1f2; color: #f43f5e; border: 1px solid #fecdd3; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 800; }
.bg-soft-primary { background-color: #e0e7ff; }

/* Campaigns performance columns */
.campaigns-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    margin-bottom: 2rem;
}

.table-card {
    background: #ffffff;
    border: 1px solid #e4e9f0;
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}

.table-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.table-card-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
}

.status-badge {
    font-size: 0.65rem;
    font-weight: 800;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-badge.high-roas {
    background: #eff6ff;
    color: #2563eb;
}

.status-badge.check-budget {
    background: #fef2f2;
    color: #ef4444;
}

.premium-table {
    width: 100%;
    border-collapse: collapse;
}

.premium-table th {
    font-size: 0.8rem;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    text-align: left;
}

.premium-table th.right {
    text-align: right;
}

.premium-table td {
    padding: 1.25rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.campaign-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 250px;
}

.campaign-sub {
    font-size: 0.75rem;
    color: #64748b;
    margin: 0.15rem 0 0 0;
    font-weight: 500;
}

.campaign-spend {
    font-size: 0.95rem;
    font-weight: 600;
    color: #475569;
    text-align: right;
}

.campaign-metric {
    font-size: 0.95rem;
    font-weight: 800;
    text-align: right;
}

.campaign-metric.roas {
    color: #2563eb;
}

.campaign-metric.acos {
    color: #ef4444;
}
</style>

<?php include '../../includes/footer.php'; ?>
