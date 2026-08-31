<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

function formatReportTypeBadge($type)
{
    $formatted = htmlspecialchars($type);
    if (strpos($type, 'Custom(') === 0) {
        $formatted = str_replace('Custom(', 'Custom (', $formatted);
    }
    return '<span class="ds-badge-type">' . $formatted . '</span>';
}

function getContributedDataSummary($row, $conn)
{
    $customerId = $row['customer_id'];
    $reportDate = $row['report_date'];
    $monthStart = date('Y-m-01', strtotime($reportDate));
    $monthEnd = date('Y-m-t', strtotime($reportDate));

    $type = $row['report_type'];

    if ($type === 'Business') {
        $sql = "SELECT SUM(ordered_product_sales) as sales, SUM(units_ordered) as units, SUM(total_order_items) as orders 
                FROM amazon_business_report 
                WHERE customer_id = ? AND report_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['orders'] > 0) {
            return '<div class="ds-summary-lines">'
                . '<div class="ds-line"><span class="ds-lbl">Sales :</span> <span class="ds-val">$' . number_format($res['sales'] ?? 0, 2) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Units :</span> <span class="ds-val">' . number_format($res['units'] ?? 0) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Orders :</span> <span class="ds-val">' . number_format($res['orders'] ?? 0) . '</span></div>'
                . '</div>';
        }
    } elseif ($type === 'Transaction') {
        $sql = "SELECT SUM(product_sales) as sales, SUM(fba_fees) as fba, SUM(selling_fees) as selling, SUM(total) as total 
                FROM amazon_transaction_report 
                WHERE customer_id = ? AND date_time BETWEEN ? AND ?";
        $monthStartDT = $monthStart . " 00:00:00";
        $monthEndDT = $monthEnd . " 23:59:59";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStartDT, $monthEndDT);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && ($res['sales'] != 0 || $res['fba'] != 0)) {
            return '<div class="ds-summary-lines">'
                . '<div class="ds-line"><span class="ds-lbl">Gross Sales :</span> <span class="ds-val">$' . number_format($res['sales'] ?? 0, 2) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">FBA Fees :</span> <span class="ds-val">$' . number_format($res['fba'] ?? 0, 2) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Selling Fees :</span> <span class="ds-val">$' . number_format($res['selling'] ?? 0, 2) . '</span></div>'
                . '</div>';
        }
    } elseif ($type === 'Detail') {
        $sql = "SELECT SUM(sessions_total) as sessions, SUM(page_views_total) as page_views 
                FROM amazon_detail_report 
                WHERE customer_id = ? AND report_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $customerId, $reportDate);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['sessions'] > 0) {
            return '<div class="ds-summary-lines">'
                . '<div class="ds-line"><span class="ds-lbl">Sessions :</span> <span class="ds-val">' . number_format($res['sessions']) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Page Views :</span> <span class="ds-val">' . number_format($res['page_views']) . '</span></div>'
                . '</div>';
        }
    } elseif (strpos($type, 'Ads') === 0) {
        $tableName = 'amazon_advertising_sp';
        if (strpos($type, 'Ads SB') === 0)
            $tableName = 'amazon_advertising_sb';
        elseif (strpos($type, 'Ads SD') === 0)
            $tableName = 'amazon_advertising_sd';

        $sql = "SELECT SUM(impressions) as impr, SUM(clicks) as clicks, SUM(spend) as spend, SUM(total_sales) as sales 
                FROM `$tableName` 
                WHERE customer_id = ? AND report_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['clicks'] > 0) {
            return '<div class="ds-summary-lines">'
                . '<div class="ds-line"><span class="ds-lbl">Spend :</span> <span class="ds-val">$' . number_format($res['spend'] ?? 0, 2) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Ad Sales :</span> <span class="ds-val">' . number_format($res['sales'] ?? 0, 2) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Clicks :</span> <span class="ds-val">' . number_format($res['clicks']) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Impr :</span> <span class="ds-val">' . number_format($res['impr']) . '</span></div>'
                . '</div>';
        }
    } elseif ($type === 'Inventory') {
        $sql = "SELECT COUNT(DISTINCT sku) as total_skus, SUM(afn_fulfillable_quantity) as afn_qty, SUM(mfn_fulfillable_quantity) as mfn_qty 
                FROM amazon_inventory 
                WHERE customer_id = ? AND report_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $customerId, $reportDate);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['total_skus'] > 0) {
            return '<div class="ds-summary-lines">'
                . '<div class="ds-line"><span class="ds-lbl">SKUs Active :</span> <span class="ds-val">' . number_format($res['total_skus']) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Fulfillable Qty :</span> <span class="ds-val">' . number_format(($res['afn_qty'] ?? 0) + ($res['mfn_qty'] ?? 0)) . '</span></div>'
                . '</div>';
        }
    } elseif ($type === 'Reimbursement') {
        $sql = "SELECT SUM(amount) as sales, SUM(quantity) as units 
                FROM amazon_returns_reimbursements 
                WHERE customer_id = ? AND type = 'Reimbursement' AND report_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['sales'] > 0) {
            return '<div class="ds-summary-lines">'
                . '<div class="ds-line"><span class="ds-lbl">Reimbursed :</span> <span class="ds-val">$' . number_format($res['sales'] ?? 0, 2) . '</span></div>'
                . '<div class="ds-line"><span class="ds-lbl">Units :</span> <span class="ds-val">' . number_format($res['units'] ?? 0) . '</span></div>'
                . '</div>';
        }
    }

    return "<span class='ds-processed'>Processed in database</span>";
}

