<?php
// Start session once so all pages can access login/cart data.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings (change if your local setup differs).
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'food_ordering_system';

// Create mysqli connection using mysqli functions only.
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Stop execution if DB connection fails.
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}
?>
