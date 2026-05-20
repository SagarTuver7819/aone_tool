<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Search Query Performance";
$page_subtitle = "Market Share & Brand Analytics";

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
            <h4 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-chart-pie me-2"></i> SEARCH QUERY PERFORMANCE</h4>
            <p class="text-muted small mb-0 fw-600">Track your market share, impressions, and brand conversion metrics.</p>
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
                <i class="fas fa-sync-alt me-2"></i> ANALYZE
            </button>
        </div>
    </div>
</div>

<!-- KPI GRID -->
<div class="kpi-grid mb-4">
    <div class="card kpi-card blue-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-search"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_total_queries">0</span></h3>
            <p>Tracked Queries</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-list"></i><span>Total Queries</span></div>
    </div>

    <div class="card kpi-card green-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-eye"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_total_impressions">0</span></h3>
            <p>Total Impressions</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-bullseye"></i><span>Market Visibility</span></div>
    </div>

    <div class="card kpi-card yellow-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-percentage"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_avg_share">0.00%</span></h3>
            <p>Avg Brand Share</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-chart-line"></i><span>Impression Share</span></div>
    </div>

    <div class="card kpi-card red-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_conv_share">0.00%</span></h3>
            <p>Purchase Share</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-check-circle"></i><span>Conversion Power</span></div>
    </div>
</div>

<!-- MAIN DATA TABLE -->
<div class="glass-card border-0 mb-5">
    <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-table me-2"></i> Brand Analytics Intelligence</h5>
            <p class="text-muted small mb-0 fw-600">Search Query Volume vs Brand Performance metrics.</p>
        </div>
        <div class="input-group" style="width: 320px; border-radius: 14px; overflow: hidden; border: 1.5px solid #e2e8f0;">
            <span class="input-group-text border-0 bg-white px-3"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="sqpSearch" class="form-control border-0 py-2" style="font-size: 0.85rem; font-weight: 700;" placeholder="Filter entries...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="analysis-table align-middle mb-0" id="sqpTable" style="width: 100%;">
            <thead>
                <tr>
                    <th class="px-4">Search Query</th>
                    <th class="text-center">Query Volume</th>
                    <th class="text-center">Total Impressions</th>
                    <th class="text-center">Brand Share (%)</th>
                    <th class="text-center">Brand Clicks</th>
                    <th class="text-center">Brand Sales</th>
                    <th class="text-center px-4">Action</th>
                </tr>
            </thead>
            <tbody id="sqp_body">
                <tr><td colspan="7" class="text-center py-5 text-muted fw-600">Initializing market analysis...</td></tr>
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

        $('#refresh_button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> LOADING...');
        $('#sqp_body').css('opacity', '0.5');
        
        $.ajax({
            url: '../../api/brand_data.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            dataType: 'json',
            success: function(res) {
                $('#refresh_button').prop('disabled', false).html('<i class="fas fa-sync-alt me-2"></i> ANALYZE');
                $('#sqp_body').css('opacity', '1');
                
                let html = '';
                let totalQueries = 0;
                let totalImps = 0;
                let sumShare = 0;

                if (res && res.search_queries && res.search_queries.length > 0) {
                    res.search_queries.forEach(row => {
                        // DYNAMIC KEY DETECTION
                        let query = 'N/A';
                        let volume = 0;
                        let impressions = 0;
                        let share = 0;

                        Object.keys(row).forEach(key => {
                            const lowerKey = key.toLowerCase();
                            const val = row[key];
                            if (lowerKey.includes('brand') || lowerKey.includes('query')) {
                                if (val !== 'Search Query') query = val;
                            }
                            if (lowerKey.includes('volume') || (lowerKey.includes('selectyear') && !isNaN(val))) {
                                volume = parseInt(val || 0);
                            }
                            if (lowerKey.includes('impressions') || lowerKey.includes('selectmonth')) {
                                impressions = parseInt(val || 0);
                            }
                            if (lowerKey.includes('score') || lowerKey.includes('share') || lowerKey.includes('monthly')) {
                                if (!isNaN(val)) share = parseFloat(val || 0);
                            }
                        });

                        if (query === 'N/A' || query === 'Search Query') return;

                        totalQueries++;
                        totalImps += impressions;
                        sumShare += share;

                        html += `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="px-4 py-3">
                                    <div class="fw-800" style="color: #1e293b; font-size: 0.95rem;">${query}</div>
                                    <div class="text-muted" style="font-size: 0.7rem; font-weight: 600;">Report Date: ${row.report_date}</div>
                                </td>
                                <td class="text-center fw-800" style="font-size: 0.95rem;">${volume.toLocaleString()}</td>
                                <td class="text-center fw-700 text-muted" style="font-size: 0.85rem;">${impressions.toLocaleString()}</td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress" style="height: 8px; width: 70px; background: #f1f5f9; border-radius: 4px;">
                                            <div class="progress-bar" style="width: ${Math.min(share, 100)}%; background: ${share > 20 ? '#10b981' : '#3b82f6'}; border-radius: 4px;"></div>
                                        </div>
                                        <span class="fw-900" style="font-size: 0.9rem; color: ${share > 20 ? '#059669' : '#1e293b'};">${share.toFixed(1)}%</span>
                                    </div>
                                </td>
                                <td class="text-center text-muted fw-600">-</td>
                                <td class="text-center text-muted fw-600">-</td>
                                <td class="text-center px-4">
                                    <button class="btn btn-sm btn-light border fw-900" style="font-size: 0.65rem; border-radius: 8px; padding: 5px 12px;">DETAILS</button>
                                </td>
                            </tr>
                        `;
                    });
                } 
                
                if (totalQueries === 0) {
                    html = `<tr><td colspan="7" class="text-center py-5 text-muted fw-600" style="font-size: 1rem;">
                        <i class="fas fa-search d-block mb-3 opacity-20 fa-3x"></i>
                        No SQP data found for ${from} to ${to}.<br>
                        <small class="text-primary mt-2 d-block">Note: Currently data is only available between 2026-01-01 and 2026-03-31.</small>
                    </td></tr>`;
                }

                $('#sqp_body').html(html);
                $('#kpi_total_queries').text(totalQueries);
                $('#kpi_total_impressions').text(totalImps.toLocaleString());
                $('#kpi_avg_share').text((totalQueries > 0 ? sumShare / totalQueries : 0).toFixed(2) + '%');

                if ($.fn.DataTable.isDataTable('#sqpTable')) {
                    $('#sqpTable').DataTable().destroy();
                }
                $('#sqpTable').DataTable({
                    order: [[1, 'desc']],
                    pageLength: 25,
                    language: { search: "_INPUT_", searchPlaceholder: "Search keywords..." }
                });
            },
            error: function() {
                $('#refresh_button').prop('disabled', false).html('<i class="fas fa-exclamation-triangle me-2"></i> ERROR');
                $('#sqp_body').html('<tr><td colspan="7" class="text-center py-5 text-danger fw-700">Failed to connect to the Brand Data engine.</td></tr>');
            }
        });
    }

    $('#refresh_button').on('click', loadData);
    $('#filter_customer, #filter_from, #filter_to').on('change', loadData);
    loadData();

    $('#sqpSearch').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $("#sqp_body tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
