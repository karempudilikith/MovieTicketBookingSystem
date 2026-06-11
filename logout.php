<?php
/**
 * User Logout Page
 * 
 * Clears the session arrays and destroys the session.
 * Displays a nice success message and redirects to the index/home page.
 */

// Include config to access session_status
require_once 'config.php';

// Unset all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This is optional but good practice to clear browser cookies.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start a temporary session just to show a logout confirmation message
session_start();
$_SESSION['success_message'] = "You have been logged out successfully.";

// Redirect to home/index page
header("Location: index.php");
exit();
?>
