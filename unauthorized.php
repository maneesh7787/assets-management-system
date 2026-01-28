<?php
/**
 * Unauthorized Access Page
 * Asset Management System
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$pageTitle = 'Unauthorized Access';
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
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-6 text-center">
                <div class="card">
                    <div class="card-body p-5">
                        <i class="bi bi-shield-exclamation text-danger" style="font-size: 5rem;"></i>
                        <h1 class="mt-3">Access Denied</h1>
                        <p class="lead">You don't have permission to access this page.</p>
                        <p class="text-muted">Please contact your administrator if you believe this is an error.</p>
                        <hr>
                        <a href="<?php echo isLoggedIn() ? getDashboardUrl(getCurrentUserRole()) : BASE_URL . '/index.php'; ?>" class="btn btn-primary">
                            <i class="bi bi-house"></i> Go to <?php echo isLoggedIn() ? 'Dashboard' : 'Login'; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
