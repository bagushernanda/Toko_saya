<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>";
    exit();
}

// Logika Hapus Kategori
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM kategori WHERE id='$id'");
    echo "<script>alert('Kategori terhapus'); location='kategori.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kategori - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        :root { --primary: #ff4757; --dark: #2f3542; --light: #f1f2f6; --sidebar-width: 260px; }
        body { background: var(--light); display: flex; margin: 0; font-family: 'Poppins', sans-serif; }
        
        /* Sidebar (Sama dengan produk.php) */
        .sidebar { width: var(--sidebar-width); background: var(--dark); color: white; position: fixed; height: 100%; display: flex; flex-direction: column; }
        .sidebar-header { padding: 30px 20px; text-align: center; background: rgba(0,0,0,0.1); }
        .sidebar-header span { color: var(--primary); }
        .nav-menu { flex: 1; padding: 20px 10px; }
        .nav-menu a { color: #a4b0be; display: flex; align-items: center; padding: 12px 15px; text-decoration: none; border-radius: 8px; margin-bottom: 8px; }
        .nav-menu a.active { background: var(--primary); color: white; }

        .main-content { margin-left: var(--sidebar-width); flex: 1; padding: 30px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
        
        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-tambah { background: #2ed573; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        .btn-edit { background: #ffa502; color: white; padding: 8px; border-radius: 5px; text-decoration: none; }
        .btn-hapus { background: #ff4757; color: white; padding: 8px; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header"><h3>Planet<span>Handuk</span></h3></div>
        <div class="nav-menu">
            <a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="produk.php"><i class="fa-solid fa-box"></i> Kelola Produk</a>
            <a href="kategori.php" class="active"><i class="fa-solid fa-list"></i> Kategori</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar"><h2>Kelola Kategori</h2></div>

        <a href="tambah_kategori.php" class="btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Kategori</a>

        <div class="table-container">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                        <th style="padding: 15px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $nomor = 1;
                    $ambil = $conn->query("SELECT * FROM kategori");
                    while($row = $ambil->fetch_assoc()):
                    ?>
                    <tr style="border-bottom: 1px solid #f1f2f6;">
                        <td style="padding: 15px;"><?php echo $nomor++; ?></td>
                        <td><strong><?php echo $row['nama_kategori']; ?></strong></td>
                        <td>
                            <a href="edit_kategori.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="kategori.php?hapus=<?php echo $row['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus kategori ini?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                        <td>
                            <?php if(!empty($row['gambar_kategori'])): ?>
                                <img src="../img/<?php echo $row['gambar_kategori']; ?>" width="60" style="border-radius: 5px;">
                            <?php else: ?>
                                <small>No Image</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>