<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Inventory Health";
$page_subtitle = "Stock Forecasting & Stockout Prevention";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    body { background-color: #f0f7f6 !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
    .status-badge {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .status-out { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    .status-low { background: #fef3c7; color: #f59e0b; border: 1px solid #fde68a; }
    .status-healthy { background: #dcfce7; color: #10b981; border: 1px solid #bbf7d0; }
    .status-excess { background: #dbeafe; color: #3b82f6; border: 1px solid #bfdbfe; }
</style>

<!-- HEADER & FILTERS -->
<div class="glass-card mb-4 mt-2">
    <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-warehouse me-2"></i> INVENTORY HEALTH</h4>
            <p class="text-muted small mb-0 fw-600">Days of Stock (DoS) forecasting and reorder intelligence.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
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
                <i class="fas fa-sync-alt me-2"></i> REFRESH INVENTORY
            </button>
        </div>
    </div>
</div>

<!-- KPI GRID -->
<div class="kpi-grid mb-4">
    <div class="card kpi-card green-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-boxes"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_total_skus">0</span></h3>
            <p>Total SKUs</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-list"></i><span>Unique Products</span></div>
    </div>

    <div class="card kpi-card red-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-exclamation-circle"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_oos">0</span></h3>
            <p>Out of Stock</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-times-circle"></i><span>Urgent Action</span></div>
    </div>

    <div class="card kpi-card yellow-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_low_stock">0</span></h3>
            <p>Low Stock (<15d)</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-truck-loading"></i><span>Reorder Soon</span></div>
    </div>

    <div class="card kpi-card blue-theme">
        <div class="kpi-header">
            <div class="kpi-icon"><i class="fas fa-coins"></i></div>
        </div>
        <div class="kpi-body">
            <h3><span id="kpi_total_value">$0</span></h3>
            <p>Inventory Value</p>
        </div>
        <div class="kpi-footer"><i class="fas fa-wallet"></i><span>Total Investment</span></div>
    </div>
</div>

<!-- INVENTORY TABLE -->
<div class="glass-card border-0 mb-5">
    <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-list-check me-2"></i> Stock Forecasting Dashboard</h5>
            <p class="text-muted small mb-0 fw-600">Latest Report: <span id="report_date_label" class="fw-900 text-primary">N/A</span></p>
        </div>
        <div class="input-group" style="width: 320px; border-radius: 14px; overflow: hidden; border: 1.5px solid #e2e8f0;">
            <span class="input-group-text border-0 bg-white px-3"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="inventorySearch" class="form-control border-0 py-2" style="font-size: 0.85rem; font-weight: 700;" placeholder="Search SKU or ASIN...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="analysis-table align-middle mb-0" id="inventoryTable" style="width: 100%;">
            <thead>
                <tr>
                    <th class="px-4">Product Detail</th>
                    <th class="text-center">Available</th>
                    <th class="text-center">Inbound</th>
                    <th class="text-center">Avg. Sales (30d)</th>
                    <th class="text-center">DoS (Days)</th>
                    <th class="text-center">Status</th>
                    <th class="text-center px-4">Action</th>
                </tr>
            </thead>
            <tbody id="inventory_body">
                <tr><td colspan="7" class="text-center py-5 text-muted fw-600">Initializing inventory audit...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    function loadData() {
        const customerId = $('#filter_customer').val();
        $('#refresh_button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> REFRESHING...');
        
        $.ajax({
            url: '../../api/inventory_data.php',
            data: { customer_id: customerId },
            success: function(res) {
                $('#refresh_button').prop('disabled', false).html('<i class="fas fa-sync-alt me-2"></i> REFRESH INVENTORY');
                
                $('#report_date_label').text(res.report_date || 'N/A');
                
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(row => {
                        let statusClass = 'status-healthy';
                        if (row.status === 'Out of Stock') statusClass = 'status-out';
                        if (row.status === 'Low Stock') statusClass = 'status-low';
                        if (row.status === 'Excess') statusClass = 'status-excess';

                        html += `
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-900" style="color: #1e293b; font-size: 0.9rem;">${row.sku}</div>
                                    <div class="text-muted" style="font-size: 0.7rem; font-weight: 700;">ASIN: ${row.asin}</div>
                                </td>
                                <td class="text-center fw-900" style="font-size: 1rem;">${row.stock.toLocaleString()}</td>
                                <td class="text-center fw-700 text-muted">${row.inbound.toLocaleString()}</td>
                                <td class="text-center fw-700 text-primary">${row.ads.toFixed(2)} / day</td>
                                <td class="text-center">
                                    <div class="fw-900 ${row.dos < 15 ? 'text-danger' : 'text-dark'}">${row.dos >= 999 ? '∞' : row.dos}</div>
                                    <div class="progress" style="height: 4px; width: 60px; margin: 2px auto; background: #f1f5f9;">
                                        <div class="progress-bar" style="width: ${Math.min(row.dos * 2, 100)}%; background: ${row.dos < 15 ? '#ef4444' : (row.dos < 30 ? '#f59e0b' : '#10b981')};"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge ${statusClass}">${row.status}</span>
                                </td>
                                <td class="text-center px-4">
                                    <button class="btn btn-sm btn-light border fw-900" style="font-size: 0.65rem; border-radius: 8px;">REORDER</button>
                                </td>
                            </tr>
                        `;
                    });

                    $('#kpi_total_skus').text(res.summary.total_skus);
                    $('#kpi_oos').text(res.summary.out_of_stock);
                    $('#kpi_low_stock').text(res.summary.low_stock);
                    $('#kpi_total_value').text('$' + res.summary.total_value.toLocaleString(undefined, {maximumFractionDigits:0}));
                } else {
                    html = '<tr><td colspan="7" class="text-center py-5 text-muted fw-600">No inventory data found. Please upload the "FBA Inventory" report.</td></tr>';
                }

                $('#inventory_body').html(html);

                if ($.fn.DataTable.isDataTable('#inventoryTable')) {
                    $('#inventoryTable').DataTable().destroy();
                }
                $('#inventoryTable').DataTable({
                    order: [[4, 'asc']],
                    pageLength: 25,
                    language: { search: "_INPUT_", searchPlaceholder: "Filter SKUs..." }
                });
            }
        });
    }

    $('#refresh_button').on('click', loadData);
    $('#filter_customer').on('change', loadData);
    loadData();

    $('#inventorySearch').on('keyup', function() {
        $('#inventoryTable').DataTable().search($(this).val()).draw();
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
