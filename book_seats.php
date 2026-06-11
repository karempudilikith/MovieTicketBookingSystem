<?php
/**
 * Book Seats Page
 * 
 * Renders the seat layout grid (Rows A, B, C with 8 columns each).
 * Fetches already booked seats from the database and disables them (red).
 * Available seats can be clicked (blue) to dynamically calculate ticket prices using JS.
 */

// Title for header
$page_title = "Book Seats";

// Include header
require_once 'includes/header.php';

// Restrict page to logged-in users
checkUser();

// Validate and retrieve show ID from GET request
$show_id = isset($_GET['show_id']) ? intval($_GET['show_id']) : 0;

if ($show_id <= 0) {
    $_SESSION['error_message'] = "Invalid show request.";
    header("Location: index.php");
    exit();
}

// Fetch details about the show and the associated movie (using SQL JOIN)
$show_sql = "SELECT shows.*, movies.title as movie_title, movies.language as movie_lang, movies.ticket_price 
             FROM shows 
             JOIN movies ON shows.movie_id = movies.id 
             WHERE shows.id = ?";
$stmt = $conn->prepare($show_sql);
$stmt->bind_param("i", $show_id);
$stmt->execute();
$show_result = $stmt->get_result();

if ($show_result->num_rows === 0) {
    $_SESSION['error_message'] = "Show time not found.";
    header("Location: index.php");
    exit();
}

$show = $show_result->fetch_assoc();
$stmt->close();

// Fetch already booked seats for this show
$booked_seats = [];
$booking_sql = "SELECT seats FROM bookings WHERE show_id = ? AND status = 'Booked'";
$booking_stmt = $conn->prepare($booking_sql);
$booking_stmt->bind_param("i", $show_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();

while ($row = $booking_result->fetch_assoc()) {
    // Since seats are saved as comma-separated values (e.g. "A1,A2"), we split them
    $seats_arr = explode(',', $row['seats']);
    $booked_seats = array_merge($booked_seats, $seats_arr);
}
$booking_stmt->close();

// Trim whitespace from seats array just in case
$booked_seats = array_map('trim', $booked_seats);
?>

<div class="mb-4">
    <a href="movie_details.php?id=<?php echo $show['movie_id']; ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Shows</a>
</div>

<div class="row">
    <!-- Movie & Showtime Info header banner -->
    <div class="col-12 mb-4">
        <div class="p-3 bg-white border rounded shadow-sm d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="text-navy fw-bold mb-1"><i class="bi bi-film"></i> <?php echo htmlspecialchars($show['movie_title']); ?></h4>
                <p class="text-muted mb-0 small">
                    <i class="bi bi-building"></i> <?php echo htmlspecialchars($show['theatre_name']); ?> | 
                    <i class="bi bi-calendar"></i> <?php echo formatDate($show['show_date']); ?> | 
                    <i class="bi bi-clock"></i> <?php echo formatTime($show['show_time']); ?>
                </p>
            </div>
            <div class="text-end">
                <span class="d-block text-muted small">Single Ticket Price</span>
                <strong class="text-success fs-5"><?php echo formatPrice($show['ticket_price']); ?></strong>
                <!-- Pass price value to JS via hidden input -->
                <input type="hidden" id="ticket-price-val" value="<?php echo $show['ticket_price']; ?>">
            </div>
        </div>
    </div>
    
    <!-- Seat Grid Container Column -->
    <div class="col-lg-7 mb-4">
        <div class="seat-container">
            <h5 class="text-center text-secondary mb-4"><i class="bi bi-grid-3x3-gap"></i> Choose Seats</h5>
            
            <!-- Screen Indicator -->
            <div class="screen-indicator">SCREEN THIS WAY</div>
            
            <!-- Row Seat layouts -->
            <?php
            // Define rows and column numbers
            $rows = ['A', 'B', 'C'];
            $cols = 8;
            
            foreach ($rows as $row_letter):
            ?>
                <div class="seat-row">
                    <!-- Row Label -->
                    <div class="seat-label"><?php echo $row_letter; ?></div>
                    
                    <!-- Seats -->
                    <?php 
                    for ($c = 1; $c <= $cols; $c++): 
                        $seat_name = $row_letter . $c;
                        $is_booked = in_array($seat_name, $booked_seats);
                    ?>
                        <div class="seat <?php echo $is_booked ? 'booked' : 'available'; ?>" 
                             data-seat="<?php echo $seat_name; ?>"
                             title="<?php echo $is_booked ? 'Seat ' . $seat_name . ' (Booked)' : 'Seat ' . $seat_name . ' (Available)'; ?>">
                            <?php echo $seat_name; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Color Code Legend -->
            <div class="seat-legend">
                <div class="legend-item">
                    <div class="legend-color available"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color selected"></div>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color booked"></div>
                    <span>Booked</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Selection summary & reservation form Column -->
    <div class="col-lg-5 mb-4">
        <div class="card card-portal shadow-sm">
            <div class="card-header-portal">
                <i class="bi bi-info-circle-fill"></i> Reservation Details
            </div>
            <div class="card-body p-4">
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Selected Seats Count:</span>
                        <strong id="selected-seats-count" class="text-navy fs-5">0</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Selected Seats:</span>
                        <strong id="selected-seats-list" class="text-secondary fs-6">None</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="fw-bold">Total Amount:</span>
                        <span class="text-success fw-bold fs-4">Rs. <span id="total-amount">0.00</span></span>
                    </li>
                </ul>
                
                <!-- Booking Submit Form -->
                <form action="booking_confirmation.php" method="POST" onsubmit="return validateBookingForm();">
                    <!-- Hidden fields to pass parameters to the next page -->
                    <input type="hidden" name="show_id" value="<?php echo $show_id; ?>">
                    <input type="hidden" name="selected_seats" id="selected-seats-input" value="">
                    <input type="hidden" name="total_price" id="total-price-input" value="0.00">
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-navy py-2"><i class="bi bi-credit-card"></i> Proceed to Confirmation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>
