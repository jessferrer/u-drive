<?php
require_once __DIR__ . '/../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear session data first
$_SESSION = [];
session_unset();
session_destroy();

// Start a new session to hold the flash message
session_start();
$_SESSION['success'] = "You have logged out successfully.";

// Redirect back to login page
redirect('../../public/login.php');
