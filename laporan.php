<?php
include 'koneksi.php';

// Query mengambil data penjualan lengkap dengan pelanggan dan detail menu
$query = "SELECT 
            p.PenjualanID, 
            p.TanggalPenjualan, 
            p.TotalHarga,
            pl.NamaPelanggan,
            pl.NomorTelepon,
            pl.Alamat,
            GROUP_CONCAT(CONCAT(pr.NamaProduk, ' (', dp.JumlahProduk, 'x)') SEPARATOR '<br>') as rincian_menu
          FROM penjualan p
          LEFT JOIN pelanggan pl ON p.PelangganID = pl.PelangganID
          LEFT JOIN detailpenjualan dp ON p.PenjualanID = dp.PenjualanID
          LEFT JOIN produk pr ON dp.ProdukID = pr.ProdukID
          GROUP BY p.PenjualanID
          ORDER BY p.PenjualanID DESC";

$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Dark Glass</title>
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
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }
        .glass-header {
            background: rgba(10, 10, 10, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen text-white flex flex-col">

    <!-- Header -->
    <header class="glass-header p-4 sticky top-0 z-50 flex justify-between items-center px-8">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full overflow-hidden border border-amber-500/50 shadow-lg bg-black/40 flex items-center justify-center">
                <img src="logo.jpg" alt="Logo" class="w-full h-full object-cover">
            </div>
            <h1 class="text-xl font-bold text-amber-400">CAFE POS - Admin</h1>
        </div>
        <a href="dashboard.php" class="text-sm bg-gray-700/60 hover:bg-gray-600/80 px-4 py-2 rounded-lg border border-gray-500/40 transition">
            Kembali ke Dashboard
        </a>
    </header>

    <!-- Main Container -->
    <main class="container mx-auto p-6 flex-grow">
        <div class="glass-card rounded-2xl p-6">
            <h2 class="text-2xl font-bold mb-6 text-amber-400 border-b border-gray-700 pb-3">
                Riwayat Laporan Penjualan Lengkap
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-700 text-gray-400 uppercase text-xs">
                            <th class="p-3">ID</th>
                            <th class="p-3">Tanggal & Waktu</th>
                            <th class="p-3">Pelanggan</th>
                            <th class="p-3">No. Telepon / Alamat</th>
                            <th class="p-3">Rincian Menu</th>
                            <th class="p-3 text-right">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-white/5 transition">
                                    <td class="p-3 font-semibold text-amber-400">#<?php echo $row['PenjualanID']; ?></td>
                                    <td class="p-3 text-gray-300">
                                        <?php 
                                            // Format tanggal dan jam: Tgl Bln Thn - HH:MM:SS
                                            echo date('d M Y - H:i:s', strtotime($row['TanggalPenjualan'])); 
                                        ?>
                                    </td>
                                    <td class="p-3 font-medium text-white">
                                        <?php echo !empty($row['NamaPelanggan']) ? htmlspecialchars($row['NamaPelanggan']) : '-'; ?>
                                    </td>
                                    <td class="p-3 text-gray-400 text-xs">
                                        <div><?php echo !empty($row['NomorTelepon']) ? htmlspecialchars($row['NomorTelepon']) : '-'; ?></div>
                                        <div class="text-gray-500"><?php echo !empty($row['Alamat']) ? htmlspecialchars($row['Alamat']) : ''; ?></div>
                                    </td>
                                    <td class="p-3 text-gray-300 text-xs leading-relaxed">
                                        <?php echo !empty($row['rincian_menu']) ? $row['rincian_menu'] : '-'; ?>
                                    </td>
                                    <td class="p-3 text-right font-bold text-amber-400">
                                        Rp <?php echo number_format($row['TotalHarga'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-400">Belum ada riwayat transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="glass-header text-center text-xs text-gray-400 py-4 mt-8">
        &copy; 2026 Admin POS Cafe System - Theme Dark Glass
    </footer>

</body>
</html>