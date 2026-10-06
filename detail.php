<?php 
include_once 'includes/header.php';
include_once 'config/helpers.php'; 

// Ambil ID Produk dari URL
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query ambil data produk berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM tb_produk WHERE id = $id_produk");
$p = mysqli_fetch_assoc($query);

// Jika produk tidak ditemukan
if(!$p) {
    echo '<div class="container my-5 text-center"><h3>Menu tidak ditemukan!</h3><a href="index.php" class="btn btn-wargt-primary mt-3">Kembali ke Beranda</a></div>';
    include_once 'includes/footer.php';
    exit;
}

// Cek Apakah User yang Sedang Login Sudah Pernah Membeli Produk Ini
$sudah_beli = false;
if (isset($_SESSION['id_user'])) {
    $id_user_cek = intval($_SESSION['id_user']);
    $cek_beli = mysqli_query($conn, "SELECT d.id_produk FROM tb_detail d JOIN tb_transaksi t ON d.id_transaksi = t.id_transaksi WHERE t.id_pelanggan = $id_user_cek AND d.id_produk = $id_produk LIMIT 1");
    $sudah_beli = $cek_beli && mysqli_num_rows($cek_beli) > 0;
}

// PENANGANAN GAMBAR: dukung kolom poto/gambar, link URL, atau file lokal di assets/img/
$img_p = !empty($p['poto']) ? $p['poto'] : ($p['gambar'] ?? '');
$img_src_p = wg_product_image($img_p, 600);
?>

