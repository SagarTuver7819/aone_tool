<?php
$file = 'c:/xampp/htdocs/sagar/backup_aone/modules/dashboard/index.php';
$content = file_get_contents($file);

$section_style = "text-transform: capitalize; font-size: 0.85rem; font-weight: 800; color: #475569; letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem 0.5rem; display: flex; align-items: center; gap: 8px;";

$new_tab_kpi_html = '
    <!-- Revenue Breakdown Section -->
    <div style="' . $section_style . '"><i class="fas fa-chart-line" style="color: var(--primary-light);"></i> Revenue Breakdown</div>
    <div class="kpi-grid">
        <div class="card kpi-card blue-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-money-bill-wave"></i><span id="kpi_sales_sub">Total Revenue</span></div>
            <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_sales">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_sales" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card emerald-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-seedling"></i><span id="kpi_organic_sub">Organic Sales</span></div>
            <div class="kpi-icon"><i class="fas fa-leaf"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_organic">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_organic" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card purple-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-bullhorn"></i><span id="kpi_ad_sales_sub">Ad Sales</span></div>
            <div class="kpi-icon"><i class="fas fa-ad"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_ad_sales">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_ad_sales" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card green-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-tachometer-alt"></i><span id="kpi_dsr_sub">Daily Sales Rate</span></div>
            <div class="kpi-icon"><i class="fas fa-calendar-day"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_dsr">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_dsr" class="cmp-tag"></span>
        </div>
        </div>
    </div>
    
    <!-- Advertising Performance Section -->
    <div style="' . $section_style . '"><i class="fas fa-bullseye" style="color: var(--primary-light);"></i> Advertising Performance</div>
    <div class="kpi-grid">
        <div class="card kpi-card rose-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-file-invoice-dollar"></i><span id="kpi_spend_sub">Ad Spend</span></div>
            <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_spend">$0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_spend" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card cyan-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-reply-all"></i><span id="kpi_roas_sub">ROAS</span></div>
            <div class="kpi-icon"><i class="fas fa-chart-area"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_roas">0.00</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_roas" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card yellow-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-stethoscope"></i><span id="kpi_acos_sub">ACOS</span></div>
            <div class="kpi-icon"><i class="fas fa-percent"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_acos">0.00%</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_acos" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card purple-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-calculator"></i><span id="kpi_tacos_sub">TACOS</span></div>
            <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_tacos">0.00%</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_tacos" class="cmp-tag"></span>
        </div>
        </div>
    </div>
    
    <!-- Traffic and Conversion Section -->
    <div style="' . $section_style . '"><i class="fas fa-users" style="color: var(--primary-light);"></i> Traffic And Conversion</div>
    <div class="kpi-grid">
        <div class="card kpi-card blue-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-globe"></i><span id="kpi_sessions_t_sub">Sessions</span></div>
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_sessions_t">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_sessions_t" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card indigo-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-check-circle"></i><span id="kpi_orders_sub">Orders</span></div>
            <div class="kpi-icon"><i class="fas fa-box"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_orders">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_orders" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card teal-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-shopping-cart"></i><span id="kpi_units_sub">Units Sold</span></div>
            <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_units">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_units" class="cmp-tag"></span>
        </div>
        </div>
        
        <div class="card kpi-card teal-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-percentage"></i><span id="kpi_conversion_sub">Conversion Rate</span></div>
            <div class="kpi-icon"><i class="fas fa-rocket"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_conversion">0.00%</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_conv" class="cmp-tag"></span>
        </div>
        </div>
    </div>';

// Replace the grid properly
$start_kpi = strpos($content, '<div id="tab_kpi"');
$start_grid = strpos($content, '<div class="kpi-grid">', $start_kpi);
$end_kpi = strpos($content, '<!-- KPI Trend Comparison (Moved Up) -->');

if ($start_grid !== false && $end_kpi !== false) {
    $content = substr_replace($content, $new_tab_kpi_html . "\n    ", $start_grid, $end_kpi - $start_grid);
}

// Remove the Sessions card from tab_traffic VERY SAFELY
$traffic_tab_start = strpos($content, '<!-- SALES & TRAFFIC TAB -->');
$traffic_grid_start = strpos($content, '<div class="kpi-grid">', $traffic_tab_start);
$traffic_grid_end = strpos($content, '</div>', $traffic_grid_start);

// Find the precise session card block in traffic tab
$session_card_str = '        <div class="card kpi-card blue-theme">
            <div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="fas fa-globe"></i><span id="">Store Traffic</span></div>
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="kpi_sessions_t">0</span></h3></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="cmp_sessions_t" class="cmp-tag"></span>
        </div>
        </div>';

$content = str_replace($session_card_str, '', $content);

file_put_contents($file, $content);
echo "Done reorganizing KPIs safely.\n";
?>
