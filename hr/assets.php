<?php
/**
 * HR - View Assets (Read-only)
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require HR role
requireRole(2);

$pageTitle = 'View Assets';

// Get all approved assets
$assetsQuery = "SELECT a.*, aa.assigned_date, aa.approval_date,
                e.full_name, e.emp_code, e.department
                FROM assets a
                LEFT JOIN asset_assignments aa ON a.asset_id = aa.asset_id AND aa.assignment_status = 'Approved' AND aa.returned_date IS NULL
                LEFT JOIN employees e ON aa.employee_id = e.employee_id
                ORDER BY a.created_at DESC";
$assets = fetchAll(executeQuery($assetsQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-laptop"></i> View Assets</h2>
    <span class="text-muted">(Read-Only Access)</span>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Assets</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Asset Type</th>
                        <th>Brand</th>
                        <th>Serial Number</th>
                        <th>Condition</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Department</th>
                        <th>Assigned Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $asset): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($asset['asset_type']); ?></td>
                        <td><?php echo htmlspecialchars($asset['brand']); ?></td>
                        <td><code><?php echo htmlspecialchars($asset['serial_number']); ?></code></td>
                        <td><?php echo htmlspecialchars($asset['condition_at_issue']); ?></td>
                        <td>
                            <span class="badge <?php echo getStatusBadgeClass($asset['current_status']); ?>">
                                <?php echo $asset['current_status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($asset['full_name']): ?>
                                <strong><?php echo htmlspecialchars($asset['full_name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($asset['emp_code']); ?></small>
                            <?php else: ?>
                                <span class="text-muted">Not Assigned</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($asset['department'] ?? '-'); ?></td>
                        <td><?php echo $asset['assigned_date'] ? formatDate($asset['assigned_date']) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> As an HR user, you have read-only access to asset information. Please contact IT/Admin for any changes or asset management tasks.
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
