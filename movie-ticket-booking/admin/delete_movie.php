<?php
/**
 * Admin Delete Movie Script
 * 
 * Takes the movie ID from the URL parameters, validates admin access,
 * and deletes the movie. Foreign key cascades automatically clean up
 * associated shows and bookings.
 */

// Include config and functions
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Restrict access to admin only
checkAdmin();

// Validate and retrieve movie ID from GET request
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($movie_id > 0) {
    // We use a prepared statement to safely run the query
    $sql = "DELETE FROM movies WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $movie_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Movie and all its scheduled shows/bookings have been deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error: Failed to delete movie. " . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid movie deletion request.";
}

// Redirect back to movies list
header("Location: movies.php");
exit();
?>
