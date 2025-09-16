<?php 
require_once __DIR__ . '/../includes/functions.php'; 

// Include the header and other parts of the page.
include __DIR__ . '/partials/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
/* Custom CSS for a better design */
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

.btn-custom-outline {
    color: #198754;
    border-color: #198754;
    font-weight: bold;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.btn-custom-outline:hover {
    background-color: #198754;
    color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.card-feature {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

.card-feature:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.icon-circle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #e9f5ee; /* A lighter shade of green */
    color: #198754; /* Green color */
    font-size: 2rem;
    margin: 0 auto 1rem;
}

/* Carousel fixes and enhancements */
.featured-carousel .carousel-indicators [data-bs-target] {
    background-color: #198754;
}

.featured-carousel .card-img-top {
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
}

/* 🔹 New Modal Styles */
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
}
</style>

<section class="hero d-flex align-items-center text-center bg-white py-5 px-3">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3 text-dark">Awesome Trips Made Easy!</h1>
        <p class="lead text-muted mb-4">Affordable and reliable car rentals tailored for you.</p>
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 w-100">
            <a href="booking.php" class="btn btn-lg w-100 w-md-auto px-5 shadow-sm btn-custom-add">
                <i class="bi bi-calendar-check me-1"></i> Book Now
            </a>
            <button type="button" class="btn btn-lg w-100 w-md-auto px-5 btn-custom-outline" data-bs-toggle="modal" data-bs-target="#checkBookingModal">
                <i class="bi bi-search me-1"></i> Check Booking
            </button>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-md-4">
            <div class="card card-feature h-100 shadow-sm border-0 p-4 text-center">
                <div class="icon-circle"><i class="bi bi-clock-history"></i></div>
                <h3 class="h5 fw-bold mb-2">Flexible Schedules</h3>
                <p class="text-muted mb-0">Pick-up and return at your preferred date & time.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-feature h-100 shadow-sm border-0 p-4 text-center">
                <div class="icon-circle"><i class="bi bi-shield-check"></i></div>
                <h3 class="h5 fw-bold mb-2">Well-Maintained Units</h3>
                <p class="text-muted mb-0">Regularly serviced cars to keep you safe on the road.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card card-feature h-100 shadow-sm border-0 p-4 text-center">
                <div class="icon-circle"><i class="bi bi-headset"></i></div>
                <h3 class="h5 fw-bold mb-2">24/7 Support</h3>
                <p class="text-muted mb-0">Our team is ready to help whenever you need it.</p>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <h2 class="h4 fw-bold mb-4 text-center">
        <i class="bi bi-car-front me-2 text-success"></i> Featured Units
    </h2>
    <?php
    $units = fetch_all("SELECT * FROM units WHERE status='available' ORDER BY make_model LIMIT 6");
    if ($units):
    ?>
    <div id="featuredCarousel" class="carousel slide featured-carousel" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <?php foreach ($units as $index => $u): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                    <div class="d-flex justify-content-center">
                        <div class="col-12 col-md-8 col-lg-6">
                            <div class="card h-100 shadow-sm border-0 p-2" style="border-radius: 14px;">
                                <?php
                                $filename = str_replace(' ', '-', strtolower($u['make_model']));
                                $image_path = 'assets/images/' . $filename . '.webp';
                                ?>
                                <img src="<?= htmlspecialchars($image_path); ?>" class="card-img-top" alt="<?= esc($u['make_model']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-success fw-bold mb-3 text-center">
                                        <?= esc($u['make_model']); ?>
                                    </h5>
                                    <div class="d-flex flex-wrap justify-content-center gap-1 mb-2">
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-gear-fill me-1"></i> <?= esc($u['transmission']); ?>
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-people-fill me-1"></i> <?= esc($u['seats']); ?> seats
                                        </span>
                                    </div>
                                    <p class="mb-1 text-center">Rate:
                                        <strong>₱<?= number_format($u['rate_per_day']); ?></strong>/day
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-0 text-center mb-2">
                                    <a href="booking.php" class="btn btn-outline-success w-100 py-2">
                                        <i class="bi bi-calendar-check me-1"></i> Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center text-muted">
        <p><i class="bi bi-exclamation-circle me-1"></i> No available units at the moment.</p>
    </div>
    <?php endif; ?>
</section>

<div class="modal fade" id="checkBookingModal" tabindex="-1" aria-labelledby="checkBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title custom-title" id="checkBookingModalLabel">Check My Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <form id="bookingForm" class="d-grid gap-3">
                    <div class="form-group">
                        <label for="booking_id" class="form-label">Booking ID</label>
                        <input type="text" name="booking_id" id="booking_id" class="form-control" required>
                    </div>
                    <button type="submit" id="checkButton" class="btn btn-custom-add w-100">Check Status</button>
                </form>
                <div id="bookingResult" class="mt-3">
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookingForm = document.getElementById('bookingForm');
        const bookingIdInput = document.getElementById('booking_id');
        const bookingResultDiv = document.getElementById('bookingResult');

        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const bookingId = bookingIdInput.value.trim();
            if (bookingId === '') {
                return;
            }

            // Show a loading state
            bookingResultDiv.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading details...</p>
                </div>
            `;

            // Use AJAX to check the booking status
            fetch('handlers/check_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `booking_id=${encodeURIComponent(bookingId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Display error message
                    bookingResultDiv.innerHTML = `
                        <div class="alert alert-danger text-center">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.error}
                        </div>
                    `;
                } else {
                    // Display success message with formatted data
                    bookingResultDiv.innerHTML = `
                        <div class="text-start">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-fill text-primary me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Booking ID</small>
                                    <span class="fw-bold fs-6">${data.booking_id}</span>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-car-front-fill text-primary me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Unit Booked</small>
                                    <span class="fw-bold fs-6">${data.unit_name}</span>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar-range-fill text-primary me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Date & Time</small>
                                    <span class="fw-bold fs-6">
                                        ${data.start_date} <span class="text-muted ms-1">${data.start_time}</span>
                                        <i class="bi bi-arrow-right-short mx-1"></i>
                                        ${data.end_date} <span class="text-muted ms-1">${data.end_time}</span>
                                    </span>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-info-circle-fill text-primary me-3 fs-5"></i>
                                <div>
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge ${data.status_class} fs-6">${data.status}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                // Display a generic error
                bookingResultDiv.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> An unexpected error occurred.
                    </div>
                `;
            });
        });
    });
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>