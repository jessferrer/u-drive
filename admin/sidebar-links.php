<style>
  /* ========== Sidebar / Offcanvas Nav (matches screenshot look) ========== */
  .sidebar-nav .nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.9rem 1rem;              /* comfy touch target */
    margin-bottom: 6px;
    color: #198754;                    /* brand green text */
    text-decoration: none;
    border-radius: 12px;               /* pill corners */
    font-size: 1.05rem;
    transition: background .2s ease, transform .15s ease, color .2s ease;
    background: transparent;
    border: 1px solid transparent;     /* allows subtle outline on active */
  }

  .sidebar-nav .nav-link i {
    font-size: 1.1rem;
    margin-right: 6px;
    min-width: 22px;                   /* keep icons aligned */
    text-align: center;
  }

  /* Hover */
  .sidebar-nav .nav-link:hover {
    background: #e9f5ee;               /* very light green */
    color: #006639 !important;
    transform: translateX(4px);
  }

  /* Active (pale green pill like the screenshot) */
  .sidebar-nav .nav-link.active {
    background: #e9f5ee;
    border-color: #d7efe3;
    color: #006639 !important;
    font-weight: 600;
  }
  .sidebar-nav .nav-link.active i { color: #006639; }

  /* Divider */
  .sidebar-nav .divider {
    display: block;
    border-top: 1px solid #dee2e6;
    margin: 1rem 0;
  }

  /* Logout pill (centered, soft background block) */
  .sidebar-nav .logout-pill {
    justify-content: center;
    background: #dc3545;
    border: 1px solid #dc3545;
    color: #ffffff !important;
    font-weight: 500;
  }
  .sidebar-nav .logout-pill:hover {
    background: #f3f4f6;
    border-color: #e6eaed;
    transform: translateY(-1px);
  }

  /* Mobile-friendly sizing */
  @media (max-width: 992px) {
    .sidebar-nav .nav-link {
      font-size: 1.1rem;
      padding: 1rem 1.05rem;
    }
  }
</style>

<nav class="nav flex-column sidebar-nav">
  <a class="nav-link <?= ($_GET['tab']??'')==='dashboard'?'active':''; ?>" href="index.php?tab=dashboard">
    <i class="bi bi-speedometer2"></i>Dashboard
  </a>

  <?php if (has_role('admin')): ?>
    <a class="nav-link <?= ($_GET['tab']??'')==='admins'?'active':''; ?>" href="index.php?tab=admins">
      <i class="bi bi-person-badge"></i>Administrators
    </a>
    <a class="nav-link <?= ($_GET['tab']??'')==='employees'?'active':''; ?>" href="index.php?tab=employees">
      <i class="bi bi-people"></i>Employees
    </a>
  <?php endif; ?>

  <a class="nav-link <?= ($_GET['tab']??'')==='affiliates'?'active':''; ?>" href="index.php?tab=affiliates">
    <i class="bi bi-building"></i>Affiliates
  </a>
  <a class="nav-link <?= ($_GET['tab']??'')==='clients'?'active':''; ?>" href="index.php?tab=clients">
    <i class="bi bi-person-lines-fill"></i>Clients
  </a>
  <a class="nav-link <?= ($_GET['tab']??'')==='units'?'active':''; ?>" href="index.php?tab=units">
    <i class="bi bi-car-front"></i>Units
  </a>
  <a class="nav-link <?= ($_GET['tab']??'')==='bookings'?'active':''; ?>" href="index.php?tab=bookings">
    <i class="bi bi-calendar-check"></i>Bookings
  </a>
</nav>
