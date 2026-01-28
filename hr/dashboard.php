<?php
/**
 * HR Dashboard
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require HR role
requireRole(2);

$pageTitle = 'HR Dashboard';

// Get statistics
$totalEmployees = countRecords('employees', "status = 'Active'");
$exitingEmployees = countRecords('employees', "status = 'Exiting'");
$totalAssets = countRecords('assets', "current_status = 'Assigned'");
$returnedAssets = countRecords('assets', "current_status = 'Returned'");

// Get employees with asset count
$employeesQuery = "SELECT e.*, 
                   (SELECT COUNT(*) FROM asset_assignments aa 
                    WHERE aa.employee_id = e.employee_id AND aa.assignment_status = 'Approved') as asset_count
                   FROM employees e 
                   WHERE e.status = 'Active'
                   ORDER BY e.full_name ASC";
$employees = fetchAll(executeQuery($employeesQuery));

// Get exiting employees with assets
$exitingQuery = "SELECT e.*, 
                 (SELECT COUNT(*) FROM asset_assignments aa 
                  WHERE aa.employee_id = e.employee_id AND aa.assignment_status = 'Approved' AND aa.returned_date IS NULL) as pending_assets
                 FROM employees e 
                 WHERE e.status = 'Exiting'
                 ORDER BY e.updated_at DESC";
$exitingEmployeesList = fetchAll(executeQuery($exitingQuery));

// Get recent asset assignments
$recentAssignmentsQuery = "SELECT aa.*, e.full_name, e.emp_code, a.asset_type, a.serial_number 
                            FROM asset_assignments aa 
                            INNER JOIN employees e ON aa.employee_id = e.employee_id 
                            INNER JOIN assets a ON aa.asset_id = a.asset_id 
                            WHERE aa.assignment_status = 'Approved'
                            ORDER BY aa.approval_date DESC LIMIT 10";
$recentAssignments = fetchAll(executeQuery($recentAssignmentsQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> HR Dashboard</h2>
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
                <p><i class="bi bi-people"></i> Active Employees</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <h3 class="text-warning"><?php echo $exitingEmployees; ?></h3>
                <p><i class="bi bi-exclamation-triangle"></i> Exiting Employees</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <h3 class="text-success"><?php echo $totalAssets; ?></h3>
                <p><i class="bi bi-laptop"></i> Assigned Assets</p>
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

<!-- Exiting Employees Alert -->
<?php if ($exitingEmployees > 0): ?>
<div class="alert alert-warning mb-4">
    <h5><i class="bi bi-exclamation-triangle"></i> Attention Required</h5>
    <p class="mb-0">There are <strong><?php echo $exitingEmployees; ?></strong> employee(s) in exit process. Please coordinate with IT for asset recovery.</p>
</div>
<?php endif; ?>

<!-- Exiting Employees with Pending Assets -->
<?php if (!empty($exitingEmployeesList)): ?>
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Exiting Employees - Asset Recovery Status</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Pending Assets</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exitingEmployeesList as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['emp_code']); ?></td>
                        <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                        <td><?php echo htmlspecialchars($emp['designation']); ?></td>
                        <td>
                            <?php if ($emp['pending_assets'] > 0): ?>
                                <span class="badge bg-danger"><?php echo $emp['pending_assets']; ?> Pending</span>
                            <?php else: ?>
                                <span class="badge bg-success">All Returned</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="employee_assets.php?id=<?php echo $emp['employee_id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View Assets
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Employee Asset Summary -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-people"></i> Employee Asset Summary</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Assets Assigned</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['emp_code']); ?></td>
                        <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                        <td><?php echo htmlspecialchars($emp['designation']); ?></td>
                        <td>
                            <span class="badge bg-primary"><?php echo $emp['asset_count']; ?></span>
                        </td>
                        <td>
                            <a href="employee_assets.php?id=<?php echo $emp['employee_id']; ?>" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-laptop"></i> View Assets
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Asset Assignments -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Asset Assignments</h5>
    </div>
    <div class="card-body">
        <?php if (empty($recentAssignments)): ?>
            <p class="text-muted text-center py-3">No recent assignments.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Asset Type</th>
                            <th>Serial Number</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAssignments as $assignment): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($assignment['full_name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($assignment['emp_code']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($assignment['asset_type']); ?></td>
                            <td><code><?php echo htmlspecialchars($assignment['serial_number']); ?></code></td>
                            <td><?php echo formatDate($assignment['assigned_date']); ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($assignment['assignment_status']); ?>">
                                    <?php echo $assignment['assignment_status']; ?>
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

<!-- Quick Links -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Links</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                <a href="employees.php" class="btn btn-primary w-100">
                    <i class="bi bi-people"></i> View All Employees
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="assets.php" class="btn btn-success w-100">
                    <i class="bi bi-laptop"></i> View All Assets
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="reports.php" class="btn btn-info w-100">
                    <i class="bi bi-file-bar-graph"></i> View Reports
                </a>
            </div>
        </div>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
