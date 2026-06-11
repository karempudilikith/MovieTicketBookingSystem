<?php
/**
 * Admin Password Reset Helper Script
 * 
 * Open this file in your browser to automatically update the default 
 * administrator account password to 'admin123' using your local server's 
 * password hashing algorithm.
 * 
 * URL: http://localhost/movie-ticket-booking/reset_admin.php
 */

require_once 'db_connect.php';
require_once 'functions.php';

echo "<h2>CinePass Admin Password Reset Tool</h2>";

// Define default credentials
$admin_email = 'admin@gmail.com';
$admin_password = 'admin123';
$admin_name = 'System Administrator';

// Generate correct hash using local PHP version
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Check if the administrator already exists
$check_sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Admin exists, update the password and role
    $stmt->close();
    $update_sql = "UPDATE users SET password = ?, role = 'admin', name = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sss", $hashed_password, $admin_name, $admin_email);
    
    if ($update_stmt->execute()) {
        echo "<p style='color: green;'><strong>Success:</strong> Admin password successfully updated to <strong>admin123</strong>!</p>";
        echo "<p>Your local password hash is: <code>" . $hashed_password . "</code></p>";
    } else {
        echo "<p style='color: red;'><strong>Error:</strong> Failed to update admin password: " . $conn->error . "</p>";
    }
    $update_stmt->close();
} else {
    // Admin doesn't exist, insert new record
    $stmt->close();
    $insert_sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sss", $admin_name, $admin_email, $hashed_password);
    
    if ($insert_stmt->execute()) {
        echo "<p style='color: green;'><strong>Success:</strong> Default admin account successfully created!</p>";
        echo "<p><strong>Email:</strong> admin@gmail.com</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p>Your local password hash is: <code>" . $hashed_password . "</code></p>";
    } else {
        echo "<p style='color: red;'><strong>Error:</strong> Failed to insert admin: " . $conn->error . "</p>";
    }
    $insert_stmt->close();
}

echo "<hr>";
echo "<p style='color: orange;'><strong>Important:</strong> Delete the <code>reset_admin.php</code> file from your server after running it for security reasons.</p>";
echo "<p><a href='admin/admin_login.php'>Go to Admin Login Page</a></p>";
?>
