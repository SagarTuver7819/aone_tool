<?php
$file = 'c:/xampp/htdocs/sagar/backup_aone/modules/dashboard/index.php';
$content = file_get_contents($file);

// Find the start and end of the <div class="kpi-grid"> inside <!-- KPI TAB -->
$start_pos = strpos($content, '<div class="kpi-grid">', strpos($content, '<!-- KPI TAB -->'));
$end_pos = strpos($content, '<!-- KPI Trend Comparison (Moved Up) -->');

if ($start_pos !== false && $end_pos !== false) {
    $grid_html = substr($content, $start_pos, $end_pos - $start_pos);
    
    // Extract cards by looking for <div class="card kpi-card
    $cards = explode('<div class="card kpi-card', $grid_html);
    array_shift($cards); // Remove the part before the first card
    
    $card_map = [];
    foreach ($cards as $card_content) {
        $full_card = '<div class="card kpi-card' . $card_content;
        // Trim any trailing </div></div> wrappers from the grid itself that got caught
        $card_end = strpos($full_card, '</div>', strpos($full_card, 'cmp-tag"></span>')) + 12;
        $full_card = substr($full_card, 0, $card_end) . "\n        </div>\n";
        
        if (strpos($full_card, 'kpi_sales_sub') !== false) $card_map['sales'] = $full_card;
        elseif (strpos($full_card, 'kpi_orders_sub') !== false) $card_map['orders'] = $full_card;
        elseif (strpos($full_card, 'kpi_units_sub') !== false) $card_map['units'] = $full_card;
        elseif (strpos($full_card, 'kpi_dsr_sub') !== false) $card_map['dsr'] = $full_card;
        elseif (strpos($full_card, 'kpi_ad_sales_sub') !== false) $card_map['ad_sales'] = $full_card;
        elseif (strpos($full_card, 'kpi_organic_sub') !== false) $card_map['organic'] = $full_card;
        elseif (strpos($full_card, 'kpi_spend_sub') !== false) $card_map['spend'] = $full_card;
        elseif (strpos($full_card, 'kpi_acos_sub') !== false) $card_map['acos'] = $full_card;
        elseif (strpos($full_card, 'kpi_tacos_sub') !== false) $card_map['tacos'] = $full_card;
        elseif (strpos($full_card, 'kpi_roas_sub') !== false) $card_map['roas'] = $full_card;
        elseif (strpos($full_card, 'kpi_conversion_sub') !== false) $card_map['conv'] = $full_card;
        elseif (strpos($full_card, 'kpi_net_profit_sub') !== false) $card_map['net'] = $full_card;
    }
    
    $section_style = "text-transform: capitalize; font-size: 0.85rem; font-weight: 800; color: #475569; letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem 0.5rem; display: flex; align-items: center; gap: 8px;";
    
    $new_html = "
    <!-- Sales Volume Section -->
    <div style=\"$section_style\"><i class=\"fas fa-chart-line\" style=\"color: var(--primary-light);\"></i> Sales Volume</div>
    <div class=\"kpi-grid\">
        " . ($card_map['sales'] ?? '') . "
        " . ($card_map['orders'] ?? '') . "
        " . ($card_map['units'] ?? '') . "
        " . ($card_map['dsr'] ?? '') . "
        " . ($card_map['organic'] ?? '') . "
        " . ($card_map['net'] ?? '') . "
    </div>
    
    <!-- Advertising Performance Section -->
    <div style=\"$section_style\"><i class=\"fas fa-bullseye\" style=\"color: var(--primary-light);\"></i> Advertising Performance</div>
    <div class=\"kpi-grid\">
        " . ($card_map['ad_sales'] ?? '') . "
        " . ($card_map['spend'] ?? '') . "
        " . ($card_map['acos'] ?? '') . "
        " . ($card_map['tacos'] ?? '') . "
        " . ($card_map['roas'] ?? '') . "
    </div>
    
    <!-- Traffic & Conversion Section -->
    <div style=\"$section_style\"><i class=\"fas fa-users\" style=\"color: var(--primary-light);\"></i> Traffic & Conversion</div>
    <div class=\"kpi-grid\">
        " . ($card_map['conv'] ?? '') . "
    </div>
    
    ";
    
    $content = substr_replace($content, $new_html, $start_pos, $end_pos - $start_pos);
    file_put_contents($file, $content);
    echo "Done structuring sections.\n";
} else {
    echo "Could not find grid boundaries.\n";
}
?>
