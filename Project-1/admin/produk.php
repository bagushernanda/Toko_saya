<?php
session_start();
include '../koneksi.php';

// Proteksi Login
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Anda harus login!');</script>";
    echo "<script>location='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .container-produk { padding: 30px; }
        .btn-tambah { background: #2ed573; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-block; margin-bottom: 20px; font-weight: bold; }
        .btn-edit { background: #ffa502; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 13px; }
        .btn-hapus { background: #ff4757; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 13px; }
        .img-produk { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body style="background: #f1f2f6;">

    <div class="container-produk">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2><i class="fa-solid fa-box"></i> Kelola Produk</h2>
            <a href="index.php" style="color: #2f3542; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
        </div>
        <hr style="margin: 20px 0; opacity: 0.1;">

        <a href="tambah_produk.php" class="btn-tambah"><i class="fa-solid fa-plus"></i> Tambah Produk</a>

        <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
            <table class="modern-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                        <th style="padding: 15px;">No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $nomor = 1;
                    $ambil = $conn->query("SELECT * FROM produk");
                    while($row = $ambil->fetch_assoc()):
                    ?>
                    <tr style="border-bottom: 1px solid #f1f2f6;">
                        <td style="padding: 15px;"><?php echo $nomor++; ?></td>
                        <td>
                            <img src="../img/<?php echo $row['gambar']; ?>" class="img-produk">
                        </td>
                        <td><strong><?php echo $row['nama']; ?></strong></td>
                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $row['stok']; ?> pcs</td>
                        <td>
                            <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" class="btn-hapus" onclick="return confirm('Hapus produk ini?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>