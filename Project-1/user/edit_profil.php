<?php 
session_start();
include '../koneksi.php'; 

if (!isset($_SESSION['pelanggan'])) {
    echo "<script>alert('Silakan login terlebih dahulu');location='login_pelanggan.php';</script>";
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
        body { background: #f1f2f6; }
        .edit-container { max-width: 700px; margin: 50px auto; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-save { background: #ff4757; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

<div class="edit-container">
    <div class="card">
        <h3><i class="fa-solid fa-user-pen"></i> Edit Profil Anda</h3>
        <hr style="margin: 20px 0; opacity: 0.1;">
        
        <form method="post">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?php echo $detail['nama_pelanggan']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Nomor Telepon/WA</label>
                <input type="text" name="telepon" class="form-control" value="<?php echo $detail['telepon_pelanggan']; ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Provinsi</label>
                    <select id="provinsi" name="provinsi" class="form-control" required>
                        <option value="">-- Pilih Provinsi --</option>
                        <?php 
                        // Sesuaikan nama tabel provinsi kamu
                        $prov = $conn->query("SELECT * FROM wilayah_provinsi");
                        while($row = $prov->fetch_assoc()){
                            echo "<option value='".$row['id']."'>".$row['nama']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kota/Kabupaten</label>
                    <select id="kota" name="kota" class="form-control" required disabled>
                        <option value="">-- Pilih Provinsi Dahulu --</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Kecamatan</label>
                    <select id="kecamatan" name="kecamatan" class="form-control" required disabled>
                        <option value="">-- Pilih Kota Dahulu --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kelurahan/Desa</label>
                    <select id="kelurahan" name="kelurahan" class="form-control" required disabled>
                        <option value="">-- Pilih Kecamatan Dahulu --</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Detail Alamat (Jalan, No Rumah, RT/RW)</label>
                <textarea name="alamat" class="form-control" rows="3"><?php echo $detail['alamat_pelanggan']; ?></textarea>
            </div>

            <button name="simpan" class="btn-save">SIMPAN PERUBAHAN</button>
            <a href="profil.php" style="display:block; text-align:center; margin-top:15px; color:#777; text-decoration:none;">Batal</a>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    // Saat Provinsi diubah
    $("#provinsi").change(function(){
        var id_prov = $(this).val();
        $.ajax({
            type: 'POST',
            url: 'ambil_wilayah.php',
            data: {type: 'kota', id: id_prov},
            success: function(res){
                $("#kota").html(res).prop("disabled", false);
                $("#kecamatan").html("<option>-- Pilih Kota Dahulu --</option>").prop("disabled", true);
                $("#kelurahan").html("<option>-- Pilih Kecamatan Dahulu --</option>").prop("disabled", true);
            }
        });
    });

    // Saat Kota diubah
    $("#kota").change(function(){
        var id_kota = $(this).val();
        $.ajax({
            type: 'POST',
            url: 'ambil_wilayah.php',
            data: {type: 'kecamatan', id: id_kota},
            success: function(res){
                $("#kecamatan").html(res).prop("disabled", false);
                $("#kelurahan").html("<option>-- Pilih Kecamatan Dahulu --</option>").prop("disabled", true);
            }
        });
    });

    // Saat Kecamatan diubah
    $("#kecamatan").change(function(){
        var id_kec = $(this).val();
        $.ajax({
            type: 'POST',
            url: 'ambil_wilayah.php',
            data: {type: 'kelurahan', id: id_kec},
            success: function(res){
                $("#kelurahan").html(res).prop("disabled", false);
            }
        });
    });
});
</script>

<?php 
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $telp = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    // Kamu bisa menggabungkan nama wilayah ke dalam alamat atau menyimpan ID-nya ke kolom baru
    
    $conn->query("UPDATE pelanggan SET 
        nama_pelanggan='$nama', 
        telepon_pelanggan='$telp', 
        alamat_pelanggan='$alamat' 
        WHERE id_pelanggan='$id_pelanggan'");

    echo "<script>alert('Profil berhasil diperbarui');location='profil.php';</script>";
}
?>
</body>
</html>