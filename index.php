<?php 
include_once 'includes/header.php';
include_once 'config/helpers.php'; 

// Filter kategori berbasis nama (sesuai schema tb_produk.kategori yang dipakai database bawaan)
$kategori_filter = isset($_GET['kat']) ? trim($_GET['kat']) : '';
$kategori_filter_sql = mysqli_real_escape_string($conn, $kategori_filter);

$query_str = "SELECT p.*,
                (SELECT AVG(u.rating) FROM tb_ulasan u WHERE u.id_produk = p.id) AS avg_rating,
                (SELECT COUNT(*) FROM tb_ulasan u WHERE u.id_produk = p.id) AS review_count
              FROM tb_produk p";
if ($kategori_filter !== '') {
    $query_str .= " WHERE p.kategori = '$kategori_filter_sql'";
}
$query_str .= " ORDER BY p.id DESC LIMIT 8";
$ambil_produk = mysqli_query($conn, $query_str);

// Statistik ringkas untuk beranda
$menu_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM tb_produk"));
$total_menu = (int)($menu_row['n'] ?? 0);
$kategori_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT NULLIF(TRIM(kategori), '')) AS n FROM tb_produk"));
$total_kategori = (int)($kategori_row['n'] ?? 0);
$rata_rating_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) AS r FROM tb_ulasan"));
$rata_rating = $rata_rating_row && $rata_rating_row['r'] !== null ? round($rata_rating_row['r'], 1) : 0.0;
$pelanggan_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM tb_user WHERE role='pelanggan'"));
$total_pelanggan = (int)($pelanggan_row['n'] ?? 0);
?>

<!-- HERO SECTION: split text + interactive 3D dinnerware viewer -->
<div class="container my-4">
    <div class="wg-hero position-relative overflow-hidden p-5 rounded-5 shadow-sm" style="background: linear-gradient(rgba(39, 53, 38, 0.88), rgba(27, 36, 25, 0.92)), url('https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=1469&auto=format&fit=crop') center/cover; min-height: 500px; display: flex; align-items: center;">
        <div class="wg-hero-split w-100">
            <div class="wg-hero-text text-white">
                <span class="badge badge-shimmer px-3 py-2 mb-3 rounded-pill" style="color:#273526; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase;">Cita Rasa Nusantara Modern</span>
                <h1 class="font-display display-4 fw-bold tracking-tight mb-3" style="line-height: 1.15;">Kelezatan Bakaran &amp; Kesegaran Jus Pilihan</h1>
                <p class="lead text-white-50 fs-6 mb-4">Nikmati perpaduan hidangan berat berkualitas tinggi, nasi-nasian hangat, snack renyah, gorengan gurih, serta aneka minuman hangat maupun es yang diracik khusus untuk kepuasan harimu di Wargt25.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="menu.php" class="btn btn-wargt-primary" style="background-color: #a67c52;">Jelajahi Menu</a>
                    <a href="#kategori" class="btn btn-outline-light px-4 py-2.5 rounded-pill fw-semibold text-sm">Lihat Menu Terlaris</a>
                </div>
                <div class="wg-trust">
                    <span><i class="bi bi-patch-check-fill"></i> Bahan Segar Pilihan</span>
                    <span><i class="bi bi-fire"></i> Resep Autentik</span>
                    <span><i class="bi bi-clock-history"></i> Buka Setiap Hari</span>
                </div>
            </div>

            <!-- Panel 3D interaktif -->
            <div class="wg-3d-panel" id="wg3d-panel">
                <div class="wg-3d-loading" id="wg3d-loading">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm text-light mb-2" role="status"></div>
                        <div>Memuat model 3D…</div>
                    </div>
                </div>
                <div class="wg-3d-hint"><i class="bi bi-arrows-move"></i> Seret untuk memutar</div>
            </div>
        </div>
    </div>
</div>

<!-- STATISTIK NYATA DARI DATABASE -->
<div class="container">
    <div class="wg-stats" data-aos="fade-up">
        <div class="wg-stat-card">
            <div class="wg-stat-icon"><i class="bi bi-egg-fried"></i></div>
            <div class="wg-stat-num" data-count="<?= (int)$total_menu ?>">0</div>
            <div class="wg-stat-label">Menu Kuliner</div>
        </div>
        <div class="wg-stat-card">
            <div class="wg-stat-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div class="wg-stat-num" data-count="<?= (int)$total_kategori ?>">0</div>
            <div class="wg-stat-label">Kategori Rasa</div>
        </div>
        <div class="wg-stat-card">
            <div class="wg-stat-icon"><i class="bi bi-star-fill"></i></div>
            <div class="wg-stat-num" data-count="<?= (float)$rata_rating ?>" data-decimals="1">0</div>
            <div class="wg-stat-label">Rata-rata Rating</div>
        </div>
        <div class="wg-stat-card">
            <div class="wg-stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="wg-stat-num" data-count="<?= (int)$total_pelanggan ?>">0</div>
            <div class="wg-stat-label">Pelanggan Terdaftar</div>
        </div>
    </div>
</div>

