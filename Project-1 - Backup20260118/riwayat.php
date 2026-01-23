<?php
session_start();
include 'koneksi.php';

// Jika Anda ingin melihat semua pesanan yang pernah masuk ke database
$ambil = $conn->query("SELECT * FROM pembelian ORDER BY tanggal_pembelian DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan - PlanetHanduk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-navbar">
        </header>

    <main class="container" style="margin-top: 30px;">
        <h2><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pesanan Anda</h2>
        
        <div style="background: white; padding: 20px; border-radius: 15px; margin-top: 20px;">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Penerima</th>
                        <th>Total Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $nomor=1; while($row = $ambil->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $nomor; ?></td>
                        <td><?php echo date("d M Y", strtotime($row['tanggal_pembelian'])); ?></td>
                        <td><?php echo $row['nama_penerima']; ?></td>
                        <td>Rp <?php echo number_format($row['total_pembelian'], 0, ',', '.'); ?></td>
                        <td>
                            <a href="nota.php?id=<?php echo $row['id_pembelian']; ?>" class="btn-detail">Lihat Nota</a>
                        </td>
                    </tr>
                    <?php $nomor++; endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>