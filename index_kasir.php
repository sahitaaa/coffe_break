<?php
session_start();
include 'koneksi.php';

// Proteksi Kasir: Jika belum login kasir ATAU admin, lempar ke login_kasir.php
if (!isset($_SESSION['kasir_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: login_kasir.php");
    exit;
}

// Nama user yang sedang aktif
$nama_user = $_SESSION['kasir_name'] ?? $_SESSION['admin_name'] ?? 'Kasir';
?>