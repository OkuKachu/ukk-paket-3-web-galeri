-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2025 at 02:25 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_galeri`
--

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE `album` (
  `AlbumID` int(11) NOT NULL,
  `NamaAlbum` varchar(255) NOT NULL,
  `Deskripsi` text NOT NULL,
  `TanggalDibuat` date NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `album`
--

INSERT INTO `album` (`AlbumID`, `NamaAlbum`, `Deskripsi`, `TanggalDibuat`, `UserID`) VALUES
(8, 'Manusia', 'Tempat para manusia', '2025-01-15', 10),
(9, 'Kucing', 'Ini bunga', '2025-01-20', 10),
(10, 'Tanaman', 'Ini untuk tanaman', '2025-01-20', 12);

-- --------------------------------------------------------

--
-- Table structure for table `foto`
--

CREATE TABLE `foto` (
  `FotoID` int(11) NOT NULL,
  `JudulFoto` varchar(255) NOT NULL,
  `Deskripsi` text NOT NULL,
  `TanggalUnggah` date NOT NULL,
  `Gambar` varchar(255) NOT NULL,
  `Album_ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foto`
--

INSERT INTO `foto` (`FotoID`, `JudulFoto`, `Deskripsi`, `TanggalUnggah`, `Gambar`, `Album_ID`, `UserID`) VALUES
(16, 'Grim REaper', '123', '2025-01-15', 'download (3)_1737353394.jpg', 8, 10),
(18, 'Menyerah', 'Ampunn', '2025-01-20', 'blue archive meme.jpg', 8, 10),
(19, 'Potrait', 'ini foto potrait', '2025-01-20', 'ffd42228-8680-4cd6-af31-0568989f2e47.jpg', 8, 10),
(20, 'Shut up', 'Shut up', '2025-01-20', 'evil Mr munchkin man.jpg', 9, 10),
(21, 'Maung', 'maung', '2025-01-20', 'hanya maung.jpg', 9, 10),
(22, 'Gif', 'Gif', '2025-01-20', 'af562ab3-5229-4a86-8110-4a30ae346966.jpg', 8, 10),
(23, 'Kucing', 'kucing', '2025-01-20', 'Ryo.jpg', 8, 10),
(24, 'hahahha', 'kucing', '2025-01-20', 'afc79b70-25a9-4c23-a7e9-e13288d6a435.jpg', 8, 10),
(25, 'Mantap', 'kucing', '2025-01-20', 'Mantap.jpg', 9, 10),
(26, 'Tertawa tetapi terluka', 'Tertawalah', '2025-01-20', 'YANG PENTING STIKER.jpg', 8, 10),
(27, 'Pohon ', 'ini pohon', '2025-01-20', 'Sycamore Tree.jpg', 10, 12);

-- --------------------------------------------------------

--
-- Table structure for table `komentar`
--

CREATE TABLE `komentar` (
  `KomentarID` int(11) NOT NULL,
  `FotoID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `IsiKomentar` text NOT NULL,
  `TanggalKomentar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komentar`
--

INSERT INTO `komentar` (`KomentarID`, `FotoID`, `UserID`, `IsiKomentar`, `TanggalKomentar`) VALUES
(7, 12, 10, 'pembohong publik', '2025-01-15'),
(8, 12, 10, 'pembohong publik', '2025-01-15'),
(9, 12, 10, 'pembohong publik', '2025-01-15'),
(10, 12, 10, 'pembohong publik', '2025-01-15'),
(11, 12, 10, 'pembohong publik', '2025-01-15'),
(12, 12, 10, 'pembohong publik', '2025-01-15'),
(13, 12, 10, 'pembohong publik', '2025-01-15'),
(14, 12, 10, 'pembohong publik', '2025-01-15'),
(16, 12, 10, 'afa iyah', '2025-01-15'),
(17, 12, 10, 'afa iyah', '2025-01-15'),
(18, 12, 10, 'afa iyah', '2025-01-15'),
(19, 12, 10, 'afa iyah', '2025-01-15'),
(20, 12, 10, 'afa iyah', '2025-01-15'),
(21, 12, 10, 'afa iyah', '2025-01-15'),
(22, 12, 10, 'afa iyah', '2025-01-15'),
(23, 12, 10, 'afa iyah', '2025-01-15'),
(24, 12, 10, 'afa iyah', '2025-01-15'),
(25, 12, 10, 'afa iyah', '2025-01-15'),
(26, 12, 10, 'afa iyah', '2025-01-15'),
(27, 12, 10, 'afa iyah', '2025-01-15'),
(28, 12, 10, 'afa iyah', '2025-01-15'),
(29, 12, 10, 'afa iyah', '2025-01-15'),
(30, 12, 10, 'afa iyah', '2025-01-15'),
(31, 12, 10, 'afa iyah', '2025-01-15'),
(32, 12, 10, 'afa iyah', '2025-01-15'),
(33, 12, 10, 'afa iyah', '2025-01-15'),
(34, 12, 10, 'zjljsj', '2025-01-15'),
(35, 12, 10, 'zjljsj', '2025-01-15'),
(36, 12, 10, 'zjljsj', '2025-01-15'),
(37, 12, 10, 'sxmssssssss', '2025-01-15'),
(38, 12, 10, '123', '2025-01-15'),
(39, 12, 10, '123', '2025-01-15'),
(40, 12, 10, '123', '2025-01-15'),
(41, 12, 10, '123', '2025-01-15'),
(42, 12, 10, '123', '2025-01-15'),
(43, 12, 10, '123', '2025-01-15'),
(45, 16, 10, 'Hahah lucu lu', '2025-01-20'),
(46, 19, 10, 'Punya gweh', '2025-01-20'),
(47, 27, 11, 'hai pohon', '2025-01-20'),
(48, 27, 10, 'hai sapi', '2025-01-20'),
(49, 27, 11, 'kok gak bisa hapus komenmu min ayam', '2025-01-20'),
(50, 27, 10, 'ini perintah admini', '2025-01-20');

