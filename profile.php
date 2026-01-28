<?php
/**
 * User Profile
 * Asset Management System
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'My Profile';
$error = '';
$success = '';

$userId = getCurrentUserId();

// Get user details
$userQuery = "SELECT u.*, r.role_name, e.employee_id, e.full_name, e.email, e.phone, e.emp_code, e.department, e.designation, e.work_location, e.date_of_joining
              FROM users u
              INNER JOIN roles r ON u.role_id = r.role_id
              LEFT JOIN employees e ON u.user_id = e.user_id
              WHERE u.user_id = '$userId'";
$user = fetchRow(executeQuery($userQuery));

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All password fields are required.';
    } elseif (!verifyPassword($currentPassword, $user['password'])) {
        $error = 'Current password is incorrect.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match.';
    } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.';
    } else {
        $hashedPassword = hashPassword($newPassword);
        $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE user_id = '$userId'";
        
        if (executeQuery($updateQuery)) {
            logAudit('Password Changed', 'users', $userId, null, 'Password changed by user');
            $success = 'Password changed successfully.';
        } else {
            $error = 'Failed to change password.';
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-circle"></i> My Profile</h2>
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

<!-- User Information -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Account Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Username:</strong></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($user['role_name']); ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Account Status:</strong></td>
                        <td>
                            <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Last Login:</strong></td>
                        <td><?php echo formatDateTime($user['last_login']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <?php if ($user['employee_id']): ?>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Employee Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Employee Code:</strong></td>
                        <td><?php echo htmlspecialchars($user['emp_code']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Full Name:</strong></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Department:</strong></td>
                        <td><?php echo htmlspecialchars($user['department']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Designation:</strong></td>
                        <td><?php echo htmlspecialchars($user['designation']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Change Password -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-key"></i> Change Password</h5>
    </div>
    <div class="card-body">
        <form method="POST" data-validate="true">
            <input type="hidden" name="change_password" value="1">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="current_password" class="form-label">Current Password *</label>
                    <input type="password" class="form-control" name="current_password" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="new_password" class="form-label">New Password *</label>
                    <input type="password" class="form-control" name="new_password" required>
                    <small class="text-muted">Minimum <?php echo PASSWORD_MIN_LENGTH; ?> characters</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password *</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-key"></i> Change Password
            </button>
        </form>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include 'includes/footer.php'; ?>
