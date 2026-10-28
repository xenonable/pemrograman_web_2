-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for calon_mhs
CREATE DATABASE IF NOT EXISTS `calon_mhs` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `calon_mhs`;

-- Dumping structure for table calon_mhs.calon_mhs
CREATE TABLE IF NOT EXISTS `calon_mhs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ttl` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asal_sekolah` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pekerjaan_ortu` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matematika` decimal(5,2) NOT NULL,
  `inggris` decimal(5,2) NOT NULL,
  `umum` decimal(5,2) NOT NULL,
  `rata_rata` decimal(5,2) NOT NULL,
  `keterangan` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table calon_mhs.calon_mhs: ~2 rows (approximately)
INSERT INTO `calon_mhs` (`id`, `kode`, `nama`, `jenis_kelamin`, `ttl`, `asal_sekolah`, `pekerjaan_ortu`, `matematika`, `inggris`, `umum`, `rata_rata`, `keterangan`, `created_at`) VALUES
	(1, 'A3', 'tanz', 'Laki-Laki', 'jakarta, 2026-10-16', 'unpam', 'nguli', 100.00, 100.00, 100.00, 100.00, 'Lulus', '2026-10-28 12:55:00'),
	(2, 'A001', 'sulthan saizul ramdan', 'Laki-Laki', 'jakarta, 2026-10-30', 'dewantoro', 'buruh', 100.00, 100.00, 99.32, 99.77, 'Lulus', '2026-10-28 12:55:49');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
