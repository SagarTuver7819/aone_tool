<?php
require_once '../../config.php';
require_once '../../includes/functions.php';
require_once '../../includes/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

$customers = get_all_customers();
$error = '';
$success = '';
$processed_reports = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $report_month = $_POST['report_month']; // YYYY-MM
    $report_date = $report_month . "-01";

    if (isset($_FILES['reports']) && !empty($_FILES['reports']['name'][0])) {
        
        if (isset($_POST['clean_db']) && $_POST['clean_db'] == '1') {
            // Truncate core tables
            $conn->query("TRUNCATE TABLE amazon_business_report");
            $conn->query("TRUNCATE TABLE amazon_detail_report");
            $conn->query("TRUNCATE TABLE amazon_transaction_report");
            $conn->query("TRUNCATE TABLE amazon_advertising_sp");
            $conn->query("TRUNCATE TABLE amazon_advertising_sb");
            $conn->query("TRUNCATE TABLE amazon_advertising_sd");
            $conn->query("TRUNCATE TABLE amazon_brand_reports");
            $conn->query("TRUNCATE TABLE amazon_returns_reimbursements");
            
            // Drop dynamic tables
            $res = $conn->query("SHOW TABLES LIKE 'dyn_%'");
            while ($row = $res->fetch_array()) {
                $conn->query("DROP TABLE `" . $row[0] . "`");
            }
        }
        
        function cleanVal($val) {
            if (empty($val)) return 0;
            $val = str_replace(['$', ',', '%', ' '], '', $val);
            return is_numeric($val) ? $val : 0;
        }

        function slugify($text) {
            return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $text));
        }

        function sanitize_sql_name($name) {
            $name = preg_replace('/[^a-zA-Z0-9_]/', '_', trim($name));
            $name = strtolower($name);
            // Collapse multiple underscores
            $name = preg_replace('/_+/', '_', $name);
            // Trim underscores from start and end
            $name = trim($name, '_');
            
            if (empty($name)) return 'col_' . substr(md5(uniqid()), 0, 8);
            if (preg_match('/^[0-9]/', $name)) {
                $name = 'col_' . $name;
            }
            return $name;
        }

        function findMatchingTable($clean_headers, $conn) {
            $res = $conn->query("SHOW TABLES LIKE 'dyn_%'");
            while ($row = $res->fetch_array()) {
                $tableName = $row[0];
                $existing_columns = [];
                $cRes = $conn->query("DESCRIBE `$tableName` ");
                while ($c = $cRes->fetch_assoc()) {
                    if ($c['Field'] != 'id' && $c['Field'] != 'created_at') {
                        $existing_columns[] = $c['Field'];
                    }
                }
                
                $h_copy = $clean_headers;
                $e_copy = $existing_columns;
                sort($h_copy);
                sort($e_copy);
                if ($h_copy == $e_copy) return $tableName;
            }
            return false;
        }

        function parseDynamicExcel($fileInfo, $conn) {
            $filePath = $fileInfo['tmp_name'];
            $fileName = $fileInfo['name'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $rows = [];
            $headers = [];
            $rows_count = 0;

            if ($ext == 'xlsx') {
                require_once __DIR__ . '/../../includes/SimpleXLSX.php';
                if ($xlsx = \Shuchkin\SimpleXLSX::parse($filePath)) {
                    $rows = $xlsx->rows();
                    $headers = array_shift($rows);
                } else return false;
            } else {
                $handle = fopen($filePath, "r");
                $headers = fgetcsv($handle);
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }

            if (empty($headers)) return false;

            $clean_headers = [];
            foreach ($headers as $h) {
                $clean_headers[] = sanitize_sql_name($h);
            }

            // 1. Check if a table with this format already exists
            $full_table_name = findMatchingTable($clean_headers, $conn);

            // 2. If not, generate a unique table name
            if (!$full_table_name) {
                $base_name = pathinfo($fileName, PATHINFO_FILENAME);
                $base_name = preg_replace('/(\d{4}|\d{2})-(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|\d{2})-\d{2}/i', '', $base_name);
                $base_name = preg_replace('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|may|june|july|august|september|october|november|december)/i', '', $base_name);
                $clean_base = sanitize_sql_name($base_name);
                $full_table_name = "dyn_" . $clean_base;
                
                // Check if this table name exists but with a DIFFERENT schema
                $check = $conn->query("SHOW TABLES LIKE '$full_table_name'");
                if ($check && $check->num_rows > 0) {
                    // It exists but didn't match in findMatchingTable. We need a new name.
                    $full_table_name = "dyn_" . $clean_base . "_" . date('Ymd_His');
                }

                $sql = "CREATE TABLE `$full_table_name` (id INT AUTO_INCREMENT PRIMARY KEY";
                foreach ($clean_headers as $col) {
                    $sql .= ", `$col` TEXT";
                }
                $sql .= ", created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
                $conn->query($sql);
            }

            // 3. Insert Data
            if (!empty($rows)) {
                $placeholders = str_repeat('?,', count($clean_headers) - 1) . '?';
                $col_names = "`" . implode("`,`", $clean_headers) . "`";
                $stmt = $conn->prepare("INSERT INTO `$full_table_name` ($col_names) VALUES ($placeholders)");
                foreach ($rows as $row) {
                    $row_data = array_slice(array_pad($row, count($clean_headers), ''), 0, count($clean_headers));
                    $types = str_repeat('s', count($row_data));
                    $stmt->bind_param($types, ...$row_data);
                    if ($stmt->execute()) {
                        $rows_count++;
                    }
                }
            }
            return ['table' => $full_table_name, 'rows' => $rows_count];
        }

        function parseBusinessCSV($filePath, $conn, $customerId, $reportDate) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $rows = [];
            $rawHeaders = [];
            $rows_count = 0;

            if ($ext == 'xlsx') {
                require_once __DIR__ . '/../../includes/SimpleXLSX.php';
                if ($xlsx = SimpleXLSX::parse($filePath)) {
                    $rows = $xlsx->rows();
                    $rawHeaders = array_shift($rows);
                } else return;
            } else {
                $fileContent = file_get_contents($filePath);
                $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);
                $lines = explode("\n", str_replace("\r", "", $fileContent));
                if (empty($lines)) return;
                $headerLine = array_shift($lines);
                $delimiter = ",";
                if (strpos($headerLine, "\t") !== false) $delimiter = "\t";
                elseif (strpos($headerLine, ";") !== false) $delimiter = ";";
                $rawHeaders = str_getcsv($headerLine, $delimiter);
                foreach ($lines as $line) {
                    if (trim($line)) $rows[] = str_getcsv($line, $delimiter);
                }
            }

            $headers = array_map('slugify', $rawHeaders);
            $colMap = array_flip($headers);

            $monthStart = date('Y-m-01', strtotime($reportDate));
            $monthEnd = date('Y-m-t', strtotime($reportDate));
            // Clear existing data for this specific month/customer to prevent duplicates
            $stmt_clear = $conn->prepare("DELETE FROM amazon_business_report WHERE customer_id = ? AND report_date BETWEEN ? AND ?");
            $stmt_clear->bind_param("iss", $customerId, $monthStart, $monthEnd);
            $stmt_clear->execute();
            
            // Map common variations to standard slugs
            $standardMap = [
                'date' => ['date'],
                'orderedproductsales' => ['orderedproductsales'],
                'orderedproductsalesb2b' => ['orderedproductsalesb2b'],
                'unitsordered' => ['unitsordered'],
                'unitsorderedb2b' => ['unitsorderedb2b'],
                'totalorderitems' => ['totalorderitems'],
                'totalorderitemsb2b' => ['totalorderitemsb2b'],
                'averagesalesperorderitem' => ['averagesalesperorderitem'],
                'averagesalesperorderitemb2b' => ['averagesalesperorderitemb2b'],
                'averageunitsperorderitem' => ['averageunitsperorderitem'],
                'averageunitsperorderitemb2b' => ['averageunitsperorderitemb2b'],
                'averagesellingprice' => ['averagesellingprice'],
                'averagesellingpriceb2b' => ['averagesellingpriceb2b'],
                'pageviewsmobileapp' => ['pageviewsmobileapp'],
                'pageviewsmobileappb2b' => ['pageviewsmobileappb2b'],
                'pageviewsbrowser' => ['pageviewsbrowser'],
                'pageviewsbrowserb2b' => ['pageviewsbrowserb2b'],
                'pageviewstotal' => ['pageviewstotal'],
                'pageviewstotalb2b' => ['pageviewstotalb2b'],
                'sessionsmobileapp' => ['sessionsmobileapp'],
                'sessionsmobileappb2b' => ['sessionsmobileappb2b'],
                'sessionsbrowser' => ['sessionsbrowser'],
                'sessionsbrowserb2b' => ['sessionsbrowserb2b'],
                'sessionstotal' => ['sessionstotal'],
                'sessionstotalb2b' => ['sessionstotalb2b'],
                'featuredofferbuyboxpercentage' => ['featuredofferbuyboxpercentage', 'buyboxpercentage'],
                'featuredofferbuyboxpercentageb2b' => ['featuredofferbuyboxpercentageb2b', 'buyboxpercentageb2b'],
                'orderitemsessionpercentage' => ['orderitemsessionpercentage'],
                'orderitemsessionpercentageb2b' => ['orderitemsessionpercentageb2b'],
                'unitsessionpercentage' => ['unitsessionpercentage'],
                'unitsessionpercentageb2b' => ['unitsessionpercentageb2b'],
                'averageoffercount' => ['averageoffercount'],
                'averageparentitems' => ['averageparentitems'],
                'unitsrefunded' => ['unitsrefunded'],
                'unitsrefundedb2b' => ['unitsrefundedb2b'],
                'refundrate' => ['refundrate'],
                'refundrateb2b' => ['refundrateb2b'],
                'feedbackreceived' => ['feedbackreceived'],
                'feedbackreceivedb2b' => ['feedbackreceivedb2b'],
                'negativefeedbackreceived' => ['negativefeedbackreceived'],
                'negativefeedbackreceivedb2b' => ['negativefeedbackreceivedb2b'],
                'receivednegativefeedbackrate' => ['receivednegativefeedbackrate'],
                'receivednegativefeedbackrateb2b' => ['receivednegativefeedbackrateb2b'],
                'atozclaimsgranted' => ['atozclaimsgranted'],
                'claimsamount' => ['claimsamount'],
                'atozclaimsgrantedb2b' => ['atozclaimsgrantedb2b'],
                'claimsamountb2b' => ['claimsamountb2b'],
                'shippedproductsales' => ['shippedproductsales'],
                'shippedproductsalesb2b' => ['shippedproductsalesb2b'],
                'unitsshipped' => ['unitsshipped'],
                'unitsshippedb2b' => ['unitsshippedb2b'],
                'ordersshipped' => ['ordersshipped'],
                'ordersshippedb2b' => ['ordersshippedb2b'],
            ];

            $getVal = function($data, $standardKey) use ($colMap, $standardMap) {
                if (isset($standardMap[$standardKey])) {
                    foreach ($standardMap[$standardKey] as $possibleSlug) {
                        if (isset($colMap[$possibleSlug])) return $data[$colMap[$possibleSlug]] ?? 0;
                    }
                }
                return 0;
            };

            $sql = "INSERT INTO amazon_business_report 
                (customer_id, report_date, ordered_product_sales, ordered_product_sales_b2b, units_ordered, units_ordered_b2b, total_order_items, total_order_items_b2b, avg_sales_per_order_item, avg_sales_per_order_item_b2b, avg_units_per_order_item, avg_units_per_order_item_b2b, avg_selling_price, avg_selling_price_b2b, page_views_mobile_app, page_views_mobile_app_b2b, page_views_browser, page_views_browser_b2b, page_views_total, page_views_total_b2b, sessions_mobile_app, sessions_mobile_app_b2b, sessions_browser, sessions_browser_b2b, sessions_total, sessions_total_b2b, buy_box_percentage, buy_box_percentage_b2b, order_item_session_percentage, order_item_session_percentage_b2b, unit_session_percentage, unit_session_percentage_b2b, avg_offer_count, avg_parent_items, units_refunded, units_refunded_b2b, refund_rate, refund_rate_b2b, feedback_received, feedback_received_b2b, negative_feedback_received, negative_feedback_received_b2b, received_negative_feedback_rate, received_negative_feedback_rate_b2b, atoz_claims_granted, claims_amount, atoz_claims_granted_b2b, claims_amount_b2b, shipped_product_sales, shipped_product_sales_b2b, units_shipped, units_shipped_b2b, orders_shipped, orders_shipped_b2b) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                ordered_product_sales = VALUES(ordered_product_sales),
                ordered_product_sales_b2b = VALUES(ordered_product_sales_b2b),
                units_ordered = VALUES(units_ordered),
                units_ordered_b2b = VALUES(units_ordered_b2b),
                total_order_items = VALUES(total_order_items),
                total_order_items_b2b = VALUES(total_order_items_b2b),
                avg_sales_per_order_item = VALUES(avg_sales_per_order_item),
                avg_sales_per_order_item_b2b = VALUES(avg_sales_per_order_item_b2b),
                avg_units_per_order_item = VALUES(avg_units_per_order_item),
                avg_units_per_order_item_b2b = VALUES(avg_units_per_order_item_b2b),
                avg_selling_price = VALUES(avg_selling_price),
                avg_selling_price_b2b = VALUES(avg_selling_price_b2b),
                page_views_mobile_app = VALUES(page_views_mobile_app),
                page_views_mobile_app_b2b = VALUES(page_views_mobile_app_b2b),
                page_views_browser = VALUES(page_views_browser),
                page_views_browser_b2b = VALUES(page_views_browser_b2b),
                page_views_total = VALUES(page_views_total),
                page_views_total_b2b = VALUES(page_views_total_b2b),
                sessions_mobile_app = VALUES(sessions_mobile_app),
                sessions_mobile_app_b2b = VALUES(sessions_mobile_app_b2b),
                sessions_browser = VALUES(sessions_browser),
                sessions_browser_b2b = VALUES(sessions_browser_b2b),
                sessions_total = VALUES(sessions_total),
                sessions_total_b2b = VALUES(sessions_total_b2b),
                buy_box_percentage = VALUES(buy_box_percentage),
                buy_box_percentage_b2b = VALUES(buy_box_percentage_b2b),
                order_item_session_percentage = VALUES(order_item_session_percentage),
                order_item_session_percentage_b2b = VALUES(order_item_session_percentage_b2b),
                unit_session_percentage = VALUES(unit_session_percentage),
                unit_session_percentage_b2b = VALUES(unit_session_percentage_b2b),
                avg_offer_count = VALUES(avg_offer_count),
                avg_parent_items = VALUES(avg_parent_items),
                units_refunded = VALUES(units_refunded),
                units_refunded_b2b = VALUES(units_refunded_b2b),
                refund_rate = VALUES(refund_rate),
                refund_rate_b2b = VALUES(refund_rate_b2b),
                feedback_received = VALUES(feedback_received),
                feedback_received_b2b = VALUES(feedback_received_b2b),
                negative_feedback_received = VALUES(negative_feedback_received),
                negative_feedback_received_b2b = VALUES(negative_feedback_received_b2b),
                received_negative_feedback_rate = VALUES(received_negative_feedback_rate),
                received_negative_feedback_rate_b2b = VALUES(received_negative_feedback_rate_b2b),
                atoz_claims_granted = VALUES(atoz_claims_granted),
                claims_amount = VALUES(claims_amount),
                atoz_claims_granted_b2b = VALUES(atoz_claims_granted_b2b),
                claims_amount_b2b = VALUES(claims_amount_b2b),
                shipped_product_sales = VALUES(shipped_product_sales),
                shipped_product_sales_b2b = VALUES(shipped_product_sales_b2b),
                units_shipped = VALUES(units_shipped),
                units_shipped_b2b = VALUES(units_shipped_b2b),
                orders_shipped = VALUES(orders_shipped),
                orders_shipped_b2b = VALUES(orders_shipped_b2b)";
            
            $stmt = $conn->prepare($sql);

            foreach ($rows as $data) {
                if (empty($data[0]) || count($data) < count($rawHeaders) / 2) continue;

                $dateVal = $getVal($data, 'date');
                if (!empty($dateVal)) {
                    // Try to normalize Amazon's date format (e.g., "Jan 1, 2026" or "01/01/26")
                    $date = date('Y-m-d', strtotime($dateVal));
                    if (!$date || $date == '1970-01-01') $date = $reportDate;
                } else {
                    $date = $reportDate;
                }

                $params = [
                    $customerId, $date,
                    cleanVal($getVal($data, 'orderedproductsales')),
                    cleanVal($getVal($data, 'orderedproductsalesb2b')),
                    cleanVal($getVal($data, 'unitsordered')),
                    cleanVal($getVal($data, 'unitsorderedb2b')),
                    cleanVal($getVal($data, 'totalorderitems')),
                    cleanVal($getVal($data, 'totalorderitemsb2b')),
                    cleanVal($getVal($data, 'averagesalesperorderitem')),
                    cleanVal($getVal($data, 'averagesalesperorderitemb2b')),
                    cleanVal($getVal($data, 'averageunitsperorderitem')),
                    cleanVal($getVal($data, 'averageunitsperorderitemb2b')),
                    cleanVal($getVal($data, 'averagesellingprice')),
                    cleanVal($getVal($data, 'averagesellingpriceb2b')),
                    cleanVal($getVal($data, 'pageviewsmobileapp')),
                    cleanVal($getVal($data, 'pageviewsmobileappb2b')),
                    cleanVal($getVal($data, 'pageviewsbrowser')),
                    cleanVal($getVal($data, 'pageviewsbrowserb2b')),
                    cleanVal($getVal($data, 'pageviewstotal')),
                    cleanVal($getVal($data, 'pageviewstotalb2b')),
                    cleanVal($getVal($data, 'sessionsmobileapp')),
                    cleanVal($getVal($data, 'sessionsmobileappb2b')),
                    cleanVal($getVal($data, 'sessionsbrowser')),
                    cleanVal($getVal($data, 'sessionsbrowserb2b')),
                    cleanVal($getVal($data, 'sessionstotal')),
                    cleanVal($getVal($data, 'sessionstotalb2b')),
                    cleanVal($getVal($data, 'featuredofferbuyboxpercentage')),
                    cleanVal($getVal($data, 'featuredofferbuyboxpercentageb2b')),
                    cleanVal($getVal($data, 'orderitemsessionpercentage')),
                    cleanVal($getVal($data, 'orderitemsessionpercentageb2b')),
                    cleanVal($getVal($data, 'unitsessionpercentage')),
                    cleanVal($getVal($data, 'unitsessionpercentageb2b')),
                    cleanVal($getVal($data, 'averageoffercount')),
                    cleanVal($getVal($data, 'averageparentitems')),
                    cleanVal($getVal($data, 'unitsrefunded')),
                    cleanVal($getVal($data, 'unitsrefundedb2b')),
                    cleanVal($getVal($data, 'refundrate')),
                    cleanVal($getVal($data, 'refundrateb2b')),
                    cleanVal($getVal($data, 'feedbackreceived')),
                    cleanVal($getVal($data, 'feedbackreceivedb2b')),
                    cleanVal($getVal($data, 'negativefeedbackreceived')),
                    cleanVal($getVal($data, 'negativefeedbackreceivedb2b')),
                    cleanVal($getVal($data, 'receivednegativefeedbackrate')),
                    cleanVal($getVal($data, 'receivednegativefeedbackrateb2b')),
                    cleanVal($getVal($data, 'atozclaimsgranted')),
                    cleanVal($getVal($data, 'claimsamount')),
                    cleanVal($getVal($data, 'atozclaimsgrantedb2b')),
                    cleanVal($getVal($data, 'claimsamountb2b')),
                    cleanVal($getVal($data, 'shippedproductsales')),
                    cleanVal($getVal($data, 'shippedproductsalesb2b')),
                    cleanVal($getVal($data, 'unitsshipped')),
                    cleanVal($getVal($data, 'unitsshippedb2b')),
                    cleanVal($getVal($data, 'ordersshipped')),
                    cleanVal($getVal($data, 'ordersshippedb2b'))
                ];

                $types = "is" . str_repeat("d", count($params) - 2);
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    $rows_count++;
                }
            }
            return $rows_count;
        }

        function parseDetailCSV($filePath, $conn, $customerId, $reportDate) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $rows = [];
            $rawHeaders = [];
            $rows_count = 0;

            if ($ext == 'xlsx') {
                require_once __DIR__ . '/../../includes/SimpleXLSX.php';
                if ($xlsx = SimpleXLSX::parse($filePath)) {
                    $rows = $xlsx->rows();
                    $rawHeaders = array_shift($rows);
                } else return;
            } else {
                $fileContent = file_get_contents($filePath);
                $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);
                $lines = explode("\n", str_replace("\r", "", $fileContent));
                if (empty($lines)) return;
                $headerLine = array_shift($lines);
                $delimiter = ",";
                if (strpos($headerLine, "\t") !== false) $delimiter = "\t";
                elseif (strpos($headerLine, ";") !== false) $delimiter = ";";
                $rawHeaders = str_getcsv($headerLine, $delimiter);
                foreach ($lines as $line) {
                    if (trim($line)) $rows[] = str_getcsv($line, $delimiter);
                }
            }

            $headers = array_map('slugify', $rawHeaders);
            $colMap = array_flip($headers);

            // Clear existing data for this specific month/customer to prevent duplicates
            $stmt_clear = $conn->prepare("DELETE FROM amazon_detail_report WHERE customer_id = ? AND report_date = ?");
            $stmt_clear->bind_param("is", $customerId, $reportDate);
            $stmt_clear->execute();
            
            $getCol = function($slugs) use ($colMap) {
                foreach ((array)$slugs as $slug) {
                    if (isset($colMap[$slug])) return $colMap[$slug];
                }
                return -1;
            };

            $parentAsinIdx = $getCol(['parentasin']);
            $asinIdx = $getCol(['childasin', 'asin']);
            $titleIdx = $getCol(['title']);
            
            $sql = "INSERT INTO amazon_detail_report 
                (customer_id, report_date, parent_asin, asin, title, sessions_mobile_app, sessions_mobile_app_b2b, sessions_browser, sessions_browser_b2b, sessions_total, sessions_total_b2b, session_percentage_mobile_app, session_percentage_mobile_app_b2b, session_percentage_browser, session_percentage_browser_b2b, session_percentage_total, session_percentage_total_b2b, page_views_mobile_app, page_views_mobile_app_b2b, page_views_browser, page_views_browser_b2b, page_views_total, page_views_total_b2b, page_views_percentage_mobile_app, page_views_percentage_mobile_app_b2b, page_views_percentage_browser, page_views_percentage_browser_b2b, page_views_percentage_total, page_views_percentage_total_b2b, buy_box_percentage, buy_box_percentage_b2b, units_ordered, units_ordered_b2b, unit_session_percentage, unit_session_percentage_b2b, ordered_product_sales, ordered_product_sales_b2b, total_order_items, total_order_items_b2b, units_refunded, units_refunded_b2b, refund_rate, refund_rate_b2b, shipped_product_sales, shipped_product_sales_b2b, units_shipped, units_shipped_b2b, orders_shipped, orders_shipped_b2b) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                title = VALUES(title),
                sessions_total = VALUES(sessions_total),
                page_views_total = VALUES(page_views_total),
                ordered_product_sales = VALUES(ordered_product_sales),
                units_ordered = VALUES(units_ordered)";

            $stmt = $conn->prepare($sql);

            foreach ($rows as $data) {
                if ($asinIdx < 0 || empty($data[$asinIdx])) continue;

                $params = [
                    $customerId, $reportDate,
                    $parentAsinIdx >= 0 ? ($data[$parentAsinIdx] ?? '') : '',
                    $asinIdx >= 0 ? ($data[$asinIdx] ?? '') : '',
                    $titleIdx >= 0 ? ($data[$titleIdx] ?? '') : '',
                    cleanVal($data[$getCol(['sessionsmobileapp'])] ?? 0),
                    cleanVal($data[$getCol(['sessionsmobileappb2b'])] ?? 0),
                    cleanVal($data[$getCol(['sessionsbrowser'])] ?? 0),
                    cleanVal($data[$getCol(['sessionsbrowserb2b'])] ?? 0),
                    cleanVal($data[$getCol(['sessionstotal'])] ?? 0),
                    cleanVal($data[$getCol(['sessionstotalb2b'])] ?? 0),
                    cleanVal($data[$getCol(['sessionpercentagemobileapp'])] ?? 0),
                    cleanVal($data[$getCol(['sessionpercentagemobileappb2b'])] ?? 0),
                    cleanVal($data[$getCol(['sessionpercentagebrowser'])] ?? 0),
                    cleanVal($data[$getCol(['sessionpercentagebrowserb2b'])] ?? 0),
                    cleanVal($data[$getCol(['sessionpercentagetotal'])] ?? 0),
                    cleanVal($data[$getCol(['sessionpercentagetotalb2b'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewsmobileapp'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewsmobileappb2b'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewsbrowser'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewsbrowserb2b'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewstotal'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewstotalb2b'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewspercentagemobileapp'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewspercentagemobileappb2b'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewspercentagebrowser'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewspercentagebrowserb2b'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewspercentagetotal'])] ?? 0),
                    cleanVal($data[$getCol(['pageviewspercentagetotalb2b'])] ?? 0),
                    cleanVal($data[$getCol(['featuredofferbuyboxpercentage', 'buyboxpercentage'])] ?? 0),
                    cleanVal($data[$getCol(['featuredofferbuyboxpercentageb2b', 'buyboxpercentageb2b'])] ?? 0),
                    cleanVal($data[$getCol(['unitsordered'])] ?? 0),
                    cleanVal($data[$getCol(['unitsorderedb2b'])] ?? 0),
                    cleanVal($data[$getCol(['unitsessionpercentage'])] ?? 0),
                    cleanVal($data[$getCol(['unitsessionpercentageb2b'])] ?? 0),
                    cleanVal($data[$getCol(['orderedproductsales'])] ?? 0),
                    cleanVal($data[$getCol(['orderedproductsalesb2b'])] ?? 0),
                    cleanVal($data[$getCol(['totalorderitems'])] ?? 0),
                    cleanVal($data[$getCol(['totalorderitemsb2b'])] ?? 0),
                    cleanVal($data[$getCol(['unitsrefunded'])] ?? 0),
                    cleanVal($data[$getCol(['unitsrefundedb2b'])] ?? 0),
                    cleanVal($data[$getCol(['refundrate'])] ?? 0),
                    cleanVal($data[$getCol(['refundrateb2b'])] ?? 0),
                    cleanVal($data[$getCol(['shippedproductsales'])] ?? 0),
                    cleanVal($data[$getCol(['shippedproductsalesb2b'])] ?? 0),
                    cleanVal($data[$getCol(['unitsshipped'])] ?? 0),
                    cleanVal($data[$getCol(['unitsshippedb2b'])] ?? 0),
                    cleanVal($data[$getCol(['ordersshipped'])] ?? 0),
                    cleanVal($data[$getCol(['ordersshippedb2b'])] ?? 0)
                ];

                $types = "issss" . str_repeat("d", count($params) - 5);
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    $rows_count++;
                }
            }
            return $rows_count;
        }


        function parseAdvertisingReport($fileInfo, $conn, $customerId, $reportDate, $type, $subType = 'general') {
            $filePath = $fileInfo['tmp_name'];
            $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            $rows_count = 0;
            $rows = [];
            if ($ext === 'xlsx') {
                if ($xlsx = SimpleXLSX::parse($filePath)) {
                    $rows = $xlsx->rows();
                } else return;
            } else {
                $handle = fopen($filePath, "r");
                while (($data = fgetcsv($handle)) !== FALSE) { $rows[] = $data; }
                fclose($handle);
            }
            if (empty($rows)) return;
            $rawHeaders = array_shift($rows);
            $headers = array_map('slugify', $rawHeaders);
            $colMap = array_flip($headers);

            $tableName = "amazon_advertising_sp"; // Default to SP
            if ($type === 'sb') $tableName = "amazon_advertising_sb";
            if ($type === 'sd') $tableName = "amazon_advertising_sd";
            
            $monthStart = date('Y-m-01', strtotime($reportDate));
            $monthEnd = date('Y-m-t', strtotime($reportDate));

            if ($type !== 'traffic') {
                $monthKey = $customerId . "_" . $monthStart . "_" . $tableName . "_" . $subType;
                if (!isset($GLOBALS['cleared_months_ads'][$monthKey])) {
                    // Clear existing data for this specific month range/customer/type/subtype
                    $stmt_clear = $conn->prepare("DELETE FROM `$tableName` WHERE customer_id = ? AND report_date BETWEEN ? AND ? AND report_type = ?");
                    $stmt_clear->bind_param("isss", $customerId, $monthStart, $monthEnd, $subType);
                    $stmt_clear->execute();
                    $GLOBALS['cleared_months_ads'][$monthKey] = true;
                }
            }

            foreach ($rows as $data) {
                if (empty($data[0])) continue;
                
                $get = function($slug) use ($colMap, $data) { return $data[$colMap[$slug] ?? -1] ?? ''; };
                $getVal = function($slug) use ($colMap, $data) { return cleanVal($data[$colMap[$slug] ?? -1] ?? 0); };

                // Get row date if available, else fallback to reportMonth-01
                $rowDate = $reportDate;
                $dateVal = $get('date');
                if (!empty($dateVal)) {
                    $parsedDate = date('Y-m-d', strtotime($dateVal));
                    if ($parsedDate && $parsedDate != '1970-01-01') $rowDate = $parsedDate;
                }

                if ($type === 'traffic') {
                    // Traffic report updates existing campaign records for that date
                    $campaign = $get('campaignname');
                    $gross = intval($getVal('grossclicks'));
                    $invalid = intval($getVal('invalidclicks'));
                    
                    // Update all ad tables for this specific date and campaign
                    $updated = false;
                    foreach (['amazon_advertising_sp', 'amazon_advertising_sb', 'amazon_advertising_sd'] as $tbl) {
                        $sql = "UPDATE `$tbl` SET gross_clicks = ?, invalid_clicks = ? WHERE customer_id = ? AND report_date = ? AND campaign_name = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iiiss", $gross, $invalid, $customerId, $rowDate, $campaign);
                        if ($stmt->execute() && $conn->affected_rows > 0) {
                            $updated = true;
                        }
                    }
                    if ($updated) {
                        $rows_count++;
                    }
                    continue;
                }

                $sql = "INSERT INTO `$tableName` (customer_id, report_date, campaign_name, ad_group_name, targeting, match_type, impressions, clicks, ctr, cpc, spend, total_sales, acos, roas, total_orders, total_units, conversion_rate, advertised_sku, advertised_asin, report_type, placement, bidding_strategy) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                $campaign = $get('campaignname') ?: $get('campaign') ?: 'Unknown';
                $adgroup = $get('adgroupname') ?: $get('adgroup') ?: '';
                
                $spend = $getVal('spend') ?: $getVal('cost') ?: 0;
                $sales = $getVal('14daytotalsales') ?: $getVal('7daytotalsales') ?: $getVal('totalsales') ?: $getVal('sales') ?: 0;
                $orders = intval($getVal('14daytotalorders') ?: $getVal('7daytotalorders') ?: $getVal('totalorders') ?: $getVal('orders') ?: 0);
                $units = intval($getVal('14daytotalunits') ?: $getVal('7daytotalunits') ?: $getVal('totalunits') ?: $getVal('units') ?: 0);
                
                $acos = $getVal('totaladvertisingcostofsalesacos') ?: $getVal('acos') ?: 0;
                $placement = $get('placement') ?: 'General';
                $bidding = $get('biddingstrategy') ?: 'N/A';

                $targetValue = $get('targeting');
                if ($subType === 'search_term') {
                    $targetValue = $get('customersearchterm') ?: $targetValue;
                }

                $params = [
                    $customerId, $rowDate,
                    $campaign, $adgroup, $targetValue, $get('matchtype'),
                    intval($getVal('impressions')), intval($getVal('clicks')),
                    $getVal('clickthroughratectr'), $getVal('costperclickcpc'), $spend,
                    $sales, $acos,
                    $getVal('returnonadspendroas'), 
                    $orders, $units, $getVal('conversionrate'),
                    $get('sku') ?: $get('advertisedsku'), $get('asin') ?: $get('advertisedasin'),
                    $subType, $placement, $bidding
                ];
                
                $types = "isssssiiddddddiidsssss";
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    $rows_count++;
                }
            }
            return $rows_count;
        }

        function parseBrandReport($fileInfo, $conn, $customerId, $reportDate, $type) {
            $filePath = $fileInfo['tmp_name'];
            $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            $rows = [];
            $rows_count = 0;
            if ($ext === 'xlsx') {
                if ($xlsx = SimpleXLSX::parse($filePath)) { $rows = $xlsx->rows(); } else return;
            } else {
                $handle = fopen($filePath, "r");
                while (($data = fgetcsv($handle)) !== FALSE) { $rows[] = $data; }
                fclose($handle);
            }
            if (empty($rows)) return;
            $rawHeaders = array_shift($rows);
            $headers = array_map('slugify', $rawHeaders);
            $colMap = array_flip($headers);

            // Clear existing data for this specific month/customer/type
            $stmt_clear = $conn->prepare("DELETE FROM amazon_brand_reports WHERE customer_id = ? AND report_date = ? AND report_type = ?");
            $stmt_clear->bind_param("iss", $customerId, $reportDate, $type);
            $stmt_clear->execute();

            foreach ($rows as $data) {
                if (empty($data[0])) continue;
                // Ensure data array matches headers length
                $padded_data = array_slice(array_pad($data, count($headers), ''), 0, count($headers));
                $row_assoc = array_combine($headers, $padded_data);
                $asin = $row_assoc['asin'] ?? '';
                $sku = $row_assoc['sku'] ?? '';
                $json = json_encode($row_assoc);

                $sql = "INSERT INTO amazon_brand_reports (customer_id, report_date, report_type, asin, sku, data_json) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssss", $customerId, $reportDate, $type, $asin, $sku, $json);
                if ($stmt->execute()) {
                    $rows_count++;
                }
            }
            return $rows_count;
        }

        function parseInventoryReport($filePath, $conn, $customerId, $reportDate) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $rows = [];
            $rows_count = 0;
            if ($ext === 'xlsx') {
                if ($xlsx = SimpleXLSX::parse($filePath)) { $rows = $xlsx->rows(); } else return;
            } else {
                $handle = fopen($filePath, "r");
                while (($data = fgetcsv($handle)) !== FALSE) { $rows[] = $data; }
                fclose($handle);
            }
            if (empty($rows)) return;
            $rawHeaders = array_shift($rows);
            $headers = array_map('slugify', $rawHeaders);
            $colMap = array_flip($headers);

            // Clear existing data for this specific month/customer
            $stmt_clear = $conn->prepare("DELETE FROM amazon_inventory WHERE customer_id = ? AND report_date = ?");
            $stmt_clear->bind_param("is", $customerId, $reportDate);
            $stmt_clear->execute();

            foreach ($rows as $data) {
                if (empty($data[0])) continue;
                $get = function($slug) use ($colMap, $data) { 
                    $idx = $colMap[$slug] ?? -1;
                    return $idx >= 0 ? ($data[$idx] ?? '') : ''; 
                };
                $getVal = function($slug) use ($colMap, $data) { 
                    $idx = $colMap[$slug] ?? -1;
                    return cleanVal($idx >= 0 ? ($data[$idx] ?? 0) : 0); 
                };

                $sql = "INSERT INTO amazon_inventory 
                    (customer_id, report_date, sku, asin, fnsku, product_name, condition_type, your_price, mfn_listing_exists, mfn_fulfillable_quantity, afn_listing_exists, afn_warehouse_quantity, afn_fulfillable_quantity, afn_unsellable_quantity, afn_reserved_quantity, afn_total_quantity, afn_inbound_working_quantity, afn_inbound_shipped_quantity, afn_inbound_receiving_quantity) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $params = [
                    $customerId, $reportDate,
                    $get('sku'), $get('asin'), $get('fnsku'), $get('productname'), $get('condition'),
                    $getVal('yourprice'), $get('mfnlistingexists'), intval($getVal('mfnfulfillablequantity')),
                    $get('afnlistingexists'), intval($getVal('afnwarehousequantity')),
                    intval($getVal('afnfulfillablequantity')), intval($getVal('afnunsellablequantity')),
                    intval($getVal('afnreservedquantity')), intval($getVal('afntotalquantity')),
                    intval($getVal('afninboundworkingquantity')), intval($getVal('afninboundshippedquantity')),
                    intval($getVal('afninboundreceivingquantity'))
                ];
                $stmt->bind_param("issssssdsisiiiiiiii", ...$params);
                if ($stmt->execute()) {
                    $rows_count++;
                }
            }
            return $rows_count;
        }

        function parseTransactionFile($fileInfo, $conn, $customerId, $reportDate) {
            $filePath = $fileInfo['tmp_name'];
            $fileName = $fileInfo['name'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $rows = [];
            $rows_count = 0;
            if ($ext === 'xlsx') {
                if ($xlsx = SimpleXLSX::parse($filePath)) {
                    $rows = $xlsx->rows();
                } else {
                    return; 
                }
            } else {
                $handle = fopen($filePath, "r");
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }

            if (empty($rows)) return;
            
            // Search for headers in first 50 rows (Amazon sometimes has many notes)
            $headerIndex = -1;
            foreach ($rows as $idx => $row) {
                $rowStr = implode(" ", array_map('slugify', $row));
                // Look for at least 3 identifying columns to be sure it's the header row
                $hits = 0;
                if (strpos($rowStr, 'datetime') !== false) $hits++;
                if (strpos($rowStr, 'transactiontype') !== false) $hits++;
                if (strpos($rowStr, 'orderid') !== false) $hits++;
                if (strpos($rowStr, 'productsales') !== false) $hits++;
                
                if ($hits >= 3) {
                    $headerIndex = $idx;
                    break;
                }
            }

            if ($headerIndex === -1) {
                $rawHeaders = array_shift($rows);
            } else {
                $rawHeaders = $rows[$headerIndex];
                // Remove everything before and including the header row
                $rows = array_slice($rows, $headerIndex + 1);
            }

            $headers = array_map('slugify', $rawHeaders);
            $colMap = array_flip($headers);

            $monthStart = date('Y-m-01', strtotime($reportDate));
            $monthEnd = date('Y-m-t', strtotime($reportDate));
            $monthKey = $customerId . "_" . $monthStart . "_" . $monthEnd;
            if (!isset($GLOBALS['cleared_months_trans'][$monthKey])) {
                $stmt_clear = $conn->prepare("DELETE FROM amazon_transaction_report WHERE customer_id = ? AND date_time BETWEEN ? AND ?");
                $monthStartDT = $monthStart . " 00:00:00";
                $monthEndDT = $monthEnd . " 23:59:59";
                $stmt_clear->bind_param("iss", $customerId, $monthStartDT, $monthEndDT);
                $stmt_clear->execute();
                $GLOBALS['cleared_months_trans'][$monthKey] = true;
            }

            $sql = "INSERT INTO amazon_transaction_report 
                    (customer_id, date_time, settlement_id, type, order_id, sku, description, quantity, marketplace, fulfillment, order_city, order_state, order_postal, product_sales, cogs, shipping_credits, gift_wrap_credits, promotional_rebates, sales_tax_collected, market_facilitator_tax, selling_fees, fba_fees, other_transaction_fees, other, total) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            foreach ($rows as $data) {
                if (empty($data[0])) continue;

                $rawDate = $data[$colMap[slugify('date/time')] ?? -1] ?? '';
                if (empty($rawDate) || stripos($rawDate, 'Note:') !== false || stripos($rawDate, 'Glossary') !== false) continue;
                
                $timestamp = strtotime($rawDate);
                if (!$timestamp || $timestamp < 1000000) continue; // Skip invalid dates
                
                $date = date('Y-m-d H:i:s', $timestamp);

                $params = [
                    $customerId,
                    $date,
                    $data[$colMap[slugify('settlement id')] ?? 0] ?? '',
                    $data[$colMap[slugify('type')] ?? 0] ?? '',
                    $data[$colMap[slugify('order id')] ?? 0] ?? '',
                    $data[$colMap[slugify('sku')] ?? 0] ?? '',
                    $data[$colMap[slugify('description')] ?? 0] ?? '',
                    intval($data[$colMap[slugify('quantity')] ?? 0] ?? 0),
                    $data[$colMap[slugify('marketplace')] ?? 0] ?? '',
                    $data[$colMap[slugify('fulfillment')] ?? 0] ?? '',
                    $data[$colMap[slugify('order city')] ?? 0] ?? '',
                    $data[$colMap[slugify('order state')] ?? 0] ?? '',
                    $data[$colMap[slugify('order postal')] ?? 0] ?? '',
                    cleanVal($data[$colMap[slugify('product sales')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('cogs')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('shipping credits')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('gift wrap credits')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('promotional rebates')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('sales tax collected')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('market facilitator tax')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('selling fees')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('fba fees')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('other transaction fees')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('other')] ?? 0] ?? 0),
                    cleanVal($data[$colMap[slugify('total')] ?? 0] ?? 0)
                ];

                $stmt->bind_param("issssssisssssdddddddddddd", ...$params);
                if ($stmt->execute()) {
                    $rows_count++;
                }
            }
            return $rows_count;
        }

        function processFile($tmpPath, $name, $report_date, $customer_id, $conn, &$processed_reports, $zip_name = null) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, ['csv', 'txt', 'xlsx'])) return;

            $file_report_date = $report_date;
            if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $name, $matches)) {
                $file_report_date = $matches[0];
            } elseif (preg_match('/(\d{2})-(\d{2})-(\d{4})/', $name, $matches)) {
                $file_report_date = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            } elseif (preg_match('/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)/i', $name, $matches)) {
                $monthStr = strtolower($matches[1]);
                $mMap = ['jan'=>1, 'feb'=>2, 'mar'=>3, 'apr'=>4, 'may'=>5, 'jun'=>6, 'jul'=>7, 'aug'=>8, 'sep'=>9, 'oct'=>10, 'nov'=>11, 'dec'=>12];
                $mNum = $mMap[$monthStr];
                $year = date('Y');
                if (preg_match('/(\d{4})/', $name, $yMatches)) $year = $yMatches[1];
                $file_report_date = "$year-" . str_pad($mNum, 2, '0', STR_PAD_LEFT) . "-01";
            }

            $content = "";
            if ($ext == 'xlsx') {
                require_once __DIR__ . '/../../includes/SimpleXLSX.php';
                if ($xlsx = \Shuchkin\SimpleXLSX::parse($tmpPath)) {
                    $rows = $xlsx->rows();
                    for($i=0; $i<min(10, count($rows)); $i++) $content .= implode(" ", $rows[$i]) . " ";
                }
            } else {
                $content = file_get_contents($tmpPath);
                $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
                $content = substr($content, 0, 2000);
            }

            $fileType = 'unknown';
            $adSubType = 'general';

            // Identification logic...
            if (stripos($content, 'ASIN') !== false && (stripos($content, 'Sessions') !== false || stripos($content, 'Page Views') !== false)) $fileType = 'detail';
            elseif (stripos($content, 'Ordered Product Sales') !== false || stripos($content, 'Revenue') !== false) $fileType = 'business';
            elseif (stripos($content, 'date/time') !== false || stripos($content, 'transaction type') !== false || stripos($content, 'total') !== false && stripos($content, 'amount') !== false || stripos($content, 'order id') !== false) $fileType = 'transaction';
            elseif (stripos($name, 'LPO') !== false) $fileType = 'transaction';
            elseif (stripos($content, 'Campaign Name') !== false && (stripos($content, 'Impressions') !== false || stripos($content, 'Gross Clicks') !== false)) {
                if (stripos($content, 'Gross Clicks') !== false || stripos($content, 'Invalid Clicks') !== false) $fileType = 'advertising_traffic';
                else {
                    if (stripos($content, 'Sponsored Products') !== false || stripos($name, 'SP_') !== false || stripos($name, 'Sponsored_Products') !== false) $fileType = 'advertising_sp';
                    elseif (stripos($content, 'Sponsored Brands') !== false || stripos($name, 'SB_') !== false || stripos($name, 'Sponsored_Brands') !== false) $fileType = 'advertising_sb';
                    elseif (stripos($content, 'Sponsored Display') !== false || stripos($name, 'SD_') !== false || stripos($name, 'Sponsored_Display') !== false) $fileType = 'advertising_sd';
                    else {
                        if (stripos($name, '_SB') !== false) $fileType = 'advertising_sb';
                        elseif (stripos($name, '_SD') !== false) $fileType = 'advertising_sd';
                        else $fileType = 'advertising_sp';
                    }

                    // Determine subtype
                    if (stripos($name, 'Search Term') !== false || stripos($content, 'Customer Search Term') !== false) $adSubType = 'search_term';
                    elseif (stripos($name, 'Targeting') !== false || stripos($content, 'Targeting') !== false) $adSubType = 'targeting';
                    elseif (stripos($name, 'Placement') !== false || stripos($content, 'Placement') !== false) $adSubType = 'placement';
                    elseif (stripos($name, 'Advertised Product') !== false || stripos($content, 'Advertised Product') !== false) $adSubType = 'advertised_product';
                    elseif (stripos($name, 'Purchased Product') !== false || stripos($content, 'Purchased Product') !== false) $adSubType = 'purchased_product';
                    elseif (stripos($name, 'Campaign') !== false) $adSubType = 'campaign';
                }
            }
            elseif (stripos($content, 'Search Query Performance') !== false || (stripos($content, 'Search Query') !== false && stripos($content, 'Brand Share') !== false)) $fileType = 'brand_search_query';
            elseif (stripos($content, 'Repeat Purchase') !== false && stripos($content, 'Repeat Customers') !== false) $fileType = 'brand_repeat_purchase';
            elseif (stripos($content, 'sku') !== false && stripos($content, 'asin') !== false && (stripos($content, 'fulfillable') !== false || stripos($content, 'afn-') !== false)) $fileType = 'inventory';
            elseif (stripos($content, 'Return') !== false && stripos($content, 'Reason') !== false && stripos($content, 'Disposition') !== false) $fileType = 'returns';
            elseif (stripos($content, 'Reimbursement ID') !== false || stripos($content, 'Reimbursement-ID') !== false) $fileType = 'reimbursements';

            $fileInfo = ['tmp_name' => $tmpPath, 'name' => $name];
            $rows_processed = 0;
            $report_category = '';

            switch ($fileType) {
                case 'business':
                    $rows_processed = parseBusinessCSV($tmpPath, $conn, $customer_id, $file_report_date);
                    $processed_reports[] = "Business ($name)";
                    $report_category = 'Business';
                    break;
                case 'detail':
                    $rows_processed = parseDetailCSV($tmpPath, $conn, $customer_id, $file_report_date);
                    $processed_reports[] = "Detail ($name)";
                    $report_category = 'Detail';
                    break;
                case 'transaction':
                    $rows_processed = parseTransactionFile($fileInfo, $conn, $customer_id, $file_report_date);
                    $processed_reports[] = "Transaction ($name)";
                    $report_category = 'Transaction';
                    break;
                case 'settlement':
                    // Not fully defined, skip row count
                    $processed_reports[] = "Settlement ($name)";
                    $report_category = 'Settlement';
                    break;
                case 'advertising_sp':
                case 'advertising_sb':
                case 'advertising_sd':
                case 'advertising_traffic': 
                    $adType = str_replace('advertising_', '', $fileType);
                    $rows_processed = parseAdvertisingReport($fileInfo, $conn, $customer_id, $file_report_date, $adType, $adSubType); 
                    $processed_reports[] = "Ads ".strtoupper($adType)." ($adSubType) ($name)";
                    $report_category = "Ads " . strtoupper($adType) . " ($adSubType)";
                    break;
                case 'brand_search_query':
                case 'brand_repeat_purchase': 
                    $bType = str_replace('brand_', '', $fileType);
                    $rows_processed = parseBrandReport($fileInfo, $conn, $customer_id, $file_report_date, $bType); 
                    $processed_reports[] = "Brand ".str_replace('_',' ',$fileType)." ($name)";
                    $report_category = "Brand " . str_replace('_',' ',$fileType);
                    break;
                case 'inventory': 
                    $rows_processed = parseInventoryReport($tmpPath, $conn, $customer_id, $file_report_date); 
                    $processed_reports[] = "Inventory ($name)";
                    $report_category = 'Inventory';
                    break;
                case 'returns':
                case 'reimbursements': 
                    // Not fully defined, skip row count
                    $processed_reports[] = ucfirst($fileType)." ($name)";
                    $report_category = ucfirst($fileType);
                    break;
                default:
                    $dyn_res = parseDynamicExcel($fileInfo, $conn);
                    if ($dyn_res) {
                        $dyn = $dyn_res['table'];
                        $rows_processed = $dyn_res['rows'];
                        $processed_reports[] = "Custom -> $dyn ($name)";
                        $report_category = "Custom ($dyn)";
                    }
                    break;
            }

            if ($report_category !== '') {
                $user_id = $_SESSION['user_id'] ?? 0;
                $stmt_log = $conn->prepare("INSERT INTO file_upload_log (customer_id, zip_filename, filename, report_type, report_date, rows_processed, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_log->bind_param("issssii", $customer_id, $zip_name, $name, $report_category, $file_report_date, $rows_processed, $user_id);
                $stmt_log->execute();
            }
        }

        // Loop through all uploaded files
        foreach ($_FILES['reports']['name'] as $i => $name) {
            if ($_FILES['reports']['error'][$i] != 0) continue;
            $tmpPath = $_FILES['reports']['tmp_name'][$i];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if ($ext == 'zip') {
                $zip = new ZipArchive;
                if ($zip->open($tmpPath) === TRUE) {
                    $extractPath = '../../uploads/temp_zip_' . time() . '_' . $i . '/';
                    if (!is_dir($extractPath)) mkdir($extractPath, 0777, true);
                    $zip->extractTo($extractPath);
                    $zip->close();

                    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extractPath));
                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            processFile($file->getPathname(), $file->getFilename(), $report_date, $customer_id, $conn, $processed_reports, $name);
                        }
                    }
                    // Cleanup temp folder
                    $it = new RecursiveDirectoryIterator($extractPath, RecursiveDirectoryIterator::SKIP_DOTS);
                    $files_to_del = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                    foreach($files_to_del as $f) {
                        if ($f->isDir()) rmdir($f->getRealPath());
                        else unlink($f->getRealPath());
                    }
                    rmdir($extractPath);
                }
            } else {
                processFile($tmpPath, $name, $report_date, $customer_id, $conn, $processed_reports);
            }
        }

        if (!empty($processed_reports)) {
            $success = "Successfully processed " . count($processed_reports) . " reports: " . implode(", ", $processed_reports);
        } else {
            $error = "No valid Amazon reports were identified from the uploaded files.";
        }
    } else {
        $error = "Please select at least one file to upload.";
    }
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<style>
    .upload-zone {
        border: 2px dashed #CBD5E1;
        background: #F8FAFC;
        border-radius: 12px;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    .upload-zone:hover, .upload-zone.dragover {
        border-color: var(--primary);
        background: #F1F5F9;
        transform: translateY(-2px);
    }
    .upload-zone i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    .file-list {
        margin-top: 1.5rem;
        text-align: left;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    .file-item {
        background: white;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #E2E8F0;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>

<div class="card">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800;">Unified Amazon Data Ingestion</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Drop all your reports together. We'll identify and process them automatically.</p>
        </div>
        <div style="background: var(--primary-light); color: var(--primary); padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">
            <i class="fas fa-magic"></i> AUTO-IDENTIFICATION ACTIVE
        </div>
    </div>

    <?php if ($error): ?>
        <div style="background: #FFF1F2; border-left: 4px solid var(--danger); padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; color: var(--danger); font-weight: 600;">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background: #F0FDF4; border-left: 4px solid var(--accent); padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; color: #166534; font-weight: 600;">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="uploadForm">
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label>Target Customer</label>
                <select name="customer_id" required style="width: 100%;">
                    <option value="">-- Choose Account --</option>
                    <?php $customers->data_seek(0); while ($row = $customers->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['customer_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Reporting Month</label>
                <input type="month" name="report_month" required style="width: 100%;">
            </div>
        </div>

        <div class="upload-zone" id="dropZone" onclick="document.getElementById('fileInput').click()">
            <i class="fas fa-cloud-upload-alt"></i>
            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">Click or Drag ZIP/Reports Here</h3>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Support for ZIP Batches, Business, Advertising, Brand, and Transaction reports (.zip, .csv, .txt, .xlsx)</p>
            <input type="file" name="reports[]" id="fileInput" multiple style="display: none;" onchange="updateFileList(this)">
            
            <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 2rem;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: #ef4444;">
                    <input type="checkbox" name="clean_db" value="1"> Clean Database Before Import
                </label>
            </div>

            <div id="fileList" class="file-list"></div>
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1rem; margin-top: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
            <i class="fas fa-bolt"></i> START BATCH PROCESSING
        </button>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 2rem;">
    <div class="card" style="margin-bottom: 0; padding: 1rem; text-align: center; background: #f0f9ff; border-color: #bae6fd;">
        <i class="fas fa-file-invoice-dollar" style="color: #0369a1; margin-bottom: 0.5rem;"></i>
        <div style="font-size: 0.75rem; font-weight: 700; color: #0369a1;">Business</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 1rem; text-align: center; background: #f0fdf4; border-color: #bbf7d0;">
        <i class="fas fa-chart-line" style="color: #15803d; margin-bottom: 0.5rem;"></i>
        <div style="font-size: 0.75rem; font-weight: 700; color: #15803d;">Detail</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 1rem; text-align: center; background: #fef2f2; border-color: #fecaca;">
        <i class="fas fa-file-invoice" style="color: #b91c1c; margin-bottom: 0.5rem;"></i>
        <div style="font-size: 0.75rem; font-weight: 700; color: #b91c1c;">Settlement</div>
    </div>
    <div class="card" style="margin-bottom: 0; padding: 1rem; text-align: center; background: #fffbeb; border-color: #fef3c7;">
        <i class="fas fa-file-excel" style="color: #b45309; margin-bottom: 0.5rem;"></i>
        <div style="font-size: 0.75rem; font-weight: 700; color: #b45309;">Transaction</div>
    </div>
</div>

<div class="card" style="margin-top: 2rem; background: #F8FAFC; border-color: #E2E8F0;">
    <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">
        <i class="fas fa-lightbulb" style="color: #eab308;"></i> Pro Tips
    </h3>
    <ul style="font-size: 0.875rem; color: var(--text-muted); margin-left: 1.25rem;">
        <li>You can select multiple files at once in the file picker.</li>
        <li>The system automatically detects the report type based on its internal structure.</li>
        <li><strong>Unknown Formats:</strong> If a report is not recognized, the system will automatically create a new table (prefixed with <code>dyn_</code>) and store the data there. You can view these in the "Dynamic Excel" section.</li>
    </ul>
</div>

<script>
    function updateFileList(input) {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';
        
        if (input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'file-item';
                
                let icon = 'fa-file';
                if (file.name.endsWith('.csv')) icon = 'fa-file-csv';
                if (file.name.endsWith('.xlsx')) icon = 'fa-file-excel';
                if (file.name.endsWith('.txt')) icon = 'fa-file-alt';
                
                item.innerHTML = `
                    <i class="fas ${icon}" style="color: var(--primary);"></i>
                    <div style="flex: 1;">
                        <div style="font-weight: 600;">${file.name}</div>
                        <div style="font-size: 0.7rem; color: #64748b;">${(file.size / 1024).toFixed(1)} KB</div>
                    </div>
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                `;
                fileList.appendChild(item);
            });
        }
    }

    const dropZone = document.getElementById('dropZone');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
    });

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById('fileInput').files = files;
        updateFileList(document.getElementById('fileInput'));
    }
</script>

<?php include '../../includes/footer.php'; ?>
