<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="assets/css/styles.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo defined('APP_NAME')?APP_NAME:'U-Drive'; ?></title>
    <link rel="icon" type="image/x-icon" href="../public/assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
  <link rel="stylesheet" href="assets/combined.css">
</head>
  <body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-accent shadow-sm d-none d-lg-block">
      <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="../public/assets/logo.webp" alt="U-Drive Logo" height="30">
        </a>
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item px-1"><a class="nav-link <?php echo active_page('home'); ?>" href="index.php"><i class="bi bi-house-door me-1"></i> Home</a></li>
          <li class="nav-item px-1"><a class="nav-link <?php echo active_page('booking'); ?>" href="booking.php?p=booking"><i class="bi bi-calendar-check me-1"></i> Book</a></li>
          <li class="nav-item px-1"><a class="nav-link <?php echo active_page('units'); ?>" href="units.php?p=units"><i class="bi bi-car-front me-1"></i> Units</a></li>
          <li class="nav-item px-1"><a class="nav-link <?php echo active_page('about'); ?>" href="about.php?p=about"><i class="bi bi-info-circle me-1"></i> About</a></li>
          <li class="nav-item px-1"><a class="nav-link <?php echo active_page('faqs'); ?>" href="faqs.php?p=faqs"><i class="bi bi-question-circle me-1"></i> FAQs</a></li>
        </ul>
        <div class="d-flex gap-2">
          <?php if(isset($_SESSION['user'])): ?>
            <a href="../admin/index.php" class="btn btn-light btn-sm btn-nav"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="../includes/auth/logout.php" class="btn btn-danger btn-sm btn-nav"><i class="bi bi-box-arrow-right"></i> Logout</a>
          <?php else: ?>
            <a href="login.php" class="btn btn-light btn-sm btn-nav"><i class="bi bi-box-arrow-in-right"></i> Login</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <nav class="navbar navbar-accent d-lg-none shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
          <div class="px-2">
            <img src="../public/assets/logo.webp" alt="U-Drive Logo" style="height: 30px; width: auto;">
          </div>
        </a>
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav">
          <i class="bi bi-list fs-3"></i>
        </button>
      </div>
    </nav>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileNav">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title"><i class="bi bi-list"></i> Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="list-unstyled mb-4">
          <li><a class="nav-link mb-2 <?php echo active_page('home'); ?>" href="index.php"><i class="bi bi-house-door me-2"></i> Home</a></li>
          <li><a class="nav-link mb-2 <?php echo active_page('booking'); ?>" href="booking.php?p=booking"><i class="bi bi-calendar-check me-2"></i> Book</a></li>
          <li><a class="nav-link mb-2 <?php echo active_page('units'); ?>" href="units.php?p=units"><i class="bi bi-car-front me-2"></i> Units</a></li>
          <li><a class="nav-link mb-2 <?php echo active_page('about'); ?>" href="about.php?p=about"><i class="bi bi-info-circle me-2"></i> About</a></li>
          <li><a class="nav-link mb-2 <?php echo active_page('faqs'); ?>" href="faqs.php?p=faqs"><i class="bi bi-question-circle me-2"></i> FAQs</a></li>
        </ul>
        <div class="d-grid gap-2">
          <?php if(isset($_SESSION['user'])): ?>
            <a href="../admin/index.php" class="btn btn-light"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="../includes/auth/logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
          <?php else: ?>
            <a href="login.php" class="btn btn-light"><i class="bi bi-box-arrow-in-right"></i> Login</a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <main class="min-vh-100">