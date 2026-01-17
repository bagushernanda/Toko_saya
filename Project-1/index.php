<?php 
session_start();
include 'koneksi.php'; 

// Hitung total item di keranjang untuk badge
$total_item = 0;
if (isset($_SESSION['keranjang'])) {
    $total_item = array_sum($_SESSION['keranjang']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanetJersey - Toko Jersey Berkualitas</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="main-navbar">
        <div class="nav-container">
            <div class="logo"><a href="index.php">Planet<span>Jersey</span></a></div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="produk.php">Produk</a></li>
                <li><a href="kategori.php">Kategori</a></li>
            </ul>
            <div class="nav-icons">
            <form action="produk.php" method="get" class="search-container">
                <input type="text" name="keyword" placeholder="Cari barang..." value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>">
                <button type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
                
                <div class="user-wrapper" style="position: relative;">
                    <?php if(isset($_SESSION['pelanggan'])): ?>
                        <a href="profil.php" title="Profil Saya">
                            <i class="fa-solid fa-user-check" style="color: #2ed573;"></i>
                        </a>
                    <?php else: ?>
                        <a href="login_pelanggan.php" title="Login/Daftar">
                            <i class="fa-solid fa-user"></i>
                        </a>
                    <?php endif; ?>
                </div>
            
            <div class="cart-wrapper">
                <a href="keranjang.php">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php if ($total_item > 0): ?>
                        <span class="badge"><?php echo $total_item; ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <div class="mobile-menu-icon">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Koleksi Jersey Premium 2024</h1>
            <p>Rasakan kelembutan kualitas hotel bintang 5 setiap hari di rumah Anda.</p>
            <a href="produk.php" class="btn">Belanja Sekarang</a>
        </div>
    </section>

    <main class="container">
    <section class="products" style="padding: 60px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="font-size: 28px; color: #2f3542; font-weight: 800;">🔥 Produk Terlaris</h2>
        <a href="produk.php" style="color: #ff4757; text-decoration: none; font-weight: bold;">Lihat Semua Katalog →</a>
    </div>

    <div class="product-grid">
        <?php 
        // Query Produk Terlaris berdasarkan tabel pembelian_produk
        $ambil = $conn->query("SELECT p.*, SUM(pp.jumlah) as total_terjual 
                               FROM produk p 
                               JOIN pembelian_produk pp ON p.id = pp.id_produk 
                               GROUP BY p.id 
                               ORDER BY total_terjual DESC 
                               LIMIT 4");
        
        // Cadangan jika belum ada penjualan, ambil produk terbaru
        if ($ambil->num_rows == 0) {
            $ambil = $conn->query("SELECT * FROM produk ORDER BY id DESC LIMIT 4");
        }

        while($row = $ambil->fetch_assoc()):
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="img/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama']; ?>">
            </div>
            <div class="product-info">
                <div>
                    <h3 title="<?php echo $row['nama']; ?>"><?php echo $row['nama']; ?></h3>
                    <p class="price" style="color: #ff4757; font-weight: 800; font-size: 18px;">
                        Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                    </p>
                    <?php if(isset($row['total_terjual'])): ?>
                        <p style="font-size: 12px; color: #747d8c; margin-top: 5px;">
                            <i class="fa-solid fa-fire"></i> <?php echo $row['total_terjual']; ?> Terjual
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="product-buttons" style="margin-top: 15px; display: flex; gap: 10px;">
                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-detail" style="flex: 1; text-align: center; padding: 10px; border: 1px solid #ff4757; color: #ff4757; border-radius: 8px; text-decoration: none;">Detail</a>
                    <a href="beli.php?id=<?php echo $row['id']; ?>" class="btn-cart" style="flex: 2; background: #ff4757; color: #fff; border-radius: 8px; text-align: center; padding: 10px; text-decoration: none;">+ Keranjang</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>
</main>
    <footer style="text-align: center; padding: 50px 0; color: #a4b0be; font-size: 14px;">
        <p>&copy; 2024 PlanetJersey. All rights reserved.</p>
    </footer>

</body>
</html>