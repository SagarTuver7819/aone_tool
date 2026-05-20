<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = 'Daily Report Data';
$page_subtitle = 'Daily performance table + charts';

$customers = get_all_customers();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card" style="margin-bottom: 1.25rem;">
    <div style="display:flex; align-items:flex-end; justify-content:space-between; gap: 1.25rem; flex-wrap: wrap;">
        <div style="display:flex; gap: 1.25rem; align-items:flex-end; flex-wrap: wrap; flex: 1;">
            <div class="form-group" style="min-width: 280px; flex: 1;">
                <label>Account Selection</label>
                <select id="filter_customer" style="width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #e5e7eb;">
                    <option value="">All Amazon Profiles</option>
                    <?php while ($row = $customers->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['customer_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date Range</label>
                <div style="display:flex; gap: 0.5rem; align-items:center;">
                    <input type="date" id="filter_from" value="2026-01-01" style="padding: 8px 12px; border-radius: 10px; border: 1px solid #e5e7eb;">
                    <span style="color:#94a3b8;">to</span>
                    <input type="date" id="filter_to" value="2026-01-31" style="padding: 8px 12px; border-radius: 10px; border: 1px solid #e5e7eb;">
                </div>
            </div>
            <button id="apply_filters" class="btn-primary" style="height: 42px; background: #bef264; color: #064e3b; font-weight: 800; border: none; border-radius: 10px; padding: 0 18px;">
                <i class="fas fa-sync-alt"></i> REFRESH
            </button>
        </div>
        <div style="display:flex; gap: 0.75rem; align-items:center;">
            <input id="table_search" type="search" placeholder="Search..." style="height: 42px; padding: 0 12px; border-radius: 10px; border: 1px solid #cbd5e1; width: 220px;">
            <button id="export_csv" class="btn" style="height: 42px; background: #f3f4f6; color: #374151; font-weight: 700; border: none; border-radius: 10px; padding: 0 16px;">
                <i class="fas fa-file-csv"></i> Export
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="chart-tabs" style="margin-bottom: 1rem;">
        <div class="chart-tab-btn active" data-daily-chart="sales">Sales</div>
        <div class="chart-tab-btn" data-daily-chart="units_sessions">Units vs Sessions</div>
        <div class="chart-tab-btn" data-daily-chart="conversion_refund">Conversion vs Refund Rate</div>
        <div class="chart-tab-btn" data-daily-chart="shipping">Shipping</div>
    </div>
    <div style="height: 420px;"><canvas id="dailyChart"></canvas></div>
</div>

<div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
        <h3 style="margin:0; display:flex; align-items:center; gap:0.6rem;"><i class="fas fa-table"></i> Daily Report Data</h3>
        <div style="font-size: 0.75rem; color: #64748B;" id="daily_meta"></div>
    </div>
    <div class="table-scroll">
        <table class="report-table">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>SALES</th>
                    <th>B2B SALES</th>
                    <th>UNITS</th>
                    <th>SESSIONS</th>
                    <th>PAGE VIEWS</th>
                    <th>CONVERSION</th>
                    <th>REFUNDS</th>
                    <th>REFUND RATE</th>
                </tr>
            </thead>
            <tbody id="daily_body"></tbody>
        </table>
    </div>
</div>

<div id="loading_overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner" style="width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top-color: #bef264; border-radius: 50%; animation: spin 1s linear infinite;"></div>
    <p style="margin-top: 1rem; font-weight: 800; color: #064e3b;">Loading report...</p>
</div>

<script>
$(document).ready(function() {
    let dailyRows = [];
    let dailyChart = null;
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function toNumber(v) {
        const n = Number(v);
        return Number.isFinite(n) ? n : 0;
    }

    function formatAbbrev(n) {
        const num = toNumber(n);
        const abs = Math.abs(num);
        if (abs >= 1e9) return (num / 1e9).toFixed(1).replace(/\.0$/, '') + 'B';
        if (abs >= 1e6) return (num / 1e6).toFixed(1).replace(/\.0$/, '') + 'M';
        if (abs >= 1e3) return (num / 1e3).toFixed(1).replace(/\.0$/, '') + 'K';
        return num.toLocaleString();
    }

    function showLoader() {
        const $o = $('#loading_overlay');
        $o.css('display', 'flex');
        requestAnimationFrame(() => $o.addClass('show'));
    }
    function hideLoader() {
        const $o = $('#loading_overlay');
        $o.removeClass('show');
        const delay = prefersReducedMotion ? 0 : 240;
        setTimeout(() => $o.css('display', 'none'), delay);
    }

    function money(n) {
        return '$' + toNumber(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function pct(n) {
        return toNumber(n).toFixed(2) + '%';
    }

    function dateShort(iso) {
        const d = new Date((iso || '').toString());
        if (Number.isNaN(d.getTime())) return iso || '';
        return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit' });
    }

    function renderTable() {
        const q = ($('#table_search').val() || '').toString().trim().toLowerCase();
        let html = '';
        dailyRows.forEach(r => {
            const line = [
                r.report_date, r.sales, r.b2b_sales, r.units, r.sessions, r.page_views,
                r.conversion, r.refunds, r.refund_rate
            ].join(' ').toLowerCase();
            if (q && !line.includes(q)) return;

            const refundClass = toNumber(r.refund_rate) >= 10 ? 'bad' : '';
            html += `<tr>
                <td class="date-cell" title="${r.report_date}">${dateShort(r.report_date)}</td>
                <td>${money(r.sales)}</td>
                <td>${money(r.b2b_sales)}</td>
                <td>${toNumber(r.units).toLocaleString()}</td>
                <td>${toNumber(r.sessions).toLocaleString()}</td>
                <td>${toNumber(r.page_views).toLocaleString()}</td>
                <td>${pct(r.conversion)}</td>
                <td>${toNumber(r.refunds).toLocaleString()}</td>
                <td class="${refundClass}">${pct(r.refund_rate)}</td>
            </tr>`;
        });
        $('#daily_body').html(html);
    }

    function renderDailyChart(type) {
        if (typeof Chart === 'undefined') return;
        const el = document.getElementById('dailyChart');
        if (!el) return;
        const ctx = el.getContext('2d');
        if (dailyChart) dailyChart.destroy();

        const labels = dailyRows.map(r => r.report_date);
        const sales = dailyRows.map(r => toNumber(r.sales));
        const b2b = dailyRows.map(r => toNumber(r.b2b_sales));
        const units = dailyRows.map(r => toNumber(r.units));
        const sessions = dailyRows.map(r => toNumber(r.sessions));
        const conversion = dailyRows.map(r => toNumber(r.conversion));
        const refunds = dailyRows.map(r => toNumber(r.refunds));
        const refundRate = dailyRows.map(r => toNumber(r.refund_rate));
        const shippedSales = dailyRows.map(r => toNumber(r.shipped_sales));
        const unitsShipped = dailyRows.map(r => toNumber(r.units_shipped));

        let config = {
            type: 'line',
            data: { labels, datasets: [] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                animation: prefersReducedMotion ? false : { duration: 900, easing: 'easeOutQuart' },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10, boxHeight: 10 } },
                    tooltip: { padding: 12, backgroundColor: 'rgba(15, 23, 42, 0.92)', titleColor: '#fff', bodyColor: '#fff' }
                },
                scales: {
                    y: { grid: { color: 'rgba(148, 163, 184, 0.18)' }, ticks: { callback: (v) => formatAbbrev(v) } },
                    y1: { display: false, position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: (v) => formatAbbrev(v) } },
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } }
                }
            }
        };

        if (type === 'units_sessions') {
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Units', data: units, backgroundColor: '#10B98155', borderColor: '#10B981', borderWidth: 1, borderRadius: 8 },
                { label: 'Sessions', data: sessions, type: 'line', yAxisID: 'y1', borderColor: '#0F172A', tension: 0.35, pointRadius: 0, borderWidth: 2 }
            ];
            config.options.scales.y1.display = true;
        } else if (type === 'conversion_refund') {
            config.type = 'line';
            config.data.datasets = [
                { label: 'Conversion %', data: conversion, borderColor: '#f43f5e', tension: 0.35, pointRadius: 2, borderWidth: 2 },
                { label: 'Refund Rate %', data: refundRate, borderColor: '#84cc16', tension: 0.35, pointRadius: 2, borderWidth: 2 }
            ];
            config.options.scales.y.ticks.callback = (v) => toNumber(v).toFixed(0) + '%';
            config.options.plugins.tooltip.callbacks = {
                label: (c) => `${c.dataset.label}: ${toNumber(c.parsed.y).toFixed(2)}%`
            };
        } else if (type === 'shipping') {
            config.type = 'bar';
            config.data.datasets = [
                { label: 'Shipped Sales', data: shippedSales, backgroundColor: '#1e293bcc', borderRadius: 8, maxBarThickness: 44 },
                { label: 'Units Shipped', data: unitsShipped, type: 'line', yAxisID: 'y1', borderColor: '#0ea5e9', tension: 0.35, pointRadius: 0, borderWidth: 2 }
            ];
            config.options.scales.y1.display = true;
            config.options.plugins.tooltip.callbacks = {
                label: (c) => {
                    if (c.dataset.label.includes('Sales')) return `${c.dataset.label}: ${money(c.parsed.y)}`;
                    return `${c.dataset.label}: ${toNumber(c.parsed.y).toLocaleString()}`;
                }
            };
        } else {
            // sales
            config.type = 'line';
            config.data.datasets = [
                { label: 'Sales', data: sales, borderColor: '#10B981', backgroundColor: '#10B98122', fill: true, tension: 0.35, pointRadius: labels.length > 40 ? 0 : 2, borderWidth: 2 },
                { label: 'B2B Sales', data: b2b, borderColor: '#0ea5e9', backgroundColor: '#0ea5e922', fill: true, tension: 0.35, pointRadius: labels.length > 40 ? 0 : 2, borderWidth: 2 }
            ];
            config.options.plugins.tooltip.callbacks = {
                label: (c) => `${c.dataset.label}: ${money(c.parsed.y)}`
            };
        }

        dailyChart = new Chart(ctx, config);
    }

    function loadDaily() {
        showLoader();
        const customerId = $('#filter_customer').val();
        let from = $('#filter_from').val();
        let to = $('#filter_to').val();
        if (!from) from = '2026-01-01';
        if (!to) to = '2026-01-31';

        $.ajax({
            url: '<?php echo BASE_URL; ?>api/daily_report_data.php',
            data: { customer_id: customerId, from_date: from, to_date: to },
            dataType: 'json',
            timeout: 60000,
            success: function(res) {
                if (!res || !res.success) throw new Error('Unexpected API response.');
                dailyRows = Array.isArray(res.rows) ? res.rows : [];
                $('#daily_meta').text(`${res.from_date} to ${res.to_date} - ${dailyRows.length} days`);
                renderTable();
                renderDailyChart($('.chart-tab-btn.active').data('daily-chart'));
            },
            error: function(xhr) {
                console.error('Daily report error:', xhr.responseText);
                alert('Daily report failed. Check console for details.');
            },
            complete: function() { hideLoader(); }
        });
    }

    $('#apply_filters').click(loadDaily);
    $('#table_search').on('input', renderTable);

    $('.chart-tab-btn').click(function() {
        $('.chart-tab-btn').removeClass('active');
        $(this).addClass('active');
        renderDailyChart($(this).data('daily-chart'));
    });

    $('#export_csv').click(function() {
        const customerId = $('#filter_customer').val();
        const from = $('#filter_from').val() || '2026-01-01';
        const to = $('#filter_to').val() || '2026-01-31';
        const url = '<?php echo BASE_URL; ?>api/export_business_daily_csv.php?customer_id=' + encodeURIComponent(customerId) + '&from_date=' + encodeURIComponent(from) + '&to_date=' + encodeURIComponent(to);
        window.location.href = url;
    });

    loadDaily();
});
</script>

<?php include '../../includes/footer.php'; ?>
