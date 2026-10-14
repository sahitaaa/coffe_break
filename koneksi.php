<?php
$koneksi = mysqli_connect("localhost", "root", "", "kasir_db");
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>