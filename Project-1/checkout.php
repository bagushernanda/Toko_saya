<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if(!isset($_SESSION['pelanggan'])) {
    echo "<script>alert('Silakan login terlebih dahulu!');location='login_pelanggan.php';</script>";
    exit();
}

// 2. Ambil Data Pelanggan yang sedang Login
$id_pelanggan_login = $_SESSION['pelanggan']['id_pelanggan'];
$ambil_pelanggan = $conn->query("SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan_login'");
$data_log = $ambil_pelanggan->fetch_assoc();

// 3. Cek Keranjang
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang kosong, silakan belanja dulu!');location='produk.php';</script>";
    exit();
}

// 4. Hitung Total Belanja Produk
$total_belanja = 0;
foreach ($_SESSION['keranjang'] as $id_produk => $jumlah) {
    $ambil = $conn->query("SELECT harga FROM produk WHERE id='$id_produk'");
    $pecah = $ambil->fetch_assoc();
    $total_belanja += ($pecah['harga'] * $jumlah);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - PlanetHanduk</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container { max-width: 1000px; margin: 50px auto; padding: 20px; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 30px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; margin-top: 5px; margin-bottom: 15px; display: block; box-sizing: border-box; }
        .btn-checkout { width: 100%; padding: 15px; background: #ff4757; color: white; border: none; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-checkout:hover { background: #e84118; }
    </style>
</head>
<body style="background: #f1f2f6;">

    <main class="container">
        <h2><i class="fa-solid fa-bag-shopping"></i> Checkout Pembayaran</h2>
        
        <div class="checkout-grid">
            <div class="card">
                <form method="post">
                    <label>Nama Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" value="<?php echo $data_log['nama_pelanggan']; ?>" readonly>

                    <label>Nama Penerima</label>
                    <input type="text" name="nama" class="form-control" value="<?php echo $data_log['nama_pelanggan']; ?>" required>

                    <label>Nomor Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="<?php echo $data_log['telepon_pelanggan']; ?>" required>

                    <label>Alamat Lengkap Pengiriman</label>
                    <textarea name="alamat" class="form-control" rows="4" required><?php echo $data_log['alamat_pelanggan']; ?></textarea>

                    <label>Pilih Ongkos Kirim</label>
                    <select name="ongkir" id="ongkir" class="form-control" onchange="hitungTotal()" required>
                        <option value="0">-- Pilih Wilayah --</option>
                        <option value="10000">Jabodetabek (Rp 10.000)</option>
                        <option value="20000">Pulau Jawa (Rp 20.000)</option>
                        <option value="50000">Luar Pulau Jawa (Rp 50.000)</option>
                    </select>

                    <button name="checkout" class="btn-checkout">BUAT PESANAN SEKARANG</button>
                    <a href="produk.php" style="display:block; text-align:center; margin-top:15px; color:#747d8c; text-decoration:none;">Batal</a>
                </form>
            </div>

            <div class="card" style="height: fit-content;">
                <h4 style="margin-top:0;">Ringkasan Belanja</h4>
                <hr style="opacity: 0.1;">
                <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                    <span>Total Produk:</span>
                    <strong>Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                    <span>Ongkos Kirim:</span>
                    <strong id="tampil-ongkir">Rp 0</strong>
                </div>
                <hr style="opacity: 0.1;">
                <div style="display: flex; justify-content: space-between; margin: 15px 0; font-size: 1.2em; color: #ff4757;">
                    <span>Total Bayar:</span>
                    <strong id="total-bayar">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></strong>
                </div>
            </div>
        </div>
    </main>

    <script>
        function hitungTotal() {
            var totalProduk = <?php echo $total_belanja; ?>;
            var ongkir = parseInt(document.getElementById('ongkir').value);
            
            document.getElementById('tampil-ongkir').innerText = "Rp " + ongkir.toLocaleString('id-ID');
            document.getElementById('total-bayar').innerText = "Rp " + (totalProduk + ongkir).toLocaleString('id-ID');
        }
    </script>

    <?php
    if (isset($_POST["checkout"])) {
        $ongkir_pilih = $_POST['ongkir'];
        $total_pembelian = $total_belanja + $ongkir_pilih;
        $tanggal_pembelian = date("Y-m-d H:i:s"); // Menghindari data null
        $nama_penerima = $_POST['nama'];
        $telepon = $_POST['telepon'];
        $alamat = $_POST['alamat']; 


        // 1. Simpan ke tabel pembelian
        $conn->query("INSERT INTO pembelian (id_pelanggan, nama_penerima, telepon, alamat_lengkap, tanggal_pembelian, total_pembelian, status_pembelian) 
                      VALUES ('$id_pelanggan_login', '$nama_penerima', '$telepon', '$alamat', '$tanggal_pembelian', '$total_pembelian', 'Pending')");
        
        $id_pembelian_baru = $conn->insert_id;

        // 2. Simpan ke tabel pembelian_produk & Potong Stok
        foreach ($_SESSION["keranjang"] as $id_produk => $jumlah) {
            $conn->query("INSERT INTO pembelian_produk (id_pembelian, id_produk, jumlah) 
                          VALUES ('$id_pembelian_baru', '$id_produk', '$jumlah')");
            
            // LOGIKA POTONG STOK
            $conn->query("UPDATE produk SET stok = stok - $jumlah WHERE id = '$id_produk'");
        }

        // 3. Kosongkan Keranjang Belanja
        unset($_SESSION["keranjang"]);

        // 4. Alihkan ke halaman nota
        echo "<script>alert('Pembelian Sukses!');</script>";
        echo "<script>location='nota.php?id=$id_pembelian_baru';</script>";
    }
    ?>

</body>
</html>