<?php
session_start();
session_destroy(); // Ini akan menghapus semua isi keranjang
header("location:produk.php");
?>