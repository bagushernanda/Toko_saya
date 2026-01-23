<?php
include 'koneksi.php';

$warna = $_POST['warna'];
$id_p = $_POST['id_produk'];

if ($_POST['type'] == 'get_ukuran') {
    $ambil = $conn->query("SELECT * FROM produk_variasi WHERE id_produk = '$id_p' AND variasi = '$warna'");
    
    echo '<option value="">-- Pilih Ukuran --</option>';
    while ($row = $ambil->fetch_assoc()) {
        echo "<option value='".$row['id_variasi']."' 
                      data-harga='".$row['harga_variasi']."' 
                      data-stok='".$row['stok']."'>
                ".$row['ukuran']."
              </option>";
    }
}
?>