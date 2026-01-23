<?php
session_start();
// Hapus session pelanggan
unset($_SESSION["pelanggan"]);
// Atau hapus semua session
// session_destroy();

echo "<script>alert('Anda telah logout');</script>";
echo "<script>location='login_pelanggan.php';</script>";
?>