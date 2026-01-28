<?php
/**
 * Database Configuration
 * Asset Management System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'asset_management_system');

// Application settings
define('APP_NAME', 'Asset Management System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/assets-management-system');

// Session settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Password settings
define('PASSWORD_MIN_LENGTH', 8);

// File upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
