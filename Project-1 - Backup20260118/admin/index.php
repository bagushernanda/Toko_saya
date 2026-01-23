<?php
session_start();
include '../koneksi.php';

// Proteksi Login
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Anda harus login!');</script>";
    echo "<script>location='login.php';</script>";
    exit();
}

// Ambil Data Statistik
$total_produk = $conn->query("SELECT COUNT(*) as jml FROM produk")->fetch_assoc()['jml'];
$total_pesanan = $conn->query("SELECT COUNT(*) as jml FROM pembelian")->fetch_assoc()['jml'];
$stok_menipis = $conn->query("SELECT COUNT(*) as jml FROM produk WHERE stok < 5")->fetch_assoc()['jml'];
$pendapatan = $conn->query("SELECT SUM(total_pembelian) as total FROM pembelian")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        :root {
            --primary: #ff4757;
            --dark: #2f3542;
            --light: #f1f2f6;
            --sidebar-width: 260px;
        }
        body { background: var(--light); display: flex; min-height: 100vh; overflow-x: hidden; }

        /* Sidebar Styling */
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

        /* Main Content */
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

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .stat-info h3 { font-size: 24px; margin-top: 5px; }
        .stat-info p { color: #888; font-size: 14px; }

        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Planet<span>Handuk</span></h3>
            <small style="opacity: 0.5;">Administrator Panel</small>
        </div>
        
        <div class="nav-menu">
            <a href="index.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="produk.php"><i class="fa-solid fa-box"></i> Kelola Produk</a>
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
            <h2>Dashboard</h2>
            <div class="user-profile">
                <span>Selamat Datang, 
                    <strong>
                        <?php 
                            // Cek apakah session admin itu array dan punya index nama_lengkap
                            if (is_array($_SESSION['admin']) && isset($_SESSION['admin']['nama_lengkap'])) {
                                echo $_SESSION['admin']['nama_lengkap']; 
                            } else {
                                // Jika isinya string (misal hanya username), tampilkan isinya langsung
                                echo $_SESSION['admin']; 
                            }
                        ?>
                    </strong>
                </span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd; color: #2196f3;"><i class="fa-solid fa-box"></i></div>
                <div class="stat-info">
                    <p>Total Produk</p>
                    <h3><?php echo $total_produk; ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8f5e9; color: #2ed573;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div class="stat-info">
                    <p>Pesanan</p>
                    <h3><?php echo $total_pesanan; ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0; color: #ffa502;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-info">
                    <p>Stok Tipis</p>
                    <h3><?php echo $stok_menipis; ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fce4ec; color: #ff4757;"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info">
                    <p>Omzet</p>
                    <h3>Rp <?php echo number_format($pendapatan, 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h4><i class="fa-solid fa-clock-rotate-left"></i> Pesanan Terbaru</h4>
                <a href="pesanan.php" style="color: var(--primary); text-decoration: none; font-size: 14px;">Lihat Semua</a>
            </div>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Tgl</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $ambil = $conn->query("SELECT * FROM pembelian ORDER BY tanggal_pembelian DESC LIMIT 5");
                    while($row = $ambil->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo date("d/m/y", strtotime($row['tanggal_pembelian'])); ?></td>
                        <td><?php echo $row['nama_penerima']; ?></td>
                        <td><span class="status-bayar">Selesai</span></td>
                        <td>Rp <?php echo number_format($row['total_pembelian'], 0, ',', '.'); ?></td>
                        <td><a href="detail_pesanan.php?id=<?php echo $row['id_pembelian']; ?>" class="btn-detail" style="padding: 5px 10px;">Detail</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>