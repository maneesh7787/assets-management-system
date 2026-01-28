<?php
/**
 * Employee - My Assets
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require employee role
requireRole(3);

$pageTitle = 'My Assets';

$employeeId = getCurrentEmployeeId();

// Get all assets (approved, pending, rejected)
$assetsQuery = "SELECT aa.*, a.asset_type, a.brand, a.serial_number, a.condition_at_issue, a.current_status,
                u.username as approved_by_name
                FROM asset_assignments aa
                INNER JOIN assets a ON aa.asset_id = a.asset_id
                LEFT JOIN users u ON aa.approved_by = u.user_id
                WHERE aa.employee_id = '$employeeId'
                ORDER BY aa.created_at DESC";
$assets = fetchAll(executeQuery($assetsQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-laptop"></i> My Assets</h2>
    <a href="submit_assets.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Submit New Assets
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Asset Records</h5>
    </div>
    <div class="card-body">
        <?php if (empty($assets)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Assets Found</h4>
                <p class="text-muted">You haven't submitted any assets yet.</p>
                <a href="submit_assets.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Submit Assets
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Asset Type</th>
                            <th>Brand</th>
                            <th>Serial Number</th>
                            <th>Condition</th>
                            <th>Date of Issue</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Approval Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($asset['asset_type']); ?></td>
                            <td><?php echo htmlspecialchars($asset['brand']); ?></td>
                            <td><code><?php echo htmlspecialchars($asset['serial_number']); ?></code></td>
                            <td><?php echo htmlspecialchars($asset['condition_at_issue']); ?></td>
                            <td><?php echo formatDate($asset['assigned_date']); ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($asset['assignment_status']); ?>">
                                    <?php echo $asset['assignment_status']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($asset['approved_by_name'] ?? 'N/A'); ?></td>
                            <td><?php echo $asset['approval_date'] ? formatDateTime($asset['approval_date']) : 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> You cannot edit or delete assets after submission. If you need to make changes, please contact IT/Admin.
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
