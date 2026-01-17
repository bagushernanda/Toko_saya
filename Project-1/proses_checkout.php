<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama   = $_POST['nama'];
    $email  = $_POST['email'];
    $alamat = $_POST['alamat'] . ", " . $_POST['kota'] . " (" . $_POST['kode_pos'] . ")";
    
    // 1. Hitung total bayar dulu dari session
    $total_bayar = 0;
    foreach ($_SESSION['keranjang'] as $id => $qty) {
        $res = mysqli_query($conn, "SELECT harga FROM produk WHERE id = '$id'");
        $row = mysqli_fetch_assoc($res);
        $total_bayar += ($row['harga'] * $qty);
    }

    // 2. Panggil Procedure untuk simpan ke tabel 'pesanan'
    // Menggunakan variabel @last_id untuk menangkap ID pesanan
    mysqli_query($conn, "CALL sp_SimpanPesanan('$nama', '$email', '$alamat', $total_bayar, @last_id)");
    $res_id = mysqli_query($conn, "SELECT @last_id as id");
    $row_id = mysqli_fetch_assoc($res_id);
    $id_pesanan_baru = $row_id['id'];

    // 3. Simpan rincian barang ke tabel 'pesanan_item'
    foreach ($_SESSION['keranjang'] as $id_produk => $jumlah) {
        mysqli_query($conn, "INSERT INTO pesanan_item (pesanan_id, produk_id, jumlah) 
                             VALUES ('$id_pesanan_baru', '$id_produk', '$jumlah')");
    }

    // 4. Kosongkan keranjang belanja
    unset($_SESSION['keranjang']);

    // 5. Alihkan ke halaman sukses
    echo "<script>alert('Pesanan Berhasil Disimpan! Terima kasih telah berbelanja.');</script>";
    echo "<script>location='index.php';</script>";
}
?>