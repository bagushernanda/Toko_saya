<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>location='login.php';</script>"; exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pesanan - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body style="background: #f1f2f6; padding: 30px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fa-solid fa-cart-arrow-down"></i> Pesanan Masuk</h2>
        <a href="index.php" style="text-decoration:none; color:#2f3542;"><i class="fa-solid fa-house"></i> Dashboard</a>
    </div>

    <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <table class="modern-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                    <th style="padding: 15px;">No</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Total Pembelian</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $nomor = 1;
                // Query mengambil data gabungan pembelian dan pelanggan
                $ambil = $conn->query("CALL sp_GetSemuaPesanan()");
                while($row = $ambil->fetch_assoc()):
                ?>
                <tr style="border-bottom: 1px solid #f1f2f6;">
                    <td style="padding: 15px;"><?php echo $nomor++; ?></td>
                    <td><strong><?php echo $row['nama_pelanggan']; ?></strong></td>
                    <td><?php echo date("d F Y", strtotime($row['tanggal_pembelian'])); ?></td>
                    <td>Rp <?php echo number_format($row['total_pembelian'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if ($row['status_pembelian'] == 'Pending'): ?>
                            <span style="background: #ffa502; color: white; padding: 3px 8px; border-radius: 5px; font-size: 11px;">Pending</span>
                        <?php elseif ($row['status_pembelian'] == 'Barang Dikirim'): ?>
                            <span style="background: #1e90ff; color: white; padding: 3px 8px; border-radius: 5px; font-size: 11px;">Dikirim</span>
                        <?php else: ?>
                            <span style="background: #2ed573; color: white; padding: 3px 8px; border-radius: 5px; font-size: 11px;"><?php echo $row['status_pembelian']; ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="detail_pesanan.php?id=<?php echo $row['id_pembelian']; ?>" class="btn-detail" style="background: #ff4757; color: white; padding: 5px 15px; border-radius: 5px; text-decoration: none; font-size: 13px;">Rincian</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>