<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    $id_barang = $_POST['id_barang'] ?? 0;
    
    if (!verifyCSRFToken($csrf_token)) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header("Location: barang.php");
        exit();
    }
    
    $result = deleteBarang($conn, $id_barang);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
}

header("Location: barang.php");
exit();
?>
