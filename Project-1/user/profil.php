<?php 
session_start();
include '../koneksi.php'; 

// Proteksi Halaman
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
    <title>Profil & Riwayat Belanja - PlanetJersey</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f1f2f6; }
        .main-wrapper { max-width: 1200px; margin: 50px auto; padding: 0 20px; }
        
        /* Layout Grid */
        .profile-grid { 
            display: grid; 
            grid-template-columns: 350px 1fr; 
            gap: 25px; 
            align-items: start;
        }

        .card { 
            background: white; 
            border-radius: 15px; 
            padding: 25px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
        }

        .card-title { 
            font-size: 1.2rem; 
            font-weight: bold; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            color: #2f3542;
            border-bottom: 2px solid #f1f2f6;
            padding-bottom: 10px;
        }

        /* Styling Form Profil (Kiri) */
        .profile-img-section { text-align: center; margin-bottom: 20px; }
        .profile-img-section i { font-size: 80px; color: #ced4da; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: bold; color: #747d8c; margin-bottom: 5px; }
        .form-control-static { 
            padding: 10px; 
            background: #f8f9fa; 
            border-radius: 8px; 
            border: 1px solid #eee;
            color: #2f3542;
        }

        /* Styling Tabel Riwayat (Kanan) */
        .table-history { width: 100%; border-collapse: collapse; }
        .table-history th { text-align: left; padding: 12px; background: #f8f9fa; color: #747d8c; font-size: 0.9rem; }
        .table-history td { padding: 15px 12px; border-bottom: 1px solid #f1f2f6; font-size: 0.95rem; }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-success { background: #d4edda; color: #155724; }

        .btn-detail {
            padding: 6px 12px;
            background: #ff4757;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        @media (max-width: 992px) {
            .profile-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="main-wrapper">
        <div class="profile-grid">
            
            <aside class="card">
                <div class="card-title">
                    <i class="fa-solid fa-user-circle"></i> Profil Saya
                </div>
                <div class="profile-img-section">
                    <i class="fa-solid fa-circle-user"></i>
                    <h3 style="margin-top:10px;"><?php echo $detail['nama_pelanggan']; ?></h3>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <div class="form-control-static"><?php echo $detail['email_pelanggan']; ?></div>
                </div>
                <div class="form-group">
                    <label>Telepon</label>
                    <div class="form-control-static"><?php echo $detail['telepon_pelanggan']; ?></div>
                </div>
                <div class="form-group">
                    <label>Alamat Default</label>
                    <div class="form-control-static" style="min-height: 60px;">
                        <?php echo $detail['alamat_pelanggan'] ? $detail['alamat_pelanggan'] : '<i style="color:#ccc">Belum ada alamat</i>'; ?>
                    </div>
                </div>

                <a href="edit_profil.php" style="display:block; text-align:center; margin-top:20px; color:#ff4757; text-decoration:none; font-weight:bold;">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Profil
                </a>
            </aside>

            <main class="card">
                <div class="card-title">
                    <i class="fa-solid fa-bag-shopping"></i> Riwayat Pemesanan
                </div>

                <div style="overflow-x: auto;">
                    <table class="table-history">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $nomor = 1;
                            // Query mengambil riwayat belanja pelanggan tersebut
                            $ambil_riwayat = $conn->query("SELECT * FROM pembelian WHERE id_pelanggan = '$id_pelanggan' ORDER BY tanggal_pembelian DESC");
                            
                            if ($ambil_riwayat->num_rows > 0):
                                while($pecah = $ambil_riwayat->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $nomor++; ?></td>
                                <td><?php echo date("d M Y", strtotime($pecah['tanggal_pembelian'])); ?></td>
                                <td><strong>Rp <?php echo number_format($pecah['total_pembelian']); ?></strong></td>
                                <td>
                                    <?php if($pecah['status_pembelian'] == 'Pending'): ?>
                                        <span class="status-badge status-pending">Menunggu Bayar</span>
                                    <?php else: ?>
                                        <span class="status-badge status-success"><?php echo $pecah['status_pembelian']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="nota.php?id=<?php echo $pecah['id_pembelian']; ?>" class="btn-detail">Nota</a>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 40px; color: #ccc;">
                                    <i class="fa-solid fa-box-open" style="font-size: 40px; display:block; margin-bottom:10px;"></i>
                                    Belum ada transaksi.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>

        </div>
    </div>

</body>
</html>