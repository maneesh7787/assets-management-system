<?php
/**
 * Admin - Reports
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Reports';

// Get various statistics
$totalEmployees = countRecords('employees');
$activeEmployees = countRecords('employees', "status = 'Active'");
$exitingEmployees = countRecords('employees', "status = 'Exiting'");
$inactiveEmployees = countRecords('employees', "status = 'Inactive'");

$totalAssets = countRecords('assets');
$assignedAssets = countRecords('assets', "current_status = 'Assigned'");
$availableAssets = countRecords('assets', "current_status = 'Available'");
$returnedAssets = countRecords('assets', "current_status = 'Returned'");

$pendingApprovals = countRecords('asset_assignments', "assignment_status = 'Pending'");
$approvedAssignments = countRecords('asset_assignments', "assignment_status = 'Approved'");
$rejectedAssignments = countRecords('asset_assignments', "assignment_status = 'Rejected'");

// Asset distribution by type
$assetTypeQuery = "SELECT asset_type, COUNT(*) as count FROM assets GROUP BY asset_type ORDER BY count DESC";
$assetTypes = fetchAll(executeQuery($assetTypeQuery));

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

// Top employees by asset count
$topEmployeesQuery = "SELECT e.emp_code, e.full_name, e.department, COUNT(aa.asset_id) as asset_count
                       FROM employees e
                       INNER JOIN asset_assignments aa ON e.employee_id = aa.employee_id
                       WHERE aa.assignment_status = 'Approved' AND e.status = 'Active'
                       GROUP BY e.employee_id
                       ORDER BY asset_count DESC
                       LIMIT 10";
$topEmployees = fetchAll(executeQuery($topEmployeesQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-bar-graph"></i> Reports & Analytics</h2>
    <button onclick="window.print()" class="btn btn-secondary no-print">
        <i class="bi bi-printer"></i> Print Report
    </button>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <h4>Employee Statistics</h4>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <h3 class="text-primary"><?php echo $totalEmployees; ?></h3>
                <p>Total Employees</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <h3 class="text-success"><?php echo $activeEmployees; ?></h3>
                <p>Active</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <h3 class="text-warning"><?php echo $exitingEmployees; ?></h3>
                <p>Exiting</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-secondary">
            <div class="card-body">
                <h3 class="text-secondary"><?php echo $inactiveEmployees; ?></h3>
                <p>Inactive</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h4>Asset Statistics</h4>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <h3 class="text-info"><?php echo $totalAssets; ?></h3>
                <p>Total Assets</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <h3 class="text-primary"><?php echo $assignedAssets; ?></h3>
                <p>Assigned</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <h3 class="text-success"><?php echo $availableAssets; ?></h3>
                <p>Available</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <h3 class="text-info"><?php echo $returnedAssets; ?></h3>
                <p>Returned</p>
            </div>
        </div>
    </div>
</div>

<!-- Asset Distribution -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Asset Distribution by Type</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Asset Type</th>
                            <th class="text-end">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assetTypes as $type): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($type['asset_type']); ?></td>
                            <td class="text-end"><strong><?php echo $type['count']; ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Assignment Status</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-end">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pending Approval</td>
                            <td class="text-end"><strong><?php echo $pendingApprovals; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Approved</td>
                            <td class="text-end"><strong><?php echo $approvedAssignments; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Rejected</td>
                            <td class="text-end"><strong><?php echo $rejectedAssignments; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Department-wise Analysis -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Department-wise Employee and Asset Count</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th class="text-end">Employees</th>
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

<!-- Top Employees by Asset Count -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Top 10 Employees by Asset Count</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th class="text-end">Total Assets</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topEmployees as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['emp_code']); ?></td>
                        <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                        <td class="text-end"><strong><?php echo $emp['asset_count']; ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
