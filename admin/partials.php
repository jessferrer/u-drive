<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($tab) && $tab !== 'dashboard' ? ucfirst($tab) . ' · ' : ''; ?><?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/x-icon" href="../public/assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
  .navbar {
    background: #006639 !important;
  }
  .navbar .navbar-brand,
  .navbar .nav-link,
  .navbar span {
    color: #fff !important;
  }
  .navbar-toggler {
    border: none;
    color: #fff !important;
    padding: 2px 8px !important;
  }
  .navbar-toggler:focus {
    box-shadow: none;
  }

  /* White menu icon for toggler */
  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='white' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    width: 25px !important;
    height: 27px;
  }

  .sidebar { min-height: 100vh; background: #f8f9fa; }
  .sidebar .nav-link.active { background: rgba(0,102,57,.1); font-weight: 600; }

  /* Modern Offcanvas Menu */
  .offcanvas {
    width: 270px;
    background: #fff;
  }
  .offcanvas-header {
    border-bottom: 1px solid #e9ecef;
  }
  .offcanvas-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #006639; /* Keep title visible */
  }
  .offcanvas-body .nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    font-size: 1rem;
    border-radius: 6px;
    margin-bottom: 4px;
    transition: background 0.2s;
  }
  .offcanvas-body .nav-link i {
    font-size: 1.2rem;
  }
  .offcanvas-body .nav-link:hover {
    background: rgba(0,102,57,.08);
  }
  .offcanvas-body .nav-link.active {
    background: rgba(0,102,57,.15);
    font-weight: 600;
  }

  @media (max-width: 991.98px) { 
    .sidebar { display: none; }
  }
  @media (min-width: 992px) { 
    .offcanvas { display: none !important; }
    .navbar-toggler { display: none; }
  }
</style>

  <link rel="stylesheet" href="assets/combined.css">
</head>
  <body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg sticky-top">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold" href="index.php">
          <div class="px-2">
            <img src="../public/assets/logo.webp" alt="U-Drive Logo" style="height: 30px; width: auto;">
          </div>
        </a>
        <div class="d-flex align-items-center">
          <div class="dropdown me-2">
            <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle fs-4 me-2"></i>
              <span class="d-none d-sm-inline"><?php echo esc(current_user()['name']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
              <li class="px-3 py-2">
                <p class="mb-0 fw-bold"><?= esc(current_user()['name']); ?></p>
                <p class="mb-0 text-muted small"><?= ucfirst(esc(current_user()['role'])); ?></p>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item text-danger fw-bold" href="../includes/auth/logout.php">
                  <i class="bi bi-box-arrow-right me-2"></i>Log Out
                </a>
              </li>
            </ul>
          </div>
          <button class="btn-sm navbar-toggler text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row flex-nowrap">

        <div class="offcanvas offcanvas-start d-lg-none text" tabindex="-1" id="sidebarMenu">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title text-white"><i class="bi bi-list"></i> Menu</h5>
            <button type="button text" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
          <div class="offcanvas-body">
            <?php include __DIR__ . '/sidebar-links.php'; ?>
          </div>
        </div>

        <div class="col-auto col-lg-2 sidebar p-3 border-end d-none d-lg-block">
          <?php include __DIR__ . '/sidebar-links.php'; ?>
        </div>

        <div class="col py-3">