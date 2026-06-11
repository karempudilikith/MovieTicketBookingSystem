<?php
/**
 * Admin Edit Movie Page
 * 
 * Fetches the selected movie record by its ID, pre-fills the editing form,
 * and updates database fields upon validation.
 */

// Title for header
$page_title = "Edit Movie";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Restrict access to admin only
checkAdmin();

// Validate and retrieve movie ID from GET request
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($movie_id <= 0) {
    $_SESSION['error_message'] = "Invalid movie edit request.";
    header("Location: movies.php");
    exit();
}

// Fetch current movie details to populate the form fields
$fetch_sql = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($fetch_sql);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Movie record not found.";
    header("Location: movies.php");
    exit();
}

$movie = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $genre = sanitize($_POST['genre']);
    $language = sanitize($_POST['language']);
    $duration = intval($_POST['duration']);
    $release_date = sanitize($_POST['release_date']);
    $description = sanitize($_POST['description']);
    $ticket_price = doubleval($_POST['ticket_price']);
    
    // Manage Poster logic (retain old poster if no new poster uploaded or typed)
    $poster = $movie['poster'];
    
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        // Upload new file
        $file_name = basename($_FILES['poster_file']['name']);
        $target_dir = __DIR__ . "/../images/";
        
        // Automatically create the images folder if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES['poster_file']['tmp_name'], $target_file)) {
            $poster = $file_name;
        }
    } elseif (!empty($_POST['poster_text'])) {
        // Manual override text
        $poster = sanitize($_POST['poster_text']);
    }

    $errors = [];
    
    if (empty($title)) $errors[] = "Movie Title is required.";
    if (empty($genre)) $errors[] = "Genre is required.";
    if (empty($language)) $errors[] = "Language is required.";
    if ($duration <= 0) $errors[] = "Duration must be greater than 0.";
    if (empty($release_date)) $errors[] = "Release Date is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if ($ticket_price <= 0) $errors[] = "Ticket Price must be greater than 0.";
    
    if (empty($errors)) {
        // Update query using prepared statement
        $update_sql = "UPDATE movies SET title = ?, genre = ?, language = ?, duration = ?, release_date = ?, description = ?, poster = ?, ticket_price = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssisssdi", $title, $genre, $language, $duration, $release_date, $description, $poster, $ticket_price, $movie_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Movie '{$title}' updated successfully!";
            header("Location: movies.php");
            exit();
        } else {
            $errors[] = "Failed to update record in database: " . $conn->error;
        }
        $update_stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header("Location: edit_movie.php?id=" . $movie_id);
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
                <i class="bi bi-pencil-square"></i> Edit Movie Details
            </div>
            <div class="card-body p-4">
                <form action="edit_movie.php?id=<?php echo $movie_id; ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="row">
                        <!-- Movie Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Movie Title</label>
                            <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($movie['title']); ?>">
                        </div>
                        
                        <!-- Genre -->
                        <div class="col-md-6 mb-3">
                            <label for="genre" class="form-label">Genre (Comma-separated)</label>
                            <input type="text" class="form-control" id="genre" name="genre" required value="<?php echo htmlspecialchars($movie['genre']); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Language -->
                        <div class="col-md-4 mb-3">
                            <label for="language" class="form-label">Language</label>
                            <input type="text" class="form-control" id="language" name="language" required value="<?php echo htmlspecialchars($movie['language']); ?>">
                        </div>
                        
                        <!-- Duration -->
                        <div class="col-md-4 mb-3">
                            <label for="duration" class="form-label">Duration (Minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" required min="1" value="<?php echo htmlspecialchars($movie['duration']); ?>">
                        </div>
                        
                        <!-- Ticket Price -->
                        <div class="col-md-4 mb-3">
                            <label for="ticket_price" class="form-label">Ticket Price (Rs.)</label>
                            <input type="number" class="form-control" id="ticket_price" name="ticket_price" required min="1" step="0.01" value="<?php echo htmlspecialchars($movie['ticket_price']); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Release Date -->
                        <div class="col-md-6 mb-3">
                            <label for="release_date" class="form-label">Release Date</label>
                            <input type="date" class="form-control" id="release_date" name="release_date" required value="<?php echo $movie['release_date']; ?>">
                        </div>
                        
                        <!-- Poster Image Option -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Movie Poster Image</label>
                            <input type="file" class="form-control mb-1" id="poster_file" name="poster_file" accept="image/*">
                            <div class="text-center my-1 text-muted small">— OR —</div>
                            <input type="text" class="form-control" id="poster_text" name="poster_text" placeholder="Type filename manually" value="<?php echo htmlspecialchars($movie['poster']); ?>">
                            <small class="text-muted text-white-50">Current poster: <strong><?php echo htmlspecialchars($movie['poster']); ?></strong></small>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Synopsis Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($movie['description']); ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-navy py-2"><i class="bi bi-save"></i> Update Movie Record</button>
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
