<?php
/**
 * Login Page
 * Asset Management System
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(getDashboardUrl(getCurrentUserRole()));
}

$error = '';
$success = '';

// Handle logout message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success = 'You have been logged out successfully.';
}

// Handle session expired message
if (isset($_GET['error']) && $_GET['error'] == 'session_expired') {
    $error = 'Your session has expired. Please login again.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Fetch user from database
        $query = "SELECT u.*, r.role_name, e.employee_id, e.full_name as employee_name 
                  FROM users u 
                  INNER JOIN roles r ON u.role_id = r.role_id 
                  LEFT JOIN employees e ON u.user_id = e.user_id 
                  WHERE u.username = '$username' AND u.is_active = 1";
        
        $result = executeQuery($query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = fetchRow($result);
            
            // Verify password
            if (verifyPassword($password, $user['password'])) {
                // Set session data
                setSessionData($user);
                
                // Update last login
                $userId = $user['user_id'];
                $updateQuery = "UPDATE users SET last_login = NOW() WHERE user_id = '$userId'";
                executeQuery($updateQuery);
                
                // Log successful login
                logAudit('User Login', 'users', $userId, null, 'Login successful');
                
                // Redirect to dashboard
                redirect(getDashboardUrl($user['role_id']));
            } else {
                $error = 'Invalid username or password.';
                logAudit('Failed Login Attempt', 'users', null, null, "Username: $username");
            }
        } else {
            $error = 'Invalid username or password.';
            logAudit('Failed Login Attempt', 'users', null, null, "Username: $username");
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-building" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo APP_NAME; ?></h3>
                <p class="mb-0">Please login to continue</p>
            </div>
            <div class="login-body bg-white">
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
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center text-muted">
                    <small>
                        <i class="bi bi-info-circle"></i> Default credentials:<br>
                        Admin: <strong>admin</strong> / <strong>Admin@123</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
