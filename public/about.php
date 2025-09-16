<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
/* Custom CSS to match the style of index.php */
.hero-section {
    background-color: #f8f9fa; /* Light gray background */
    padding: 6rem 0;
    text-align: center;
}

.hero-section h1 {
    font-size: 3rem;
    font-weight: 700;
}

.hero-section p {
    font-size: 1.25rem;
    color: #6c757d;
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
    margin: 0 auto 1.5rem;
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

.btn-custom-add {
    background: linear-gradient(45deg, #198754, #28a745);
    border: none;
    color: #fff;
    font-weight: bold;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.btn-custom-add:hover {
    background: linear-gradient(45deg, #28a745, #198754);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px);
}
</style>

<div class="container my-4">
  <h1 class="h3 text-center mb-4 fw-bold text-dark">About Us</h1>
    <div class="row">
        <div class="col-lg-8 mx-auto text-center">
            <p class="lead text-muted mb-5">
                At <strong>U-Drive</strong>, our journey began with a simple idea: to provide a hassle-free car rental experience. We believe that getting to your destination should be as enjoyable as the journey itself. Our mission is to offer a fleet of meticulously maintained vehicles, transparent pricing, and unparalleled customer support, ensuring every trip is a memorable one.
            </p>
        </div>
    </div>
    
    <div class="row g-4 text-center mt-4">
        <div class="col-md-4">
            <div class="card card-feature p-4 h-100">
                <div class="icon-circle"><i class="bi bi-car-front"></i></div>
                <h5 class="fw-bold mb-2">Our Fleet</h5>
                <p class="text-muted mb-0">
                    Explore our diverse fleet, from fuel-efficient compacts for city travel to spacious SUVs and vans for family adventures.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-feature p-4 h-100">
                <div class="icon-circle"><i class="bi bi-shield-check"></i></div>
                <h5 class="fw-bold mb-2">Safety & Reliability</h5>
                <p class="text-muted mb-0">
                    Your safety is our top priority. Every vehicle undergoes a rigorous 20-point inspection before each rental, giving you peace of mind on the road.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-feature p-4 h-100">
                <div class="icon-circle"><i class="bi bi-headset"></i></div>
                <h5 class="fw-bold mb-2">Customer Care</h5>
                <p class="text-muted mb-0">
                    Our dedicated support team is available 24/7 to assist with bookings, road-side assistance, or any questions you may have.
                </p>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5 pt-3">
        <a href="booking.php?p=booking" class="btn btn-md btn-success shadow-sm px-5 rounded-pill">
            <i class="bi bi-calendar-check me-2"></i> Start Your Journey Today
        </a>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>