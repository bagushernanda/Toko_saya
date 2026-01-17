<?php 
session_start(); //
include 'koneksi.php'; 

// Hitung total item di keranjang untuk navbar
$total_item = 0;
if (isset($_SESSION['keranjang'])) {
    $total_item = array_sum($_SESSION['keranjang']); //
}

// Query mengambil data kategori dan gambar dari database
$sql_kategori = "SELECT k.id, k.nama_kategori, k.gambar_kategori, COUNT(p.id) as total_produk 
                 FROM kategori k 
                 LEFT JOIN produk p ON k.id = p.kategori_id 
                 GROUP BY k.id";
$result_kategori = mysqli_query($conn, $sql_kategori);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kategori Produk - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <header class="main-navbar">
    <div class="nav-container">
        <div class="logo">
            <a href="index.php">Planet<span>Handuk</span></a>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="produk.php">Produk</a></li>
            <li><a href="kategori.php" class="active">Kategori</a></li>
            <li><a href="#">Kontak</a></li>
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

    <main class="container">
        <div class="category-header">
            <h2>Pilih Kategori</h2>
            <p>Temukan koleksi handuk terbaik berdasarkan kategori Anda</p>
        </div>

            <div class="category-grid">
                <?php while($row = mysqli_fetch_assoc($result_kategori)): ?>
                    <a href="daftar_kategori.php?id=<?php echo $row['id']; ?>" class="category-card">
                        <img src="img/<?php echo $row['gambar_kategori']; ?>" alt="<?php echo $row['nama_kategori']; ?>">
                        <div class="category-overlay">
                            <h3><?php echo $row['nama_kategori']; ?></h3>
                            <span><?php echo $row['total_produk']; ?> Produk</span>
                        </div>
                    </a>
                <?php endwhile; ?>
        </div>
    </main>

</body>
</html>