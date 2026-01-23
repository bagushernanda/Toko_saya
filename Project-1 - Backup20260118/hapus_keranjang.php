<?php
session_start();
$id_variasi = $_GET["id"];
unset($_SESSION["keranjang"][$id_variasi]);

echo "<script>alert('Produk telah dihapus dari keranjang');</script>";
echo "<script>location='keranjang.php';</script>";
?>