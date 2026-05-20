<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Brand Intelligence";
$page_subtitle = "Search Query Performance & Repeat Purchase Analytics";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 16px;">
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
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Analysis Period</label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="date" id="filter_from" class="form-control" value="<?php echo date('Y-m-01'); ?>" style="padding: 0.6rem; border-radius: 8px;">
                <span style="color: #94a3b8;">to</span>
                <input type="date" id="filter_to" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="padding: 0.6rem; border-radius: 8px;">
            </div>
        </div>
        <button id="refresh_brand" class="btn btn-primary" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-sync-alt"></i> REFRESH
        </button>
    </div>
    <div id="date_suggestion" style="margin-top: 1rem; font-size: 0.8rem; display: none;"></div>
</div>

<div id="no_data_alert" style="display: none; padding: 2rem; background: #fff1f2; color: #e11d48; border-radius: 16px; border: 1px solid #fecdd3; margin-bottom: 2rem; text-align: center;">
    <i class="fas fa-search-minus" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
    <div style="font-weight: 800; font-size: 1.1rem;">No brand report data found for this period.</div>
    <div id="suggest_range" style="margin-top: 0.5rem; font-weight: 600;">Please upload "Search Query Performance" or "Repeat Purchase" reports.</div>
</div>

<div id="main_content">
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
                <i class="fas fa-chart-line" style="color: #0ea5e9; margin-right: 0.5rem;"></i> Brand Share Trend (Market vs You)
            </div>
            <div style="padding: 1.5rem; height: 350px;"><canvas id="brandTrendChart"></canvas></div>
        </div>
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden; background: linear-gradient(135deg, #0ea5e9 0%, #0c4a6e 100%); color: white;">
            <div style="padding: 1.5rem;">
                <p style="font-size: 0.8rem; font-weight: 700; opacity: 0.8; text-transform: uppercase;">Avg. Brand Share</p>
                <h2 id="avg_brand_share" style="font-size: 3rem; font-weight: 900; margin: 0;">0.0%</h2>
                <div style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                    <p style="font-size: 0.8rem; line-height: 1.6;">This metric represents your average click/purchase share across all identified search queries in the period.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 2rem;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-search" style="color: #0ea5e9; margin-right: 0.5rem;"></i> Search Query Performance (Brand vs Market)
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

    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
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

$(document).ready(function() {
    $.get('../../api/get_data_range.php', function(ranges) {
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
    }, function(data) {
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

$('#refresh_brand').click(loadBrandData);
</script>

<?php include '../../includes/footer.php'; ?>
