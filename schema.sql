-- ============================================================
-- SKEMA DATABASE Wargt25 / Baso Bakar Nusantara
-- ============================================================
CREATE DATABASE IF NOT EXISTS db_toko CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_toko;

CREATE TABLE IF NOT EXISTS tb_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    hp VARCHAR(20) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    role ENUM('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_username (username),
    UNIQUE KEY uq_user_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS tb_produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    harga INT NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    poto VARCHAR(255) DEFAULT NULL,
    kategori VARCHAR(100) DEFAULT NULL,
    deskripsi TEXT DEFAULT NULL,
    gambar VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS tb_transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT NOT NULL,
    tanggal DATE NOT NULL,
    total_harga INT NOT NULL DEFAULT 0,
    opsi_pengiriman VARCHAR(100) DEFAULT NULL,
    opsi_pembayaran VARCHAR(100) DEFAULT NULL,
    status_pesanan VARCHAR(50) NOT NULL DEFAULT 'Menunggu Pembayaran',
    CONSTRAINT fk_transaksi_user FOREIGN KEY (id_pelanggan) REFERENCES tb_user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS tb_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    varian VARCHAR(255) DEFAULT NULL,
    catatan TEXT DEFAULT NULL,
    CONSTRAINT fk_detail_transaksi FOREIGN KEY (id_transaksi) REFERENCES tb_transaksi(id_transaksi) ON DELETE CASCADE,
    CONSTRAINT fk_detail_produk FOREIGN KEY (id_produk) REFERENCES tb_produk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS tb_ulasan (
    id_ulasan INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT NOT NULL,
    id_user INT NOT NULL,
    nama_user VARCHAR(100) NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    komentar TEXT NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ulasan_produk FOREIGN KEY (id_produk) REFERENCES tb_produk(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_kategori_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO kategori (nama_kategori, slug) VALUES
('Makanan Berat','makanan-berat'),
('Bakar-bakaran','bakar-bakaran'),
('Nasi Nasian','nasi-nasian'),
('Goreng-gorengan','goreng-gorengan'),
('Snack','snack'),
('Minuman Hangat','minuman-hangat'),
('Minuman Biasa','minuman-biasa'),
('Minuman Es','minuman-es'),
('Aneka Jus','aneka-jus');

-- Untuk database lama yang belum memiliki kolom baru, gunakan migrate_db.sql.
