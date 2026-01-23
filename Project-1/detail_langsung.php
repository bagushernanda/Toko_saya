<?php 
session_start();
include 'koneksi.php'; 

// Ambil ID dari URL
$id_produk = $_GET['id'];

// Ambil data produk
$query = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p 
                              JOIN kategori k ON p.kategori_id = k.id 
                              WHERE p.id = '$id_produk'");
$detail = mysqli_fetch_assoc($query);

if (!$detail) {
    echo "<script>alert('Produk tidak ditemukan');location='index.php';</script>";
    exit();
}

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
    <title><?php echo $detail['nama']; ?> - PlanetHanduk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; padding: 40px; max-width: 1200px; margin: auto; }
        .img-container img { width: 100%; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .price-tag { color: #ff4757; font-size: 2.5rem; font-weight: bold; margin: 15px 0; }
        .selection-box { background: #f8f9fa; padding: 25px; border-radius: 15px; border: 1px solid #eee; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        .form-control { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; outline: none; }
        .btn-beli { background: #ff4757; color: white; border: none; padding: 15px; width: 100%; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-beli:disabled { background: #ccc; cursor: not-allowed; }
        #info-stok { margin-top: 10px; font-size: 14px; }
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

    <div class="container">
        <div class="detail-wrapper">
            <div class="img-container">
                <img src="img/<?php echo $detail['gambar']; ?>" alt="<?php echo $detail['nama']; ?>">
            </div>

            <div class="info-container">
                <p style="color: #ff4757; text-transform: uppercase; font-weight: bold; font-size: 14px;"><?php echo $detail['nama_kategori']; ?></p>
                <h1><?php echo $detail['nama']; ?></h1>
                
                <div class="price-tag" id="display-harga">Rp <?php echo number_format($detail['harga'], 0, ',', '.'); ?></div>
                
                <p style="color: #666; line-height: 1.6; margin-bottom: 25px;"><?php echo $detail['deskripsi']; ?></p>

                <div class="selection-box">
                    <form action="beli.php" method="post">
                        <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">

                        <div class="form-group">
                            <label>Pilih Warna</label>
                            <select id="pilih-warna" class="form-control" required>
                                <option value="">-- Pilih Warna --</option>
                                <?php 
                                // Group by warna agar tidak duplikat
                                $warna_q = $conn->query("SELECT variasi FROM produk_variasi WHERE id_produk = '$id_produk' GROUP BY variasi");
                                while($w = $warna_q->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $w['variasi']; ?>"><?php echo $w['variasi']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Ukuran</label>
                            <select name="id_variasi" id="pilih-ukuran" class="form-control" disabled required>
                                <option value="">-- Pilih Warna Dulu --</option>
                            </select>
                        </div>

                        <div id="info-stok"></div>

                        <div class="form-group" style="margin-top: 20px;">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah" value="1" min="1" class="form-control" style="width: 100px;">
                        </div>

                        <button type="submit" name="beli" id="btn-submit" class="btn-beli" disabled>
                            <i class="fa-solid fa-cart-shopping"></i> Beli Langsung
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // 1. Saat Warna dipilih, ambil ukuran menggunakan AJAX
        $('#pilih-warna').change(function() {
            var warna = $(this).val();
            var id_p = "<?php echo $id_produk; ?>";

            if (warna != "") {
                $.ajax({
                    type: 'POST',
                    url: 'ambil_data_variasi.php',
                    data: {warna: warna, id_produk: id_p, type: 'get_ukuran'},
                    success: function(res) {
                        $('#pilih-ukuran').html(res).prop('disabled', false);
                        $('#display-harga').text("Rp <?php echo number_format($detail['harga'], 0, ',', '.'); ?>"); // Reset harga ke default
                        $('#info-stok').html('');
                        $('#btn-submit').prop('disabled', true);
                    }
                });
            } else {
                $('#pilih-ukuran').html('<option value="">-- Pilih Warna Dulu --</option>').prop('disabled', true);
                $('#btn-submit').prop('disabled', true);
            }
        });

        // 2. Saat Ukuran dipilih, ambil harga dan stok
        $(document).on('change', '#pilih-ukuran', function() {
            var id_v = $(this).val();
            var selected = $(this).find('option:selected');
            var harga = selected.data('harga');
            var stok = selected.data('stok');

            if (id_v != "") {
                // Update Harga
                var formattedHarga = new Intl.NumberFormat('id-ID').format(harga);
                $('#display-harga').text('Rp ' + formattedHarga);
                
                // Update Stok
                $('#info-stok').html('<i class="fa-solid fa-box"></i> Stok tersedia: <strong>' + stok + ' pcs</strong>');
                $('#btn-submit').prop('disabled', false);
                $('input[name="jumlah"]').attr('max', stok);
            } else {
                $('#btn-submit').prop('disabled', true);
            }
        });
    });
    </script>
</body>
</html>