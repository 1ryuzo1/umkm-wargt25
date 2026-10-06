<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini khusus administrator.');
}
// Utilitas backfill: hanya diperlukan untuk data produk lama yang kolom
// kategorinya masih kosong (mis. sebelum perbaikan import_menu.php).
// Jika produk sudah diimpor lewat import_menu.php versi terbaru, kategori
// sudah terisi otomatis dan script ini tidak wajib dijalankan lagi.
include_once 'config/database.php';
include_once 'config/helpers.php';

// Deteksi nama kolom nama produk yang benar di database
$col_nama = 'nama';
$cek = mysqli_query($conn, "SHOW COLUMNS FROM tb_produk");
if ($cek) {
    while ($row = mysqli_fetch_assoc($cek)) {
        if (strtolower($row['Field']) == 'nama_produk') {
            $col_nama = 'nama_produk';
        }
    }
}

// PENTING: urutan dari yang paling spesifik ke paling umum, supaya nama
// seperti "Nasi Ayam" / "Nasi Dadar" (Makanan Berat) tidak tertimpa oleh
// aturan umum "%Nasi%" (Nasi Nasian) yang dijalankan belakangan.
if (!in_array('kategori', wg_detect_columns($conn, 'tb_produk'), true)) { wg_flash_redirect('admin/produk.php', 'Kolom kategori pada tb_produk tidak ditemukan.'); }

mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Bakar-bakaran' WHERE $col_nama LIKE '%Bakar%'");
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Goreng-gorengan' WHERE $col_nama LIKE '%Goreng%' OR $col_nama LIKE '%Bulat%'");
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Minuman Hangat' WHERE $col_nama LIKE '%Hangat%' OR $col_nama LIKE '%Jahe%'");
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Minuman Es' WHERE $col_nama LIKE '%Dingin%' OR $col_nama LIKE '%Teajus%' OR $col_nama LIKE '%Marimas%' OR $col_nama LIKE '%Top Ice%'");
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Minuman Biasa' WHERE $col_nama LIKE '%Biasa%'");
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Aneka Jus' WHERE $col_nama LIKE '%Pop Ice%' OR $col_nama LIKE '%Jus%'");
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Snack' WHERE ($col_nama LIKE '%Chitato%' OR $col_nama LIKE '%Chiki%' OR $col_nama LIKE '%Oreo%' OR $col_nama LIKE '%Tango%' OR $col_nama LIKE '%Nabati%' OR $col_nama LIKE '%Beng Beng%' OR $col_nama LIKE '%Pringles%' OR $col_nama LIKE '%Qtela%' OR $col_nama LIKE '%Cheetos%' OR $col_nama LIKE '%Taro%' OR $col_nama LIKE '%Pilus%' OR $col_nama LIKE '%Astor%' OR $col_nama LIKE '%Better%' OR $col_nama LIKE '%Chocolatos%' OR $col_nama LIKE '%Good Time%' OR $col_nama LIKE '%Malkist%' OR $col_nama LIKE '%Nextstar%' OR $col_nama LIKE '%Tricks%' OR $col_nama LIKE '%Waffelo%' OR $col_nama LIKE '%Zetz%' OR $col_nama LIKE '%Sip%' OR $col_nama LIKE '%Ahh%' OR $col_nama LIKE '%Dilan%' OR $col_nama LIKE '%Supreme%' OR $col_nama LIKE '%Enaak%' OR $col_nama LIKE '%Rin-bae%' OR $col_nama LIKE '%Tic-Tic%' OR $col_nama LIKE '%Gerry%' OR $col_nama LIKE '%Selai Olays%' OR $col_nama LIKE '%Biskitop%' OR $col_nama LIKE '%So Nice%')");

// Aturan spesifik Makanan Berat DIJALANKAN SEBELUM aturan umum "Nasi Nasian"
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Makanan Berat' WHERE $col_nama LIKE '%Sop%' OR $col_nama LIKE '%Nasi Ayam%' OR $col_nama LIKE '%Nasi Dadar%'");

// Aturan umum "Nasi" dijalankan TERAKHIR supaya tidak menimpa Makanan Berat di atas
mysqli_query($conn, "UPDATE tb_produk SET kategori = 'Nasi Nasian' WHERE ($col_nama LIKE '%Nasi%' OR $col_nama LIKE '%Bubur%') AND kategori NOT IN ('Makanan Berat')");

echo "<script>alert('Kategori berhasil diperbarui!'); window.location='admin/produk.php';</script>";
