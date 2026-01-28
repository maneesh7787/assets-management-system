<?php
/**
 * Helper Functions
 * Asset Management System
 */

/**
 * Redirect to a URL
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Display formatted date
 */
function formatDate($date, $format = 'd-m-Y') {
    if (!$date) return 'N/A';
    return date($format, strtotime($date));
}

/**
 * Display formatted datetime
 */
function formatDateTime($datetime, $format = 'd-m-Y H:i:s') {
    if (!$datetime) return 'N/A';
    return date($format, strtotime($datetime));
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        'Pending' => 'bg-warning',
        'Approved' => 'bg-success',
        'Rejected' => 'bg-danger',
        'Returned' => 'bg-info',
        'Active' => 'bg-success',
        'Inactive' => 'bg-secondary',
        'Exiting' => 'bg-warning',
        'Available' => 'bg-info',
        'Assigned' => 'bg-primary',
        'Replaced' => 'bg-secondary',
        'Damaged' => 'bg-danger'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

/**
 * Get role name by ID
 */
function getRoleName($roleId) {
    $roles = [
        1 => 'Admin',
        2 => 'HR',
        3 => 'Employee'
    ];
    return $roles[$roleId] ?? 'Unknown';
}

/**
 * Generate random password
 */
function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    $charLength = strlen($chars);
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, $charLength - 1)];
    }
    return $password;
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Get dashboard URL by role
 */
function getDashboardUrl($roleId) {
    $urls = [
        1 => BASE_URL . '/admin/dashboard.php',
        2 => BASE_URL . '/hr/dashboard.php',
        3 => BASE_URL . '/employee/dashboard.php'
    ];
    return $urls[$roleId] ?? BASE_URL . '/index.php';
}

/**
 * Check if value exists in database
 */
function recordExists($table, $column, $value, $excludeId = null) {
    global $conn;
    
    $table = sanitizeInput($table);
    $column = sanitizeInput($column);
    $value = sanitizeInput($value);
    
    $query = "SELECT COUNT(*) as count FROM $table WHERE $column = '$value'";
    
    if ($excludeId) {
        $idColumn = $table === 'users' ? 'user_id' : 
                   ($table === 'employees' ? 'employee_id' : 
                   ($table === 'assets' ? 'asset_id' : 'id'));
        $excludeId = sanitizeInput($excludeId);
        $query .= " AND $idColumn != '$excludeId'";
    }
    
    $result = executeQuery($query);
    $row = fetchRow($result);
    return $row['count'] > 0;
}

/**
 * Get employee details by ID
 */
function getEmployeeById($employeeId) {
    global $conn;
    $employeeId = sanitizeInput($employeeId);
    
    $query = "SELECT e.*, u.username 
              FROM employees e 
              LEFT JOIN users u ON e.user_id = u.user_id 
              WHERE e.employee_id = '$employeeId'";
    
    $result = executeQuery($query);
    return fetchRow($result);
}

/**
 * Get asset details by ID
 */
function getAssetById($assetId) {
    global $conn;
    $assetId = sanitizeInput($assetId);
    
    $query = "SELECT * FROM assets WHERE asset_id = '$assetId'";
    $result = executeQuery($query);
    return fetchRow($result);
}

/**
 * Count records in table with condition
 */
function countRecords($table, $condition = '') {
    global $conn;
    $table = sanitizeInput($table);
    
    $query = "SELECT COUNT(*) as count FROM $table";
    if ($condition) {
        $query .= " WHERE $condition";
    }
    
    $result = executeQuery($query);
    $row = fetchRow($result);
    return $row['count'] ?? 0;
}
?>
