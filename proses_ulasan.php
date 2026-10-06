<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'config/database.php';
include_once 'config/helpers.php';

if (($_SESSION['role'] ?? '') !== 'pelanggan' || !isset($_SESSION['id_user'])) {
    wg_flash_redirect('auth/login.php', 'Silakan login sebagai pelanggan untuk memberi ulasan.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id_produk = (int)($_POST['id_produk'] ?? 0);
$rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
$komentar = trim($_POST['komentar'] ?? '');
$id_user = (int)$_SESSION['id_user'];
$nama_user = mysqli_real_escape_string($conn, $_SESSION['nama'] ?? 'Pengguna');

if ($id_produk <= 0 || $komentar === '') {
    wg_flash_redirect('detail.php?id=' . $id_produk, 'Rating dan komentar wajib diisi.');
}

// Hanya pelanggan yang memang pernah membeli produk ini yang bisa mengulas.
$cek_beli = mysqli_query($conn, "SELECT d.id_produk FROM tb_detail d JOIN tb_transaksi t ON d.id_transaksi=t.id_transaksi WHERE t.id_pelanggan=$id_user AND d.id_produk=$id_produk LIMIT 1");
if (!$cek_beli || mysqli_num_rows($cek_beli) === 0) {
    wg_flash_redirect('detail.php?id=' . $id_produk, 'Ulasan hanya dapat diberikan setelah kamu membeli produk ini.');
}

$komentar_sql = mysqli_real_escape_string($conn, $komentar);
$query = "INSERT INTO tb_ulasan (id_produk, id_user, nama_user, rating, komentar, tanggal) VALUES ($id_produk, $id_user, '$nama_user', $rating, '$komentar_sql', NOW())";
if (mysqli_query($conn, $query)) {
    wg_flash_redirect('detail.php?id=' . $id_produk, 'Terima kasih atas ulasan kamu!');
}

wg_flash_redirect('detail.php?id=' . $id_produk, 'Gagal mengirim ulasan. Silakan coba lagi.');
