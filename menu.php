<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$pesan = '';

// Proses Tambah Produk Baru
if (isset($_POST['tambah_produk'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    
    // Sederhana: Input nama file gambar langsung (misal: 'kopi.jpg')
    $gambar = mysqli_real_escape_string($koneksi, $_POST['gambar_file']); 

    $query_tambah = "INSERT INTO produk (NamaProduk, Harga, Stok, Gambar) 
                     VALUES ('$nama', '$harga', '$stok', '$gambar')";
    if (mysqli_query($koneksi, $query_tambah)) {
        $pesan = "Produk berhasil ditambahkan!";
    } else {
        $pesan = "Gagal menambah produk: " . mysqli_error($koneksi);
    }
}

// Proses Edit Produk
if (isset($_POST['edit_produk'])) {
    $id = (int)$_POST['produk_id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $gambar = mysqli_real_escape_string($koneksi, $_POST['gambar_file']);

    $query_edit = "UPDATE produk SET NamaProduk='$nama', Harga='$harga', Stok='$stok', Gambar='$gambar' 
                   WHERE ProdukID='$id'";
    if (mysqli_query($koneksi, $query_edit)) {
        $pesan = "Produk berhasil diperbarui!";
    } else {
        $pesan = "Gagal memperbarui produk: " . mysqli_error($koneksi);
    }
}

// Proses Hapus Produk
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if (mysqli_query($koneksi, "DELETE FROM produk WHERE ProdukID='$id'")) {
        $pesan = "Produk berhasil dihapus!";
    } else {
        $pesan = "Gagal menghapus produk: " . mysqli_error($koneksi);
    }
}

// Ambil semua daftar produk
$query_produk = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY ProdukID DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Cafe POS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-image: url('bighrount.jpg'); background-size: cover; background-position: center; background-attachment: fixed; }
        .glass-card { background: rgba(18, 18, 18, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .glass-input { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; }
    </style>
</head>
<body class="min-h-screen text-white flex flex-col">

    <!-- Header (Sama dengan dashboard) -->
    <header class="bg-black/60 p-4 border-b border-gray-700/50 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wide text-amber-400">CAFE POS - Admin</h1>
            <a href="dashboard.php" class="text-sm bg-gray-700/80 px-3 py-1 rounded-md hover:bg-gray-800 transition">Kembali ke Dashboard</a>
        </div>
    </header>

    <main class="container mx-auto p-6 flex-grow space-y-8">
        <h2 class="text-3xl font-extrabold text-white flex items-center gap-3">
            <span>🍽️</span> Kelola Menu Makanan & Minuman
        </h2>

        <?php if (!empty($pesan)): ?>
            <div class="bg-blue-600/30 border border-blue-500 text-blue-200 text-sm p-4 rounded-xl text-center shadow-lg">
                <?php echo $pesan; ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah/Edit Produk -->
        <div class="glass-card rounded-2xl p-6 shadow-xl">
            <h3 class="text-xl font-bold text-gray-200 mb-5 border-b border-gray-700 pb-2">Input/Edit Data Produk</h3>
            <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="hidden" name="produk_id" id="produk_id">
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-400 mb-1">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" required placeholder="Contoh: Kopi Susu Aren" class="w-full px-4 py-2 rounded-lg glass-input text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" id="harga" required placeholder="15000" class="w-full px-4 py-2 rounded-lg glass-input text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Stok</label>
                    <input type="number" name="stok" id="stok" required placeholder="50" class="w-full px-4 py-2 rounded-lg glass-input text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">File Gambar (Simpan di Folder Utama)</label>
                    <input type="text" name="gambar_file" id="gambar_file" required placeholder="kopi.jpg" class="w-full px-4 py-2 rounded-lg glass-input text-sm">
                </div>
                <div class="md:col-span-5 flex justify-end gap-2 pt-2">
                    <button type="submit" name="tambah_produk" id="btn_tambah" class="py-2.5 px-6 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold rounded-lg shadow transition hover:from-amber-600 text-sm">
                        Simpan Produk Baru
                    </button>
                    <button type="submit" name="edit_produk" id="btn_edit" class="py-2.5 px-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg shadow transition hover:from-blue-600 text-sm hidden">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="resetForm()" class="py-2.5 px-6 bg-gray-600 text-white font-semibold rounded-lg shadow transition hover:bg-gray-700 text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Produk -->
        <div class="glass-card rounded-2xl p-6 shadow-xl overflow-x-auto">
            <h3 class="text-xl font-bold text-gray-200 mb-4">Daftar Produk Saat Ini</h3>
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase border-b border-gray-700">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Gambar</th>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Stok</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    <?php while ($row = mysqli_fetch_assoc($query_produk)): ?>
                        <tr class="hover:bg-gray-800/40">
                            <td class="px-4 py-3"><?php echo $row['ProdukID']; ?></td>
                            <td class="px-4 py-3">
                                <img src="<?php echo $row['Gambar']; ?>" alt="<?php echo htmlspecialchars($row['NamaProduk']); ?>" class="w-12 h-12 object-cover rounded-lg border border-gray-600">
                            </td>
                            <td class="px-4 py-3 font-medium text-white"><?php echo htmlspecialchars($row['NamaProduk']); ?></td>
                            <td class="px-4 py-3">Rp <?php echo number_format($row['Harga'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-3"><?php echo $row['Stok']; ?></td>
                            <td class="px-4 py-3 space-x-1">
                                <button onclick='setEditForm(<?php echo json_encode($row); ?>)' class="text-xs bg-blue-600/70 px-3 py-1 rounded hover:bg-blue-700 transition">Edit</button>
                                <a href="menu.php?hapus=<?php echo $row['ProdukID']; ?>" onclick="return confirm('Hapus produk ini?')" class="text-xs bg-red-600/70 px-3 py-1 rounded hover:bg-red-700 transition">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="text-center text-xs text-gray-500 py-4 mt-8 border-t border-gray-700/50 bg-black/40">
        &copy; 2026 Admin POS Cafe System - Theme Dark Glass
    </footer>

    <!-- JavaScript Sederhana untuk Mengisi Form Edit -->
    <script>
        function setEditForm(data) {
            document.getElementById('produk_id').value = data.ProdukID;
            document.getElementById('nama_produk').value = data.NamaProduk;
            document.getElementById('harga').value = data.Harga;
            document.getElementById('stok').value = data.Stok;
            document.getElementById('gambar_file').value = data.Gambar;
            
            // Ubah tombol
            document.getElementById('btn_tambah').classList.add('hidden');
            document.getElementById('btn_edit').classList.remove('hidden');
        }

        function resetForm() {
            document.getElementById('produk_id').value = '';
            document.getElementById('nama_produk').value = '';
            document.getElementById('harga').value = '';
            document.getElementById('stok').value = '';
            document.getElementById('gambar_file').value = '';

            // Ubah tombol kembali
            document.getElementById('btn_tambah').classList.remove('hidden');
            document.getElementById('btn_edit').classList.add('hidden');
        }
    </script>

</body>
</html>