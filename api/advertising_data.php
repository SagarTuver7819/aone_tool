<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $session_customer_id = $_SESSION['customer_id'] ?? 0;
    $requested_customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
    
    if (($_SESSION['role'] ?? '') === 'customer') {
        $customer_id = $session_customer_id;
    } else {
        $customer_id = $requested_customer_id;
    }

    $from_date = $_GET['from_date'] ?? date('Y-m-01');
    $to_date = $_GET['to_date'] ?? date('Y-m-t');

    $where_customer = ($customer_id > 0) ? "customer_id = $customer_id" : "1=1";

    $brand = mysqli_real_escape_string($conn, $_GET['brand'] ?? '');
    $traffic_type = $_GET['traffic_type'] ?? 'all';

    $where_brand = "1=1";
    if (!empty($brand)) {
        if ($traffic_type === 'non_branded') {
            $where_brand = "(targeting NOT LIKE '%$brand%' AND campaign_name NOT LIKE '%$brand%')";
        } else {
            $where_brand = "(targeting LIKE '%$brand%' OR campaign_name LIKE '%$brand%')";
        }
    }

    // Standard report types to avoid double counting for totals
    $total_report_types = "('campaign', 'general', 'advertised_product')";

    // 1. Sponsored Products (SP) Summary
    $sql_sp = "SELECT 
        COALESCE(SUM(impressions), 0) as impressions,
        COALESCE(SUM(clicks), 0) as clicks,
        COALESCE(SUM(spend), 0) as spend,
        COALESCE(SUM(total_sales), 0) as sales,
        COALESCE(SUM(total_orders), 0) as orders,
        COALESCE((SUM(spend) / NULLIF(SUM(total_sales), 0)) * 100, 0) as acos,
        COALESCE(SUM(total_sales) / NULLIF(SUM(spend), 0), 0) as roas,
        COALESCE((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 0) as ctr,
        COALESCE((SUM(total_orders) / NULLIF(SUM(clicks), 0)) * 100, 0) as cvr
        FROM amazon_advertising_sp 
        WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN $total_report_types";
    
    $stmt = $conn->prepare($sql_sp);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $sp_summary = $stmt->get_result()->fetch_assoc() ?: ['impressions'=>0,'clicks'=>0,'spend'=>0,'sales'=>0,'orders'=>0,'acos'=>0,'roas'=>0,'ctr'=>0,'cvr'=>0];

    // 2. Sponsored Brands (SB) Summary
    $sql_sb = "SELECT 
        COALESCE(SUM(impressions), 0) as impressions,
        COALESCE(SUM(clicks), 0) as clicks,
        COALESCE(SUM(spend), 0) as spend,
        COALESCE(SUM(total_sales), 0) as sales,
        COALESCE(SUM(total_orders), 0) as orders
        FROM amazon_advertising_sb 
        WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN $total_report_types";
    $stmt = $conn->prepare($sql_sb);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $sb_summary = $stmt->get_result()->fetch_assoc() ?: ['impressions'=>0,'clicks'=>0,'spend'=>0,'sales'=>0,'orders'=>0];

    // 3. Sponsored Display (SD) Summary
    $sql_sd = "SELECT 
        COALESCE(SUM(impressions), 0) as impressions,
        COALESCE(SUM(clicks), 0) as clicks,
        COALESCE(SUM(spend), 0) as spend,
        COALESCE(SUM(total_sales), 0) as sales,
        COALESCE(SUM(total_orders), 0) as orders
        FROM amazon_advertising_sd 
        WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN $total_report_types";
    $stmt = $conn->prepare($sql_sd);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $sd_summary = $stmt->get_result()->fetch_assoc() ?: ['impressions'=>0,'clicks'=>0,'spend'=>0,'sales'=>0,'orders'=>0];

    // 4. Campaign & Target Performance (Granular)
    $sql_campaigns = "SELECT 
                        campaign_name, 
                        ad_group_name,
                        targeting,
                        match_type,
                        'SP' as type, 
                        SUM(spend) as spend, 
                        SUM(total_sales) as sales, 
                        SUM(clicks) as clicks, 
                        SUM(impressions) as impressions, 
                        SUM(total_orders) as orders
                      FROM amazon_advertising_sp 
                      WHERE $where_customer AND ($where_brand) 
                      AND report_date BETWEEN ? AND ? 
                      AND report_type IN ('targeting', 'campaign', 'general')
                      GROUP BY campaign_name, ad_group_name, targeting, match_type
                      UNION ALL
                      SELECT 
                        campaign_name, 
                        ad_group_name,
                        targeting,
                        match_type,
                        'SB' as type, 
                        SUM(spend) as spend, 
                        SUM(total_sales) as sales, 
                        SUM(clicks) as clicks, 
                        SUM(impressions) as impressions, 
                        SUM(total_orders) as orders
                      FROM amazon_advertising_sb 
                      WHERE $where_customer AND ($where_brand) 
                      AND report_date BETWEEN ? AND ? 
                      AND report_type IN ('targeting', 'campaign', 'general')
                      GROUP BY campaign_name, ad_group_name, targeting, match_type
                      UNION ALL
                      SELECT 
                        campaign_name, 
                        ad_group_name,
                        targeting,
                        match_type,
                        'SD' as type, 
                        SUM(spend) as spend, 
                        SUM(total_sales) as sales, 
                        SUM(clicks) as clicks, 
                        SUM(impressions) as impressions, 
                        SUM(total_orders) as orders
                      FROM amazon_advertising_sd 
                      WHERE $where_customer AND ($where_brand) 
                      AND report_date BETWEEN ? AND ? 
                      AND report_type IN ('targeting', 'campaign', 'general')
                      GROUP BY campaign_name, ad_group_name, targeting, match_type
                      ORDER BY spend DESC LIMIT 1000";
    
    $stmt = $conn->prepare($sql_campaigns);
    $stmt->bind_param("ssssss", $from_date, $to_date, $from_date, $to_date, $from_date, $to_date);
    
    // Debug log
    $logData = [
        'customer_id' => $customer_id,
        'brand' => $brand,
        'traffic_type' => $traffic_type,
        'from' => $from_date,
        'to' => $to_date,
        'sql' => $sql_campaigns
    ];
    file_put_contents(__DIR__ . '/../scratch/last_request.json', json_encode($logData, JSON_PRETTY_PRINT));
    
    $stmt->execute();
    $campaigns = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 5. Daily Advertising Spend Trend
    $sql_daily = "SELECT report_date, SUM(spend) as total_spend, SUM(total_sales) as total_sales
                  FROM (
                    SELECT report_date, spend, total_sales FROM amazon_advertising_sp WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN $total_report_types
                    UNION ALL
                    SELECT report_date, spend, total_sales FROM amazon_advertising_sb WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN $total_report_types
                    UNION ALL
                    SELECT report_date, spend, total_sales FROM amazon_advertising_sd WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN $total_report_types
                  ) as combined
                  GROUP BY report_date ORDER BY report_date ASC";
    $stmt = $conn->prepare($sql_daily);
    $stmt->bind_param("ssssss", $from_date, $to_date, $from_date, $to_date, $from_date, $to_date);
    $stmt->execute();
    $daily_res = $stmt->get_result();
    $daily_trend = ['labels' => [], 'spend' => [], 'sales' => []];
    while($row = $daily_res->fetch_assoc()) {
        $daily_trend['labels'][] = date('d M', strtotime($row['report_date']));
        $daily_trend['spend'][] = floatval($row['total_spend']);
        $daily_trend['sales'][] = floatval($row['total_sales']);
    }

    $payload = [
        'summary' => [
            'sp' => $sp_summary,
            'sb' => $sb_summary,
            'sd' => $sd_summary,
            'total_spend' => $sp_summary['spend'] + $sb_summary['spend'] + $sd_summary['spend'],
            'total_sales' => $sp_summary['sales'] + $sb_summary['sales'] + $sd_summary['sales'],
            'total_clicks' => $sp_summary['clicks'] + $sb_summary['clicks'] + $sd_summary['clicks'],
            'total_orders' => $sp_summary['orders'] + $sb_summary['orders'] + $sd_summary['orders']
        ],
        'campaigns' => $campaigns,
        'daily_trend' => $daily_trend,
        'placements' => fetchPlacements($conn, $where_customer, $where_brand, $from_date, $to_date),
        'placements_sp' => fetchPlacementsSP($conn, $where_customer, $where_brand, $from_date, $to_date),
        'placements_sb' => fetchPlacementsSB($conn, $where_customer, $where_brand, $from_date, $to_date),
        'bidding' => fetchBidding($conn, $where_customer, $where_brand, $from_date, $to_date),
        'purchased_products' => fetchPurchasedProducts($conn, $where_customer, $from_date, $to_date),
        'invalid_traffic' => fetchInvalidTraffic($conn, $where_customer, $where_brand, $from_date, $to_date),
        'sp_skus' => fetchSpSkus($conn, $where_customer, $where_brand, $from_date, $to_date),
        'sb_skus' => fetchSbSkus($conn, $where_customer, $from_date, $to_date),
        'match_types' => fetchMatchTypes($conn, $where_customer, $where_brand, $from_date, $to_date),
        'match_types_daily' => fetchMatchTypesDaily($conn, $where_customer, $where_brand, $from_date, $to_date),
        'top_keywords' => fetchTopKeywords($conn, $where_customer, $where_brand, $from_date, $to_date),
        'heatmap' => fetchHeatmapData($conn, $where_customer, $where_brand, $from_date, $to_date)
    ];

    echo json_encode($payload);

} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => $t->getMessage(), 'trace' => $t->getTraceAsString()]);
}

