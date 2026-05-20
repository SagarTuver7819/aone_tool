<?php
$file = 'c:/xampp/htdocs/sagar/backup_aone/api/dashboard_data.php';
$content = file_get_contents($file);

// Inject cmp date variables
$pattern1 = "/\\\$prev_to = date\\('Y-m-d', strtotime\\(\\\$from_date \\. ' -1 day'\\)\\);\\s*\\\$prev_from = date\\('Y-m-d', strtotime\\(\\\$prev_to \\. ' -' \\. \\(\\\$diff_days - 1\\) \\. ' days'\\)\\);/s";
$replacement1 = "$0
    \$cmp_start_1 = \$prev_from;
    \$cmp_end_1 = \$prev_to;
    \$cmp_start_2 = \$from_date;
    \$cmp_end_2 = \$to_date;";

$content = preg_replace($pattern1, $replacement1, $content);

$pattern2 = "/\\\$split_start_end = date\\('Y-m-d', strtotime\\(\\\$mid_date \\. \" - 1 day\"\\)\\);/s";
$replacement2 = "$0
        \$cmp_start_1 = \$from_date;
        \$cmp_end_1 = \$split_start_end;
        \$cmp_start_2 = \$mid_date;
        \$cmp_end_2 = \$to_date;";

$content = preg_replace($pattern2, $replacement2, $content);


// Replace the comparisons block
$pattern3 = "/\\\$comparisons = \\[.*?\\];/s";
$replacement3 = "
    // --- Additional Advertising & Transaction Data for Comparisons ---
    \$sql_ads_cmp = \"SELECT SUM(spend) as spend, SUM(total_sales) as ad_sales 
                    FROM (
                        SELECT spend, total_sales FROM amazon_advertising_sp WHERE \$where_customer AND report_date BETWEEN ? AND ? AND report_type = 'general'
                        UNION ALL
                        SELECT spend, total_sales FROM amazon_advertising_sb WHERE \$where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                        UNION ALL
                        SELECT spend, total_sales FROM amazon_advertising_sd WHERE \$where_customer AND report_date BETWEEN ? AND ? AND report_type = 'campaign'
                    ) as t\";
    \$stmt_ads_cmp = \$conn->prepare(\$sql_ads_cmp);
    
    \$stmt_ads_cmp->bind_param(\"ssssss\", \$cmp_start_1, \$cmp_end_1, \$cmp_start_1, \$cmp_end_1, \$cmp_start_1, \$cmp_end_1);
    \$stmt_ads_cmp->execute();
    \$ads_prev = \$stmt_ads_cmp->get_result()->fetch_assoc();
    
    \$stmt_ads_cmp->bind_param(\"ssssss\", \$cmp_start_2, \$cmp_end_2, \$cmp_start_2, \$cmp_end_2, \$cmp_start_2, \$cmp_end_2);
    \$stmt_ads_cmp->execute();
    \$ads_curr = \$stmt_ads_cmp->get_result()->fetch_assoc();

    \$ad_spend_prev = floatval(\$ads_prev['spend'] ?? 0);
    \$ad_sales_prev = floatval(\$ads_prev['ad_sales'] ?? 0);
    \$ad_spend_curr = floatval(\$ads_curr['spend'] ?? 0);
    \$ad_sales_curr = floatval(\$ads_curr['ad_sales'] ?? 0);

    \$sales_prev = floatval(\$totals_prev['total_sales'] ?? 0);
    \$sales_curr = floatval(\$cmp_current['total_sales'] ?? 0);

    \$organic_prev = max(0, \$sales_prev - \$ad_sales_prev);
    \$organic_curr = max(0, \$sales_curr - \$ad_sales_curr);

    \$acos_prev = \$ad_sales_prev > 0 ? (\$ad_spend_prev / \$ad_sales_prev) * 100 : 0;
    \$acos_curr = \$ad_sales_curr > 0 ? (\$ad_spend_curr / \$ad_sales_curr) * 100 : 0;

    \$tacos_prev = \$sales_prev > 0 ? (\$ad_spend_prev / \$sales_prev) * 100 : 0;
    \$tacos_curr = \$sales_curr > 0 ? (\$ad_spend_curr / \$sales_curr) * 100 : 0;

    \$roas_prev = \$ad_spend_prev > 0 ? (\$ad_sales_prev / \$ad_spend_prev) : 0;
    \$roas_curr = \$ad_spend_curr > 0 ? (\$ad_sales_curr / \$ad_spend_curr) : 0;
    
    // Reverse direction for ACoS and TACoS (lower is better)
    function getComparisonRev(\$curr, \$prev) {
        \$c = floatval(\$curr ?? 0);
        \$p = floatval(\$prev ?? 0);
        if (\$p <= 0) return ['pct' => 0, 'dir' => 'none'];
        \$diff = \$c - \$p;
        return [
            'pct' => number_format(abs((\$diff / \$p) * 100), 1),
            'dir' => \$diff < 0 ? 'up' : (\$diff > 0 ? 'down' : 'none')
        ];
    }

    \$comparisons = [
        'sales' => getComparison(\$cmp_current['total_sales'], \$totals_prev['total_sales']),
        'orders' => getComparison(\$cmp_current['total_orders'], \$totals_prev['total_orders']),
        'units' => getComparison(\$cmp_current['total_units'], \$totals_prev['total_units']),
        'dsr' => getComparison(\$cmp_current['total_sales'], \$totals_prev['total_sales']), // Identical percentage to sales
        'ad_sales' => getComparison(\$ad_sales_curr, \$ad_sales_prev),
        'organic' => getComparison(\$organic_curr, \$organic_prev),
        'spend' => getComparisonRev(\$ad_spend_curr, \$ad_spend_prev), // Lower spend is technically better, or up if spend increases? Usually spend up is 'down' in green/red context
        'acos' => getComparisonRev(\$acos_curr, \$acos_prev),
        'tacos' => getComparisonRev(\$tacos_curr, \$tacos_prev),
        'roas' => getComparison(\$roas_curr, \$roas_prev),
        'conv' => getComparison(\$cmp_current['avg_conversion'] ?? 0, \$totals_prev['avg_conversion'] ?? 0),
        'refunds' => getComparisonRev(\$cmp_current['avg_refund_rate'] ?? 0, \$totals_prev['avg_refund_rate'] ?? 0),
        'b2b' => getComparison(\$cmp_current['b2b_sales'] ?? 0, \$totals_prev['b2b_sales'] ?? 0)
    ];";

$content = preg_replace($pattern3, $replacement3, $content);
file_put_contents($file, $content);
echo "Done";
?>
