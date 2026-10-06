<?php
include_once 'includes/header.php';
include_once 'config/helpers.php';

$batas = 12;
$halaman = max(1, isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1);
$halaman_awal = ($halaman - 1) * $batas;

$search = trim($_GET['search'] ?? '');
$kategori_filter = trim($_GET['kategori'] ?? '');
$max_harga = isset($_GET['max_harga']) && $_GET['max_harga'] !== '' ? max(0, (int)$_GET['max_harga']) : '';

$where = ['1=1'];
if ($search !== '') {
    $escSearch = mysqli_real_escape_string($conn, $search);
    $where[] = "p.nama LIKE '%$escSearch%'";
}
if ($kategori_filter !== '') {
    $escKat = mysqli_real_escape_string($conn, $kategori_filter);
    $where[] = "p.kategori = '$escKat'";
}
if ($max_harga !== '' && $max_harga > 0) {
    $where[] = "p.harga <= $max_harga";
}
$where_sql = 'WHERE ' . implode(' AND ', $where);

$sql_jumlah = "SELECT COUNT(*) AS total FROM tb_produk p $where_sql";
$res_jumlah = mysqli_query($conn, $sql_jumlah);
$row_jumlah = $res_jumlah ? mysqli_fetch_assoc($res_jumlah) : ['total' => 0];
$total_data = (int)($row_jumlah['total'] ?? 0);
$total_halaman = max(1, (int)ceil($total_data / $batas));
if ($halaman > $total_halaman) {
    $halaman = $total_halaman;
    $halaman_awal = ($halaman - 1) * $batas;
}

$q = "SELECT p.*,
        (SELECT AVG(u.rating) FROM tb_ulasan u WHERE u.id_produk = p.id) AS avg_rating,
        (SELECT COUNT(*) FROM tb_ulasan u WHERE u.id_produk = p.id) AS review_count
      FROM tb_produk p
      $where_sql
      ORDER BY p.id DESC
      LIMIT $halaman_awal, $batas";
$res = mysqli_query($conn, $q);
$kategori_options = wg_get_categories($conn);
?>

