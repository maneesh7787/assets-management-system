<?php
/**
 * Database Connection Handler
 * Asset Management System
 */

require_once 'config.php';

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");

/**
 * Execute a query and return the result
 */
function executeQuery($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("Query Error: " . mysqli_error($conn));
    }
    return $result;
}

/**
 * Sanitize input to prevent SQL injection
 */
function sanitizeInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

/**
 * Get single row from result
 */
function fetchRow($result) {
    return mysqli_fetch_assoc($result);
}

/**
 * Get all rows from result
 */
function fetchAll($result) {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Get number of rows affected
 */
function affectedRows() {
    global $conn;
    return mysqli_affected_rows($conn);
}

/**
 * Get last inserted ID
 */
function lastInsertId() {
    global $conn;
    return mysqli_insert_id($conn);
}

/**
 * Close database connection
 */
function closeConnection() {
    global $conn;
    mysqli_close($conn);
}
?>
