<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../includes/functions.php';

// The CORRECT sync_booking_with_clients function, now using both email and phone
function sync_booking_with_clients($data) {
    $email = trim($data['client_email'] ?? '');
    $phone = trim($data['client_number'] ?? '');

    // Check if a client with this email OR phone number already exists
    if (!empty($email) || !empty($phone)) {
        $existing = fetch_one("SELECT id, booking_count FROM clients WHERE email=? OR phone=?", [$email, $phone]);

        if ($existing) {
            // Client exists, simply increment their booking count
            $newBookingCount = $existing['booking_count'] + 1;
            exec_stmt("UPDATE clients SET booking_count=? WHERE id=?", [
                $newBookingCount,
                $existing['id']
            ]);
        } else {
            // New client, insert with a booking count of 1
            exec_stmt("INSERT INTO clients(name, email, phone, booking_count) VALUES(?,?,?,?)", [
                $data['full_name'],
                $email,
                $phone,
                1
            ]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['full_name'] ?? '');
    $number   = trim($_POST['client_number'] ?? '');
    $email    = trim($_POST['client_email'] ?? '');
    $unit_id  = intval($_POST['unit_id'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_date   = $_POST['end_date'] ?? '';
    $end_time   = $_POST['end_time'] ?? '';

    $start = $start_date . ' ' . $start_time;
    $end   = $end_date . ' ' . $end_time;
    $notes = trim($_POST['notes'] ?? '');

    // Date Validation
    $start_timestamp = strtotime($start);
    $end_timestamp   = strtotime($end);

    if ($start_timestamp < time()) {
        $_SESSION['error'] = '⚠ The start date and time cannot be in the past.';
        redirect('../booking.php');
        exit;
    }

    if ($end_timestamp <= $start_timestamp) {
        $_SESSION['error'] = '⚠ The return date and time must be after the pickup date and time.';
        redirect('../booking.php');
        exit;
    }

    if (!$name || !$number || !$unit_id) {
        $_SESSION['error'] = '⚠ Missing required fields.';
        redirect('../booking.php');
        exit;
    }

    // Check overlap: any confirmed or pending booking that collides
    $conflict = fetch_one("
        SELECT id FROM bookings
        WHERE unit_id=?
          AND NOT(end_at <= ? OR start_at >= ?)
          AND status IN ('pending','confirmed')
        LIMIT 1
    ", [$unit_id, $start, $end]);

    if ($conflict) {
        $_SESSION['error'] = 'This unit is not available for the selected time window.';
        redirect('../booking.php');
        exit;
    }
    
    // 🔹 Generate a unique booking ID
    $booking_id = generate_short_id();

    // Insert new booking with PENDING status
    exec_stmt("
        INSERT INTO bookings(booking_id, client_name, client_number, client_email, unit_id, start_at, end_at, notes, status)
        VALUES(?,?,?,?,?,?,?,?,?)
             ", [$booking_id, $name, $number, $email, $unit_id, $start, $end, $notes, 'pending']);

    // Call the correct sync function after the booking is made
    sync_booking_with_clients([
        'full_name' => $name,
        'client_number' => $number,
        'client_email' => $email
    ]);

    // 🔹 Store the booking details in the session to be displayed on booking.php
    $_SESSION['last_booking'] = [
        'id' => $booking_id,
        'name' => $name,
        'unit_id' => $unit_id,
        'start_at' => $start,
        'end_at' => $end,
        'status' => 'Pending'
    ];

    // 🔹 Redirect with a success flag
    redirect('../booking.php?success=1');
}