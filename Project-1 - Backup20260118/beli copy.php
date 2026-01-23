<?php
session_start();
// Ambil ID produk dari URL
$id_produk = $_GET['id'];

// Cek apakah ada jumlah yang dikirim via POST, jika tidak default 1
$jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

// Jika produk sudah ada di keranjang, tambahkan jumlahnya
if (isset($_SESSION['keranjang'][$id_produk])) {
    $_SESSION['keranjang'][$id_produk] += $jumlah;
} else {
    // Jika belum ada, buat baru dengan jumlah tersebut
    $_SESSION['keranjang'][$id_produk] = $jumlah;
}

// Alihkan kembali ke halaman keranjang
echo "<script>alert('Produk telah ditambahkan ke keranjang');</script>";
echo "<script>location='keranjang.php';</script>";
?>