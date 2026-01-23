<?php
session_start();
include '../koneksi.php';

$id = $_GET['id'];
$ambil = $conn->query("SELECT * FROM kategori WHERE id='$id'");
$row = $ambil->fetch_assoc();

if (isset($_POST['ubah'])) {
    $nama = $_POST['nama'];
    $nama_foto = $_FILES['gambar']['name'];
    $lokasi = $_FILES['gambar']['tmp_name'];

    // Jika user mengupload foto baru
    if (!empty($lokasi)) {
        // 1. Hapus foto lama jika ada di folder
        if (file_exists("../img/" . $row['gambar_kategori'])) {
            unlink("../img/" . $row['gambar_kategori']);
        }
        // 2. Upload foto baru
        move_uploaded_file($lokasi, "../img/" . $nama_foto);
        
        $conn->query("UPDATE kategori SET nama_kategori='$nama', gambar_kategori='$nama_foto' WHERE id='$id'");
    } else {
        // Jika tidak ganti foto, update nama saja
        $conn->query("UPDATE kategori SET nama_kategori='$nama' WHERE id='$id'");
    }

    echo "<script>alert('Kategori diperbarui'); location='kategori.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body style="background: #f1f2f6; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div style="background: white; padding: 30px; border-radius: 15px; width: 400px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h3>Edit Kategori</h3>
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom:15px;">
                <label>Nama Kategori</label>
                <input type="text" name="nama" class="form-control" value="<?= $row['nama_kategori'] ?>" style="width:100%; padding:10px; margin-top:5px;" required>
            </div>
            
            <div style="margin-bottom:15px;">
                <label>Gambar Saat Ini</label><br>
                <img src="../img/<?= $row['gambar_kategori'] ?>" width="100" style="margin: 5px 0; border-radius: 5px;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Ganti Gambar (Kosongkan jika tidak diubah)</label>
                <input type="file" name="gambar" style="width:100%; margin-top:5px;">
            </div>

            <button name="ubah" style="background: #ffa502; color: white; border: none; width: 100%; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold;">Perbarui</button>
            <a href="kategori.php" style="display:block; text-align:center; margin-top:10px; color:#999; text-decoration:none;">Batal</a>
        </form>
    </div>
</body>
</html>