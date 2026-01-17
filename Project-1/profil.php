<?php 
session_start();
include 'koneksi.php'; 

// Proteksi Halaman: Jika belum login, tendang ke login_pelanggan.php
if (!isset($_SESSION['pelanggan'])) {
    echo "<script>alert('Silakan login terlebih dahulu');</script>";
    echo "<script>location='login_pelanggan.php';</script>";
    exit();
}

// Ambil ID pelanggan dari session
$id_pelanggan = $_SESSION['pelanggan']['id_pelanggan'];

// Ambil data terbaru dari database agar profil selalu update
$ambil = $conn->query("SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");
$detail = $ambil->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - PlanetHanduk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container { max-width: 800px; margin: 50px auto; padding: 20px; }
        .profile-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .profile-header { display: flex; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .profile-header i { font-size: 50px; color: #ff4757; margin-right: 20px; }
        .form-row { margin-bottom: 20px; }
        .form-row label { display: block; font-weight: bold; margin-bottom: 8px; color: #2f3542; }
        .form-row input, .form-row textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .btn-update { background: #ff4757; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-update:hover { background: #ff6b81; }
    </style>
</head>
<body>

    <header class="main-navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.php">Planet<span>Handuk</span></a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="produk.php">Katalog</a></li>
            </ul>
            <div class="nav-icons">
                <a href="logout_pelanggan.php" style="color: #ff4757; font-size: 14px; text-decoration: none; font-weight: bold;">Logout</a>
            </div>
        </div>
    </header>

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <i class="fa-solid fa-circle-user"></i>
                <div>
                    <h2>Profil Saya</h2>
                    <p style="color: #747d8c;">Kelola informasi profil Anda untuk keamanan akun</p>
                </div>
            </div>

            <form method="post">
                <div class="form-row">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo $detail['nama_pelanggan']; ?>" required>
                </div>
                <div class="form-row">
                    <label>Email</label>
                    <input type="email" value="<?php echo $detail['email_pelanggan']; ?>" readonly style="background: #f1f2f6; cursor: not-allowed;">
                    <small style="color: #a4b0be;">Email tidak dapat diubah</small>
                </div>
                <div class="form-row">
                    <label>Nomor Telepon / WA</label>
                    <input type="text" name="telepon" value="<?php echo $detail['telepon_pelanggan']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Provinsi</label>
                    <select name="provinsi" id="provinsi" class="form-control" required>
                        <option value="">-- Pilih Provinsi --</option>
                        <?php 
                        $ambil_prov = $conn->query("SELECT * FROM reg_provinces ORDER BY name ASC");
                        while($prov = $ambil_prov->fetch_assoc()){
                            echo "<option value='".$prov['id']."'>".$prov['name']."</option>";
                        }
                        ?>
                    </select>
                </div>  

                <div class="form-group">
                    <label>Kabupaten / Kota</label>
                    <select name="kota" id="kota" class="form-control" required>
                        <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kecamatan</label>
                    <select name="kecamatan" id="kecamatan" class="form-control" required>
                        <option value="">-- Pilih Kota Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelurahan / Desa</label>
                    <select name="kelurahan" id="kelurahan" class="form-control" required>
                        <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>Alamat Lengkap Pengiriman</label>
                    <textarea name="alamat" rows="4" required><?php echo $detail['alamat_pelanggan']; ?></textarea>
                </div>
                <button name="update" class="btn-update">Simpan Perubahan</button>
            </form>

            <?php 
            if (isset($_POST['update'])) {
                $nama = $_POST['nama'];
                $telepon = $_POST['telepon'];
                $alamat = $_POST['alamat'];

                $conn->query("UPDATE pelanggan SET nama_pelanggan='$nama', telepon_pelanggan='$telepon', alamat_pelanggan='$alamat' WHERE id_pelanggan='$id_pelanggan'");

                echo "<script>alert('Profil berhasil diperbarui!');</script>";
                echo "<script>location='profil.php';</script>";
            }
            ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
    // 1. Jika Provinsi berubah -> Ambil Kota
    $("#provinsi").on("change", function(){
        var id_prov = $(this).val();
        $.ajax({
            type: 'POST',
            url: 'ambil-wilayah.php',
            data: {id_provinsi: id_prov},
            success: function(hasil){
                $("#kota").html(hasil);
                $("#kecamatan").html("<option value=''>-- Pilih Kota Terlebih Dahulu --</option>");
                $("#kelurahan").html("<option value=''>-- Pilih Kecamatan Terlebih Dahulu --</option>");
            }
        });
    });

    // 2. Jika Kota berubah -> Ambil Kecamatan
    $("#kota").on("change", function(){
        var id_kota = $(this).val();
        $.ajax({
            type: 'POST',
            url: 'ambil-wilayah.php',
            data: {id_kota: id_kota},
            success: function(hasil){
                $("#kecamatan").html(hasil);
                $("#kelurahan").html("<option value=''>-- Pilih Kecamatan Terlebih Dahulu --</option>");
            }
        });
    });

    // 3. Jika Kecamatan berubah -> Ambil Kelurahan
    $("#kecamatan").on("change", function(){
        var id_kec = $(this).val();
        $.ajax({
            type: 'POST',
            url: 'ambil-wilayah.php',
            data: {id_kecamatan: id_kec},
            success: function(hasil){
                $("#kelurahan").html(hasil);
            }
        });
    });
});
</script>

</body>
</html>