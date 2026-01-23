<?php 
session_start();
include 'koneksi.php';

// 1. Ambil ID kategori dari URL
$id_kat = $_GET['id'];

// 2. Ambil data Nama Kategori untuk judul halaman
$res_kat = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id = '$id_kat'");
$data_kat = mysqli_fetch_assoc($res_kat);

// 3. Ambil Produk yang hanya sesuai dengan kategori ini
$query_produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori 
                                    FROM produk p 
                                    JOIN kategori k ON p.kategori_id = k.id 
                                    WHERE p.kategori_id = '$id_kat'");

// Hitung item keranjang untuk navbar
$total_item = (isset($_SESSION['keranjang'])) ? array_sum($_SESSION['keranjang']) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $data_kat['nama_kategori']; ?> - PlanetJersey</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="produk-style.css">
</head>
<body>

    <header class="main-navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.html">Planet<span>Jersey</span></a></div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="produk.php">Katalog</a></li>
                <li><a href="kategori.php" class="active">Kategori</a></li>
            </ul>
            <div class="nav-icons">
                <div class="cart-wrapper">
                    <a href="keranjang.php"><i class="fa-solid fa-cart-shopping"></i>
                    <?php if($total_item > 0): ?><span class="badge"><?php echo $total_item; ?></span><?php endif; ?></a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
    <div class="catalog-header" style="background: white; padding: 30px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <div class="header-info">
            <h1 style="font-size: 28px; color: #333; margin-bottom: 5px;">Koleksi <?php echo $data_kat['nama_kategori']; ?></h1>
            <p style="color: #888;">Menemukan <strong><?php echo mysqli_num_rows($query_produk); ?></strong> produk berkualitas.</p>
        </div>
        <a href="kategori.php" class="btn-detail" style="flex: none; padding: 10px 20px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="product-grid">
        <?php if(mysqli_num_rows($query_produk) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query_produk)): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="img/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama']; ?>">
                    </div>
                    
                    <div class="product-info">
                        <h3><?php echo $row['nama']; ?></h3>
                        <p class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        
                        <div class="action-buttons">
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-detail">Detail</a>
                            <a href="beli.php?id=<?php echo $row['id']; ?>" class="btn-cart">
                                <i class="fas fa-shopping-cart"></i> + Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px 0; background: #fff; border-radius: 15px;">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" wid  th="120" style="opacity: 0.5;">
                <p style="margin-top: 20px; color: #888; font-size: 18px;">Maaf, produk untuk kategori ini belum tersedia.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>