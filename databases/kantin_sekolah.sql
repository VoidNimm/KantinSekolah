-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 02:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kantin_sekolah`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `jurusan` enum('RPL','AKL','MP','Adnor') NOT NULL,
  `nama_makanan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `jurusan`, `nama_makanan`, `deskripsi`, `gambar`, `harga`) VALUES
(4, 'AKL', 'Indomie', 'mie enak', 'uploads/682572a61042f.jpg', 8000),
(6, 'AKL', 'Es Jeruk', 'seger disaat panas-panas', 'uploads/68257313437cd.jpg', 6000),
(15, 'RPL', 'Nasi Katsu ', 'Katsu enak buatan anak rpl', 'uploads/682571c027d60.jpg', 10000),
(16, 'RPL', 'Mineral Water', 'seger banget', 'uploads/682571f5b48e3.jpg', 6000),
(17, 'RPL', 'Nasi Nugget', 'nasi dengan nugget dan saos', 'uploads/6825722c97fcd.jpg', 10000),
(21, 'MP', 'pepen oreng', 'asdasdas', 'uploads/6823ea0f8e9f1.jpg', 12000),
(22, 'MP', 'Teh Manis', 'asdkjapsidjaisniaskdas', 'uploads/6823ea530b619.jpg', 20000),
(23, 'MP', 'Rice Bowl Brocoli Goreng', 'Masakan mamah tinuk terlezat', 'uploads/6823ef41a4509.jpg', 10000),
(24, 'Adnor', 'Ciki', 'enak bat dijamin gile bat dah pokoknya', 'uploads/682573970e1ff.jpg', 6000),
(25, 'Adnor', 'Gorengan', 'enak pedes pake cabe', 'uploads/682573ed3f181.jpg', 2000),
(27, 'MP', 'spaghettiti enak', 'aosjdasojdaasdj', 'uploads/682560104fa46.jpg', 12000);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `jurusan` enum('RPL','AKL','MP','Adnor') NOT NULL,
  `nama_makanan` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `jurusan`, `nama_makanan`, `harga`, `jumlah`, `total`, `tanggal`) VALUES
(1, 'RPL', 'Nasi Goreng Spesial', 15000, 5, 75000, '2025-05-10 20:56:06'),
(2, 'RPL', 'orang aring', 45000, 3, 135000, '2025-05-11 19:34:33'),
(3, 'RPL', 'pepen oreng', 12000, 16, 192000, '2025-05-11 20:20:04'),
(4, 'RPL', 'pepen oreng', 12000, 1, 12000, '2025-05-11 20:36:22'),
(5, 'RPL', '🍆🍆🍆', 100000, 100, 10000000, '2025-05-11 21:08:35'),
(6, 'RPL', 'pepen oreng oreng', 12000, 10, 120000, '2025-05-12 14:15:39'),
(7, 'RPL', 'pepen oreng', 12000, 1, 12000, '2025-05-14 06:43:14'),
(8, 'RPL', 'pepen oreng', 12000, 2, 24000, '2025-05-14 06:43:20'),
(10, 'RPL', 'Teh Manis', 6000, 1, 6000, '2025-05-14 06:44:05'),
(11, 'RPL', 'orang aring', 45000, 1, 45000, '2025-05-14 06:44:14'),
(12, 'RPL', 'orang aring', 45000, 1, 45000, '2025-05-14 06:44:23'),
(13, 'RPL', 'pepen oreng', 12000, 1, 12000, '2025-05-14 07:52:39'),
(14, 'RPL', 'Teh Manis', 6000, 1, 6000, '2025-05-14 07:52:42'),
(15, 'MP', 'Teh Manis', 20000, 10, 200000, '2025-05-14 07:57:02'),
(16, 'MP', 'pepen oreng', 12000, 100, 1200000, '2025-05-14 08:23:38'),
(17, 'MP', 'pepen oreng', 12000, 100, 1200000, '2025-05-14 08:24:44'),
(18, 'MP', 'pepen oreng', 12000, 100, 1200000, '2025-05-14 08:24:46'),
(19, 'MP', 'Rice Bowl Brocoli Goreng', 10000, 100, 1000000, '2025-05-14 08:31:51'),
(20, 'Adnor', 'Nasi Goreng Kecap Inggris', 15000, 1, 15000, '2025-05-14 09:22:22'),
(21, 'Adnor', 'Es Teh Original British', 6000, 1, 6000, '2025-05-14 09:22:24'),
(22, 'MP', 'pepen oreng', 12000, 1, 12000, '2025-05-14 11:24:49'),
(24, 'RPL', 'orang aring', 450000, 1, 450000, '2025-05-15 07:40:51'),
(25, 'RPL', 'Teh Manis', 6000, 10, 60000, '2025-05-15 08:14:02'),
(26, 'Adnor', 'Ciki', 6000, 2, 12000, '2025-05-15 12:06:43'),
(27, 'Adnor', 'Gorengan', 2000, 1, 2000, '2025-05-15 12:06:43'),
(28, 'Adnor', 'Gorengan', 2000, 5, 10000, '2025-05-15 12:06:55'),
(29, 'Adnor', 'Ciki', 6000, 1, 6000, '2025-05-15 12:06:55'),
(30, 'RPL', 'Mineral Water', 6000, 2, 12000, '2025-05-15 18:41:29'),
(31, 'RPL', 'Nasi Katsu ', 10000, 1, 10000, '2025-05-15 18:41:29'),
(32, 'RPL', 'Nasi Katsu ', 10000, 3, 30000, '2025-07-29 10:36:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jurusan` enum('RPL','AKL','MP','Adnor') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `jurusan`, `nama_lengkap`) VALUES
(1, 'adminrpl', '12345', 'RPL', NULL),
(2, 'adminkeu', '12345', 'AKL', NULL),
(3, 'adminghanim', '12345', 'RPL', 'Akmal Ghanim'),
(5, 'biasa', 'biasa123', 'RPL', 'orang biasa'),
(6, '', '', '', ''),
(7, 'biasaaja', '12345', 'RPL', 'orang biasa saja'),
(8, 'akmaladmin', '1234', 'RPL', 'akmal admin ganteng'),
(9, 'wong biasa', '12345', 'RPL', 'biasa ah'),
(10, 'adminmp', '12345', 'MP', 'Taqy Nabil Negriano'),
(11, 'adminadaptif', '12345', 'Adnor', 'orang aring'),
(12, 'adminakl', '12345', 'AKL', 'Jangkung'),
(13, 'adminadnor', '12345', 'Adnor', 'adnor ahmad');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
