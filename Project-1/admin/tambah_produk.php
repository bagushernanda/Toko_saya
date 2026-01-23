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
    $kategori_id = $_POST['kategori_id'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Proses Gambar
    $nama_foto = $_FILES['foto']['name'];
    $lokasi = $_FILES['foto']['tmp_name'];

    if (!empty($lokasi)) {
        move_uploaded_file($lokasi, "../img/" . $nama_foto);
        
        // 1. Simpan ke tabel produk utama 
        // Harga utama diambil dari harga variasi pertama sebagai default
        $harga_default = $_POST['harga_variasi'][0];
        $conn->query("INSERT INTO produk (nama, kategori_id, harga, stok, gambar, deskripsi) 
                      VALUES ('$nama', '$kategori_id', '$harga_default', 0, '$nama_foto', '$deskripsi')");
        
        $id_produk_baru = $conn->insert_id;

        // 2. Simpan Data Variasi & Hitung Total Stok
        $total_stok = 0;
        $input_variasi = $_POST['variasi'];
        $input_ukuran = $_POST['ukuran'];
        $input_stok = $_POST['stok_v'];
        $input_harga = $_POST['harga_variasi'];

        foreach ($input_variasi as $key => $val) {
            $v_nama = mysqli_real_escape_string($conn, $val);
            $v_ukuran = mysqli_real_escape_string($conn, $input_ukuran[$key]);
            $v_stok = $input_stok[$key];
            $v_harga = $input_harga[$key];

            $conn->query("INSERT INTO produk_variasi (id_produk, variasi, ukuran, harga_variasi, stok) 
                          VALUES ('$id_produk_baru', '$v_nama', '$v_ukuran', '$v_harga', '$v_stok')");
            
            $total_stok += $v_stok;
        }

        // 3. Update total stok di tabel produk utama
        $conn->query("UPDATE produk SET stok = '$total_stok' WHERE id = '$id_produk_baru'");

        echo "<script>alert('Produk berhasil ditambahkan');</script>";
        echo "<script>location='produk.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk - PlanetJersey</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container { max-width: 900px; margin: 40px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-control { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-top: 5px; }
        .row-v { display: flex; gap: 10px; margin-bottom: 12px; background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px solid #eee; align-items: center; }
        
        /* Gaya Tombol Biru (Sama dengan Edit Profil) */
        .btn-save-blue {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white; border: none; padding: 14px 30px; border-radius: 10px;
            font-weight: 600; cursor: pointer; transition: 0.3s; width: 100%;
        }
        .btn-save-blue:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4); }
    </style>
</head>
<body style="background: #f1f2f6;">

    <div class="form-container">
        <h2 style="margin-bottom: 25px; color: #2f3542;"><i class="fa-solid fa-plus-circle"></i> Tambah Produk Baru</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="font-weight:bold;">Nama Produk</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Jersey MU Home 2024" required>
                </div>
                <div class="form-group">
                    <label style="font-weight:bold;">Kategori</label>
                    <select name="kategori_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <?php 
                        $kat = $conn->query("SELECT * FROM kategori");
                        while($k = $kat->fetch_assoc()):
                        ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo $k['nama_kategori']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight:bold;">Foto Produk</label>
                <input type="file" name="foto" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label style="font-weight:bold;">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="form-control" placeholder="Tuliskan detail bahan, kualitas, dll..."></textarea>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 25px;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="font-size: 18px; color: #2f3542;"><i class="fa-solid fa-layer-group"></i> Variasi & Stok</h3>
                <button type="button" onclick="tambahBaris()" style="background: #2ed573; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 13px;">
                    <i class="fa-solid fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div id="container-variasi">
                <div class="row-v">
                    <input type="text" name="variasi[]" placeholder="Warna/Tipe" class="form-control" style="flex:2" required>
                    <input type="text" name="ukuran[]" placeholder="Ukuran" class="form-control" style="flex:1" required>
                    <input type="number" name="harga_variasi[]" placeholder="Harga Rp" class="form-control" style="flex:2" required>
                    <input type="number" name="stok_v[]" placeholder="Stok" class="form-control" style="flex:1" required>
                    <button type="button" style="background:transparent; border:none; color:#ccc; cursor:not-allowed;"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>

            <div style="margin-top: 40px; display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <button name="simpan" class="btn-save-blue">
                    <i class="fa-solid fa-check-circle"></i> Simpan Produk
                </button>
                <a href="produk.php" style="text-align: center; padding: 14px; background: #dfe4ea; color: #2f3542; text-decoration: none; border-radius: 10px; font-weight: bold;">Batal</a>
            </div>
        </form>
    </div>

    <script>
    function tambahBaris() {
        let container = document.getElementById('container-variasi');
        let div = document.createElement('div');
        div.className = "row-v";
        div.innerHTML = `
            <input type="text" name="variasi[]" placeholder="Warna/Tipe" class="form-control" style="flex:2" required>
            <input type="text" name="ukuran[]" placeholder="Ukuran" class="form-control" style="flex:1" required>
            <input type="number" name="harga_variasi[]" placeholder="Harga Rp" class="form-control" style="flex:2" required>
            <input type="number" name="stok_v[]" placeholder="Stok" class="form-control" style="flex:1" required>
            <button type="button" onclick="this.parentElement.remove()" style="background:#ff4757; color:white; border:none; border-radius:8px; padding:10px; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
        `;
        container.appendChild(div);
    }
    </script>
</body>
</html>