<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

function getContributedDataSummary($row, $conn) {
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
            return "📈 <b>Sales:</b> $" . number_format($res['sales'] ?? 0, 2) . " | <b>Units:</b> " . number_format($res['units'] ?? 0) . " | <b>Orders:</b> " . number_format($res['orders'] ?? 0);
        }
    } 
    elseif ($type === 'Transaction') {
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
            return "💰 <b>Gross Sales:</b> $" . number_format($res['sales'] ?? 0, 2) . " | <b>FBA Fees:</b> $" . number_format($res['fba'] ?? 0, 2) . " | <b>Selling Fees:</b> $" . number_format($res['selling'] ?? 0, 2);
        }
    }
    elseif ($type === 'Detail') {
        $sql = "SELECT SUM(sessions_total) as sessions, SUM(page_views_total) as page_views 
                FROM amazon_detail_report 
                WHERE customer_id = ? AND report_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $customerId, $reportDate);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['sessions'] > 0) {
            return "👥 <b>Sessions:</b> " . number_format($res['sessions']) . " | <b>Page Views:</b> " . number_format($res['page_views']);
        }
    }
    elseif (strpos($type, 'Ads') === 0) {
        $tableName = 'amazon_advertising_sp';
        if (strpos($type, 'Ads SB') === 0) $tableName = 'amazon_advertising_sb';
        elseif (strpos($type, 'Ads SD') === 0) $tableName = 'amazon_advertising_sd';
        
        $sql = "SELECT SUM(impressions) as impr, SUM(clicks) as clicks, SUM(spend) as spend, SUM(total_sales) as sales 
                FROM `$tableName` 
                WHERE customer_id = ? AND report_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $customerId, $monthStart, $monthEnd);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['clicks'] > 0) {
            return "📢 <b>Spend:</b> $" . number_format($res['spend'] ?? 0, 2) . " | <b>Ad Sales:</b> $" . number_format($res['sales'] ?? 0, 2) . " | <b>Clicks:</b> " . number_format($res['clicks']) . " | <b>Impr:</b> " . number_format($res['impr']);
        }
    }
    elseif ($type === 'Inventory') {
        $sql = "SELECT COUNT(DISTINCT sku) as total_skus, SUM(afn_fulfillable_quantity) as afn_qty, SUM(mfn_fulfillable_quantity) as mfn_qty 
                FROM amazon_inventory 
                WHERE customer_id = ? AND report_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $customerId, $reportDate);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['total_skus'] > 0) {
            return "📦 <b>SKUs Active:</b> " . number_format($res['total_skus']) . " | <b>Fulfillable Qty:</b> " . number_format(($res['afn_qty'] ?? 0) + ($res['mfn_qty'] ?? 0));
        }
    }
    
    return "<span style='color: #94a3b8; font-style: italic;'>Processed in database</span>";
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
$limit = 25;
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
                'label' => 'Ad Performance Cards (Total Ad Spend, Ad Sales, ACOS, CTR, CPC, ROAS)',
                'types' => ['Ads'],
                'description' => 'Displays total impressions, clicks, CPC, CTR, ROAS, and ACOS metrics for the given period.'
            ],
            'daily_ad_trends_chart' => [
                'label' => 'Daily Advertising Trends Chart',
                'types' => ['Ads'],
                'description' => 'Plots daily PPC ad spend, clicks, impressions, and ad sales over time.'
            ],
            'campaign_type_breakdown' => [
                'label' => 'Campaign Type Breakdown (SP vs SB vs SD Tables & Pie Charts)',
                'types' => ['Ads'],
                'description' => 'Groups advertising metrics by Sponsored Products (SP), Sponsored Brands (SB), and Sponsored Display (SD).'
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
        'types' => ['Brand search query', 'Brand repeat purchase'],
        'description' => 'Analyzes brand search query frequency, brand impression/click share, and repeat customer acquisition.',
        'target_file' => 'modules/amazon_ads/brand_analytics.php',
        'sections' => [
            'search_query_performance' => [
                'label' => 'Search Query Performance Summary & Search Volume Charts',
                'types' => ['Brand search query'],
                'description' => 'Analyzes search queries frequency, impression share, click shares, and cart additions for brand keywords.'
            ],
            'repeat_purchase' => [
                'label' => 'Repeat Purchase Behavior Statistics',
                'types' => ['Brand repeat purchase'],
                'description' => 'Identifies repeating customer cohorts, repeat unit counts, and order statistics.'
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
    
    // Check if a specific page section is selected
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

<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" id="filter-form" style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="flex: 1; min-width: 220px;">
            <label>Customer Account</label>
            <select name="customer_id" style="width: 100%;" <?php echo ($user_role === 'customer') ? 'disabled' : ''; ?>>
                <?php if ($user_role === 'admin'): ?>
                    <option value="">All Accounts</option>
                <?php endif; ?>
                <?php $customers->data_seek(0); while ($row = $customers->fetch_assoc()): ?>
                    <?php 
                        $selected = ($selected_customer == $row['id']) ? 'selected' : '';
                        if ($user_role === 'customer' && $session_customer_id != $row['id']) continue;
                    ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($row['customer_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group" style="flex: 1; min-width: 200px;">
            <label>Filter by Sidebar Menu Target</label>
            <select name="sidebar_menu" id="sidebar_menu_select" style="width: 100%;" onchange="updateSectionsDropdown()">
                <option value="">-- Select Sidebar Menu --</option>
                <?php foreach ($menu_mapping as $key => $mapping): ?>
                    <option value="<?php echo $key; ?>" <?php echo ($sidebar_menu === $key) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mapping['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="flex: 1; min-width: 200px;">
            <label>Page Section Target</label>
            <select name="page_section" id="page_section_select" style="width: 100%;">
                <option value="">-- Select Page Section --</option>
            </select>
        </div>

        <div class="form-group" style="flex: 1.5; min-width: 200px;">
            <label>Search Files (Name / ZIP)</label>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by CSV or ZIP filename..." style="width: 100%;">
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 15px;">
                <i class="fas fa-search"></i> FILTER
            </button>
            <a href="tracking.php" class="btn btn-outline" style="height: 40px; padding: 0 15px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                <i class="fas fa-undo"></i> RESET
            </a>
        </div>
    </form>
</div>

<script>
    const sectionsData = <?php echo json_encode(array_map(function($m) { return $m['sections'] ?? []; }, $menu_mapping)); ?>;
    const initialSection = "<?php echo $selected_section; ?>";

    function updateSectionsDropdown() {
        const menuVal = document.getElementById('sidebar_menu_select').value;
        const sectionSelect = document.getElementById('page_section_select');
        sectionSelect.innerHTML = '<option value="">-- Select Page Section --</option>';
        
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

    document.addEventListener('DOMContentLoaded', function() {
        updateSectionsDropdown();
    });
</script>

<?php if ($sidebar_menu !== '' && isset($menu_mapping[$sidebar_menu])): ?>
    <?php 
        $current_menu = $menu_mapping[$sidebar_menu]; 
        $title = 'Data usage for "' . $current_menu['label'] . '" Sidebar Page';
        $desc = $current_menu['description'];
        if ($selected_section !== '' && isset($current_menu['sections'][$selected_section])) {
            $current_section = $current_menu['sections'][$selected_section];
            $title = 'Data usage for "' . $current_menu['label'] . '" → "' . $current_section['label'] . '" Section';
            $desc = $current_section['description'];
        }
    ?>
    <div class="card" style="margin-bottom: 1.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; display: flex; gap: 1rem; align-items: center; padding: 1.25rem;">
        <div style="background: #dcfce7; color: #16a34a; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
            <i class="fas fa-info-circle"></i>
        </div>
        <div>
            <h4 style="margin: 0; font-size: 1rem; font-weight: 700; color: #14532d;">
                <?php echo htmlspecialchars($title); ?>
            </h4>
            <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #166534; line-height: 1.4;">
                <?php echo htmlspecialchars($desc); ?>
            </p>
            <a href="<?php echo BASE_URL . $current_menu['target_file']; ?>" style="display: inline-block; margin-top: 8px; font-size: 0.8rem; font-weight: 700; color: #166534; text-decoration: underline;">
                Go to <?php echo htmlspecialchars($current_menu['label']); ?> Page <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            </a>
        </div>
    </div>
<?php endif; ?>

<div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; border: 1px solid #CBD5E1;">
    <div style="padding: 1.5rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-weight: 700; font-size: 1.1rem; margin: 0;">Ingested Files Log</h3>
        <span style="font-size: 0.85rem; background: var(--primary-light); color: var(--primary); padding: 4px 12px; border-radius: 50px; font-weight: 600;">
            Total Records: <?php echo $total_rows; ?>
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; font-weight: 600; color: #475569;">
                    <th style="padding: 1rem 1.5rem;">Customer</th>
                    <th style="padding: 1rem 1.5rem;">Report Type</th>
                    <th style="padding: 1rem 1.5rem;">ZIP File Source</th>
                    <th style="padding: 1rem 1.5rem;">Extracted/Uploaded File</th>
                    <th style="padding: 1rem 1.5rem;">Report Month</th>
                    <th style="padding: 1rem 1.5rem; text-align: right;">Records Ingested</th>
                    <th style="padding: 1rem 1.5rem;">Ingested Data Summary</th>
                    <th style="padding: 1rem 1.5rem;">Uploaded By</th>
                    <th style="padding: 1rem 1.5rem; text-align: right;">Upload Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 1rem 1.5rem; font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td style="padding: 1rem 1.5rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: #0369a1; background: #e0f2fe; padding: 4px 8px; border-radius: 4px;">
                                    <?php echo htmlspecialchars($row['report_type']); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem 1.5rem; color: #64748b;">
                                <?php if ($row['zip_filename']): ?>
                                    <i class="fas fa-file-archive" style="color: #f59e0b; margin-right: 4px;"></i> 
                                    <?php echo htmlspecialchars($row['zip_filename']); ?>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Direct Upload</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.5rem; font-family: monospace; font-size: 0.8rem;"><?php echo htmlspecialchars($row['filename']); ?></td>
                            <td style="padding: 1rem 1.5rem; font-weight: 600;"><?php echo date('F Y', strtotime($row['report_date'])); ?></td>
                            <td style="padding: 1rem 1.5rem; text-align: right; font-weight: 700; color: #0f766e;"><?php echo number_format($row['rows_processed']); ?></td>
                            <td style="padding: 1rem 1.5rem; font-size: 0.8rem; color: #334155;"><?php echo getContributedDataSummary($row, $conn); ?></td>
                            <td style="padding: 1rem 1.5rem;"><?php echo htmlspecialchars($row['username'] ?? 'System'); ?></td>
                            <td style="padding: 1rem 1.5rem; text-align: right; color: #64748b; font-size: 0.8rem;"><?php echo date('d M Y h:i A', strtotime($row['uploaded_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="padding: 3rem; text-align: center; color: #94a3b8; font-size: 0.95rem;">
                            <i class="fas fa-history" style="font-size: 2rem; margin-bottom: 0.75rem; display: block; opacity: 0.5;"></i>
                            No file ingestion logs found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div style="padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; background: #F8FAFC; border-top: 1px solid #E2E8F0;">
            <div style="font-size: 0.85rem; color: #64748b;">
                Showing page <strong><?php echo $page; ?></strong> of <strong><?php echo $total_pages; ?></strong>
            </div>
            <div style="display: flex; gap: 4px;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&customer_id=<?php echo $selected_customer; ?>&sidebar_menu=<?php echo urlencode($sidebar_menu); ?>&page_section=<?php echo urlencode($selected_section); ?>&search=<?php echo urlencode($search); ?>" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.8rem; height: auto;">Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="btn btn-primary" style="padding: 4px 10px; font-size: 0.8rem; height: auto;"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&customer_id=<?php echo $selected_customer; ?>&sidebar_menu=<?php echo urlencode($sidebar_menu); ?>&page_section=<?php echo urlencode($selected_section); ?>&search=<?php echo urlencode($search); ?>" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.8rem; height: auto;"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&customer_id=<?php echo $selected_customer; ?>&sidebar_menu=<?php echo urlencode($sidebar_menu); ?>&page_section=<?php echo urlencode($selected_section); ?>&search=<?php echo urlencode($search); ?>" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.8rem; height: auto;">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
