<?php
$file = 'c:/xampp/htdocs/sagar/backup_aone/api/dashboard_data.php';
$content = file_get_contents($file);

// Replace the comparisons block again to add net_profit
$pattern = "/\\\$comparisons = \\[.*?\\];/s";
$replacement = "$0

    // Net Profit Comparison
    \$sql_trans_cmp = \"SELECT SUM(total) as net FROM amazon_transaction_report WHERE \$where_customer AND date_time BETWEEN ? AND ? AND type != 'Transfer'\";
    \$stmt_trans_cmp = \$conn->prepare(\$sql_trans_cmp);
    
    \$dt_start_1 = \$cmp_start_1 . ' 00:00:00';
    \$dt_end_1 = \$cmp_end_1 . ' 23:59:59';
    \$stmt_trans_cmp->bind_param(\"ss\", \$dt_start_1, \$dt_end_1);
    \$stmt_trans_cmp->execute();
    \$net_prev = floatval(\$stmt_trans_cmp->get_result()->fetch_assoc()['net'] ?? 0);
    
    \$dt_start_2 = \$cmp_start_2 . ' 00:00:00';
    \$dt_end_2 = \$cmp_end_2 . ' 23:59:59';
    \$stmt_trans_cmp->bind_param(\"ss\", \$dt_start_2, \$dt_end_2);
    \$stmt_trans_cmp->execute();
    \$net_curr = floatval(\$stmt_trans_cmp->get_result()->fetch_assoc()['net'] ?? 0);
    
    // Net profit roughly = trans total - cogs - ad spend
    // We already have ad_spend_prev and ad_spend_curr. We ignore COGS for rough comparison if it's monthly.
    \$net_profit_prev = \$net_prev - \$ad_spend_prev;
    \$net_profit_curr = \$net_curr - \$ad_spend_curr;
    
    \$comparisons['net_profit'] = getComparison(\$net_profit_curr, \$net_profit_prev);
";

$content = preg_replace($pattern, $replacement, $content);
file_put_contents($file, $content);
?>
