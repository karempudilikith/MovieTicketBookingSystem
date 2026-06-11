<?php
/**
 * Application Homepage
 * 
 * Displays the main customer landing interface.
 * Features a search box to search by title, genre, or language.
 * Fetches and displays all available movies from the database in a responsive grid.
 */

// Title for header
$page_title = "Home - Movie Ticket Portal";

// Include header (this also starts sessions and connects to db)
require_once 'includes/header.php';

// Capture search query from GET request
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Construct base query
$sql = "SELECT * FROM movies";

// If search query is provided, modify SQL to filter records
if (!empty($search)) {
    // Escaping search inputs to prevent SQL errors
    $escaped_search = db_escape($conn, $search);
    $sql .= " WHERE title LIKE '%$escaped_search%' OR genre LIKE '%$escaped_search%' OR language LIKE '%$escaped_search%'";
}

$sql .= " ORDER BY release_date DESC";
$result = $conn->query($sql);
?>

<!-- Portal Header Banner -->
<div class="hero-banner rounded text-center mb-4">
    <h1 class="display-5 fw-bold"><i class="bi bi-camera-reels"></i> Cinema Chusuko</h1>
    <p class="lead">Book tickets for your favorite movies at screens near you!</p>
    
    <!-- Search Bar Form -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-8 col-lg-6">
            <form action="index.php" method="GET" class="d-flex shadow-sm rounded">
                <input class="form-control me-2 py-2" type="search" name="search" placeholder="Search by Movie Title, Genre, Language..." aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-warning px-4" type="submit"><i class="bi bi-search"></i> Search</button>
            </form>
            <?php if (!empty($search)): ?>
                <div class="mt-2 small text-light-50">
                    <a href="index.php" class="text-white text-decoration-underline">Clear Search Filter</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Movie Grid -->
<div class="row">
    <div class="col-12 mb-3">
        <h3 class="border-bottom pb-2 text-navy fw-bold">
            <i class="bi bi-fire text-danger"></i> 
            <?php echo !empty($search) ? "Search Results" : "Now Showing Movies"; ?>
        </h3>
    </div>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($movie = $result->fetch_assoc()): ?>
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 card-portal">
                    <!-- Movie Poster (with fallback if image is missing) -->
                    <?php 
                    $poster_path = 'images/' . $movie['poster'];
                    if (!empty($movie['poster']) && file_exists($poster_path)): 
                    ?>
                        <img src="<?php echo $poster_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>" style="height: 320px; object-fit: cover;">
                    <?php else: ?>
                        <!-- Dynamic CSS placeholder card poster -->
                        <div class="bg-secondary text-white text-center d-flex flex-column align-items-center justify-content-center" style="height: 320px; background: linear-gradient(135deg, #1e3d59 0%, #17b978 100%) !important;">
                            <i class="bi bi-film display-2 mb-2"></i>
                            <span class="px-2 fw-semibold text-wrap"><?php echo htmlspecialchars($movie['title']); ?></span>
                            <small class="text-white-50 mt-1">Poster Image Not Uploaded</small>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Card Body containing details -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-navy mb-1"><?php echo htmlspecialchars($movie['title']); ?></h5>
                        <p class="text-muted small mb-2"><i class="bi bi-tags"></i> <?php echo htmlspecialchars($movie['genre']); ?></p>
                        
                        <div class="mb-3">
                            <span class="badge bg-navy text-white"><i class="bi bi-translate"></i> <?php echo htmlspecialchars($movie['language']); ?></span>
                            <span class="badge bg-secondary"><i class="bi bi-hourglass-split"></i> <?php echo htmlspecialchars($movie['duration']); ?> mins</span>
                        </div>
                        
                        <!-- Pushes details and buttons to bottom -->
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <span class="fw-bold text-success fs-5"><?php echo formatPrice($movie['ticket_price']); ?></span>
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>" class="btn btn-navy btn-sm"><i class="bi bi-ticket-perforated"></i> View Shows</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <!-- No movies found message -->
        <div class="col-12 text-center my-5">
            <div class="p-5 bg-white rounded shadow-sm border border-light">
                <i class="bi bi-emoji-frown display-1 text-muted"></i>
                <h4 class="mt-3 text-secondary">No Movies Found</h4>
                <p class="text-muted">We couldn't find any movies matching "<strong><?php echo htmlspecialchars($search); ?></strong>".</p>
                <a href="index.php" class="btn btn-navy mt-2">View All Movies</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>
