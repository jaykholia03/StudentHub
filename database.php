<?php
$conn = mysqli_connect('localhost', 'root', '', 'studenthub');

if (!$conn) {
    die('Could not connect to the database.');
}

mysqli_set_charset($conn, 'utf8mb4');

require_once __DIR__ . '/includes/helpers.php';
