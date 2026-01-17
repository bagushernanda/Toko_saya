<?php
include 'koneksi.php';

if (isset($_POST['id_provinsi'])) {
    $id = $_POST['id_provinsi'];
    $ambil = $conn->query("SELECT * FROM reg_regencies WHERE province_id = '$id' ORDER BY name ASC");
    echo "<option value=''>-- Pilih Kabupaten/Kota --</option>";
    while ($row = $ambil->fetch_assoc()) echo "<option value='".$row['id']."'>".$row['name']."</option>";

} elseif (isset($_POST['id_kota'])) {
    $id = $_POST['id_kota'];
    $ambil = $conn->query("SELECT * FROM reg_districts WHERE regency_id = '$id' ORDER BY name ASC");
    echo "<option value=''>-- Pilih Kecamatan --</option>";
    while ($row = $ambil->fetch_assoc()) echo "<option value='".$row['id']."'>".$row['name']."</option>";

} elseif (isset($_POST['id_kecamatan'])) {
    $id = $_POST['id_kecamatan'];
    $ambil = $conn->query("SELECT * FROM reg_villages WHERE district_id = '$id' ORDER BY name ASC");
    echo "<option value=''>-- Pilih Kelurahan/Desa --</option>";
    while ($row = $ambil->fetch_assoc()) echo "<option value='".$row['id']."'>".$row['name']."</option>";
}
?>