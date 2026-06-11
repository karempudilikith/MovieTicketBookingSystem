<?php
/**
 * Configuration File
 * 
 * This file defines the global constants used across the application
 * and initializes the user session if it hasn't been started already.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration Constants
// Change these settings if you are using a different server setup
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movie_booking_db_new');

// Application Constants
define('APP_NAME', 'CinePass - Movie Ticket Booking Portal');
define('BASE_URL', 'http://localhost:8000/'); // Adjust if placed in a subdirectory
?>
