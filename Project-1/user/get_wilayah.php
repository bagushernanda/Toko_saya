<?php
include '../koneksi.php';

$action = $_POST['action'];
$id = $_POST['id'];

if ($action == 'get_kabupaten') {
    echo '<option value="">-- Pilih Kota/Kabupaten --</option>';
    $query = $conn->query("SELECT * FROM reg_regencies WHERE province_id = '$id' ORDER BY name ASC");
    while ($row = $query->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    }
}

if ($action == 'get_kecamatan') {
    echo '<option value="">-- Pilih Kecamatan --</option>';
    $query = $conn->query("SELECT * FROM reg_districts WHERE regency_id = '$id' ORDER BY name ASC");
    while ($row = $query->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    }
}

if ($action == 'get_desa') {
    echo '<option value="">-- Pilih Kelurahan/Desa --</option>';
    $query = $conn->query("SELECT * FROM reg_villages WHERE district_id = '$id' ORDER BY name ASC");
    while ($row = $query->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    }
}
?>