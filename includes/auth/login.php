<?php
require_once __DIR__ . '/../functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    $u = fetch_one("SELECT id, name, email, password_hash, role FROM users WHERE email=? LIMIT 1", [$email]);

    if ($u && password_verify($pass, $u['password_hash'])) {
        $_SESSION['user'] = [
            'id'    => $u['id'],
            'name'  => $u['name'],
            'email' => $u['email'],
            'role'  => $u['role']
        ];
        redirect('../../admin/index.php');
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        redirect('../../public/login.php');
    }
}
