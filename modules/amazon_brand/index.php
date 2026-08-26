<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Brand Intelligence";
$page_subtitle = "Search Query Performance & Repeat Purchase Analytics";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card"
    style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 16px;">
    <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 250px;">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Amazon
                Profile</label>
            <select id="filter_customer"
                style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                <option value="">All Amazon Profiles</option>
                <?php
                $customers = get_all_customers();
                while ($row = $customers->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['customer_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Analysis
                Period</label>
            <div class="figma-date-picker-wrap">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.6666 1.3335V4.00016M5.33325 1.3335V4.00016" stroke="#363B4F" stroke-width="1.4"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M8.66667 2.6665H7.33333C4.81917 2.6665 3.5621 2.6665 2.78105 3.44755C2 4.2286 2 5.48568 2 7.99984V9.33317C2 11.8473 2 13.1044 2.78105 13.8854C3.5621 14.6665 4.81917 14.6665 7.33333 14.6665H8.66667C11.1808 14.6665 12.4379 14.6665 13.2189 13.8854C14 13.1044 14 11.8473 14 9.33317V7.99984C14 5.48568 14 4.2286 13.2189 3.44755C12.4379 2.6665 11.1808 2.6665 8.66667 2.6665Z"
                        stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2 6.6665H14" stroke="#363B4F" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <input type="text" class="flatpickr-range-input date-range-picker" id="date_range_picker_brand"
                    placeholder="Select date range" readonly>
                <input type="hidden" id="filter_from" value="<?php echo date('Y-m-01'); ?>">
                <input type="hidden" id="filter_to" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        <button id="refresh_brand" class="btn-figma-refresh" title="Refresh">
            <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M10.1115 0.666504L10.5101 1.41169C10.7796 1.91548 10.9143 2.16738 10.8253 2.27526C10.7361 2.38314 10.4427 2.29601 9.85573 2.12176C9.26893 1.94754 8.64593 1.85381 8.00033 1.85381C4.50252 1.85381 1.66699 4.60548 1.66699 7.99987C1.66699 9.11927 1.97541 10.1689 2.51428 11.0729M5.88921 15.3332L5.49057 14.588C5.22105 14.0842 5.08629 13.8323 5.17539 13.7244C5.26451 13.6165 5.55799 13.7037 6.14492 13.8779C6.73173 14.0521 7.35473 14.1459 8.00033 14.1459C11.4981 14.1459 14.3337 11.3942 14.3337 7.99987C14.3337 6.8804 14.0253 5.83082 13.4864 4.92682"
                    stroke="#363B4F" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </div>
    <div id="date_suggestion" style="margin-top: 1rem; font-size: 0.8rem; display: none;"></div>
</div>

<div id="no_data_alert"
    style="display: none; padding: 2rem; background: #fff1f2; color: #e11d48; border-radius: 16px; border: 1px solid #fecdd3; margin-bottom: 2rem; text-align: center;">
    <i class="fas fa-search-minus" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
    <div style="font-weight: 800; font-size: 1.1rem;">No brand report data found for this period.</div>
    <div id="suggest_range" style="margin-top: 0.5rem; font-weight: 600;">Please upload "Search Query Performance" or
        "Repeat Purchase" reports.</div>
</div>

<div id="main_content">
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="card"
            style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
            <div
                style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
                <i class="fas fa-chart-line" style="color: #0ea5e9; margin-right: 0.5rem;"></i> Brand Share Trend
                (Market vs You)
            </div>
            <div style="padding: 1.5rem; height: 350px;"><canvas id="brandTrendChart"></canvas></div>
        </div>
        <div class="card"
            style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden; background: linear-gradient(135deg, #0ea5e9 0%, #0c4a6e 100%); color: white;">
            <div style="padding: 1.5rem;">
                <p style="font-size: 0.8rem; font-weight: 700; opacity: 0.8; text-transform: uppercase;">Avg. Brand
                    Share</p>
                <h2 id="avg_brand_share" style="font-size: 3rem; font-weight: 900; margin: 0;">0.0%</h2>
                <div style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                    <p style="font-size: 0.8rem; line-height: 1.6;">This metric represents your average click/purchase
                        share across all identified search queries in the period.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card"
        style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 2rem;">
        <div
            style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-search" style="color: #0ea5e9; margin-right: 0.5rem;"></i> Search Query Performance (Brand
            vs Market)
        </div>
        <div style="padding: 1rem; overflow-x: auto;">
            <table class="table" id="sqpTable" style="width: 100%;">
                <thead style="background: #f1f5f9;">
                    <tr>
                        <th>Search Query</th>
                        <th style="text-align: center;">Impressions (B/M)</th>
                        <th style="text-align: center;">Clicks (B/M)</th>
                        <th style="text-align: center;">Add to Cart (B/M)</th>
                        <th style="text-align: center;">Purchases (B/M)</th>
                        <th style="text-align: center;">Brand Share</th>
                    </tr>
                </thead>
                <tbody id="sqp_body"></tbody>
            </table>
        </div>
    </div>

    <div class="card"
        style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div
            style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-redo" style="color: #0ea5e9; margin-right: 0.5rem;"></i> Repeat Purchase Behavior
        </div>
        <div style="padding: 1rem; overflow-x: auto;">
            <table class="table" id="repeatTable" style="width: 100%;">
                <thead style="background: #f1f5f9;">
                    <tr>
                        <th>ASIN / SKU</th>
                        <th style="text-align: right;">Total Orders</th>
                        <th style="text-align: right;">Repeat Customers</th>
                        <th style="text-align: right;">Repeat Sales</th>
                        <th style="text-align: center;">Repeat %</th>
                    </tr>
                </thead>
                <tbody id="repeat_body"></tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let brandTrendChart;

    $(document).ready(function () {
        $.get('../../api/get_data_range.php', function (ranges) {
            if (ranges.brand && ranges.brand.min_date) {
                $('#date_suggestion').html(`<i class="fas fa-info-circle"></i> Brand data available from <b>${ranges.brand.min_date}</b> to <b>${ranges.brand.max_date}</b>`).show();
            }
        });

        loadBrandData();
    });

    function loadBrandData() {
        const customerId = $('#filter_customer').val();
        const fromDate = $('#filter_from').val();
        const toDate = $('#filter_to').val();

        $('#refresh_brand').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

        $.get('../../api/brand_data.php', {
            customer_id: customerId,
            from_date: fromDate,
            to_date: toDate
        }, function (data) {
            $('#refresh_brand').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> REFRESH');

            if (data.search_queries.length === 0 && data.repeat_purchases.length === 0) {
                $('#main_content').hide();
                $('#no_data_alert').show();
                return;
            }

            $('#no_data_alert').hide();
            $('#main_content').show();

            // Trend Chart
            if (brandTrendChart) brandTrendChart.destroy();
            const ctx = document.getElementById('brandTrendChart').getContext('2d');
            brandTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.trends.map(t => t.month),
                    datasets: [{
                        label: 'Avg Brand Share %',
                        data: data.trends.map(t => t.avg_share),
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => v + '%' } }
                    }
                }
            });

            // Avg KPI
            const avgShare = data.trends.length > 0 ? (data.trends.reduce((a, b) => a + parseFloat(b.avg_share), 0) / data.trends.length).toFixed(1) : 0;
            $('#avg_brand_share').text(avgShare + '%');

            // SQP Table
            let sqpHtml = '';
            data.search_queries.forEach(row => {
                sqpHtml += `<tr>
                <td style="font-weight: 700; color: #1e293b;">${row['searchquery'] || row['search_query'] || 'N/A'}</td>
                <td style="text-align: center;">${row['brandimpressions'] || 0} / ${row['marketimpressions'] || 0}</td>
                <td style="text-align: center;">${row['brandclicks'] || 0} / ${row['marketclicks'] || 0}</td>
                <td style="text-align: center;">${row['brandaddtocarts'] || 0} / ${row['marketaddtocarts'] || 0}</td>
                <td style="text-align: center;">${row['brandpurchases'] || 0} / ${row['marketpurchases'] || 0}</td>
                <td style="text-align: center; font-weight: 800; color: #0ea5e9;">${row['brandshare'] || 0}%</td>
            </tr>`;
            });
            $('#sqp_body').html(sqpHtml || '<tr><td colspan="6" class="text-center">No search query data</td></tr>');

            // Repeat Purchase Table
            let rpHtml = '';
            data.repeat_purchases.forEach(row => {
                rpHtml += `<tr>
                <td><div style="font-weight: 700;">${row['asin'] || 'N/A'}</div><div style="font-size: 0.7rem; color: #94a3b8;">${row['sku'] || ''}</div></td>
                <td style="text-align: right;">${row['totalorders'] || 0}</td>
                <td style="text-align: right;">${row['repeatcustomers'] || 0}</td>
                <td style="text-align: right;">$${parseFloat(row['repeatsales'] || 0).toFixed(2)}</td>
                <td style="text-align: center; font-weight: 800; color: #10b981;">${row['repeatpurchasepct'] || 0}%</td>
            </tr>`;
            });
            $('#repeat_body').html(rpHtml || '<tr><td colspan="5" class="text-center">No repeat purchase data</td></tr>');
        });
    }

    if (typeof flatpickr !== 'undefined') {
        flatpickr("#date_range_picker_brand", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "M d, Y",
            defaultDate: [$('#filter_from').val() || "<?php echo date('Y-m-01'); ?>", $('#filter_to').val() || "<?php echo date('Y-m-d'); ?>"],
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const from = instance.formatDate(selectedDates[0], "Y-m-d");
                    const to = instance.formatDate(selectedDates[1], "Y-m-d");
                    $('#filter_from').val(from);
                    $('#filter_to').val(to);
                    loadBrandData();
                }
            }
        });
    }

    $('#refresh_brand').click(loadBrandData);
    $('#filter_customer').change(loadBrandData);
    loadBrandData();
});
</script>

<?php include '../../includes/footer.php'; ?>