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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        :root {
            --primary: #ff4757;
            --dark: #2f3542;
            --light: #f1f2f6;
            --sidebar-width: 260px;
        }
        body { background: var(--light); display: flex; min-height: 100vh; overflow-x: hidden; margin: 0; }

        /* Sidebar Styling (Sesuai index.php) */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--dark);
            color: white;
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }
        .sidebar-header { padding: 30px 20px; text-align: center; background: rgba(0,0,0,0.1); }
        .sidebar-header span { color: var(--primary); }
        
        .nav-menu { flex: 1; padding: 20px 10px; }
        .nav-menu a {
            color: #a4b0be;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: 0.3s;
        }
        .nav-menu a i { width: 25px; font-size: 18px; }
        .nav-menu a:hover, .nav-menu a.active { background: var(--primary); color: white; }

        .logout-section { padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .btn-logout {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
            border: 1px solid #ff4757;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-logout:hover { background: #ff4757; color: white; }

        /* Main Content Styling */
        .main-content { margin-left: var(--sidebar-width); flex: 1; padding: 30px; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }

        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .img-produk { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        
        .btn-tambah { background: #2ed573; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.3s; display: inline-block; margin-bottom: 20px; }
        .btn-tambah:hover { background: #26af5a; transform: translateY(-2px); }
        
        .btn-edit { background: #ffa502; color: white; padding: 8px; border-radius: 5px; text-decoration: none; margin-right: 5px; }
        .btn-hapus { background: #ff4757; color: white; padding: 8px; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Planet<span>Handuk</span></h3>
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
        <div class="top-bar">
            <h2>Kelola Produk</h2>
            <div class="user-profile">
                <span><i class="fa-solid fa-circle-user"></i> Admin <strong>PlanetHanduk</strong></span>
            </div>
        </div>

        <a href="tambah_produk.php" class="btn-tambah">
            <i class="fa-solid fa-plus"></i> Tambah Produk Baru
        </a>

        <div class="table-container">
            <table class="modern-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                        <th style="padding: 15px;">No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Total Stok</th>
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
                        <td>
                            <span style="background: #e3f2fd; color: #2196f3; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                                <?php echo $row['stok']; ?> pcs
                            </span>
                        </td>
                        <td>
                            <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" class="btn-hapus" title="Hapus" onclick="return confirm('Hapus produk ini?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>