<div class="container my-5">
    <div class="row g-4">
        <!-- Sidebar Filter System -->
        <div class="col-lg-3" data-aos="fade-right">
            <div class="card p-4 border-0 shadow-sm rounded-4 sticky-top bg-white" style="top: 90px; border: 1px solid rgba(39, 53, 38, 0.05) !important;">
                <h5 class="font-display fw-bold mb-4" style="color: #2b302a;"><i class="bi bi-filter-left me-1"></i> Filter Kuliner</h5>
                
                <form method="GET" action="">
                    <!-- Input Pencarian -->
                    <div class="mb-3">
                        <label class="form-label text-sm fw-semibold text-muted" style="font-size: 0.85rem;">Cari Produk</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-start-pill text-sm" value="<?= htmlspecialchars($search); ?>" placeholder="Nama menu..." style="font-size: 0.85rem;">
                            <button class="btn px-3 rounded-end-pill text-white" type="submit" style="background-color: #273526;"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    
                    <!-- Pilihan Kategori -->
                    <div class="mb-3">
                        <label class="form-label text-sm fw-semibold text-muted" style="font-size: 0.85rem;">Kategori Menu</label>
                        <select name="kategori" class="form-select rounded-3 text-sm" onchange="this.form.submit()" style="font-size: 0.85rem;">
                            <option value="">Semua Kategori</option>
                            <?php foreach($kategori_options as $kat): ?>
                            <option value="<?= htmlspecialchars($kat['nama_kategori']); ?>" <?= $kategori_filter === $kat['nama_kategori'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Batas Harga Maksimal -->
                    <div class="mb-4">
                        <label class="form-label text-sm fw-semibold text-muted" style="font-size: 0.85rem;">Maksimal Harga</label>
                        <input type="number" name="max_harga" class="form-control rounded-3 text-sm" placeholder="Contoh: 25000" value="<?= $max_harga; ?>" style="font-size: 0.85rem;">
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <button type="submit" class="btn btn-wargt-primary w-100 py-2 rounded-pill text-sm" style="font-size: 0.85rem; background-color: #273526; color: #fff;">Terapkan Filter</button>
                    <?php if($search !== '' || $kategori_filter !== '' || $max_harga !== ''): ?>
                        <a href="menu.php" class="btn btn-sm w-100 text-center mt-2 fw-semibold text-decoration-none small" style="color: #a67c52; font-size: 0.8rem;">Reset Filter</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Grid Items Area -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted small mb-0">Menampilkan <b><?= $total_data; ?></b> menu kuliner pilihan</p>
            </div>

            <div class="row g-4">
                <?php if(!$res || mysqli_num_rows($res) == 0): ?>
                    <div class="col-12 text-center py-5 bg-white rounded-4 border" style="border-color: rgba(0,0,0,0.05) !important;">
                        <i class="bi bi-inboxes text-muted fs-1 d-block mb-2"></i>
                        <h6 class="text-muted fw-medium">Menu kuliner tidak ditemukan. Silakan cari kata kunci lain.</h6>
                    </div>
                <?php endif; ?>

                <?php while($row = mysqli_fetch_assoc($res)): 
                    $rating = $row['avg_rating'] !== null ? round($row['avg_rating'], 1) : null;
                    $review_count = (int)($row['review_count'] ?? 0);
                    $nama_kat = !empty($row['kategori']) ? $row['kategori'] : 'Lainnya';
                    
                    $img = !empty($row['poto']) ? $row['poto'] : ($row['gambar'] ?? '');
                    $img_src = wg_product_image($img, 400);
                ?>
                <div class="col-md-4 col-sm-6" data-aos="zoom-in">
                    <div class="wg-menu-card-wrap">
                        <div class="wg-menu-card">

                            <!-- SISI DEPAN -->
                            <div class="wg-menu-face wg-menu-front">
                                <?php if(function_exists('wg_kategori_steam') && wg_kategori_steam($nama_kat)): ?>
                                <div class="wg-steam"><span></span><span></span><span></span></div>
                                <?php endif; ?>

                                <div class="wg-price-tag"><?= rupiah($row['harga']) ?></div>

                                <div class="wg-plate">
                                    <div class="wg-plate-img">
                                        <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400&auto=format&fit=crop'">
                                    </div>
                                    <span class="wg-kat-chip"><i class="bi <?= function_exists('wg_kategori_icon') ? wg_kategori_icon($nama_kat) : 'bi-tag'; ?>"></i> <?= htmlspecialchars($nama_kat) ?></span>
                                </div>

                                <h6 class="wg-menu-name"><?= htmlspecialchars($row['nama']) ?></h6>

                                <div class="wg-menu-stars">
                                    <?php if($rating !== null): 
                                        for($i=1;$i<=5;$i++):
                                            echo '<i class="bi '.($i <= round($rating) ? 'bi-star-fill' : 'bi-star-fill muted-star').'"></i>';
                                        endfor;
                                    ?>
                                    <span class="text-muted ms-1"><?= $rating ?> (<?= $review_count ?>)</span>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ada ulasan</span>
                                    <?php endif; ?>
                                </div>
                                <div class="wg-menu-stock">Stok tersisa <?= (int)$row['stok'] ?> porsi</div>

                                <div class="wg-flip-hint"><i class="bi bi-arrow-repeat"></i> Sentuh untuk lihat detail</div>
                            </div>

                            <!-- SISI BELAKANG -->
                            <div class="wg-menu-face wg-menu-back">
                                <div class="wg-menu-back-top">
                                    <span class="wg-kat-chip-static"><i class="bi <?= function_exists('wg_kategori_icon') ? wg_kategori_icon($nama_kat) : 'bi-tag'; ?>"></i> <?= htmlspecialchars($nama_kat) ?></span>
                                    <h6 class="wg-menu-name"><?= htmlspecialchars($row['nama']) ?></h6>
                                    <p class="wg-menu-desc"><?= htmlspecialchars($row['deskripsi']) ?></p>
                                </div>
                                <div class="wg-menu-back-foot">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="wg-menu-back-price"><?= rupiah($row['harga']) ?></span>
                                        <a href="detail.php?id=<?= $row['id']; ?>" class="wg-btn-flip-detail">Detail <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                    <form action="proses_keranjang.php" method="POST">
                                        <input type="hidden" name="id_produk" value="<?= $row['id']; ?>">
                                        <button type="submit" name="tambah" class="btn btn-wargt-primary w-100 py-2 rounded-pill d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-bag-plus"></i> Beli Menu
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- PAGINATION -->
            <?php if($total_halaman > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link rounded-start-pill px-3" href="?halaman=<?= $halaman - 1; ?>&search=<?= urlencode($search); ?>&kategori=<?= urlencode($kategori_filter); ?>&max_harga=<?= $max_harga; ?>" style="color: #273526;">Sebelumnya</a>
                    </li>

                    <?php for($x = 1; $x <= $total_halaman; $x++): ?>
                        <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                            <a class="page-link" href="?halaman=<?= $x; ?>&search=<?= urlencode($search); ?>&kategori=<?= urlencode($kategori_filter); ?>&max_harga=<?= $max_harga; ?>" style="<?= ($halaman == $x) ? 'background-color: #273526; border-color: #273526; color: #fff;' : 'color: #273526;'; ?>"><?= $x; ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                        <a class="page-link rounded-end-pill px-3" href="?halaman=<?= $halaman + 1; ?>&search=<?= urlencode($search); ?>&kategori=<?= urlencode($kategori_filter); ?>&max_harga=<?= $max_harga; ?>" style="color: #273526;">Selanjutnya</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>