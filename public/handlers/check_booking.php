<?php
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

// Get the booking ID from the POST request
$booking_id = trim($_POST['booking_id'] ?? '');

if (empty($booking_id)) {
    echo json_encode(['error' => 'Please enter a booking ID.']);
    exit;
}

// Fetch the booking details, including the unit name
$booking = fetch_one(
    "SELECT b.booking_id, b.status, b.start_at, b.end_at, u.make_model AS unit_name
     FROM bookings b
     JOIN units u ON b.unit_id = u.id
     WHERE b.booking_id = ?",
    [$booking_id]
);

if ($booking) {
    // 🔹 Make the status value uniform by trimming whitespace and converting to lowercase
    $booking_status = strtolower(trim($booking['status']));
    $status_class = 'bg-secondary text-light'; // Default for unknown status

    if ($booking_status === 'pending') {
        $status_class = 'bg-warning text-dark';
    } else if ($booking_status === 'confirmed') {
        $status_class = 'bg-success text-light';
    } else if ($booking_status === 'cancelled') {
        $status_class = 'bg-danger text-light';
    }

    // Return the formatted booking data as a JSON object
    echo json_encode([
        'booking_id' => $booking['booking_id'],
        'unit_name' => esc($booking['unit_name']),
        'status' => ucfirst(esc($booking['status'])),
        'status_class' => $status_class,
        'start_date' => date('M j, Y', strtotime($booking['start_at'])),
        'start_time' => date('H:i', strtotime($booking['start_at'])),
        'end_date' => date('M j, Y', strtotime($booking['end_at'])),
        'end_time' => date('H:i', strtotime($booking['end_at'])),
    ]);
} else {
    // Return an error if no booking is found
    echo json_encode(['error' => 'Booking ID not found.']);
}