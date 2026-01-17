<?php
session_start();
include '../koneksi.php';

// Proteksi Login
if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>";
    exit();
}

// 1. Ambil data produk lama berdasarkan ID
$id_produk = $_GET['id'];
$ambil = $conn->query("SELECT * FROM produk WHERE id='$id_produk'");
$row = $ambil->fetch_assoc();

// 2. Jika tombol simpan/ubah diklik
if (isset($_POST['ubah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori_id = $_POST['kategori_id'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $nama_foto = $_FILES['foto']['name'];
    $lokasi = $_FILES['foto']['tmp_name'];

    // Logika Update: Cek apakah user upload foto baru atau tidak
    if (!empty($lokasi)) {
        // Hapus foto lama di folder img
        if (file_exists("../img/".$row['gambar'])) {
            unlink("../img/".$row['gambar']);
        }
        // Upload foto baru
        move_uploaded_file($lokasi, "../img/".$nama_foto);
        
        $conn->query("UPDATE produk SET 
            nama='$nama', 
            kategori_id='$kategori_id', 
            harga='$harga', 
            stok='$stok', 
            gambar='$nama_foto', 
            deskripsi='$deskripsi' 
            WHERE id='$id_produk'");
    } else {
        // Jika foto tidak diganti, query tanpa mengubah kolom gambar
        $conn->query("UPDATE produk SET 
            nama='$nama', 
            kategori_id='$kategori_id', 
            harga='$harga', 
            stok='$stok', 
            deskripsi='$deskripsi' 
            WHERE id='$id_produk'");
    }

    echo "<script>alert('Data produk telah diperbarui!');</script>";
    echo "<script>location='produk.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: #f1f2f6; padding: 40px;">

    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom: 20px;"><i class="fa-solid fa-pen-to-square"></i> Edit Produk</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Nama Produk</label>
                <input type="text" name="nama" value="<?php echo $row['nama']; ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Kategori Produk</label>
                <select name="kategori_id" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php 
                    $ambil_kat = $conn->query("SELECT * FROM kategori");
                    while($kat = $ambil_kat->fetch_assoc()):
                    ?>
                    <option value="<?php echo $kat['id']; ?>" <?php echo ($kat['id'] == $row['kategori_id']) ? 'selected' : ''; ?>>
                        <?php echo $kat['nama_kategori']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Harga (Rp)</label>
                <input type="number" name="harga" value="<?php echo $row['harga']; ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Stok</label>
                <input type="number" name="stok" value="<?php echo $row['stok']; ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Deskripsi</label>
                <textarea name="deskripsi" rows="5" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required><?php echo $row['deskripsi']; ?></textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Foto Produk (Kosongkan jika tidak ingin ganti)</label>
                <div style="margin-bottom: 10px;">
                    <img src="../img/<?php echo $row['gambar']; ?>" width="100" style="border-radius: 8px; border: 1px solid #eee;">
                </div>
                <input type="file" name="foto" style="width:100%;">
            </div>

            <button name="ubah" style="background:#ffa502; color:white; border:none; padding:12px 20px; border-radius:8px; font-weight:bold; cursor:pointer; width:100%;">
                <i class="fa-solid fa-save"></i> SIMPAN PERUBAHAN
            </button>
            <a href="produk.php" style="display:block; text-align:center; margin-top:15px; color:#747d8c; text-decoration:none;">Batal</a>
        </form>
    </div>

</body>
</html>