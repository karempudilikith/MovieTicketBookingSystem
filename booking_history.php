<?php
/**
 * User Booking History Page
 * 
 * Lists all movie tickets purchased by the currently logged-in customer.
 * Displays details about movies, screens, date/time, and pricing.
 * Includes direct links to re-print e-tickets.
 */

// Title for header
$page_title = "Booking History";

// Include header
require_once 'includes/header.php';

// Restrict access to logged-in users only
checkUser();

$user_id = $_SESSION['user_id'];

// Query bookings specifically matching the logged-in customer's ID (with details)
$sql = "SELECT bookings.*, 
               movies.title as movie_title, 
               shows.theatre_name, shows.show_date, shows.show_time
        FROM bookings
        JOIN shows ON bookings.show_id = shows.id
        JOIN movies ON shows.movie_id = movies.id
        WHERE bookings.user_id = ?
        ORDER BY bookings.booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="mb-4">
    <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Homepage</a>
</div>

<div class="card card-portal overflow-hidden shadow-sm">
    <div class="card-header-portal">
        <i class="bi bi-clock-history"></i> Your Ticket Booking History
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-portal mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 100px;">Ticket ID</th>
                        <th>Movie Title</th>
                        <th>Theatre / Screen</th>
                        <th>Show Date & Time</th>
                        <th>Seats Booked</th>
                        <th>Total Price</th>
                        <th>Purchase Date</th>
                        <th class="text-center" style="width: 140px;">Print Voucher</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($booking = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary font-monospace">Cine-<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td class="fw-bold text-navy"><?php echo htmlspecialchars($booking['movie_title']); ?></td>
                                <td><?php echo htmlspecialchars($booking['theatre_name']); ?></td>
                                <td>
                                    <span class="d-block"><?php echo formatDate($booking['show_date']); ?></span>
                                    <small class="text-danger fw-semibold"><?php echo formatTime($booking['show_time']); ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $seats_arr = explode(',', $booking['seats']);
                                    foreach ($seats_arr as $seat):
                                    ?>
                                        <span class="badge bg-info text-dark font-monospace mb-1"><?php echo htmlspecialchars($seat); ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td class="fw-bold text-success"><?php echo formatPrice($booking['total_price']); ?></td>
                                <td class="small text-muted"><?php echo date("d M Y h:i A", strtotime($booking['booking_date'])); ?></td>
                                <td class="text-center">
                                    <!-- Redirects to payment success page in GET mode to print ticket -->
                                    <a href="payment_success.php?booking_id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-navy px-3" title="Print E-Ticket">
                                        <i class="bi bi-printer"></i> View / Print
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- If user has not booked any tickets yet -->
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-ticket-perforated display-4 text-muted d-block mb-2"></i>
                                <span class="text-muted">You haven't booked any movie tickets yet.</span>
                                <a href="index.php" class="btn btn-navy btn-sm mt-3">Book Tickets Now</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$stmt->close();
// Include footer
require_once 'includes/footer.php';
?>
