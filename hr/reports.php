<?php
/**
 * HR - Reports (Read-only)
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require HR role
requireRole(2);

$pageTitle = 'Reports';

// Get various statistics
$activeEmployees = countRecords('employees', "status = 'Active'");
$exitingEmployees = countRecords('employees', "status = 'Exiting'");
$assignedAssets = countRecords('assets', "current_status = 'Assigned'");

// Department-wise employee and asset count
$deptQuery = "SELECT e.department, 
               COUNT(DISTINCT e.employee_id) as emp_count,
               COUNT(DISTINCT aa.asset_id) as asset_count
               FROM employees e
               LEFT JOIN asset_assignments aa ON e.employee_id = aa.employee_id AND aa.assignment_status = 'Approved'
               WHERE e.status = 'Active'
               GROUP BY e.department
               ORDER BY emp_count DESC";
$departments = fetchAll(executeQuery($deptQuery));

// Employees with pending asset returns (exiting employees)
$exitingAssetsQuery = "SELECT e.emp_code, e.full_name, e.department, 
                        COUNT(aa.asset_id) as pending_assets
                        FROM employees e
                        INNER JOIN asset_assignments aa ON e.employee_id = aa.employee_id
                        WHERE e.status = 'Exiting' AND aa.assignment_status = 'Approved' AND aa.returned_date IS NULL
                        GROUP BY e.employee_id
                        ORDER BY pending_assets DESC";
$exitingAssets = fetchAll(executeQuery($exitingAssetsQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-bar-graph"></i> HR Reports</h2>
    <button onclick="window.print()" class="btn btn-secondary no-print">
        <i class="bi bi-printer"></i> Print Report
    </button>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <h3 class="text-success"><?php echo $activeEmployees; ?></h3>
                <p>Active Employees</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <h3 class="text-warning"><?php echo $exitingEmployees; ?></h3>
                <p>Exiting Employees</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <h3 class="text-primary"><?php echo $assignedAssets; ?></h3>
                <p>Total Assigned Assets</p>
            </div>
        </div>
    </div>
</div>

<!-- Department-wise Analysis -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Department-wise Employee and Asset Summary</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th class="text-end">Active Employees</th>
                        <th class="text-end">Assets Allocated</th>
                        <th class="text-end">Avg Assets per Employee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $dept): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dept['department']); ?></td>
                        <td class="text-end"><?php echo $dept['emp_count']; ?></td>
                        <td class="text-end"><?php echo $dept['asset_count']; ?></td>
                        <td class="text-end">
                            <?php echo $dept['emp_count'] > 0 ? number_format($dept['asset_count'] / $dept['emp_count'], 2) : '0'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Exiting Employees - Asset Recovery Status -->
<?php if (!empty($exitingAssets)): ?>
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">Exiting Employees - Asset Recovery Pending</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th class="text-end">Pending Assets</th>
                        <th>Action Required</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exitingAssets as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['emp_code']); ?></td>
                        <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                        <td class="text-end">
                            <span class="badge bg-danger"><?php echo $emp['pending_assets']; ?></span>
                        </td>
                        <td>
                            <small class="text-danger">
                                <i class="bi bi-exclamation-triangle"></i> Coordinate with IT for asset collection
                            </small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-info-circle"></i> Please coordinate with IT/Admin team to ensure all assets are collected before employee exit.
        </div>
    </div>
</div>
<?php endif; ?>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> As an HR user, you have read-only access to reports. For detailed analytics and administrative reports, please contact IT/Admin.
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
