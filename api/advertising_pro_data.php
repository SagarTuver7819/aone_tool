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
    $customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : ($_SESSION['customer_id'] ?? 0);
    $from_date = $_GET['from_date'] ?? date('Y-m-01');
    $to_date = $_GET['to_date'] ?? date('Y-m-t');

    // 1. Fetch Keyword Data (Combined from SP, SB, SD)
    // For simplicity, we'll focus on SP (Sponsored Products) as it has targeting data usually.
    
    $sql = "SELECT targeting as keyword, campaign_name, ad_group_name, match_type,
            SUM(impressions) as impressions,
            SUM(clicks) as clicks,
            SUM(spend) as spend,
            SUM(total_sales) as sales
            FROM amazon_advertising_sp 
            WHERE customer_id = $customer_id AND report_date BETWEEN ? AND ?
            GROUP BY targeting, campaign_name, ad_group_name, match_type
            ORDER BY spend DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from_date, $to_date);
    $stmt->execute();
    $keywords = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $recommendations = [];

    foreach ($keywords as &$k) {
        $k['acos'] = $k['sales'] > 0 ? ($k['spend'] / $k['sales']) * 100 : 0;
        $k['ctr'] = $k['impressions'] > 0 ? ($k['clicks'] / $k['impressions']) * 100 : 0;
        $k['cpc'] = $k['clicks'] > 0 ? ($k['spend'] / $k['clicks']) : 0;
        $k['roas'] = $k['spend'] > 0 ? ($k['sales'] / $k['spend']) : 0;

        // Basic Recommendation Logic
        if ($k['clicks'] > 15 && $k['sales'] == 0) {
            $recommendations[] = [
                'keyword' => $k['keyword'],
                'reason' => 'Bleeding (High Clicks, No Sales)',
                'action' => 'Pause or Lower Bid',
                'severity' => 'high'
            ];
        } elseif ($k['acos'] > 50 && $k['sales'] > 0) {
            $recommendations[] = [
                'keyword' => $k['keyword'],
                'reason' => 'High ACoS (> 50%)',
                'action' => 'Optimize Bid',
                'severity' => 'medium'
            ];
        } elseif ($k['acos'] < 15 && $k['sales'] > 100) {
            $recommendations[] = [
                'keyword' => $k['keyword'],
                'reason' => 'Winner (Low ACoS, High Sales)',
                'action' => 'Increase Bid to scale',
                'severity' => 'info'
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'keywords' => $keywords,
        'recommendations' => $recommendations
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
