<?php
session_start();
include '../koneksi.php';

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $nama_foto = $_FILES['gambar']['name'];
    $lokasi = $_FILES['gambar']['tmp_name'];

    // Jika ada foto yang diupload
    if (!empty($lokasi)) {
        // Pindahkan file ke folder img
        move_uploaded_file($lokasi, "../img/" . $nama_foto);
        $conn->query("INSERT INTO kategori (nama_kategori, gambar_kategori) VALUES ('$nama', '$nama_foto')");
    } else {
        $conn->query("INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
    }

    echo "<script>alert('Kategori tersimpan'); location='kategori.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body style="background: #f1f2f6; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div style="background: white; padding: 30px; border-radius: 15px; width: 400px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h3>Tambah Kategori</h3>
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom:15px;">
                <label>Nama Kategori</label>
                <input type="text" name="nama" class="form-control" style="width:100%; padding:10px; margin-top:5px;" required>
            </div>
            <div style="margin-bottom:15px;">
                <label>Gambar Kategori</label>
                <input type="file" name="gambar" style="width:100%; margin-top:5px;" required>
            </div>
            <button name="simpan" style="background: #2ed573; color: white; border: none; width: 100%; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold;">Simpan</button>
            <a href="kategori.php" style="display:block; text-align:center; margin-top:10px; color:#999; text-decoration:none;">Batal</a>
        </form>
    </div>
</body>
</html>