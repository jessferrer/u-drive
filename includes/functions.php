<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/db.php';

function redirect($path) {
    header("Location: {$path}"); exit;
}

function esc($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

function is_logged_in() { return isset($_SESSION['user']); }
function current_user() { return $_SESSION['user'] ?? null; }

function require_login() {
    if (!is_logged_in()) { redirect('login.php'); }
}

function has_role($role) {
    $u = current_user();
    return $u && isset($u['role']) && $u['role'] === $role;
}

// Simple query helpers
function fetch_all($sql, $params = [], $types = ''){
    global $mysqli;
    $stmt = $mysqli->prepare($sql);
    if(!$stmt){ die('SQL error: ' . $mysqli->error); }
    if($params){
        if(!$types){ // infer simple types (s for string / i for int) 
            $types = '';
            foreach($params as $p){ $types .= is_int($p) ? 'i' : 's'; }
        }
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}

function fetch_one($sql, $params = [], $types = ''){
    $rows = fetch_all($sql, $params, $types);
    return $rows ? $rows[0] : null;
}

function exec_stmt($sql, $params = [], $types = ''){
    global $mysqli;
    $stmt = $mysqli->prepare($sql);
    if(!$stmt){ die('SQL error: ' . $mysqli->error); }
    if($params){
        if(!$types){
            $types = '';
            foreach($params as $p){ $types .= is_int($p) ? 'i' : 's'; }
        }
        $stmt->bind_param($types, ...$params);
    }
    $ok = $stmt->execute();
    if(!$ok){ die('SQL exec error: ' . $stmt->error); }
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

function active_page($name){
    $p = $_GET['p'] ?? 'home';
    return $p === $name ? 'active' : '';
}

function generate_short_id($prefix = 'UD-', $length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charLength = strlen($characters);
    do {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charLength - 1)];
        }
        $new_id = $prefix . $randomString;
        // Check if this ID already exists in the database
        $conflict = fetch_one("SELECT booking_id FROM bookings WHERE booking_id=?", [$new_id]);
    } while ($conflict); // Repeat if a conflict is found

    return $new_id;
}

?>

