<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Loyalty & Retention";
$page_subtitle = "Customer Lifetime Value & Repeat Purchase Strategy";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    body { background-color: #f0f7f6 !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
    .loyalty-hero {
        background: linear-gradient(135deg, #00353B 0%, #005a63 100%);
        border-radius: 24px;
        padding: 3rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .loyalty-hero::after {
        content: ''; position: absolute; top: -50%; right: -10%; width: 400px; height: 400px;
        background: rgba(199, 254, 145, 0.1); border-radius: 50%; blur: 80px;
    }
</style>

<!-- HEADER & FILTERS -->
<div class="glass-card mb-4 mt-2">
    <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-users me-2"></i> LOYALTY & RETENTION</h4>
            <p class="text-muted small mb-0 fw-600">Analyze customer repeat behavior and brand loyalty metrics.</p>
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
                <i class="fas fa-sync-alt me-2"></i> ANALYZE LOYALTY
            </button>
        </div>
    </div>
</div>

<!-- KPI GRID -->
<div class="kpi-grid mb-4">
    <div class="card kpi-card green-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-redo"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_repeat_rate">0.0%</span></h3>
            <p>Repeat Purchase Rate</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-user-check"></i><span>Customer Loyalty</span></div>
    </div>

    <div class="card kpi-card blue-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-hand-holding-usd"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_repeat_revenue">$0.00</span></h3>
            <p>Repeat Revenue</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-dollar-sign"></i><span>Retention Value</span></div>
    </div>

    <div class="card kpi-card yellow-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-user-plus"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_total_customers">0</span></h3>
            <p>Unique Customers</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-users"></i><span>Total Reach</span></div>
    </div>

    <div class="card kpi-card red-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-heart"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_loyalty_score">0/10</span></h3>
            <p>Brand Loyalty Score</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-star"></i><span>Growth Health</span></div>
    </div>
</div>

<!-- MAIN ANALYTICS -->
<div class="row mb-5">
    <!-- PRODUCT LOYALTY TABLE -->
    <div class="col-lg-8">
        <div class="glass-card h-100">
            <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-medal me-2"></i> Product Loyalty Index</h5>
                    <p class="text-muted small mb-0 fw-600">Which products drive the most repeat purchases?</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="analysis-table align-middle mb-0" id="loyaltyTable">
                    <thead>
                        <tr>
                            <th class="px-4 text-start">Product ASIN / SKU</th>
                            <th class="text-center">Total Orders</th>
                            <th class="text-center">Repeat Orders</th>
                            <th class="text-center">Repeat Revenue</th>
                            <th class="text-center px-4">Repeat %</th>
                        </tr>
                    </thead>
                    <tbody id="loyalty_body">
                        <tr><td colspan="5" class="text-center py-5 text-muted fw-600">No repeat purchase data found.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- INSIGHTS & ACTIONS -->
    <div class="col-lg-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-900 mb-4" style="color: #00353B;"><i class="fas fa-lightbulb me-2"></i> Loyalty Strategy</h5>
            
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="background: #10b98115; color: #10b981; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-sync"></i>
                    </div>
                    <h6 class="fw-800 mb-0">Retention Target</h6>
                </div>
                <p class="small text-muted fw-600">Aim for a repeat purchase rate of 15% or higher for healthy brand growth.</p>
            </div>

            <div class="mb-4">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="background: #3b82f615; color: #3b82f6; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h6 class="fw-800 mb-0">Subscribe & Save</h6>
                </div>
                <p class="small text-muted fw-600">Items with >10% repeat rate are prime candidates for S&S enrollment.</p>
            </div>

            <div class="alert alert-info border-0" style="background: #f0f9ff; border-radius: 14px;">
                <h6 class="fw-900 text-primary mb-1 small"><i class="fas fa-info-circle me-1"></i> Data Sync Note</h6>
                <p class="mb-0" style="font-size: 0.75rem; font-weight: 600; line-height: 1.4;">
                    Retention metrics require the <b>Brand Analytics - Repeat Purchase</b> report to be uploaded in the Intelligence Engine.
                </p>
            </div>

            <button class="action-btn w-100 mt-3" onclick="window.location.href='../report_upload/'">
                <i class="fas fa-upload me-2"></i> UPLOAD NEW REPORTS
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function loadData() {
        const customerId = $('#filter_customer').val();
        const from = $('#filter_from').val();
        const to = $('#filter_to').val();

        $('#refresh_button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> LOADING...');
        
        $.ajax({
            url: '../../api/brand_data.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            success: function(res) {
                $('#refresh_button').prop('disabled', false).html('<i class="fas fa-sync-alt me-2"></i> ANALYZE LOYALTY');
                
                let html = '';
                let tOrders = 0, tRepeat = 0, tRepeatRev = 0, tUnique = 0;

                if (res.repeat_purchases && res.repeat_purchases.length > 0) {
                    res.repeat_purchases.forEach(row => {
                        // Detect dynamic keys
                        let asin = row.asin || row.sku || 'N/A';
                        let orders = parseInt(row.totalorders || row.orders || 0);
                        let repeat = parseInt(row.repeatorders || row.repeat_customers || 0);
                        let revenue = parseFloat(row.repeatrevenue || row.repeat_sales || 0);
                        let rate = parseFloat(row.repeatrate || row.repeat_purchase_pct || 0);

                        tOrders += orders;
                        tRepeat += repeat;
                        tRepeatRev += revenue;
                        tUnique += (orders - repeat); // Approx unique if not provided

                        html += `
                            <tr>
                                <td class="px-4 py-3 text-start">
                                    <div class="fw-900" style="color: #1e293b;">${asin}</div>
                                    <div class="text-muted" style="font-size: 0.65rem; font-weight: 700;">LOYALTY TIER A</div>
                                </td>
                                <td class="text-center fw-600 text-muted">${orders.toLocaleString()}</td>
                                <td class="text-center fw-800">${repeat.toLocaleString()}</td>
                                <td class="text-center fw-700">$${revenue.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                                <td class="text-center px-4">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress" style="height: 6px; width: 60px; background: #f1f5f9; border-radius: 4px;">
                                            <div class="progress-bar" style="width: ${Math.min(rate, 100)}%; background: #10b981;"></div>
                                        </div>
                                        <span class="fw-900" style="color: #059669;">${rate.toFixed(1)}%</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center py-5 text-muted fw-600">No loyalty data found for this period. Please upload Brand Analytics reports.</td></tr>';
                }

                $('#loyalty_body').html(html);
                $('#kpi_repeat_rate').text((tOrders > 0 ? (tRepeat / tOrders * 100) : 0).toFixed(1) + '%');
                $('#kpi_repeat_revenue').text('$' + tRepeatRev.toLocaleString(undefined, {minimumFractionDigits:2}));
                $('#kpi_total_customers').text(tUnique.toLocaleString());
                $('#kpi_loyalty_score').text(Math.min(10, Math.ceil((tRepeat / tOrders) * 40)) + '/10');

                if ($.fn.DataTable.isDataTable('#loyaltyTable')) {
                    $('#loyaltyTable').DataTable().destroy();
                }
                $('#loyaltyTable').DataTable({
                    order: [[4, 'desc']],
                    pageLength: 25,
                    language: { search: "_INPUT_", searchPlaceholder: "Search products..." }
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
