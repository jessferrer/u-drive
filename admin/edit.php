<?php
ob_start();
require_once __DIR__ . '/auth.php';
include __DIR__ . '/partials.php';

$t   = $_GET['t']   ?? '';
$id  = intval($_GET['id'] ?? 0);
$tab = $_GET['tab'] ?? 'dashboard';

// --- MODIFIED: Added image_url to units and affiliate_id
$meta = [
  'users'      => ['title'=>'User','fields'=>['name','email','phone','role'],'table'=>'users'],
  'affiliates' => ['title'=>'Affiliate','fields'=>['name','contact_email'],'table'=>'affiliates'],
  'clients'    => ['title'=>'Client','fields'=>['name','email','phone'],'table'=>'clients'],
  'units'      => ['title'=>'Unit','fields'=>['make_model','transmission','seats','rate_per_day','status','affiliate_id','image_url'],'table'=>'units'],
  'bookings'   => ['title'=>'Booking','fields'=>['client_name','client_number','unit_id','start_at','end_at','status','notes'],'table'=>'bookings'],
];

if(!isset($meta[$t])) die('Unknown table');
$m = $meta[$t];
$row = fetch_one("SELECT * FROM {$m['table']} WHERE id=?", [$id]);
if(!$row) die('Not found');

$alert = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
  // --- NEW: Handle file upload for units table
  $imageUrl = null;
  if ($t === 'units' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $image = $_FILES['image'];
      $upload_dir = __DIR__ . '/../public/assets/images/';
      $filename = str_replace(' ', '-', strtolower($_POST['make_model']));
      $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
      $target_file = $upload_dir . $filename . '.' . $file_extension;
      
      if (move_uploaded_file($image['tmp_name'], $target_file)) {
          $imageUrl = 'assets/images/' . $filename . '.' . $file_extension;
      } else {
          $_SESSION['error'] = "Failed to upload new image.";
          header("Location: ?t=$t&id=$id&tab=$tab");
          exit;
      }
  }

  if ($t === 'bookings') {
    $unitId       = intval($_POST['unit_id'] ?? 0);
    $start_at     = ($_POST['start_date'] ?? '') . ' ' . ($_POST['start_time'] ?? '');
    $end_at       = ($_POST['end_date'] ?? '') . ' ' . ($_POST['end_time'] ?? '');
    $status       = $_POST['status'] ?? '';
    $clientNumber = $_POST['client_number'] ?? '';
    $oldStatus    = $row['status'] ?? '';

    // Check date overlap
    if ($unitId && $start_at && $end_at) {
      $conflict = fetch_one(
        "SELECT id FROM bookings
         WHERE unit_id=? AND id<>?
         AND status IN ('pending','confirmed')
         AND NOT (end_at <= ? OR start_at >= ?)",
        [$unitId, $id, $start_at, $end_at]
      );
      if ($conflict) {
        $alert = "<div class='alert alert-danger mt-3'>⚠ This unit is already booked for the selected period.</div>";
      }
    }

    if (!$alert) {
      // Update booking data
      $sets = [];
      $params = [];
      foreach($m['fields'] as $f){
        if ($f === 'start_at') {
            $sets[] = "$f=?";
            $params[] = $start_at;
        } elseif ($f === 'end_at') {
            $sets[] = "$f=?";
            $params[] = $end_at;
        } else {
            $sets[] = "$f=?";
            $params[] = $_POST[$f] ?? '';
        }
      }
      $params[] = $id;
      exec_stmt("UPDATE {$m['table']} SET ".implode(',', $sets)." WHERE id=?", $params);

      // Increment client booking_count only if status changed to confirmed
      if ($status === 'confirmed' && $oldStatus !== 'confirmed' && $clientNumber) {
        exec_stmt("UPDATE clients SET booking_count = booking_count + 1 WHERE phone=?", [$clientNumber]);
      }

      // Free unit if canceled or completed
      if ($unitId > 0 && in_array($status, ['completed','canceled'])) {
        exec_stmt("UPDATE units SET status='available', booked_until=NULL WHERE id=?", [$unitId]);
      }

      header("Location: index.php?tab=" . urlencode($tab));
      exit;
    }
  } else {
      // Generic update for other tables
      $sets = [];
      $params = [];
      foreach($m['fields'] as $f){
          // --- NEW: Handle image_url field
          if ($f === 'image_url') {
              if ($imageUrl !== null) {
                  $sets[] = "$f=?";
                  $params[] = $imageUrl;
              }
          } else {
              $sets[] = "$f=?";
              $params[] = $_POST[$f] ?? '';
          }
      }

      $params[] = $id;
      exec_stmt("UPDATE {$m['table']} SET ".implode(',', $sets)." WHERE id=?", $params);
      header("Location: index.php?tab=" . urlencode($tab));
      exit;
  }
}

