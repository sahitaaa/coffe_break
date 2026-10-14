<?php
session_start();
include 'koneksi.php';

// Cek apakah admin sudah login. Jika belum, lempar ke login.php
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Ambil data ringkasan untuk dashboard
$query_total_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
$data_produk = mysqli_fetch_assoc($query_total_produk);

$query_total_pelanggan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pelanggan");
$data_pelanggan = mysqli_fetch_assoc($query_total_pelanggan);

// Hitung total pendapatan
$query_pendapatan = mysqli_query($koneksi, "SELECT SUM(TotalHarga) as total FROM penjualan");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan =$data_pendapatan['total'] ?? 0;

// Ambil 10 transaksi terbaru lengkap dengan pelanggan dan item menu
$query_transaksi = "SELECT 
                        p.PenjualanID, 
                        p.TanggalPenjualan, 
                        p.TotalHarga,
                        pl.NamaPelanggan,
                        pl.NomorTelepon,
                        pl.Alamat,
                        GROUP_CONCAT(CONCAT(pr.NamaProduk, ' (x', dp.JumlahProduk, ')') SEPARATOR ', ') as rincian_menu
                    FROM penjualan p
                    LEFT JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
                    LEFT JOIN detailpenjualan dp ON p.PenjualanID = dp.PenjualanID
                    LEFT JOIN produk pr ON dp.ProdukID = pr.ProdukID
                    GROUP BY p.PenjualanID
                    ORDER BY p.PenjualanID DESC LIMIT 10";

