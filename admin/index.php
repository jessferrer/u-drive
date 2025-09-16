<?php
ob_start();
require_once __DIR__ . '/auth.php';
include __DIR__ . '/partials.php';

exec_stmt("
    UPDATE bookings
    SET status = 'completed'
    WHERE status = 'confirmed' AND end_at < NOW()
");

$tab = $_GET['tab'] ?? 'dashboard';

$tabMap = [
    'Dashboard' => 'dashboard',
    'Bookings' => 'bookings',
    'Units' => 'units',
    'Clients' => 'clients',
    'Affiliates' => 'affiliates'
];

if (has_role('admin')) {
    $tabMap['Employees'] = 'employees';
    $tabMap['Administrators'] = 'admins';
}

$icons = [
    'Dashboard' => 'bi-speedometer2',
    'Bookings' => 'bi-calendar-check',
    'Units' => 'bi-car-front',
    'Clients' => 'bi-person',
    'Affiliates' => 'bi-building',
    'Employees' => 'bi-people',
    'Administrators' => 'bi-shield-lock'
];

$counts = [
    'Bookings' => fetch_one("SELECT COUNT(*) c FROM bookings")['c'] ?? 0,
    'Units' => fetch_one("SELECT COUNT(*) c FROM units")['c'] ?? 0,
    'Clients' => fetch_one("SELECT COUNT(*) c FROM clients")['c'] ?? 0,
    'Affiliates' => fetch_one("SELECT COUNT(*) c FROM affiliates")['c'] ?? 0
];

if (has_role('admin')) {
    $counts['Employees'] = fetch_one("SELECT COUNT(*) c FROM users WHERE role='employee'")['c'] ?? 0;
    $counts['Administrators'] = fetch_one("SELECT COUNT(*) c FROM users WHERE role='admin'")['c'] ?? 0;
}

$adminOnly = ['admins','employees'];

/** --- Handle POST --- **/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['__table'])) {
    $table = $_POST['__table'];
    $tabRedirect = $_GET['tab'] ?? 'dashboard';

    if (in_array($table, $adminOnly) && !has_role('admin')) {
        $_SESSION['error'] = "You don’t have permission to add data here.";
    } else {
        // --- START NEW/MODIFIED LOGIC ---
        // Handle file upload for units table
        $imageUrl = null;
        if ($table === 'units' && isset($_FILES['image'])) {
            $image = $_FILES['image'];
            if ($image['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../public/assets/images/';
                $filename = str_replace(' ', '-', strtolower($_POST['make_model']));
                $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                $target_file = $upload_dir . $filename . '.' . $file_extension;
                
                if (move_uploaded_file($image['tmp_name'], $target_file)) {
                    $imageUrl = 'assets/images/' . $filename . '.' . $file_extension;
                } else {
                    $_SESSION['error'] = "Failed to upload image.";
                    header("Location: ?tab=" . urlencode($tabRedirect));
                    exit;
                }
            }
        }
        // --- END NEW/MODIFIED LOGIC ---

        if ($table === 'users' && isset($_POST['password'])) {
            exec_stmt(
                "INSERT INTO users(name,email,phone,password_hash,role) VALUES(?,?,?,?,?)",
                [$_POST['name'], $_POST['email'], $_POST['phone'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['role']]
            );
        } else {
            // Update fields based on table type
            $fieldsToInsert = array_keys($_POST);
            // --- MODIFIED LOGIC: Explicitly handle 'units' table for image_url ---
            if ($table === 'units') {
                $fieldsToInsert = ['make_model','transmission','seats','rate_per_day','status','affiliate_id', 'image_url'];
                $_POST['image_url'] = $imageUrl;
            }
            
            // --- NEW LOGIC: Correctly gather values for insertion ---
            $fields = array_filter($fieldsToInsert, fn($f) => !in_array($f, ['__table', 'image']));
            $values = array_map(fn($f) => $_POST[$f] ?? null, $fields);
            
            $cols = implode(',', $fields);
            $qs = implode(',', array_fill(0, count($fields), '?'));
            
            exec_stmt("INSERT INTO $table($cols) VALUES($qs)", $values);
        }
        header("Location: ?tab=" . urlencode($tabRedirect));
        exit;
    }
}

/** --- CRUD Table Function --- **/
function crud_table($title, $table, $fields, $labels, $selectSql, $formDefaults = [], $tab = 'dashboard', $pagination = false) {
    $allowAdd = !in_array($table, ['admins','employees']) || has_role('admin');
    
    // Pagination logic
    $itemsPerPage = 10;
    $totalRows = fetch_one("SELECT COUNT(*) c FROM ($selectSql) a")['c'];
    $totalPages = ceil($totalRows / $itemsPerPage);
    $currentPage = intval($_GET['page'] ?? 1);
    $offset = ($currentPage - 1) * $itemsPerPage;

    $paginatedSql = $selectSql . " LIMIT $itemsPerPage OFFSET $offset";

    echo "<div class='content-section mb-4'>";
    echo "<div class='card p-3 shadow-sm border-0 rounded-4 mb-3'><h2 class='h4 fw-bold mb-3'>".esc($title)."</h2>";

    if ($allowAdd) {
        // --- MODIFIED FORM TAG TO SUPPORT FILE UPLOADS ---
        $form_enctype = ($table === 'units') ? 'enctype="multipart/form-data"' : '';
        echo "<form method='post' $form_enctype class='row g-3'>
                <input type='hidden' name='__table' value='".esc($table)."'>";
        
        echo "<div class='row g-3'>";
        foreach ($fields as $f) {
            if ($f === 'id') continue;
            
            $val = esc($_POST[$f] ?? $formDefaults[$f] ?? '');
            
            // 🔹 Corrected logic to handle form labels separately
            $form_label = ($f === 'affiliate_id') ? 'Affiliate' : ($labels[$f] ?? ucfirst($f));
            echo "<div class='col-md-4'>
                    <label class='form-label small text-muted'>".esc($form_label)."</label>";
            
            if (str_contains($f,'password')) {
                echo "<input type='password' name='$f' class='form-control'>";
            } elseif ($f==='email') {
                echo "<input type='email' name='$f' class='form-control' value='$val'>";
            } elseif ($f==='role') {
                echo "<select name='role' class='form-select'><option value='admin'>Admin</option><option value='employee'>Employee</option></select>";
            } elseif ($f==='status') {
                echo "<select name='status' class='form-select'><option value='available'>Available</option><option value='booked'>Booked</option></select>";
            } elseif ($f==='affiliate_id') {
                $affiliates = fetch_all("SELECT id, name FROM affiliates ORDER BY name ASC");
                echo "<select name='affiliate_id' class='form-select'>";
                echo "<option value=''>Select Affiliate</option>";
                foreach ($affiliates as $aff) {
                    echo "<option value='".esc($aff['id'])."'>".esc($aff['name'])."</option>";
                }
                echo "</select>";
            // --- NEW: RENDER FILE INPUT FOR IMAGE UPLOAD ---
            } elseif ($f==='image') {
                echo "<input type='file' name='image' class='form-control' accept='image/*'>";
            } else {
                echo "<input type='text' name='$f' class='form-control' value='$val'>";
            }
            echo "</div>";
        }
        echo "</div>";

        echo "<div class='col-12 mt-4'><button class='btn btn-lg w-100 rounded-pill btn-custom-add'>+ Add</button></div>
              </form>";
    }
    echo "</div>";

    $rows = fetch_all($pagination ? $paginatedSql : $selectSql);

    echo "<div class='d-flex justify-content-end align-items-center mb-2 py-2'>
              <input type='text' id='search_$table' class='form-control w-100 rounded-pill' placeholder='🔍 Search...' oninput='filterTable(\"$table\")'>
          </div>";

    echo "<div class='table-responsive shadow-sm rounded-2'>
              <table id='table_$table' class='table table-striped table-hover align-middle border mb-0 table-lg'>
                <thead class='table-success'><tr>";
    foreach ($labels as $lab) echo "<th class='small fw-bold'>".esc($lab)."</th>";
    echo "<th class='small fw-bold text-center'>Actions</th></tr></thead><tbody>";
    foreach ($rows as $r) {
        echo "<tr>";
        foreach (array_keys($labels) as $f) {
            // --- MODIFIED: DISPLAY IMAGE FOR 'image' FIELD ---
            if ($table === 'units' && $f === 'image') {
                $imgSrc = !empty($r['image_url']) ? "../public/" . esc($r['image_url']) : '';
                echo "<td class='text-center'>";
                if ($imgSrc) {
                    echo "<img src='$imgSrc' alt='Unit Image' style='max-width: 80px; height: auto; border-radius: 4px;'>";
                } else {
                    echo "No Image";
                }
                echo "</td>";
            } else {
                $displayValue = ($f === 'affiliate_name' && empty($r[$f])) ? 'N/A' : esc($r[$f] ?? '');
                echo "<td class='text-truncate' style='max-width:180px;' title='".esc($r[$f]??'')."'>".esc($displayValue)."</td>";
            }
        }
        echo "<td class='text-nowrap text-center'>
                    <div class='d-flex justify-content-center gap-1'>
                      <a class='btn btn-light btn-sm rounded-circle shadow-sm' href='edit.php?t=$table&id=".$r['id']."&tab=$tab' title='Edit'><i class='bi bi-pencil text-primary'></i></a>
                      <a class='btn btn-light btn-sm rounded-circle shadow-sm' href='remove.php?t=$table&id=".$r['id']."' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='bi bi-trash text-danger'></i></a>
                    </div>
                  </td></tr>";
    }
    echo "</tbody></table></div>";
    
    if ($pagination && $totalPages > 1) {
        echo "<nav class='mt-3'><ul class='pagination justify-content-center'>";
        
        $params = $_GET;
        unset($params['page']);
        $queryString = http_build_query($params);

        $prevPage = max(1, $currentPage - 1);
        echo "<li class='page-item " . ($currentPage === 1 ? 'disabled' : '') . "'><a class='page-link' href='?$queryString&page=$prevPage' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";

        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<li class='page-item " . ($i === $currentPage ? 'active' : '') . "'><a class='page-link' href='?$queryString&page=$i'>$i</a></li>";
        }

        $nextPage = min($totalPages, $currentPage + 1);
        echo "<li class='page-item " . ($currentPage === $totalPages ? 'disabled' : '') . "'><a class='page-link' href='?$queryString&page=$nextPage' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";

        echo "</ul></nav>";
    }
    
    echo "</div>";
}

/** --- Render Tabs --- **/
switch ($tab) {
    case 'dashboard':
        echo "<div class='row pt-2 g-4 mb-4'>";

        foreach (['Bookings','Units','Clients','Affiliates'] as $name) {
            $t = strtolower($name);
            echo "<div class='col-6 col-md-3'>
                        <a href='?tab=$t' class='text-decoration-none'>
                          <div class='card shadow-sm border-0 p-4 text-center hover-scale'>
                            <i class='bi {$icons[$name]} fs-1 text-success mb-2'></i>
                            <h5 class='fw-bold mb-0'>$name</h5>
                            <p class='text-muted mb-0'>{$counts[$name]} total</p>
                          </div>
                        </a>
                      </div>";
        }
        echo "</div>";

        echo "<div class='card shadow-sm border-0 p-4'>
                <h5 class='fw-bold mb-3'>Recent Bookings</h5>";
        $recent = fetch_all("
            SELECT b.*, u.make_model, u.image_url 
            FROM bookings b 
            LEFT JOIN units u ON u.id=b.unit_id 
            ORDER BY b.id DESC LIMIT 5
        ");
        if ($recent) {
            echo "<ul class='list-group list-group-flush'>";
            foreach ($recent as $r) {
                $badge = $r['status'] === 'confirmed' ? 'bg-success' : ($r['status'] === 'pending' ? 'bg-warning text-dark' : ($r['status'] === 'cancelled' ? 'bg-danger' : 'bg-secondary'));
                $imgSrc = !empty($r['image_url']) ? "../public/" . esc($r['image_url']) : '../public/assets/images/no-image.webp';
                echo "<li class='list-group-item d-flex justify-content-between align-items-center px-0 border-0'>
                    <a href='edit.php?t=bookings&id=".esc($r['id'])."&tab=bookings'>
                    <div class='d-flex align-items-center'>
                        <img src='".esc($imgSrc)."' alt='Unit Image' style='width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 1rem;'>
                        <div>
                            <span class='fw-semibold text-dark'>".esc($r['client_name'])."</span>
                            <div class='text-muted small'>".esc($r['make_model'])."</div>
                            <div class='text-muted small'>Return: ".esc(date('M j, Y', strtotime($r['end_at'])))."</div>
                        </div>
                    </div>
                    <div class='d-flex flex-column align-items-end'>
                        <a href='edit.php?t=bookings&id=".esc($r['id'])."&tab=bookings'>
                            <span class='badge {$badge} mb-1'>".ucfirst($r['status'])."</span>
                        </a>
                    </div>
                </li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='text-muted mb-0'>No recent bookings.</p>";
        }
        echo "</div>";
        break;

    case 'bookings':
        crud_table('Bookings','bookings',['client_name','client_number','make_model','start_at','end_at','status'],
          ['id'=>'ID','client_name'=>'Client','client_number'=>'Phone','make_model'=>'Unit','start_at'=>'Start','end_at'=>'End','status'=>'Status'],
          "SELECT b.id,b.client_name,b.client_number,u.make_model,b.start_at,b.end_at,b.status FROM bookings b LEFT JOIN units u ON u.id=b.unit_id ORDER BY b.id ASC",[],$tab,true);
        break;
    case 'admins':
        if (has_role('admin')) {
            crud_table('Administrators','users',['name','email','password','role'],
              ['id'=>'ID','name'=>'Name','email'=>'Email','role'=>'Role'],
              "SELECT id,name,email,role FROM users WHERE role='admin' ORDER BY id ASC",['role'=>'admin'],$tab);
        }
        break;
    case 'employees':
        if (has_role('admin')) {
            crud_table('Employees','users',['name','email','phone','password','role'],
              ['id'=>'ID','name'=>'Name','email'=>'Email','phone'=>'Phone','role'=>'Role'],
              "SELECT id,name,email,phone,role FROM users WHERE role='employee' ORDER BY id ASC",['role'=>'employee'],$tab);
        }
        break;
    case 'affiliates':
        crud_table(
          'Affiliates',
          'affiliates',
          ['name','number','address'],
          ['id'=>'ID','name'=>'Name','number'=>'Phone','address'=>'Address','owned_units'=>'Owned Units'],
          "SELECT a.id,a.name,a.number,a.address,COUNT(u.id) AS owned_units FROM affiliates a LEFT JOIN units u ON u.affiliate_id=a.id GROUP BY a.id ORDER BY a.id ASC",[],$tab,true);
        break;
    case 'clients':
        crud_table(
          'Clients',
          'clients',
          ['name','email','phone'],
          ['id'=>'ID','name'=>'Name','email'=>'Email','phone'=>'Phone','booking_count'=>'Bookings'],
          "SELECT id,name,email,phone,booking_count FROM clients ORDER BY id ASC",[],$tab,true);
        break;
    case 'units':
        crud_table(
        'Units',
        'units',
        ['make_model','transmission','seats','rate_per_day','status','affiliate_id','image'],
        ['id'=>'ID','make_model'=>'Make & Model','transmission'=>'Transmission','seats'=>'Seats','rate_per_day'=>'Rate/Day','status'=>'Status','affiliate_name'=>'Affiliate','image'=>'Image'],
        "SELECT u.id,u.make_model,u.transmission,u.seats,u.rate_per_day,u.status,a.name AS affiliate_name, u.image_url FROM units u LEFT JOIN affiliates a ON u.affiliate_id=a.id ORDER BY u.id ASC",[],$tab,true);
        break;
}
?>

<script>
function filterTable(table){
    let filter=document.getElementById('search_'+table)?.value.toLowerCase() || '';
    document.querySelectorAll('#table_'+table+' tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(filter)?'':'none');
}
</script>
<style>
/* CSS for the Add button */
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

/* CSS for hover effect on dashboard cards */
.hover-scale:hover {
    transform:translateY(-4px);
    box-shadow:0 6px 16px rgba(0,0,0,0.15);
    transition:transform .2s,box-shadow .2s;
}

/* CSS for bigger table and custom pagination colors */
.table-lg tr th, .table-lg tr td {
    font-size: 1rem; /* Adjust font size */
    padding: 1rem; /* Increase padding */
}

.pagination a.page-link, .pagination a.page-link span {
    color: #006639 !important;
    border-color: #006639 !important;
}

.pagination li.active a.page-link {
    background-color: #006639 !important;
    color: white !important;
    border-color: #006639 !important;
}

.pagination a.page-link:hover {
    background-color: #0066391a; /* A light green color on hover */
}
</style>

<?php include __DIR__.'/footer.php'; ?>
<?php ob_end_flush(); ?>