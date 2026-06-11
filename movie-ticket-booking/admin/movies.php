<?php
/**
 * Admin Movies Management Page
 * 
 * Lists all movies stored in the database in a clean tabular view.
 * Provides shortcuts to add a new movie, edit an existing movie,
 * or delete a movie.
 */

// Title for header
$page_title = "Manage Movies";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Restrict access to admin only
checkAdmin();

// Fetch all movies from database
$sql = "ORDER BY release_date DESC";
$result = $conn->query("SELECT * FROM movies " . $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div>
        <h2 class="text-navy fw-bold mb-0"><i class="bi bi-film"></i> Manage Movies</h2>
        <p class="text-muted mb-0">Create, edit, and delete movie registers in the portal database.</p>
    </div>
    <div>
        <a href="add_movie.php" class="btn btn-navy"><i class="bi bi-plus-circle"></i> Add New Movie</a>
    </div>
</div>

<div class="card card-portal overflow-hidden">
    <div class="card-header-portal">
        <i class="bi bi-list-stars"></i> Registered Movies List
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-portal mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Poster</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Language</th>
                        <th>Duration</th>
                        <th>Release Date</th>
                        <th>Price</th>
                        <th class="text-center" style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($movie = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <!-- Poster Preview -->
                                    <?php 
                                    $poster_path = '../images/' . $movie['poster'];
                                    if (!empty($movie['poster']) && file_exists($poster_path)): 
                                    ?>
                                        <img src="<?php echo $poster_path; ?>" class="rounded shadow-sm" alt="Poster" style="width: 50px; height: 65px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white text-center rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 65px;">
                                            <i class="bi bi-film"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-navy"><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                <td><span class="badge bg-navy text-white"><?php echo htmlspecialchars($movie['language']); ?></span></td>
                                <td><?php echo htmlspecialchars($movie['duration']); ?> mins</td>
                                <td><?php echo formatDate($movie['release_date']); ?></td>
                                <td class="fw-bold text-success"><?php echo formatPrice($movie['ticket_price']); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Edit Button -->
                                        <a href="edit_movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Movie">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <!-- Delete Button (Includes JS confirmation prompt) -->
                                        <a href="delete_movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete \'<?php echo addslashes($movie['title']); ?>\'? This will also delete all scheduled shows and bookings for this movie.');" title="Delete Movie">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-info-circle display-4 text-muted d-block mb-2"></i>
                                <span class="text-muted">No movies registered yet. Click "Add New Movie" to register your first movie!</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
