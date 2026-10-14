<?php
include 'koneksi.php';

// Ambil semua daftar produk
$query_produk = mysqli_query($koneksi, "SELECT * FROM produk");

// Simpan data ke array
$produk_list = [];
while ($row = mysqli_fetch_assoc($query_produk)) {
    $produk_list[] =$row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem db_kasir - Dark Glass</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-image: url('bighrount.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        /* Dark Glassmorphism Effect */
        .glass-card {
            background: rgba(18, 18, 18, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
        }

        .glass-header {
            background: rgba(10, 10, 10, 0.75);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #3b82f6;
            outline: none;
        }

        /* Scrollbar custom */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.2);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Header Navigation -->
    <header class="glass-header text-white p-4 sticky top-0 z-50 flex justify-between items-center px-8">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full overflow-hidden border border-amber-500/50 shadow-lg bg-black/40 flex items-center justify-center">
                <img src="logo.jpg" alt="Logo Cafe" class="w-full h-full object-cover">
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-wide">CAFE & RESTO POS</h1>
                <p class="text-xs text-gray-400">Selamat Datang di Portal Kasir Digital</p>
            </div>
        </div>
        <div>
            <a href="login.php" class="text-sm bg-amber-500/20 text-amber-300 border border-amber-500/40 px-4 py-2 rounded-lg hover:bg-amber-500/40 hover:scale-105 transition duration-300 flex items-center gap-2 font-medium">
                Login Admin / Dashboard
            </a>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow container mx-auto p-4 md:p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri & Tengah: Menu Produk -->
        <section class="lg:col-span-2 space-y-6">
            
            <!-- Dark Glass Box Menu -->
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-2xl font-bold mb-4 text-amber-400 border-b border-gray-700 pb-2">
                    Daftar Menu Resto
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    <?php foreach ($produk_list as$item): ?>
                        <div class="glass-card rounded-xl p-3.5 flex flex-col justify-between hover:border-amber-500/50 transition duration-300 hover:scale-102 hover:shadow-amber-500/20 shadow-lg">
                            <div>
                                <div class="w-full h-44 rounded-lg overflow-hidden mb-3 border border-gray-700/50 bg-black/30">
                                    <img src="<?php echo $item['Gambar']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['NamaProduk']); ?>" 
                                         class="w-full h-full object-cover hover:scale-110 transition duration-500">
                                </div>
                                <h3 class="font-semibold text-white text-sm line-clamp-2">
                                    <?php echo htmlspecialchars($item['NamaProduk']); ?>
                                </h3>
                            </div>
                            <div class="mt-3 flex items-center justify-between pt-2 border-t border-gray-700/40">
                                <span class="text-amber-400 font-bold text-sm">
                                    Rp <?php echo number_format($item['Harga'], 0, ',', '.'); ?>
                                </span>
                                <span class="text-xs text-gray-400 bg-gray-800/60 px-2 py-0.5 rounded border border-gray-700">
                                    Stok: <?php echo $item['Stok']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Kolom Kanan: Form Transaksi Multi-Item -->
        <section class="space-y-6">
            <div class="glass-card rounded-2xl p-6 sticky top-24 shadow-2xl">
                <h2 class="text-xl font-bold mb-4 text-amber-400 border-b border-gray-700 pb-2">
                    Input Transaksi Penjualan
                </h2>

                <form action="proses_transaksi.php" method="POST" class="space-y-4">
                    
                    <!-- Data Pelanggan -->
                    <div class="border-b border-gray-700/50 pb-4 space-y-3">
                        <h3 class="text-sm font-semibold text-gray-300">Data Pelanggan</h3>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" required placeholder="Masukkan nama pelanggan" class="w-full px-3 py-2.5 rounded-lg glass-input text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" placeholder="Contoh: 08123456789" class="w-full px-3 py-2.5 rounded-lg glass-input text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Alamat</label>
                            <textarea name="alamat" rows="2" placeholder="Alamat pelanggan..." class="w-full px-3 py-2 rounded-lg glass-input text-sm"></textarea>
                        </div>
                    </div>

                    <!-- Detail Pesanan (Dinamis Multi Item) -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-300">Detail Pesanan</h3>
                            <button type="button" onclick="tambahItem()" class="text-xs bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 border border-amber-500/40 px-2.5 py-1 rounded-lg transition font-medium">
                                + Tambah Menu
                            </button>
                        </div>

                        <!-- Container Baris Menu -->
                        <div id="container-pesanan" class="space-y-3 max-h-60 overflow-y-auto pr-1">
                            <!-- Item Pertama (Default) -->
                            <div class="item-pesanan p-3 rounded-xl bg-black/20 border border-gray-700/60 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold text-amber-400/80">Item #1</span>
                                    <button type="button" onclick="hapusItem(this)" class="tombol-hapus hidden text-xs text-red-400 hover:text-red-300 transition">Hapus</button>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-2">
                                        <select name="produk_id[]" required onchange="hitungTotal()" class="select-produk w-full px-2.5 py-2 rounded-lg glass-input text-xs text-white cursor-pointer focus:bg-gray-900">
                                            <option value="" data-harga="0" class="bg-gray-900 text-gray-400">-- Pilih Menu --</option>
                                            <?php foreach ($produk_list as$item): ?>
                                                <option value="<?php echo $item['ProdukID']; ?>" data-harga="<?php echo $item['Harga']; ?>" class="bg-gray-900 text-white">
                                                    <?php echo htmlspecialchars($item['NamaProduk']); ?> - Rp <?php echo number_format($item['Harga'], 0, ',', '.'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="number" name="jumlah[]" min="1" value="1" required oninput="hitungTotal()" placeholder="Qty" class="input-jumlah w-full px-2.5 py-2 rounded-lg glass-input text-xs text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Display Total Harga (Baru) -->
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 flex justify-between items-center mt-4">
                        <div>
                            <span class="text-xs text-gray-400 block">Total Estimasi Pesanan</span>
                            <span class="text-xs text-amber-400/80" id="total-item-count">0 Item Dipilih</span>
                        </div>
                        <div class="text-right">
                            <span id="display-total-harga" class="text-xl font-bold text-amber-400">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" name="simpan" class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold rounded-xl shadow-lg transition duration-200 mt-4 text-sm transform hover:scale-102">
                        Simpan & Process Transaksi
                    </button>
                </form>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="glass-header text-center text-xs text-gray-400 py-4 mt-8">
        &copy; 2026 POS Cafe System - Theme Dark Glassmorphism
    </footer>

    <!-- JavaScript Tambah, Hapus Item, & Hitung Total Real-time -->
    <script>
        function tambahItem() {
            const container = document.getElementById('container-pesanan');
            const itemCount = container.getElementsByClassName('item-pesanan').length + 1;
            
            const div = document.createElement('div');
            div.className = 'item-pesanan p-3 rounded-xl bg-black/20 border border-gray-700/60 space-y-2';
            
            div.innerHTML = `
                <div class="flex justify-between items-center">
                    <span class="text-xs font-semibold text-amber-400/80">Item #${itemCount}</span>
                    <button type="button" onclick="hapusItem(this)" class="tombol-hapus text-xs text-red-400 hover:text-red-300 transition">Hapus</button>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2">
                        <select name="produk_id[]" required onchange="hitungTotal()" class="select-produk w-full px-2.5 py-2 rounded-lg glass-input text-xs text-white cursor-pointer focus:bg-gray-900">
                            <option value="" data-harga="0" class="bg-gray-900 text-gray-400">-- Pilih Menu --</option>
                            <?php foreach ($produk_list as $item): ?>
                                <option value="<?php echo $item['ProdukID']; ?>" data-harga="<?php echo $item['Harga']; ?>" class="bg-gray-900 text-white">
                                    <?php echo htmlspecialchars($item['NamaProduk']); ?> - Rp <?php echo number_format($item['Harga'], 0, ',', '.'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <input type="number" name="jumlah[]" min="1" value="1" required oninput="hitungTotal()" placeholder="Qty" class="input-jumlah w-full px-2.5 py-2 rounded-lg glass-input text-xs text-center">
                    </div>
                </div>
            `;
            
            container.appendChild(div);
            updateTombolHapus();
            hitungTotal();
        }

        function hapusItem(button) {
            const item = button.closest('.item-pesanan');
            item.remove();
            updateTombolHapus();
            
            // Re-index label angka item
            const items = document.querySelectorAll('#container-pesanan .item-pesanan');
            items.forEach((el, index) => {
                el.querySelector('span').innerText = `Item #${index + 1}`;
            });

            hitungTotal();
        }

        function updateTombolHapus() {
            const items = document.querySelectorAll('#container-pesanan .item-pesanan');
            items.forEach(el => {
                const btn = el.querySelector('.tombol-hapus');
                if (items.length === 1) {
                    btn.classList.add('hidden');
                } else {
                    btn.classList.remove('hidden');
                }
            });
        }

        // Fungsi Hitung Total Keseluruhan
        function hitungTotal() {
            let totalHarga = 0;
            let totalQty = 0;

            const items = document.querySelectorAll('.item-pesanan');   

            items.forEach(item => {
                const selectProduk = item.querySelector('.select-produk');
                const inputJumlah = item.querySelector('.input-jumlah');

                if (selectProduk && inputJumlah) {
                    const selectedOption = selectProduk.options[selectProduk.selectedIndex];
                    const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                    const jumlah = parseInt(inputJumlah.value) || 0;

                    if (harga > 0 && jumlah > 0) {
                        totalHarga += harga * jumlah;
                        totalQty += jumlah;
                    }
                }
            });

            // Update Tampilan Total
            document.getElementById('display-total-harga').innerText = 'Rp ' + totalHarga.toLocaleString('id-ID');
            document.getElementById('total-item-count').innerText = totalQty + ' Item Dipilih';
        }
    </script>

</body>
</html>