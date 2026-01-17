<?php
session_start();
include '../koneksi.php';

// Cek jika tombol login ditekan
if (isset($_POST['login'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    // 1. Jalankan query ke database
    $ambil = $conn->query("SELECT * FROM admin WHERE username='$user' AND password='$pass'");
    
    // 2. HITUNG apakah ada data yang cocok (INI VARIABELNYA)
    $cocok = $ambil->num_rows;

    // 3. Jika cocoknya ada 1 data
    if ($cocok == 1) {
        // Ambil data lengkap admin tersebut sebagai ARRAY
        $akun = $ambil->fetch_assoc();
        
        // Simpan ke session
        $_SESSION['admin'] = $akun;

        echo "<div class='alert alert-success'>Login Berhasil!</div>";
        echo "<meta http-equiv='refresh' content='1;url=index.php'>";
    } else {
        // Jika tidak ada yang cocok
        echo "<div class='alert alert-danger'>Username atau Password Salah!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - PlanetHanduk</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .login-box h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-login { width: 100%; padding: 12px; background: #ff4757; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-login:hover { background: #ff6b81; }
    </style>
</head>
<body style="background: #f1f2f6;">

    <div class="login-box">
        <h2>Admin Login</h2>
        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="user" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="pass" required>
            </div>
            <button type="submit" name="login" class="btn-login">Masuk ke Panel</button>
        </form>

        <?php
        if (isset($_POST['login'])) {
            $user = $_POST['user'];
            $pass = $_POST['pass'];

            // Cek ke database
            $ambil = $conn->query("SELECT * FROM admin WHERE username='$user' AND password='$pass'");
            $cocok = $ambil->num_rows;

            if ($cocok == 1) {
                $_SESSION['admin'] = $ambil->fetch_assoc();
                echo "<div style='color:green; text-align:center; margin-top:15px;'>Login Berhasil!</div>";
                echo "<meta http-equiv='refresh' content='1;url=index.php'>";
            } else {
                echo "<div style='color:red; text-align:center; margin-top:15px;'>Username atau Password Salah!</div>";
            }
        }
        ?>
    </div>

</body>
</html>