function fetchMatchTypes($conn, $where_customer, $where_brand, $from, $to) {
    $sql = "SELECT 
                LOWER(match_type) as match_type,
                SUM(spend) as spend,
                SUM(total_sales) as sales,
                SUM(clicks) as clicks,
                SUM(impressions) as impressions,
                SUM(total_orders) as orders,
                COALESCE((SUM(spend) / NULLIF(SUM(total_sales), 0)) * 100, 0) as acos,
                COALESCE(SUM(total_sales) / NULLIF(SUM(spend), 0), 0) as roas,
                COALESCE((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 0) as ctr
            FROM (
                SELECT match_type, spend, total_sales, clicks, impressions, total_orders FROM amazon_advertising_sp WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND match_type != '' AND match_type IS NOT NULL
                UNION ALL
                SELECT match_type, spend, total_sales, clicks, impressions, total_orders FROM amazon_advertising_sb WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND match_type != '' AND match_type IS NOT NULL
                UNION ALL
                SELECT match_type, spend, total_sales, clicks, impressions, total_orders FROM amazon_advertising_sd WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND match_type != '' AND match_type IS NOT NULL
            ) as combined
            GROUP BY LOWER(match_type)
            ORDER BY spend DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $from, $to, $from, $to, $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchMatchTypesDaily($conn, $where_customer, $where_brand, $from, $to) {
    $sql = "SELECT 
                report_date,
                LOWER(match_type) as match_type,
                SUM(spend) as spend,
                SUM(total_sales) as sales
            FROM (
                SELECT report_date, match_type, spend, total_sales FROM amazon_advertising_sp WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND match_type != '' AND match_type IS NOT NULL
                UNION ALL
                SELECT report_date, match_type, spend, total_sales FROM amazon_advertising_sb WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND match_type != '' AND match_type IS NOT NULL
            ) as combined
            GROUP BY report_date, LOWER(match_type)
            ORDER BY report_date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $from, $to, $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchTopKeywords($conn, $where_customer, $where_brand, $from, $to) {
    $sql = "SELECT 
                targeting as keyword,
                LOWER(match_type) as match_type,
                SUM(spend) as spend,
                SUM(total_sales) as sales,
                SUM(clicks) as clicks,
                SUM(impressions) as impressions,
                SUM(total_orders) as orders,
                COALESCE((SUM(spend) / NULLIF(SUM(total_sales), 0)) * 100, 0) as acos,
                COALESCE(SUM(total_sales) / NULLIF(SUM(spend), 0), 0) as roas,
                COALESCE((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 0) as ctr,
                'SP' as ad_type
            FROM amazon_advertising_sp
            WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = 'targeting' AND targeting != '' AND targeting IS NOT NULL
            GROUP BY targeting, match_type
            UNION ALL
            SELECT 
                targeting as keyword,
                LOWER(match_type) as match_type,
                SUM(spend) as spend,
                SUM(total_sales) as sales,
                SUM(clicks) as clicks,
                SUM(impressions) as impressions,
                SUM(total_orders) as orders,
                COALESCE((SUM(spend) / NULLIF(SUM(total_sales), 0)) * 100, 0) as acos,
                COALESCE(SUM(total_sales) / NULLIF(SUM(spend), 0), 0) as roas,
                COALESCE((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 0) as ctr,
                'SB' as ad_type
            FROM amazon_advertising_sb
            WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = 'targeting' AND targeting != '' AND targeting IS NOT NULL
            GROUP BY targeting, match_type
            ORDER BY spend DESC
            LIMIT 100";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $from, $to, $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


function fetchSpSkus($conn, $where_customer, $where_brand, $from, $to) {
    $sql = "SELECT 
                advertised_sku as sku, 
                advertised_asin as asin,
                SUM(impressions) as impressions,
                SUM(clicks) as clicks,
                SUM(spend) as spend,
                SUM(total_sales) as sales,
                SUM(total_orders) as orders,
                COALESCE((SUM(spend) / NULLIF(SUM(total_sales), 0)) * 100, 0) as acos,
                COALESCE(SUM(total_sales) / NULLIF(SUM(spend), 0), 0) as roas,
                COALESCE((SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100, 0) as ctr,
                COALESCE((SUM(total_orders) / NULLIF(SUM(clicks), 0)) * 100, 0) as cvr
            FROM amazon_advertising_sp
            WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND advertised_sku != '' AND advertised_sku IS NOT NULL
            GROUP BY advertised_sku, advertised_asin
            ORDER BY spend DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchSbSkus($conn, $where_customer, $from, $to) {
    $res = $conn->query("SHOW TABLES LIKE 'dyn_sponsored_brands_attributed_purchases_report'");
    if (!$res || $res->num_rows === 0) return [];
    
    $sql = "SELECT 
                p.purchased_asin as asin,
                COALESCE(sp.advertised_sku, 'ASIN Lookup Needed') as sku,
                SUM(CAST(REPLACE(REPLACE(p.col_14_day_total_sales, '$', ''), ',', '') AS DECIMAL(10,2))) as sales,
                SUM(CAST(p.col_14_day_total_orders AS UNSIGNED)) as orders,
                SUM(CAST(p.col_14_day_total_units AS UNSIGNED)) as units,
                p.campaign_name
            FROM dyn_sponsored_brands_attributed_purchases_report p
            LEFT JOIN (
                SELECT advertised_asin, advertised_sku 
                FROM amazon_advertising_sp 
                WHERE advertised_sku != '' AND advertised_sku IS NOT NULL 
                GROUP BY advertised_asin, advertised_sku
            ) sp ON p.purchased_asin = sp.advertised_asin
            GROUP BY p.purchased_asin, p.campaign_name
            ORDER BY sales DESC";
            
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

function fetchPlacements($conn, $where, $where_brand, $from, $to) {
    $sql = "SELECT placement, SUM(spend) as spend, SUM(total_sales) as sales, SUM(clicks) as clicks, SUM(impressions) as impressions
            FROM (
                SELECT placement, spend, total_sales, clicks, impressions FROM amazon_advertising_sp WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = 'placement'
                UNION ALL
                SELECT placement, spend, total_sales, clicks, impressions FROM amazon_advertising_sb WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = 'placement'
            ) as p
            GROUP BY placement ORDER BY spend DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $from, $to, $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchBidding($conn, $where, $where_brand, $from, $to) {
    $sql = "SELECT bidding_strategy, SUM(spend) as spend, SUM(total_sales) as sales
            FROM (
                SELECT 
                    CASE 
                        WHEN bidding_strategy LIKE '%down only%' THEN 'Dynamic bids - down only'
                        WHEN bidding_strategy LIKE '%up and down%' THEN 'Dynamic bids - up and down'
                        WHEN bidding_strategy = 'Fixed bids' OR bidding_strategy = 'manual' OR bidding_strategy = '' THEN 'Fixed bids'
                        ELSE 'Other / Auto'
                    END as bidding_strategy, 
                    spend, total_sales 
                FROM amazon_advertising_sp WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN ('campaign', 'general')
            ) as b
            GROUP BY bidding_strategy ORDER BY spend DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
function fetchPurchasedProducts($conn, $where, $from, $to) {
    $products = [];
    $res = $conn->query("SHOW TABLES LIKE 'dyn_%purchased_product%'");
    if (!$res) return [];
    
    while($row = $res->fetch_array()) {
        $table = $row[0];
        $colsRes = $conn->query("DESCRIBE `$table` ");
        $salesCol = '';
        while($c = $colsRes->fetch_assoc()) {
            $f = strtolower($c['Field']);
            if (strpos($f, 'total_sales') !== false) { $salesCol = $c['Field']; break; }
        }
        if (!$salesCol) continue;

        $sql = "SELECT purchased_asin, campaign_name, SUM(CAST(REPLACE(REPLACE(`$salesCol`, '$', ''), ',', '') AS DECIMAL(10,2))) as total_sales 
                FROM `$table` 
                GROUP BY purchased_asin, campaign_name ORDER BY total_sales DESC LIMIT 20";
        $pRes = $conn->query($sql);
        if ($pRes) {
            while($p = $pRes->fetch_assoc()) $products[] = $p;
        }
    }
    return $products;
}

function fetchInvalidTraffic($conn, $where, $where_brand, $from, $to) {
    $sql = "SELECT SUM(gross_clicks) as gross, SUM(invalid_clicks) as invalid 
            FROM (
                SELECT gross_clicks, invalid_clicks FROM amazon_advertising_sp WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN ('campaign', 'general')
                UNION ALL
                SELECT gross_clicks, invalid_clicks FROM amazon_advertising_sb WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN ('campaign', 'general')
                UNION ALL
                SELECT gross_clicks, invalid_clicks FROM amazon_advertising_sd WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN ('campaign', 'general')
            ) as t";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $from, $to, $from, $to, $from, $to);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    
    $gross = intval($res['gross'] ?? 0);
    $invalid = intval($res['invalid'] ?? 0);
    $pct = $gross > 0 ? ($invalid / $gross) * 100 : 0;
    
    return [
        'gross_clicks' => $gross,
        'invalid_clicks' => $invalid,
        'pct' => number_format($pct, 2) . '%'
    ];
}

function fetchHeatmapData($conn, $where_customer, $where_brand, $from, $to) {
    $sql = "SELECT 
                DAYOFWEEK(report_date) as day_num, 
                SUM(spend) as spend, 
                SUM(total_sales) as sales
            FROM (
                SELECT report_date, spend, total_sales FROM amazon_advertising_sp WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN ('campaign', 'general', 'advertised_product')
                UNION ALL
                SELECT report_date, spend, total_sales FROM amazon_advertising_sb WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN ('campaign', 'general', 'advertised_product')
                UNION ALL
                SELECT report_date, spend, total_sales FROM amazon_advertising_sd WHERE $where_customer AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type IN ('campaign', 'general', 'advertised_product')
            ) as combined
            GROUP BY DAYOFWEEK(report_date)
            ORDER BY day_num ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $from, $to, $from, $to, $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchPlacementsSP($conn, $where, $where_brand, $from, $to) {
    $sql = "SELECT placement, SUM(spend) as spend, SUM(total_sales) as sales, SUM(clicks) as clicks, SUM(impressions) as impressions
            FROM amazon_advertising_sp 
            WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = 'placement'
            GROUP BY placement ORDER BY spend DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetchPlacementsSB($conn, $where, $where_brand, $from, $to) {
    $sql = "SELECT placement, SUM(spend) as spend, SUM(total_sales) as sales, SUM(clicks) as clicks, SUM(impressions) as impressions
            FROM amazon_advertising_sb 
            WHERE $where AND ($where_brand) AND report_date BETWEEN ? AND ? AND report_type = 'placement'
            GROUP BY placement ORDER BY spend DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
