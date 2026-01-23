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
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Planet<span>Jersey</span></h3>
            <small style="opacity: 0.5;">Administrator Panel</small>
        </div>
        
        <div class="nav-menu">
            <a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="produk.php"><i class="fa-solid fa-box"></i> Kelola Produk</a>
            <a href="kategori.php" class="active"><i class="fa-solid fa-list"></i> Kategori</a>
            <a href="pesanan.php"><i class="fa-solid fa-cart-shopping"></i> Pesanan Masuk</a>
            <a href="../produk.php" target="_blank"><i class="fa-solid fa-globe"></i> Lihat Toko</a>
        </div>

        <div class="logout-section">
            <a href="logout.php" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar"><h2>Kelola Kategori</h2> 
            <a href="tambah_kategori.php" class="btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Kategori</a>
        </div>
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