<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'config/database.php';
include_once 'config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['checkout'])) {
    header('Location: keranjang.php');
    exit;
}

if (!isset($_SESSION['id_user'], $_SESSION['role'])) {
    wg_flash_redirect('auth/login.php', 'Silakan login sebelum checkout.');
}

if (empty($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
    wg_flash_redirect('keranjang.php', 'Keranjang masih kosong.');
}

$id_pelanggan = (int)$_SESSION['id_user'];
$tanggal = date('Y-m-d');
$opsi_pengiriman = trim($_POST['opsi_pengiriman'] ?? 'Ambil di Resto (Dine In / Take Away)');
$opsi_pembayaran = trim($_POST['opsi_pembayaran'] ?? 'Bayar Langsung di Kasir (Cash / COD)');

$kolom_ada = wg_detect_columns($conn, 'tb_transaksi');
$detail_kolom_ada = wg_detect_columns($conn, 'tb_detail');

// Hitung ulang total dari database agar harga tidak bisa dimanipulasi lewat hidden input.
$items = [];
$total_harga = 0;
foreach ($_SESSION['keranjang'] as $id_produk => $qty) {
    $id_produk = (int)$id_produk;
    $qty = max(0, (int)$qty);
    if ($id_produk <= 0 || $qty <= 0) continue;

    $result = mysqli_query($conn, "SELECT id, nama, harga, stok FROM tb_produk WHERE id = $id_produk LIMIT 1");
    $produk = $result ? mysqli_fetch_assoc($result) : null;
    if (!$produk) {
        wg_flash_redirect('keranjang.php', 'Ada produk di keranjang yang sudah tidak tersedia. Silakan cek kembali keranjang.');
    }
    if ($qty > (int)$produk['stok']) {
        wg_flash_redirect('keranjang.php', 'Stok untuk "' . $produk['nama'] . '" tidak mencukupi.');
    }

    $subtotal = (int)$produk['harga'] * $qty;
    $total_harga += $subtotal;
    $opsi = $_SESSION['keranjang_opsi'][$id_produk] ?? [];
    $items[] = [
        'id_produk' => $id_produk,
        'qty' => $qty,
        'varian' => trim($opsi['varian'] ?? ''),
        'catatan' => trim($opsi['catatan'] ?? ''),
    ];
}

if (!$items) {
    wg_flash_redirect('keranjang.php', 'Keranjang tidak memiliki item yang valid.');
}

mysqli_begin_transaction($conn);
try {
    $kolom_insert = ['id_pelanggan', 'tanggal', 'total_harga'];
    $nilai_insert = [$id_pelanggan, "'$tanggal'", $total_harga];

    if (in_array('opsi_pengiriman', $kolom_ada, true)) {
        $kolom_insert[] = 'opsi_pengiriman';
        $nilai_insert[] = "'" . mysqli_real_escape_string($conn, $opsi_pengiriman) . "'";
    }
    if (in_array('opsi_pembayaran', $kolom_ada, true)) {
        $kolom_insert[] = 'opsi_pembayaran';
        $nilai_insert[] = "'" . mysqli_real_escape_string($conn, $opsi_pembayaran) . "'";
    }
    if (in_array('status_pesanan', $kolom_ada, true)) {
        $kolom_insert[] = 'status_pesanan';
        $nilai_insert[] = "'Menunggu Pembayaran'";
    }

    $query_trx = "INSERT INTO tb_transaksi (" . implode(', ', $kolom_insert) . ") VALUES (" . implode(', ', $nilai_insert) . ")";
    if (!mysqli_query($conn, $query_trx)) {
        throw new Exception(mysqli_error($conn));
    }
    $id_transaksi = (int)mysqli_insert_id($conn);

    foreach ($items as $item) {
        $id_produk = $item['id_produk'];
        $qty = $item['qty'];
        $detail_cols = ['id_transaksi', 'id_produk', 'jumlah'];
        $detail_vals = [$id_transaksi, $id_produk, $qty];

        if (in_array('varian', $detail_kolom_ada, true)) {
            $detail_cols[] = 'varian';
            $detail_vals[] = "'" . mysqli_real_escape_string($conn, $item['varian']) . "'";
        }
        if (in_array('catatan', $detail_kolom_ada, true)) {
            $detail_cols[] = 'catatan';
            $detail_vals[] = "'" . mysqli_real_escape_string($conn, $item['catatan']) . "'";
        }

        $query_detail = "INSERT INTO tb_detail (" . implode(', ', $detail_cols) . ") VALUES (" . implode(', ', $detail_vals) . ")";
        if (!mysqli_query($conn, $query_detail)) {
            throw new Exception(mysqli_error($conn));
        }

        // Guard stok di level query agar tidak pernah menjadi negatif.
        $stock_ok = mysqli_query($conn, "UPDATE tb_produk SET stok = stok - $qty WHERE id = $id_produk AND stok >= $qty");
        if (!$stock_ok || mysqli_affected_rows($conn) !== 1) {
            throw new Exception('Stok produk berubah atau tidak mencukupi.');
        }
    }

    mysqli_commit($conn);
    unset($_SESSION['keranjang'], $_SESSION['keranjang_opsi']);
    $nota_url = 'cetak_nota.php?id=' . $id_transaksi;
    wg_flash_redirect($nota_url, 'Transaksi berhasil dibuat.');
} catch (Throwable $e) {
    mysqli_rollback($conn);
    wg_flash_redirect('keranjang.php', 'Checkout gagal diproses. Silakan cek stok dan coba lagi.');
}
