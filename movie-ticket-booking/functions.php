<?php
/**
 * Global Helper Functions File
 * 
 * This file contains reusable functions for input sanitization, 
 * authentication checking, session management, and output formatting.
 */

// Include database connection to have access to $conn if needed, 
// though we usually include it in individual pages
require_once 'db_connect.php';

/**
 * Sanitize User Form Input
 * Helps prevent Cross-Site Scripting (XSS) attacks by converting special characters.
 * 
 * @param string $data Raw input data
 * @return string Cleaned data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Escape string for direct SQL queries (when prepared statements aren't used)
 * 
 * @param mysqli $conn Active database connection
 * @param string $data Raw input
 * @return string Safe SQL-escaped string
 */
function db_escape($conn, $data) {
    return mysqli_real_escape_string($conn, sanitize($data));
}

/**
 * Check if a user is currently logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if the logged-in user is an administrator
 * 
 * @return bool True if logged in as admin, false otherwise
 */
function isAdmin() {
    return (isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

/**
 * Restrict page access to logged-in users only
 * Redirects to login page if unauthorized.
 */
function checkUser() {
    if (!isLoggedIn()) {
        $_SESSION['error_message'] = "Please log in to continue.";
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

/**
 * Restrict page access to administrators only
 * Redirects to admin login if unauthorized.
 */
function checkAdmin() {
    if (!isAdmin()) {
        $_SESSION['error_message'] = "Access denied! You must be an administrator.";
        header("Location: " . BASE_URL . "admin/admin_login.php");
        exit();
    }
}

/**
 * Format date to a readable form (e.g. "01 Jun 2026")
 * 
 * @param string $date SQL Date (YYYY-MM-DD)
 * @return string Formatted date
 */
function formatDate($date) {
    return date("d M Y", strtotime($date));
}

/**
 * Format time to 12-hour AM/PM format (e.g. "02:30 PM")
 * 
 * @param string $time SQL Time (HH:MM:SS)
 * @return string Formatted time
 */
function formatTime($time) {
    return date("h:i A", strtotime($time));
}

/**
 * Format currency with a prefix (e.g. "Rs. 150.00")
 * 
 * @param double $price Numeric price value
 * @return string Formatted price string
 */
function formatPrice($price) {
    return "Rs. " . number_format($price, 2);
}
?>
