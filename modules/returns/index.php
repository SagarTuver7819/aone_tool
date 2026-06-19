<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = 'Return Page';
$page_subtitle = 'Returns intelligence, reasons & product performance';

$customers = get_all_customers();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
.returns-page .kpi-grid-6 {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.returns-page .kpi-card {
    background: #ffffff;
    border: 1px solid #e7e8e9;
    border-radius: 16px;
    padding: 1.25rem !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    min-height: 130px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.returns-page .kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}
.returns-page .kpi-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}
.returns-page .kpi-label {
    font-size: 0.7rem;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.returns-page .kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    background: #f1f5f9;
    color: #475569;
}
.returns-page .kpi-value {
    font-family: 'Hanken Grotesk', sans-serif;
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -0.02em;
}
.returns-page .kpi-sub {
    font-size: 0.75rem;
    font-weight: 700;
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}
.returns-page .kpi-sub.up { color: #10b981; }
.returns-page .kpi-sub.down { color: #ef4444; }
.returns-page .kpi-sub.neutral { color: #64748b; }
.returns-page .kpi-sub.critical { color: #ef4444; }

.returns-charts-row {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.returns-card {
    background: #ffffff;
    border: 1px solid #e7e8e9;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
}
.returns-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.returns-card-title {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.01em;
}
.returns-trend-toggle {
    display: flex;
    gap: 0.35rem;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 8px;
}
.returns-trend-btn {
    border: none;
    background: transparent;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 800;
    color: #64748b;
    cursor: pointer;
    transition: all 0.15s ease;
}
.returns-trend-btn.active {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.donut-wrap {
    position: relative;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.donut-center {
    position: absolute;
    text-align: center;
    pointer-events: none;
}
.donut-center .total-val {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
}
.donut-center .total-lbl {
    font-size: 0.7rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
}
.reason-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 0.75rem;
}
.reason-legend-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.75rem;
    font-weight: 700;
    color: #475569;
}
.reason-legend-item .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 0.5rem;
    flex-shrink: 0;
}
.reason-legend-item .label-wrap {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}
.reason-legend-item .label-wrap span {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.trend-chart-wrap { height: 280px; }

.product-table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.product-thumb {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #e2e8f0, #f8fafc);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.product-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.product-cell .name {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.85rem;
}
.sellable-bar-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.sellable-bar {
    flex: 1;
    height: 6px;
    background: #e2e8f0;
    border-radius: 99px;
    overflow: hidden;
    min-width: 60px;
}
.sellable-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    border-radius: 99px;
    transition: width 0.4s ease;
}
.sellable-pct {
    font-size: 0.8rem;
    font-weight: 800;
    color: #0f172a;
    min-width: 36px;
    text-align: right;
}
.status-tag {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    border: 1px solid;
}
.status-tag.optimal { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
.status-tag.watch { background: #fffbeb; color: #d97706; border-color: #fde68a; }
.status-tag.critical { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #94a3b8;
    font-weight: 600;
    font-size: 0.85rem;
}

@media (max-width: 1400px) {
    .returns-page .kpi-grid-6 { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 900px) {
    .returns-page .kpi-grid-6 { grid-template-columns: repeat(2, 1fr); }
    .returns-charts-row { grid-template-columns: 1fr; }
}
</style>

<div class="returns-page">
    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 1.25rem; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 260px;">
                <label>Account Selection</label>
                <select id="filter_customer" style="width: 100%;" <?php echo (($_SESSION['role'] ?? '') === 'customer') ? 'disabled' : ''; ?>>
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <option value="">All Amazon Profiles</option>
                    <?php endif; ?>
                    <?php $customers->data_seek(0); while ($row = $customers->fetch_assoc()): ?>
                        <?php
                            $selected = (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) == $row['id']) ? 'selected' : '';
                            if (($_SESSION['role'] ?? '') === 'customer' && ($_SESSION['customer_id'] ?? 0) != $row['id']) continue;
                        ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($row['customer_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date Range</label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="date" id="filter_from" value="">
                    <span style="color: #94a3b8;">to</span>
                    <input type="date" id="filter_to" value="">
                </div>
            </div>
            <button id="apply_filters" class="btn btn-primary" style="height: 40px; padding: 0 20px;">
                <i class="fas fa-sync-alt"></i> REFRESH
            </button>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="kpi-grid-6">
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Total Returns</span>
                <div class="kpi-icon"><i class="fas fa-undo"></i></div>
            </div>
            <div class="kpi-value" id="kpi_total_returns">0</div>
            <div class="kpi-sub up" id="cmp_total_returns"><i class="fas fa-arrow-up"></i> — vs LW</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Sellable %</span>
                <div class="kpi-icon"><i class="fas fa-box-open"></i></div>
            </div>
            <div class="kpi-value" id="kpi_sellable_pct">0%</div>
            <div class="kpi-sub up" id="cmp_sellable_pct"><i class="fas fa-arrow-up"></i> — vs LW</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Damaged %</span>
                <div class="kpi-icon"><i class="fas fa-exclamation-circle"></i></div>
            </div>
            <div class="kpi-value" id="kpi_damaged_pct">0%</div>
            <div class="kpi-sub neutral" id="cmp_damaged_pct">Stable</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Top Reason</span>
                <div class="kpi-icon"><i class="fas fa-brain"></i></div>
            </div>
            <div class="kpi-value" id="kpi_top_reason" style="font-size: 1.25rem;">—</div>
            <div class="kpi-sub neutral" id="kpi_top_reason_sub">— of total</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Top SKU</span>
                <div class="kpi-icon"><i class="fas fa-shopping-bag"></i></div>
            </div>
            <div class="kpi-value" id="kpi_top_sku" style="font-size: 1.1rem;">—</div>
            <div class="kpi-sub neutral" id="kpi_top_sku_sub">—</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Defect Rate</span>
                <div class="kpi-icon"><i class="fas fa-times-circle"></i></div>
            </div>
            <div class="kpi-value" id="kpi_defect_rate">0%</div>
            <div class="kpi-sub critical" id="cmp_defect_rate"><i class="fas fa-arrow-up"></i> — Critical</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="returns-charts-row">
        <div class="returns-card">
            <div class="returns-card-header">
                <span class="returns-card-title">Return Reasons</span>
                <i class="fas fa-ellipsis-v" style="color: #94a3b8; cursor: pointer;"></i>
            </div>
            <div class="donut-wrap">
                <canvas id="reasonsChart"></canvas>
                <div class="donut-center">
                    <div class="total-val" id="donut_total">0</div>
                    <div class="total-lbl">Total</div>
                </div>
            </div>
            <div class="reason-legend" id="reason_legend"></div>
        </div>
        <div class="returns-card">
            <div class="returns-card-header">
                <span class="returns-card-title">Time Trend Analysis</span>
                <div class="returns-trend-toggle">
                    <button class="returns-trend-btn active" data-trend="daily">Daily</button>
                    <button class="returns-trend-btn" data-trend="monthly">Monthly</button>
                </div>
            </div>
            <div class="trend-chart-wrap">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Product Performance Table -->
    <div class="returns-card">
        <div class="product-table-header">
            <span class="returns-card-title">Product Performance</span>
            <button id="export_csv" class="btn btn-outline" style="height: 36px; padding: 0 16px; font-size: 0.8rem;">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
        <div class="table-scroll">
            <table class="report-table" id="products_table">
                <thead>
                    <tr>
                        <th>PRODUCT NAME</th>
                        <th>RETURN COUNT</th>
                        <th>TOP REASON</th>
                        <th>SELLABLE RATIO</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody id="products_body">
                    <tr><td colspan="5" class="empty-state">Loading returns data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="loading_overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner" style="width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top-color: #bef264; border-radius: 50%; animation: spin 1s linear infinite;"></div>
    <p style="margin-top: 1rem; font-weight: 700; color: #064e3b;">Loading returns...</p>
</div>

<script>
$(document).ready(function() {
    let reasonsChart = null;
    let trendChart = null;
    let trendMode = 'daily';
    let lastData = null;
    let productRows = [];

    function toNum(v) {
        const n = Number(v);
        return Number.isFinite(n) ? n : 0;
    }

    function formatAbbrev(n) {
        const num = toNum(n);
        const abs = Math.abs(num);
        if (abs >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
        return num.toLocaleString();
    }

    function showLoader() { $('#loading_overlay').css('display', 'flex'); }
    function hideLoader() { $('#loading_overlay').css('display', 'none'); }

    function setCmp(el, cmp, suffix, invert) {
        const $el = $(el);
        if (!cmp || cmp.dir === 'none') {
            $el.removeClass('up down neutral critical').addClass('neutral').html('Stable');
            return;
        }
        const isUp = cmp.dir === 'up';
        const good = invert ? !isUp : isUp;
        const cls = good ? 'up' : (invert ? 'critical' : 'down');
        const icon = isUp ? 'fa-arrow-up' : 'fa-arrow-down';
        const sign = isUp ? '+' : '-';
        $el.removeClass('up down neutral critical').addClass(cls)
            .html(`<i class="fas ${icon}"></i> ${sign}${cmp.pct}% ${suffix || 'vs LW'}`);
    }

    function renderKpis(kpis) {
        $('#kpi_total_returns').text(formatAbbrev(kpis.total_returns));
        $('#kpi_sellable_pct').text(kpis.sellable_pct + '%');
        $('#kpi_damaged_pct').text(kpis.damaged_pct + '%');
        $('#kpi_top_reason').text(kpis.top_reason === '—' ? '—' : kpis.top_reason.replace(/_/g, ' '));
        $('#kpi_top_reason_sub').text(kpis.top_reason_pct ? kpis.top_reason_pct + '% of total' : '— of total');
        $('#kpi_top_sku').text(kpis.top_sku);
        $('#kpi_top_sku_sub').text(kpis.top_sku_name && kpis.top_sku_name !== '—' ? kpis.top_sku_name : '—');
        $('#kpi_defect_rate').text(kpis.defect_rate + '%');

        const cmp = kpis.comparison || {};
        setCmp('#cmp_total_returns', cmp.total_returns, 'vs LW', true);
        setCmp('#cmp_sellable_pct', cmp.sellable_pct, 'vs LW', false);
        setCmp('#cmp_damaged_pct', cmp.damaged_pct, 'vs LW', true);
        setCmp('#cmp_defect_rate', cmp.defect_rate, '', true);
        if (cmp.defect_rate && cmp.defect_rate.dir !== 'none') {
            const isUp = cmp.defect_rate.dir === 'up';
            $('#cmp_defect_rate').append(isUp ? ' Critical' : '');
        }
    }

    function renderReasonsChart(reasons, total) {
        const el = document.getElementById('reasonsChart');
        if (!el || typeof Chart === 'undefined') return;
        if (reasonsChart) reasonsChart.destroy();

        $('#donut_total').text(formatAbbrev(total));

        if (!reasons || reasons.length === 0) {
            reasonsChart = new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: { labels: ['No Data'], datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderWidth: 0 }] },
                options: { cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
            $('#reason_legend').html('<div class="empty-state" style="padding:0.5rem;">No return reason data available</div>');
            return;
        }

        reasonsChart = new Chart(el.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: reasons.map(r => r.label),
                datasets: [{
                    data: reasons.map(r => r.count),
                    backgroundColor: reasons.map(r => r.color),
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        padding: 12,
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.parsed} (${reasons[ctx.dataIndex].pct}%)`
                        }
                    }
                }
            }
        });

        let legendHtml = '';
        reasons.forEach(r => {
            legendHtml += `<div class="reason-legend-item">
                <div class="label-wrap"><span class="dot" style="background:${r.color}"></span><span>${r.label}</span></div>
                <span>${r.pct}%</span>
            </div>`;
        });
        $('#reason_legend').html(legendHtml);
    }

    function renderTrendChart(trend) {
        const el = document.getElementById('trendChart');
        if (!el || typeof Chart === 'undefined') return;
        if (trendChart) trendChart.destroy();

        const data = trend[trendMode] || { labels: [], sellable: [], damaged: [] };

        trendChart = new Chart(el.getContext('2d'), {
            type: 'line',
            data: {
                labels: data.labels.length ? data.labels : ['—'],
                datasets: [
                    {
                        label: 'Sellable Returns',
                        data: data.sellable.length ? data.sellable : [0],
                        borderColor: '#60a5fa',
                        backgroundColor: 'rgba(96, 165, 250, 0.15)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#60a5fa',
                        borderWidth: 2
                    },
                    {
                        label: 'Damaged Returns',
                        data: data.damaged.length ? data.damaged : [0],
                        borderColor: '#f472b6',
                        backgroundColor: 'transparent',
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointRadius: 3,
                        pointBackgroundColor: '#f472b6',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8, font: { size: 11, weight: '700' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        padding: 12
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: '700' } } },
                    y: { grid: { color: 'rgba(148, 163, 184, 0.15)' }, beginAtZero: true, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    function statusClass(status) {
        const s = (status || '').toUpperCase();
        if (s === 'OPTIMAL') return 'optimal';
        if (s === 'WATCH') return 'watch';
        return 'critical';
    }

    function renderProducts(products) {
        productRows = products || [];
        if (!productRows.length) {
            $('#products_body').html('<tr><td colspan="5" class="empty-state">No return data found. Upload Amazon Returns reports via Report Upload Center.</td></tr>');
            return;
        }

        let html = '';
        productRows.forEach(p => {
            html += `<tr>
                <td>
                    <div class="product-cell">
                        <div class="product-thumb"><i class="fas fa-box"></i></div>
                        <span class="name">${escapeHtml(p.product_name)}</span>
                    </div>
                </td>
                <td>${toNum(p.return_count).toLocaleString()}</td>
                <td>${escapeHtml((p.top_reason || '—').replace(/_/g, ' '))}</td>
                <td>
                    <div class="sellable-bar-wrap">
                        <div class="sellable-bar"><div class="sellable-bar-fill" style="width:${p.sellable_ratio}%"></div></div>
                        <span class="sellable-pct">${p.sellable_ratio}%</span>
                    </div>
                </td>
                <td><span class="status-tag ${statusClass(p.status)}">${p.status}</span></td>
            </tr>`;
        });
        $('#products_body').html(html);
    }

    function escapeHtml(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function loadData() {
        const customerId = $('#filter_customer').val() || ($('#customer_id_hidden').val() || '');
        const from = $('#filter_from').val();
        const to = $('#filter_to').val();

        showLoader();
        $.ajax({
            url: '<?php echo BASE_URL; ?>api/returns_data.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            dataType: 'json',
            success: function(res) {
                hideLoader();
                if (res.error) {
                    alert(res.error);
                    return;
                }
                lastData = res;
                renderKpis(res.kpis || {});
                renderReasonsChart(res.reasons || [], (res.kpis || {}).total_returns || 0);
                renderTrendChart(res.trend || {});
                renderProducts(res.products || []);
            },
            error: function() {
                hideLoader();
                alert('Failed to load returns data.');
            }
        });
    }

    function initDates() {
        $.getJSON('<?php echo BASE_URL; ?>api/get_data_range.php', function(ranges) {
            const ops = ranges.ops || {};
            const trans = ranges.trans || {};
            let from = '2026-01-01';
            let to = '2026-02-28';

            if (ops.min_date && ops.min_date !== '0000-00-00' && ops.max_date && ops.max_date !== '0000-00-00') {
                from = ops.min_date;
                to = ops.max_date;
            } else if (trans.min_date) {
                from = String(trans.min_date).split(' ')[0];
                to = String(trans.max_date || trans.min_date).split(' ')[0];
            }

            $('#filter_from').val(from);
            $('#filter_to').val(to);
            loadData();
        }).fail(function() {
            $('#filter_from').val('2026-01-01');
            $('#filter_to').val('2026-02-28');
            loadData();
        });
    }

    $('#apply_filters').on('click', loadData);

    $('.returns-trend-btn').on('click', function() {
        $('.returns-trend-btn').removeClass('active');
        $(this).addClass('active');
        trendMode = $(this).data('trend');
        if (lastData) renderTrendChart(lastData.trend || {});
    });

    $('#export_csv').on('click', function() {
        if (!productRows.length) {
            alert('No data to export.');
            return;
        }
        const headers = ['Product Name', 'SKU', 'Return Count', 'Top Reason', 'Sellable Ratio', 'Status'];
        const lines = [headers.join(',')];
        productRows.forEach(p => {
            lines.push([
                '"' + (p.product_name || '').replace(/"/g, '""') + '"',
                '"' + (p.sku || '') + '"',
                p.return_count,
                '"' + (p.top_reason || '').replace(/"/g, '""') + '"',
                p.sellable_ratio + '%',
                p.status
            ].join(','));
        });
        const blob = new Blob([lines.join('\n')], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'returns_product_performance.csv';
        a.click();
    });

    initDates();
});
</script>

<?php include '../../includes/footer.php'; ?>
