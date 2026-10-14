<?php
session_start();
include 'koneksi.php';

// Proteksi: Hanya kasir yang bisa masuk
if (!isset($_SESSION['kasir_logged_in'])) {
    header("Location: login_kasir.php");
    exit;
}

$kasir_id   =$_SESSION['kasir_id'] ?? 0;
$kasir_nama =$_SESSION['kasir_name'] ?? 'Kasir';

// Nama file saat ini secara otomatis
$current_page = basename($_SERVER['PHP_SELF']);

// Proses Pembatalan/Hapus Transaksi oleh Kasir
if (isset($_GET['aksi']) &&$_GET['aksi'] === 'batal') {
    $penjualan_id = (int)$_GET['id'];

    // 1. Kembalikan Stok Produk
    $q_detail = mysqli_query($koneksi, "SELECT ProdukID, JumlahProduk FROM detailpenjualan WHERE PenjualanID = '$penjualan_id'");
    while ($item = mysqli_fetch_assoc($q_detail)) {
        $p_id =$item['ProdukID'];
        $qty  =$item['JumlahProduk'];
        mysqli_query($koneksi, "UPDATE produk SET Stok = Stok + $qty WHERE ProdukID = '$p_id'");
    }

    // 2. Hapus detail & penjualan
    mysqli_query($koneksi, "DELETE FROM detailpenjualan WHERE PenjualanID = '$penjualan_id'");
    mysqli_query($koneksi, "DELETE FROM penjualan WHERE PenjualanID = '$penjualan_id'");

    echo "<script>alert('Transaksi #$penjualan_id berhasil dibatalkan dan stok dikembalikan!'); window.location='$current_page';</script>";
    exit;
}

// Pencarian Transaksi
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi,$_GET['search']) : '';
$where_search =$search ? "AND (pl.NamaPelanggan LIKE '%$search\%' OR p.PenjualanID LIKE '\%$search%')" : "";

// Hitung Ringkasan Hari Ini
$hari_ini = date('Y-m-d');
$q_transaksi_today = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM penjualan WHERE DATE(TanggalPenjualan) = '$hari_ini'");
$total_today = mysqli_fetch_assoc($q_transaksi_today)['total'] ?? 0;

$q_omset_today = mysqli_query($koneksi, "SELECT SUM(TotalHarga) as total FROM penjualan WHERE DATE(TanggalPenjualan) = '$hari_ini'");
$omset_today = mysqli_fetch_assoc($q_omset_today)['total'] ?? 0;

// Query Daftar Transaksi
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
                    WHERE 1=1 $where_search
                    GROUP BY p.PenjualanID
                    ORDER BY p.PenjualanID DESC";

