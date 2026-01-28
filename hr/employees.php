<?php
/**
 * HR - View Employees (Read-only)
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require HR role
requireRole(2);

$pageTitle = 'View Employees';

// Get all employees
$employeesQuery = "SELECT e.*, u.username, u.is_active,
                   (SELECT COUNT(*) FROM asset_assignments aa 
                    WHERE aa.employee_id = e.employee_id AND aa.assignment_status = 'Approved') as asset_count
                   FROM employees e 
                   LEFT JOIN users u ON e.user_id = u.user_id 
                   ORDER BY e.created_at DESC";
$employees = fetchAll(executeQuery($employeesQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> View Employees</h2>
    <span class="text-muted">(Read-Only Access)</span>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Employees</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Emp Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Status</th>
                        <th>Assets</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['emp_code']); ?></td>
                        <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($emp['email']); ?></td>
                        <td><?php echo htmlspecialchars($emp['phone']); ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                        <td><?php echo htmlspecialchars($emp['designation']); ?></td>
                        <td>
                            <span class="badge <?php echo getStatusBadgeClass($emp['status']); ?>">
                                <?php echo $emp['status']; ?>
                            </span>
                        </td>
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

<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> As an HR user, you have read-only access. You cannot create, edit, or delete employee records. Please contact IT/Admin for any changes.
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
