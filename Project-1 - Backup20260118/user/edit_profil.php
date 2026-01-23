<?php 
session_start();
include '../koneksi.php'; // Pastikan path koneksi sudah benar

// Proteksi Halaman
if (!isset($_SESSION['pelanggan'])) {
    echo "<script>location='login_pelanggan.php';</script>";
    exit();
}

$id_pelanggan = $_SESSION['pelanggan']['id_pelanggan'];
$ambil = $conn->query("SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");
$detail = $ambil->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil - PlanetHanduk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f1f2f6; font-family: 'Poppins', sans-serif; }
        .edit-card { max-width: 800px; margin: 40px auto; background: white; padding: 35px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); }
        .profile-section { text-align: center; margin-bottom: 30px; position: relative; }
        .profile-section img { width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #ff4757; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #333; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; transition: 0.3s; }
        .form-control:focus { border-color: #ff4757; outline: none; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn-save { background: #ff4757; color: white; padding: 15px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; font-size: 1rem; margin-top: 20px; }
        .btn-save:hover { background: #e84118; }
        @media (max-width: 600px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="edit-card">
    <h3 style="text-align: center;"><i class="fa-solid fa-user-gear"></i> Perbarui Profil</h3>
    <hr style="margin: 20px 0; opacity: 0.1;">
    
    <form method="post" enctype="multipart/form-data">
        <div class="profile-section">
            <?php if(!empty($detail['foto_profil'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($detail['foto_profil']); ?>" id="preview">
            <?php else: ?>
                <img src="https://via.placeholder.com/150" id="preview">
            <?php endif; ?>
            <div style="margin-top: 10px;">
                <label for="foto" style="cursor: pointer; color: #ff4757; font-weight: bold; font-size: 0.8rem;">
                    <i class="fa-solid fa-camera"></i> Ubah Foto Profil
                </label>
                <input type="file" name="foto_profil" id="foto" style="display:none" accept="image/*" onchange="previewImage(this)">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?php echo $detail['nama_pelanggan']; ?>" required>
            </div>
            <div class="form-group">
                <label>Telepon / WA</label>
                <input type="text" name="telepon" class="form-control" value="<?php echo $detail['telepon_pelanggan']; ?>" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Provinsi</label>
                <select id="provinsi" name="provinsi" class="form-control" required>
                    <option value="">-- Pilih Provinsi --</option>
                    <?php 
                    $prov = $conn->query("SELECT * FROM reg_provinces ORDER BY name ASC");
                    while($p = $prov->fetch_assoc()) {
                        echo "<option value='".$p['id']."'>".$p['name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kota / Kabupaten</label>
                <select id="kabupaten" name="kabupaten" class="form-control" required disabled>
                    <option value="">-- Pilih Provinsi Dahulu --</option>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Kecamatan</label>
                <select id="kecamatan" name="kecamatan" class="form-control" required disabled>
                    <option value="">-- Pilih Kabupaten Dahulu --</option>
                </select>
            </div>
            <div class="form-group">
                <label>Kelurahan / Desa</label>
                <select id="desa" name="desa" class="form-control" required disabled>
                    <option value="">-- Pilih Kecamatan Dahulu --</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Detail Alamat (Jalan, No Rumah, Blok, RT/RW)</label>
            <textarea name="alamat_manual" class="form-control" rows="3" placeholder="Contoh: Jl. Melati No. 45, RT 05 RW 01" required></textarea>
            <small style="color: #888;">Alamat saat ini: <?php echo $detail['alamat_pelanggan']; ?></small>
        </div>

        <button name="simpan" class="btn-save">SIMPAN PERUBAHAN</button>
        <a href="profil.php" style="display:block; text-align:center; margin-top:15px; color:#999; text-decoration:none;">Batal</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Fungsi Preview Gambar
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { $('#preview').attr('src', e.target.result); }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    // Dropdown Wilayah Bertingkat
    $('#provinsi').change(function() {
        var id_prov = $(this).val();
        $.ajax({
            type: 'POST', url: 'get_wilayah.php', data: {action: 'get_kabupaten', id: id_prov},
            success: function(html) {
                $('#kabupaten').html(html).prop('disabled', false);
                $('#kecamatan').html('<option>-- Pilih Kabupaten Dahulu --</option>').prop('disabled', true);
                $('#desa').html('<option>-- Pilih Kecamatan Dahulu --</option>').prop('disabled', true);
            }
        });
    });

    $('#kabupaten').change(function() {
        var id_kab = $(this).val();
        $.ajax({
            type: 'POST', url: 'get_wilayah.php', data: {action: 'get_kecamatan', id: id_kab},
            success: function(html) {
                $('#kecamatan').html(html).prop('disabled', false);
                $('#desa').html('<option>-- Pilih Kecamatan Dahulu --</option>').prop('disabled', true);
            }
        });
    });

    $('#kecamatan').change(function() {
        var id_kec = $(this).val();
        $.ajax({
            type: 'POST', url: 'get_wilayah.php', data: {action: 'get_desa', id: id_kec},
            success: function(html) { $('#desa').html(html).prop('disabled', false); }
        });
    });
});
</script>

<?php 
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telp = mysqli_real_escape_string($conn, $_POST['telepon']);
    $detail_jalan = mysqli_real_escape_string($conn, $_POST['alamat_manual']);

    // 1. Logika Foto BLOB
    $foto_sql = "";
    if(!empty($_FILES['foto_profil']['tmp_name'])) {
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $foto_blob = addslashes(file_get_contents($file_tmp));
        $foto_sql = ", foto_profil = '$foto_blob'";
    }

    // 2. Logika Nama Wilayah
    $id_prov = $_POST['provinsi'];
    $id_kab  = $_POST['kabupaten'];
    $id_kec  = $_POST['kecamatan'];
    $id_desa = $_POST['desa'];

    $q_prov = $conn->query("SELECT name FROM reg_provinces WHERE id='$id_prov'")->fetch_assoc();
    $q_kab  = $conn->query("SELECT name FROM reg_regencies WHERE id='$id_kab'")->fetch_assoc();
    $q_kec  = $conn->query("SELECT name FROM reg_districts WHERE id='$id_kec'")->fetch_assoc();
    $q_desa = $conn->query("SELECT name FROM reg_villages WHERE id='$id_desa'")->fetch_assoc();

    $n_prov = ucwords(strtolower($q_prov['name']));
    $n_kab  = ucwords(strtolower($q_kab['name']));
    $n_kec  = ucwords(strtolower($q_kec['name']));
    $n_desa = ucwords(strtolower($q_desa['name']));

    // Gabungkan Alamat Lengkap
    $alamat_lengkap = $detail_jalan . ", Kel. " . $n_desa . ", Kec. " . $n_kec . ", " . $n_kab . ", " . $n_prov;

    // 3. Update Database
    $conn->query("UPDATE pelanggan SET 
        nama_pelanggan='$nama', 
        telepon_pelanggan='$telp', 
        alamat_pelanggan='$alamat_lengkap' 
        $foto_sql
        WHERE id_pelanggan='$id_pelanggan'");

    echo "<script>alert('Data Berhasil Diperbarui!'); location='profil.php';</script>";
}
?>
</body>
</html>