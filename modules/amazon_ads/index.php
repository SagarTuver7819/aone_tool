<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Advertising Intelligence";
$page_subtitle = "Sponsored Products, Brands & Display Analytics";

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
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Period</label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="date" id="filter_from" class="form-control" value="2026-01-01" style="padding: 0.6rem; border-radius: 8px;">
                <span style="color: #94a3b8;">to</span>
                <input type="date" id="filter_to" class="form-control" value="2026-03-31" style="padding: 0.6rem; border-radius: 8px;">
            </div>
        </div>
        <button id="refresh_ads" class="btn btn-primary" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-sync-alt"></i> REFRESH
        </button>
    </div>
</div>

<div class="kpi-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 1.5rem;">
    <!-- Main KPIs with Targets -->
    <div class="card kpi-card" style="border-radius: 16px; border: none; padding: 1.5rem; box-shadow: 0 10px 20px rgba(0,0,0,0.03); background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%); border-top: 4px solid #3b82f6; transition: transform 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <p style="color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin: 0;">Total Ad Spend</p>
            <i class="fas fa-wallet" style="color: #3b82f6; opacity: 0.5;"></i>
        </div>
        <h3 id="total_ad_spend" style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0;">$0.00</h3>
        <p style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.5rem; font-weight: 700;">Across SP, SB, SD</p>
    </div>
    <div class="card kpi-card" style="border-radius: 16px; border: none; padding: 1.5rem; box-shadow: 0 10px 20px rgba(0,0,0,0.03); background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-top: 4px solid #10b981; transition: transform 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <p style="color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin: 0;">Total Ad Sales</p>
            <i class="fas fa-chart-line" style="color: #10b981; opacity: 0.5;"></i>
        </div>
        <h3 id="total_ad_sales" style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0;">$0.00</h3>
        <p id="sales_status" style="font-size: 0.65rem; color: #10b981; margin-top: 0.5rem; font-weight: 700;">PPC Revenue Active</p>
    </div>
    <div class="card kpi-card" style="border-radius: 16px; border: none; padding: 1.5rem; box-shadow: 0 10px 20px rgba(0,0,0,0.03); background: linear-gradient(135deg, #ffffff 0%, #fff1f2 100%); border-top: 4px solid #f43f5e; transition: transform 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <p style="color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin: 0;">Average ACoS</p>
            <div id="acos_target_icon" style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8;"></div>
        </div>
        <h3 id="total_acos" style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0;">0.00%</h3>
        <p id="acos_target_label" style="font-size: 0.65rem; color: #94a3b8; margin-top: 0.5rem; font-weight: 700;">Target: 25.00%</p>
    </div>
    <div class="card kpi-card" style="border-radius: 16px; border: none; padding: 1.5rem; box-shadow: 0 10px 20px rgba(0,0,0,0.03); background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%); border-top: 4px solid #8b5cf6; transition: transform 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <p style="color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin: 0;">Overall ROAS</p>
            <i class="fas fa-rocket" style="color: #8b5cf6; opacity: 0.5;"></i>
        </div>
        <h3 id="total_roas" style="font-size: 1.75rem; font-weight: 900; color: #1e293b; margin: 0;">0.00</h3>
        <p style="font-size: 0.65rem; color: #8b5cf6; margin-top: 0.5rem; font-weight: 700;">Efficiency Index</p>
    </div>
</div>

