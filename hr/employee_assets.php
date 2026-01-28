<?php
/**
 * HR - Employee Assets View
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require HR role
requireRole(2);

$pageTitle = 'Employee Assets';

$employeeId = isset($_GET['id']) ? sanitizeInput($_GET['id']) : 0;

// Get employee details
$employee = getEmployeeById($employeeId);

if (!$employee) {
    header('Location: employees.php');
    exit();
}

// Get employee's assets
$assetsQuery = "SELECT aa.*, a.asset_type, a.brand, a.serial_number, a.condition_at_issue, a.current_status
                FROM asset_assignments aa
                INNER JOIN assets a ON aa.asset_id = a.asset_id
                WHERE aa.employee_id = '$employeeId' AND aa.assignment_status = 'Approved'
                ORDER BY aa.approval_date DESC";
$assets = fetchAll(executeQuery($assetsQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-laptop"></i> Employee Assets</h2>
    <a href="employees.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Employees
    </a>
</div>

<!-- Employee Information -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-badge"></i> Employee Information</h5>
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
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge <?php echo getStatusBadgeClass($employee['status']); ?>">
                                <?php echo $employee['status']; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Assets -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Assigned Assets</h5>
    </div>
    <div class="card-body">
        <?php if (empty($assets)): ?>
            <p class="text-muted text-center py-3">No assets assigned to this employee.</p>
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
                            <th>Current Status</th>
                            <th>Return Status</th>
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
                                <span class="badge <?php echo getStatusBadgeClass($asset['current_status']); ?>">
                                    <?php echo $asset['current_status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($asset['returned_date']): ?>
                                    <span class="badge bg-success">Returned on <?php echo formatDate($asset['returned_date']); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Not Returned</span>
                                <?php endif; ?>
                            </td>
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
