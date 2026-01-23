<?php 
// Sertakan file koneksi ke database
include 'koneksi.php'; 

if (isset($_POST['daftar'])) {
    // 1. Mengambil data dari form dan melindunginya dari SQL Injection
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password']; // Sebaiknya gunakan password_hash() untuk keamanan lebih tinggi
    $telp = mysqli_real_escape_string($conn, $_POST['telepon']);

    // 2. Cek apakah email sudah terdaftar di database
    $ambil = $conn->query("SELECT * FROM pelanggan WHERE email_pelanggan='$email'");
    $yang_cocok = $ambil->num_rows;

    if ($yang_cocok == 1) {
        // Jika email sudah ada
        echo "<script>alert('Pendaftaran gagal, email sudah digunakan!');</script>";
        echo "<script>location='daftar.php';</script>";
    } else {
        // 3. Masukkan data ke tabel pelanggan
        $conn->query("INSERT INTO pelanggan (nama_pelanggan, email_pelanggan, password_pelanggan, telepon_pelanggan) 
                      VALUES ('$nama', '$email', '$pass', '$telp')");

        echo "<script>alert('Pendaftaran sukses, silakan login');</script>";
        echo "<script>location='login_pelanggan.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="auth.css"> </head>
<body class="auth-body-bg">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Gabung dengan komunitas PlanetHanduk</p>
            </div>
            <div class="auth-content">
                <form method="post">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="nama" placeholder="Nama Lengkap" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" placeholder="email@aktif.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="telepon" placeholder="0812xxxx" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button name="daftar" class="btn-submit">REGISTER</button>
                </form>
                <div class="auth-footer">
                    Sudah punya akun? <a href="login_pelanggan.php">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>