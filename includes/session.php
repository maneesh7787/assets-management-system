<?php
/**
 * Session Management
 * Asset Management System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
}

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $elapsed = time() - $_SESSION['last_activity'];
        if ($elapsed > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            return false;
        }
    }
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Require login - redirect to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn() || !checkSessionTimeout()) {
        header('Location: ' . BASE_URL . '/index.php?error=session_expired');
        exit();
    }
}

/**
 * Check if user has required role
 */
function hasRole($requiredRoles) {
    if (!is_array($requiredRoles)) {
        $requiredRoles = [$requiredRoles];
    }
    return in_array($_SESSION['role_id'], $requiredRoles);
}

/**
 * Require specific role - redirect if user doesn't have permission
 */
function requireRole($requiredRoles) {
    requireLogin();
    if (!hasRole($requiredRoles)) {
        header('Location: ' . BASE_URL . '/unauthorized.php');
        exit();
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    return $_SESSION['role_id'] ?? null;
}

/**
 * Get current username
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Get current employee ID (if user is an employee)
 */
function getCurrentEmployeeId() {
    return $_SESSION['employee_id'] ?? null;
}

/**
 * Set session data after login
 */
function setSessionData($userData) {
    $_SESSION['user_id'] = $userData['user_id'];
    $_SESSION['username'] = $userData['username'];
    $_SESSION['role_id'] = $userData['role_id'];
    $_SESSION['role_name'] = $userData['role_name'];
    $_SESSION['employee_id'] = $userData['employee_id'] ?? null;
    $_SESSION['employee_name'] = $userData['employee_name'] ?? null;
    $_SESSION['last_activity'] = time();
}

/**
 * Clear session and logout
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php?logout=success');
    exit();
}

/**
 * Log audit trail
 */
function logAudit($action, $tableName = null, $recordId = null, $oldValue = null, $newValue = null) {
    global $conn;
    
    $userId = getCurrentUserId();
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $userId = sanitizeInput($userId);
    $action = sanitizeInput($action);
    $tableName = $tableName ? sanitizeInput($tableName) : 'NULL';
    $recordId = $recordId ? sanitizeInput($recordId) : 'NULL';
    $oldValue = $oldValue ? "'" . sanitizeInput($oldValue) . "'" : 'NULL';
    $newValue = $newValue ? "'" . sanitizeInput($newValue) . "'" : 'NULL';
    $ipAddress = sanitizeInput($ipAddress);
    $userAgent = sanitizeInput($userAgent);
    
    $query = "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent) 
              VALUES ('$userId', '$action', " . ($tableName !== 'NULL' ? "'$tableName'" : 'NULL') . ", " . ($recordId !== 'NULL' ? "'$recordId'" : 'NULL') . ", 
              $oldValue, $newValue, '$ipAddress', '$userAgent')";
    
    executeQuery($query);
}
?>
