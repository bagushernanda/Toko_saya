<?php
$host = "localhost";
$user = "root";     // Default XAMPP
$pass = "";         // Default XAMPP (kosong)
$db   = "toko_online";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>