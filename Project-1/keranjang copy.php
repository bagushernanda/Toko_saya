<?php
session_start();
include 'koneksi.php';

// Proteksi: Jika keranjang kosong, arahkan ke produk atau tampilkan pesan nanti
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    $keranjang_kosong = true;
} else {
    $keranjang_kosong = false;
}

// Hitung total item untuk navbar badge
$total_item = (isset($_SESSION['keranjang'])) ? array_sum($_SESSION['keranjang']) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="main-navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.html">Planet<span>Handuk</span></a></div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="produk.php">Katalog</a></li>
                <li><a href="kategori.php">Kategori</a></li>
            </ul>
            <div class="nav-icons">
                <div class="cart-wrapper">
                    <a href="keranjang.php" class="active">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <?php if($total_item > 0): ?><span class="badge"><?php echo $total_item; ?></span><?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="margin-top: 30px;">
        <h2 style="margin-bottom: 20px;"><i class="fa-solid fa-basket-shopping"></i> Keranjang Belanja</h2>

        <?php if ($keranjang_kosong): ?>
            <div style="text-align: center; padding: 100px 0; background: white; border-radius: 15px;">
                <i class="fa-solid fa-cart-arrow-down" style="font-size: 4rem; color: #ddd;"></i>
                <p style="margin-top: 20px; color: #888;">Keranjang Anda masih kosong.</p>
                <a href="produk.php" class="btn-cart" style="display: inline-block; margin-top: 20px; padding: 10px 30px;">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_belanja = 0;
                        foreach ($_SESSION['keranjang'] as $id_produk => $jumlah): 
                            $ambil = $conn->query("SELECT * FROM produk WHERE id = '$id_produk'");
                            $pecah = $ambil->fetch_assoc();
                            $subtotal = $pecah['harga'] * $jumlah;
                        ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 15px;">
                                <img src="img/<?php echo $pecah['gambar']; ?>" width="70" style="border-radius: 8px;">
                                <strong><?php echo $pecah['nama']; ?></strong>
                            </td>
                            <td>Rp <?php echo number_format($pecah['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <form action="update_keranjang.php" method="post" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">
                                    <input type="number" name="jumlah" value="<?php echo $jumlah; ?>" min="1" max="<?php echo $pecah['stok']; ?>" style="width: 60px; padding: 5px; border-radius: 5px; border: 1px solid #ddd;">
                                    <button type="submit" class="btn-detail" style="padding: 5px 10px;"><i class="fa-solid fa-rotate"></i></button>
                                </form>
                            </td>
                            <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            <td>
                                <a href="hapus_keranjang.php?id=<?php echo $id_produk; ?>" class="btn-delete-cart" onclick="return confirm('Hapus produk ini dari keranjang?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                        <?php $total_belanja += $subtotal; endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align: right; padding: 20px;">Total Belanja</th>
                            <th colspan="2" style="font-size: 20px; color: #ff4757;">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></th>
                        </tr>
                    </tfoot>
                </table>

                <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                    <a href="produk.php" class="btn-detail" style="padding: 12px 25px;">Lanjut Belanja</a>
                    <a href="checkout.php" class="btn-cart" style="padding: 12px 40px;">Checkout Sekarang <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>