<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$customers = get_all_customers();

$page_title = "Payment & Transaction Analytics";
$page_subtitle = "In-depth financial breakdown from Amazon Transaction Ledger";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-radius: 16px;">
    <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 250px;">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Amazon Profile</label>
            <select id="customerSelect" style="width: 100%; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                <?php while ($row = $customers->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['customer_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label style="font-weight: 700; color: #475569; margin-bottom: 0.5rem; display: block;">Analysis Month</label>
            <input type="month" id="reportMonth" class="form-control" value="<?php echo date('Y-m'); ?>" style="padding: 0.6rem; border-radius: 8px;">
        </div>
        <button onclick="loadPaymentData()" class="btn btn-primary" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-sync-alt"></i> ANALYZE
        </button>
    </div>
    <div id="date_suggestion" style="margin-top: 1rem; font-size: 0.8rem; display: none;"></div>
</div>

<div id="no_data_alert" style="display: none; padding: 2rem; background: #fff1f2; color: #e11d48; border-radius: 16px; border: 1px solid #fecdd3; margin-bottom: 2rem; text-align: center;">
    <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
    <div style="font-weight: 800; font-size: 1.1rem;">No transaction data found for this period.</div>
    <div id="suggest_range" style="margin-top: 0.5rem; font-weight: 600;"></div>
</div>

<div id="main_content">
    <!-- Summary Overview -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;" id="summaryCards">
        <!-- Cards will be injected by JS -->
    </div>

    <!-- Main Analysis Grid -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
        <!-- Left: SKU Performance -->
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
                <i class="fas fa-boxes" style="color: #6366f1; margin-right: 0.5rem;"></i> SKU Level Performance
            </div>
            <div style="padding: 1rem; overflow-x: auto;">
                <table class="table" id="skuTable" style="width: 100%;">
                    <thead style="background: #f1f5f9;">
                        <tr>
                            <th>SKU</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Sales</th>
                            <th style="text-align: right;">Rebates</th>
                            <th style="text-align: right;">Fees</th>
                            <th style="text-align: right; background: #eef2ff;">Gross Net</th>
                        </tr>
                    </thead>
                    <tbody id="skuBody"></tbody>
                </table>
            </div>
        </div>

        <!-- Right: Type Breakdown & Geography -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
                <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
                    <i class="fas fa-tags" style="color: #6366f1; margin-right: 0.5rem;"></i> Transaction Types
                </div>
                <div id="typeBreakdown" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;"></div>
            </div>

            <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
                <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 700; color: #334155;">
                    <i class="fas fa-map-marker-alt" style="color: #6366f1; margin-right: 0.5rem;"></i> Top Sales by State
                </div>
                <div id="geoBreakdown" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check available ranges
    $.get('../../api/get_data_range.php', function(ranges) {
        if (ranges.trans && ranges.trans.min_date) {
            const min = new Date(ranges.trans.min_date);
            const max = new Date(ranges.trans.max_date);
            $('#date_suggestion').html(`<i class="fas fa-info-circle"></i> Data available from <b>${min.toLocaleString('default', { month: 'short', year: 'numeric' })}</b> to <b>${max.toLocaleString('default', { month: 'short', year: 'numeric' })}</b>`).show();
            $('#suggest_range').text(`Note: Most recent data is for ${max.toLocaleString('default', { month: 'long', year: 'numeric' })}`);
        }
    });

    loadPaymentData();
});

async function loadPaymentData() {
    const customerId = $('#customerSelect').val();
    const month = $('#reportMonth').val();
    const fromDate = month + '-01';
    const toDate = new Date(month + '-01');
    toDate.setMonth(toDate.getMonth() + 1);
    toDate.setDate(0);
    const toDateStr = toDate.toISOString().split('T')[0];

    const btn = $('button[onclick="loadPaymentData()"]');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Analyzing...');

    try {
        const response = await fetch(`../../api/payment_analysis.php?customer_id=${customerId}&from_date=${fromDate}&to_date=${toDateStr}`);
        const data = await response.json();

        if (data.status === 'success' && data.sku_analysis.length > 0) {
            $('#no_data_alert').hide();
            $('#main_content').show();
            renderSummary(data);
            renderSKUAnalysis(data.sku_analysis);
            renderTypeBreakdown(data.summary);
            renderGeoBreakdown(data.geo_analysis);
        } else {
            $('#main_content').hide();
            $('#no_data_alert').show();
        }
    } catch (error) {
        console.error('Error loading payment data:', error);
    } finally {
        btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> ANALYZE');
    }
}

