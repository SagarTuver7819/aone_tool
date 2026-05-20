<?php
require_once '../../config.php';
require_once '../../includes/functions.php';
require_once '../../includes/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

$error = '';
$success = '';

// Helper function to sanitize names for SQL
function sanitize_sql_name($name) {
    // Remove special characters and replace spaces with underscores
    $name = preg_replace('/[^a-zA-Z0-9_]/', '_', trim($name));
    $name = strtolower($name);
    // Ensure it doesn't start with a number
    if (preg_match('/^[0-9]/', $name)) {
        $name = 'col_' . $name;
    }
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];
    $custom_table_name = !empty($_POST['table_name']) ? sanitize_sql_name($_POST['table_name']) : sanitize_sql_name(pathinfo($file['name'], PATHINFO_FILENAME));
    
    // Prefix to avoid conflicts with core tables
    $full_table_name = "dyn_" . $custom_table_name;
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $rows = [];
    $headers = [];

    try {
        if ($ext == 'xlsx') {
            if ($xlsx = SimpleXLSX::parse($file['tmp_name'])) {
                $rows = $xlsx->rows();
                $headers = array_shift($rows);
            } else {
                throw new Exception(SimpleXLSX::parseError());
            }
        } else if ($ext == 'csv') {
            $handle = fopen($file['tmp_name'], "r");
            $headers = fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        } else {
            throw new Exception("Unsupported file format. Please upload .xlsx or .csv");
        }

        if (empty($headers)) {
            throw new Exception("File is empty or could not read headers.");
        }

        // Sanitize headers for column names
        $clean_headers = [];
        foreach ($headers as $h) {
            $clean_headers[] = sanitize_sql_name($h);
        }

        // 1. Check if table exists
        $check_table = $conn->query("SHOW TABLES LIKE '$full_table_name'");
        if ($check_table->num_rows == 0) {
            // Create Table
            $sql = "CREATE TABLE `$full_table_name` (id INT AUTO_INCREMENT PRIMARY KEY";
            foreach ($clean_headers as $col) {
                $sql .= ", `$col` TEXT";
            }
            $sql .= ", created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
            if (!$conn->query($sql)) {
                throw new Exception("Error creating table: " . $conn->error);
            }
            $success = "New table `$full_table_name` created successfully. ";
        } else {
            // Table exists, check for missing columns
            $existing_columns = [];
            $res = $conn->query("DESCRIBE `$full_table_name`");
            while ($row = $res->fetch_assoc()) {
                $existing_columns[] = $row['Field'];
            }

            foreach ($clean_headers as $col) {
                if (!in_array($col, $existing_columns)) {
                    $conn->query("ALTER TABLE `$full_table_name` ADD COLUMN `$col` TEXT");
                }
            }
            $success = "Table `$full_table_name` updated with new data. ";
        }

        // 2. Insert Data
        if (!empty($rows)) {
            $placeholders = str_repeat('?,', count($clean_headers) - 1) . '?';
            $col_names = "`" . implode("`,`", $clean_headers) . "`";
            $sql = "INSERT INTO `$full_table_name` ($col_names) VALUES ($placeholders)";
            $stmt = $conn->prepare($sql);

            $count = 0;
            foreach ($rows as $row) {
                // Pad or trim row to match headers count
                $row_data = array_slice(array_pad($row, count($clean_headers), ''), 0, count($clean_headers));
                
                $types = str_repeat('s', count($row_data));
                $stmt->bind_param($types, ...$row_data);
                if ($stmt->execute()) {
                    $count++;
                }
            }
            $success .= "Inserted $count rows.";
        } else {
            $success .= "No data rows found to insert.";
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get list of dynamic tables
$dynamic_tables = [];
$res = $conn->query("SHOW TABLES LIKE 'dyn_%'");
while ($row = $res->fetch_array()) {
    $dynamic_tables[] = $row[0];
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800;">Dynamic Excel Importer</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Upload any Excel/CSV. We'll automatically create tables and columns.</p>
        </div>
        <div style="background: var(--primary-light); color: var(--primary); padding: 0.5rem 1rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">
            <i class="fas fa-database"></i> DYNAMIC SCHEMA MODE
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

    <form method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label>Table Name (Optional)</label>
                <input type="text" name="table_name" placeholder="e.g. sales_report" style="width: 100%;">
                <small style="color: var(--text-muted);">Leave blank to use filename. Prefix 'dyn_' will be added automatically.</small>
            </div>
            <div class="form-group">
                <label>Select File (.xlsx, .csv)</label>
                <input type="file" name="excel_file" required style="width: 100%;">
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1rem; border-radius: 12px;">
            <i class="fas fa-upload"></i> UPLOAD AND PROCESS
        </button>
    </form>
</div>

<div class="card" style="margin-top: 2rem;">
    <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem;">Existing Dynamic Tables</h3>
    
    <?php if (empty($dynamic_tables)): ?>
        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
            <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p>No dynamic tables found. Upload a file to get started.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #F1F5F9;">
                        <th style="text-align: left; padding: 1rem;">Table Name</th>
                        <th style="text-align: left; padding: 1rem;">Records</th>
                        <th style="text-align: right; padding: 1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dynamic_tables as $table): 
                        $count_res = $conn->query("SELECT COUNT(*) as total FROM `$table` ");
                        $count = $count_res ? $count_res->fetch_assoc()['total'] : 0;
                    ?>
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem; font-weight: 600;"><?php echo $table; ?></td>
                            <td style="padding: 1rem;"><?php echo number_format($count); ?> rows</td>
                            <td style="padding: 1rem; text-align: right;">
                                <a href="view_data.php?table=<?php echo urlencode($table); ?>" class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">
                                    <i class="fas fa-eye"></i> View Data
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
