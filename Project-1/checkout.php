<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if(!isset($_SESSION['pelanggan'])) {
    echo "<script>alert('Silakan login terlebih dahulu!');location='login_pelanggan.php';</script>";
    exit();
}

// 2. Cek Keranjang
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang kosong, silakan belanja dulu!');location='produk.php';</script>";
    exit();
}

// 3. Ambil Data Pelanggan
$id_pelanggan_login = $_SESSION['pelanggan']['id_pelanggan'];
$ambil_pelanggan = $conn->query("SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan_login'");
$data_log = $ambil_pelanggan->fetch_assoc();

// 4. Hitung Total Belanja
$total_belanja = 0;
foreach ($_SESSION['keranjang'] as $id_variasi => $jumlah) {
    $ambil = $conn->query("SELECT p.harga FROM produk_variasi v 
                           JOIN produk p ON v.id_produk = p.id 
                           WHERE v.id_variasi = '$id_variasi'");
    $pecah = $ambil->fetch_assoc();
    if ($pecah) {
        $total_belanja += ($pecah['harga'] * $jumlah);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - PlanetJersey</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .checkout-container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 25px; align-items: start; }
        .card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .card-title { font-size: 1.2rem; font-weight: bold; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.95rem; }
        .total-pay { border-top: 2px dashed #eee; padding-top: 15px; margin-top: 15px; font-size: 1.2rem; font-weight: bold; color: #ff4757; }
        .btn-order { width: 100%; padding: 15px; background: #ff4757; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer; transition: 0.3s; }
        .btn-order:hover { background: #e84118; }
        @media (max-width: 850px) { .checkout-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="checkout-container">
        <h2><i class="fa-solid fa-cash-register"></i> Konfirmasi Pesanan</h2>
        <hr style="margin-bottom: 30px; opacity: 0.2;">

        <div class="checkout-grid">
            <div class="checkout-form">
                <div class="card">
                    <div class="card-title"><i class="fa-solid fa-location-dot" style="color: #ff4757;"></i> Informasi Pengiriman</div>
                    <form method="post" id="form-checkout">
                        <div class="form-group">
                            <label>Nama Lengkap Penerima</label>
                            <input type="text" name="nama" class="form-control" value="<?php echo $data_log['nama_pelanggan']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon / WhatsApp</label>
                            <input type="text" name="telepon" class="form-control" value="<?php echo $data_log['telepon_pelanggan']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat Lengkap (Jalan, No. Rumah, Kec, Kota)</label>
                            <textarea name="alamat" class="form-control" rows="3" required><?php echo $data_log['alamat_pelanggan']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Wilayah Pengiriman (Ongkir)</label>
                            <select name="ongkir" class="form-control" id="ongkir-select" required onchange="updateTotal()">
                                <option value="" data-price="0">-- Pilih Wilayah --</option>
                                <option value="10000" data-price="10000">Jabodetabek (Rp 10.000)</option>
                                <option value="20000" data-price="20000">Pulau Jawa (Rp 20.000)</option>
                                <option value="50000" data-price="50000">Luar Pulau Jawa (Rp 50.000)</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-title"><i class="fa-solid fa-box" style="color: #ff4757;"></i> Detail Item</div>
                    <?php foreach ($_SESSION["keranjang"] as $id_variasi => $jumlah): 
                        $ambil = $conn->query("SELECT p.nama, p.harga, v.warna, v.ukuran 
                                               FROM produk_variasi v 
                                               JOIN produk p ON v.id_produk = p.id 
                                               WHERE v.id_variasi = '$id_variasi'");
                        $pecah = $ambil->fetch_assoc();
                    ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 10px 0;">
                        <div>
                            <strong><?php echo $pecah['nama']; ?></strong><br>
                            <small style="color: #777;">Varian: <?php echo $pecah['warna']; ?> | <?php echo $pecah['ukuran']; ?></small>
                        </div>
                        <div style="text-align: right;">
                            <span><?php echo $jumlah; ?> x </span>
                            <strong>Rp <?php echo number_format($pecah['harga']); ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="checkout-summary">
                <div class="card" style="position: sticky; top: 20px;">
                    <div class="card-title">Ringkasan Belanja</div>
                    <div class="summary-item">
                        <span>Total Harga (Produk)</span>
                        <span>Rp <?php echo number_format($total_belanja); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Biaya Pengiriman</span>
                        <span id="label-ongkir">Rp 0</span>
                    </div>
                    <div class="summary-item total-pay">
                        <span>Total Tagihan</span>
                        <span id="label-total">Rp <?php echo number_format($total_belanja); ?></span>
                    </div>
                    <p style="font-size: 0.8rem; color: #888; margin: 15px 0;">* Dengan mengklik tombol di bawah, Anda setuju dengan syarat dan ketentuan PlanetJersey.</p>
                    <button type="submit" form="form-checkout" name="checkout" class="btn-order">PESAN SEKARANG</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTotal() {
            const ongkirSelect = document.getElementById('ongkir-select');
            const ongkir = parseInt(ongkirSelect.value) || 0;
            const subtotal = <?php echo $total_belanja; ?>;
            const total = subtotal + ongkir;

            document.getElementById('label-ongkir').innerText = "Rp " + ongkir.toLocaleString('id-ID');
            document.getElementById('label-total').innerText = "Rp " + total.toLocaleString('id-ID');
        }
    </script>

    <?php
    if (isset($_POST["checkout"])) {
        $ongkir_pilih = $_POST['ongkir'];
        $total_pembelian = $total_belanja + $ongkir_pilih;
        $tanggal_pembelian = date("Y-m-d H:i:s");
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $telp = mysqli_real_escape_string($conn, $_POST['telepon']);
        $almt = mysqli_real_escape_string($conn, $_POST['alamat']);

        // 1. Simpan Pembelian
        $conn->query("INSERT INTO pembelian (id_pelanggan, nama_penerima, telepon, alamat_lengkap, tanggal_pembelian, total_pembelian, status_pembelian) 
                      VALUES ('$id_pelanggan_login', '$nama', '$telp', '$almt', '$tanggal_pembelian', '$total_pembelian', 'Pending')");
        
        $id_pembelian_baru = $conn->insert_id;

        // 2. Simpan Detail & Update Stok
        foreach ($_SESSION["keranjang"] as $id_variasi => $jumlah) {
            $ambil_v = $conn->query("SELECT id_produk FROM produk_variasi WHERE id_variasi = '$id_variasi'");
            $data_v = $ambil_v->fetch_assoc();
            $id_p_asli = $data_v['id_produk'];

            // Simpan id_variasi ke database
            $conn->query("INSERT INTO pembelian_produk (id_pembelian, id_produk, id_variasi, jumlah) 
                          VALUES ('$id_pembelian_baru', '$id_p_asli', '$id_variasi', '$jumlah')");
            
            // Potong stok variasi
            $conn->query("UPDATE produk_variasi SET stok = stok - $jumlah WHERE id_variasi = '$id_variasi'");

            // Sinkronkan ke stok utama produk
            $ambil_total = $conn->query("SELECT SUM(stok) as total FROM produk_variasi WHERE id_produk = '$id_p_asli'");
            $stok_total_baru = $ambil_total->fetch_assoc()['total'];
            $conn->query("UPDATE produk SET stok = '$stok_total_baru' WHERE id = '$id_p_asli'");
        }

        unset($_SESSION["keranjang"]);
        echo "<script>alert('Pembelian Berhasil!');location='nota.php?id=$id_pembelian_baru';</script>";
    }
    ?>
</body>
</html>