$result_transaksi = mysqli_query($koneksi,$query_transaksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Cafe POS</title>
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
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen text-white flex flex-col justify-between">

    <div>
        <!-- Navigasi Utama (Header) -->
        <header class="bg-black/60 p-4 border-b border-gray-700/50 backdrop-blur-md sticky top-0 z-40">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold tracking-wide text-amber-400">CAFE POS - Admin</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-300">Selamat datang, <strong class="text-white"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator Cafe!'); ?></strong></span>
                    <a href="logout.php" class="text-sm bg-red-600/80 px-3 py-1.5 rounded-md hover:bg-red-700 transition">Logout</a>
                </div>
            </div>
        </header>

        <div class="container mx-auto p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <!-- Sidebar Menu -->
            <aside class="md:col-span-1 space-y-3">
                <h2 class="text-gray-400 uppercase text-xs font-bold px-3">MAIN NAVIGATION</h2>
                <nav class="space-y-1">
                    <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-amber-500 text-black font-semibold shadow-lg">
                        <span>🏠</span> Dashboard
                    </a>
                    <a href="menu.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700/50 hover:text-white transition">
                        <span>🍽️</span> Kelola Produk (Menu)
                    </a>
                    <a href="laporan.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700/50 hover:text-white transition">
                        <span>📄</span> Laporan Penjualan
                    </a>

                    <!-- MENU BARU: DASHBOARD KASIR -->
                    <a href="dashboard_kasir.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-700/50 hover:text-white transition">
                        <span>🖥️</span> Dashboard Kasir
                    </a>

                    <div class="border-t border-gray-700/50 my-2"></div>
                    <a href="index.php" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-amber-300 hover:bg-amber-900/40 transition border border-amber-500/20">
                        <span>🛒</span> Buka Halaman Kasir
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="md:col-span-3 space-y-6">
                <h2 class="text-3xl font-extrabold text-white">Dashboard Overview</h2>
                
                <!-- Cards Stat -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Card 1: Total Produk -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-4xl">🍽️</span>
                            <span class="text-amber-400 text-sm font-bold">Produk</span>
                        </div>
                        <p class="text-xs text-gray-400">Total Menu Terdaftar</p>
                        <p class="text-4xl font-extrabold text-white mt-1"><?php echo $data_produk['total']; ?></p>
                    </div>

                    <!-- Card 2: Total Pelanggan -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-4xl">👥</span>
                            <span class="text-amber-400 text-sm font-bold">Pelanggan</span>
                        </div>
                        <p class="text-xs text-gray-400">Total Pelanggan Terdata</p>
                        <p class="text-4xl font-extrabold text-white mt-1"><?php echo $data_pelanggan['total']; ?></p>
                    </div>

                    <!-- Card 3: Total Pendapatan -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-4xl">💰</span>
                            <span class="text-amber-400 text-sm font-bold">Pendapatan</span>
                        </div>
                        <p class="text-xs text-gray-400">Total Nilai Penjualan</p>
                        <p class="text-2xl font-extrabold text-white mt-2">
                            Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?>
                        </p>
                    </div>
                </div>

                <!-- Section Laporan & Bukti Pemesanan Terbaru -->
                <div class="glass-card rounded-2xl p-6 shadow-xl">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-700/60 pb-3">
                        <h3 class="text-xl font-bold text-amber-400 flex items-center gap-2">
                            <span>🧾</span> Bukti Pemesanan & Transaksi Terakhir
                        </h3>
                        <a href="laporan.php" class="text-xs text-amber-400 hover:text-amber-300 underline">
                            Lihat Semua Laporan →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="border-b border-gray-700 text-gray-400 text-xs uppercase">
                                    <th class="p-3">ID</th>
                                    <th class="p-3">Waktu Pemesanan</th>
                                    <th class="p-3">Pelanggan</th>
                                    <th class="p-3">Pesanan Menu</th>
                                    <th class="p-3">Total Belanja</th>
                                    <th class="p-3 text-center">Bukti / Struk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                                        <tr class="hover:bg-white/5 transition">
                                            <td class="p-3 font-bold text-amber-400">#<?php echo $row['PenjualanID']; ?></td>
                                            <td class="p-3 text-gray-300 text-xs">
                                                <?php echo date('d M Y - H:i:s', strtotime($row['TanggalPenjualan'])); ?>
                                            </td>
                                            <td class="p-3">
                                                <div class="font-medium text-white"><?php echo !empty($row['NamaPelanggan']) ? htmlspecialchars($row['NamaPelanggan']) : '-'; ?></div>
                                                <div class="text-xs text-gray-400"><?php echo !empty($row['NomorTelepon']) ? htmlspecialchars($row['NomorTelepon']) : ''; ?></div>
                                            </td>
                                            <td class="p-3 text-gray-300 text-xs max-w-xs truncate">
                                                <?php echo !empty($row['rincian_menu']) ? htmlspecialchars($row['rincian_menu']) : '-'; ?>
                                            </td>
                                            <td class="p-3 font-bold text-amber-400">
                                                Rp <?php echo number_format($row['TotalHarga'], 0, ',', '.'); ?>
                                            </td>
                                            <td class="p-3 text-center">
                                                <button onclick="bukaModalStruk('<?php echo $row['PenjualanID']; ?>', '<?php echo date('d/m/Y H:i:s', strtotime($row['TanggalPenjualan'])); ?>', '<?php echo addslashes($row['NamaPelanggan']); ?>', '<?php echo addslashes($row['NomorTelepon']); ?>', '<?php echo addslashes($row['Alamat']); ?>', '<?php echo addslashes($row['rincian_menu']); ?>', '<?php echo number_format($row['TotalHarga'], 0, ',', '.'); ?>')" 
                                                        class="bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 border border-amber-500/40 text-xs px-3 py-1.5 rounded-lg transition">
                                                    📄 Lihat Struk
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-gray-400">Belum ada transaksi recorded.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Pop-up Struk Pemesanan -->
    <div id="modalStruk" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden flex justify-center items-center z-50 p-4">
        <div class="bg-white text-black rounded-xl max-w-md w-full p-6 shadow-2xl relative font-mono text-sm">
            <div class="text-center border-b border-gray-300 pb-3 mb-3">
                <h2 class="text-xl font-bold uppercase tracking-wider">CAFE POS</h2>
                <p class="text-xs text-gray-600">Bukti Struk Pemesanan Kasir</p>
            </div>

            <div class="space-y-1 text-xs mb-3">
                <div class="flex justify-between"><span>No. Transaksi:</span><strong id="strukID">#0</strong></div>
                <div class="flex justify-between"><span>Waktu:</span><span id="strukWaktu">-</span></div>
                <div class="flex justify-between"><span>Pelanggan:</span><strong id="strukNama">-</strong></div>
                <div class="flex justify-between"><span>No. HP:</span><span id="strukTelp">-</span></div>
                <div class="flex justify-between"><span>Alamat:</span><span id="strukAlamat">-</span></div>
            </div>

            <div class="border-t border-b border-dashed border-gray-400 py-3 my-2">
                <p class="font-bold mb-1 text-xs text-gray-700">Rincian Menu:</p>
                <div id="strukItem" class="text-xs space-y-1 text-gray-800"></div>
            </div>

            <div class="flex justify-between text-base font-bold my-3">
                <span>TOTAL:</span>
                <span id="strukTotal" class="text-amber-700">Rp 0</span>
            </div>

            <div class="text-center text-xs text-gray-500 mt-4 mb-2">
                --- Terima Kasih Atas Kunjungan Anda ---
            </div>

            <div class="flex gap-2 mt-4">
                <button onclick="window.print()" class="flex-1 bg-gray-800 text-white py-2 rounded-lg font-sans text-xs hover:bg-black transition">🖨️ Cetak Struk</button>
                <button onclick="tutupModalStruk()" class="flex-1 bg-red-600 text-white py-2 rounded-lg font-sans text-xs hover:bg-red-700 transition">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Script Pop-up Modal -->
    <script>
        function bukaModalStruk(id, waktu, nama, telp, alamat, items, total) {
            document.getElementById('strukID').innerText = '#' + id;
            document.getElementById('strukWaktu').innerText = waktu;
            document.getElementById('strukNama').innerText = nama || '-';
            document.getElementById('strukTelp').innerText = telp || '-';
            document.getElementById('strukAlamat').innerText = alamat || '-';
            document.getElementById('strukTotal').innerText = 'Rp ' + total;

            let listItems = items.split(', ').map(item => `<div>• ${item}</div>`).join('');
            document.getElementById('strukItem').innerHTML = listItems || '-';

            document.getElementById('modalStruk').classList.remove('hidden');
        }

        function tutupModalStruk() {
            document.getElementById('modalStruk').classList.add('hidden');
        }
    </script>

</body>
</html>