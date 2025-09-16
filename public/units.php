<?php
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/partials/header.php';

// Get all units and check if they have a confirmed booking right now
$units = fetch_all("
    SELECT u.*,
    EXISTS (
        SELECT 1
        FROM bookings b
        WHERE b.unit_id = u.id
        AND b.status = 'confirmed'
        AND NOW() BETWEEN b.start_at AND b.end_at
    ) AS is_booked_now
    FROM units u
    ORDER BY u.make_model
");
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<style>
  .unit-card .card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }
  .unit-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
  }
  .unit-card .card-img-top {
    height: 200px;
    object-fit: cover;
    border-radius: 0.5rem 0.5rem 0 0;
  }
  .unit-card .card-body {
    padding: 1rem;
  }
  .unit-card .badge {
    font-weight: 500;
  }
  .unit-card .card-title {
    font-size: 1.25rem;
  }
</style>

<div class="container my-4">
  <h1 class="h3 text-center mb-4 fw-bold text-dark">Available Units</h1>

  <div class="row g-2 filter-bar mb-4">
    <div class="col-12 col-sm-6 col-lg-4">
      <input type="text" id="searchInput" class="form-control" placeholder="Search by model...">
    </div>
    <div class="col-12 col-sm-3 col-lg-4">
      <select id="seatsFilter" class="form-select">
        <option value="">All Seats</option>
        <option value="5">5 Seats</option>
        <option value="7">7 Seats</option>
        <option value="16">16 Seats</option>
      </select>
    </div>
  </div>

  <div class="row g-4" id="unitsContainer">
    <?php
    foreach($units as $u):
      $statusLabel = $u['status'];
      if ($u['is_booked_now'] == 1) {
          $statusLabel = 'booked';
      }
    ?>
      <div class="col-12 col-sm-6 col-lg-4 unit-card" data-seats="<?php echo esc($u['seats']); ?>">
        <div class="card h-100 shadow-sm border-0 rounded-3">
          <img src="<?= htmlspecialchars($u['image_url']); ?>" class="card-img-top" alt="<?= esc($u['make_model']); ?>">
          <div class="card-body d-flex flex-column text-center">
            <h5 class="card-title fw-bold mb-2 text-dark">
              <i class="bi bi-car-front me-1 text-accent"></i> <?php echo esc($u['make_model']); ?>
            </h5>

            <div class="d-flex flex-wrap justify-content-center gap-1 mb-1">
              <span class="badge bg-light text-dark">
                <i class="bi bi-gear-fill me-1"></i> <?php echo esc($u['transmission']); ?>
              </span>
              <span class="badge bg-light text-dark">
                <i class="bi bi-people-fill me-1"></i> <?php echo esc($u['seats']); ?> seats
              </span>
              <span class="badge
                <?php echo esc(
                  ($statusLabel === 'available') ? 'bg-success text-white' :
                  (($statusLabel === 'unavailable') ? 'bg-dark text-white' :
                  (($statusLabel === 'maintenance') ? 'bg-secondary text-white' :
                  (($statusLabel === 'booked') ? 'bg-danger text-white' : 'bg-light text-dark')))); ?>">
                <?php echo esc(ucfirst($statusLabel)); ?>
              </span>
            </div>

            <p class="mb-1">
              <span class="fw-semibold">Rate:</span>
              <strong class="text-dark">₱<?php echo number_format($u['rate_per_day']); ?></strong>/day
            </p>
          </div>

          <div class="card-footer bg-white border-0 mb-2">
            <?php if ($statusLabel === 'available'): ?>
              <a href="booking.php?p=booking&unit_id=<?php echo esc($u['id']); ?>" class="btn btn-accent w-100">
                <i class="bi bi-calendar-check"></i> Book Now
              </a>
            <?php else: ?>
              <button class="btn btn-secondary w-100" disabled>
                <i class="bi bi-x-circle"></i> Not Available
              </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <p id="noResults" class="text-center text-muted" style="display: none;">No results found.</p>
</div>

<script>
  const searchInput = document.getElementById("searchInput");
  const seatsFilter = document.getElementById("seatsFilter");
  const unitCards = document.querySelectorAll(".unit-card");
  const noResults = document.getElementById("noResults");

  function filterUnits() {
    const searchValue = searchInput.value.toLowerCase().trim();
    const seatsValue = seatsFilter.value;
    let visibleCount = 0;

    unitCards.forEach(card => {
      const seats = card.dataset.seats;
      const modelName = card.querySelector(".card-title").textContent.toLowerCase();

      const matchesSearch = modelName.includes(searchValue);
      const matchesSeats = !seatsValue || seats === seatsValue;

      if (matchesSearch && matchesSeats) {
        card.style.display = "";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    noResults.style.display = visibleCount === 0 ? "block" : "none";
  }

  searchInput.addEventListener("input", filterUnits);
  seatsFilter.addEventListener("change", filterUnits);
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>