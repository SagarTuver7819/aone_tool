<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Placement Analysis";
$page_subtitle = "Top of Search vs. Product Page Optimization";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    body { background-color: #f0f7f6 !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
</style>

<!-- HEADER & FILTERS -->
<div class="glass-card mb-4 mt-2">
    <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-map-marker-alt me-2"></i> PLACEMENT ANALYSIS</h4>
            <p class="text-muted small mb-0 fw-600">Analyze efficiency across Top of Search, Product Pages, and Rest of Search.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <div class="input-group input-group-sm" style="width: 280px; border-radius: 12px; overflow: hidden; border: 1.5px solid #e2e8f0;">
                <span class="input-group-text bg-white border-0 ps-3"><i class="fas fa-calendar-alt text-primary"></i></span>
                <input type="date" id="filter_from" class="form-control border-0 fw-700" style="font-size: 0.8rem;" value="2026-01-01">
                <input type="date" id="filter_to" class="form-control border-0 fw-700" style="font-size: 0.8rem;" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <select id="filter_customer" class="form-select form-select-sm fw-700" style="width: 200px; border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 0.5rem 1rem;">
                <option value="">All Accounts</option>
                <?php
                $customers = get_all_customers();
                while($c = $customers->fetch_assoc()) {
                    $sel = ($c['id'] == ($_SESSION['customer_id'] ?? 0)) ? 'selected' : '';
                    echo "<option value='{$c['id']}' $sel>" . htmlspecialchars($c['customer_name']) . "</option>";
                }
                ?>
            </select>
            <button id="refresh_button" class="action-btn">
                <i class="fas fa-bolt me-2"></i> ANALYZE PLACEMENT
            </button>
        </div>
    </div>
</div>

<!-- KPI GRID -->
<div class="kpi-grid mb-4">
    <div class="card kpi-card blue-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_total_spend">$0.00</span></h3>
            <p>Total Ad Spend</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-coins"></i><span>Global Spend</span></div>
    </div>

    <div class="card kpi-card green-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-shopping-bag"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_total_sales">$0.00</span></h3>
            <p>Total Ad Sales</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-rocket"></i><span>PPC Revenue</span></div>
    </div>

    <div class="card kpi-card yellow-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_avg_roas">0.00</span></h3>
            <p>Average ROAS</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-percentage"></i><span>Efficiency Score</span></div>
    </div>

    <div class="card kpi-card red-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-fire"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_avg_acos">0.00%</span></h3>
            <p>Average ACoS</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-tachometer-alt"></i><span>Spend Velocity</span></div>
    </div>
</div>

<!-- PLACEMENT COMPARISON CARDS -->
<div class="row mb-5" id="placement_insights_grid">
    <!-- Dynamic insights will be injected here -->
</div>

<!-- DATA TABLE -->
<div class="glass-card border-0 mb-5">
    <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-table me-2"></i> Detailed Placement Breakdown</h5>
            <p class="text-muted small mb-0 fw-600">Metric comparison across all Amazon placements.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="analysis-table align-middle mb-0" id="placementTable" style="width: 100%;">
            <thead>
                <tr>
                    <th class="px-4 text-start">Placement Type</th>
                    <th class="text-center">Impressions</th>
                    <th class="text-center">Clicks</th>
                    <th class="text-center">CTR (%)</th>
                    <th class="text-center">Avg. CPC</th>
                    <th class="text-center">Spend</th>
                    <th class="text-center">Sales</th>
                    <th class="text-center">ACoS</th>
                    <th class="text-center px-4">ROAS</th>
                </tr>
            </thead>
            <tbody id="placement_body">
                <tr><td colspan="9" class="text-center py-5">Initialising placement analysis...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    function loadData() {
        const customerId = $('#filter_customer').val();
        const from = $('#filter_from').val();
        const to = $('#filter_to').val();

        $('#refresh_button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> ANALYZING...');
        
        $.ajax({
            url: '../../api/placement_data.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            success: function(res) {
                $('#refresh_button').prop('disabled', false).html('<i class="fas fa-bolt me-2"></i> ANALYZE PLACEMENT');
                
                let html = '';
                let insightsHtml = '';
                let tSpend = 0, tSales = 0;

                if (res.data && res.data.length > 0) {
                    res.data.forEach(row => {
                        const spend = parseFloat(row.spend || 0);
                        const sales = parseFloat(row.sales || 0);
                        const acos = sales > 0 ? (spend / sales) * 100 : 0;
                        const roas = spend > 0 ? (sales / spend) : 0;
                        const ctr = parseFloat(row.ctr || 0);
                        const cpc = parseFloat(row.cpc || 0);

                        tSpend += spend;
                        tSales += sales;

                        // Add to main table
                        html += `
                            <tr>
                                <td class="px-4 py-3 text-start">
                                    <div class="fw-900" style="color: #1e293b; font-size: 0.95rem;">${row.placement || 'Other'}</div>
                                    <div class="text-muted" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Location Insight</div>
                                </td>
                                <td class="text-center fw-600 text-muted">${parseInt(row.impressions).toLocaleString()}</td>
                                <td class="text-center fw-600">${parseInt(row.clicks).toLocaleString()}</td>
                                <td class="text-center fw-800 text-primary">${ctr.toFixed(2)}%</td>
                                <td class="text-center fw-700">$${cpc.toFixed(2)}</td>
                                <td class="text-center fw-900" style="color: #00353B;">$${spend.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                                <td class="text-center fw-900 text-success">$${sales.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                                <td class="text-center">
                                    <span class="badge ${acos < 30 ? 'bg-success' : (acos < 50 ? 'bg-warning' : 'bg-danger')}" style="padding: 6px 12px; border-radius: 8px;">
                                        ${acos.toFixed(2)}%
                                    </span>
                                </td>
                                <td class="text-center px-4 fw-900 text-info">${roas.toFixed(2)}x</td>
                            </tr>
                        `;

                        // Add to insights grid for major placements
                        if (['Top of Search on Amazon', 'Product pages on Amazon', 'Rest of search on Amazon'].includes(row.placement)) {
                            const iconMap = {
                                'Top of Search on Amazon': 'fa-arrow-up',
                                'Product pages on Amazon': 'fa-images',
                                'Rest of search on Amazon': 'fa-search'
                            };
                            const colorMap = {
                                'Top of Search on Amazon': '#3b82f6',
                                'Product pages on Amazon': '#10b981',
                                'Rest of search on Amazon': '#f59e0b'
                            };

                            insightsHtml += `
                                <div class="col-md-4">
                                    <div class="glass-card p-4 h-100" style="border-top: 5px solid ${colorMap[row.placement]} !important;">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div style="background: ${colorMap[row.placement]}15; color: ${colorMap[row.placement]}; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                                <i class="fas ${iconMap[row.placement]}"></i>
                                            </div>
                                            <div class="text-end">
                                                <p class="small text-muted fw-800 mb-0">SALES SHARE</p>
                                                <h5 class="fw-900 mb-0" id="share_${row.placement.replace(/\s+/g, '_')}">0%</h5>
                                            </div>
                                        </div>
                                        <h6 class="fw-900 text-dark mb-1">${row.placement.replace(' on Amazon', '')}</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="small fw-700 text-muted">ACoS Efficiency</span>
                                            <span class="small fw-900" style="color: ${acos < 30 ? '#10b981' : '#ef4444'}">${acos.toFixed(2)}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px; border-radius: 10px; background: #f1f5f9;">
                                            <div class="progress-bar" style="width: ${acos < 100 ? acos : 100}%; background: ${acos < 30 ? '#10b981' : (acos < 50 ? '#f59e0b' : '#ef4444')};"></div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                } else {
                    html = '<tr><td colspan="9" class="text-center py-5 text-muted fw-700">No placement analysis found for this period.</td></tr>';
                    insightsHtml = '<div class="col-12 text-center py-4 text-muted">Awaiting data for visual insights...</div>';
                }

                $('#placement_body').html(html);
                $('#placement_insights_grid').html(insightsHtml);

                // Update Shares after tSales is calculated
                if (res.data) {
                    res.data.forEach(row => {
                        const sales = parseFloat(row.sales || 0);
                        const share = tSales > 0 ? (sales / tSales) * 100 : 0;
                        $(`#share_${row.placement.replace(/\s+/g, '_')}`).text(share.toFixed(1) + '%');
                    });
                }

                $('#kpi_total_spend').text('$' + tSpend.toLocaleString(undefined, {minimumFractionDigits:2}));
                $('#kpi_total_sales').text('$' + tSales.toLocaleString(undefined, {minimumFractionDigits:2}));
                $('#kpi_avg_roas').text(tSpend > 0 ? (tSales / tSpend).toFixed(2) : '0.00');
                $('#kpi_avg_acos').text((tSales > 0 ? (tSpend / tSales * 100) : 0).toFixed(2) + '%');

                if ($.fn.DataTable.isDataTable('#placementTable')) {
                    $('#placementTable').DataTable().destroy();
                }
                $('#placementTable').DataTable({
                    order: [[5, 'desc']],
                    pageLength: 25,
                    language: { search: "_INPUT_", searchPlaceholder: "Filter placements..." }
                });
            }
        });
    }

    $('#refresh_button').on('click', loadData);
    $('#filter_customer, #filter_from, #filter_to').on('change', loadData);
    loadData();
});
</script>

<?php include '../../includes/footer.php'; ?>
