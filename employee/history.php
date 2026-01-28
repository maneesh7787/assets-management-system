<?php
/**
 * Employee Asset History
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require employee role
requireRole(3);

$pageTitle = 'Asset History';

$employeeId = getCurrentEmployeeId();

// Get asset history
$historyQuery = "SELECT ah.*, a.asset_type, a.brand, a.serial_number, u.username as performed_by_name
                 FROM asset_history ah
                 INNER JOIN assets a ON ah.asset_id = a.asset_id
                 LEFT JOIN users u ON ah.performed_by = u.user_id
                 WHERE ah.employee_id = '$employeeId'
                 ORDER BY ah.action_date DESC";
$history = fetchAll(executeQuery($historyQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history"></i> Asset History</h2>
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Complete Asset History</h5>
    </div>
    <div class="card-body">
        <?php if (empty($history)): ?>
            <p class="text-muted text-center py-3">No history available.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Asset</th>
                            <th>Action</th>
                            <th>Old Status</th>
                            <th>New Status</th>
                            <th>Performed By</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $record): ?>
                        <tr>
                            <td><?php echo formatDateTime($record['action_date']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($record['asset_type']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($record['serial_number']); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $record['action_type']; ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($record['old_status'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($record['new_status'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($record['performed_by_name'] ?? 'System'); ?></td>
                            <td><?php echo htmlspecialchars($record['remarks'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
