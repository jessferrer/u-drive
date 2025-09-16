<?php
require_once __DIR__ . '/../includes/functions.php';
// This ensures the session is started to handle error messages.
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// This includes the header, which contains the navigation and the opening HTML tags.
include __DIR__ . '/partials/header.php';

// ❗ New: Fetch all confirmed bookings to be used by JavaScript for client-side validation.
$confirmedBookings = fetch_all("
    SELECT unit_id, start_at, end_at
    FROM bookings
    WHERE status='confirmed'
");

// Fetch available units for the dropdown menu.
$units = fetch_all("SELECT id, make_model FROM units WHERE status='available' ORDER BY make_model");

// 🔹 New: Logic for displaying the modal
$showSuccessModal = false;
$bookingDetails = [];
if (isset($_GET['success']) && isset($_SESSION['last_booking'])) {
    $lastBooking = $_SESSION['last_booking'];
    
    // Fetch the unit model using the unit_id from the session
    $unit = fetch_one("SELECT make_model FROM units WHERE id=?", [$lastBooking['unit_id']]);
    
    $bookingDetails = [
        'booking_id' => $lastBooking['id'],
        'client_name' => $lastBooking['name'],
        'unit_name' => $unit['make_model'] ?? 'N/A',
        'start_at' => $lastBooking['start_at'],
        'end_at' => $lastBooking['end_at'],
        'status' => $lastBooking['status']
    ];
    
    $showSuccessModal = true;
    
    // Clear the session variable so the modal doesn't show again on refresh
    unset($_SESSION['last_booking']);
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
/* ... (Your existing CSS here) ... */
.btn-custom-add {
    background: linear-gradient(45deg, #198754, #28a745);
    border: none;
    color: #fff;
    font-weight: bold;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.btn-custom-add:hover {
    color: #fff;
    background: linear-gradient(45deg, #28a745, #198754);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px);
}

/* 🔹 New modal styling */
.modal-content.custom-modal {
    border-radius: 1rem;
}
.modal-title.custom-title {
    color: #198754;
    font-weight: bold;
}
.booking-id-highlight {
    font-size: 1.5rem;
    font-weight: bold;
    color: #28a745;
    background-color: #e9f5ee;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    word-wrap: break-word;
    /* New: Use flexbox for alignment */
    display: flex;
    justify-content: space-between;
    align-items: center;
}
/* New CSS for copy button */
.copy-button {
    background-color: transparent;
    border: none;
    color: #28a745;
    transition: color 0.2s ease;
    font-size: 1rem; /* Smaller icon to fit inside */
    margin-left: 1rem;
    cursor: pointer;
}
.copy-button:hover {
    color: #198754;
}
</style>

<div class="container my-4">
    <h1 class="h3 text-center mb-4 fw-bold text-dark">Booking</h1>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center mb-3 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo esc($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div id="availabilityAlert" class="alert alert-warning text-center mb-3 shadow-sm" style="display:none;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> The selected unit is not available for this time window.
    </div>

    <div class="card-form">
        <form method="post" action="../public/handlers/submit_booking.php" class="row g-3" onsubmit="return validateBooking()">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="text" name="client_number" class="form-control" placeholder="e.g. 0917 123 4567" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="client_email">Email</label>
                <input type="email" name="client_email" id="client_email" class="form-control" placeholder="Enter your email address">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Select Unit</label>
                <select name="unit_id" id="unit_id" class="form-select" required>
                    <option value="" disabled selected>Choose a car...</option>
                    <?php
                      foreach($units as $u){
                          echo '<option value="'.esc($u['id']).'">'.esc($u['make_model']).'</option>';
                      }
                    ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Pick-up Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Pick-up Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Return Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold">Return Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Special requests, additional details..."></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-lg w-100 w-md-auto px-5 shadow-sm btn-custom-add mt-2">
                    <i class="bi bi-calendar-check"></i> Book Now
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title custom-title" id="successModalLabel">Booking Confirmed!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <h6 class="mt-3 mb-2 text-muted">Your Booking ID</h6>
                
                <div class="booking-id-highlight mb-4 d-flex justify-content-between align-items-center" id="booking-id-container">
                    <span id="booking-id-text"><?= esc($bookingDetails['booking_id'] ?? '') ?></span>
                    <button class="btn p-0 border-0 ms-auto copy-button" id="copy-button" title="Copy Booking ID">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                
                <div class="text-start">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-fill text-primary me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Client Name</small>
                            <span class="fw-bold fs-6"><?= esc($bookingDetails['client_name'] ?? '') ?></span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-car-front-fill text-primary me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Unit Booked</small>
                            <span class="fw-bold fs-6"><?= esc($bookingDetails['unit_name'] ?? '') ?></span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-calendar-range-fill text-primary me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Date & Time</small>
                            <span class="fw-bold fs-6">
                                <?= esc(date('M j, Y', strtotime($bookingDetails['start_at'] ?? ''))) ?>
                                <span class="text-muted ms-1"><?= esc(date('H:i', strtotime($bookingDetails['start_at'] ?? ''))) ?></span>
                                <i class="bi bi-arrow-right-short mx-1"></i>
                                <?= esc(date('M j, Y', strtotime($bookingDetails['end_at'] ?? ''))) ?>
                                <span class="text-muted ms-1"><?= esc(date('H:i', strtotime($bookingDetails['end_at'] ?? ''))) ?></span>
                            </span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-info-circle-fill text-primary me-3 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-warning text-dark fs-6"><?= esc($bookingDetails['status'] ?? '') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass PHP booking data to a JavaScript variable
    const confirmedBookings = <?php echo json_encode($confirmedBookings); ?>;

    // Get all relevant input elements
    const unitSelect = document.getElementById('unit_id');
    const startDateInput = document.getElementById('start_date');
    const startTimeInput = document.getElementById('start_time');
    const endDateInput = document.getElementById('end_date');
    const endTimeInput = document.getElementById('end_time');
    const availabilityAlert = document.getElementById('availabilityAlert');

    // Add event listeners to trigger validation on changes
    unitSelect.addEventListener('change', checkBookingOverlap);
    startDateInput.addEventListener('change', checkBookingOverlap);
    startTimeInput.addEventListener('change', checkBookingOverlap);
    endDateInput.addEventListener('change', checkBookingOverlap);
    endTimeInput.addEventListener('change', checkBookingOverlap);

    function checkBookingOverlap() {
        const unitId = unitSelect.value;
        const startDate = startDateInput.value;
        const startTime = startTimeInput.value;
        const endDate = endDateInput.value;
        const endTime = endTimeInput.value;

        // Only proceed if all date/time fields are filled
        if (!unitId || !startDate || !startTime || !endDate || !endTime) {
            availabilityAlert.style.display = 'none';
            return;
        }

        const selectedStart = new Date(`${startDate}T${startTime}`);
        const selectedEnd = new Date(`${endDate}T${endTime}`);

        // Check for logical date order
        if (selectedEnd <= selectedStart) {
            availabilityAlert.textContent = '⚠ The return date and time must be after the pick-up date and time.';
            availabilityAlert.style.display = 'block';
            return;
        }

        let isConflict = false;
        for (const booking of confirmedBookings) {
            if (booking.unit_id === unitId) {
                const bookingStart = new Date(booking.start_at);
                const bookingEnd = new Date(booking.end_at);

                // Check for overlap using the standard time window logic
                if (!(bookingEnd <= selectedStart || bookingStart >= selectedEnd)) {
                    isConflict = true;
                    break; // Exit the loop on first conflict
                }
            }
        }

        if (isConflict) {
            availabilityAlert.textContent = '⚠ The selected unit is not available for this time window.';
            availabilityAlert.style.display = 'block';
        } else {
            availabilityAlert.style.display = 'none';
        }
    }

    // Final form submission validation to prevent submission on overlap
    function validateBooking() {
        checkBookingOverlap();
        return availabilityAlert.style.display === 'none';
    }

    // 🔹 JavaScript to show the modal automatically
    <?php if ($showSuccessModal): ?>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('successModal'));
            myModal.show();
        });
    <?php endif; ?>

    // New JavaScript function to handle the copy action
    document.getElementById('copy-button').addEventListener('click', function() {
        const bookingIdText = document.getElementById('booking-id-text').innerText;
        
        // Use the Clipboard API to copy the text
        navigator.clipboard.writeText(bookingIdText).then(() => {
            // Optional: Provide visual feedback
            const copyButton = document.getElementById('copy-button');
            copyButton.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
            setTimeout(() => {
                copyButton.innerHTML = '<i class="bi bi-clipboard"></i>';
            }, 2000); // Change back after 2 seconds
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    });
</script>

<?php 
// This includes the footer, which contains the closing HTML and the Bootstrap JavaScript.
include __DIR__ . '/partials/footer.php';
?>