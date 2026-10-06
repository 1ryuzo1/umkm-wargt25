<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'config/database.php';
include_once 'config/helpers.php';

if (!isset($_SESSION['role'])) {
    wg_flash_redirect('auth/login.php', 'Anda harus login terlebih dahulu untuk memesan.');
}

if (isset($_POST['tambah'])) {
    $id_produk = (int)($_POST['id_produk'] ?? 0);
    $varian = trim($_POST['varian'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    $query = mysqli_query($conn, "SELECT id, stok FROM tb_produk WHERE id = $id_produk LIMIT 1");
    $produk = $query ? mysqli_fetch_assoc($query) : null;
    if (!$produk) {
        wg_flash_redirect('menu.php', 'Produk tidak ditemukan.');
    }
    if ((int)$produk['stok'] < 1) {
        wg_flash_redirect('menu.php', 'Stok produk habis.');
    }

    if (!isset($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }
    if (!isset($_SESSION['keranjang_opsi']) || !is_array($_SESSION['keranjang_opsi'])) {
        $_SESSION['keranjang_opsi'] = [];
    }

    $qty_sekarang = isset($_SESSION['keranjang'][$id_produk]) ? (int)$_SESSION['keranjang'][$id_produk] : 0;
    if ($qty_sekarang >= (int)$produk['stok']) {
        wg_flash_redirect('keranjang.php', 'Jumlah di keranjang sudah mencapai stok yang tersedia.');
    }

    $_SESSION['keranjang'][$id_produk] = $qty_sekarang + 1;
    $_SESSION['keranjang_opsi'][$id_produk] = [
        'varian' => $varian !== '' ? $varian : 'Standard Original',
        'catatan' => $catatan,
    ];

    header('Location: keranjang.php');
    exit;
}

if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $id_produk = (int)($_GET['id'] ?? 0);
    unset($_SESSION['keranjang'][$id_produk], $_SESSION['keranjang_opsi'][$id_produk]);
    header('Location: keranjang.php');
    exit;
}

header('Location: keranjang.php');
exit;
