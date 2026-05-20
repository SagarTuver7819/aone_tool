<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Returns & Reimbursements";
$page_subtitle = "Post-purchase Operations & Recovery Tracking";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #fff1f2 100%); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 16px;">
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
                <input type="date" id="filter_from" class="form-control" value="<?php echo date('Y-m-01'); ?>" style="padding: 0.6rem; border-radius: 8px;">
                <span style="color: #94a3b8;">to</span>
                <input type="date" id="filter_to" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="padding: 0.6rem; border-radius: 8px;">
            </div>
        </div>
        <button id="refresh_ops" class="btn btn-primary" style="background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-sync-alt"></i> REFRESH
        </button>
    </div>
    <div id="date_suggestion" style="margin-top: 1rem; font-size: 0.8rem; display: none;"></div>
</div>

<div id="no_data_alert" style="display: none; padding: 2rem; background: #f8fafc; color: #64748b; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 2rem; text-align: center;">
    <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 1rem; display: block; color: #cbd5e1;"></i>
    <div style="font-weight: 800; font-size: 1.1rem;">No operations data found for this period.</div>
    <div id="suggest_range" style="margin-top: 0.5rem; font-weight: 600;">Upload "Returns" or "Reimbursements" reports to see analysis.</div>
</div>

<div id="main_content">
    <div class="kpi-grid" style="grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card kpi-card" style="background: #fff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 5px solid #f43f5e;">
            <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Total Returns</p>
            <h3 id="total_returns" style="font-size: 2rem; font-weight: 800; color: #1e293b;">0</h3>
        </div>
        <div class="card kpi-card" style="background: #fff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 5px solid #f59e0b;">
            <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Units Returned</p>
            <h3 id="return_units" style="font-size: 2rem; font-weight: 800; color: #1e293b;">0</h3>
        </div>
        <div class="card kpi-card" style="background: #fff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-left: 5px solid #10b981;">
            <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Total Reimbursed</p>
            <h3 id="total_reimbursed" style="font-size: 2rem; font-weight: 800; color: #1e293b;">$0.00</h3>
        </div>
    </div>

    <div style="display: block; margin-top: 2rem;">
        <div class="card" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; background: #fff;">
            <div style="background: linear-gradient(90deg, #f8fafc 0%, #ffffff 100%); padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: #1e293b; font-size: 1.1rem; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fas fa-undo-alt" style="color: #f43f5e; margin-right: 0.75rem;"></i> Recent Returns Analysis</span>
                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Real-time Ops Tracking</span>
            </div>
            <div style="padding: 1.5rem; overflow-x: auto;">
                <table class="table" id="returnsTable" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 1rem; border-bottom: 2px solid #edf2f7; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800;">Date</th>
                            <th style="padding: 1rem; border-bottom: 2px solid #edf2f7; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800;">Order ID</th>
                            <th style="padding: 1rem; border-bottom: 2px solid #edf2f7; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800;">Product / SKU</th>
                            <th style="padding: 1rem; border-bottom: 2px solid #edf2f7; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800;">Return Reason</th>
                            <th style="padding: 1rem; border-bottom: 2px solid #edf2f7; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 800; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="returns_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $.get('../../api/get_data_range.php', function(ranges) {
        if (ranges.ops && ranges.ops.min_date) {
            $('#date_suggestion').html(`<i class="fas fa-info-circle"></i> Operations data available from <b>${ranges.ops.min_date}</b> to <b>${ranges.ops.max_date}</b>`).show();
        }
    });

    loadOpsData();
});

function loadOpsData() {
    const customerId = $('#filter_customer').val();
    const fromDate = $('#filter_from').val();
    const toDate = $('#filter_to').val();

    $('#refresh_ops').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

    $.get('../../api/ops_data.php', {
        customer_id: customerId,
        from_date: fromDate,
        to_date: toDate
    }, function(data) {
        $('#refresh_ops').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> REFRESH');
        
        if (data.returns.length === 0 && data.reimbursements.length === 0) {
            $('#main_content').hide();
            $('#no_data_alert').show();
            return;
        }

        $('#no_data_alert').hide();
        $('#main_content').show();

        $('#total_returns').text(data.stats.total_returns || 0);
        $('#return_units').text(data.stats.return_units || 0);
        $('#total_reimbursed').text('$' + parseFloat(data.stats.total_reimbursed || 0).toLocaleString(undefined, {minimumFractionDigits: 2}));

        // Returns Table
        let returnsHtml = '';
        data.returns.forEach(r => {
            returnsHtml += `<tr style="transition: all 0.2s;">
                <td style="padding: 1.25rem 1rem; color: #64748b; font-weight: 600; font-size: 1rem;">${r.report_date}</td>
                <td style="padding: 1.25rem 1rem; font-weight: 800; color: #f43f5e; font-size: 1rem; font-family: monospace;">${r.order_id}</td>
                <td style="padding: 1.25rem 1rem;">
                    <div style="font-weight: 800; color: #1e293b; font-size: 1.1rem;">${r.sku}</div>
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;">${r.asin}</div>
                </td>
                <td style="padding: 1.25rem 1rem;">
                    <span style="font-size: 1rem; color: #475569; font-weight: 600; line-height: 1.4;">${r.reason}</span>
                </td>
                <td style="padding: 1.25rem 1rem; text-align: center;">
                    <span style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; border: 1px solid #e2e8f0;">${r.status}</span>
                </td>
            </tr>`;
        });
        $('#returns_body').html(returnsHtml || '<tr><td colspan="5" class="text-center" style="padding: 4rem; color: #94a3b8; font-weight: 600;">No recent returns found.</td></tr>');
        
        // Initialize/Re-initialize DataTable
        if ($.fn.DataTable.isDataTable('#returnsTable')) {
            $('#returnsTable').DataTable().destroy();
        }
        $('#returnsTable').DataTable({
            pageLength: 10,
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search returns..."
            }
        });
    });
}

$('#refresh_ops').click(loadOpsData);
</script>

<?php include '../../includes/footer.php'; ?>