$result_transaksi = mysqli_query($koneksi,$query_transaksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Kasir - Cafe POS</title>
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
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
    </style>
</head>
<body class="min-h-screen text-white flex flex-col justify-between">

    <div>
        <!-- Header Kasir -->
        <header class="bg-black/70 p-4 border-b border-gray-700/50 backdrop-blur-md sticky top-0 z-40">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-amber-400">CAFE POS - Area Kasir</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-300">Petugas: <strong class="text-white"><?php echo htmlspecialchars($kasir_nama); ?></strong></span>
                    <a href="logout_kasir.php" class="text-xs bg-red-600/80 hover:bg-red-700 px-3 py-1.5 rounded-lg transition">Logout</a>
                </div>
            </div>
        </header>

        <div class="container mx-auto p-6 space-y-6">

            <!-- Banner Akses Cepat & Ringkasan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tombol Buat Pesanan Baru -->
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border-amber-500/40">
                    <div>
                        <h3 class="text-lg font-bold text-amber-400">Kasir POS</h3>
                        <p class="text-xs text-gray-400 mt-1">Lakukan input pesanan & transaksi baru</p>
                    </div>
                    <a href="index.php" class="mt-4 inline-block text-center bg-amber-500 hover:bg-amber-600 text-black font-bold py-2.5 rounded-xl transition">
                        🛒 Buka Aplikasi Kasir
                    </a>
                </div>

                <!-- Total Transaksi Hari Ini -->
                <div class="glass-card rounded-2xl p-6">
                    <p class="text-xs text-gray-400">Transaksi Hari Ini</p>
                    <p class="text-3xl font-bold text-white mt-2"><?php echo $total_today; ?> <span class="text-xs font-normal text-gray-400">Nota</span></p>
                </div>

                <!-- Total Omset Hari Ini -->
                <div class="glass-card rounded-2xl p-6">
                    <p class="text-xs text-gray-400">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold text-amber-400 mt-2">Rp <?php echo number_format($omset_today, 0, ',', '.'); ?></p>
                </div>
            </div>

            <!-- Tabel Kelola Data Transaksi Kasir -->
            <div class="glass-card rounded-2xl p-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-xl font-bold text-amber-400">Kelola Data Transaksi & Pesanan</h3>

                    <!-- Form Cari Nota -->
                    <form method="GET" class="flex gap-2 w-full md:w-auto">
                        <input type="text" name="search" placeholder="Cari ID / Nama Pelanggan..." value="<?php echo htmlspecialchars($search); ?>"
                               class="bg-black/50 border border-gray-600 rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-amber-400 w-full">
                        <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-xs px-3 py-1.5 rounded-lg transition">Cari</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-gray-700 text-gray-400 text-xs uppercase">
                                <th class="p-3">ID Transaksi</th>
                                <th class="p-3">Waktu</th>
                                <th class="p-3">Pelanggan</th>
                                <th class="p-3">Item Pesanan</th>
                                <th class="p-3">Total</th>
                                <th class="p-3 text-center">Aksi & Struk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="p-3 font-bold text-amber-400">#<?php echo $row['PenjualanID']; ?></td>
                                        <td class="p-3 text-xs text-gray-300">
                                            <?php echo date('d/m/Y H:i:s', strtotime($row['TanggalPenjualan'])); ?>
                                        </td>
                                        <td class="p-3">
                                            <div class="font-medium text-white"><?php echo !empty($row['NamaPelanggan']) ? htmlspecialchars($row['NamaPelanggan']) : '-'; ?></div>
                                            <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['NomorTelepon']); ?></div>
                                        </td>
                                        <td class="p-3 text-xs text-gray-300 max-w-xs truncate">
                                            <?php echo htmlspecialchars($row['rincian_menu']); ?>
                                        </td>
                                        <td class="p-3 font-bold text-amber-400">
                                            Rp <?php echo number_format($row['TotalHarga'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="p-3 text-center space-x-1">
                                            <button onclick="bukaStruk('<?php echo $row['PenjualanID']; ?>', '<?php echo date('d/m/Y H:i:s', strtotime($row['TanggalPenjualan'])); ?>', '<?php echo addslashes($row['NamaPelanggan']); ?>', '<?php echo addslashes($row['NomorTelepon']); ?>', '<?php echo addslashes($row['Alamat']); ?>', '<?php echo addslashes($row['rincian_menu']); ?>', '<?php echo number_format($row['TotalHarga'], 0, ',', '.'); ?>')" 
                                                    class="bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 border border-amber-500/40 text-xs px-2.5 py-1 rounded transition">
                                                📄 Struk
                                            </button>
                                            <a href="<?php echo $current_page; ?>?aksi=batal&id=<?php echo$row['PenjualanID']; ?>" 
                                               onclick="return confirm('Yakin ingin membatalkan transaksi #<?php echo $row['PenjualanID']; ?>? Stok barang akan dikembalikan.');"
                                               class="bg-red-500/20 hover:bg-red-500/40 text-red-300 border border-red-500/40 text-xs px-2.5 py-1 rounded transition">
                                                🗑️ Batal
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-400">Tidak ada data transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Struk -->
    <div id="modalStruk" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden flex justify-center items-center z-50 p-4">
        <div class="bg-white text-black rounded-xl max-w-md w-full p-6 shadow-2xl relative font-mono text-sm">
            <div class="text-center border-b border-gray-300 pb-3 mb-3">
                <h2 class="text-xl font-bold uppercase tracking-wider">CAFE POS</h2>
                <p class="text-xs text-gray-600">Bukti Pembayaran</p>
            </div>

            <div class="space-y-1 text-xs mb-3">
                <div class="flex justify-between"><span>No. Nota:</span><strong id="strukID">#0</strong></div>
                <div class="flex justify-between"><span>Waktu:</span><span id="strukWaktu">-</span></div>
                <div class="flex justify-between"><span>Pelanggan:</span><strong id="strukNama">-</strong></div>
                <div class="flex justify-between"><span>No. Telepon:</span><span id="strukTelp">-</span></div>
            </div>

            <div class="border-t border-b border-dashed border-gray-400 py-3 my-2">
                <p class="font-bold mb-1 text-xs text-gray-700">Detail Pesanan:</p>
                <div id="strukItem" class="text-xs space-y-1 text-gray-800"></div>
            </div>

            <div class="flex justify-between text-base font-bold my-3">
                <span>TOTAL:</span>
                <span id="strukTotal" class="text-amber-700">Rp 0</span>
            </div>

            <div class="flex gap-2 mt-4 font-sans">
                <button onclick="window.print()" class="flex-1 bg-gray-800 text-white py-2 rounded-lg text-xs hover:bg-black transition">🖨️ Cetak</button>
                <button onclick="tutupStruk()" class="flex-1 bg-red-600 text-white py-2 rounded-lg text-xs hover:bg-red-700 transition">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function bukaStruk(id, waktu, nama, telp, alamat, items, total) {
            document.getElementById('strukID').innerText = '#' + id;
            document.getElementById('strukWaktu').innerText = waktu;
            document.getElementById('strukNama').innerText = nama || '-';
            document.getElementById('strukTelp').innerText = telp || '-';
            document.getElementById('strukTotal').innerText = 'Rp ' + total;

            let listItems = items.split(', ').map(item => `<div>• ${item}</div>`).join('');
            document.getElementById('strukItem').innerHTML = listItems || '-';

            document.getElementById('modalStruk').classList.remove('hidden');
        }

        function tutupStruk() {
            document.getElementById('modalStruk').classList.add('hidden');
        }
    </script>

</body>
</html>