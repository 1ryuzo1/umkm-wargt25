-- Migrasi aman & kompatibel untuk database Wargt25 lama.
-- Bisa di-import ulang. Kolom yang sudah ada akan dilewati.
USE db_toko;

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

-- tb_transaksi: opsi pengiriman
SET @exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tb_transaksi'
      AND COLUMN_NAME = 'opsi_pengiriman'
);
SET @sql = IF(@exists = 0,
    'ALTER TABLE tb_transaksi ADD COLUMN opsi_pengiriman VARCHAR(100) DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- tb_transaksi: opsi pembayaran
SET @exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tb_transaksi'
      AND COLUMN_NAME = 'opsi_pembayaran'
);
SET @sql = IF(@exists = 0,
    'ALTER TABLE tb_transaksi ADD COLUMN opsi_pembayaran VARCHAR(100) DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- tb_transaksi: status pesanan
SET @exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tb_transaksi'
      AND COLUMN_NAME = 'status_pesanan'
);
SET @sql = IF(@exists = 0,
    'ALTER TABLE tb_transaksi ADD COLUMN status_pesanan VARCHAR(50) NOT NULL DEFAULT ''Menunggu Pembayaran''',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- tb_detail: varian pesanan
SET @exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tb_detail'
      AND COLUMN_NAME = 'varian'
);
SET @sql = IF(@exists = 0,
    'ALTER TABLE tb_detail ADD COLUMN varian VARCHAR(255) DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- tb_detail: catatan pesanan
SET @exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'tb_detail'
      AND COLUMN_NAME = 'catatan'
);
SET @sql = IF(@exists = 0,
    'ALTER TABLE tb_detail ADD COLUMN catatan TEXT DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Perbaikan data minor.
UPDATE tb_produk
SET kategori = 'Snack'
WHERE id = 243 AND (kategori IS NULL OR TRIM(kategori) = '');

UPDATE tb_produk
SET nama = 'Pringles'
WHERE nama = 'Pringels';

-- Pastikan transaksi lama tidak punya status kosong.
UPDATE tb_transaksi
SET status_pesanan = 'Menunggu Pembayaran'
WHERE status_pesanan IS NULL OR TRIM(status_pesanan) = '';
