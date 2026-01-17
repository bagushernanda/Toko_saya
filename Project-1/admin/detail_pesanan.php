<?php
session_start();
include '../koneksi.php';

$id_pembelian = $_GET['id'];
$ambil = $conn->query("SELECT * FROM pembelian JOIN pelanggan ON pembelian.id_pelanggan = pelanggan.id_pelanggan WHERE pembelian.id_pembelian = '$id_pembelian'");
$detail = $ambil->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Pesanan - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: #f1f2f6; padding: 30px;">

    <div style="max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 15px;">
        <h2 style="margin-bottom: 20px;">Rincian Pesanan #<?php echo $detail['id_pembelian']; ?></h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <h4 style="margin-bottom: 10px; color: #ff4757;">Informasi Pelanggan</h4>
                <p><strong>Nama:</strong> <?php echo $detail['nama_pelanggan']; ?></p>
                <p><strong>Telepon:</strong> <?php echo $detail['telepon_pelanggan']; ?></p>
                <p><strong>Email:</strong> <?php echo $detail['email_pelanggan']; ?></p>
            </div>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
                <h4 style="margin-bottom: 10px; color: #ff4757;">Pengiriman</h4>
                <p><strong>Tanggal:</strong> <?php echo date("d/m/Y", strtotime($detail['tanggal_pembelian'])); ?></p>
                <p><strong>Total:</strong> Rp <?php echo number_format($detail['total_pembelian'], 0, ',', '.'); ?></p>
                <p><strong>Status:</strong> <?php echo $detail['status_pembelian']; ?></p>
            </div>
        </div>

        <table class="modern-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; background: #f1f2f6;">
                    <th style="padding: 12px;">Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Mengambil produk yang dibeli dari tabel pembelian_produk
                $ambil_produk = $conn->query("SELECT * FROM pembelian_produk JOIN produk ON pembelian_produk.id_produk = produk.id WHERE pembelian_produk.id_pembelian = '$id_pembelian'");
                while($item = $ambil_produk->fetch_assoc()):
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?php echo $item['nama']; ?></td>
                    <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                    <td><?php echo $item['jumlah']; ?></td>
                    <td>Rp <?php echo number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="margin-top: 30px;">
            <a href="pesanan.php" style="color: #747d8c; text-decoration: none;"><i class="fa-solid fa-chevron-left"></i> Kembali ke Daftar Pesanan</a>
        </div>
    </div>

    <div style="max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 15px;">
    <h4 style="margin-bottom: 15px; color: #2f3542;"><i class="fa-solid fa-truck-fast"></i> Update Status Pesanan</h4>
    <form method="post">
        <div style="display: flex; gap: 10px;">
            <select name="status" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; flex: 1;">
                <option value="Pending" <?php echo ($detail['status_pembelian'] == 'Pending') ? 'selected' : ''; ?>>Pending (Menunggu Pembayaran)</option>
                <option value="Sudah Bayar" <?php echo ($detail['status_pembelian'] == 'Sudah Bayar') ? 'selected' : ''; ?>>Sudah Bayar</option>
                <option value="Barang Dikirim" <?php echo ($detail['status_pembelian'] == 'Barang Dikirim') ? 'selected' : ''; ?>>Barang Dikirim</option>
                <option value="Selesai" <?php echo ($detail['status_pembelian'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                <option value="Batal" <?php echo ($detail['status_pembelian'] == 'Batal') ? 'selected' : ''; ?>>Batal</option>
            </select>
            <button name="proses" style="background: #2ed573; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Update Status
            </button>
        </div>
    </form>
    </div>

    <?php 
    if (isset($_POST['proses'])) {
        $status_baru = $_POST['status'];
        $conn->query("UPDATE pembelian SET status_pembelian = '$status_baru' WHERE id_pembelian = '$id_pembelian'");

        echo "<script>alert('Status pesanan berhasil diperbarui!');</script>";
        echo "<script>location='pesanan.php?id=$id_pembelian';</script>";
    }
    ?>

</body>
</html>