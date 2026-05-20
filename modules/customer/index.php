<?php
require_once '../../config.php';
require_once '../../includes/functions.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?msg=Account Purged Successfully");
    exit();
}

$customers = get_all_customers();

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 800;">Account Management</h2>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Manage individual Amazon Seller Profiles and synchronization settings.</p>
    </div>
    <a href="manage.php" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> PROVISION NEW ACCOUNT
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: #F0FDF4; border-left: 4px solid var(--accent); padding: 1rem; margin-bottom: 2rem; border-radius: 8px; color: #166534; font-weight: 600;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Seller Profile</th>
                    <th>Entity/Company</th>
                    <th>Email Contact</th>
                    <th>Connectivity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $customers->fetch_assoc()): ?>
                <tr>
                    <td><span style="color: var(--text-muted); font-weight: 700;">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></span></td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                    </td>
                    <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <span style="font-size: 0.75rem; color: #10B981;"><i class="fas fa-link"></i> Live</span>
                    </td>
                    <td>
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; <?php echo $row['status'] == 'Active' ? 'background: #DCFCE7; color: #166534;' : 'background: #FEE2E2; color: #991B1B;'; ?>">
                            <?php echo strtoupper($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="manage.php?id=<?php echo $row['id']; ?>" class="btn" style="padding: 6px; color: var(--primary-light);">
                            <i class="fas fa-cog"></i>
                        </a>
                        <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn" style="padding: 6px; color: var(--danger);" onclick="return confirm('WARNING: Purging an account will delete all associated analytics. Proceed?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($customers->num_rows == 0): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="fas fa-user-slash" style="font-size: 2rem; display: block; margin-bottom: 1rem; opacity: 0.3;"></i>
                        No seller accounts provisioned yet.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
