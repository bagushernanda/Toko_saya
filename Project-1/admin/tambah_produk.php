<?php
session_start();
include '../koneksi.php';

// Proteksi Login
if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>";
    exit();
}
if (isset($_POST['simpan'])) {
    // 1. Ambil data dari form produk utama
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori_id = $_POST['kategori_id'];
    $harga = $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    // Proses Gambar
    $nama_foto = $_FILES['foto']['name'];
    $lokasi = $_FILES['foto']['tmp_name'];

    if (!empty($lokasi)) {
        // Pindahkan file ke folder img
        move_uploaded_file($lokasi, "../img/" . $nama_foto);
        
        // 2. Simpan ke tabel produk utama 
        // Note: Stok awal kita set 0 karena akan dihitung otomatis dari total variasi
        $conn->query("INSERT INTO produk (nama, kategori_id, harga, stok, gambar, deskripsi) 
                      VALUES ('$nama', '$kategori_id', '$harga', 0, '$nama_foto', '$deskripsi')");
        
        // 3. Ambil ID Produk yang baru saja di-insert
        $id_produk_baru = $conn->insert_id;

        // 4. Ambil data variasi dari form (warna, ukuran, stok_v)
        $warna = $_POST['warna'];   // array dari input name="warna[]"
        $ukuran = $_POST['ukuran']; // array dari input name="ukuran[]"
        $stok_v = $_POST['stok_v']; // array dari input name="stok_v[]"

        $total_stok_produk = 0;

        foreach ($warna as $key => $val) {
            $w = mysqli_real_escape_string($conn, $val);
            $u = mysqli_real_escape_string($conn, $ukuran[$key]);
            $s = (int)$stok_v[$key];

            // Hanya simpan jika warna dan ukuran tidak kosong
            if (!empty($w) && !empty($u)) {
                $conn->query("INSERT INTO produk_variasi (id_produk, warna, ukuran, stok) 
                              VALUES ('$id_produk_baru', '$w', '$u', '$s')");
                
                // Tambahkan ke hitungan total stok
                $total_stok_produk += $s;
            }
        }

        // 5. Update kolom stok di tabel produk utama agar sinkron dengan total variasi
        $conn->query("UPDATE produk SET stok = '$total_stok_produk' WHERE id = '$id_produk_baru'");

        echo "<script>alert('Produk dan Variasi berhasil ditambahkan!');</script>";
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
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Deskripsi</label>
                <textarea name="deskripsi" rows="5" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" required></textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight:bold; margin-bottom:5px;">Foto Produk</label>
                <input type="file" name="foto" style="width:100%;" required>
            </div>
            <div class="card" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px;">
                <h4><i class="fa-solid fa-layer-group"></i> Atur Variasi & Stok</h4>
                <p style="font-size: 12px; color: #666;">Contoh: Warna [Putih], Ukuran [M], Stok [50]</p>
    
                <div id="container-variasi">
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="warna[]" placeholder="Warna" class="form-control" required>
                    <input type="text" name="ukuran[]" placeholder="Ukuran" class="form-control" required>
                    <input type="number" name="stok_v[]" placeholder="Stok" class="form-control" style="width: 80px;" required>
                    </div>
                </div>
    
                <button type="button" onclick="tambahBaris()" style="background: #341f97; color: white; border:none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                    + Tambah Kombinasi Lain
                </button>
            </div>

<script>
function tambahBaris() {
    let container = document.getElementById('container-variasi');
    let div = document.createElement('div');
    div.style.display = "flex";
    div.style.gap = "10px";
    div.style.marginBottom = "10px";
    div.innerHTML = `
        <input type="text" name="warna[]" placeholder="Warna" class="form-control" required>
        <input type="text" name="ukuran[]" placeholder="Ukuran" class="form-control" required>
        <input type="number" name="stok_v[]" placeholder="Stok" class="form-control" style="width: 80px;" required>
        <button type="button" onclick="this.parentElement.remove()" style="background:red; color:white; border:none; border-radius:5px; padding:0 10px;">X</button>
    `;
    container.appendChild(div);
}
</script>

            <button name="simpan" style="background:#2ed573; color:white; border:none; padding:12px 20px; border-radius:8px; font-weight:bold; cursor:pointer; width:100%;">
                <i class="fa-solid fa-save"></i> SIMPAN PRODUK
            </button>
            <a href="produk.php" style="display:block; text-align:center; margin-top:15px; color:#747d8c; text-decoration:none;">Batal</a>
        </form>
    </div>

</body>
</html>