// Back button link
$backUrl = "index.php?tab=" . urlencode($tab);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<div class="card shadow-sm">
  <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Edit <?php echo esc($m['title']); ?></h5>
    <a href="<?php echo esc($backUrl); ?>" class="btn btn-outline-light btn-md">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>
  <div class="card-body">
    <?php echo $alert; ?>
    <form method="post" class="row g-3" enctype="multipart/form-data">
      <?php foreach($m['fields'] as $f): ?>
        <div class="col-12 col-md-6">
          <label class="form-label"><?php echo esc(ucwords(str_replace('_',' ', $f))); ?></label>

          <?php if ($t === 'bookings' && $f === 'status'): ?>
            <select name="status" class="form-select">
              <?php foreach (['pending','confirmed','completed','canceled'] as $s): ?>
                <option value="<?php echo esc($s); ?>" <?php if($row[$f]===$s) echo "selected"; ?>><?php echo ucfirst($s); ?></option>
              <?php endforeach; ?>
            </select>

          <?php elseif ($t === 'bookings' && $f === 'unit_id'): ?>
            <select name="unit_id" class="form-select">
              <?php
              $units = fetch_all("SELECT id, make_model, status FROM units WHERE status='available' OR id=? ORDER BY make_model", [$row[$f]]);
              foreach ($units as $u): ?>
                <option value="<?php echo esc($u['id']); ?>" <?php if($row[$f]==$u['id']) echo "selected"; ?>>
                  <?php echo esc($u['make_model']); ?> (<?php echo esc($u['status']); ?>)
                </option>
              <?php endforeach; ?>
            </select>

          <?php elseif ($t === 'bookings' && $f === 'start_at'): ?>
            <div class="row g-2">
              <div class="col"><input type="date" class="form-control" name="start_date" value="<?php echo esc(substr($row[$f],0,10)); ?>" required></div>
              <div class="col"><input type="time" class="form-control" name="start_time" value="<?php echo esc(substr($row[$f],11,5)); ?>" required></div>
            </div>

          <?php elseif ($t === 'bookings' && $f === 'end_at'): ?>
            <div class="row g-2">
              <div class="col"><input type="date" class="form-control" name="end_date" value="<?php echo esc(substr($row[$f],0,10)); ?>" required></div>
              <div class="col"><input type="time" class="form-control" name="end_time" value="<?php echo esc(substr($row[$f],11,5)); ?>" required></div>
            </div>

          <?php elseif ($t === 'bookings' && $f === 'notes'): ?>
            <textarea class="form-control" name="notes" rows="3"><?php echo esc($row[$f]); ?></textarea>
          
          <?php elseif ($t === 'units' && $f === 'affiliate_id'): ?>
            <select name="affiliate_id" class="form-select">
                <option value="">Select Affiliate</option>
                <?php $affiliates = fetch_all("SELECT id, name FROM affiliates ORDER BY name ASC");
                foreach ($affiliates as $aff): ?>
                    <option value="<?php echo esc($aff['id']); ?>" <?php if($row['affiliate_id'] == $aff['id']) echo "selected"; ?>>
                        <?php echo esc($aff['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
          
          <?php elseif ($t === 'units' && $f === 'image_url'): ?>
            <input type="file" class="form-control" name="image" accept="image/*">
            <?php if (!empty($row[$f])): ?>
                <div class="mt-2 text-center">
                    <p class="mb-1 small text-muted">Current Image:</p>
                    <img src="../public/<?php echo esc($row[$f]); ?>" alt="Current Unit Image" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                </div>
            <?php endif; ?>
          
          <?php else: ?>
            <input type="text" class="form-control" name="<?php echo esc($f); ?>" value="<?php echo esc($row[$f]); ?>">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <div class="col-12 text-end">
        <button class="btn btn-lg w-100 rounded-pill btn-custom-add">
          <i class="bi bi-save small"></i> Save
        </button>
      </div>
    </form>
  </div>
</div>

<style>
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
    box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}
</style>

<?php
ob_end_flush();
include __DIR__ . '/footer.php';
?>