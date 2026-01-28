<?php
/**
 * Employee Dashboard
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require employee role
requireRole(3);

$pageTitle = 'Employee Dashboard';

$employeeId = getCurrentEmployeeId();

// Get employee details
$employee = getEmployeeById($employeeId);

// Get asset statistics
$totalAssetsAssigned = countRecords('asset_assignments', "employee_id = '$employeeId' AND assignment_status = 'Approved'");
$pendingApprovals = countRecords('asset_assignments', "employee_id = '$employeeId' AND assignment_status = 'Pending'");
$rejectedAssets = countRecords('asset_assignments', "employee_id = '$employeeId' AND assignment_status = 'Rejected'");

// Get assigned assets
$assignedAssetsQuery = "SELECT aa.*, a.asset_type, a.brand, a.serial_number, a.condition_at_issue
                         FROM asset_assignments aa
                         INNER JOIN assets a ON aa.asset_id = a.asset_id
                         WHERE aa.employee_id = '$employeeId' AND aa.assignment_status = 'Approved'
                         ORDER BY aa.approval_date DESC";
$assignedAssets = fetchAll(executeQuery($assignedAssetsQuery));

// Get pending assets
$pendingAssetsQuery = "SELECT aa.*, a.asset_type, a.brand, a.serial_number
                        FROM asset_assignments aa
                        INNER JOIN assets a ON aa.asset_id = a.asset_id
                        WHERE aa.employee_id = '$employeeId' AND aa.assignment_status = 'Pending'
                        ORDER BY aa.created_at DESC";
$pendingAssets = fetchAll(executeQuery($pendingAssetsQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> My Dashboard</h2>
    <div>
        <span class="text-muted">Welcome, <strong><?php echo htmlspecialchars($employee['full_name']); ?></strong></span>
    </div>
</div>

<!-- Employee Info Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-badge"></i> My Profile</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Employee Code:</strong></td>
                        <td><?php echo htmlspecialchars($employee['emp_code']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Full Name:</strong></td>
                        <td><?php echo htmlspecialchars($employee['full_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo htmlspecialchars($employee['email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo htmlspecialchars($employee['phone']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Department:</strong></td>
                        <td><?php echo htmlspecialchars($employee['department']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Designation:</strong></td>
                        <td><?php echo htmlspecialchars($employee['designation']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Work Location:</strong></td>
                        <td><?php echo htmlspecialchars($employee['work_location']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date of Joining:</strong></td>
                        <td><?php echo formatDate($employee['date_of_joining']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <h3 class="text-success"><?php echo $totalAssetsAssigned; ?></h3>
                <p><i class="bi bi-laptop"></i> Assigned Assets</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <h3 class="text-warning"><?php echo $pendingApprovals; ?></h3>
                <p><i class="bi bi-clock-history"></i> Pending Approval</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stat-card border-danger">
            <div class="card-body">
                <h3 class="text-danger"><?php echo $rejectedAssets; ?></h3>
                <p><i class="bi bi-x-circle"></i> Rejected</p>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Assets -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-laptop"></i> My Assigned Assets</h5>
    </div>
    <div class="card-body">
        <?php if (empty($assignedAssets)): ?>
            <p class="text-muted text-center py-3">No assets assigned yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Asset Type</th>
                            <th>Brand</th>
                            <th>Serial Number</th>
                            <th>Condition</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignedAssets as $asset): ?>
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
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pending Approvals -->
<?php if (!empty($pendingAssets)): ?>
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pending Approvals</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Asset Type</th>
                        <th>Brand</th>
                        <th>Serial Number</th>
                        <th>Submitted Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingAssets as $asset): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($asset['asset_type']); ?></td>
                        <td><?php echo htmlspecialchars($asset['brand']); ?></td>
                        <td><code><?php echo htmlspecialchars($asset['serial_number']); ?></code></td>
                        <td><?php echo formatDateTime($asset['created_at']); ?></td>
                        <td>
                            <span class="badge <?php echo getStatusBadgeClass($asset['assignment_status']); ?>">
                                <?php echo $asset['assignment_status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                <a href="submit_assets.php" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle"></i> Submit New Assets
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="my_assets.php" class="btn btn-success w-100">
                    <i class="bi bi-laptop"></i> View All Assets
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="history.php" class="btn btn-info w-100">
                    <i class="bi bi-clock-history"></i> Asset History
                </a>
            </div>
        </div>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
