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

<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden; background: #ffffff;">
    <div style="background: #ffffff; padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h5 style="margin: 0; font-weight: 900; color: #1e293b;"><i class="fas fa-layer-group text-primary me-2"></i> Campaign Performance Breakdown</h5>
            <p style="margin: 0; font-size: 0.75rem; color: #64748b; font-weight: 600;">Cross-channel advertising efficiency metrics</p>
        </div>
        <div class="badge" style="background: #eff6ff; color: #3b82f6; font-size: 0.7rem; padding: 8px 16px; border-radius: 50px; font-weight: 800; border: 1px solid #dbeafe;">SP, SB, SD ACTIVE</div>
    </div>
    <div style="padding: 1.5rem;">
        <table class="table table-hover align-middle mb-0" id="campaignTable" style="width: 100%;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th class="px-4 py-3 text-uppercase small fw-800 text-muted">Campaign & Ad Group</th>
                    <th class="text-uppercase small fw-800 text-muted">Targeting</th>
                    <th class="text-center text-uppercase small fw-800 text-muted">Match</th>
                    <th class="text-end text-uppercase small fw-800 text-muted">Spend</th>
                    <th class="text-end text-uppercase small fw-800 text-muted">Sales</th>
                    <th class="text-center text-uppercase small fw-800 text-muted">ACoS</th>
                    <th class="text-center text-uppercase small fw-800 text-muted">ROAS</th>
                    <th class="text-center text-uppercase small fw-800 text-muted px-4">Bid Action</th>
                </tr>
            </thead>
            <tbody id="campaign_body" style="border-top: none;"></tbody>
        </table>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 2rem; margin-bottom: 2rem;">
    <!-- Placement Performance -->
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-map-marker-alt" style="color: #10b981; margin-right: 0.5rem;"></i> Placement Performance
        </div>
        <div style="padding: 1rem;">
            <table class="table" id="placementTable" style="width: 100%; font-size: 0.85rem;">
                <thead style="background: #f1f5f9;">
                    <tr>
                        <th>Placement</th>
                        <th style="text-align: right;">Spend</th>
                        <th style="text-align: center;">ACoS</th>
                        <th style="text-align: center;">CTR</th>
                    </tr>
                </thead>
                <tbody id="placement_body"></tbody>
            </table>
        </div>
    </div>

    <!-- Bidding Strategy -->
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
            
            // Campaigns Table
            let html = '';
            if (data.campaigns && data.campaigns.length > 0) {
                data.campaigns.forEach(c => {
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

                html += `<tr>
                    <td class="px-4 py-3">
                        <div style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">${c.campaign_name}</div>
                        <div style="font-size: 0.65rem; color: #64748b; font-weight: 600;"><i class="fas fa-layer-group me-1"></i> ${c.ad_group_name || 'N/A'}</div>
                    </td>
                    <td style="font-weight: 700; color: #334155; font-size: 0.8rem;">${c.targeting || 'N/A'}</td>
                    <td class="text-center"><span class="badge bg-light text-dark border" style="font-size: 0.6rem;">${c.match_type || 'N/A'}</span></td>
                    <td class="text-end fw-700" style="color: #475569;">${formatCurrency(c.spend)}</td>
                    <td class="text-end fw-900" style="color: #10b981;">${formatCurrency(c.sales)}</td>
                    <td class="text-center">
                        <span style="color: ${acos > 35 ? '#ef4444' : (acos < 25 ? '#10b981' : '#f59e0b')}; font-weight: 900; background: ${acos > 35 ? '#fff1f2' : (acos < 25 ? '#f0fdf4' : '#fffbeb')}; padding: 4px 10px; border-radius: 8px; font-size: 0.75rem;">${acos}%</span>
                    </td>
                    <td class="text-center fw-800 text-primary">${roas}</td>
                    <td class="text-center px-4">
                        <span style="${bidStyle} font-size: 0.6rem; font-weight: 900; padding: 4px 8px; border-radius: 6px; letter-spacing: 0.02em;">${bidAction}</span>
                    </td>
                </tr>`;
                });
            }
            $('#campaign_body').html(html || '<tr><td colspan="8" class="text-center" style="padding: 3rem; color: #94a3b8; font-weight: 600;">No targeting data found for the selected criteria.</td></tr>');

            if ($.fn.DataTable.isDataTable('#campaignTable')) {
                $('#campaignTable').DataTable().destroy();
            }
            $('#campaignTable').DataTable({ 
                order: [[3, 'desc']], 
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Quick filter campaigns..."
                }
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
.badge-sp { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 800; }
.badge-sb { background: #faf5ff; color: #a855f7; border: 1px solid #e9d5ff; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 800; }
.badge-sd { background: #fff1f2; color: #f43f5e; border: 1px solid #fecdd3; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 800; }
.bg-soft-primary { background-color: #e0e7ff; }
</style>

<?php include '../../includes/footer.php'; ?>
