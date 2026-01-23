<?php 
session_start();
include 'koneksi.php'; 

// Hitung total item di keranjang
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
    <title>PlanetJersey - Premium Sportswear</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        :root {
            --primary-color: #ff4757;
            --gradient: linear-gradient(135deg, #ff4757 0%, #ff6b81 100%);
            --soft-bg: #f8f9fa;
        }

        /* Hero Section Premium */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            margin-bottom: 50px;
        }

        .hero-content h1 { font-size: 3.5rem; margin-bottom: 20px; text-shadow: 2px 2px 10px rgba(0,0,0,0.3); }
        .hero-content p { font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9; }
        .btn-primary { 
            background: var(--gradient); color: white; padding: 15px 40px; 
            border-radius: 30px; text-decoration: none; font-weight: bold;
            transition: 0.3s; box-shadow: 0 10px 20px rgba(255, 71, 87, 0.3);
        }
        .btn-primary:hover { transform: translateY(-5px); box-shadow: 0 15px 25px rgba(255, 71, 87, 0.5); }

        /* Feature Section */
        .features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 60px; }
        .feature-box { 
            background: white; padding: 30px; border-radius: 20px; text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s;
        }
        .feature-box i { font-size: 2.5rem; color: var(--primary-color); margin-bottom: 15px; }
        .feature-box:hover { transform: scale(1.05); }

        /* Trending Section */
        .section-title { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .section-title h2 { font-size: 1.8rem; font-weight: 800; color: #2f3542; }
        
        /* Swiper Customization */
        .swiper-button-next, .swiper-button-prev { 
            background: white; width: 45px; height: 45px; border-radius: 50%; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); color: var(--primary-color) !important;
        }
        .swiper-button-next:after, .swiper-button-prev:after { font-size: 18px; font-weight: bold; }

        /* Product Card Eye-Catching */
        .trending-card {
            background: white; border-radius: 15px; overflow: hidden; border: 1px solid #eee;
            transition: 0.3s; height: 100%; position: relative;
        }
        .trending-card:hover { transform: translateY(-10px); box-shadow: 0 20px 30px rgba(0,0,0,0.1); }
        .badge-mall {
            position: absolute; top: 10px; left: 10px; background: #d0011b;
            color: white; font-size: 10px; padding: 3px 8px; border-radius: 3px; font-weight: bold; z-index: 5;
        }
        .discount-label {
            position: absolute; top: 0; right: 0; background: #ff4757;
            color: white; padding: 5px 12px; font-weight: bold; border-bottom-left-radius: 15px; z-index: 5;
        }
        .img-container { width: 100%; aspect-ratio: 1/1; overflow: hidden; }
        .img-container img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .trending-card:hover .img-container img { transform: scale(1.1); }
        
        .card-body { padding: 15px; }
        .card-name { font-size: 14px; font-weight: 600; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 10px; color: #2f3542; }
        .card-price { color: var(--primary-color); font-size: 1.2rem; font-weight: 800; }
        .card-sold { font-size: 11px; color: #747d8c; margin-top: 10px; display: flex; align-items: center; gap: 5px; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2rem; }
            .features { grid-template-columns: 1fr; }
            .hero { border-radius: 0; }
        }
    </style>
</head>
<body>

    <header class="main-navbar">
    <div class="nav-container">
        <div class="logo">
            <a href="index.php">Planet<span>Jersey</span></a>
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
                        <a href="user/profil.php" title="Profil Saya">
                            <i class="fa-solid fa-user-check" style="color: #2ed573;"></i>
                        </a>
                    <?php else: ?>
                        <a href="user/login_pelanggan.php" title="Login/Daftar">
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
            <h1>Level Up Your Game</h1>
            <p>Dapatkan Jersey kualitas Grade Ori dengan harga terbaik di Indonesia.</p>
            <a href="produk.php" class="btn-primary">MULAI BELANJA <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </section>

    <main class="container">
        <div class="features">
            <div class="feature-box">
                <i class="fa-solid fa-truck-fast"></i>
                <h3>Pengiriman Cepat</h3>
                <p>Kirim ke seluruh Indonesia dengan proteksi maksimal.</p>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-shield-halved"></i>
                <h3>Kualitas Terjamin</h3>
                <p>Hanya menjual produk kualitas Premium dan Grade Ori.</p>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-headset"></i>
                <h3>CS 24/7</h3>
                <p>Tim kami siap membantu kendala belanja Anda kapan saja.</p>
            </div>
        </div>

        <div class="section-title">
            <div>
                <h2>🔥 Produk Terpopuler</h2>
                <p style="color: #747d8c;">Jersey yang paling banyak dicari minggu ini</p>
            </div>
            <a href="produk.php" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">Lihat Semua</a>
        </div>

        <div class="swiper trendingSwiper">
            <div class="swiper-wrapper">
                <?php 
                // Mengambil produk terlaris berdasarkan transaksi
                $ambil = $conn->query("SELECT p.*, SUM(pp.jumlah) as terjual FROM produk p 
                                       LEFT JOIN pembelian_produk pp ON p.id = pp.id_produk 
                                       GROUP BY p.id ORDER BY terjual DESC LIMIT 10");
                while($row = $ambil->fetch_assoc()):
                ?>
                <div class="swiper-slide">
                    <div class="trending-card">
                        <div class="badge-mall">MALL</div>
                        <div class="discount-label">-10%</div>
                        <div class="img-container">
                            <img src="img/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama']; ?>">
                        </div>
                        <div class="card-body">
                            <div class="card-name"><?php echo $row['nama']; ?></div>
                            <div class="card-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
                            <div class="card-sold">
                                <i class="fa-solid fa-fire-smoke" style="color: #ff9f43;"></i>
                                <?php echo ($row['terjual'] ?? 0); ?>+ Terjual
                            </div>
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-primary" style="display: block; text-align: center; margin-top: 15px; padding: 10px; font-size: 14px;">Detail</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </main>

    <footer style="text-align: center; padding: 50px 0; color: #a4b0be; background: #fff; margin-top: 50px; border-top: 1px solid #eee;">
        <p>&copy; 2024 PlanetJersey. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".trendingSwiper", {
            slidesPerView: 2,
            spaceBetween: 20,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                1024: { slidesPerView: 4 },
                1200: { slidesPerView: 5 },
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
    </script>
</body>
</html>