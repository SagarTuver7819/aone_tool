<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Order Intelligence";
$page_subtitle = "Daily SKU/ASIN Purchase & Fulfillment Tracking";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 16px;">
    <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 250px;">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Search Orders</label>
            <input type="text" id="order_search" class="form-control" placeholder="Order ID, SKU, or ASIN..." style="padding: 0.75rem; border-radius: 8px;">
        </div>
        <div class="form-group">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Status Filter</label>
            <select id="status_filter" class="form-control" style="padding: 0.75rem; border-radius: 8px;">
                <option value="">All Statuses</option>
                <option value="Shipped">Shipped</option>
                <option value="Unshipped">Unshipped</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        <button id="refresh_orders" class="btn btn-primary" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-sync-alt"></i> REFRESH
        </button>
    </div>
</div>

<div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
    <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155; display: flex; justify-content: space-between; align-items: center;">
        <div><i class="fas fa-box" style="color: #6366f1; margin-right: 0.5rem;"></i> Recent Amazon Orders</div>
        <div style="font-size: 0.75rem; color: #64748b;" id="total_count">0 orders found</div>
    </div>
    <div style="padding: 1rem; overflow-x: auto;">
        <table class="table" id="orderTable" style="width: 100%;">
            <thead style="background: #f1f5f9;">
                <tr>
                    <th>Purchase Date</th>
                    <th>Order ID</th>
                    <th>SKU / ASIN</th>
                    <th style="text-align: center;">Qty</th>
                    <th>Status</th>
                    <th>Ship City/State</th>
                </tr>
            </thead>
            <tbody id="order_body">
                <tr><td colspan="6" class="text-center">Loading orders...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    loadOrders();
    
    $('#refresh_orders').click(loadOrders);
    $('#order_search').on('keyup', function(e) {
        if (e.key === 'Enter') loadOrders();
    });
});

function loadOrders() {
    const search = $('#order_search').val();
    const status = $('#status_filter').val();
    
    $('#refresh_orders').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    
    $.get('../../api/order_data.php', {
        search: search,
        status: status
    }, function(data) {
        $('#refresh_orders').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> REFRESH');
        $('#total_count').text(`${data.total || 0} orders found`);
        
        let html = '';
        if (data.orders && data.orders.length > 0) {
            data.orders.forEach(o => {
                const date = new Date(o.purchase_date).toLocaleDateString('en-US', {month: 'short', day: '2-digit', year: 'numeric'});
                const statusClass = o.order_status === 'Shipped' ? 'success' : (o.order_status === 'Cancelled' ? 'danger' : 'warning');
                
                html += `<tr>
                    <td style="color: #64748b; font-size: 1rem; font-weight: 600; padding: 1rem;">${date}</td>
                    <td style="font-weight: 800; color: #1e293b; font-size: 1.1rem; font-family: monospace;">${o.amazon_order_id}</td>
                    <td style="padding: 1rem;">
                        <div style="font-weight: 800; color: #4f46e5; font-size: 1.1rem;">${o.sku}</div>
                        <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">${o.asin}</div>
                    </td>
                    <td style="text-align: center; font-weight: 800; font-size: 1.1rem;">${o.quantity}</td>
                    <td><span class="badge badge-${statusClass}" style="font-size: 0.75rem; padding: 6px 12px; border-radius: 50px; font-weight: 800; text-transform: uppercase;">${o.order_status}</span></td>
                    <td style="font-size: 1rem; color: #475569; font-weight: 600;">${o.ship_city}, ${o.ship_state}</td>
                </tr>`;
            });
        } else {
            html = '<tr><td colspan="6" class="text-center">No orders found matching your criteria.</td></tr>';
        }
        $('#order_body').html(html);

        // Initialize DataTables
        if ($.fn.DataTable.isDataTable('#orderTable')) {
            $('#orderTable').DataTable().destroy();
        }
        $('#orderTable').DataTable({
            pageLength: 10,
            order: [[0, 'desc']],
            language: { search: "_INPUT_", searchPlaceholder: "Search orders..." }
        });
    });
}
</script>

<style>
.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef9c3; color: #854d0e; }
.badge-danger { background: #fee2e2; color: #991b1b; }
</style>

<?php include '../../includes/footer.php'; ?>