<!-- MENU TERATAS / TERLARIS -->
<div class="container mt-5" id="kategori">
    <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
        <div>
            <h4 class="font-display fw-bold m-0 text-dark">Menu Teratas &amp; Terlaris</h4>
            <p class="text-muted small m-0">Pilihan menu favorit yang paling sering dipesan pelanggan — sentuh kartu untuk membaliknya</p>
        </div>
        <?php if($kategori_filter != ''): ?>
            <a href="index.php" class="text-decoration-none small fw-bold" style="color: #a67c52;"><i class="bi bi-x-circle-fill"></i> Bersihkan Filter</a>
        <?php endif; ?>
    </div>
    
    <!-- Badges Filter Kategori -->
    <div class="d-flex gap-2 overflow-x-auto pb-2" style="white-space: nowrap; scrollbar-width: none;">
        <?php
        $kategori_list = ['Makanan Berat', 'Bakar-bakaran', 'Nasi Nasian', 'Goreng-gorengan', 'Snack', 'Minuman Hangat', 'Minuman Biasa', 'Minuman Es', 'Aneka Jus'];
        foreach($kategori_list as $kat):
            $active_class = ($kategori_filter == $kat) ? 'background-color: #273526; color: #fff;' : 'background-color: #fff; color: #273526; border: 1px solid rgba(39, 53, 38, 0.1);';
        ?>
            <a href="index.php?kat=<?= urlencode($kat) ?>#kategori" class="btn wg-pill px-4 py-2 rounded-pill fw-semibold text-sm shadow-sm" style="<?= $active_class ?> font-size: 0.85rem;">
                <i class="bi <?= wg_kategori_icon($kat) ?> me-1"></i><?= $kat ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- ETALASE PRODUK -->
<div class="container mb-5 mt-4">
    <div class="row g-4">
        <?php 
        if(!$ambil_produk || mysqli_num_rows($ambil_produk) == 0): 
        ?>
            <div class="col-12 text-center py-5 bg-white rounded-4 border">
                <i class="bi bi-inboxes text-muted fs-1 d-block mb-2"></i>
                <p class="text-muted m-0">Menu pada kategori <b>"<?= htmlspecialchars($kategori_filter) ?>"</b> belum tersedia.</p>
            </div>
        <?php 
        else:
            while($p = mysqli_fetch_assoc($ambil_produk)):
                $rating = $p['avg_rating'] !== null ? round($p['avg_rating'], 1) : null;
                $review_count = (int)($p['review_count'] ?? 0);
                $nama_kat = !empty($p['kategori']) ? $p['kategori'] : 'Lainnya';

                // PENANGANAN GAMBAR: dukung kolom poto/gambar, link URL, atau file lokal di assets/img/
                $img_p = !empty($p['poto']) ? $p['poto'] : ($p['gambar'] ?? '');
                $img_src_p = !empty($img_p)
                    ? (filter_var($img_p, FILTER_VALIDATE_URL) ? $img_p : 'assets/img/' . ltrim($img_p, '/'))
                    : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600&auto=format&fit=crop';
        ?>
            <div class="col-xl-3 col-lg-4 col-md-6" data-aos="zoom-in">
                <div class="wg-menu-card-wrap">
                    <div class="wg-menu-card">

                        <!-- SISI DEPAN -->
                        <div class="wg-menu-face wg-menu-front">
                            <?php if(wg_kategori_steam($nama_kat)): ?>
                            <div class="wg-steam"><span></span><span></span><span></span></div>
                            <?php endif; ?>

                            <div class="wg-price-tag"><?= rupiah($p['harga']) ?></div>

                            <div class="wg-plate">
                                <div class="wg-plate-img">
                                    <img src="<?= htmlspecialchars($img_src_p) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600&auto=format&fit=crop'">
                                </div>
                                <span class="wg-kat-chip"><i class="bi <?= wg_kategori_icon($nama_kat) ?>"></i> <?= htmlspecialchars($nama_kat) ?></span>
                            </div>

                            <h6 class="wg-menu-name"><?= htmlspecialchars($p['nama']) ?></h6>

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
                            <div class="wg-menu-stock">Stok tersisa <?= (int)$p['stok'] ?> porsi</div>

                            <div class="wg-flip-hint"><i class="bi bi-arrow-repeat"></i> Sentuh untuk lihat detail</div>
                        </div>

                        <!-- SISI BELAKANG -->
                        <div class="wg-menu-face wg-menu-back">
                            <div class="wg-menu-back-top">
                                <span class="wg-kat-chip-static"><i class="bi <?= wg_kategori_icon($nama_kat) ?>"></i> <?= htmlspecialchars($nama_kat) ?></span>
                                <h6 class="wg-menu-name"><?= htmlspecialchars($p['nama']) ?></h6>
                                <p class="wg-menu-desc"><?= htmlspecialchars($p['deskripsi']) ?></p>
                            </div>
                            <div class="wg-menu-back-foot">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="wg-menu-back-price"><?= rupiah($p['harga']) ?></span>
                                    <a href="detail.php?id=<?= $p['id']; ?>" class="wg-btn-flip-detail">Detail <i class="bi bi-arrow-right"></i></a>
                                </div>
                                <form action="proses_keranjang.php" method="POST">
                                    <input type="hidden" name="id_produk" value="<?= $p['id'] ?>">
                                    <button type="submit" name="tambah" class="btn btn-wargt-primary w-100 py-2 rounded-pill d-flex align-items-center justify-content-center gap-2 text-sm">
                                        <i class="bi bi-bag-plus"></i> Masukkan Keranjang
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php 
            endwhile; 
        endif; 
        ?>
    </div>

    <!-- Tombol Menuju Halaman Menu Lengkap -->
    <div class="text-center mt-5">
        <a href="menu.php" class="btn btn-wargt-outline px-5 py-2.5 rounded-pill fw-semibold shadow-sm" style="border-color: #273526; color: #273526;">
            Lihat Semua Menu Kuliner Lainnya <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>

<!-- THREE.JS -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script src="assets/js/scene3d.js"></script>

<?php include_once 'includes/footer.php'; ?>