<div class="kpi-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card kpi-card" style="border-radius: 12px; border: none; padding: 1rem 1.25rem; box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white; border-bottom: 3px solid #f59e0b;">
        <p style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">Total Orders</p>
        <h4 id="total_orders" style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;">0</h4>
    </div>
    <div class="card kpi-card" style="border-radius: 12px; border: none; padding: 1rem 1.25rem; box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white; border-bottom: 3px solid #06b6d4;">
        <p style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">Global CTR</p>
        <h4 id="total_ctr" style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;">0.00%</h4>
    </div>
    <div class="card kpi-card" style="border-radius: 12px; border: none; padding: 1rem 1.25rem; box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white; border-bottom: 3px solid #ec4899;">
        <p style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">Avg. CPC</p>
        <h4 id="total_cpc" style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;">$0.00</h4>
    </div>
    <div class="card kpi-card" style="border-radius: 12px; border: none; padding: 1rem 1.25rem; box-shadow: 0 4px 10px rgba(0,0,0,0.02); background: white; border-bottom: 3px solid #64748b;">
        <p style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">Conv. Rate (CvR)</p>
        <h4 id="total_cvr" style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;">0.00%</h4>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2.5rem;">
    <div class="card" style="padding: 1.25rem; border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.08); background: white; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fas fa-search-dollar"></i></div>
        <div style="flex: 1;">
            <p style="font-size: 0.65rem; color: #64748b; font-weight: 800; text-transform: uppercase; margin: 0;">Sponsored Products</p>
            <h4 id="sp_spend" style="margin: 0; font-weight: 900; color: #1e293b;">$0.00</h4>
        </div>
        <div style="text-align: right;"><span id="sp_pct" style="font-size: 0.75rem; font-weight: 800; color: #3b82f6;">0%</span></div>
    </div>
    <div class="card" style="padding: 1.25rem; border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.08); background: white; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #faf5ff; color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fas fa-ad"></i></div>
        <div style="flex: 1;">
            <p style="font-size: 0.65rem; color: #64748b; font-weight: 800; text-transform: uppercase; margin: 0;">Sponsored Brands</p>
            <h4 id="sb_spend" style="margin: 0; font-weight: 900; color: #1e293b;">$0.00</h4>
        </div>
        <div style="text-align: right;"><span id="sb_pct" style="font-size: 0.75rem; font-weight: 800; color: #a855f7;">0%</span></div>
    </div>
    <div class="card" style="padding: 1.25rem; border-radius: 16px; border: none; box-shadow: 0 4px 15px rgba(244, 63, 94, 0.08); background: white; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fff1f2; color: #f43f5e; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fas fa-tv"></i></div>
        <div style="flex: 1;">
            <p style="font-size: 0.65rem; color: #64748b; font-weight: 800; text-transform: uppercase; margin: 0;">Sponsored Display</p>
            <h4 id="sd_spend" style="margin: 0; font-weight: 900; color: #1e293b;">$0.00</h4>
        </div>
        <div style="text-align: right;"><span id="sd_pct" style="font-size: 0.75rem; font-weight: 800; color: #f43f5e;">0%</span></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-chart-area" style="color: #6366f1; margin-right: 0.5rem;"></i> Spend vs Sales Trend
        </div>
        <div style="padding: 1rem; height: 350px;"><canvas id="adTrendChart"></canvas></div>
    </div>
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
            <i class="fas fa-pie-chart" style="color: #8b5cf6; margin-right: 0.5rem;"></i> Ad Type Distribution
        </div>
        <div style="padding: 1rem; height: 350px;"><canvas id="adTypeChart"></canvas></div>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 2rem; margin-bottom: 2rem;">
    <!-- Large Placement Performance Table -->
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: #1e293b; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fas fa-bullseye" style="color: #10b981; margin-right: 0.5rem;"></i> Placement Efficiency</span>
            <span class="badge bg-light text-muted fw-800" style="font-size: 0.6rem;">SP / SB DATA</span>
        </div>
        <div style="padding: 1rem;">
            <table class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="small fw-800 text-muted text-uppercase">Placement</th>
                        <th class="text-end small fw-800 text-muted text-uppercase">Spend (Share %)</th>
                        <th class="text-center small fw-800 text-muted text-uppercase">ACoS</th>
                        <th class="text-center small fw-800 text-muted text-uppercase">ROAS</th>
                        <th class="text-center small fw-800 text-muted text-uppercase">CTR</th>
                    </tr>
                </thead>
                <tbody id="placement_body" style="border-top: none;"></tbody>
            </table>
        </div>
    </div>

    <!-- Bidding Strategy -->
    <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
        <div style="background: #ffffff; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; font-weight: 800; color: #1e293b; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fas fa-gavel" style="color: #f59e0b; margin-right: 0.5rem;"></i> Bidding Strategy Efficiency</span>
            <span class="badge" style="background: #fffbeb; color: #b45309; font-size: 0.6rem; font-weight: 800;">ALGORITHMIC VIEW</span>
        </div>
        <div style="padding: 1rem;">
            <table class="table table-hover align-middle mb-0" style="width: 100%; font-size: 0.85rem;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="small fw-800 text-muted text-uppercase">Strategy</th>
                        <th class="text-end small fw-800 text-muted text-uppercase">Spend</th>
                        <th class="text-end small fw-800 text-muted text-uppercase">Sales</th>
                        <th class="text-center small fw-800 text-muted text-uppercase">ROAS</th>
                    </tr>
                </thead>
                <tbody id="bidding_body" style="border-top: none;"></tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let adTrendChart = null;
    let adTypeChart = null;

    $.get('../../api/get_data_range.php', function(ranges) {
        if (ranges.ads && ranges.ads.min_date) {
            $('#filter_from').val(ranges.ads.min_date);
            $('#filter_to').val(ranges.ads.max_date);
            loadAdData();
        }
    });

    function formatCurrency(v) {
        return '$' + parseFloat(v || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    function loadAdData() {
        const customerId = $('#filter_customer').val();
        const fromDate = $('#filter_from').val();
        const toDate = $('#filter_to').val();

        if (!fromDate || !toDate) return;

        $('#refresh_ads').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

        $.get('../../api/advertising_data.php', {
            customer_id: customerId,
            from_date: fromDate,
            to_date: toDate
        }, function(data) {
            $('#refresh_ads').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> REFRESH');
            $('#total_ad_spend').text(formatCurrency(data.summary.total_spend));
            $('#total_ad_sales').text(formatCurrency(data.summary.total_sales));
            $('#total_orders').text(parseInt(data.summary.total_orders).toLocaleString());
            
            $('#sp_spend').text(formatCurrency(data.summary.sp.spend));
            $('#sb_spend').text(formatCurrency(data.summary.sb.spend));
            $('#sd_spend').text(formatCurrency(data.summary.sd.spend));
            
            const totalSpend = data.summary.total_spend;
            if (totalSpend > 0) {
                $('#sp_pct').text(Math.round((data.summary.sp.spend / totalSpend) * 100) + '%');
                $('#sb_pct').text(Math.round((data.summary.sb.spend / totalSpend) * 100) + '%');
                $('#sd_pct').text(Math.round((data.summary.sd.spend / totalSpend) * 100) + '%');
            }

            const totalAcos = data.summary.total_sales > 0 ? (data.summary.total_spend / data.summary.total_sales) * 100 : 0;
            const totalRoas = data.summary.total_spend > 0 ? (data.summary.total_sales / data.summary.total_spend) : 0;
            
            const totalImpressions = (parseInt(data.summary.sp.impressions) || 0) + (parseInt(data.summary.sb.impressions) || 0) + (parseInt(data.summary.sd.impressions) || 0);
            const totalClicks = (parseInt(data.summary.sp.clicks) || 0) + (parseInt(data.summary.sb.clicks) || 0) + (parseInt(data.summary.sd.clicks) || 0);
            
            const totalCtr = totalImpressions > 0 ? (totalClicks / totalImpressions) * 100 : 0;
            const totalCpc = totalClicks > 0 ? data.summary.total_spend / totalClicks : 0;
            const totalCvr = totalClicks > 0 ? (data.summary.total_orders / totalClicks) * 100 : 0;

            $('#total_acos').text(totalAcos.toFixed(2) + '%');
            $('#total_roas').text(totalRoas.toFixed(2));
            $('#total_ctr').text(totalCtr.toFixed(2) + '%');
            $('#total_cpc').text(formatCurrency(totalCpc));
            $('#total_cvr').text(totalCvr.toFixed(2) + '%');

            // Color Coding ACoS
            if (totalAcos > 0) {
                if (totalAcos < 25) {
                    $('#acos_target_icon').css('background', '#10b981');
                    $('#acos_target_label').text('Excellent Health').css('color', '#10b981');
                    $('#total_acos').css('color', '#10b981');
                } else if (totalAcos < 35) {
                    $('#acos_target_icon').css('background', '#f59e0b');
                    $('#acos_target_label').text('Monitor Closely').css('color', '#f59e0b');
                    $('#total_acos').css('color', '#f59e0b');
                } else {
                    $('#acos_target_icon').css('background', '#f43f5e');
                    $('#acos_target_label').text('High Spend Warning').css('color', '#f43f5e');
                    $('#total_acos').css('color', '#f43f5e');
                }
            } else {
                $('#acos_target_icon').css('background', '#94a3b8');
                $('#acos_target_label').text('Target: 25.00%').css('color', '#94a3b8');
                $('#total_acos').css('color', '#1e293b');
            }

            // Trend Chart
            if (adTrendChart) adTrendChart.destroy();
            const ctx = document.getElementById('adTrendChart').getContext('2d');
            
            const spendGradient = ctx.createLinearGradient(0, 0, 0, 400);
            spendGradient.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
            spendGradient.addColorStop(1, 'rgba(239, 68, 68, 0)');

            const salesGradient = ctx.createLinearGradient(0, 0, 0, 400);
            salesGradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            salesGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            adTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.daily_trend.labels,
                    datasets: [
                        {
                            label: 'Ad Spend',
                            data: data.daily_trend.spend,
                            borderColor: '#ef4444',
                            backgroundColor: spendGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Ad Sales',
                            data: data.daily_trend.sales,
                            borderColor: '#10b981',
                            backgroundColor: salesGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6, font: { weight: '800', size: 11 } } }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { display: true, color: '#f1f5f9' }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    },
                    interaction: { mode: 'index', intersect: false }
                }
            });

            // Type Distribution
            if (adTypeChart) adTypeChart.destroy();
            const ctx2 = document.getElementById('adTypeChart').getContext('2d');
            adTypeChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['SP', 'SB', 'SD'],
                    datasets: [{
                        data: [data.summary.sp.spend, data.summary.sb.spend, data.summary.sd.spend],
                        backgroundColor: ['#3b82f6', '#a855f7', '#f43f5e']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Placements Table
            let p_html = '';
            const totalPlacementSpend = data.placements.reduce((acc, p) => acc + parseFloat(p.spend), 0);
            data.placements.forEach(p => {
                const acos = p.sales > 0 ? (p.spend / p.sales * 100).toFixed(2) : '0.00';
                const roas = p.spend > 0 ? (p.sales / p.spend).toFixed(2) : '0.00';
                const ctr = p.impressions > 0 ? (p.clicks / p.impressions * 100).toFixed(2) : '0.00';
                const spendShare = totalPlacementSpend > 0 ? (p.spend / totalPlacementSpend * 100).toFixed(1) : '0.0';
                
                p_html += `<tr>
                    <td style="font-weight: 800; color: #1e293b;">${p.placement}</td>
                    <td style="text-align: right; font-weight: 700;">${formatCurrency(p.spend)} <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 600;">(${spendShare}%)</span></td>
                    <td style="text-align: center; font-weight: 800; color: ${acos > 35 ? '#ef4444' : '#10b981'};">${acos}%</td>
                    <td style="text-align: center; font-weight: 800; color: #3b82f6;">${roas}</td>
                    <td style="text-align: center; color: #64748b;">${ctr}%</td>
                </tr>`;
            });
            $('#placement_body').html(p_html || '<tr><td colspan="5" class="text-center" style="padding: 2rem; color: #94a3b8;">No placement data found.</td></tr>');

            // Bidding Strategy
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
            $('#bidding_body').html(b_html || '<tr><td colspan="4" class="text-center" style="padding: 2rem; color: #94a3b8;">No strategy data found.</td></tr>');
        });
    }

    $('#refresh_ads').click(loadAdData);
});
</script>

<style>
.badge-sp { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; }
.badge-sb { background: #faf5ff; color: #a855f7; border: 1px solid #e9d5ff; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; }
.badge-sd { background: #fff1f2; color: #f43f5e; border: 1px solid #fecdd3; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; }
</style>

<?php include '../../includes/footer.php'; ?>
