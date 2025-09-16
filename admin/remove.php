<?php require_once __DIR__ . '/auth.php'; ?>
<?php
$t = $_GET['t'] ?? '';
$id = intval($_GET['id'] ?? 0);

$allowed = ['users','affiliates','clients','units','bookings'];
if (!in_array($t, $allowed)) { 
    die('Invalid'); 
}

if ($t === 'bookings') {
    // Get the booking details before deleting
    $booking = fetch_one("SELECT unit_id FROM bookings WHERE id=?", [$id]);

    if ($booking && $booking['unit_id']) {
        // Free the unit linked to this booking
        exec_stmt("UPDATE units SET status='available' WHERE id=?", [$booking['unit_id']]);
    }
}

// Delete the record
exec_stmt("DELETE FROM $t WHERE id=?", [$id]);

// Redirect back with correct tab name
header("Location: index.php?tab=" . ($t === 'users' ? 'employees' : $t));
exit;
