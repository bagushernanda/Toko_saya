<?php
session_start();
$id_produk = $_POST['id_produk'];
$jumlah = $_POST['jumlah'];

// Update jumlah di session
$_SESSION['keranjang'][$id_produk] = $jumlah;

echo "<script>alert('Jumlah produk berhasil diperbarui');</script>";
echo "<script>location='keranjang.php';</script>";
?>