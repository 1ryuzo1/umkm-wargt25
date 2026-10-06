-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2026 at 05:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toko`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_detail`
--

CREATE TABLE `tb_detail` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `varian` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_detail`
--

INSERT INTO `tb_detail` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`, `varian`, `catatan`) VALUES
(1, 1, 201, 2, 'Level Pedas Sedang (Rekomendasi)', ''),
(2, 1, 212, 1, 'Standard Original', ''),
(3, 2, 201, 2, 'Pedas Manis', ''),
(4, 2, 214, 3, 'Normal', ''),
(5, 3, 272, 1, 'Porsi Standard Original', ''),
(6, 4, 280, 1, 'Porsi Standard Original', '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_produk`
--

CREATE TABLE `tb_produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `poto` text NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_produk`
--

INSERT INTO `tb_produk` (`id`, `nama`, `harga`, `stok`, `poto`, `kategori`, `deskripsi`, `gambar`) VALUES
(201, 'Bakso Bakar (Varian Pedas & Ori)', 10000, 50, '', 'Bakar-bakaran', 'Bakar-bakaran dengan pilihan varian pedas atau original.', ''),
(202, 'Sosis Bakar (Varian Pedas & Ori)', 10000, 50, '', 'Bakar-bakaran', 'Bakar-bakaran dengan pilihan varian rasa pedas atau original.', ''),
(203, 'Usus Bakar (Varian Pedas & Ori)', 8000, 50, '', 'Bakar-bakaran', 'Bakar-bakaran dengan pilihan varian rasa pedas atau original.', ''),
(204, 'Fishroll Bakar (Varian Pedas & Ori)', 12000, 50, '', 'Bakar-bakaran', 'Bakar-bakaran dengan pilihan varian rasa pedas atau original.', ''),
(205, 'Salmon Ball Bakar (Varian Pedas & Ori)', 13000, 50, '', 'Bakar-bakaran', 'Bakar-bakaran dengan pilihan varian rasa pedas atau original.', ''),
(206, 'Chikuwa Bakar (Varian Pedas & Ori)', 12000, 50, '', 'Bakar-bakaran', 'Bakar-bakaran dengan pilihan varian rasa pedas atau original.', ''),
(207, 'Kentang Goreng', 10000, 60, '', 'Goreng-gorengan', 'Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.', ''),
(208, 'Sosis Goreng', 10000, 60, '', 'Goreng-gorengan', 'Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.', ''),
(209, 'Otak-otak Goreng', 8000, 60, '', 'Goreng-gorengan', 'Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.', ''),
(210, 'Baso Goreng', 8000, 60, '', 'Goreng-gorengan', 'Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.', ''),
(211, 'Tahu Bulat', 7000, 60, '', 'Goreng-gorengan', 'Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.', ''),
(212, 'Sotong Goreng', 9000, 60, '', 'Goreng-gorengan', 'Bisa pilih bumbu & kombinasi: balado, jagung, asin, rumput laut, bbq, keju, saus.', ''),
(213, 'Matcha Hangat', 6000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(214, 'Teh Tawar Hangat', 3000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(215, 'Teh Manis Hangat', 4000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(216, 'Susu Coklat Hangat', 6000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(217, 'Susu Putih Hangat', 6000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(218, 'Coklat Hangat', 6000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(219, 'Kopi Hitam Hangat', 5000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(220, 'Kopi Susu Hangat', 6000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(221, 'Wedang Jahe', 7000, 40, '', 'Minuman Hangat', 'Minuman hangat tradisional penangkal dingin.', ''),
(222, 'Good Day Hangat', 6000, 40, '', 'Minuman Hangat', 'Minuman hangat nikmat.', ''),
(223, 'Matcha Dingin', 7000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(224, 'Susu Matcha Dingin', 8000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(225, 'Teh Tawar Dingin', 4000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(226, 'Teh Manis Dingin', 5000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(227, 'Susu Coklat Dingin', 7000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(228, 'Susu Putih Dingin', 7000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(229, 'Coklat Dingin', 7000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(230, 'Teajus', 3000, 40, '', 'Aneka Jus', 'Minuman es segar.', ''),
(231, 'Marimas', 3000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(232, 'Top Ice', 3000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(233, 'Good Day Dingin', 7000, 40, '', 'Minuman Es', 'Minuman es segar.', ''),
(234, 'Teh Tawar Biasa', 3000, 40, '', 'Minuman Biasa', 'Suhu ruangan / biasa.', ''),
(235, 'Teh Manis Biasa', 4000, 40, '', 'Minuman Biasa', 'Suhu ruangan / biasa.', ''),
(236, 'Susu Putih Biasa', 5000, 40, '', 'Minuman Biasa', 'Suhu ruangan / biasa.', ''),
(237, 'Susu Coklat Biasa', 5000, 40, '', 'Minuman Biasa', 'Suhu ruangan / biasa.', ''),
(238, 'Pop Ice (Aneka Rasa)', 5000, 40, '', 'Aneka Jus', 'Minuman pop ice segar aneka rasa.', ''),
(239, 'Chitato', 10000, 30, '', 'Snack', 'Camilan snack ringan.', ''),
(240, 'Chitato Lite', 10000, 30, '', 'Snack', 'Camilan snack ringan.', ''),
(241, 'Chiki Balls', 5000, 30, '', 'Snack', 'Camilan snack ringan.', ''),
(242, 'Pringles', 15000, 30, '', 'Snack', 'Camilan snack ringan.', ''),
(243, 'Tos Tos', 7000, 30, '', 'Snack', 'Camilan snack ringan.', ''),
(244, 'Oreo', 8000, 30, '', 'Snack', 'Camilan biskuit favorit.', ''),
(245, 'Tango', 7000, 30, '', 'Snack', 'Camilan wafer renyah.', ''),
(246, 'Nabati', 6000, 30, '', 'Snack', 'Camilan wafer lezat.', ''),
(247, 'Sip', 3000, 30, '', 'Snack', 'Camilan ringan.', ''),
(248, 'Ahh', 3000, 30, '', 'Snack', 'Camilan wafer roll.', ''),
(249, 'Dilan', 4000, 30, '', 'Snack', 'Camilan biskuit karamel.', ''),
(250, 'Chocolatos', 4000, 30, '', 'Snack', 'Camilan wafer cokelat.', ''),
(251, 'Astor', 10000, 30, '', 'Snack', 'Camilan wafer stick.', ''),
(252, 'Beng Beng', 4000, 30, '', 'Snack', 'Camilan cokelat kriuk.', ''),
(253, 'Better', 4000, 30, '', 'Snack', 'Camilan biskuit lapis vanila.', ''),
(254, 'Sosis So Nice', 3000, 30, '', 'Snack', 'Camilan sosis siap makan.', ''),
(255, 'Supreme', 5000, 30, '', 'Snack', 'Camilan snack.', ''),
(256, 'Malkist', 7000, 30, '', 'Snack', 'Biskuit malkist gurih.', ''),
(257, 'Biskitop Vegetable and Cheese', 8000, 30, '', 'Snack', 'Biskuit sayur dan keju.', ''),
(258, 'Tricks', 8000, 30, '', 'Snack', 'Potato baked crackers.', ''),
(259, 'Enaak', 3000, 30, '', 'Snack', 'Camilan ringan.', ''),
(260, 'Rin-bae', 5000, 30, '', 'Snack', 'Camilan snack.', ''),
(261, 'Taro', 6000, 30, '', 'Snack', 'Snack legendaris.', ''),
(262, 'Zetz', 5000, 30, '', 'Snack', 'Camilan ringan.', ''),
(263, 'Waffelo', 5000, 30, '', 'Snack', 'Wafer lezat.', ''),
(264, 'Tic-Tic', 4000, 30, '', 'Snack', 'Snack mie kriuk.', ''),
(265, 'Pilus', 4000, 30, '', 'Snack', 'Pilus gurih renyah.', ''),
(266, 'Gerry', 5000, 30, '', 'Snack', 'Camilan biskuit/wafer.', ''),
(267, 'Good Time', 7000, 30, '', 'Snack', 'Cookies cokelat chocochips.', ''),
(268, 'Selai Olays', 6000, 30, '', 'Snack', 'Camilan biskuit selai.', ''),
(269, 'Nextstar', 5000, 30, '', 'Snack', 'Nastar cookies lembut.', ''),
(270, 'Cheetos', 7000, 30, '', 'Snack', 'Snack jagung gurih.', ''),
(271, 'Qtela', 8000, 30, '', 'Snack', 'Keripik singkong/tempe renyah.', ''),
(272, 'Nasi Goreng (Varian Pedas & Ori)', 15000, 45, '', 'Nasi Nasian', 'Nasi goreng lezat dengan pilihan varian pedas atau ori.', ''),
(273, 'Nasi Kuning (Varian Pedas & Ori)', 13000, 45, '', 'Nasi Nasian', 'Nasi kuning harum dengan pilihan varian pedas atau ori.', ''),
(274, 'Nasi Uduk (Varian Pedas & Ori)', 13000, 45, '', 'Nasi Nasian', 'Nasi uduk gurih dengan pilihan varian pedas atau ori.', ''),
(275, 'Nasi Lengko (Varian Pedas & Ori)', 12000, 45, '', 'Nasi Nasian', 'Nasi lengko khas dengan pilihan varian pedas atau ori.', ''),
(276, 'Nasi Jamblang (Pilihan Toping)', 15000, 45, '', 'Nasi Nasian', 'Nasi jamblang dengan pilihan toping: semur telur, telur puyuh, semur tahu, usus, perkedel, semur dadar, dadar.', ''),
(277, 'Bubur (Pilihan Toping)', 10000, 45, '', 'Nasi Nasian', 'Bubur dengan pilihan toping (pakai/tidak pakai kacang, sayur, sup, ayam).', ''),
(278, 'Nasi Ayam (Varian Pedas & Ori)', 18000, 40, '', 'Makanan Berat', 'Nasi ayam lezat dengan varian pedas atau ori.', ''),
(279, 'Nasi Dadar (Varian Pedas & Ori)', 10000, 40, '', 'Makanan Berat', 'Nasi telur dadar dengan varian pedas atau ori.', ''),
(280, 'Sop Sayur Asem', 10000, 34, '', 'Makanan Berat', 'Sayur asem segar menghangatkan.', ''),
(281, 'Sop Sayur Lodeh', 10000, 35, '', 'Makanan Berat', 'Sayur lodeh gurih khas nusantara.', '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_harga` int(11) NOT NULL,
  `opsi_pengiriman` varchar(100) DEFAULT NULL,
  `opsi_pembayaran` varchar(100) DEFAULT NULL,
  `status_pesanan` varchar(50) NOT NULL DEFAULT 'Menunggu Pembayaran'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal`, `total_harga`, `opsi_pengiriman`, `opsi_pembayaran`, `status_pesanan`) VALUES
(1, 2, '2026-07-29', 29000, 'Ambil di Resto (Dine In / Take Away)', 'Bayar Langsung di Kasir (Cash / COD)', 'Selesai'),
(2, 2, '2026-07-29', 29000, 'Ambil di Resto (Dine In / Take Away)', 'Bayar Langsung di Kasir (Cash / COD)', 'Selesai'),
(3, 2, '2026-07-30', 15000, 'Ambil di Resto (Dine In / Take Away)', 'Bayar Langsung di Kasir (Cash / COD)', 'Selesai'),
(4, 2, '2026-07-30', 10000, 'Ambil di Resto (Dine In / Take Away)', 'Bayar Langsung di Kasir (Cash / COD)', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `tb_ulasan`
--

CREATE TABLE `tb_ulasan` (
  `id_ulasan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `komentar` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hp` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `nama`, `email`, `username`, `password`, `hp`, `alamat`, `role`) VALUES
(1, 'Master Admin', 'admin@wargt25.com', 'admin', '$2y$10$8g6i/Gwhd6I30SSFLk0LquifVvGgeztC66bZ8YqwDXfoqHtz0Mvey', '08123456789', 'Jl. Nusantara No. 25', 'admin'),
(2, 'gatau', 'rizkyazzamy41@gmail.com', 'anjai', '$2y$10$sXCXKtI1C3.UWnn9yUlkCui/8zonuHDtlvIv/3GQcMl/N7GmCNuHO', '243564743', 'ikan', 'pelanggan');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  UNIQUE KEY `uq_kategori_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `slug`) VALUES
(1, 'Makanan Berat', 'makanan-berat'),
(2, 'Bakar-bakaran', 'bakar-bakaran'),
(3, 'Nasi Nasian', 'nasi-nasian'),
(4, 'Goreng-gorengan', 'goreng-gorengan'),
(5, 'Snack', 'snack'),
(6, 'Minuman Hangat', 'minuman-hangat'),
(7, 'Minuman Biasa', 'minuman-biasa'),
(8, 'Minuman Es', 'minuman-es'),
(9, 'Aneka Jus', 'aneka-jus');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `tb_ulasan`
--
ALTER TABLE `tb_ulasan`
  ADD PRIMARY KEY (`id_ulasan`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_detail`
--
ALTER TABLE `tb_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_ulasan`
--
ALTER TABLE `tb_ulasan`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_detail`
--
ALTER TABLE `tb_detail`
  ADD CONSTRAINT `tb_detail_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_detail_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
