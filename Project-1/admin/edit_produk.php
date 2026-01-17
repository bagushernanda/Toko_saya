<?php
session_start();
include '../koneksi.php';

// Proteksi Login
if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>";
    exit();
}

// 1. Ambil data produk utama berdasarkan ID
$id_produk = $_GET['id'];
$ambil = $conn->query("SELECT * FROM produk WHERE id='$id_produk'");
$row = $ambil->fetch_assoc();

// 2. Jika tombol simpan/ubah diklik
if (isset($_POST['ubah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori_id = $_POST['kategori_id'];
    $harga = $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $nama_foto = $_FILES['foto']['name'];
    $lokasi = $_FILES['foto']['tmp_name'];

    // A. Update Data Produk Utama
    if (!empty($lokasi)) {
        if (file_exists("../img/".$row['gambar'])) { unlink("../img/".$row['gambar']); }
        move_uploaded_file($lokasi, "../img/".$nama_foto);
        $conn->query("UPDATE produk SET nama='$nama', kategori_id='$kategori_id', harga='$harga', gambar='$nama_foto', deskripsi='$deskripsi' WHERE id='$id_produk'");
    } else {
        $conn->query("UPDATE produk SET nama='$nama', kategori_id='$kategori_id', harga='$harga', deskripsi='$deskripsi' WHERE id='$id_produk'");
    }

    // B. Kelola Variasi (Hapus variasi lama dan masukkan yang baru agar sinkron)
    $conn->query("DELETE FROM produk_variasi WHERE id_produk = '$id_produk'");
    
    $warnas = $_POST['warna'];
    $ukurans = $_POST['ukuran'];
    $stoks = $_POST['stok_variasi'];
    $total_stok = 0;

    foreach ($warnas as $key => $warna) {
        $warna_val = mysqli_real_escape_string($conn, $warna);
        $ukuran_val = mysqli_real_escape_string($conn, $ukurans[$key]);
        $stok_val = $stoks[$key];
        
        $conn->query("INSERT INTO produk_variasi (id_produk, warna, ukuran, stok) 
                      VALUES ('$id_produk', '$warna_val', '$ukuran_val', '$stok_val')");
        $total_stok += $stok_val;
    }

    // C. Update Total Stok di Tabel Produk Utama
    $conn->query("UPDATE produk SET stok = '$total_stok' WHERE id = '$id_produk'");

    echo "<script>alert('Data produk dan variasi telah diperbarui!');</script>";
    echo "<script>location='produk.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - PlanetHanduk</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: #f1f2f6; padding: 40px;">

    <div style="max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom: 20px;"><i class="fa-solid fa-pen-to-square"></i> Edit Produk & Variasi</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="font-weight:bold;">Nama Produk</label>
                    <input type="text" name="nama" value="<?php echo $row['nama']; ?>" class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
                </div>
                <div>
                    <label style="font-weight:bold;">Kategori</label>
                    <select name="kategori_id" class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
                        <?php 
                        $ambil_kat = $conn->query("SELECT * FROM kategori");
                        while($kat = $ambil_kat->fetch_assoc()): ?>
                            <option value="<?php echo $kat['id']; ?>" <?php if($kat['id']==$row['kategori_id']) echo 'selected'; ?>>
                                <?php echo $kat['nama_kategori']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight:bold;">Harga (Rp)</label>
                <input type="number" name="harga" value="<?php echo $row['harga']; ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight:bold;">Deskripsi</label>
                <textarea name="deskripsi" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required><?php echo $row['deskripsi']; ?></textarea>
            </div>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #eee;">
                <h4 style="margin-bottom: 15px;"><i class="fa-solid fa-layer-group"></i> Pengaturan Variasi (Warna & Ukuran)</h4>
                <div id="container-variasi">
                    <?php 
                    $ambil_v = $conn->query("SELECT * FROM produk_variasi WHERE id_produk = '$id_produk'");
                    while($v = $ambil_v->fetch_assoc()):
                    ?>
                    <div class="row-variasi" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                        <input type="text" name="warna[]" placeholder="Warna" value="<?php echo $v['warna']; ?>" style="flex:2; padding:8px; border-radius:5px; border:1px solid #ddd;" required>
                        <input type="text" name="ukuran[]" placeholder="Ukuran" value="<?php echo $v['ukuran']; ?>" style="flex:1; padding:8px; border-radius:5px; border:1px solid #ddd;" required>
                        <input type="number" name="stok_variasi[]" placeholder="Stok" value="<?php echo $v['stok']; ?>" style="flex:1; padding:8px; border-radius:5px; border:1px solid #ddd;" required>
                        <button type="button" class="btn-hapus-v" style="background:#ff4757; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                    </div>
                    <?php endwhile; ?>
                </div>
                <button type="button" id="btn-tambah-v" style="background:#2ed573; color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; margin-top:10px;">
                    <i class="fa-solid fa-plus"></i> Tambah Variasi
                </button>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-weight:bold;">Foto Produk</label>
                <div style="margin-bottom: 10px;">
                    <img src="../img/<?php echo $row['gambar']; ?>" width="100" style="border-radius: 8px; border: 1px solid #eee;">
                </div>
                <input type="file" name="foto">
            </div>

            <button name="ubah" style="background:#ffa502; color:white; border:none; padding:15px; border-radius:8px; font-weight:bold; cursor:pointer; width:100%;">
                <i class="fa-solid fa-save"></i> SIMPAN PERUBAHAN
            </button>
            <a href="produk.php" style="display:block; text-align:center; margin-top:15px; color:#747d8c; text-decoration:none;">Batal</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function(){
        // Tambah baris variasi baru
        $("#btn-tambah-v").click(function(){
            var html = `
            <div class="row-variasi" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                <input type="text" name="warna[]" placeholder="Warna" style="flex:2; padding:8px; border-radius:5px; border:1px solid #ddd;" required>
                <input type="text" name="ukuran[]" placeholder="Ukuran" style="flex:1; padding:8px; border-radius:5px; border:1px solid #ddd;" required>
                <input type="number" name="stok_variasi[]" placeholder="Stok" style="flex:1; padding:8px; border-radius:5px; border:1px solid #ddd;" required>
                <button type="button" class="btn-hapus-v" style="background:#ff4757; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
            </div>`;
            $("#container-variasi").append(html);
        });

        // Hapus baris variasi
        $(document).on("click", ".btn-hapus-v", function(){
            $(this).closest(".row-variasi").remove();
        });
    });
    </script>
</body>
</html>