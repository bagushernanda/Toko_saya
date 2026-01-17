<?php
include '../koneksi.php';

$ambil = $conn->query("SELECT * FROM produk WHERE id='$_GET[id]'");
$data = $ambil->fetch_assoc();
$foto = $data['gambar'];

if (file_exists("../img/$foto")) {
    unlink("../img/$foto"); // Menghapus file di folder img
}

$conn->query("DELETE FROM produk WHERE id='$_GET[id]'");

echo "<script>alert('Produk terhapus');</script>";
echo "<script>location='produk.php';</script>";
?>