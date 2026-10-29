<?php
session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
} else {
    // Jika sudah login, redirect ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>
