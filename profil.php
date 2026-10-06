<?php 
include_once 'includes/header.php'; 

if (!isset($_SESSION['role'])) {
    echo "<script>window.location.href='auth/login.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];

// Proses Update Profil Pengguna
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $hp = mysqli_real_escape_string($conn, $_POST['hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    $cek_username = mysqli_query($conn, "SELECT id FROM tb_user WHERE username='$username' AND id<>$id_user LIMIT 1");
    if ($cek_username && mysqli_num_rows($cek_username) > 0) {
        $profil_error = 'Username sudah dipakai akun lain.';
    }

    $update_str = "UPDATE tb_user SET nama='$nama', username='$username', hp='$hp', alamat='$alamat' WHERE id=$id_user";
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $update_str = "UPDATE tb_user SET nama='$nama', username='$username', password='$password_hash', hp='$hp', alamat='$alamat' WHERE id=$id_user";
    }

    if (empty($profil_error) && mysqli_query($conn, $update_str)) {
        $_SESSION['nama'] = $nama;
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profil.php';</script>";
        exit;
    } elseif (empty($profil_error)) {
        $profil_error = 'Profil gagal diperbarui. Periksa kembali data yang dimasukkan.';
    }
}

// Ambil data user terupdate
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_user WHERE id = $id_user"));
?>

<div class="container my-5">
    <?php if (!empty($profil_error)): ?><div class="alert alert-danger py-2 small"><?= htmlspecialchars($profil_error); ?></div><?php endif; ?>
    <div class="row g-4">
        <!-- FORM EDIT PROFILE PELANGGAN (Kiri) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="font-display fw-bold mb-3" style="color: #2b302a;">Pengaturan Akun</h5>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Lengkap</label>
                        <input type="text" class="form-control rounded-3 text-sm" name="nama" value="<?= htmlspecialchars($user_data['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Username</label>
                        <input type="text" class="form-control rounded-3 text-sm" name="username" value="<?= htmlspecialchars($user_data['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nomor WhatsApp (HP)</label>
                        <input type="text" class="form-control rounded-3 text-sm" name="hp" value="<?= htmlspecialchars($user_data['hp']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Alamat Rumah</label>
                        <textarea class="form-control rounded-3 text-sm" name="alamat" rows="2" required><?= htmlspecialchars($user_data['alamat']) ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-muted">Kata Sandi Baru (Kosongkan jika tidak diganti)</label>
                        <input type="password" class="form-control rounded-3 text-sm" name="password" placeholder="••••••••">
                    </div>
                    <button type="submit" name="update_profil" class="btn btn-wargt-primary w-100 py-2 rounded-pill fw-semibold text-sm">Simpan Pembaruan</button>
                </form>
            </div>
        </div>

        <!-- RIWAYAT TRANSAKSI PELANGGAN (Kanan) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="font-display fw-bold mb-3" style="color: #2b302a;">Riwayat Transaksi Belanja</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr class="small">
                                <th>ID Nota</th>
                                <th>Tanggal</th>
                                <th>Total Belanja</th>
                                <th class="text-center">Aksi Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $query_riwayat = mysqli_query($conn, "SELECT * FROM tb_transaksi WHERE id_pelanggan = $id_user ORDER BY id_transaksi DESC");
                            if (mysqli_num_rows($query_riwayat) == 0):
                            ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Anda belum pernah melakukan transaksi.</td></tr>
                            <?php 
                            else:
                                while($trx = mysqli_fetch_assoc($query_riwayat)):
                            ?>
                            <tr>
                                <td><b style="color: #a67c52;">#TRX-<?= $trx['id_transaksi'] ?></b></td>
                                <td class="text-muted small"><?= date('d M Y', strtotime($trx['tanggal'])) ?></td>
                                <td class="fw-bold"><?= rupiah($trx['total_harga']) ?></td>
                                <td class="text-center">
                                    <!-- Pelanggan dapat mencetak invoice secara mandiri -->
                                    <a href="cetak_nota.php?id=<?= $trx['id_transaksi'] ?>" target="_blank" class="btn btn-sm text-white px-3 rounded-pill text-xs" style="background-color: #273526; font-size: 0.75rem;">
                                        <i class="bi bi-printer me-1"></i> Cetak Invoice
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>