-- --------------------------------------------------------

--
-- Table structure for table `like`
--

CREATE TABLE `like` (
  `LikeID` int(11) NOT NULL,
  `FotoID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TanggalLike` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `like`
--

INSERT INTO `like` (`LikeID`, `FotoID`, `UserID`, `TanggalLike`) VALUES
(31, 16, 10, '2025-01-15'),
(32, 16, 10, '2025-01-20'),
(35, 19, 10, '2025-01-20'),
(41, 23, 10, '2025-01-20'),
(42, 26, 10, '2025-01-20'),
(44, 26, 12, '2025-01-20'),
(45, 25, 11, '2025-01-20'),
(46, 26, 11, '2025-01-20'),
(47, 27, 11, '2025-01-20'),
(48, 27, 10, '2025-01-20'),
(49, 20, 10, '2025-01-21');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `NamaLengkap` varchar(255) NOT NULL,
  `Alamat` text NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Username`, `Password`, `Email`, `NamaLengkap`, `Alamat`, `role`) VALUES
(4, 'user1', '$2y$10$QuiYB/PkwIIg/mN4G635LuOAbM4OouOSyHT5dOGIoZdswuVH0xrL6', '12345@gmail.com', 'user21', '12345', 'user'),
(6, 'admin', '$2y$10$4fv/b0kj4G7.XFy6R9fJFeTZOEZUbqPAEr6FXUfqiKZju/tGP9VzW', 'admin@example.com', 'Admin Galeri', 'Jl. Contoh No. 123', 'user'),
(10, 'ayam', '$2y$10$TepmXKbwUO8b2wYxGQoIcOmgVZ.f4d9UkY7A.vx12/yJ1oacwRkP2', 'pendu71@gmail.com', 'Ayam', 'Amba', 'admin'),
(11, 'sapi', '$2y$10$C6BPMH7zWkIFlxKTlrTJ.e8rTF6aEQVdL7FJx7/8NQ6msdlXOvgfC', 'Silvia@gmail.com', 'Sapi', 'Amba', 'user'),
(12, 'bebek', '$2y$10$WtF1grjeqlTY0LAz77GBu.gjydqNOZFA8A95olJSZa66qxTNY3.hi', 'rusdyngawi123@gmail.com', 'bebek', '', 'admin'),
(14, '꧁ℭ℟Åℤ¥༒₭ÏḼḼ℥℟꧂', '$2y$10$SZRvPfgZhlwp01s3pKEb1enn8TTWkQFLGWVnXjtft.abiS6dtDQVq', 'pendu77@gmail.com', 'Wawa Ganteng', '123', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`AlbumID`),
  ADD KEY `album_ibfk_1` (`UserID`);

--
-- Indexes for table `foto`
--
ALTER TABLE `foto`
  ADD PRIMARY KEY (`FotoID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `foto_ibfk_1` (`Album_ID`);

--
-- Indexes for table `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`KomentarID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `komentar_ibfk_1` (`FotoID`);

--
-- Indexes for table `like`
--
ALTER TABLE `like`
  ADD PRIMARY KEY (`LikeID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `like_ibfk_1` (`FotoID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `album`
--
ALTER TABLE `album`
  MODIFY `AlbumID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `foto`
--
ALTER TABLE `foto`
  MODIFY `FotoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `komentar`
--
ALTER TABLE `komentar`
  MODIFY `KomentarID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `like`
--
ALTER TABLE `like`
  MODIFY `LikeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `album_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `foto`
--
ALTER TABLE `foto`
  ADD CONSTRAINT `foto_ibfk_1` FOREIGN KEY (`Album_ID`) REFERENCES `album` (`AlbumID`) ON DELETE CASCADE,
  ADD CONSTRAINT `foto_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `komentar`
--
ALTER TABLE `komentar`
  ADD CONSTRAINT `komentar_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `like`
--
ALTER TABLE `like`
  ADD CONSTRAINT `like_ibfk_1` FOREIGN KEY (`FotoID`) REFERENCES `foto` (`FotoID`) ON DELETE CASCADE,
  ADD CONSTRAINT `like_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
