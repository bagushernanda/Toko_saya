<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk - PlanetJersey</title>
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
            <a href="produk.php" class="active"><i class="fa-solid fa-box"></i> Kelola Produk</a>
            <a href="kategori.php"><i class="fa-solid fa-list"></i> Kategori</a>
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
        <div class="top-bar"><h2>Daftar Produk</h2>
            <a href="tambah_produk.php" class="btn-tambah"><i class="fa-solid fa-plus"></i>+ Tambah Produk</a>
        </div>

        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #f1f2f6; color: #747d8c;">
                        <th style="padding: 15px;">No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Harga (Termurah)</th>
                        <th>Total Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $nomor = 1;
                    // Query JOIN ke produk_variasi menggunakan kolom harga_variasi dan stok
                    $ambil = $conn->query("SELECT p.*, MIN(pv.harga_variasi) as harga_min, SUM(pv.stok) as total_stok 
                                           FROM produk p 
                                           LEFT JOIN produk_variasi pv ON p.id = pv.id_produk 
                                           GROUP BY p.id");
                    while($row = $ambil->fetch_assoc()):
                    ?>
                    <tr style="border-bottom: 1px solid #f1f2f6;">
                        <td style="padding: 15px;"><?php echo $nomor++; ?></td>
                        <td><img src="../img/<?php echo $row['gambar']; ?>" width="60" style="border-radius: 8px;"></td>
                        <td><strong><?php echo $row['nama']; ?></strong></td>
                        <td>
                            <span style="color: #2ed573; font-weight: bold;">Rp <?php echo number_format($row['harga_min'], 0, ',', '.'); ?></span>
                        </td>
                        <td>
                            <span style="background: #e3f2fd; color: #2196f3; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                                <?php echo number_format($row['total_stok']); ?> pcs
                            </span>
                        </td>
                        <td>
                            <a href="edit_produk.php?id=<?php echo $row['id']; ?>" style="color: #ffa502; margin-right: 10px;"><i class="fa-solid fa-pen"></i></a>
                            <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" style="color: #ff4757;"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>