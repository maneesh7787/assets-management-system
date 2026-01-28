<?php
/**
 * Admin Dashboard
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Admin Dashboard';

// Get dashboard statistics
$totalEmployees = countRecords('employees', "status = 'Active'");
$totalAssets = countRecords('assets');
$pendingApprovals = countRecords('asset_assignments', "assignment_status = 'Pending'");
$returnedAssets = countRecords('assets', "current_status = 'Returned'");
$assignedAssets = countRecords('assets', "current_status = 'Assigned'");
$availableAssets = countRecords('assets', "current_status = 'Available'");

// Get asset breakdown by type
$assetTypesQuery = "SELECT asset_type, COUNT(*) as count FROM assets GROUP BY asset_type ORDER BY count DESC";
$assetTypesResult = executeQuery($assetTypesQuery);
$assetTypes = fetchAll($assetTypesResult);

// Get recent asset assignments
$recentAssignmentsQuery = "SELECT aa.*, e.full_name, e.emp_code, a.asset_type, a.brand, a.serial_number 
                            FROM asset_assignments aa 
                            INNER JOIN employees e ON aa.employee_id = e.employee_id 
                            INNER JOIN assets a ON aa.asset_id = a.asset_id 
                            ORDER BY aa.created_at DESC LIMIT 5";
$recentAssignments = fetchAll(executeQuery($recentAssignmentsQuery));

// Get employees near exit
$exitingEmployeesQuery = "SELECT * FROM employees WHERE status = 'Exiting' ORDER BY updated_at DESC LIMIT 5";
$exitingEmployees = fetchAll(executeQuery($exitingEmployeesQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <div>
        <span class="text-muted">Welcome, <strong><?php echo getCurrentUsername(); ?></strong></span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <h3 class="text-primary"><?php echo $totalEmployees; ?></h3>
                <p><i class="bi bi-people"></i> Total Employees</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <h3 class="text-success"><?php echo $totalAssets; ?></h3>
                <p><i class="bi bi-laptop"></i> Total Assets</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <h3 class="text-warning"><?php echo $pendingApprovals; ?></h3>
                <p><i class="bi bi-clock-history"></i> Pending Approvals</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <h3 class="text-info"><?php echo $returnedAssets; ?></h3>
                <p><i class="bi bi-box-arrow-in-down"></i> Returned Assets</p>
            </div>
        </div>
    </div>
</div>

<!-- Asset Status -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Asset Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Assigned</span>
                        <strong><?php echo $assignedAssets; ?></strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: <?php echo $totalAssets > 0 ? ($assignedAssets / $totalAssets * 100) : 0; ?>%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Available</span>
                        <strong><?php echo $availableAssets; ?></strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: <?php echo $totalAssets > 0 ? ($availableAssets / $totalAssets * 100) : 0; ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Returned</span>
                        <strong><?php echo $returnedAssets; ?></strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: <?php echo $totalAssets > 0 ? ($returnedAssets / $totalAssets * 100) : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Assets by Type</h5>
            </div>
            <div class="card-body">
                <?php foreach ($assetTypes as $type): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><?php echo htmlspecialchars($type['asset_type']); ?></span>
                        <strong><?php echo $type['count']; ?></strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $totalAssets > 0 ? ($type['count'] / $totalAssets * 100) : 0; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Asset Assignments</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentAssignments)): ?>
                    <p class="text-muted">No recent assignments.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentAssignments as $assignment): ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?php echo htmlspecialchars($assignment['full_name']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($assignment['asset_type']); ?> - 
                                        <?php echo htmlspecialchars($assignment['serial_number']); ?>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge <?php echo getStatusBadgeClass($assignment['assignment_status']); ?>">
                                        <?php echo $assignment['assignment_status']; ?>
                                    </span>
                                    <br>
                                    <small class="text-muted"><?php echo formatDateTime($assignment['created_at']); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Employees Exiting</h5>
            </div>
            <div class="card-body">
                <?php if (empty($exitingEmployees)): ?>
                    <p class="text-muted">No employees in exit process.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($exitingEmployees as $employee): ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong><?php echo htmlspecialchars($employee['full_name']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($employee['emp_code']); ?> - <?php echo htmlspecialchars($employee['department']); ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning">Exiting</span>
                                    <br>
                                    <a href="employees.php?action=view&id=<?php echo $employee['employee_id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="employees.php?action=add" class="btn btn-primary w-100">
                            <i class="bi bi-person-plus"></i> Add Employee
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="assets.php?action=add" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle"></i> Add Asset
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="approvals.php" class="btn btn-warning w-100">
                            <i class="bi bi-check-circle"></i> Review Approvals
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="reports.php" class="btn btn-info w-100">
                            <i class="bi bi-file-bar-graph"></i> View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
