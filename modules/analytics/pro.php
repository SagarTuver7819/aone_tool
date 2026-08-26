<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Advanced P&L Analytics";
$page_subtitle = "SKU-Level Profitability & Expense Automation";

include '../../includes/header.php';
include '../../includes/sidebar.php';

$customer_id = $_SESSION['customer_id'] ?? 0;
?>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
    <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 250px;">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Amazon Profile</label>
            <select id="filter_customer" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                <?php 
                $customers = get_all_customers();
                while ($row = $customers->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo $customer_id == $row['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['customer_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Analysis Period</label>
            <div class="figma-date-picker-wrap">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.6666 1.3335V4.00016M5.33325 1.3335V4.00016" stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8.66667 2.6665H7.33333C4.81917 2.6665 3.5621 2.6665 2.78105 3.44755C2 4.2286 2 5.48568 2 7.99984V9.33317C2 11.8473 2 13.1044 2.78105 13.8854C3.5621 14.6665 4.81917 14.6665 7.33333 14.6665H8.66667C11.1808 14.6665 12.4379 14.6665 13.2189 13.8854C14 13.1044 14 11.8473 14 9.33317V7.99984C14 5.48568 14 4.2286 13.2189 3.44755C12.4379 2.6665 11.1808 2.6665 8.66667 2.6665Z" stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2 6.6665H14" stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <input type="text" class="flatpickr-range-input date-range-picker" id="date_range_picker_pro" placeholder="Select date range" readonly>
                <input type="hidden" id="filter_from" value="<?php echo date('Y-m-01'); ?>">
                <input type="hidden" id="filter_to" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button id="refresh_data" class="btn-figma-refresh" title="Analyze">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.1115 0.666504L10.5101 1.41169C10.7796 1.91548 10.9143 2.16738 10.8253 2.27526C10.7361 2.38314 10.4427 2.29601 9.85573 2.12176C9.26893 1.94754 8.64593 1.85381 8.00033 1.85381C4.50252 1.85381 1.66699 4.60548 1.66699 7.99987C1.66699 9.11927 1.97541 10.1689 2.51428 11.0729M5.88921 15.3332L5.49057 14.588C5.22105 14.0842 5.08629 13.8323 5.17539 13.7244C5.26451 13.6165 5.55799 13.7037 6.14492 13.8779C6.73173 14.0521 7.35473 14.1459 8.00033 14.1459C11.4981 14.1459 14.3337 11.3942 14.3337 7.99987C14.3337 6.8804 14.0253 5.83082 13.4864 4.92682" stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <a href="manage_config.php" class="btn btn-outline-primary" style="padding: 0.5rem 1.25rem; border-radius: 10px; font-weight: 600; border: 1.5px solid #4362CE; color: #4362CE; font-size: 0.82rem; height: 38px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fas fa-cog"></i> SETUP RULES
            </a>
        </div>
    </div>
    <div id="date_suggestion" style="margin-top: 1rem; font-size: 0.8rem; display: none;"></div>
</div>

<div id="no_data_alert" style="display: none; padding: 2rem; background: #fff1f2; color: #e11d48; border-radius: 16px; border: 1px solid #fecdd3; margin-bottom: 2rem; text-align: center;">
    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
    <div style="font-weight: 800; font-size: 1.1rem;">No transaction data found for this period in Advanced Analytics.</div>
    <div id="suggest_range" style="margin-top: 0.5rem; font-weight: 600;"></div>
</div>

<div id="main_content">
    <div class="kpi-grid" style="grid-template-columns: repeat(5, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card kpi-card" style="background: #ffffff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Total Revenue</p>
        <h3 id="stat_sales" style="font-size: 1.75rem; font-weight: 800; color: #1e293b;">$0.00</h3>
        <div style="height: 4px; width: 40px; background: #3b82f6; border-radius: 2px; margin-top: 0.5rem;"></div>
    </div>
    <div class="card kpi-card" style="background: #ffffff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Amazon Fees</p>
        <h3 id="stat_fees" style="font-size: 1.75rem; font-weight: 800; color: #f43f5e;">$0.00</h3>
        <div style="height: 4px; width: 40px; background: #f43f5e; border-radius: 2px; margin-top: 0.5rem;"></div>
    </div>
    <div class="card kpi-card" style="background: #ffffff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Total COGS</p>
        <h3 id="stat_cogs" style="font-size: 1.75rem; font-weight: 800; color: #f59e0b;">$0.00</h3>
        <div style="height: 4px; width: 40px; background: #f59e0b; border-radius: 2px; margin-top: 0.5rem;"></div>
    </div>
    <div class="card kpi-card" style="background: #ffffff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Net Profit</p>
        <h3 id="stat_profit" style="font-size: 1.75rem; font-weight: 800; color: #10b981;">$0.00</h3>
        <div style="height: 4px; width: 40px; background: #10b981; border-radius: 2px; margin-top: 0.5rem;"></div>
    </div>
    <div class="card kpi-card" style="background: #ffffff; border: none; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
        <p style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">ROI / Margin</p>
        <h3 id="stat_margin" style="font-size: 1.75rem; font-weight: 800; color: #8b5cf6;">0% / 0%</h3>
        <div style="height: 4px; width: 40px; background: #8b5cf6; border-radius: 2px; margin-top: 0.5rem;"></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-chart-pie" style="color: #3b82f6; margin-right: 0.5rem;"></i> Expense Breakdown
        </div>
        <div style="padding: 1.5rem; height: 300px;">
            <canvas id="expenseChart"></canvas>
        </div>
    </div>
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-list-alt" style="color: #3b82f6; margin-right: 0.5rem;"></i> Top Expenses (Rules)
        </div>
        <div id="rules_breakdown" style="padding: 1.5rem; height: 300px; overflow-y: auto;">
            <!-- Dynamic Rules List -->
        </div>
    </div>
</div>

<div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
    <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155; display: flex; justify-content: space-between; align-items: center;">
        <div><i class="fas fa-boxes" style="color: #3b82f6; margin-right: 0.5rem;"></i> SKU Performance (Advanced)</div>
        <div style="font-size: 0.8rem; font-weight: 400; color: #64748b;">Includes Historical COGS & Custom Shipping Rules</div>
    </div>
    <div style="padding: 1rem;">
        <table class="table" id="proSkuTable" style="width: 100%;">
            <thead style="background: #f1f5f9;">
                <tr>
                    <th>SKU</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Sales</th>
                    <th style="text-align: right;">Amazon Fees</th>
                    <th style="text-align: right;">COGS (Avg)</th>
                    <th style="text-align: right;">Shipping</th>
                    <th style="text-align: right;">Net Profit</th>
                    <th style="text-align: center;">ROI</th>
                </tr>
            </thead>
            <tbody id="sku_body"></tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let expenseChart;

    // Check available ranges
    $.get('../../api/get_data_range.php', function(ranges) {
        if (ranges.trans && ranges.trans.min_date) {
            const min = new Date(ranges.trans.min_date);
            const max = new Date(ranges.trans.max_date);
            $('#date_suggestion').html(`<i class="fas fa-info-circle"></i> Transaction data exists from <b>${ranges.trans.min_date}</b> to <b>${ranges.trans.max_date}</b>`).show();
            $('#suggest_range').text(`Try selecting a range around ${ranges.trans.max_date}`);
        }
    });

    function formatCurrency(v) {
        return '$' + parseFloat(v).toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    function loadProData() {
        const customerId = $('#filter_customer').val();
        const fromDate = $('#filter_from').val();
        const toDate = $('#filter_to').val();

        $('#refresh_data').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> CALCULATING...');

        $.get('../../api/pl_pro_analytics.php', {
            customer_id: customerId,
            from_date: fromDate,
            to_date: toDate
        }, function(res) {
            $('#refresh_data').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> ANALYZE');
            
            if (!res.summary || (res.summary.sales == 0 && res.sku_breakdown.length == 0)) {
                $('#main_content').hide();
                $('#no_data_alert').show();
                return;
            }

            $('#no_data_alert').hide();
            $('#main_content').show();

            const s = res.summary;
            $('#stat_sales').text(formatCurrency(s.sales));
            $('#stat_fees').text(formatCurrency(Math.abs(s.amazon_fees)));
            $('#stat_cogs').text(formatCurrency(s.cogs));
            $('#stat_profit').text(formatCurrency(s.net_profit));
            $('#stat_margin').text(s.roi.toFixed(1) + '% / ' + s.margin.toFixed(1) + '%');

            // Chart
            if (expenseChart) expenseChart.destroy();
            const ctx = document.getElementById('expenseChart').getContext('2d');
            expenseChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Amazon Fees', 'COGS', 'Shipping', 'Other Fixed', 'Rules'],
                    datasets: [{
                        data: [Math.abs(s.amazon_fees), s.cogs, s.shipping, s.other_fixed, s.variable_rules],
                        backgroundColor: ['#f43f5e', '#f59e0b', '#3b82f6', '#94a3b8', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: { maintainAspectRatio: false, cutout: '70%' }
            });

            // SKU Table
            let html = '';
            res.sku_breakdown.forEach(row => {
                const roi = row.cogs > 0 ? (row.net - row.cogs - row.shipping - row.other_fixed) / row.cogs * 100 : 0;
                const net = row.net - row.cogs - row.shipping - row.other_fixed;
                html += `<tr>
                    <td style="font-weight: 700; color: #334155;">${row.sku}</td>
                    <td style="text-align: center;">${row.qty}</td>
                    <td style="text-align: right;">${formatCurrency(row.sales)}</td>
                    <td style="text-align: right; color: #ef4444;">${formatCurrency(row.amazon_fees)}</td>
                    <td style="text-align: right;">${formatCurrency(row.cogs)}</td>
                    <td style="text-align: right;">${formatCurrency(row.shipping)}</td>
                    <td style="text-align: right; font-weight: 800; color: ${net >= 0 ? '#10b981' : '#ef4444'};">${formatCurrency(net)}</td>
                    <td style="text-align: center;"><span class="badge ${roi >= 20 ? 'bg-success' : (roi >= 0 ? 'bg-warning' : 'bg-danger')}">${roi.toFixed(1)}%</span></td>
                </tr>`;
            });
            $('#sku_body').html(html);

        }).fail(function(e) {
            alert("Error loading data: " + (e.responseJSON ? e.responseJSON.error : 'Unknown error'));
            $('#refresh_data').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> ANALYZE');
        });
    }

    if (typeof flatpickr !== 'undefined') {
        flatpickr("#date_range_picker_pro", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "M d, Y",
            defaultDate: [$('#filter_from').val() || "<?php echo date('Y-m-01'); ?>", $('#filter_to').val() || "<?php echo date('Y-m-d'); ?>"],
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const from = instance.formatDate(selectedDates[0], "Y-m-d");
                    const to = instance.formatDate(selectedDates[1], "Y-m-d");
                    $('#filter_from').val(from);
                    $('#filter_to').val(to);
                    loadProData();
                }
            }
        });
    }

    $('#refresh_data').click(loadProData);
    $('#filter_customer').change(loadProData);
    loadProData();
});
</script>

<?php include '../../includes/footer.php'; ?>
