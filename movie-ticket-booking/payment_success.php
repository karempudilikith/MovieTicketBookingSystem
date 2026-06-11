<?php
/**
 * Payment Success & Ticket Receipt Page
 * 
 * Implements the Post-Redirect-Get (PRG) pattern for refresh-safety:
 * 1. POST Request: Saves the booking to the database and redirects to the GET request.
 * 2. GET Request: Fetches the booking details and renders a clean, printable receipt ticket.
 */

// Title for header
$page_title = "Ticket Confirmation";

// Include header
require_once 'includes/header.php';

// Restrict access to logged-in users only
checkUser();

// --- 1. POST Request Handling (Saving the booking) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $show_id = isset($_POST['show_id']) ? intval($_POST['show_id']) : 0;
    $selected_seats = isset($_POST['selected_seats']) ? sanitize($_POST['selected_seats']) : '';
    $total_price = isset($_POST['total_price']) ? doubleval($_POST['total_price']) : 0.00;
    $user_id = $_SESSION['user_id'];
    
    if ($show_id <= 0 || empty($selected_seats) || $total_price <= 0) {
        $_SESSION['error_message'] = "Booking parameters missing.";
        header("Location: index.php");
        exit();
    }
    
    // Insert booking into bookings table
    $insert_sql = "INSERT INTO bookings (user_id, show_id, seats, total_price, status) VALUES (?, ?, ?, ?, 'Booked')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisd", $user_id, $show_id, $selected_seats, $total_price);
    
    if ($stmt->execute()) {
        $new_booking_id = $stmt->insert_id;
        $stmt->close();
        
        // Redirect to GET request to prevent duplicate database inserts on page refresh
        $_SESSION['success_message'] = "Payment Successful! Your tickets have been reserved.";
        header("Location: payment_success.php?booking_id=" . $new_booking_id);
        exit();
    } else {
        $stmt->close();
        $_SESSION['error_message'] = "Booking insertion failed: " . $conn->error;
        header("Location: index.php");
        exit();
    }
}

// --- 2. GET Request Handling (Displaying printable receipt) ---
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id <= 0) {
    $_SESSION['error_message'] = "Booking receipt not found.";
    header("Location: index.php");
    exit();
}

// Fetch booking records along with movie, show and user details
$booking_sql = "SELECT bookings.*, 
                       users.name as customer_name, users.email as customer_email,
                       movies.title as movie_title, movies.language as movie_lang,
                       shows.theatre_name, shows.show_date, shows.show_time
                FROM bookings
                JOIN users ON bookings.user_id = users.id
                JOIN shows ON bookings.show_id = shows.id
                JOIN movies ON shows.movie_id = movies.id
                WHERE bookings.id = ?";
$stmt = $conn->prepare($booking_sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Ticket record not found.";
    header("Location: index.php");
    exit();
}

$booking = $result->fetch_assoc();
$stmt->close();

// Security: Prevent users from viewing other customers' receipts (unless they are admin)
if ($booking['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
    $_SESSION['error_message'] = "Access denied. You cannot view other user's tickets.";
    header("Location: index.php");
    exit();
}
?>

<div class="row justify-content-center">
    <!-- Success Banner -->
    <div class="col-md-8 text-center no-print mb-3">
        <div class="p-3 bg-success bg-opacity-10 text-success rounded border border-success border-opacity-20 d-inline-block px-5">
            <i class="bi bi-patch-check-fill display-3"></i>
            <h4 class="fw-bold mt-2">Reservation Completed!</h4>
            <p class="mb-0 small">Thank you for booking with CinePass. Your ticket details are shown below.</p>
        </div>
    </div>
    
    <!-- Printable Ticket Container Card -->
    <div class="col-md-8 col-lg-7">
        <div class="card card-portal shadow border-primary">
            
            <!-- Ticket Header block (styled like a movie voucher header) -->
            <div class="card-header-portal text-center d-flex justify-content-between align-items-center bg-navy py-3">
                <h4 class="mb-0 fw-bold"><i class="bi bi-ticket-perforated"></i> E-Ticket Voucher</h4>
                <span class="badge bg-warning text-dark font-monospace fw-bold">ID: Cine-<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            
            <!-- Ticket Body -->
            <div class="card-body p-4 bg-white">
                <div class="text-center mb-4">
                    <h2 class="text-navy fw-bold mb-0"><?php echo htmlspecialchars($booking['movie_title']); ?></h2>
                    <span class="text-muted text-uppercase small font-monospace"><?php echo htmlspecialchars($booking['movie_lang']); ?> | Cinema Ticket</span>
                </div>
                
                <div class="row mb-4">
                    <!-- Column Left: Screening Details -->
                    <div class="col-6 border-end">
                        <small class="text-muted d-block text-uppercase font-monospace mb-1">Theatre / Screen</small>
                        <strong class="text-navy d-block"><?php echo htmlspecialchars($booking['theatre_name']); ?></strong>
                        
                        <small class="text-muted d-block text-uppercase font-monospace mt-3 mb-1">Seats Reserved</small>
                        <div>
                            <?php 
                            $seats_arr = explode(',', $booking['seats']);
                            foreach ($seats_arr as $seat):
                            ?>
                                <span class="badge bg-info text-dark font-monospace me-1"><?php echo htmlspecialchars($seat); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Column Right: Date and Timing details -->
                    <div class="col-6 ps-4">
                        <small class="text-muted d-block text-uppercase font-monospace mb-1">Show Date</small>
                        <strong class="text-navy d-block"><?php echo formatDate($booking['show_date']); ?></strong>
                        
                        <small class="text-muted d-block text-uppercase font-monospace mt-3 mb-1">Show Time</small>
                        <strong class="text-danger fs-5"><?php echo formatTime($booking['show_time']); ?></strong>
                    </div>
                </div>
                
                <!-- Ticket details box -->
                <div class="p-3 bg-light rounded border mb-4 font-monospace">
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span>Customer Name:</span>
                        <strong class="text-navy"><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Booking Timestamp:</span>
                        <span><?php echo date("d M Y h:i A", strtotime($booking['booking_date'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 text-success fw-bold fs-5">
                        <span>Total Price Paid:</span>
                        <span><?php echo formatPrice($booking['total_price']); ?></span>
                    </div>
                </div>
                
                <!-- Action Buttons: Back & Print (hidden when printing) -->
                <div class="d-flex gap-2 justify-content-between align-items-center no-print">
                    <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Home</a>
                    <a href="booking_history.php" class="btn btn-outline-navy"><i class="bi bi-clock-history"></i> My Bookings</a>
                    <!-- Javascript print trigger -->
                    <button onclick="window.print();" class="btn btn-warning px-4"><i class="bi bi-printer"></i> Print Ticket</button>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>
