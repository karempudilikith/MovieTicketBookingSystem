<?php
/**
 * Booking Confirmation Page
 * 
 * Receives seat selection metadata from 'book_seats.php' via POST.
 * Fetches relevant movie/show details from database, displays a summary,
 * and prompts the user to confirm the reservation before simulating payment.
 */

// Title for header
$page_title = "Booking Confirmation";

// Include header
require_once 'includes/header.php';

// Restrict access to logged-in users only
checkUser();

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Direct access to confirmation page is denied. Please select seats first.";
    header("Location: index.php");
    exit();
}

// Retrieve posted variables
$show_id = isset($_POST['show_id']) ? intval($_POST['show_id']) : 0;
$selected_seats = isset($_POST['selected_seats']) ? sanitize($_POST['selected_seats']) : '';
$total_price = isset($_POST['total_price']) ? doubleval($_POST['total_price']) : 0.00;

// Validation
if ($show_id <= 0 || empty($selected_seats) || $total_price <= 0) {
    $_SESSION['error_message'] = "Invalid booking details received. Please select seats again.";
    header("Location: index.php");
    exit();
}

// Fetch show details
$show_sql = "SELECT shows.*, movies.title as movie_title, movies.language as movie_lang, movies.ticket_price 
             FROM shows 
             JOIN movies ON shows.movie_id = movies.id 
             WHERE shows.id = ?";
$stmt = $conn->prepare($show_sql);
$stmt->bind_param("i", $show_id);
$stmt->execute();
$show_result = $stmt->get_result();

if ($show_result->num_rows === 0) {
    $_SESSION['error_message'] = "Show details not found.";
    header("Location: index.php");
    exit();
}

$show = $show_result->fetch_assoc();
$stmt->close();
?>

<div class="mb-4">
    <a href="book_seats.php?show_id=<?php echo $show_id; ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Change Seat Selection</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card card-portal shadow-sm">
            <div class="card-header-portal text-center">
                <h3><i class="bi bi-shield-check"></i> Booking Summary</h3>
                <p class="mb-0 text-white-50 small">Please review your ticket reservation details below</p>
            </div>
            <div class="card-body p-4">
                
                <!-- Ticket details table -->
                <table class="table table-bordered mb-4">
                    <tbody>
                        <tr>
                            <td class="bg-light fw-bold" style="width: 40%;">Movie Name</td>
                            <td class="fw-semibold text-navy"><?php echo htmlspecialchars($show['movie_title']); ?> (<?php echo htmlspecialchars($show['movie_lang']); ?>)</td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold">Theatre / Screen</td>
                            <td><?php echo htmlspecialchars($show['theatre_name']); ?></td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold">Show Date</td>
                            <td><?php echo formatDate($show['show_date']); ?></td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold">Show Time</td>
                            <td class="fw-bold text-secondary"><?php echo formatTime($show['show_time']); ?></td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold">Booked Seats</td>
                            <td>
                                <?php 
                                $seats_arr = explode(',', $selected_seats);
                                foreach ($seats_arr as $seat):
                                ?>
                                    <span class="badge bg-info text-dark me-1"><?php echo htmlspecialchars($seat); ?></span>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold">Ticket Price</td>
                            <td><?php echo formatPrice($show['ticket_price']); ?> per seat</td>
                        </tr>
                        <tr class="table-success text-success fw-bold fs-5">
                            <td>Total Amount</td>
                            <td><?php echo formatPrice($total_price); ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Payment Form Simulating Bank Gateway -->
                <form action="payment_success.php" method="POST">
                    <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
                    <input type="hidden" name="selected_seats" value="<?php echo $selected_seats; ?>">
                    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                    
                    <div class="alert alert-info text-center small py-2 mb-4">
                        <i class="bi bi-info-circle"></i> Clicking confirm will simulate a successful banking transaction.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-navy py-2"><i class="bi bi-credit-card-2-front"></i> Confirm & Pay <?php echo formatPrice($total_price); ?></button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light">
                <span class="text-muted small">Transactions are processed securely. No actual funds are charged.</span>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>
