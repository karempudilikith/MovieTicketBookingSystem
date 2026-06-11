<?php
/**
 * Database Connection File
 * 
 * This file establishes a connection to the MySQL database using the 'mysqli' extension.
 * It includes 'config.php' to access the database credentials.
 */

// Include configuration file to get DB constants
require_once 'config.php';

// Create connection to the database
// We use the object-oriented approach of mysqli which is simple and clean
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if the connection has failed
if ($conn->connect_error) {
    // If the database connection fails, stop execution and show the error
    die("Database Connection Failed: " . $conn->connect_error . " <br><br>
         Please check if your XAMPP Apache and MySQL servers are running, 
         and that you have imported the SQL file in phpMyAdmin.");
}

// Set character set to UTF-8 to handle any special characters in movie titles
$conn->set_charset("utf8mb4");
?>
