<?php
/**
 * Movie Details & Shows Select Page
 * 
 * Fetches the specific movie by its ID from the URL parameters.
 * Lists the description, price, runtime, and all scheduled showtimes/theatres.
 * Users select a show to enter the seat reservation phase.
 */

// Title for header
$page_title = "Movie Details";

// Include header
require_once 'includes/header.php';

// Validate and retrieve movie ID from GET request
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($movie_id <= 0) {
    $_SESSION['error_message'] = "Invalid movie request.";
    header("Location: index.php");
    exit();
}

// Fetch movie details from database
$movie_sql = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($movie_sql);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$movie_result = $stmt->get_result();

if ($movie_result->num_rows === 0) {
    $_SESSION['error_message'] = "Movie not found.";
    header("Location: index.php");
    exit();
}

$movie = $movie_result->fetch_assoc();
$stmt->close();

// Fetch shows for this movie scheduled for today and onwards
// We order shows by date, then by time, to display them logically
$shows_sql = "SELECT * FROM shows WHERE movie_id = ? AND show_date >= CURDATE() ORDER BY show_date, show_time";
$shows_stmt = $conn->prepare($shows_sql);
$shows_stmt->bind_param("i", $movie_id);
$shows_stmt->execute();
$shows_result = $shows_stmt->get_result();
$shows_stmt->close();
?>

<!-- Back to Home navigation -->
<div class="mb-4 no-print">
    <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Now Showing</a>
</div>

<!-- Movie Details Card -->
<div class="card card-portal p-0 mb-4 overflow-hidden">
    <div class="row g-0">
        <!-- Movie Poster Column -->
        <div class="col-md-4 text-center bg-dark">
            <?php 
            $poster_path = 'images/' . $movie['poster'];
            if (!empty($movie['poster']) && file_exists($poster_path)): 
            ?>
                <img src="<?php echo $poster_path; ?>" class="img-fluid w-100" alt="<?php echo htmlspecialchars($movie['title']); ?>" style="max-height: 500px; object-fit: cover;">
            <?php else: ?>
                <div class="text-white d-flex flex-column align-items-center justify-content-center h-100 py-5" style="min-height: 350px; background: linear-gradient(135deg, #1e3d59 0%, #17b978 100%);">
                    <i class="bi bi-film display-1 mb-2"></i>
                    <span class="fs-4 px-3 fw-bold"><?php echo htmlspecialchars($movie['title']); ?></span>
                    <span class="text-white-50">Poster Image Not Found</span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Movie Metadata Column -->
        <div class="col-md-8">
            <div class="card-body p-4">
                <h2 class="card-title fw-bold text-navy mb-2"><?php echo htmlspecialchars($movie['title']); ?></h2>
                
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <span class="badge bg-navy text-white fs-6"><i class="bi bi-tags-fill"></i> <?php echo htmlspecialchars($movie['genre']); ?></span>
                    <span class="badge bg-secondary fs-6"><i class="bi bi-translate"></i> <?php echo htmlspecialchars($movie['language']); ?></span>
                    <span class="badge bg-info text-dark fs-6"><i class="bi bi-clock-fill"></i> <?php echo htmlspecialchars($movie['duration']); ?> mins</span>
                </div>
                
                <hr>
                
                <h5 class="fw-bold text-secondary">Synopsis</h5>
                <p class="card-text text-dark" style="line-height: 1.6; text-align: justify;">
                    <?php echo nl2br(htmlspecialchars($movie['description'])); ?>
                </p>
                
                <hr>
                
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <strong>Release Date:</strong> 
                        <span class="text-muted"><?php echo formatDate($movie['release_date']); ?></span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong>Ticket Price:</strong> 
                        <span class="text-success fw-bold fs-5"><?php echo formatPrice($movie['ticket_price']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Movie Shows Listing -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="border-bottom pb-2 text-navy fw-bold"><i class="bi bi-calendar-event"></i> Select a Theatre & Show Time</h4>
    </div>
    
    <div class="col-12 mt-3">
        <?php if ($shows_result && $shows_result->num_rows > 0): ?>
            <!-- Group shows in tables/lists by date for easier navigation -->
            <div class="row">
                <?php 
                $current_date = '';
                while ($show = $shows_result->fetch_assoc()): 
                    // Format dates to separate rows
                    $show_date_formatted = formatDate($show['show_date']);
                    if ($current_date !== $show_date_formatted):
                        $current_date = $show_date_formatted;
                ?>
                        <!-- Header for each date -->
                        <div class="col-12 mt-3">
                            <h5 class="bg-secondary text-white p-2 rounded fw-semibold">
                                <i class="bi bi-calendar-check"></i> Shows on: <?php echo $current_date; ?>
                            </h5>
                        </div>
                <?php 
                    endif; 
                ?>
                
                <!-- Individual show ticket card -->
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 shadow-sm border border-light">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-navy mb-1"><i class="bi bi-building"></i> <?php echo htmlspecialchars($show['theatre_name']); ?></h6>
                            <p class="text-muted small mb-3"><i class="bi bi-clock"></i> Show Time: <strong class="text-dark"><?php echo formatTime($show['show_time']); ?></strong></p>
                            
                            <div class="d-grid">
                                <a href="book_seats.php?show_id=<?php echo $show['id']; ?>" class="btn btn-navy">
                                    <i class="bi bi-grid-3x3-gap"></i> Book Seats
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- Error panel if no shows found -->
            <div class="alert alert-warning text-center p-4">
                <i class="bi bi-info-circle display-4 text-warning"></i>
                <h5 class="mt-3">No Shows Available</h5>
                <p class="mb-0">There are currently no active shows scheduled for "<strong><?php echo htmlspecialchars($movie['title']); ?></strong>". Please check back later or view other movies.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>