// Check auth
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$session_customer_id = $_SESSION['customer_id'] ?? 0;
$user_role = $_SESSION['role'] ?? 'customer';

$customers = get_all_customers();

$page_title = "Data Source Tracking Log";
$page_subtitle = "Detailed audit trail of uploaded reports, ZIP archives, and database ingestion counts.";

include '../../includes/header.php';
include '../../includes/sidebar.php';

// Pagination and filters
$limit = isset($_GET['limit']) ? max(5, intval($_GET['limit'])) : 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$selected_customer = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
if ($user_role === 'customer') {
    $selected_customer = $session_customer_id;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$report_type = isset($_GET['report_type']) ? trim($_GET['report_type']) : '';
$sidebar_menu = isset($_GET['sidebar_menu']) ? trim($_GET['sidebar_menu']) : '';
$selected_section = isset($_GET['page_section']) ? trim($_GET['page_section']) : '';

$menu_mapping = [
    'overview' => [
        'label' => 'Overview Dashboard',
        'types' => ['Business', 'Transaction'],
        'description' => 'Calculates Total Revenue, Organic Sales, Ad Sales, Orders, Daily Sales Rate, and overall KPIs.',
        'target_file' => 'modules/dashboard/index.php?tab=kpi',
        'sections' => [
            'revenue_breakdown' => [
                'label' => 'Revenue Breakdown (Cards: Revenue, Organic, Ad Sales, DSR)',
                'types' => ['Business', 'Transaction'],
                'description' => 'Calculates gross orders, organic, and PPC ad sales using both processed order reports and monthly transaction data.'
            ],
            'advertising_performance' => [
                'label' => 'Advertising Performance (Spend, ROAS, ACOS, TACOS)',
                'types' => ['Ads'],
                'description' => 'Tracks Amazon Sponsor Products, Brand, and Display ad spend and attributes sales/ROAS accordingly.'
            ],
            'traffic_conversion' => [
                'label' => 'Traffic And Conversion (Sessions, Orders, Units, Conv. Rate)',
                'types' => ['Detail', 'Business'],
                'description' => 'Renders detail page traffic statistics, customer sessions, and overall units/orders conversion rates.'
            ],
            'kpi_trend' => [
                'label' => 'KPI Trend - 3-Month Comparison Table',
                'types' => ['Business', 'Transaction'],
                'description' => 'Compares historical metrics side-by-side for last three months using Business and Transaction logs.'
            ],
            'daily_perf_chart' => [
                'label' => 'Daily Performance Trends Chart',
                'types' => ['Business', 'Detail', 'Transaction', 'Ads'],
                'description' => 'Populates the daily timeline charts for Sales, Orders, Units, Page Views, Sessions, and Refunds.'
            ]
        ]
    ],
    'profit_fees' => [
        'label' => 'Profit & Fees',
        'types' => ['Transaction'],
        'description' => 'Calculates Gross Profits, Net Payouts, FBA fees, referral fees, shipping/gift wrap credits, and expense deductions.',
        'target_file' => 'modules/dashboard/index.php?tab=financial',
        'sections' => [
            'gross_net_payout' => [
                'label' => 'Profit Metrics (Gross Revenue, FBA Fees, Selling Fees, Est. Net Payout)',
                'types' => ['Transaction'],
                'description' => 'Calculates total payouts, net margins, and gross profit distributions from transaction settlement details.'
            ],
            'payout_distribution_chart' => [
                'label' => 'Estimated Payout Distribution Chart',
                'types' => ['Transaction'],
                'description' => 'Visualizes fee ratios (referral, fulfillment, promo rebates) against gross sales in a stacked chart format.'
            ],
            'pnl_statement' => [
                'label' => 'P&L Statement Table',
                'types' => ['Transaction'],
                'description' => 'Provides line-item accounting of all Amazon fee types, gross sales, shipping collections, and cost adjustments.'
            ],
            'pnl_sku_table' => [
                'label' => 'SKU Level Profit & Loss Table',
                'types' => ['Transaction'],
                'description' => 'Extracts individual product sales, specific SKU referral commissions, and FBA storage fees.'
            ]
        ]
    ],
    'products' => [
        'label' => 'Product Performance',
        'types' => ['Detail', 'Business'],
        'description' => 'Renders page views, mobile sessions, unit session percentages (conversion rates), and sales broken down by parent/child ASINs.',
        'target_file' => 'modules/dashboard/index.php?tab=products',
        'sections' => [
            'asin_performance' => [
                'label' => 'ASIN performance (Sessions, Page Views, Buy Box %)',
                'types' => ['Detail'],
                'description' => 'Extracts session counts, buy box ownership ratios, and conversion details broken down by ASIN.'
            ],
            'asin_sales' => [
                'label' => 'ASIN sales (Sales, Units, Orders)',
                'types' => ['Business'],
                'description' => 'Maps product sales, ordered units, and item order counts to individual ASINs.'
            ],
            'historical_asin_chart' => [
                'label' => 'Historical Monthly ASIN Analysis Table & Chart',
                'types' => ['Detail', 'Business'],
                'description' => 'Combines month-over-month detail session traffic with Business sales trends for a selected ASIN.'
            ]
        ]
    ],
    'advertising' => [
        'label' => 'Advertising Overview',
        'types' => ['Ads'],
        'description' => 'Tracks impressions, PPC clicks, ad spend, ACoS, TACoS, ROAS, click-through rates (CTR), and placement statistics.',
        'target_file' => 'modules/amazon_ads/index.php',
        'sections' => [
            'ad_performance_cards' => [
                'label' => 'Ad Performance Cards (Total Ad Sales, Spend, TACOS, ROAS)',
                'types' => ['Ads'],
                'description' => 'Displays total PPC impressions, clicks, CTR, ROAS, and ACOS metrics for the given period.'
            ],
            'ad_type_performance' => [
                'label' => 'Ad Type Performance (Sponsored Products, Brands & Display Mini-Grid)',
                'types' => ['Ads'],
                'description' => 'Groups advertising metrics by Sponsored Products (SP), Sponsored Brands (SB), and Sponsored Display (SD).'
            ],
            'daily_ad_trends_chart' => [
                'label' => 'Ad Spend vs Sales Trend & Bar Charts',
                'types' => ['Ads'],
                'description' => 'Plots daily PPC ad spend, clicks, impressions, and ad sales over time.'
            ],
            'heatmap' => [
                'label' => 'Spends vs Sales Heatmap',
                'types' => ['Ads'],
                'description' => 'Visualizes PPC spend and ad sales intensity by day of week versus hour of day.'
            ]
        ]
    ],
    'campaign_performance' => [
        'label' => 'Campaign & Target Analysis',
        'types' => ['Ads'],
        'description' => 'Tracks metrics at the individual campaign level, including SP, SB, and SD campaigns.',
        'target_file' => 'modules/amazon_ads/campaign_performance.php',
        'sections' => [
            'campaign_performance_table' => [
                'label' => 'All Campaigns Performance Table',
                'types' => ['Ads'],
                'description' => 'Lists ad spend, ad sales, orders, ACOS, and impressions for all active campaigns.'
            ],
            'campaign_performance_chart' => [
                'label' => 'Ad Spend vs Ad Sales Campaign Comparison Chart',
                'types' => ['Ads'],
                'description' => 'Displays campaign-level spend against generated ad sales using SP, SB, and SD logs.'
            ]
        ]
    ],
    'brand' => [
        'label' => 'Brand Analytics',
        'types' => ['Brand brand search query', 'Brand brand repeat purchase'],
        'description' => 'Analyzes brand search query frequency, brand impression/click share, and repeat customer acquisition.',
        'target_file' => 'modules/amazon_ads/brand_analytics.php',
        'sections' => [
            'search_query_performance' => [
                'label' => 'Search Query Performance Summary & Search Volume Charts',
                'types' => ['Brand brand search query'],
                'description' => 'Analyzes search queries frequency, impression share, click shares, and cart additions for brand keywords.'
            ],
            'repeat_purchase' => [
                'label' => 'Repeat Purchase Behavior Statistics',
                'types' => ['Brand brand repeat purchase'],
                'description' => 'Identifies repeating customer cohorts, repeat unit counts, and order statistics.'
            ]
        ]
    ],
    'reimbursements' => [
        'label' => 'Reimbursements',
        'types' => ['Reimbursements', 'Transaction'],
        'description' => 'Tracks recovered revenue, units recovered, and case tracker for refunds/reimbursements.',
        'target_file' => 'modules/reimbursements/index.php',
        'sections' => [
            'overview' => [
                'label' => 'Executive Recovery Overview (Cards: Total, Units, Rate, Pending)',
                'types' => ['Reimbursements', 'Transaction'],
                'description' => 'Displays total cash recovered, recovery rate, units reimbursed, and pending values.'
            ],
            'trend' => [
                'label' => 'Reimbursement Value Trend Chart',
                'types' => ['Reimbursements', 'Transaction'],
                'description' => 'Plots timeline charts of aggregate reimbursed funds over the selected period.'
            ],
            'reasons' => [
                'label' => 'Reimbursement Reason Analysis',
                'types' => ['Reimbursements', 'Transaction'],
                'description' => 'Analyzes return vs warehouse loss reasons for reimbursed funds.'
            ],
            'leaderboard' => [
                'label' => 'Product Recovery Leaderboard',
                'types' => ['Reimbursements', 'Transaction'],
                'description' => 'Ranks top SKUs by total reimbursement value and recovery efficiency.'
            ],
            'cases' => [
                'label' => 'Case Recovery Tracker',
                'types' => ['Reimbursements', 'Transaction'],
                'description' => 'Audit logs of individual reimbursement cases, case IDs, and statuses.'
            ]
        ]
    ],
    'returns' => [
        'label' => 'Return Page',
        'types' => ['Returns', 'Transaction'],
        'description' => 'Tracks return statistics, return reasons distribution, daily/monthly trend and product performance.',
        'target_file' => 'modules/returns/index.php',
        'sections' => [
            'kpis' => [
                'label' => 'Return Performance KPIs (Total, Sellable, Damaged, Defect Rate)',
                'types' => ['Returns', 'Transaction'],
                'description' => 'Tracks return counts, sellable vs damaged units, top reasons, and critical defect rates.'
            ],
            'reasons' => [
                'label' => 'Return Reasons Chart',
                'types' => ['Returns', 'Transaction'],
                'description' => 'Visualizes buyer return reasons (defective, unwanted, etc.) using returns report data.'
            ],
            'trend' => [
                'label' => 'Time Trend Analysis',
                'types' => ['Returns', 'Transaction'],
                'description' => 'Plots daily and monthly trends of returned units over time.'
            ],
            'products' => [
                'label' => 'Product Performance Table',
                'types' => ['Returns', 'Transaction'],
                'description' => 'Ranks products and SKUs by return frequency and sellable ratio.'
            ]
        ]
    ],
];

// Build Query
$where_clauses = [];
if ($selected_customer > 0) {
    $where_clauses[] = "f.customer_id = " . $selected_customer;
}
if ($search !== '') {
    $esc_search = $conn->real_escape_string($search);
    $where_clauses[] = "(f.filename LIKE '%$esc_search%' OR f.zip_filename LIKE '%$esc_search%')";
}
if ($report_type !== '') {
    $esc_type = $conn->real_escape_string($report_type);
    $where_clauses[] = "f.report_type = '$esc_type'";
}

if ($sidebar_menu !== '' && isset($menu_mapping[$sidebar_menu])) {
    $current_menu = $menu_mapping[$sidebar_menu];
    $mapped_types = $current_menu['types'];

    if ($selected_section !== '' && isset($current_menu['sections'][$selected_section])) {
        $mapped_types = $current_menu['sections'][$selected_section]['types'];
    }

    $type_clauses = [];
    foreach ($mapped_types as $t) {
        if ($t === 'Ads') {
            $type_clauses[] = "f.report_type LIKE 'Ads%'";
        } elseif ($t === 'Brand') {
            $type_clauses[] = "f.report_type LIKE 'Brand%'";
        } else {
            $esc_t = $conn->real_escape_string($t);
            $type_clauses[] = "f.report_type LIKE '$esc_t%'";
        }
    }
    if (count($type_clauses) > 0) {
        $where_clauses[] = "(" . implode(" OR ", $type_clauses) . ")";
    }
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Count total queries
$count_sql = "SELECT COUNT(*) as total FROM file_upload_log f $where_sql";
$count_res = $conn->query($count_sql);
$total_rows = $count_res ? $count_res->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_rows / $limit);

// Fetch data
$sql = "SELECT f.*, c.customer_name, u.username 
        FROM file_upload_log f
        JOIN customers c ON f.customer_id = c.id
        LEFT JOIN users u ON f.uploaded_by = u.id
        $where_sql
        ORDER BY f.uploaded_at DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<style>
    body {
        background-color: #F8FAFC !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        color: #0F172A;
    }

    .top-header {
        display: none !important;
    }

    .main-wrapper {
        padding: 1.25rem 2rem 2rem 2rem !important;
        overflow-x: hidden;
    }

    .ds-container {
        padding: 0;
        width: 100%;
        max-width: 100%;
        margin: 0;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    /* Topbar styling - Clean Transparent Header matching Figma */
    .figma-page-topbar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 0.75rem !important;
        flex-wrap: nowrap !important;
        padding: 0.25rem 0 1rem 0 !important;
        background: transparent !important;
        border: none !important;
        border-bottom: 1px solid #EAECEF !important;
        border-radius: 0 !important;
        margin-bottom: 1.25rem !important;
        box-shadow: none !important;
        width: 100%;
    }

    .figma-page-topbar-left {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .figma-select-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .figma-select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        min-width: 170px;
        padding: 0.45rem 2.2rem 0.45rem 0.85rem;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1E2238;
        background: #FFFFFF;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .figma-select-wrapper select:focus {
        border-color: #4362CE;
    }

    .figma-select-wrapper .select-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        width: 12px;
        height: 12px;
    }

    .figma-page-breadcrumb {
        font-size: 0.82rem;
        font-weight: 500;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .figma-page-breadcrumb .breadcrumb-dot {
        margin: 0 3px;
        opacity: 0.4;
        font-size: 0.9rem;
    }

    .figma-page-breadcrumb strong {
        color: #1E293B;
        font-weight: 600;
    }

    .figma-page-topbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-figma-primary {
        background: #4362CE !important;
        color: #FFFFFF !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1.15rem !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        box-shadow: 0px 4px 10px rgba(67, 98, 206, 0.2) !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-primary:hover {
        background: #3452BA !important;
        transform: translateY(-1px);
        color: #FFFFFF !important;
    }

    .btn-figma-outline-sm {
        background: #F1F4F9 !important;
        color: #363B4F !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1.05rem !important;
        font-size: 0.82rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.45rem !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-outline-sm:hover {
        background: #E2E8F0 !important;
        color: #0F172A !important;
    }

    .btn-figma-icon-sm {
        width: 38px !important;
        height: 38px !important;
        border-radius: 50% !important;
        background: #F1F4F9 !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #475569 !important;
        cursor: pointer !important;
        position: relative !important;
        transition: all 0.2s ease !important;
    }

    .btn-figma-icon-sm:hover {
        background: #E2E8F0 !important;
        color: #0F172A !important;
    }

    .btn-figma-icon-sm .notif-badge {
        position: absolute;
        top: 9px;
        right: 9px;
        width: 6px;
        height: 6px;
        background: #EE473D;
        border-radius: 50%;
        border: 1.5px solid #F1F4F9;
    }

    /* Page Header */
    .ds-page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 1.25rem;
        width: 100%;
    }

    .ds-page-title h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .ds-page-title p {
        font-size: 0.82rem;
        color: #64748B;
        font-weight: 500;
        margin: 3px 0 0 0;
    }

    .ds-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ds-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .ds-select {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        height: 38px;
        padding: 0 32px 0 12px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #0F172A;
        outline: none;
        cursor: pointer;
        min-width: 170px;
        appearance: none;
        -webkit-appearance: none;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        transition: border-color 0.15s ease;
    }

    .ds-select:focus {
        border-color: #4362CE;
    }

    .ds-select-wrap i.chevron-icon {
        position: absolute;
        right: 12px;
        pointer-events: none;
        font-size: 0.7rem;
        color: #64748B;
    }

    .btn-ds-refresh {
        width: 38px;
        height: 38px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        background: #FFFFFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        text-decoration: none;
    }

    .btn-ds-refresh:hover {
        background: #F8FAFC;
        color: #0F172A;
        border-color: #CBD5E1;
    }

    /* Main Table Card */
    .ds-card {
        background: #FFFFFF;
        border: 1px solid #EAECEF;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        width: 100%;
    }

    .ds-card-head {
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #F1F5F9;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .ds-card-title {
        font-size: 1.08rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
    }

    .ds-head-right {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
    }

    .ds-total-summary {
        font-size: 0.78rem;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .ds-total-summary span {
        color: #4362CE;
        margin-left: 2px;
    }

    .ds-search-box {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .ds-search-box svg {
        position: absolute;
        left: 11px;
        pointer-events: none;
    }

    .ds-search-box input {
        height: 38px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 0 12px 0 34px;
        font-size: 0.8rem;
        font-weight: 500;
        color: #0F172A;
        width: 220px;
        outline: none;
        font-family: inherit;
        background: #FFFFFF;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        transition: border-color 0.15s ease;
    }

    .ds-search-box input:focus {
        border-color: #4362CE;
    }

    /* Table */
    .ds-table-wrap {
        overflow-x: auto;
        width: 100%;
        -webkit-overflow-scrolling: touch;
    }

    .ds-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.82rem;
    }

    .ds-table thead th {
        background: #FFFFFF;
        border-bottom: 1px solid #EAECEF;
        padding: 14px 18px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
    }

    .ds-table tbody td {
        padding: 16px 18px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        color: #0F172A;
    }

    .ds-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ds-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .ds-cust-name {
        font-weight: 600;
        color: #0F172A;
        font-size: 0.82rem;
        line-height: 1.3;
    }

    .ds-badge-type {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.72rem;
        font-weight: 500;
        color: #4362CE;
        background: #EFF4FE;
        border-radius: 6px;
        line-height: 1.35;
        word-break: break-word;
        max-width: 180px;
        text-align: center;
        border: 1px solid rgba(67, 98, 206, 0.08);
    }

    .ds-filename {
        font-weight: 600;
        color: #0F172A;
        margin-bottom: 3px;
        font-size: 0.82rem;
        line-height: 1.3;
    }

    .ds-zipname {
        font-size: 0.74rem;
        color: #64748B;
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 400;
    }

    .ds-month {
        font-weight: 600;
        color: #0F172A;
        margin-bottom: 3px;
        font-size: 0.82rem;
    }

    .ds-time {
        font-size: 0.74rem;
        color: #64748B;
        font-weight: 400;
    }

    .ds-records {
        font-weight: 700;
        font-size: 0.9rem;
        color: #0F172A;
        text-align: center;
    }

    .ds-summary-lines {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ds-line {
        font-size: 0.78rem;
        color: #64748B;
        line-height: 1.3;
    }

    .ds-lbl {
        font-weight: 400;
        color: #64748B;
    }

    .ds-val {
        font-weight: 700;
        color: #0F172A;
    }

    .ds-processed {
        color: #94A3B8;
        font-size: 0.78rem;
        font-weight: 400;
    }

    .ds-agent {
        color: #475569;
        font-weight: 400;
        font-size: 0.8rem;
    }

    /* Footer Pagination */
    .ds-table-foot {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #FFFFFF;
        border-top: 1px solid #F1F5F9;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .ds-foot-left {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.8rem;
        color: #64748B;
        font-weight: 500;
    }

    .ds-entries-select {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #0F172A;
        outline: none;
        cursor: pointer;
    }

    .ds-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ds-page-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748B;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all 0.15s ease;
    }

    .ds-page-btn:hover {
        background: #F1F5F9;
        color: #0F172A;
    }

    .ds-page-btn.active {
        background: #4362CE;
        color: #FFFFFF;
        font-weight: 700;
    }

    .ds-page-btn.disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .ds-page-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .ds-controls {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 768px) {
        .main-wrapper {
            padding: 0.75rem 0.75rem 90px 0.75rem !important;
        }

        .ds-container {
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .figma-page-topbar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            padding-bottom: 0.75rem !important;
        }

        .figma-page-topbar-left {
            width: 100% !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 8px !important;
        }

        .figma-select-wrapper,
        .figma-select-wrapper select {
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .figma-page-breadcrumb {
            display: none !important;
        }

        .figma-page-topbar-right {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            width: 100% !important;
        }

        .figma-page-topbar-right .btn-figma-primary,
        .figma-page-topbar-right .btn-figma-outline-sm {
            flex: 1 !important;
            justify-content: center !important;
            text-align: center !important;
            padding: 0.5rem 0.6rem !important;
            font-size: 0.78rem !important;
        }

        .figma-page-topbar-right .btn-figma-icon-sm {
            flex-shrink: 0 !important;
        }

        .ds-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .ds-select {
            width: 100%;
        }

        .ds-card-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .ds-head-right {
            width: 100%;
            justify-content: space-between;
        }

        .ds-search-box {
            width: 100%;
        }

        .ds-search-box input {
            width: 100%;
        }

        .ds-table-foot {
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }
    }
</style>

<div class="ds-container">

    <!-- Figma-style Top Bar -->
    <div class="figma-page-topbar">
        <div class="figma-page-topbar-left">
            <div class="figma-select-wrapper">
                <select onchange="applyCustomerFilter(this.value)">
                    <option value="">All Amazon Profiles</option>
                    <?php $customers->data_seek(0);
                    while ($c = $customers->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($selected_customer == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['customer_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <img src="<?php echo BASE_URL; ?>assets/icons/Topbar/Down Up Arrow.svg" class="select-icon"
                    alt="Toggle" />
            </div>
            <span class="figma-page-breadcrumb">Dashboard <span class="breadcrumb-dot">•</span> <strong>Data Source
                    Tracking</strong></span>
        </div>
        <div class="figma-page-topbar-right">
            <?php if ($user_role === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>modules/report_upload/index.php" class="btn-figma-primary">
                    <i class="fas fa-plus"></i> New Upload
                </a>
            <?php endif; ?>
            <button type="button" class="btn-figma-outline-sm" onclick="exportCSV()">
                <i class="fas fa-file-export"></i> Export CSV
            </button>
            <button type="button" class="btn-figma-icon-sm" title="Search"><i class="fas fa-search"></i></button>
            <button type="button" class="btn-figma-icon-sm" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge"></span>
            </button>
        </div>
    </div>

    <!-- Page Header (Figma Matching) -->
    <div class="ds-page-head">
        <div class="ds-page-title">
            <h2>Data Source Tracking</h2>
            <p>Manage individual Amazon Seller Profiles and synchronization settings.</p>
        </div>

        <form method="GET" id="filter-form" class="ds-controls">
            <?php if ($selected_customer > 0): ?>
                <input type="hidden" name="customer_id" value="<?php echo $selected_customer; ?>">
            <?php endif; ?>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="limit" value="<?php echo $limit; ?>">

            <!-- Sidebar Menu Select -->
            <div class="ds-select-wrap">
                <select name="sidebar_menu" id="sidebar_menu_select" class="ds-select"
                    onchange="updateSectionsDropdown(); document.getElementById('filter-form').submit();">
                    <option value="">Select Sidebar Menu</option>
                    <?php foreach ($menu_mapping as $key => $mapping): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($sidebar_menu === $key) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mapping['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down chevron-icon"></i>
            </div>

            <!-- Page Section Target Select -->
            <div class="ds-select-wrap">
                <select name="page_section" id="page_section_select" class="ds-select"
                    onchange="document.getElementById('filter-form').submit();">
                    <option value="">Select Page Section</option>
                </select>
                <i class="fas fa-chevron-down chevron-icon"></i>
            </div>

            <!-- Refresh Button -->
            <a href="tracking.php" class="btn-ds-refresh" title="Reset Filters">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.65 6.88A6 6 0 1 0 14 8h-1.5a4.5 4.5 0 1 1-.5-2.02L10 8h5V3l-1.35 1.88z"
                        fill="#64748B" />
                </svg>
            </a>
        </form>
    </div>

    <!-- Main Card: Ingested Files Log -->
    <div class="ds-card">

        <!-- Card Header -->
        <div class="ds-card-head">
            <h3 class="ds-card-title">Ingested Files Log</h3>
            <div class="ds-head-right">
                <div class="ds-total-summary">TOTAL SUMMARY : <span><?php echo $total_rows; ?></span></div>
                <div class="ds-search-box">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="7" cy="7" r="5" stroke="#94A3B8" stroke-width="1.4" />
                        <path d="M11 11L14.5 14.5" stroke="#94A3B8" stroke-width="1.4" stroke-linecap="round" />
                    </svg>
                    <input type="text" id="live_search_input" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search Files (Name / ZIP)"
                        onkeydown="if(event.key==='Enter'){ applySearch(this.value); }">
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="ds-table-wrap">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">Customer</th>
                        <th style="width: 13%;">Report Type</th>
                        <th style="width: 25%;">File Name / ZIP Source</th>
                        <th style="width: 13%;">Month & Timestamp</th>
                        <th style="width: 9%; text-align: center;">Records Ingested</th>
                        <th style="width: 19%;">Ingested Data Summary</th>
                        <th style="width: 7%;">Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <!-- Customer -->
                                <td>
                                    <div class="ds-cust-name"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                                </td>

                                <!-- Report Type -->
                                <td>
                                    <?php echo formatReportTypeBadge($row['report_type']); ?>
                                </td>

                                <!-- File Name / ZIP Source -->
                                <td>
                                    <div class="ds-filename"><?php echo htmlspecialchars($row['filename']); ?></div>
                                    <?php if (!empty($row['zip_filename'])): ?>
                                        <div class="ds-zipname">
                                            <svg width="13" height="13" viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9 1.5H3.5C2.67157 1.5 2 2.17157 2 3V13C2 13.8284 2.67157 14.5 3.5 14.5H12.5C13.3284 14.5 14 13.8284 14 13V6.5L9 1.5Z"
                                                    stroke="#4362CE" stroke-width="1.3" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M9 1.5V6.5H14" stroke="#4362CE" stroke-width="1.3" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M5.5 8.5H7.5M5.5 10.5H7.5" stroke="#4362CE" stroke-width="1.3"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <?php echo htmlspecialchars($row['zip_filename']); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="ds-zipname" style="color: #94A3B8; font-style: italic;">Direct File Upload</div>
                                    <?php endif; ?>
                                </td>

                                <!-- Month & Timestamp -->
                                <td>
                                    <div class="ds-month"><?php echo date('M Y', strtotime($row['report_date'])); ?></div>
                                    <div class="ds-time"><?php echo date('d M • h:i A', strtotime($row['uploaded_at'])); ?>
                                    </div>
                                </td>

                                <!-- Records Ingested -->
                                <td>
                                    <div class="ds-records"><?php echo number_format($row['rows_processed']); ?></div>
                                </td>

                                <!-- Ingested Data Summary -->
                                <td>
                                    <?php echo getContributedDataSummary($row, $conn); ?>
                                </td>

                                <!-- Agent -->
                                <td>
                                    <div class="ds-agent"><?php echo htmlspecialchars($row['username'] ?? 'Admin'); ?></div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding: 4rem 1.5rem; text-align: center; color: #94A3B8;">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1"
                                    stroke-width="1.5" style="margin-bottom: 8px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <div style="font-weight: 600; font-size: 0.9rem;">No ingested file logs found</div>
                                <div style="font-size: 0.78rem; margin-top: 4px;">Try changing the search keywords or filter
                                    selection</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="ds-table-foot">
            <div class="ds-foot-left">
                <span>Show</span>
                <select class="ds-entries-select" onchange="changeLimit(this.value)">
                    <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                </select>
                <span>Entries</span>
                <span style="margin-left: 12px;">
                    Showing <?php echo $total_rows > 0 ? ($offset + 1) : 0; ?> to
                    <?php echo min($offset + $limit, $total_rows); ?> of <?php echo $total_rows; ?> entries
                </span>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="ds-pagination">
                    <!-- Prev -->
                    <a href="?page=<?php echo max(1, $page - 1); ?>&limit=<?php echo $limit; ?>&customer_id=<?php echo $selected_customer; ?>&sidebar_menu=<?php echo urlencode($sidebar_menu); ?>&page_section=<?php echo urlencode($selected_section); ?>&search=<?php echo urlencode($search); ?>"
                        class="ds-page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-chevron-left" style="font-size: 0.7rem;"></i>
                    </a>

                    <!-- Page Numbers -->
                    <?php
                    $start_p = max(1, $page - 2);
                    $end_p = min($total_pages, $start_p + 4);
                    if ($end_p - $start_p < 4) {
                        $start_p = max(1, $end_p - 4);
                    }
                    for ($p = $start_p; $p <= $end_p; $p++):
                        ?>
                        <a href="?page=<?php echo $p; ?>&limit=<?php echo $limit; ?>&customer_id=<?php echo $selected_customer; ?>&sidebar_menu=<?php echo urlencode($sidebar_menu); ?>&page_section=<?php echo urlencode($selected_section); ?>&search=<?php echo urlencode($search); ?>"
                            class="ds-page-btn <?php echo $p == $page ? 'active' : ''; ?>">
                            <?php echo $p; ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Next -->
                    <a href="?page=<?php echo min($total_pages, $page + 1); ?>&limit=<?php echo $limit; ?>&customer_id=<?php echo $selected_customer; ?>&sidebar_menu=<?php echo urlencode($sidebar_menu); ?>&page_section=<?php echo urlencode($selected_section); ?>&search=<?php echo urlencode($search); ?>"
                        class="ds-page-btn <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<script>
    const sectionsData = <?php echo json_encode(array_map(function ($m) {
        return $m['sections'] ?? [];
    }, $menu_mapping)); ?>;
    const initialSection = "<?php echo $selected_section; ?>";

    function updateSectionsDropdown() {
        const menuVal = document.getElementById('sidebar_menu_select').value;
        const sectionSelect = document.getElementById('page_section_select');
        sectionSelect.innerHTML = '<option value="">Select Page Section</option>';

        if (menuVal && sectionsData[menuVal]) {
            const sections = sectionsData[menuVal];
            for (const key in sections) {
                const opt = document.createElement('option');
                opt.value = key;
                opt.textContent = sections[key].label;
                if (key === initialSection) {
                    opt.selected = true;
                }
                sectionSelect.appendChild(opt);
            }
        }
    }

    function applySearch(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('search', val);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function changeLimit(limitVal) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limitVal);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function applyCustomerFilter(custId) {
        const url = new URL(window.location.href);
        if (custId) {
            url.searchParams.set('customer_id', custId);
        } else {
            url.searchParams.delete('customer_id');
        }
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function exportCSV() {
        let csv = [];
        const rows = document.querySelectorAll(".ds-table tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) {
                row.push('"' + cols[j].innerText.replace(/"/g, '""').replace(/\n/g, ' ') + '"');
            }
            csv.push(row.join(","));
        }
        const blob = new Blob([csv.join("\n")], { type: "text/csv" });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.setAttribute("href", url);
        a.setAttribute("download", "data_source_tracking.csv");
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateSectionsDropdown();
    });
</script>

<?php include '../../includes/footer.php'; ?>