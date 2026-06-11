<?php
/**
 * Admin Bookings Management Page
 * 
 * Lists all movie tickets booked across the application.
 * Joins users, shows, and movies to fetch dynamic descriptions.
 */

// Title for header
$page_title = "Manage Bookings";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Restrict access to admin only
checkAdmin();

// Fetch all bookings from database with details about user, movie, and show
$bookings_sql = "SELECT bookings.*, 
                        users.name as customer_name, users.email as customer_email,
                        movies.title as movie_title,
                        shows.theatre_name, shows.show_date, shows.show_time
                 FROM bookings
                 JOIN users ON bookings.user_id = users.id
                 JOIN shows ON bookings.show_id = shows.id
                 JOIN movies ON shows.movie_id = movies.id
                 ORDER BY bookings.booking_date DESC";

$result = $conn->query($bookings_sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div>
        <h2 class="text-navy fw-bold mb-0"><i class="bi bi-ticket-perforated"></i> Customer Bookings</h2>
        <p class="text-muted mb-0">Review audit trail of all ticket sales, seat numbers, and receipts.</p>
    </div>
</div>

<div class="card card-portal overflow-hidden shadow-sm">
    <div class="card-header-portal">
        <i class="bi bi-list-check"></i> Overall Ticket Bookings
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-portal mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Customer</th>
                        <th>Movie Details</th>
                        <th>Screen / Theatre</th>
                        <th>Show Timing</th>
                        <th>Seats</th>
                        <th>Paid Amount</th>
                        <th>Booking Date</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($booking = $result->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge bg-secondary">B-<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                <td>
                                    <strong class="text-navy"><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                    <span class="d-block small text-muted"><?php echo htmlspecialchars($booking['customer_email']); ?></span>
                                </td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($booking['movie_title']); ?></td>
                                <td><?php echo htmlspecialchars($booking['theatre_name']); ?></td>
                                <td>
                                    <span class="d-block"><?php echo formatDate($booking['show_date']); ?></span>
                                    <small class="text-secondary fw-bold"><?php echo formatTime($booking['show_time']); ?></small>
                                </td>
                                <td>
                                    <?php 
                                    // Split seats string and apply small pill badge to each seat
                                    $seats_arr = explode(',', $booking['seats']);
                                    foreach ($seats_arr as $seat):
                                    ?>
                                        <span class="badge bg-info text-dark mb-1"><?php echo htmlspecialchars($seat); ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td class="fw-bold text-success"><?php echo formatPrice($booking['total_price']); ?></td>
                                <td class="small text-muted"><?php echo date("d M Y h:i A", strtotime($booking['booking_date'])); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success text-white py-2 px-3 rounded-pill">
                                        <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-ticket-detailed display-4 text-muted d-block mb-2"></i>
                                <span class="text-muted">No ticket bookings recorded yet.</span>
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