<div class="container my-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color: #a67c52;">Beranda</a></li>
            <li class="breadcrumb-item"><a href="menu.php" class="text-decoration-none" style="color: #a67c52;">Menu</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($p['nama']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Gambar Produk -->
        <div class="col-lg-5" data-aos="fade-right">
            <div class="card tilt-card img-zoom-wrap border-0 shadow-sm rounded-4 overflow-hidden bg-light" style="height: 380px;">
                <img src="<?= htmlspecialchars($img_src_p); ?>" class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($p['nama']); ?>" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=600&auto=format&fit=crop'">
            </div>
        </div>

        <!-- Detail & Form Beli / Varian -->
        <div class="col-lg-7" data-aos="fade-left">
            <span class="badge rounded-pill px-3 py-1.5 mb-2" style="background-color: rgba(39,53,38,0.85); font-size: 0.75rem;">
                <?= htmlspecialchars($p['kategori']); ?>
            </span>
            <h2 class="font-display fw-bold text-dark mb-2"><?= htmlspecialchars($p['nama']); ?></h2>
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="wg-price fs-4 fw-extrabold" style="color: #a67c52;"><?= rupiah($p['harga']); ?></span>
                <span class="text-muted small">Stok Tersedia: <b><?= $p['stok']; ?> porsi</b></span>
            </div>
            
            <p class="text-muted mb-4" style="line-height: 1.6;"><?= htmlspecialchars($p['deskripsi']); ?></p>

            <!-- FORM KERANJANG DENGAN PILIHAN VARIAN -->
            <form action="proses_keranjang.php" method="POST" class="card p-4 border-0 shadow-sm rounded-4 bg-light mb-4" style="border: 1px solid rgba(39,53,38,0.05) !important;">
                <input type="hidden" name="id_produk" value="<?= $p['id']; ?>">
                
                <!-- Pilihan Varian Berdasarkan Kategori / Produk -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-sm" style="font-size: 0.85rem;">Pilih Varian / Level Rasa:</label>
                    <select name="varian" class="form-select rounded-3 text-sm" required style="font-size: 0.85rem;">
                        <?php if(strpos(strtolower($p['kategori']), 'minuman') !== false || strpos(strtolower($p['kategori']), 'jus') !== false): ?>
                            <option value="Normal Ice / Manis Sedang">Normal Ice / Manis Sedang</option>
                            <option value="Less Ice / Less Sugar">Less Ice / Less Sugar (Kurang Es/Gula)</option>
                            <option value="No Ice / Tanpa Es">No Ice / Tanpa Es</option>
                        <?php elseif(strpos(strtolower($p['kategori']), 'bakar') !== false): ?>
                            <option value="Sedang (Rekomendasi)">Level Pedas Sedang (Rekomendasi)</option>
                            <option value="Pedas Manis">Level Pedas Manis Gurih</option>
                            <option value="Super Pedas Mercon">Level Super Pedas Mercon 🔥</option>
                        <?php else: ?>
                            <option value="Standard Original">Porsi Standard Original</option>
                            <option value="Porsi Jumbo">Porsi Jumbo</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-sm" style="font-size: 0.85rem;">Catatan Tambahan (Opsional):</label>
                    <input type="text" name="catatan" class="form-control rounded-3 text-sm" placeholder="Contoh: Jangan pakai bawang, kuah dipisah..." style="font-size: 0.85rem;">
                </div>

                <button type="submit" name="tambah" class="btn text-white w-100 py-2.5 rounded-pill fw-semibold shadow-sm" style="background-color: #273526;">
                    <i class="bi bi-bag-plus me-1"></i> Masukkan Keranjang
                </button>
            </form>
        </div>
    </div>

    <!-- BAGIAN RATING & ULASAN PELANGGAN -->
    <div class="row mt-5 pt-4 border-top">
        <div class="col-lg-8">
            <h4 class="font-display fw-bold mb-4">Ulasan &amp; Penilaian Pelanggan</h4>
            
            <?php 
            // Ambil data ulasan dari database
            $res_ulasan = mysqli_query($conn, "SELECT * FROM tb_ulasan WHERE id_produk = $id_produk ORDER BY id_ulasan DESC");
            if(!$res_ulasan || mysqli_num_rows($res_ulasan) == 0):
            ?>
                <p class="text-muted italic small mb-4">Belum ada ulasan untuk produk ini.</p>
            <?php else: 
                while($u = mysqli_fetch_assoc($res_ulasan)):
            ?>
                <div class="card wg-review-card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white" data-aos="fade-up">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold m-0 text-dark"><?= htmlspecialchars($u['nama_user']); ?></h6>
                        <span class="text-warning small wg-stars">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $u['rating'] ? '-fill' : ''; ?>"></i>
                            <?php endfor; ?>
                        </span>
                    </div>
                    <p class="text-muted small m-0"><?= htmlspecialchars($u['komentar']); ?></p>
                    <small class="text-muted" style="font-size: 0.7rem;"><?= $u['tanggal']; ?></small>
                </div>
            <?php endwhile; endif; ?>

            <!-- FORM KIRIM RATING (DENGAN VALIDASI SUDAH BELI) -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mt-4 bg-white">
                <h5 class="fw-bold mb-3" style="font-size: 1rem;">Tulis Ulasan Produk Ini</h5>
                
                <?php if($sudah_beli): // Jika terdeteksi sudah pernah membeli ?>
                    <form action="proses_ulasan.php" method="POST">
                        <input type="hidden" name="id_produk" value="<?= $id_produk; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label text-sm fw-semibold d-block">Beri Rating (Bintang):</label>
                            <div class="wg-star-picker" data-target="#wg-rating-input">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <input type="hidden" id="wg-rating-input" name="rating" value="5">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-sm fw-semibold">Komentar / Pengalaman Kamu:</label>
                            <textarea name="komentar" rows="3" class="form-control rounded-3 text-sm" placeholder="Bagaimana rasa dan kualitas menu ini?" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-wargt-primary btn-sm px-4 rounded-pill" style="background-color: #a67c52;">Kirim Ulasan</button>
                    </form>
                <?php else: // Jika belum pernah membeli produk ?>
                    <div class="alert alert-warning py-2 text-sm m-0" role="alert">
                        <i class="bi bi-lock-fill me-1"></i> Maaf, kamu <b>belum bisa memberikan ulasan</b> karena belum pernah membeli atau memesan produk ini. Selesaikan pesanan produk ini terlebih dahulu untuk membuka akses ulasan!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>