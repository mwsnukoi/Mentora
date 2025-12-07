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


-- Dumping database structure for db_elearning
CREATE DATABASE IF NOT EXISTS `db_elearning` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_elearning`;

-- Dumping data for table db_elearning.kelas: ~2 rows (approximately)
INSERT INTO `kelas` (`id`, `guru_id`, `nama_kelas`, `deskripsi`, `kode_kelas`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
	(1, 2, 'Pemrograman Web', 'Ayo membuat website yang sederhana namun tetap responsive', '76EF6', 'Senin', '08:00:00', '00:00:00'),
	(2, 2, 'English', 'Ayo belajar bahasa inggris dengan menyenangkan.', '95B52', 'Selasa', '10:43:00', '10:45:00');

-- Dumping data for table db_elearning.kelas_siswa: ~1 rows (approximately)
INSERT INTO `kelas_siswa` (`id`, `kelas_id`, `siswa_id`) VALUES
	(1, 1, 3);

-- Dumping data for table db_elearning.komentar: ~3 rows (approximately)
INSERT INTO `komentar` (`id`, `materi_id`, `user_id`, `isi_komentar`, `tanggal`) VALUES
	(1, 1, 2, 'Ketik kembali kode ini, lalu pahami kode tersebut. Minggu depan akan saya tanya!', '2025-12-06 12:43:47'),
	(2, 2, 2, 'Silahkan nonton dan simak vidio ini.', '2025-12-06 13:02:28'),
	(3, 1, 3, 'Baik pakk', '2025-12-06 13:13:07');

-- Dumping data for table db_elearning.materi: ~1 rows (approximately)
INSERT INTO `materi` (`id`, `kelas_id`, `judul`, `bab`, `tipe`, `file_content`, `deskripsi`, `tanggal`) VALUES
	(1, 1, 'CODING', 'Modul Pembelajaran', 'teks', '<?php\r\n$host = "localhost";\r\n$user = "root";\r\n$pass = "";        // Laragon defaultnya kosong\r\n$db   = "db_elearning";\r\n\r\n$conn = mysqli_connect($host, $user, $pass, $db);\r\n\r\nif (!$conn) {\r\n    die("Koneksi gagal: " . mysqli_connect_error());\r\n}\r\n?>', 'Coding is fun! Setiap kelompok harus membuat website yang menarik.', '2025-12-06 07:30:40'),
	(2, 2, 'Belajar Bahasa inggris', 'Modul Pembelajaran', 'youtube', 'https://youtu.be/Q77lp_uIT7A?si=2HVQqMKq1xiVKgaF', 'Ayo belajar bahasa baru hari ini. Silahkan nonton vidio berikut ini!', '2025-12-06 13:01:12');

-- Dumping data for table db_elearning.nilai_quiz: ~1 rows (approximately)
INSERT INTO `nilai_quiz` (`id`, `quiz_id`, `siswa_id`, `skor`, `tanggal_kerja`) VALUES
	(1, 1, 3, 50, '2025-12-06 13:14:43');

-- Dumping data for table db_elearning.pengumuman: ~2 rows (approximately)
INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `tipe`, `foto`, `penulis_id`, `tanggal`) VALUES
	(1, 'Ayo Masuk Di Prodi TRPL!', 'ayo segera bergabung dengan kami di TRPL!', 'umum', '', 1, '2025-12-06 07:26:15'),
	(2, 'Oasis Menduniaa..', 'oasis merupakan band terbaik di dunia, dan tercatat hampir seluruh populasi manusia pernah mendengar band bernama oasis ini.', 'umum', '1765006040_oasis2.jpg', 1, '2025-12-06 07:27:20');

-- Dumping data for table db_elearning.quiz: ~1 rows (approximately)
INSERT INTO `quiz` (`id`, `kelas_id`, `judul`, `deskripsi`, `tanggal_buat`) VALUES
	(1, 1, 'Tes', 'Di isi dengan teliti..', '2025-12-06 12:45:17');

-- Dumping data for table db_elearning.soal: ~2 rows (approximately)
INSERT INTO `soal` (`id`, `quiz_id`, `pertanyaan`, `opsi_a`, `opsi_b`, `opsi_c`, `opsi_d`, `kunci_jawaban`) VALUES
	(1, 1, 'Apa Kepanjangan HTML?', 'Hyper text markup languange', 'HTML AJA', 'HTMLLLLLLL', 'HTML.', 'A'),
	(2, 1, 'Apa fungsi vscode?', 'Menulis kode', 'Tempat mengarang', 'Coding (Pakai AI)', 'Tempat Curhat', 'C');

-- Dumping data for table db_elearning.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `foto`, `email`, `no_hp`) VALUES
	(1, 'admin', 'admin123', 'Administrator', 'admin', 'default.png', NULL, NULL),
	(2, 'guru', 'guru123', 'Bapak Guru Budi', 'guru', 'default.png', NULL, NULL),
	(3, 'siswa', 'siswa123', 'Andi Siswa', 'siswa', 'default.png', NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
