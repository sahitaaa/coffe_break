<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set Timezone ke WIB
    date_default_timezone_set('Asia/Jakarta');

    // 1. Ambil data pelanggan
    $nama_pelanggan = mysqli_real_escape_string($koneksi,$_POST['nama_pelanggan']);
    $alamat         = mysqli_real_escape_string($koneksi, $_POST['alamat']);$nomor_telepon  = mysqli_real_escape_string($koneksi,$_POST['nomor_telepon']);

    // Ambil data array produk & jumlah dari form multi-item
    $produk_ids     =$_POST['produk_id'];
    $jumlah_produks =$_POST['jumlah'];

    // 2. Simpan ke Tabel `pelanggan`
    $q_pelanggan = "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) 
                    VALUES ('$nama_pelanggan', '$alamat', '$nomor_telepon')";
    if (!mysqli_query($koneksi,$q_pelanggan)) {
        die("Gagal simpan pelanggan: " . mysqli_error($koneksi));
    }
    $pelanggan_id = mysqli_insert_id($koneksi);

    // 3. Cek ketersediaan stok semua produk
    $total_harga = 0;
    $detail_item = [];

    foreach ($produk_ids as $index =>$p_id) {
        $produk_id = (int)$p_id;
        $jumlah    = (int)$jumlah_produks[$index];

        if ($produk_id > 0 &&$jumlah > 0) {
            $q_produk    = mysqli_query($koneksi, "SELECT NamaProduk, Harga, Stok FROM produk WHERE ProdukID = '$produk_id'");
            $data_produk = mysqli_fetch_assoc($q_produk);

            if (!$data_produk) {
                die("Produk tidak ditemukan!");
            }

            if ($data_produk['Stok'] <$jumlah) {
                echo "<script>
                        alert('Stok " . addslashes($data_produk['NamaProduk']) . " tidak mencukupi!'); 
                        window.location='index.php';
                      </script>";
                exit;
            }

            $subtotal     = $data_produk['Harga'] *$jumlah;
            $total_harga +=$subtotal;

            $detail_item[] = [
                'produk_id' => $produk_id,
                'jumlah'    => $jumlah,
                'subtotal'  => $subtotal,
                'stok_lama' => $data_produk['Stok']
            ];
        }
    }

    if (empty($detail_item)) {
        echo "<script>alert('Pilih minimal satu produk!'); window.location='index.php';</script>";
        exit;
    }

    // Tanggal + Waktu Lengkap (Tahun-Bulan-Hari Jam:Menit:Detik)
    $tanggal_waktu = date('Y-m-d H:i:s');

    // 4. Simpan ke Tabel `penjualan`
    $q_penjualan = "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID) 
                    VALUES ('$tanggal_waktu', '$total_harga', '$pelanggan_id')";
    if (!mysqli_query($koneksi,$q_penjualan)) {
        die("Gagal simpan penjualan: " . mysqli_error($koneksi));
    }
    $penjualan_id = mysqli_insert_id($koneksi);

    // 5. Simpan ke Tabel `detailpenjualan` dan Update Stok
    foreach ($detail_item as $item) {$p_id     = $item['produk_id'];$qty      = $item['jumlah'];$subtotal = $item['subtotal'];$stok_baru= $item['stok_lama'] -$qty;

        $q_detail = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) 
                     VALUES ('$penjualan_id', '$p_id', '$qty', '$subtotal')";
        if (!mysqli_query($koneksi,$q_detail)) {
            die("Gagal simpan detail penjualan: " . mysqli_error($koneksi));
        }

        mysqli_query($koneksi, "UPDATE produk SET Stok = '$stok_baru' WHERE ProdukID = '$p_id'");
    }

    // 6. Selesai
    echo "<script>
            alert('Transaksi Berhasil!');
            window.location='index.php';
          </script>";
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>