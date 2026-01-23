<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>";
    exit();
}

$id_produk = $_GET['id'];
$ambil = $conn->query("SELECT * FROM produk WHERE id='$id_produk'");
$row = $ambil->fetch_assoc();

if (isset($_POST['ubah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori_id = $_POST['kategori_id'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // 1. Update Tabel Produk Utama
    $nama_foto = $_FILES['foto']['name'];
    $lokasi = $_FILES['foto']['tmp_name'];

    if (!empty($lokasi)) {
        if (file_exists("../img/".$row['gambar'])) { unlink("../img/".$row['gambar']); }
        move_uploaded_file($lokasi, "../img/".$nama_foto);
        $conn->query("UPDATE produk SET nama='$nama', kategori_id='$kategori_id', gambar='$nama_foto', deskripsi='$deskripsi' WHERE id='$id_produk'");
    } else {
        $conn->query("UPDATE produk SET nama='$nama', kategori_id='$kategori_id', deskripsi='$deskripsi' WHERE id='$id_produk'");
    }

    // 2. LOGIKA SMART SYNC VARIASI
    $current_db_ids = [];
    $res = $conn->query("SELECT id_variasi FROM produk_variasi WHERE id_produk='$id_produk'");
    while($v_row = $res->fetch_assoc()) { $current_db_ids[] = $v_row['id_variasi']; }

    $input_ids = $_POST['id_variasi']; // Hidden input ID
    $kept_ids = [];

    foreach ($_POST['variasi'] as $key => $val) {
        $id_v     = $input_ids[$key];
        $v_nama   = mysqli_real_escape_string($conn, $val);
        $v_ukuran = mysqli_real_escape_string($conn, $_POST['ukuran'][$key]);
        $v_harga  = $_POST['harga_variasi'][$key];
        $v_stok   = $_POST['stok'][$key];

        if (!empty($id_v)) {
            // Update data lama
            $conn->query("UPDATE produk_variasi SET variasi='$v_nama', ukuran='$v_ukuran', harga_variasi='$v_harga', stok='$v_stok' WHERE id_variasi='$id_v'");
            $kept_ids[] = $id_v;
        } else {
            // Insert data baru
            $conn->query("INSERT INTO produk_variasi (id_produk, variasi, ukuran, harga_variasi, stok) VALUES ('$id_produk', '$v_nama', '$v_ukuran', '$v_harga', '$v_stok')");
            $kept_ids[] = $conn->insert_id;
        }
    }

    // Hapus yang tidak ada di form
    $ids_to_delete = array_diff($current_db_ids, $kept_ids);
    if (!empty($ids_to_delete)) {
        $conn->query("DELETE FROM produk_variasi WHERE id_variasi IN (" . implode(',', $ids_to_delete) . ")");
    }

    // 3. Update Total Stok di Tabel Produk Utama (Manual Sync)
    $conn->query("UPDATE produk SET stok = (SELECT SUM(stok) FROM produk_variasi WHERE id_produk='$id_produk') WHERE id='$id_produk'");

    echo "<script>alert('Data produk dan variasi berhasil diperbarui');</script>";
    echo "<script>location='produk.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk - PlanetJersey</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style_admin.css">
</head>
<body style="background: #f1f2f6; padding: 40px 0;">

    <div class="container" style="max-width: 900px; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom: 25px; color: #2f3542;"><i class="fa-solid fa-pen-to-square"></i> Edit Produk</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom: 8px; font-weight:bold;">Nama Produk</label>
                    <input type="text" name="nama" class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" value="<?php echo $row['nama']; ?>" required>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom: 8px; font-weight:bold;">Kategori</label>
                    <select name="kategori_id" class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required>
                        <?php 
                        $kat = $conn->query("SELECT * FROM kategori");
                        while($k = $kat->fetch_assoc()):
                        ?>
                        <option value="<?php echo $k['id']; ?>" <?php if($k['id']==$row['kategori_id']) echo 'selected'; ?>>
                            <?php echo $k['nama_kategori']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom: 8px; font-weight:bold;">Foto Produk</label>
                <img src="../img/<?php echo $row['gambar']; ?>" width="100" style="margin-bottom: 10px; border-radius: 8px; border: 1px solid #eee;">
                <input type="file" name="foto" class="form-control" style="width:100%;">
                <small style="color: #747d8c;">*Kosongkan jika tidak ingin mengganti foto</small>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom: 8px; font-weight:bold;">Deskripsi</label>
                <textarea name="deskripsi" rows="5" class="form-control" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;"><?php echo $row['deskripsi']; ?></textarea>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 25px;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 18px; color: #2f3542;"><i class="fa-solid fa-tags"></i> Variasi Produk</h3>
                <button type="button" id="add-v" style="background: #2ed573; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 13px;">
                    <i class="fa-solid fa-plus"></i> Tambah Variasi
                </button>
            </div>

            <div id="container-variasi">
                <?php 
                $v_ambil = $conn->query("SELECT * FROM produk_variasi WHERE id_produk='$id_produk'");
                while($v_row = $v_ambil->fetch_assoc()):
                ?>
                <div class="row-v" style="display: flex; gap: 10px; margin-bottom: 12px; background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px solid #eee; align-items: center;">
                    <input type="hidden" name="id_variasi[]" value="<?php echo $v_row['id_variasi']; ?>">
                    <input type="text" name="variasi[]" placeholder="Warna" value="<?php echo $v_row['variasi']; ?>" style="flex: 2; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                    <input type="text" name="ukuran[]" placeholder="Ukuran" value="<?php echo $v_row['ukuran']; ?>" style="flex: 1; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                    <input type="number" name="harga_variasi[]" placeholder="Harga" value="<?php echo $v_row['harga_variasi']; ?>" style="flex: 2; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                    <input type="number" name="stok[]" placeholder="Stok" value="<?php echo $v_row['stok']; ?>" style="flex: 1; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                    <button type="button" class="btn-remove" style="background: #ff4757; color: white; border: none; width: 35px; height: 35px; border-radius: 5px; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
                </div>
                <?php endwhile; ?>
            </div>

            <div style="margin-top: 40px; display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <button type="submit" name="ubah" class="btn-save-produk">
                    <i class="fa-solid fa-save"></i> Simpan Perubahan
                </button>
                
                <a href="produk.php" class="btn-back-produk">
                    Kembali
                </a>
            </div>
        </form>
    </div>

    <script>
        // Fungsi Tambah Input Variasi
        document.getElementById('add-v').onclick = function() {
            var container = document.getElementById('container-variasi');
            var row = document.createElement('div');
            row.className = 'row-v';
            row.style = 'display: flex; gap: 10px; margin-bottom: 12px; background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px solid #eee; align-items: center;';
            row.innerHTML = `
                <input type="hidden" name="id_variasi[]" value="">
                <input type="text" name="variasi[]" placeholder="Warna" style="flex: 2; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                <input type="text" name="ukuran[]" placeholder="Ukuran" style="flex: 1; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                <input type="number" name="harga_variasi[]" placeholder="Harga" style="flex: 2; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                <input type="number" name="stok[]" placeholder="Stok" style="flex: 1; padding: 8px; border-radius: 5px; border: 1px solid #ddd;" required>
                <button type="button" class="btn-remove" style="background: #ff4757; color: white; border: none; width: 35px; height: 35px; border-radius: 5px; cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
            `;
            container.appendChild(row);
        };

        // Fungsi Hapus Baris Input (Event Delegation)
        document.getElementById('container-variasi').addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove') || e.target.parentElement.classList.contains('btn-remove')) {
                var targetRow = e.target.closest('.row-v');
                targetRow.remove();
            }
        });
    </script>
</body>
</html>