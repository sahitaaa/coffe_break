<?php
session_start();
include 'koneksi.php';

// Jika sudah login kasir, langsung lempar ke index.php
if (isset($_SESSION['kasir_logged_in'])) {
    header("Location: index_kasir.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Cek user kasir
    $query = mysqli_query($koneksi, "SELECT * FROM petugas WHERE Username = '$username' AND Level = 'kasir'");
    $user = mysqli_fetch_assoc($query);

    if ($user && ($password === $user['Password'] || password_verify($password, $user['Password']))) {
        $_SESSION['kasir_logged_in'] = true;
        $_SESSION['kasir_id']        = $user['PetugasID'];
        $_SESSION['kasir_name']      = $user['NamaPetugas'];

        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau Password Kasir salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir - Cafe POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('bighrount.jpg');
            background-size: cover;
            background-position: center;
        }
        .glass-card {
            background: rgba(18, 18, 18, 0.8);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="glass-card rounded-2xl p-8 max-w-md w-full shadow-2xl text-white">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-extrabold text-amber-400">LOGIN KASIR</h1>
            <p class="text-gray-400 text-xs mt-1">Masuk untuk memproses transaksi penjualan</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-300 text-xs p-3 rounded-lg mb-4 text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-xs text-gray-300 font-semibold mb-1">Username Kasir</label>
                <input type="text" name="username" required 
                       class="w-full bg-black/40 border border-gray-600 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs text-gray-300 font-semibold mb-1">Password</label>
                <input type="password" name="password" required 
                       class="w-full bg-black/40 border border-gray-600 rounded-lg p-2.5 text-sm text-white focus:outline-none focus:border-amber-400">
            </div>

            <button type="submit" 
                    class="w-full bg-amber-500 hover:bg-amber-600 text-black font-bold py-2.5 rounded-lg transition text-sm">
                Masuk Kasir 🛒
            </button>
        </form>

        <div class="mt-6 text-center border-t border-gray-700/60 pt-4">
            <a href="login.php" class="text-xs text-amber-400 hover:underline">Login sebagai Admin? Klik di sini</a>
        </div>
    </div>

</body>
</html>