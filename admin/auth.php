<?php
require_once __DIR__ . '/../includes/functions.php';

// Auto-release units when booking time ends
exec_stmt("UPDATE units SET status='available', booked_until=NULL WHERE status='booked' AND booked_until < NOW()");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Idle timeout 
$idle_timeout = 600;

// If not logged in → redirect to login
if (!is_logged_in()) {
    redirect('../public/login.php');
    exit;
}

// Check for inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $idle_timeout)) {
    // Clear session
    $_SESSION = [];
    session_unset();
    session_destroy();

    // Start fresh session to store flash message
    session_start();
    $_SESSION['error'] = "You have been logged out due to inactivity.";
    redirect('../public/login.php');
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
