<?php
include 'koneksi.php';
$warna = $_POST['warna'];
$id_p = $_POST['id_produk'];

$ambil = $conn->query("SELECT * FROM produk_variasi WHERE id_produk='$id_p' AND warna='$warna' AND stok > 0");

echo "<option value=''>-- Pilih Ukuran --</option>";
while($row = $ambil->fetch_assoc()){
    echo "<option value='".$row['id_variasi']."'>".$row['ukuran']." (Stok: ".$row['stok'].")</option>";
}
?>