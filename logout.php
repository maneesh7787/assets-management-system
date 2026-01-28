<?php
/**
 * Logout Handler
 * Asset Management System
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Log logout action
if (isLoggedIn()) {
    logAudit('User Logout', 'users', getCurrentUserId(), null, 'Logout successful');
}

// Logout user
logout();
?>
