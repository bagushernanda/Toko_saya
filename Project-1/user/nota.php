<?php
session_start();
include '../koneksi.php';

// Ambil ID pembelian dari URL
$id_pembelian = $_GET['id'];

// Ambil data pembelian
$ambil = $conn->query("SELECT * FROM pembelian WHERE id_pembelian = '$id_pembelian'");
$detail = $ambil->fetch_assoc();

// Proteksi: Jika ID tidak ada di database
if (!$detail) {
    echo "<script>alert('Nota tidak ditemukan!');location='produk.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pembelian - PlanetJersey</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .nota-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-top: 30px;
        }
        .nota-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px dashed #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .info-pengiriman {
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .status-bayar {
            display: inline-block;
            padding: 5px 15px;
            background: #e1f7e7;
            color: #2ed573;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        @media print {
            .no-print { display: none; }
            .nota-box { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

    <main class="container">
        <div class="nota-box">
            <div class="nota-header">
                <div>
                    <h1 style="color: #333;">NOTA <span style="color: #ff4757;">#<?php echo $detail['id_pembelian']; ?></span></h1>
                    <p style="color: #888;"><?php echo date("d F Y, H:i", strtotime($detail['tanggal_pembelian'])); ?></p>
                </div>
                <div style="text-align: right;">
                    <div class="status-bayar">PESANAN BERHASIL</div>
                </div>
            </div>

            <div class="info-pengiriman">
                <div>
                    <h4 style="color: #ff4757; margin-bottom: 10px;">Penerima:</h4>
                    <strong><?php echo $detail['nama_penerima']; ?></strong><br>
                    <?php echo $detail['telepon']; ?><br>
                    <?php echo $detail['alamat_lengkap']; ?>
                </div>
                <div style="text-align: right;">
                    <h4 style="color: #ff4757; margin-bottom: 10px;">Metode Pembayaran:</h4>
                    <p>Transfer Bank / COD<br>(Hubungi Admin untuk konfirmasi)</p>
                </div>
            </div>

            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_produk = 0;
                    $ambil_produk = $conn->query("SELECT * FROM pembelian_produk JOIN produk 
                                                 ON pembelian_produk.id_produk = produk.id 
                                                 WHERE pembelian_produk.id_pembelian = '$id_pembelian'");
                    while($item = $ambil_produk->fetch_assoc()): 
                        $subtotal = $item['harga'] * $item['jumlah'];
                        $total_produk += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo $item['nama']; ?></td>
                        <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $item['jumlah']; ?></td>
                        <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right; padding: 10px;">Total Produk</th>
                        <th>Rp <?php echo number_format($total_produk, 0, ',', '.'); ?></th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right; padding: 10px;">Ongkos Kirim</th>
                        <th>Rp <?php echo number_format($detail['total_pembelian'] - $total_produk, 0, ',', '.'); ?></th>
                    </tr>
                    <tr style="font-size: 1.2rem; color: #ff4757;">
                        <th colspan="3" style="text-align: right; padding: 20px;">GRAND TOTAL</th>
                        <th>Rp <?php echo number_format($detail['total_pembelian'], 0, ',', '.'); ?></th>
                    </tr>
                </tfoot>
            </table>

            <div class="no-print" style="margin-top: 40px; display: flex; gap: 15px;">
                <button onclick="window.print()" class="btn-detail" style="padding: 12px 25px;">
                    <i class="fa-solid fa-print"></i> Cetak Nota
                </button>
                <a href="../produk.php" class="btn-cart" style="padding: 12px 25px; text-decoration: none;">
                    Kembali Belanja
                </a>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #888;">
            <p>Terima kasih telah berbelanja di <strong>PlanetJersey</strong>!</p>
        </div>
    </main>

</body>
</html>