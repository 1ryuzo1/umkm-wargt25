<?php
include_once 'includes/header.php';
include_once 'config/helpers.php';

if (!isset($_SESSION['role'])) {
    wg_flash_redirect('auth/login.php', 'Silakan login untuk membuka keranjang.');
}

$cart = isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
$cart_options = isset($_SESSION['keranjang_opsi']) && is_array($_SESSION['keranjang_opsi']) ? $_SESSION['keranjang_opsi'] : [];

$cart_items = [];
$grand_total = 0;
foreach ($cart as $id_prod => $qty) {
    $id_prod = (int)$id_prod;
    $qty = (int)$qty;
    if ($id_prod <= 0 || $qty <= 0) continue;

    $res = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id = $id_prod LIMIT 1");
    $p = $res ? mysqli_fetch_assoc($res) : null;
    if (!$p) continue;

    $qty = min($qty, max(0, (int)$p['stok']));
    if ($qty <= 0) continue;

    $subtotal = (int)$p['harga'] * $qty;
    $grand_total += $subtotal;
    $cart_items[] = [
        'product' => $p,
        'qty' => $qty,
        'subtotal' => $subtotal,
        'options' => $cart_options[$id_prod] ?? [],
    ];
}
?>

<div class="container my-5">
    <h4 class="font-display fw-bold mb-4" style="color: #2b302a;" data-aos="fade-up"><i class="bi bi-bag-check me-2"></i>Keranjang Belanja Anda</h4>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <?php if (!$cart_items): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted m-0">Keranjang Anda masih kosong.</p>
                        <a href="menu.php" class="btn btn-wargt-primary mt-3 btn-sm">Cari Menu Kuliner</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item):
                                    $p = $item['product'];
                                    $qty = $item['qty'];
                                    $opsi = $item['options'];
                                    $img = !empty($p['poto']) ? $p['poto'] : ($p['gambar'] ?? '');
                                    $img_src = wg_product_image($img, 100);
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= htmlspecialchars($img_src) ?>" class="rounded-3 object-fit-cover" width="60" height="60" alt="<?= htmlspecialchars($p['nama']) ?>">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($p['nama']) ?></h6>
                                                <small class="badge bg-light text-muted border"><?= htmlspecialchars($p['kategori'] ?: 'Lainnya') ?></small>
                                                <?php if (!empty($opsi['varian'])): ?><div class="small text-muted mt-1">Varian: <?= htmlspecialchars($opsi['varian']) ?></div><?php endif; ?>
                                                <?php if (!empty($opsi['catatan'])): ?><div class="small text-muted">Catatan: <?= htmlspecialchars($opsi['catatan']) ?></div><?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-medium"><?= rupiah($p['harga']) ?></td>
                                    <td class="fw-bold"><?= $qty ?>x</td>
                                    <td class="fw-bold text-dark"><?= rupiah($item['subtotal']) ?></td>
                                    <td class="text-center">
                                        <a href="proses_keranjang.php?aksi=hapus&id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-light border text-danger rounded-pill px-2.5" title="Hapus dari keranjang">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($cart_items): ?>
        <div class="col-lg-4">
            <form action="proses_checkout.php" method="POST">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-3">
                    <h6 class="fw-bold mb-3 text-dark">Opsi Operasional / Pengiriman</h6>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Metode Pengiriman</label>
                        <select class="form-select rounded-3 text-sm" name="opsi_pengiriman" required style="font-size:0.85rem;">
                            <option value="Ambil di Resto (Dine In / Take Away)">Ambil di Resto (Dine In / Take Away)</option>
                            <option value="Kurir Jauh (Jarak Jauh / Online)">Kirim Kurir Jauh (Jarak Jauh / Online)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-muted">Metode Pembayaran</label>
                        <select class="form-select rounded-3 text-sm" name="opsi_pembayaran" required style="font-size:0.85rem;">
                            <option value="Transfer Bank (VA Manual)">Transfer Bank (VA Manual)</option>
                            <option value="Bayar Langsung di Kasir (Cash / COD)">Bayar Langsung di Kasir (Cash / COD)</option>
                        </select>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold mb-3 text-dark">Ringkasan Biaya</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Total Biaya Item</span>
                        <span class="fw-medium"><?= rupiah($grand_total) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-4">
                        <span class="fw-bold text-dark">Total Biaya</span>
                        <h4 class="fw-extrabold m-0" style="color: #a67c52; font-weight: 800;"><?= rupiah($grand_total) ?></h4>
                    </div>
                    <button type="submit" name="checkout" class="btn btn-wargt-primary w-100 py-2.5 rounded-pill fw-bold text-sm">
                        <i class="bi bi-wallet2 me-2"></i> Beli Semua Pesanan
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
