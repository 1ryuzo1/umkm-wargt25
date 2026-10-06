<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini khusus administrator.');
}
include_once 'config/database.php';
include_once 'config/helpers.php';
wg_ensure_category_table($conn);

// 1. Deteksi nama kolom yang benar pada tabel tb_produk
$col_nama     = 'nama';
$col_harga    = 'harga';
$col_stok     = 'stok';
$col_desk     = 'deskripsi';
$col_img      = 'poto';
$col_kategori = 'kategori';

$cek = mysqli_query($conn, "SHOW COLUMNS FROM tb_produk");
if ($cek) {
    $fields = [];
    while ($row = mysqli_fetch_assoc($cek)) {
        $fields[] = strtolower($row['Field']);
    }

    // Sesuaikan dengan kolom yang tersedia di database
    if (in_array('nama_produk', $fields)) $col_nama = 'nama_produk';
    elseif (in_array('nama', $fields)) $col_nama = 'nama';

    if (in_array('harga_produk', $fields)) $col_harga = 'harga_produk';
    elseif (in_array('harga', $fields)) $col_harga = 'harga';

    if (in_array('stok', $fields)) $col_stok = 'stok';
    elseif (in_array('jumlah_stok', $fields)) $col_stok = 'jumlah_stok';

    if (in_array('deskripsi', $fields)) $col_desk = 'deskripsi';
    elseif (in_array('keterangan', $fields)) $col_desk = 'keterangan';

    if (in_array('poto', $fields)) $col_img = 'poto';
    elseif (in_array('gambar', $fields)) $col_img = 'gambar';
    elseif (in_array('foto', $fields)) $col_img = 'foto';

    if (in_array('kategori', $fields)) $col_kategori = 'kategori';
}

// 2. Hapus data lama dari tb_produk
mysqli_query($conn, "DELETE FROM tb_produk");

