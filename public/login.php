<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<?php 
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
} 
?>
<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="assets/css/styles.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'U-Drive'; ?></title>
    <link rel="icon" type="image/x-icon" href="../public/assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
  <link rel="stylesheet" href="assets/combined.css">
  <style>
    .login-container-offset {
      margin-top: -40px;
    }
  </style>
</head>
  <body>
    
    <div class="d-flex justify-content-center align-items-center vh-100 flex-column px-3 login-container-offset">
      <a href="index.php">
        <img src="../public/assets/logo.png" alt="U-Drive Logo" style="max-width:240px;" class="mb-4 img-fluid" loading="lazy">
      </a>
      
      <div class="login-card">  
        <h1 class="h4 text-center mb-4 text-dark"><i class="bi bi-box-arrow-in-right me-1"></i> Login</h1>

        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="post" action="../includes/auth/login.php" class="d-grid gap-3">
          <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div>
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-login btn-lg bg-success text-white w-100 mt-2"><i class="bi bi-box-arrow-in-right me-1"></i> Login</button>
        </form>
      </div>
    </div>

    <?php include __DIR__ . '../../admin/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="assets/combined.js" defer></script>
</body>
</html>