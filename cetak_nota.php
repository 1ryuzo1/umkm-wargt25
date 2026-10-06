<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
include_once 'config/database.php';
include_once 'config/helpers.php';

if (!isset($_SESSION['role'])) {
    die("Akses ditolak. Silakan login.");
}

$id_transaksi = intval($_GET['id']);

// Ambil data transaksi beserta informasi user
$query_trx = mysqli_query($conn, "SELECT t.*, u.nama, u.hp, u.alamat FROM tb_transaksi t JOIN tb_user u ON t.id_pelanggan = u.id WHERE t.id_transaksi = $id_transaksi LIMIT 1");
$trx = $query_trx ? mysqli_fetch_assoc($query_trx) : null;

if (!$trx) {
    die("Nota transaksi tidak ditemukan.");
}
if (($_SESSION['role'] ?? '') !== 'admin' && (int)$trx['id_pelanggan'] !== (int)($_SESSION['id_user'] ?? 0)) {
    http_response_code(403);
    die("Anda tidak memiliki akses ke nota ini.");
}
$trx_cols = wg_detect_columns($conn, 'tb_transaksi');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #TRX-<?= $trx['id_transaksi'] ?> - Wargt25</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfaf6; padding: 30px 10px; display: flex; flex-direction: column; align-items: center; color: #2b302a; }
        .btn-group-print { margin-bottom: 20px; display: flex; gap: 10px; }
        .btn-print { background: #273526; color: #fff; border: none; padding: 10px 24px; font-weight: 600; border-radius: 50px; cursor: pointer; font-size: 0.85rem; }
        .btn-close-tab { background: #fff; color: #273526; border: 1px solid #273526; }
        .invoice-card { background: #fff; width: 100%; max-width: 420px; padding: 30px; border-radius: 16px; border: 1px solid rgba(39,53,38,0.08); box-shadow: 0 4px 12px rgba(0,0,0,0.01); }
        .invoice-header { text-align: center; border-bottom: 1px dashed #a67c52; padding-bottom: 15px; margin-bottom: 15px; }
        .brand-logo { font-size: 1.6rem; font-weight: 800; color: #273526; }
        .brand-logo span { color: #a67c52; }
        .meta-text { display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 6px; }
        .item-table { width: 100%; margin: 15px 0; border-collapse: collapse; }
        .item-table th { border-bottom: 1px solid #273526; padding-bottom: 6px; text-align: left; font-size: 0.8rem; color: #5c5e5a; }
        .item-table td { padding: 10px 0; font-size: 0.85rem; border-bottom: 1px solid rgba(0,0,0,0.03); }
        .price-mono { text-align: right; font-family: 'Space Mono', monospace; font-weight: 600; }
        .total-row { font-weight: 800; font-size: 1.05rem; color: #273526; }
        @media print {
            body { background: #fff; padding: 0; }
            .btn-group-print { display: none !important; }
            .invoice-card { border: none; max-width: 100%; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>

    <div class="btn-group-print">
        <button onclick="window.print()" class="btn-print">Cetak / Unduh PDF</button>
        <button onclick="window.close()" class="btn-print btn-close-tab">Tutup</button>
    </div>

    <div class="invoice-card">
        <div class="invoice-header">
            <div class="brand-logo">Wargt<span>25</span></div>
            <small style="color: #5c5e5a; font-weight: 500;">Premium Culinary & Cafe Service</small>
        </div>
        
        <div class="meta-text"><span>Nomor Nota:</span><strong>#TRX-<?= $trx['id_transaksi'] ?></strong></div>
        <div class="meta-text"><span>Tanggal Transaksi:</span><strong><?= date('d M Y', strtotime($trx['tanggal'])) ?></strong></div>
        <div class="meta-text"><span>Nama Pembeli:</span><strong><?= htmlspecialchars($trx['nama']) ?> (<span><?= htmlspecialchars($trx['hp']) ?></span>)</strong></div>
        <div class="meta-text" style="align-items: flex-start;"><span>Alamat Kirim:</span><strong style="text-align: right; max-width: 200px; font-weight: 500; font-size: 0.75rem;"><?= htmlspecialchars($trx['alamat']) ?></strong></div>

        <table class="item-table">
            <thead>
                <tr>
                    <th>Menu Kuliner</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil daftar detail item pesanan milik id_transaksi terkait
                $detail_cols = wg_detect_columns($conn, 'tb_detail');
                $item_extra = '';
                if (in_array('varian', $detail_cols, true)) $item_extra .= ', d.varian';
                if (in_array('catatan', $detail_cols, true)) $item_extra .= ', d.catatan';
                $query_items = mysqli_query($conn, "SELECT d.jumlah, p.nama, p.harga$item_extra FROM tb_detail d JOIN tb_produk p ON d.id_produk = p.id WHERE d.id_transaksi = $id_transaksi");
                while ($query_items && ($item = mysqli_fetch_assoc($query_items))):
                    $sub = $item['harga'] * $item['jumlah'];
                ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($item['nama']) ?><br>
                        <small class="text-muted"><?= (int)$item['jumlah'] ?> x Rp <?= number_format($item['harga'],0,',','.') ?></small>
                        <?php if (!empty($item['varian'])): ?><br><small>Varian: <?= htmlspecialchars($item['varian']) ?></small><?php endif; ?>
                        <?php if (!empty($item['catatan'])): ?><br><small>Catatan: <?= htmlspecialchars($item['catatan']) ?></small><?php endif; ?>
                    </td>
                    <td class="price-mono">Rp <?= number_format($sub,0,',','.') ?></td>
                </tr>
                <?php endwhile; ?>
                
                <tr class="total-row">
                    <td style="padding-top: 15px; border: none;">Total Bayar</td>
                    <td class="price-mono" style="padding-top: 15px; border: none; color: #a67c52;">Rp <?= number_format($trx['total_harga'],0,',','.') ?></td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: center; font-size: 0.7rem; color: #5c5e5a; margin-top: 25px; border-top: 1px dashed #ccc; padding-top: 15px;">
            Terima kasih telah menikmati hidangan Wargt25!<br>
            Pesanan Anda segera diproses oleh dapur kami.
        </div>
    </div>

</body>
</html>