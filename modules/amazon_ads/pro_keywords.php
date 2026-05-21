<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$page_title = "Search Term & Keyword Insights";
$page_subtitle = "Analyzing targeting efficiency and actual customer search behavior";

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    body { background-color: #f0f7f6 !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
    .glass-card { 
        background: rgba(255, 255, 255, 0.95); 
        backdrop-filter: blur(10px); 
        border: 1px solid rgba(226, 232, 240, 0.8); 
        border-radius: 24px; 
        box-shadow: 0 4px 24px -1px rgba(0, 0, 0, 0.04); 
        overflow: hidden;
        transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out);
    }
    .glass-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px -4px rgba(0, 0, 0, 0.08); }
    
    .analysis-table { width: 100%; border-collapse: collapse !important; }
    .analysis-table th { 
        background: #f8fafc !important; 
        color: #64748b !important; 
        font-weight: 800 !important; 
        font-size: 0.9rem !important; 
        text-transform: uppercase; 
        letter-spacing: 0.05em; 
        padding: 1.25rem 1rem !important;
        text-align: center !important;
        border-bottom: 2px solid #f1f5f9;
    }
    .analysis-table td { 
        padding: 1.25rem 1rem !important; 
        border-bottom: 1px solid #f1f5f9; 
        text-align: center !important;
        font-weight: 600;
        font-size: 0.975rem !important;
        color: #1e293b;
    }
    .analysis-table tr:hover td { background: #f8fafc; }

    .filter-label { font-weight: 800; font-size: 0.65rem; color: #94a3b8; margin-bottom: 0.6rem; display: block; text-transform: uppercase; letter-spacing: 0.08em; }
    .filter-input { 
        border-radius: 14px; border: 1.5px solid #e2e8f0; font-weight: 700; font-size: 0.8rem; 
        padding: 0.7rem 1rem; background: #ffffff; transition: all 0.2s; 
    }
    .filter-input:focus { border-color: #00353B; box-shadow: 0 0 0 4px rgba(0, 53, 59, 0.05); outline: none; }
    
    .section-title { font-weight: 900; color: #00353B; letter-spacing: -0.04em; font-size: 2rem; }
    
    .action-btn {
        background: #00353B; color: #C7FE91; border: none; border-radius: 14px;
        font-weight: 800; letter-spacing: 0.02em; padding: 0.8rem 1.5rem;
        box-shadow: 0 8px 20px -4px rgba(0, 53, 59, 0.3);
        transition: all 0.3s;
    }
    .action-btn:hover { background: #002b2f; transform: scale(1.02); box-shadow: 0 12px 24px -4px rgba(0, 53, 59, 0.4); color: #C7FE91; }

    /* Group specific colors from dashboard */
    .th-ads { background: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%) !important; color: #fff !important; border-color: #991b1b !important; }
    .th-growth { background: linear-gradient(180deg, #10b981 0%, #047857 100%) !important; color: #fff !important; border-color: #065f46 !important; }
    .th-standard { background: #f8fafc !important; color: #64748b !important; }
</style>

    <!-- Header Area -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h2 style="font-weight: 800; color: #1e293b; margin: 0;">Search Term Intelligence</h2>
            <p style="color: #64748b; font-weight: 600; font-size: 0.85rem; margin-top: 4px;">Advanced targeting analysis and automated budget insights</p>
        </div>
        <button id="refresh_button" class="action-btn">
            <i class="fas fa-bolt me-2"></i> ANALYZE PERFORMANCE
        </button>
    </div>

    <!-- Smart Filter Bar -->
    <div class="glass-card p-4 mb-4">
        <form id="filterForm" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="filter-label">AMAZON PROFILE</label>
                <select id="filter_customer" class="form-select filter-input shadow-none">
                    <option value="">All Profiles</option>
                    <?php 
                    $customers = get_all_customers();
                    while($c = $customers->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['customer_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="filter-label">BRAND</label>
                <select id="filter_brand" class="form-select filter-input shadow-none">
                    <option value="">All Brands</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="filter-label">TRAFFIC</label>
                <select id="filter_traffic_type" class="form-select filter-input shadow-none">
                    <option value="all">All Traffic</option>
                    <option value="branded">Branded</option>
                    <option value="non_branded">Non-Branded</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="filter-label">RANGE</label>
                <input type="date" id="filter_from" class="form-control filter-input shadow-none" value="2026-01-01">
            </div>
            <div class="col-md-2">
                <label class="filter-label">&nbsp;</label>
                <input type="date" id="filter_to" class="form-control filter-input shadow-none" value="2026-03-31">
            </div>
            <input type="hidden" id="filter_report_type" value="search_term">
        </form>
    </div>

<div class="p-0">
    <!-- Header Summary KPI Grid -->
    <div class="kpi-grid mb-4">
        <div class="card kpi-card blue-theme">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
            </div>
            <div class="kpi-body">
                <h3><span id="kpi_total_spend">$0.00</span></h3>
                <p>Total Ad Spend</p>
            </div>
            <div class="kpi-footer"><i class="fas fa-coins"></i><span>Aggregated Ad Costs</span></div>
        </div>

        <div class="card kpi-card green-theme">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
            <div class="kpi-body">
                <h3><span id="kpi_total_sales">$0.00</span></h3>
                <p>Total Ad Sales</p>
            </div>
            <div class="kpi-footer"><i class="fas fa-rocket"></i><span>Conversion Revenue</span></div>
        </div>

        <div class="card kpi-card yellow-theme">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-percentage"></i></div>
            </div>
            <div class="kpi-body">
                <h3><span id="kpi_avg_acos">0.00%</span></h3>
                <p>Targeting ACoS</p>
            </div>
            <div class="kpi-footer"><i class="fas fa-chart-line"></i><span>Efficiency Score</span></div>
        </div>

        <div class="card kpi-card red-theme">
            <div class="kpi-header">
                <div class="kpi-icon"><i class="fas fa-fire"></i></div>
            </div>
            <div class="kpi-body">
                <h3><span id="kpi_wasted_spend">$0.00</span></h3>
                <p>Wasted Spend</p>
            </div>
            <div class="kpi-footer"><i class="fas fa-shield-virus"></i><span>Risk Detected</span></div>
        </div>
    </div>

    <!-- MAIN DASHBOARD GRID -->
    <div class="row g-4 mb-5">
        
        <!-- Negative Suggestions -->
        <div class="col-12">
            <div class="glass-card border-0">
                <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-900 mb-1" style="color: #b91c1c;"><i class="fas fa-shield-virus me-2"></i> Negative Insights</h5>
                        <p class="text-muted small mb-0 fw-600">High spend, zero sales. Block these immediately.</p>
                    </div>
                </div>
                <div class="p-4" style="background: rgba(239, 68, 68, 0.02); max-height: 600px; overflow-y: auto;">
                    <div id="negative_list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                        <div class="text-center py-5 text-muted small w-100" style="grid-column: 1 / -1;">
                            <i class="fas fa-search-dollar fa-2x mb-3 opacity-20 text-danger"></i>
                            <div>No high-risk terms detected.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Opportunities -->
        <div class="col-12">
            <div class="glass-card border-0">
                <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-900 mb-1" style="color: #047857;"><i class="fas fa-rocket me-2"></i> SEO Expansion Opportunities</h5>
                        <p class="text-muted small mb-0 fw-600">Top converters. Move to product listing SEO.</p>
                    </div>
                </div>
                <div class="p-4" style="background: rgba(16, 185, 129, 0.02); max-height: 600px; overflow-y: auto;">
                    <div id="seo_list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                        <div class="text-center py-5 text-muted small w-100" style="grid-column: 1 / -1;">
                            <i class="fas fa-lightbulb fa-2x mb-3 opacity-20 text-success"></i>
                            <div>Scanning for opportunities...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Granular Analytics -->
        <div class="col-12">
            <div class="glass-card border-0">
                <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-900 mb-1" style="color: #00353B;"><i class="fas fa-table me-2"></i> Targeting Performance Log</h5>
                        <p class="text-muted small mb-0 fw-600">Granular data for every targeting keyword and search term.</p>
                    </div>
                    <div class="input-group" style="width: 320px; border-radius: 14px; overflow: hidden; border: 1.5px solid #e2e8f0;">
                        <span class="input-group-text border-0 bg-white px-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="keywordSearch" class="form-control border-0 py-2" style="font-size: 0.85rem; font-weight: 700; outline: none; box-shadow: none;" placeholder="Filter entries...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="analysis-table align-middle mb-0" id="keywordTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="px-4 th-standard">Query / Campaign</th>
                                <th class="th-standard">Type</th>
                                <th class="th-standard">Orders</th>
                                <th class="th-ads">Spend</th>
                                <th class="th-growth">Sales</th>
                                <th class="th-ads">ACoS</th>
                                <th class="th-growth">ROAS</th>
                                <th class="th-standard px-4">Rec.</th>
                            </tr>
                        </thead>
                        <tbody id="keyword_body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
    </div>
</div>

<script>
$(document).ready(function() {
    function loadBrands(callback) {
        const customerId = $('#filter_customer').val();
        $.get('../../api/get_brands.php', { customer_id: customerId }, function(brands) {
            let html = '<option value="">All Brands</option>';
            brands.forEach(b => {
                html += `<option value="${b}">${b}</option>`;
            });
            $('#filter_brand').html(html);
            if (callback) callback();
        });
    }

    // Sequential initialization
    $.get('../../api/get_data_range.php', function(ranges) {
        if (ranges.ads && ranges.ads.min_date) {
            $('#filter_from').val(ranges.ads.min_date);
            $('#filter_to').val(ranges.ads.max_date);
        }
        loadBrands(loadData);
    });

    // Auto-refresh when filters change
    $('#filter_customer').on('change', function() {
        loadBrands();
        loadData();
    });
    $('#filter_brand, #filter_report_type, #filter_traffic_type, #filter_from, #filter_to').on('change', loadData);
    
    // Explicit click listener for the Analyze button
    $('#refresh_button').on('click', function(e) {
        e.preventDefault();
        loadData();
    });

    function loadData() {
        const customerId = $('#filter_customer').val();
        const from = $('#filter_from').val();
        const to = $('#filter_to').val();
        const reportType = $('#filter_report_type').val();
        const brand = $('#filter_brand').val();
        const trafficType = $('#filter_traffic_type').val();

        $('#refresh_button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> EXPLORING...');
        $('#keyword_body').css('opacity', '0.5');

        $.ajax({
            url: '<?php echo BASE_URL; ?>api/keyword_data.php',
            data: { 
                customer_id: customerId, 
                from_date: from, 
                to_date: to,
                report_type: reportType,
                brand: brand,
                traffic_type: trafficType
            },
            success: function(res) {
                $('#refresh_button').prop('disabled', false).html('<i class="fas fa-bolt me-2"></i> ANALYZE');
                $('#keyword_body').css('opacity', '1');
                
                let html = '';
                let negHtml = '';
                let seoHtml = '';
                
                let totalSpend = 0, totalSales = 0, wastedSpend = 0;

                if (res.data && res.data.length > 0) {
                    res.data.forEach(row => {
                        const spend = parseFloat(row.spend || 0);
                        const sales = parseFloat(row.sales || 0);
                        totalSpend += spend;
                        totalSales += sales;
                        if (sales === 0) wastedSpend += spend;

                        const acos = parseFloat(row.acos || 0);
                        const orders = parseInt(row.orders || 0);
                        const clicks = parseInt(row.clicks || 0);
                        
                        let acosDisplay = acos.toFixed(2) + '%';
                        let acosStyle = `background: ${acos > 30 ? '#fff1f2' : '#f0fdf4'}; color: ${acos > 30 ? '#ef4444' : '#10b981'};`;
                        
                        let bidAction = '';
                        let bidStyle = '';
                        let seoBadge = '';

                        // Logic for main table and lists
                        if (sales > 0) {
                            if (acos < 15) {
                                bidAction = 'SCALE UP';
                                bidStyle = 'background: #dcfce7; color: #166534;';
                                if (orders > 5) {
                                    seoBadge = '<span class="ms-2 badge bg-success" style="font-size: 0.55rem; vertical-align: middle;">TOP CONVERTER</span>';
                                    seoHtml += `
                                        <div class="bg-white p-3 shadow-sm border" 
                                             style="border-radius: 16px; border: 1px solid #f1f5f9 !important; transition: all 0.3s ease;"
                                        >
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.85rem; line-height: 1.2;">${row.targeting}</div>
                                                <span class="badge bg-success" style="font-size: 0.5rem;">${orders} ORDERS</span>
                                            </div>
                                            <div class="d-flex gap-2 mb-3">
                                                <div style="background: #f0fdf4; padding: 3px 8px; border-radius: 6px; font-weight: 800; color: #10b981; font-size: 0.65rem;">${acos.toFixed(1)}% ACoS</div>
                                                <div style="background: #eff6ff; padding: 3px 8px; border-radius: 6px; font-weight: 800; color: #3b82f6; font-size: 0.65rem;">${parseFloat(row.roas).toFixed(2)}x ROAS</div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span style="font-size: 0.65rem; font-weight: 800; color: #94a3b8;">Revenue: $${sales.toFixed(2)}</span>
                                                <button class="btn btn-sm py-1 px-3 fw-900" style="background: #10b981; color: #fff; font-size: 0.6rem; border-radius: 8px;">SCALE</button>
                                            </div>
                                        </div>`;
                                } else if (orders > 1) {
                                    seoBadge = '<span class="ms-2 badge bg-info" style="font-size: 0.55rem; vertical-align: middle;">SEO POTENTIAL</span>';
                                }
                            } else if (acos < 25) {
                                bidAction = 'MAINTAIN';
                                bidStyle = 'background: #f0fdf4; color: #10b981;';
                            } else if (acos < 35) {
                                bidAction = 'OPTIMIZE';
                                bidStyle = 'background: #fffbeb; color: #b45309;';
                            } else {
                                bidAction = 'REDUCE BID';
                                bidStyle = 'background: #fff1f2; color: #ef4444;';
                            }
                        } else if (spend > 10 || clicks > 15) {
                            bidAction = 'NEGATE / PAUSE';
                            bidStyle = 'background: #fef2f2; color: #991b1b;';
                            acosDisplay = 'WASTED';
                            acosStyle = 'background: #fff1f2; color: #ef4444; font-size: 0.6rem; letter-spacing: 0.05em;';
                            
                            negHtml += `
                                        <div class="bg-white p-3 shadow-sm border" 
                                             style="border-radius: 16px; border: 1px solid #fecaca !important; transition: all 0.3s ease;"
                                        >
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.85rem; line-height: 1.2;">${row.targeting}</div>
                                                <span class="badge bg-danger" style="font-size: 0.5rem;">WASTED</span>
                                            </div>
                                            <div class="d-flex gap-2 mb-3">
                                                <div style="background: #fef2f2; padding: 3px 8px; border-radius: 6px; font-weight: 800; color: #b91c1c; font-size: 0.65rem;">$${spend.toFixed(2)} SPENT</div>
                                                <div style="background: #f8fafc; padding: 3px 8px; border-radius: 6px; font-weight: 800; color: #475569; font-size: 0.65rem;">${clicks} CLICKS</div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span style="font-size: 0.65rem; font-weight: 800; color: #94a3b8;">Conversion: 0.00%</span>
                                                <button class="btn btn-sm py-1 px-3 fw-900" style="background: #b91c1c; color: #fff; font-size: 0.6rem; border-radius: 8px;">NEGATE</button>
                                            </div>
                                        </div>`;
                        } else {
                            bidAction = 'COLLECT DATA';
                            bidStyle = 'background: #f8fafc; color: #64748b;';
                            acosDisplay = '-';
                            acosStyle = 'background: #f8fafc; color: #94a3b8;';
                        }

                        html += `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="px-4 py-3">
                                    <div class="fw-800" style="color: #1e293b; font-size: 0.95rem; line-height: 1.4;">${row.targeting} ${seoBadge}</div>
                                    <div class="text-muted" style="font-size: 0.75rem; font-weight: 600; margin-top: 2px;">
                                        <i class="fas fa-folder-open me-1 opacity-50"></i> ${row.campaign} 
                                    </div>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border fw-700" style="font-size: 0.75rem; padding: 5px 10px;">${row.match_type || 'N/A'}</span></td>
                                <td class="text-center fw-800" style="font-size: 0.95rem;">${orders}</td>
                                <td class="text-end fw-700" style="color: #475569; font-size: 0.9rem;">$${spend.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                                <td class="text-end fw-900" style="color: #059669; font-size: 1rem;">$${sales.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                                <td class="text-center fw-900" style="${acosStyle} border-radius: 8px; font-size: 0.9rem; padding: 6px;">${acosDisplay}</td>
                                <td class="text-center fw-900 text-primary" style="font-size: 0.95rem;">${parseFloat(row.roas).toFixed(2)}</td>
                                <td class="text-center px-4">
                                    <span style="${bidStyle} font-size: 0.7rem; font-weight: 900; padding: 5px 10px; border-radius: 8px; letter-spacing: 0.02em; display: inline-block;">${bidAction}</span>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="8" class="text-center" style="padding: 5rem; color: #94a3b8; font-weight: 600;">No performance data found for the selected criteria.</td></tr>';
                }

                $('#keyword_body').html(html);
                $('#negative_list').html(negHtml || '<div class="text-center py-4 text-muted small">No negative risks detected.</div>');
                $('#seo_list').html(seoHtml || '<div class="text-center py-4 text-muted small">No SEO opportunities found.</div>');

                // Update KPI Cards
                $('#kpi_total_spend').text('$' + totalSpend.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#kpi_total_sales').text('$' + totalSales.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#kpi_wasted_spend').text('$' + wastedSpend.toLocaleString(undefined, {minimumFractionDigits: 2}));
                const avgAcos = totalSales > 0 ? (totalSpend / totalSales) * 100 : 0;
                $('#kpi_avg_acos').text(avgAcos.toFixed(2) + '%');

                if ($.fn.DataTable.isDataTable('#keywordTable')) {
                    $('#keywordTable').DataTable().destroy();
                }
                $('#keywordTable').DataTable({ 
                    order: [[4, 'desc']], 
                    pageLength: 25,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search keywords..."
                    }
                });
            },
            error: function(xhr) {
                $('#refresh_button').prop('disabled', false).html('<i class="fas fa-magic me-2"></i> EXPLORE');
                $('#keyword_body').html('<tr><td colspan="8" class="text-center text-danger py-5">Error loading data. Please try again.</td></tr>');
            }
        });
    }

    $('#keywordSearch').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $("#keyword_body tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadData();
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
