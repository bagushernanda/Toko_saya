<?php 
session_start();
// Memanggil file koneksi
include 'koneksi.php'; 
// Hitung total item di keranjang
$total_item = 0;
if (isset($_SESSION['keranjang'])) {
    $total_item = array_sum($_SESSION['keranjang']);
}
// 1. Ambil kata kunci dari URL
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$id_kat = isset($_GET['id_kategori']) ? $_GET['id_kategori'] : '';

// 2. Logika Query Dinamis
if ($keyword !== '') {
    // Jika ada pencarian
    $judul_halaman = "Hasil Pencarian: '$keyword'";
    $query = "SELECT p.*, k.nama_kategori FROM produk p 
              JOIN kategori k ON p.kategori_id = k.id 
              WHERE p.nama LIKE '%$keyword%' OR p.deskripsi LIKE '%$keyword%'";
} elseif ($id_kat !== '') {
    // Jika ada filter kategori
    $res_kat = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id = '$id_kat'");
    $data_kat = mysqli_fetch_assoc($res_kat);
    $judul_halaman = "Kategori: " . ($data_kat['nama_kategori'] ?? 'Tidak Ditemukan');
    $query = "SELECT p.*, k.nama_kategori FROM produk p 
              JOIN kategori k ON p.kategori_id = k.id 
              WHERE p.kategori_id = '$id_kat'";
} else {
    // Default: Tampilkan semua
    $judul_halaman = "Katalog Produk";
    $query = "SELECT p.*, k.nama_kategori FROM produk p 
              JOIN kategori k ON p.kategori_id = k.id";
}

// 3. Eksekusi Query
$result = mysqli_query($conn, $query);

// Cek error SQL jika ada
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Produk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="produk-style.css">
</head>
<body>
    <header class="main-navbar">
    <div class="nav-container">
        <div class="logo">
            <a href="index.php">Planet<span>Handuk</span></a>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="produk.php" class="active">Produk</a></li>
            <li><a href="kategori.php">Kategori</a></li>
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

    <main class="product-container">
        <aside class="sidebar">
            <h3>Filter</h3>
            <div class="filter-group">
                <h4>Kategori</h4>
                <ul>
                    <li><input type="checkbox"> Elektronik</li>
                    <li><input type="checkbox"> Fashion</li>
                    <li><input type="checkbox"> Aksesoris</li>
                </ul>
            </div>
            <div class="filter-group">
                <h4>Rentang Harga</h4>
                <input type="range" min="0" max="2000000" step="50000">
                <p>Rp 0 - Rp 2jt+</p>
            </div>
            <button class="btn-filter">Terapkan</button>
        </aside>

        <section class="catalog">
                <div class="catalog-header">
                    <h2><?php echo $judul_halaman; ?></h2>
                    <div class="header-info">
            <p>Menampilkan <strong>1-12</strong> dari 50 Produk</p>
                </div>
                    <div class="header-filter">
                        <label for="sort">Urutkan:</label>
                        <select id="sort" class="sort-select">
                            <option>Produk Terbaru</option>
                            <option>Harga: Rendah ke Tinggi</option>
                            <option>Harga: Tinggi ke Rendah</option>
                            <option>Paling Laris</option>
                        </select>
                    </div>
                </div>

                <div class="product-grid">
                    <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="img/<?php echo $row['gambar']; ?>" alt="">
                                </div>
                                <div class="product-info">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                        <small><?php echo $row['nama_kategori']; ?></small>
                                        <small style="color: <?php echo $row['stok'] > 0 ? '#2ed573' : '#ff4757'; ?>; font-weight: bold;">
                                            Stok: <?php echo $row['stok']; ?>
                                        </small>
                                    </div>
                                    
                                    <h3><?php echo $row['nama']; ?></h3>
                                    <p class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                    
                                    <div class="action-buttons">
                                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-detail">Detail</a>
                                        <?php if($row['stok'] > 0): ?>
                                            <a href="beli.php?id=<?php echo $row['id']; ?>" class="btn-cart">+ Keranjang</a>
                                        <?php else: ?>
                                            <button class="btn-cart" style="background: #ccc; cursor: not-allowed;" disabled>Habis</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                            <p>Maaf, produk "<strong><?php echo htmlspecialchars($keyword); ?></strong>" tidak ditemukan.</p>
                            <a href="produk.php" style="color: #ff4757;">Lihat Semua Produk</a>
                        </div>
                    <?php endif; ?>
                </div>
                
            <div class="pagination">
                <span class="active">1</span>
                <span>2</span>
                <span>3</span>
                <span><i class="fas fa-chevron-right"></i></span>
            </div>
        </section>
    </main>

</body>
</html>