function renderSummary(data) {
    const container = document.getElementById('summaryCards');
    const t = data.totals;
    const summary = data.summary;
    
    const ads = summary['Service Fee'] ? Math.abs(summary['Service Fee'].total) : 0;
    const orderData = summary['Order'] || { product_sales: 0, rebates: 0, selling_fees: 0, fba_fees: 0 };
    const orderGrossNet = orderData.product_sales + orderData.rebates + orderData.selling_fees + orderData.fba_fees;

    const cards = [
        { label: 'Gross Product Sales', value: formatCurrency(t.product_sales), color: '#6366f1', sub: 'Total revenue before deductions', border: '#6366f1' },
        { label: 'Gross Net (Orders)', value: formatCurrency(orderGrossNet), color: '#4f46e5', sub: 'Order Revenue - Rebates - Fees', border: '#4f46e5' },
        { label: 'Advertising Cost', value: formatCurrency(ads), color: '#f43f5e', sub: 'Service Fee (AMS/Ads)', border: '#f43f5e' },
        { label: 'Amazon Net Profit', value: formatCurrency(data.net_profit), color: '#10b981', sub: 'Net Profit (Transfers Excluded)', border: '#10b981' }
    ];

    container.innerHTML = cards.map(c => `
        <div class="card" style="margin-bottom: 0; padding: 1.5rem; border-left: 5px solid ${c.border}; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-radius: 12px;">
            <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem;">${c.label}</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #1e293b;">${c.value}</div>
            <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.5rem; font-weight: 500;">${c.sub}</div>
        </div>
    `).join('');
}

function renderSKUAnalysis(skus) {
    const body = document.getElementById('skuBody');
    body.innerHTML = skus.map(s => `
        <tr>
            <td>
                <div style="font-weight: 700; color: #334155;">${s.sku}</div>
                <div style="font-size: 0.7rem; color: #94a3b8; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${s.description}</div>
            </td>
            <td style="text-align: center;">${s.qty}</td>
            <td style="text-align: right;">${formatCurrency(s.sales)}</td>
            <td style="text-align: right; color: ${s.rebates < 0 ? '#ef4444' : 'inherit'}">${formatCurrency(s.rebates)}</td>
            <td style="text-align: right; color: #ef4444;">${formatCurrency(s.selling_fees + s.fba_fees)}</td>
            <td style="text-align: right; font-weight: 800; background: #f8fafc; color: #1e293b;">${formatCurrency(s.gross_net)}</td>
        </tr>
    `).join('');
}

function renderTypeBreakdown(summary) {
    const container = document.getElementById('typeBreakdown');
    const types = Object.keys(summary).sort((a, b) => Math.abs(summary[b].total) - Math.abs(summary[a].total));
    
    container.innerHTML = types.map(type => {
        const data = summary[type];
        const isPositive = data.total >= 0;
        const color = type === 'Transfer' ? '#64748b' : (isPositive ? '#10b981' : '#f43f5e');
        
        return `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: #fff; border: 1px solid #f1f5f9; border-left: 5px solid ${color}; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                <div>
                    <div style="font-weight: 800; font-size: 0.85rem; color: #1e293b;">${type}</div>
                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600;">${data.count.toLocaleString()} transactions</div>
                </div>
                <div style="font-weight: 800; color: ${color}; font-size: 1rem;">
                    ${isPositive ? '' : '-'}${formatCurrency(Math.abs(data.total))}
                </div>
            </div>
        `;
    }).join('');
}

function renderGeoBreakdown(geo) {
    const container = document.getElementById('geoBreakdown');
    const states = {};
    geo.forEach(g => {
        if (!states[g.order_state]) states[g.order_state] = 0;
        states[g.order_state] += parseFloat(g.sales);
    });

    const sortedStates = Object.entries(states).sort((a,b) => b[1] - a[1]).slice(0, 5);
    const max = sortedStates[0] ? sortedStates[0][1] : 1;

    container.innerHTML = sortedStates.map(([state, sales]) => `
        <div style="margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 700; margin-bottom: 0.4rem; color: #475569;">
                <span>${state || 'Unknown'}</span>
                <span>${formatCurrency(sales)}</span>
            </div>
            <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%); width: ${(sales/max)*100}%"></div>
            </div>
        </div>
    `).join('');
}

function formatCurrency(val) {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
}
</script>

<?php include '../../includes/footer.php'; ?>
