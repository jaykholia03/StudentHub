<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (current_role() !== 'admin') {
    header('Location: ../dashboard.php');
    exit;
}

$adminName = $_SESSION['full_name'] ?? 'Admin';
$flash = flash_get();
