<?php
session_start();
include 'koneksi.php';

// Ambil info status login
$is_admin = isset($_SESSION['admin_logged_in']);
$is_kasir = isset($_SESSION['kasir_logged_in']);

// Nama User yang sedang aktif
$nama_user = $_SESSION['admin_name'] ?? $_SESSION['kasir_name'] ?? 'Tamu';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Navigasi & Pengelolaan - Cafe POS</title>
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
            background: rgba(18, 18, 18, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="min-h-screen text-white flex flex-col justify-between">

    <!-- Header Portal -->
    <header class="bg-black/70 p-4 border-b border-gray-700/50 backdrop-blur-md sticky top-0 z-40">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full overflow-hidden border border-amber-500/50 bg-black flex items-center justify-center">
                    <img src="logo.jpg" alt="Logo" class="w-full h-full object-cover">
                </div>
                <h1 class="text-xl font-bold tracking-wide text-amber-400">CAFE POS - Portal Utama</h1>
            </div>
            
            <div class="flex items-center gap-4">
                <?php if ($is_admin || $is_kasir): ?>
                    <span class="text-sm text-gray-300">Login sebagai: <strong class="text-amber-400"><?php echo htmlspecialchars($nama_user); ?></strong></span>
                    <a href="logout.php" class="text-xs bg-red-600/80 hover:bg-red-700 px-3 py-1.5 rounded-lg transition">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-xs bg-amber-500 text-black font-bold hover:bg-amber-400 px-3 py-1.5 rounded-lg transition">Login Admin</a>
                    <a href="login_kasir.php" class="text-xs bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg transition">Login Kasir</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Content Utama -->
    <main class="container mx-auto p-6 max-w-5xl flex-grow flex flex-col justify-center my-8">
        <div class="text-center mb-10">
            <h2 class="text-4xl font-extrabold text-white mb-2">Pilih Ruang Kerja</h2>
            <p class="text-gray-400 text-sm">Silakan pilih modul yang ingin diakses untuk pengelolaan sistem cafe</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Pilihan 1: Akses Admin -->
            <div class="glass-card rounded-3xl p-8 hover:border-amber-500/60 transition duration-300 flex flex-col justify-between shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-8 -bottom-8 text-8xl opacity-10 group-hover:opacity-20 transition">⚙️</div>
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-3xl mb-4">
                        👑
                    </div>
                    <h3 class="text-2xl font-bold text-amber-400 mb-2">Panel Admin</h3>
                    <p class="text-xs text-gray-300 leading-relaxed mb-6">
                        Akses modul pengelolaan penuh: Tambah/Edit Produk & Stok Menu, Pantau Pendapatan, dan Lihat Ringkasan Laporan Penjualan Keseluruhan.
                    </p>
                </div>

                <div class="space-y-2">
                    <a href="dashboard.php" class="block text-center w-full bg-amber-500 hover:bg-amber-600 text-black font-bold py-3 rounded-xl transition shadow-lg text-sm">
                        Masuk Dashboard Admin →
                    </a>
                    <a href="menu.php" class="block text-center w-full bg-white/5 hover:bg-white/10 text-gray-300 border border-gray-600/40 py-2 rounded-xl transition text-xs">
                        Kelola Menu & Stok
                    </a>
                </div>
            </div>

            <!-- Pilihan 2: Akses Kasir & POS -->
            <div class="glass-card rounded-3xl p-8 hover:border-amber-500/60 transition duration-300 flex flex-col justify-between shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-8 -bottom-8 text-8xl opacity-10 group-hover:opacity-20 transition">🛒</div>
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-3xl mb-4">
                        🛒
                    </div>
                    <h3 class="text-2xl font-bold text-amber-400 mb-2">Kasir & POS Transaksi</h3>
                    <p class="text-xs text-gray-300 leading-relaxed mb-6">
                        Akses khusus area kasir: Lakukan Input Pesanan Baru, Kelola Nota Transaksi Penjualan, Pembatalan Pesanan, dan Cetak Struk Pembayaran.
                    </p>
                </div>

                <div class="space-y-2">
                    <a href="dashboard_kasir.php" class="block text-center w-full bg-amber-500 hover:bg-amber-600 text-black font-bold py-3 rounded-xl transition shadow-lg text-sm">
                        Masuk Dashboard Kasir →
                    </a>
                    <a href="index.php" class="block text-center w-full bg-white/5 hover:bg-white/10 text-amber-300 border border-amber-500/30 py-2 rounded-xl transition text-xs">
                        🛒 Buka Aplikasi POS Kasir
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black/60 text-center text-xs text-gray-400 py-4 border-t border-gray-800">
        &copy; 2026 Admin POS Cafe System - Portal Navigasi
    </footer>

</body>
</html>