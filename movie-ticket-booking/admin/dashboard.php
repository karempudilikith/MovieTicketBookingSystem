<?php
/**
 * Admin Control Dashboard
 * 
 * Shows summary statistics (total movies, shows, bookings, and users)
 * using Bootstrap metrics cards, and provides navigation to all administrative operations.
 */

// Title for header
$page_title = "Admin Dashboard";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Restrict access to admin only
checkAdmin();

// 1. Fetch count of total movies
$movies_count = 0;
$movie_query = $conn->query("SELECT COUNT(*) as total FROM movies");
if ($movie_query) {
    $row = $movie_query->fetch_assoc();
    $movies_count = $row['total'];
}

// 2. Fetch count of total shows
$shows_count = 0;
$show_query = $conn->query("SELECT COUNT(*) as total FROM shows");
if ($show_query) {
    $row = $show_query->fetch_assoc();
    $shows_count = $row['total'];
}

// 3. Fetch count of total bookings
$bookings_count = 0;
$booking_query = $conn->query("SELECT COUNT(*) as total FROM bookings");
if ($booking_query) {
    $row = $booking_query->fetch_assoc();
    $bookings_count = $row['total'];
}

// 4. Fetch count of total registered users (excluding admin role)
$users_count = 0;
$user_query = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
if ($user_query) {
    $row = $user_query->fetch_assoc();
    $users_count = $row['total'];
}
?>

<div class="row">
    <!-- Admin Portal Banner -->
    <div class="col-12 mb-4">
        <div class="p-4 bg-white rounded shadow-sm border border-light d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="text-navy fw-bold mb-1"><i class="bi bi-speedometer2"></i> Administrative Portal</h2>
                <p class="text-muted mb-0">Manage movies, shows, bookings, and customer details.</p>
            </div>
            <div class="text-end">
                <span class="badge bg-danger p-2 fs-6"><i class="bi bi-person-badge"></i> Signed in as Administrator</span>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards row -->
    <!-- Movies Counter Card -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-portal admin-card-stat movies p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Total Movies</h6>
                    <h2 class="fw-bold mb-0 text-navy"><?php echo $movies_count; ?></h2>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-film fs-2 text-primary"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="movies.php" class="text-primary text-decoration-none small fw-bold">Manage Movies <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Shows Counter Card -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-portal admin-card-stat shows p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Total Shows</h6>
                    <h2 class="fw-bold mb-0 text-navy"><?php echo $shows_count; ?></h2>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-calendar-event fs-2 text-warning"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="shows.php" class="text-warning text-decoration-none small fw-bold">Manage Shows <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Bookings Counter Card -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-portal admin-card-stat bookings p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Total Bookings</h6>
                    <h2 class="fw-bold mb-0 text-navy"><?php echo $bookings_count; ?></h2>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-ticket-perforated fs-2 text-success"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="bookings.php" class="text-success text-decoration-none small fw-bold">View Bookings <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Users Counter Card -->
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card card-portal admin-card-stat users p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small">Total Users</h6>
                    <h2 class="fw-bold mb-0 text-navy"><?php echo $users_count; ?></h2>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded">
                    <i class="bi bi-people fs-2 text-danger"></i>
                </div>
            </div>
            <div class="mt-3">
                <a href="users.php" class="text-danger text-decoration-none small fw-bold">View Users <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Link Dashboard Operations -->
<div class="row mt-2">
    <div class="col-12">
        <div class="card card-portal">
            <div class="card-header-portal">
                <i class="bi bi-sliders"></i> Quick Administrative Control Actions
            </div>
            <div class="card-body p-4">
                <div class="row text-center">
                    
                    <div class="col-6 col-md-3 mb-3">
                        <a href="add_movie.php" class="btn btn-outline-primary py-3 w-100">
                            <i class="bi bi-plus-circle fs-3 mb-2 d-block"></i> Add Movie
                        </a>
                    </div>
                    
                    <div class="col-6 col-md-3 mb-3">
                        <a href="shows.php" class="btn btn-outline-warning py-3 w-100">
                            <i class="bi bi-calendar-plus fs-3 mb-2 d-block"></i> Add Showtime
                        </a>
                    </div>
                    
                    <div class="col-6 col-md-3 mb-3">
                        <a href="bookings.php" class="btn btn-outline-success py-3 w-100">
                            <i class="bi bi-receipt-cutoff fs-3 mb-2 d-block"></i> View Reports
                        </a>
                    </div>
                    
                    <div class="col-6 col-md-3 mb-3">
                        <a href="users.php" class="btn btn-outline-danger py-3 w-100">
                            <i class="bi bi-person-check fs-3 mb-2 d-block"></i> Manage Users
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
