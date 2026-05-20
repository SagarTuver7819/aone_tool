<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

$table = $_GET['table'] ?? '';

// Security: Ensure table name starts with dyn_
if (strpos($table, 'dyn_') !== 0) {
    die("Unauthorized access.");
}

// Get columns
$columns = [];
$res = $conn->query("DESCRIBE `$table` ");
while ($row = $res->fetch_assoc()) {
    $columns[] = $row['Field'];
}

// Pagination
$limit = 50;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_res = $conn->query("SELECT COUNT(*) as total FROM `$table` ");
$total_rows = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Get data
$data_res = $conn->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT $limit OFFSET $offset");

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="card">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800;"><?php echo htmlspecialchars($table); ?></h2>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Showing records for dynamic table. Total: <?php echo number_format($total_rows); ?> rows.</p>
        </div>
        <a href="index.php" class="btn-primary" style="background: var(--text-muted); border-color: var(--text-muted);">
            <i class="fas fa-arrow-left"></i> Back to Upload
        </a>
    </div>

    <div style="overflow-x: auto; max-height: 70vh;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
            <thead>
                <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                    <?php foreach ($columns as $col): ?>
                        <th style="text-align: left; padding: 1rem; white-space: nowrap;"><?php echo ucwords(str_replace('_', ' ', $col)); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $data_res->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <?php foreach ($columns as $col): ?>
                            <td style="padding: 0.75rem 1rem;"><?php echo htmlspecialchars($row[$col] ?? ''); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem;">
            <?php for ($i = 1; $i <= min($total_pages, 10); $i++): ?>
                <a href="?table=<?php echo urlencode($table); ?>&page=<?php echo $i; ?>" 
                   style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #E2E8F0; text-decoration: none; color: <?php echo $i == $page ? 'white' : 'var(--text)'; ?>; background: <?php echo $i == $page ? 'var(--primary)' : 'white'; ?>;">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($total_pages > 10): ?>
                <span style="padding: 0.5rem;">...</span>
                <a href="?table=<?php echo urlencode($table); ?>&page=<?php echo $total_pages; ?>" 
                   style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #E2E8F0; text-decoration: none; color: <?php echo $page == $total_pages ? 'white' : 'var(--text)'; ?>; background: <?php echo $page == $total_pages ? 'var(--primary)' : 'white'; ?>;">
                    <?php echo $total_pages; ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
