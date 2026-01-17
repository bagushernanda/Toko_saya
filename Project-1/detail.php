<?php 
session_start();
include 'koneksi.php'; 

// Ambil ID dari URL
$id_produk = $_GET['id'];

// Ambil data produk berdasarkan ID (Tanpa mengambil kolom stok utama)
$query = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p 
                              JOIN kategori k ON p.kategori_id = k.id 
                              WHERE p.id = '$id_produk'");
$detail = mysqli_fetch_assoc($query);

// Jika produk tidak ditemukan
if (!$detail) {
    echo "<script>alert('Produk tidak ditemukan');location='produk.php';</script>";
    exit();
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
        .selection-container { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px; border: 1px solid #eee; }
        .form-control { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; margin-bottom: 10px; }
        .btn-cart:disabled { background: #ccc !important; cursor: not-allowed; }
        #info-stok { margin-bottom: 15px; font-weight: bold; transition: 0.3s; }
        .stok-ada { color: #2ed573; }
        .stok-limit { color: #ffa502; }
    </style>
</head>
<body>
    <div class="container" style="padding: 40px 20px;">
        <a href="produk.php" style="text-decoration: none; color: #ff4757; font-weight: bold;">← Kembali ke Katalog</a>
        
        <div class="detail-row" style="margin-top: 20px; display: flex; gap: 40px;">
            <div class="detail-col" style="flex: 1;">
                <img src="img/<?php echo $detail['gambar']; ?>" alt="<?php echo $detail['nama']; ?>" style="width: 100%; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            </div>
            
            <div class="detail-col" style="flex: 1;">
                <h6 style="color: #747d8c; text-transform: uppercase; letter-spacing: 1px;">Home / <?php echo $detail['nama_kategori']; ?></h6>
                <h2 style="font-size: 2.5rem; margin: 10px 0;"><?php echo $detail['nama']; ?></h2>
                <h3 class="price" style="color: #ff4757; font-size: 1.8rem; margin-bottom: 20px;">Rp <?php echo number_format($detail['harga'], 0, ',', '.'); ?></h3>
                
                <div class="selection-container">
                    <div class="form-group">
                        <label><strong>Pilih Warna:</strong></label>
                        <select id="pilih-warna" class="form-control">
                            <option value="">-- Pilih Warna --</option>
                            <?php 
                            $ambil_warna = $conn->query("SELECT DISTINCT warna FROM produk_variasi WHERE id_produk = '$id_produk' AND stok > 0");
                            while($w = $ambil_warna->fetch_assoc()): ?>
                                <option value="<?php echo $w['warna']; ?>"><?php echo $w['warna']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <form action="beli.php" method="post">
                        <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">
                        
                        <label><strong>Pilih Ukuran:</strong></label>
                        <select name="id_variasi" id="pilih-ukuran" class="form-control" required disabled>
                            <option value="">-- Pilih Warna Terlebih Dahulu --</option>
                        </select>

                        <div id="info-stok"></div>

                        <div class="action-bar" style="display: flex; gap: 10px; margin-top: 5px;">
                            <input type="number" name="jumlah" value="1" min="1" class="form-control" style="width: 80px; text-align: center;" required>
                            
                            <button type="submit" name="beli" id="btn-submit" class="btn-cart" style="flex: 1; background: #ff4757; color: white; border: none; padding: 10px; border-radius: 5px; font-weight: bold; cursor: pointer;" disabled>
                                <i class="fa-solid fa-cart-shopping"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </form>
                </div>

                <div style="margin-top: 30px;">
                    <h4>Detail Produk</h4>
                    <p style="color: #4b4b4b; line-height: 1.6;"><?php echo nl2br($detail['deskripsi']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function(){
        // Reset tampilan stok
        function resetStok() {
            $("#info-stok").html("").removeClass("stok-ada stok-limit");
            $("#btn-submit").prop("disabled", true);
        }

        // Logic saat warna dipilih
        $("#pilih-warna").on("change", function(){
            var warna_terpilih = $(this).val();
            var id_p = "<?php echo $id_produk; ?>";
            resetStok();
            
            if(warna_terpilih !== ""){
                $.ajax({
                    type: 'POST',
                    url: 'ambil-ukuran.php',
                    data: {warna: warna_terpilih, id_produk: id_p},
                    success: function(hasil){
                        $("#pilih-ukuran").html(hasil).prop("disabled", false);
                    }
                });
            } else {
                $("#pilih-ukuran").html("<option value=''>-- Pilih Warna Terlebih Dahulu --</option>").prop("disabled", true);
            }
        });

        // Logic saat ukuran dipilih (Menampilkan Stok)
        $("#pilih-ukuran").on("change", function(){
            var id_v = $(this).val();
            var teks_option = $("#pilih-ukuran option:selected").text();

            if(id_v !== ""){
                // Ekstrak angka stok dari teks option "Ukuran (Stok: 10)"
                var stokMatch = teks_option.match(/\d+/);
                var stok = stokMatch ? parseInt(stokMatch[0]) : 0;

                $("#info-stok").html("<i class='fa-solid fa-box-open'></i> Stok tersedia: " + stok + " pcs");
                $("#info-stok").addClass(stok < 5 ? "stok-limit" : "stok-ada");
                $("#btn-submit").prop("disabled", false);
                
                // Set batas maksimal input jumlah sesuai stok
                $("input[name='jumlah']").attr("max", stok);
            } else {
                resetStok();
            }
        });
    });
    </script>
</body>
</html>