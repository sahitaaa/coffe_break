<?php
session_start();
include 'koneksi.php';

// Jika admin sudah login, langsung arahkan ke dashboard/halaman utama
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Cek data admin di database (Sesuaikan nama tabel & kolom jika berbeda)
    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        // Memeriksa password (bisa menggunakan password_verify jika di-hash, atau pembandingan langsung)
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $row['username'];
            $_SESSION['admin_name'] = isset($row['nama_user']) ? $row['nama_user'] : $row['username'];
            
            // Arahkan ke halaman Dashboard Admin
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Cafe & Resto POS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('bighrount.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(18, 18, 18, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.6);
        }
        .glass-input {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #f59e0b;
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="glass-card w-full max-w-md rounded-2xl p-8 space-y-6 text-white">
        
        <!-- Header / Logo -->
    <div class="text-center space-y-2">
        <!-- Logo Gambar JPG -->
        <div class="w-16 h-16 rounded-full overflow-hidden border border-amber-400/30 shadow-lg bg-black/40 mx-auto flex items-center justify-center">
            <img src="logo.jpg" alt="Logo Cafe" class="w-full h-full object-cover">
        </div>
        <h1 class="text-2xl font-bold tracking-wide text-amber-400">Portal Admin POS</h1>
        <p class="text-xs text-gray-400">Silakan login untuk mengelola transaksi & menu</p>
    </div>
        <!-- Alert Pesan Error -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-300 text-xs p-3 rounded-lg text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-300 mb-1">Username</label>
                <input type="text" name="username" required placeholder="Masukkan username" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
            </div>

            <div>
                <label class="block text-xs text-gray-300 mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
            </div>

            <button type="submit" name="login" class="w-full py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold rounded-xl shadow-lg transition duration-200 text-sm mt-2">
                Masuk ke System
            </button>
        </form>

        <!-- Link Kembali ke Kasir Front-End -->
        <div class="text-center pt-2 border-t border-gray-700/50">
            <a href="index.php" class="text-xs text-gray-400 hover:text-amber-400 transition">
                ← Kembali ke Halaman Kasir (Index)
            </a>
        </div>

    </div>

</body>
</html>