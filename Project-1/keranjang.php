<?php
session_start();
include 'koneksi.php';

// Jika keranjang kosong
if (empty($_SESSION["keranjang"])) {
    echo "<script>alert('Keranjang kosong, silakan belanja dulu!');location='produk.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - PlanetJersey</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="main-navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.php">Planet<span>Jersey</span></a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="produk.php">Produk</a></li>
            </ul>
        </div>
    </header>

    <main class="container" style="padding: 40px 20px;">
        <h2><i class="fa-solid fa-cart-shopping"></i> Keranjang Belanja</h2>
        <hr>

        <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">No</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Produk</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Varian</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Harga</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Jumlah</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Subtotal</th>
                    <th style="padding: 15px; border-bottom: 2px solid #ddd;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $nomor = 1;
                $total_belanja = 0;
                foreach ($_SESSION["keranjang"] as $id_variasi => $jumlah): 
                    // Ambil detail berdasarkan ID Variasi
                    $ambil = $conn->query("SELECT p.nama, p.harga, p.gambar, v.warna, v.ukuran 
                                           FROM produk_variasi v 
                                           JOIN produk p ON v.id_produk = p.id 
                                           WHERE v.id_variasi = '$id_variasi'");
                    $pecah = $ambil->fetch_assoc();
                    
                    // Jika data tidak ditemukan di DB (misal variasi dihapus admin), hapus dari session
                    if (!$pecah) {
                        unset($_SESSION["keranjang"][$id_variasi]);
                        continue;
                    }

                    $subharga = $pecah["harga"] * $jumlah;
                ?>
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><?php echo $nomor; ?></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">
                        <img src="img/<?php echo $pecah['gambar']; ?>" width="50" style="border-radius: 5px; vertical-align: middle; margin-right: 10px;">
                        <?php echo $pecah['nama']; ?>
                    </td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">
                        <span style="background: #eee; padding: 5px 10px; border-radius: 15px; font-size: 12px;">
                            <?php echo $pecah['warna']; ?> | <?php echo $pecah['ukuran']; ?>
                        </span>
                    </td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">Rp <?php echo number_format($pecah['harga']); ?></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;"><?php echo $jumlah; ?></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">Rp <?php echo number_format($subharga); ?></td>
                    <td style="padding: 15px; border-bottom: 1px solid #eee;">
                        <a href="hapus-keranjang.php?id=<?php echo $id_variasi; ?>" style="color: #ff4757;"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php 
                    $nomor++; 
                    $total_belanja += $subharga;
                endforeach; 
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" style="padding: 15px; text-align: right;">Total Belanja</th>
                    <th colspan="2" style="padding: 15px; text-align: left; font-size: 20px; color: #ff4757;">Rp <?php echo number_format($total_belanja); ?></th>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <a href="produk.php" style="padding: 12px 25px; border: 1px solid #ff4757; color: #ff4757; text-decoration: none; border-radius: 8px;">Lanjut Belanja</a>
            <a href="checkout.php" style="padding: 12px 25px; background: #ff4757; color: #fff; text-decoration: none; border-radius: 8px;">Checkout Sekarang</a>
        </div>
    </main>

</body>
</html>