// 3. Daftar menu baru lengkap
// Format tiap item: [nama, harga, stok, deskripsi, kategori]
// Nilai kategori WAJIB SAMA PERSIS dengan daftar filter di index.php & menu.php
// agar fitur filter kategori berfungsi.
$menu_baru = [
    // --- BAKAR-BAKARAN ---
    ["Bakso Bakar (Varian Pedas & Ori)", 10000, 50, "Bakar-bakaran dengan pilihan varian rasa pedas atau original.", "Bakar-bakaran"],
    ["Sosis Bakar (Varian Pedas & Ori)", 10000, 50, "Bakar-bakaran dengan pilihan varian rasa pedas atau original.", "Bakar-bakaran"],
    ["Usus Bakar (Varian Pedas & Ori)", 8000, 50, "Bakar-bakaran dengan pilihan varian rasa pedas atau original.", "Bakar-bakaran"],
    ["Fishroll Bakar (Varian Pedas & Ori)", 12000, 50, "Bakar-bakaran dengan pilihan varian rasa pedas atau original.", "Bakar-bakaran"],
    ["Salmon Ball Bakar (Varian Pedas & Ori)", 13000, 50, "Bakar-bakaran dengan pilihan varian rasa pedas atau original.", "Bakar-bakaran"],
    ["Chikuwa Bakar (Varian Pedas & Ori)", 12000, 50, "Bakar-bakaran dengan pilihan varian rasa pedas atau original.", "Bakar-bakaran"],

    // --- GORENG-GORENGAN ---
    ["Kentang Goreng", 10000, 60, "Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.", "Goreng-gorengan"],
    ["Sosis Goreng", 10000, 60, "Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.", "Goreng-gorengan"],
    ["Otak-otak Goreng", 8000, 60, "Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.", "Goreng-gorengan"],
    ["Baso Goreng", 8000, 60, "Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.", "Goreng-gorengan"],
    ["Tahu Bulat", 7000, 60, "Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.", "Goreng-gorengan"],
    ["Sotong Goreng", 9000, 60, "Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.", "Goreng-gorengan"],

    // --- MINUMAN HANGAT ---
    ["Matcha Hangat", 6000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Teh Tawar Hangat", 3000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Teh Manis Hangat", 4000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Susu Coklat Hangat", 6000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Susu Putih Hangat", 6000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Coklat Hangat", 6000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Kopi Hitam Hangat", 5000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Kopi Susu Hangat", 6000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],
    ["Wedang Jahe", 7000, 40, "Minuman hangat tradisional penangkal dingin.", "Minuman Hangat"],
    ["Good Day Hangat", 6000, 40, "Minuman hangat nikmat.", "Minuman Hangat"],

    // --- MINUMAN ES ---
    ["Matcha Dingin", 7000, 40, "Minuman es segar.", "Minuman Es"],
    ["Susu Matcha Dingin", 8000, 40, "Minuman es segar.", "Minuman Es"],
    ["Teh Tawar Dingin", 4000, 40, "Minuman es segar.", "Minuman Es"],
    ["Teh Manis Dingin", 5000, 40, "Minuman es segar.", "Minuman Es"],
    ["Susu Coklat Dingin", 7000, 40, "Minuman es segar.", "Minuman Es"],
    ["Susu Putih Dingin", 7000, 40, "Minuman es segar.", "Minuman Es"],
    ["Coklat Dingin", 7000, 40, "Minuman es segar.", "Minuman Es"],
    ["Teajus", 3000, 40, "Minuman es segar.", "Minuman Es"],
    ["Marimas", 3000, 40, "Minuman es segar.", "Minuman Es"],
    ["Top Ice", 3000, 40, "Minuman es segar.", "Minuman Es"],
    ["Good Day Dingin", 7000, 40, "Minuman es segar.", "Minuman Es"],

    // --- MINUMAN BIASA ---
    ["Teh Tawar Biasa", 3000, 40, "Suhu ruangan / biasa.", "Minuman Biasa"],
    ["Teh Manis Biasa", 4000, 40, "Suhu ruangan / biasa.", "Minuman Biasa"],
    ["Susu Putih Biasa", 5000, 40, "Suhu ruangan / biasa.", "Minuman Biasa"],
    ["Susu Coklat Biasa", 5000, 40, "Suhu ruangan / biasa.", "Minuman Biasa"],

    // --- ANEKA JUS ---
    ["Pop Ice (Aneka Rasa)", 5000, 40, "Minuman pop ice segar aneka rasa.", "Aneka Jus"],

    // --- SNACK ---
    ["Chitato", 10000, 30, "Camilan snack ringan.", "Snack"],
    ["Chitato Lite", 10000, 30, "Camilan snack ringan.", "Snack"],
    ["Chiki Balls", 5000, 30, "Camilan snack ringan.", "Snack"],
    ["Pringles", 15000, 30, "Camilan snack ringan.", "Snack"],
    ["Tos Tos", 7000, 30, "Camilan snack ringan.", "Snack"],
    ["Oreo", 8000, 30, "Camilan biskuit favorit.", "Snack"],
    ["Tango", 7000, 30, "Camilan wafer renyah.", "Snack"],
    ["Nabati", 6000, 30, "Camilan wafer lezat.", "Snack"],
    ["Sip", 3000, 30, "Camilan ringan.", "Snack"],
    ["Ahh", 3000, 30, "Camilan wafer roll.", "Snack"],
    ["Dilan", 4000, 30, "Camilan biskuit karamel.", "Snack"],
    ["Chocolatos", 4000, 30, "Camilan wafer cokelat.", "Snack"],
    ["Astor", 10000, 30, "Camilan wafer stick.", "Snack"],
    ["Beng Beng", 4000, 30, "Camilan cokelat kriuk.", "Snack"],
    ["Better", 4000, 30, "Camilan biskuit lapis vanila.", "Snack"],
    ["Sosis So Nice", 3000, 30, "Camilan sosis siap makan.", "Snack"],
    ["Supreme", 5000, 30, "Camilan snack.", "Snack"],
    ["Malkist", 7000, 30, "Biskuit malkist gurih.", "Snack"],
    ["Biskitop Vegetable and Cheese", 8000, 30, "Biskuit sayur dan keju.", "Snack"],
    ["Tricks", 8000, 30, "Potato baked crackers.", "Snack"],
    ["Enaak", 3000, 30, "Camilan ringan.", "Snack"],
    ["Rin-bae", 5000, 30, "Camilan snack.", "Snack"],
    ["Taro", 6000, 30, "Snack legendaris.", "Snack"],
    ["Zetz", 5000, 30, "Camilan ringan.", "Snack"],
    ["Waffelo", 5000, 30, "Wafer lezat.", "Snack"],
    ["Tic-Tic", 4000, 30, "Snack mie kriuk.", "Snack"],
    ["Pilus", 4000, 30, "Pilus gurih renyah.", "Snack"],
    ["Gerry", 5000, 30, "Camilan biskuit/wafer.", "Snack"],
    ["Good Time", 7000, 30, "Cookies cokelat chocochips.", "Snack"],
    ["Selai Olays", 6000, 30, "Camilan biskuit selai.", "Snack"],
    ["Nextstar", 5000, 30, "Nastar cookies lembut.", "Snack"],
    ["Cheetos", 7000, 30, "Snack jagung gurih.", "Snack"],
    ["Qtela", 8000, 30, "Keripik singkong/tempe renyah.", "Snack"],

    // --- NASI NASIAN ---
    ["Nasi Goreng (Varian Pedas & Ori)", 15000, 45, "Nasi goreng lezat dengan pilihan varian pedas atau ori.", "Nasi Nasian"],
    ["Nasi Kuning (Varian Pedas & Ori)", 13000, 45, "Nasi kuning harum dengan pilihan varian pedas atau ori.", "Nasi Nasian"],
    ["Nasi Uduk (Varian Pedas & Ori)", 13000, 45, "Nasi uduk gurih dengan pilihan varian pedas atau ori.", "Nasi Nasian"],
    ["Nasi Lengko (Varian Pedas & Ori)", 12000, 45, "Nasi lengko khas dengan pilihan varian pedas atau ori.", "Nasi Nasian"],
    ["Nasi Jamblang (Pilihan Toping)", 15000, 45, "Nasi jamblang dengan pilihan toping: semur telur, telur puyuh, semur tahu, usus, perkedel, semur dadar, dadar.", "Nasi Nasian"],
    ["Bubur (Pilihan Toping)", 10000, 45, "Bubur dengan pilihan toping (pakai/tidak pakai kacang, sayur, sup, ayam).", "Nasi Nasian"],

    // --- MAKANAN BERAT ---
    ["Nasi Ayam (Varian Pedas & Ori)", 18000, 40, "Nasi ayam lezat dengan varian pedas atau ori.", "Makanan Berat"],
    ["Nasi Dadar (Varian Pedas & Ori)", 10000, 40, "Nasi telur dadar dengan varian pedas atau ori.", "Makanan Berat"],
    ["Sop Sayur Asem", 10000, 35, "Sayur asem segar menghangatkan.", "Makanan Berat"],
    ["Sop Sayur Lodeh", 10000, 35, "Sayur lodeh gurih khas nusantara.", "Makanan Berat"]
];

$berhasil = 0;
foreach ($menu_baru as $item) {
    $nama     = mysqli_real_escape_string($conn, $item[0]);
    $harga    = intval($item[1]);
    $stok     = intval($item[2]);
    $deskripsi = mysqli_real_escape_string($conn, $item[3]);
    $kategori = mysqli_real_escape_string($conn, $item[4]);

    // Gunakan nama kolom yang sudah terdeteksi otomatis
    $sql = "INSERT INTO tb_produk ($col_nama, $col_harga, $col_stok, $col_desk, $col_img, $col_kategori)
            VALUES ('$nama', '$harga', '$stok', '$deskripsi', '', '$kategori')";
    if (mysqli_query($conn, $sql)) {
        $berhasil++;
    }
}

echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>";
echo "<h2 style='color: #273526;'>Berhasil! Sebanyak $berhasil menu baru telah dimasukkan.</h2>";
echo "<p>Semua produk lama telah dibersihkan dan diganti dengan menu baru (lengkap dengan kategori).</p>";
echo "<br><a href='admin/produk.php' style='background: #273526; color: white; padding: 10px 20px; text-decoration: none; border-radius: 20px; font-weight: bold;'>Buka Manajemen Produk Admin</a>";
echo "</div>";
