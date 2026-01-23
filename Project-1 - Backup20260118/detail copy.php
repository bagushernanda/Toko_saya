<?php 
session_start();
include 'koneksi.php'; 

// Ambil ID dari URL
$id_produk = $_GET['id'];

// Ambil data produk berdasarkan ID
$query = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p 
                              JOIN kategori k ON p.kategori_id = k.id 
                              WHERE p.id = '$id_produk'");
$detail = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $detail['nama']; ?> - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <a href="produk.php" style="text-decoration: none; color: #ff4757;">← Kembali ke Katalog</a>
        
        <div class="detail-row" style="margin-top: 20px;">
            <div class="detail-col">
                <img src="img/<?php echo $detail['gambar']; ?>" alt="">
            </div>
            <div class="detail-col">
                <h6>Home / <?php echo $detail['nama_kategori']; ?></h6>
                <h2><?php echo $detail['nama']; ?></h2>
                <h3 class="price">Rp <?php echo number_format($detail['harga'], 0, ',', '.'); ?></h3>
                
                <form action="beli.php?id=<?php echo $id_produk; ?>" method="post">
                    <div class="action-bar">
                            <input type="number" 
                                name="jumlah" 
                                value="1" 
                                min="1" 
                                max="<?php echo $detail['stok']; ?>" 
                                required>
                            
                            <?php if($detail['stok'] > 0): ?>
                                <button type="submit" class="btn-cart" style="flex: none; padding: 10px 30px;">
                                    Tambah ke Keranjang
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn-cart" style="background: #ccc; cursor: not-allowed;" disabled>
                                    Stok Habis
                                </button>
                            <?php endif; ?>
                    </div>
                </form>

                <h4>Detail Produk</h4>
                <p><?php echo nl2br($detail['deskripsi']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>