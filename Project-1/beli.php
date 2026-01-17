<?php
session_start();
include 'koneksi.php';

// 1. Menangkap data dari form detail.php
if (isset($_POST['id_variasi'])) {
    $id_variasi = $_POST['id_variasi'];
    $jumlah = $_POST['jumlah'];

    // 2. Cek apakah produk dengan variasi ini sudah ada di keranjang
    if (isset($_SESSION['keranjang'][$id_variasi])) {
        // Jika sudah ada, tambahkan jumlahnya
        $_SESSION['keranjang'][$id_variasi] += $jumlah;
    } else {
        // Jika belum ada, buat baru
        $_SESSION['keranjang'][$id_variasi] = $jumlah;
    }

    echo "<script>alert('Produk berhasil dimasukkan ke keranjang');</script>";
    echo "<script>location='keranjang.php';</script>";

} else {
    // Jika diakses tanpa memilih variasi (langsung tembak URL)
    echo "<script>alert('Silakan pilih warna dan ukuran terlebih dahulu!');</script>";
    echo "<script>location='index.php';</script>";
}

// Alihkan kembali ke halaman keranjang
echo "<script>alert('Produk telah ditambahkan ke keranjang');</script>";
echo "<script>location='keranjang.php';</script>";
?>