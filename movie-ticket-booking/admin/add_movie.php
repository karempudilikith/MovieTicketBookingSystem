<?php
/**
 * Admin Add Movie Page
 * 
 * Renders a Form allowing administrators to insert a new movie record into the database.
 * The system stores the poster filename (e.g. 'interstellar.jpg') in the database.
 * Note: To keep things beginner-friendly and avoid file permission/upload errors,
 * we allow administrators to enter the poster filename, and explain they can place 
 * the file manually in the 'images/' folder, or upload it using a simple form file upload
 * which automatically saves it to 'images/' folder.
 */

// Title for header
$page_title = "Add Movie";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Restrict access to admin only
checkAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $genre = sanitize($_POST['genre']);
    $language = sanitize($_POST['language']);
    $duration = intval($_POST['duration']);
    $release_date = sanitize($_POST['release_date']);
    $description = sanitize($_POST['description']);
    $ticket_price = doubleval($_POST['ticket_price']);
    
    // Handle Poster Image Upload / Text Filename
    $poster = "placeholder.jpg"; // Default placeholder if nothing is provided
    
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        // Simple file upload handler
        $file_name = basename($_FILES['poster_file']['name']);
        $target_dir = __DIR__ . "/../images/";
        
        // Automatically create the images folder if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . $file_name;
        
        // Try uploading the file
        if (move_uploaded_file($_FILES['poster_file']['tmp_name'], $target_file)) {
            $poster = $file_name;
        }
    } elseif (!empty($_POST['poster_text'])) {
        // Fallback: If user typed in the file name manually
        $poster = sanitize($_POST['poster_text']);
    }

    $errors = [];
    
    // Server-side validation
    if (empty($title)) $errors[] = "Movie Title is required.";
    if (empty($genre)) $errors[] = "Genre is required.";
    if (empty($language)) $errors[] = "Language is required.";
    if ($duration <= 0) $errors[] = "Duration must be greater than 0.";
    if (empty($release_date)) $errors[] = "Release Date is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if ($ticket_price <= 0) $errors[] = "Ticket Price must be greater than 0.";
    
    if (empty($errors)) {
        // Insert query using prepared statement
        $sql = "INSERT INTO movies (title, genre, language, duration, release_date, description, poster, ticket_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssd", $title, $genre, $language, $duration, $release_date, $description, $poster, $ticket_price);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Movie '{$title}' added successfully!";
            header("Location: movies.php");
            exit();
        } else {
            $errors[] = "Database insertion failed: " . $conn->error;
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header("Location: add_movie.php");
        exit();
    }
}
?>

<div class="mb-4">
    <a href="movies.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Movie List</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-portal form-portal shadow-sm">
            <div class="card-header-portal">
                <i class="bi bi-plus-circle-fill"></i> Add New Movie Register
            </div>
            <div class="card-body p-4">
                <!-- Added enctype="multipart/form-data" to support file uploads -->
                <form action="add_movie.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="row">
                        <!-- Movie Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Movie Title</label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="e.g. Inception">
                        </div>
                        
                        <!-- Genre -->
                        <div class="col-md-6 mb-3">
                            <label for="genre" class="form-label">Genre (Comma-separated)</label>
                            <input type="text" class="form-control" id="genre" name="genre" required placeholder="e.g. Action, Sci-Fi">
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Language -->
                        <div class="col-md-4 mb-3">
                            <label for="language" class="form-label">Language</label>
                            <input type="text" class="form-control" id="language" name="language" required placeholder="e.g. English">
                        </div>
                        
                        <!-- Duration -->
                        <div class="col-md-4 mb-3">
                            <label for="duration" class="form-label">Duration (Minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" required min="1" placeholder="e.g. 148">
                        </div>
                        
                        <!-- Ticket Price -->
                        <div class="col-md-4 mb-3">
                            <label for="ticket_price" class="form-label">Ticket Price (Rs.)</label>
                            <input type="number" class="form-control" id="ticket_price" name="ticket_price" required min="1" step="0.01" placeholder="e.g. 150">
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Release Date -->
                        <div class="col-md-6 mb-3">
                            <label for="release_date" class="form-label">Release Date</label>
                            <input type="date" class="form-control" id="release_date" name="release_date" required>
                        </div>
                        
                        <!-- Poster Image Option -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Movie Poster Image</label>
                            <!-- File upload option -->
                            <input type="file" class="form-control mb-1" id="poster_file" name="poster_file" accept="image/*">
                            <div class="text-center my-1 text-muted small">— OR —</div>
                            <!-- Text entry fallback option -->
                            <input type="text" class="form-control" id="poster_text" name="poster_text" placeholder="Type filename manually (e.g., interstellar.jpg)">
                            <small class="text-muted text-white-50">Upload a poster or type its exact filename (must reside in the images/ folder).</small>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Synopsis Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Write a summary description of the movie plots..."></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-navy py-2"><i class="bi bi-check-circle"></i> Save Movie Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
