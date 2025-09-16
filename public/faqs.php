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

.accordion-item {
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    overflow: hidden;
    transition: box-shadow 0.2s ease-in-out;
}

.accordion-item:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

.accordion-button:not(.collapsed) {
    color: #fff;
    background-color: #198754;
    border-color: #198754;
}

.accordion-button:not(.collapsed)::after {
    filter: brightness(0) invert(1);
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

.contact-card {
    background-color: #e9f5ee;
    border: 1px solid #c8e6c9;
}
</style>

<div class="container my-4">
  <h1 class="h3 text-center mb-4 fw-bold text-dark">Frequently Asked Questions</h1>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <h5 class="mb-0 fw-bold">What documents are required to rent a car?</h5>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You need a **valid driver’s license** and at least one **government-issued ID** (e.g., passport, national ID, SSS, or company ID).
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <h5 class="mb-0 fw-bold">Is there a security deposit required?</h5>
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, a refundable security deposit is required before the start of the rental. It will be returned in full once the unit is safely returned without damages or violations.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <h5 class="mb-0 fw-bold">What is the minimum age requirement?</h5>
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Renters must be at least **21 years old** and hold a valid driver’s license for at least 1 year. Additional fees may apply for drivers under 25.
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            <h5 class="mb-0 fw-bold">What payment methods do you accept?</h5>
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We accept cash, bank transfers, GCash, and major credit/debit cards. Payment must be completed before the release of the vehicle.
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            <h5 class="mb-0 fw-bold">Is there a mileage limit?</h5>
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Most rentals include unlimited mileage within the region. For long-distance or out-of-town trips, please confirm with our staff before booking.
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            <h5 class="mb-0 fw-bold">What is your fuel policy?</h5>
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Cars are rented out with a full tank and must be returned with a full tank. Otherwise, fuel charges will apply.
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            <h5 class="mb-0 fw-bold">What should I do in case of an accident or breakdown?</h5>
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Contact our 24/7 support hotline immediately. For accidents, report to the nearest police station and obtain a police report. Repairs must only be done through our authorized service centers.
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                            <h5 class="mb-0 fw-bold">What happens if I return the car late?</h5>
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            A grace period of 1 hour is allowed. Beyond that, additional hourly charges apply. After 4 hours late, a full day rate will be charged.
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                            <h5 class="mb-0 fw-bold">Can I cancel or change my booking?</h5>
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, bookings can be changed or canceled at least 24 hours before pick-up time. Late cancellations may incur charges.
                        </div>
                    </div>
                </div>

                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="headingTen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                            <h5 class="mb-0 fw-bold">Do you offer car delivery or pick-up service?</h5>
                        </button>
                    </h2>
                    <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, we can deliver or pick up the car at your location for an additional fee depending on the distance.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-4 mt-5 text-center contact-card">
                <h5 class="fw-bold mb-3 text-dark">Can't find your answer?</h5>
                <p class="text-muted mb-4">
                    If you have a question that isn't covered in our FAQs, please don't hesitate to reach out to our team.
                </p>
                <a href="tel:+639457626944" class="btn btn-lg btn-custom-add rounded-pill px-5">
                    <i class="bi bi-chat-dots me-2"></i> Contact Us
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>