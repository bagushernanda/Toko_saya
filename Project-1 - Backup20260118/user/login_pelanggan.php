<?php 
session_start(); 
include '../koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../auth.css">
</head>
<body class="auth-body-bg">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Silakan masuk ke akun Anda</p>
            </div>
            <div class="auth-content">
                <form method="post">
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" placeholder="email@contoh.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button name="login" class="btn-submit">LOGIN</button>
                </form>

                <?php 
                if (isset($_POST['login'])) {
                    // 1. Ambil data dari form dan bersihkan untuk keamanan
                    $email = mysqli_real_escape_string($conn, $_POST['email']);
                    $password = $_POST['password']; // Jika pakai password_hash di pendaftaran, gunakan password_verify nantinya

                    // 2. Query ke tabel pelanggan
                    $ambil = $conn->query("SELECT * FROM pelanggan WHERE email_pelanggan='$email' AND password_pelanggan='$password'");
                    
                    // 3. Hitung jumlah data yang ditemukan
                    $akun_cocok = $ambil->num_rows;

                    if ($akun_cocok == 1) {
                        // Jika valid: ambil data dan simpan ke session
                        $akun = $ambil->fetch_assoc();
                        $_SESSION["pelanggan"] = $akun;

                        echo "<div style='color:green; text-align:center; margin-top:15px;'>Login Berhasil! Tunggu sebentar...</div>";
                        echo "<meta http-equiv='refresh' content='1;url=index.php'>";
                    } else {
                        // Jika TIDAK valid: tampilkan pesan error
                        echo "<div style='color:red; text-align:center; margin-top:15px;'>Login Gagal! Email atau Password salah.</div>";
                    }
                }
                ?>

                <div class="auth-footer">
                    Belum punya akun? <a href="daftar.php">Daftar Sekarang</a><br>
                    <a href="produk.php" class="back-home"><i class="fa-solid fa-arrow-left"></i> Kembali ke Katalog</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>