/**
 * main.js - Seat Selection Logic
 * CinePass Movie Ticket Booking Portal
 * 
 * Handles interactive seat clicks, toggles selection states,
 * dynamically updates pricing counters, and validates seat submissions.
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Select all available seat elements in the grid
    const seats = document.querySelectorAll('.seat.available');
    
    // 2. Select display elements for updating values
    const selectedSeatsCount = document.getElementById('selected-seats-count');
    const selectedSeatsList = document.getElementById('selected-seats-list');
    const totalAmountSpan = document.getElementById('total-amount');
    
    // 3. Select hidden form inputs to be submitted to backend
    const hiddenSeatsInput = document.getElementById('selected-seats-input');
    const hiddenTotalAmountInput = document.getElementById('total-price-input');
    
    // Get ticket price from HTML attribute (stored in totalAmountSpan container)
    const ticketPrice = parseFloat(document.getElementById('ticket-price-val').value);
    
    // Set to track chosen seat names (e.g. A1, B3)
    let selectedSeats = [];

    // 4. Add Click Event Listener to each available seat
    seats.forEach(function(seat) {
        seat.addEventListener('click', function() {
            const seatName = this.getAttribute('data-seat');
            
            // Toggle selection state
            if (this.classList.contains('selected')) {
                // If already selected, deselect it
                this.classList.remove('selected');
                selectedSeats = selectedSeats.filter(item => item !== seatName);
            } else {
                // If not selected, select it
                this.classList.add('selected');
                selectedSeats.push(seatName);
            }
            
            // 5. Update the UI and Form inputs
            updateSelectionSummary();
        });
    });

    /**
     * Updates seat counters, total price calculation, 
     * and binds data to hidden inputs for form post.
     */
    function updateSelectionSummary() {
        // Sort seats alphabetically for a clean order (e.g., A1, A2, B1)
        selectedSeats.sort();
        
        // Update display text
        selectedSeatsCount.textContent = selectedSeats.length;
        selectedSeatsList.textContent = selectedSeats.length > 0 ? selectedSeats.join(', ') : 'None';
        
        // Calculate total amount
        const totalCost = selectedSeats.length * ticketPrice;
        totalAmountSpan.textContent = totalCost.toFixed(2);
        
        // Update hidden inputs for form post
        hiddenSeatsInput.value = selectedSeats.join(',');
        hiddenTotalAmountInput.value = totalCost.toFixed(2);
    }
});

/**
 * Validates the booking form before letting user proceed
 * Ensures at least one seat is chosen.
 */
function validateBookingForm() {
    const seatsInput = document.getElementById('selected-seats-input');
    if (!seatsInput || seatsInput.value.trim() === '') {
        alert('Please select at least one seat to proceed with the booking.');
        return false;
    }
    return true;
}
