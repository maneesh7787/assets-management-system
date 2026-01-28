<?php
/**
 * Admin - Manage Employees
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Manage Employees';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add' || $action == 'edit') {
        $empCode = sanitizeInput($_POST['emp_code']);
        $fullName = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $department = sanitizeInput($_POST['department']);
        $designation = sanitizeInput($_POST['designation']);
        $workLocation = sanitizeInput($_POST['work_location']);
        $dateOfJoining = sanitizeInput($_POST['date_of_joining']);
        $status = sanitizeInput($_POST['status']);
        
        // Validation
        if (empty($empCode) || empty($fullName) || empty($email)) {
            $error = 'Employee Code, Full Name, and Email are required.';
        } elseif (!validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            if ($action == 'add') {
                // Check if employee code or email already exists
                if (recordExists('employees', 'emp_code', $empCode)) {
                    $error = 'Employee Code already exists.';
                } elseif (recordExists('employees', 'email', $email)) {
                    $error = 'Email already exists.';
                } else {
                    // Generate username and password
                    $username = strtolower(str_replace(' ', '.', $fullName));
                    $password = generatePassword();
                    $hashedPassword = hashPassword($password);
                    
                    // Create user account
                    $userQuery = "INSERT INTO users (username, password, role_id, is_active) 
                                  VALUES ('$username', '$hashedPassword', 3, 1)";
                    
                    if (executeQuery($userQuery)) {
                        $userId = lastInsertId();
                        
                        // Create employee record
                        $createdBy = getCurrentUserId();
                        $employeeQuery = "INSERT INTO employees (user_id, emp_code, full_name, email, phone, department, designation, work_location, date_of_joining, status, created_by) 
                                          VALUES ('$userId', '$empCode', '$fullName', '$email', '$phone', '$department', '$designation', '$workLocation', '$dateOfJoining', '$status', '$createdBy')";
                        
                        if (executeQuery($employeeQuery)) {
                            $employeeId = lastInsertId();
                            
                            logAudit('Employee Created', 'employees', $employeeId, null, "$empCode - $fullName");
                            
                            $success = "Employee created successfully. Username: <strong>$username</strong>, Password: <strong>$password</strong>";
                        } else {
                            $error = 'Failed to create employee record.';
                        }
                    } else {
                        $error = 'Failed to create user account.';
                    }
                }
            } else { // Edit
                $employeeId = sanitizeInput($_POST['employee_id']);
                
                // Check if email exists for other employees
                if (recordExists('employees', 'email', $email, $employeeId)) {
                    $error = 'Email already exists for another employee.';
                } else {
                    $updateQuery = "UPDATE employees SET 
                                    emp_code = '$empCode',
                                    full_name = '$fullName',
                                    email = '$email',
                                    phone = '$phone',
                                    department = '$department',
                                    designation = '$designation',
                                    work_location = '$workLocation',
                                    date_of_joining = '$dateOfJoining',
                                    status = '$status'
                                    WHERE employee_id = '$employeeId'";
                    
                    if (executeQuery($updateQuery)) {
                        logAudit('Employee Updated', 'employees', $employeeId, null, "$empCode - $fullName");
                        $success = 'Employee updated successfully.';
                    } else {
                        $error = 'Failed to update employee.';
                    }
                }
            }
        }
    } elseif ($action == 'reset_password') {
        $userId = sanitizeInput($_POST['user_id']);
        $newPassword = generatePassword();
        $hashedPassword = hashPassword($newPassword);
        
        $query = "UPDATE users SET password = '$hashedPassword' WHERE user_id = '$userId'";
        if (executeQuery($query)) {
            logAudit('Password Reset', 'users', $userId, null, 'Password reset by admin');
            $success = "Password reset successfully. New password: <strong>$newPassword</strong>";
        } else {
            $error = 'Failed to reset password.';
        }
    } elseif ($action == 'toggle_status') {
        $userId = sanitizeInput($_POST['user_id']);
        $currentStatus = sanitizeInput($_POST['current_status']);
        $newStatus = $currentStatus == 1 ? 0 : 1;
        
        $query = "UPDATE users SET is_active = '$newStatus' WHERE user_id = '$userId'";
        if (executeQuery($query)) {
            logAudit('User Status Changed', 'users', $userId, $currentStatus, $newStatus);
            $success = 'User status updated successfully.';
        } else {
            $error = 'Failed to update user status.';
        }
    }
}

// Get all employees
$employeesQuery = "SELECT e.*, u.username, u.is_active 
                    FROM employees e 
                    LEFT JOIN users u ON e.user_id = u.user_id 
                    ORDER BY e.created_at DESC";
$employees = fetchAll(executeQuery($employeesQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Manage Employees</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        <i class="bi bi-person-plus"></i> Add Employee
    </button>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Employees Table -->
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
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Status</th>
                        <th>User Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['emp_code']); ?></td>
                        <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($emp['email']); ?></td>
                        <td><?php echo htmlspecialchars($emp['department']); ?></td>
                        <td><?php echo htmlspecialchars($emp['designation']); ?></td>
                        <td>
                            <span class="badge <?php echo getStatusBadgeClass($emp['status']); ?>">
                                <?php echo $emp['status']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $emp['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $emp['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewEmployee(<?php echo $emp['employee_id']; ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="editEmployee(<?php echo $emp['employee_id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="resetPassword(<?php echo $emp['user_id']; ?>)">
                                    <i class="bi bi-key"></i>
                                </button>
                                <button class="btn btn-outline-secondary" onclick="toggleStatus(<?php echo $emp['user_id']; ?>, <?php echo $emp['is_active']; ?>)">
                                    <i class="bi bi-power"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" data-validate="true">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emp_code" class="form-label">Employee Code *</label>
                            <input type="text" class="form-control" name="emp_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" class="form-control" name="department">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" class="form-control" name="designation">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="work_location" class="form-label">Work Location</label>
                            <input type="text" class="form-control" name="work_location">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_joining" class="form-label">Date of Joining</label>
                            <input type="date" class="form-control" name="date_of_joining">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="Active">Active</option>
                                <option value="Exiting">Exiting</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> A username and password will be auto-generated and displayed after creation.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Employee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden forms for actions -->
<form id="resetPasswordForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="reset_password">
    <input type="hidden" name="user_id" id="reset_user_id">
</form>

<form id="toggleStatusForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="toggle_status">
    <input type="hidden" name="user_id" id="toggle_user_id">
    <input type="hidden" name="current_status" id="current_status">
</form>

<script>
function viewEmployee(id) {
    window.location.href = 'employee_details.php?id=' + id;
}

function editEmployee(id) {
    window.location.href = 'edit_employee.php?id=' + id;
}

function resetPassword(userId) {
    if (confirm('Are you sure you want to reset the password for this user?')) {
        document.getElementById('reset_user_id').value = userId;
        document.getElementById('resetPasswordForm').submit();
    }
}

function toggleStatus(userId, currentStatus) {
    var action = currentStatus == 1 ? 'deactivate' : 'activate';
    if (confirm('Are you sure you want to ' + action + ' this user?')) {
        document.getElementById('toggle_user_id').value = userId;
        document.getElementById('current_status').value = currentStatus;
        document.getElementById('toggleStatusForm').submit();
    }
}
</script>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>
