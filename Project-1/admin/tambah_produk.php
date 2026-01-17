<?php
session_start();
include '../koneksi.php';

// Proteksi Login
if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>";
    exit();
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori_id = $_POST['kategori_id']; // Mengambil ID kategori dari dropdown
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Proses Gambar
    $nama_foto = $_FILES['foto']['name'];
    $lokasi = $_FILES['foto']['tmp_name'];

    if (!empty($lokasi)) {
        // Pindahkan file ke folder img yang ada di luar folder admin
        move_uploaded_file($lokasi, "../img/".$nama_foto);
        
        // Pastikan kolom di database Anda sesuai (misal: kategori_id atau kategori_id)
        $conn->query("INSERT INTO produk (nama, kategori_id, harga, stok, gambar, deskripsi) 
                      VALUES ('$nama', '$kategori_id', '$harga', '$stok', '$nama_foto', '$deskripsi')");
       
        echo "<script>alert('Produk berhasil ditambahkan!');</script>";
        echo "<script>location='produk.php';</script>";
    } else {
        echo "<script>alert('Gagal! Foto wajib diunggah.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: #f1f2f6; padding: 40px;">

    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom: 20px;"><i class="fa-solid fa-plus-circle"></i> Tambah Produk Baru</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Nama Produk</label>
                <input type="text" name="nama" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Kategori Produk</label>
                <select name="kategori_id" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php 
                    // Ambil data kategori dari database
                    $ambil_kat = $conn->query("SELECT * FROM kategori");
                    while($kat = $ambil_kat->fetch_assoc()):
                    ?>
                    <option value="<?php echo $kat['id']; ?>">
                        <?php echo $kat['nama_kategori']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Harga (Rp)</label>
                <input type="number" name="harga" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Stok</label>
                <input type="number" name="stok" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Deskripsi</label>
                <textarea name="deskripsi" rows="5" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required></textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Foto Produk</label>
                <input type="file" name="foto" style="width:100%;" required>
            </div>

            <button name="simpan" style="background:#2ed573; color:white; border:none; padding:12px 20px; border-radius:8px; font-weight:bold; cursor:pointer; width:100%;">
                <i class="fa-solid fa-save"></i> SIMPAN PRODUK
            </button>
            <a href="produk.php" style="display:block; text-align:center; margin-top:15px; color:#747d8c; text-decoration:none;">Batal</a>
        </form>
    </div>

</body>
</html>