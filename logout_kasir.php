<?php
session_start();
unset($_SESSION['kasir_logged_in']);
unset($_SESSION['kasir_id']);
unset($_SESSION['kasir_name']);

header("Location: login_kasir.php");
exit;
?>