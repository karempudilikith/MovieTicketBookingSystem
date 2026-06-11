<?php
/**
 * Admin Shows Management Page
 * 
 * Lists scheduled shows with details.
 * Provides a form to schedule a new show by linking movies to a theatre, date, and time.
 * Includes show deletion triggers.
 */

// Title for header
$page_title = "Manage Shows";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Restrict access to admin only
checkAdmin();

// Handle Show Insertion Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_show') {
    $movie_id = intval($_POST['movie_id']);
    $theatre_name = sanitize($_POST['theatre_name']);
    $show_date = sanitize($_POST['show_date']);
    $show_time = sanitize($_POST['show_time']);
    
    $errors = [];
    
    if ($movie_id <= 0) $errors[] = "Please select a valid movie.";
    if (empty($theatre_name)) $errors[] = "Theatre Name is required.";
    if (empty($show_date)) $errors[] = "Show Date is required.";
    if (empty($show_time)) $errors[] = "Show Time is required.";
    
    if (empty($errors)) {
        // Insert new show into database
        $insert_sql = "INSERT INTO shows (movie_id, theatre_name, show_time, show_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("isss", $movie_id, $theatre_name, $show_time, $show_date);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Show scheduled successfully!";
            header("Location: shows.php");
            exit();
        } else {
            $errors[] = "Failed to schedule show: " . $conn->error;
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header("Location: shows.php");
        exit();
    }
}

// Handle Show Deletion GET Trigger
if (isset($_GET['delete'])) {
    $show_id_to_delete = intval($_GET['delete']);
    if ($show_id_to_delete > 0) {
        $delete_sql = "DELETE FROM shows WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $show_id_to_delete);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Show deleted successfully. All tickets booked for this show were cancelled.";
        } else {
            $_SESSION['error_message'] = "Error deleting show: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: shows.php");
    exit();
}

// Fetch all movies to populate dropdown select
$movies_result = $conn->query("SELECT id, title FROM movies ORDER BY title ASC");

// Fetch all scheduled shows with movie titles (using JOIN query)
$shows_sql = "SELECT shows.*, movies.title as movie_title, movies.language as movie_lang 
              FROM shows 
              JOIN movies ON shows.movie_id = movies.id 
              ORDER BY shows.show_date DESC, shows.show_time ASC";
$shows_result = $conn->query($shows_sql);
?>

<div class="row">
    <!-- Page Title -->
    <div class="col-12 mb-4">
        <h2 class="text-navy fw-bold"><i class="bi bi-calendar-event"></i> Manage Showtimes</h2>
        <p class="text-muted">Map registered movies to screens, schedule dates, and timings.</p>
    </div>
    
    <!-- Add Showtime Column -->
    <div class="col-md-4 mb-4">
        <div class="card card-portal form-portal shadow-sm">
            <div class="card-header-portal">
                <i class="bi bi-calendar-plus"></i> Schedule New Show
            </div>
            <div class="card-body">
                <form action="shows.php" method="POST">
                    <input type="hidden" name="action" value="add_show">
                    
                    <!-- Select Movie -->
                    <div class="mb-3">
                        <label for="movie_id" class="form-label">Select Movie</label>
                        <select class="form-select" id="movie_id" name="movie_id" required>
                            <option value="">-- Choose Movie --</option>
                            <?php if ($movies_result && $movies_result->num_rows > 0): ?>
                                <?php while ($movie = $movies_result->fetch_assoc()): ?>
                                    <option value="<?php echo $movie['id']; ?>"><?php echo htmlspecialchars($movie['title']); ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Screen / Theatre name -->
                    <div class="mb-3">
                        <label for="theatre_name" class="form-label">Theatre / Screen Name</label>
                        <input type="text" class="form-control" id="theatre_name" name="theatre_name" required placeholder="e.g. Screen 1 - PVR">
                    </div>
                    
                    <!-- Date input -->
                    <div class="mb-3">
                        <label for="show_date" class="form-label">Show Date</label>
                        <input type="date" class="form-control" id="show_date" name="show_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <!-- Time input -->
                    <div class="mb-3">
                        <label for="show_time" class="form-label">Show Time</label>
                        <input type="time" class="form-control" id="show_time" name="show_time" required>
                    </div>
                    
                    <!-- Submit -->
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-navy"><i class="bi bi-plus-circle"></i> Add Show</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scheduled Shows List Column -->
    <div class="col-md-8 mb-4">
        <div class="card card-portal overflow-hidden shadow-sm">
            <div class="card-header-portal">
                <i class="bi bi-table"></i> Current Scheduled Shows List
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-portal mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Movie</th>
                                <th>Theatre / Screen</th>
                                <th>Show Date</th>
                                <th>Time</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($shows_result && $shows_result->num_rows > 0): ?>
                                <?php while ($show = $shows_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-navy"><?php echo htmlspecialchars($show['movie_title']); ?></strong>
                                            <span class="d-block small text-muted">(<?php echo htmlspecialchars($show['movie_lang']); ?>)</span>
                                        </td>
                                        <td><?php echo htmlspecialchars($show['theatre_name']); ?></td>
                                        <td><?php echo formatDate($show['show_date']); ?></td>
                                        <td class="fw-bold text-secondary"><?php echo formatTime($show['show_time']); ?></td>
                                        <td class="text-center">
                                            <a href="shows.php?delete=<?php echo $show['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this show? All tickets purchased will be deleted.');" title="Delete Showtime">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-calendar-x display-4 text-muted d-block mb-2"></i>
                                        <span class="text-muted">No shows scheduled yet. Add details on the left to set up a new show!</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-2">
    <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
