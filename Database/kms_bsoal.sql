-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Jun 2026 pada 09.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kms_bsoal`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `description`) VALUES
(1, 'MTK-U', 'Matematika (Umum)', NULL),
(2, 'MTK-P', 'Matematika (Peminatan)', NULL),
(3, 'FIS', 'Fisika', NULL),
(4, 'KIM', 'Kimia', NULL),
(5, 'BIO', 'Biologi', NULL),
(6, 'BIN', 'Bahasa Indonesia', NULL),
(7, 'BIG', 'Bahasa Inggris', NULL),
(12, 'SEJ-I', 'Sejarah Indonesia', NULL),
(13, 'SEJ-P', 'Sejarah (Peminatan)', NULL),
(14, 'GEO', 'Geografi', NULL),
(15, 'SOS', 'Sosiologi', NULL),
(16, 'EKO', 'Ekonomi', NULL),
(18, 'KRISTEN', 'Pendidikan Agama Kristen', NULL),
(19, 'PPKN', 'PPKn', NULL),
(20, 'SENI', 'Seni Budaya', NULL),
(21, 'PJOK', 'PJOK', NULL),
(22, 'PKWU', 'Prakarya dan Kewirausahaan', NULL),
(24, 'BK', 'Bimbingan Konseling (BK)', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `classes`
--

INSERT INTO `classes` (`id`, `name`) VALUES
(1, 'X'),
(2, 'XI'),
(3, 'XII');

-- --------------------------------------------------------

--
-- Struktur dari tabel `discussions`
--

CREATE TABLE `discussions` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_edited` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `discussions`
--

INSERT INTO `discussions` (`id`, `question_id`, `actor_id`, `comment`, `created_at`, `is_edited`) VALUES
(1, 1, 5, 'Mr., soal nomor 2 apakah tidak terlalu sulit untuk kelas 10 semester awal? Sepertinya angka logaritmanya terlalu besar.', '2026-06-14 09:38:33', 0),
(2, 1, 4, 'Bisa disederhanakan Ms. Rina, nanti saya revisi angka eksponennya agar tidak terlalu pecah dan sesuai dengan jam terbang siswa di minggu pertama.', '2026-06-14 09:38:33', 0),
(3, 6, 6, 'Ms. Mega, mungkin bisa ditambahkan gambar struktur molekul untuk soal nomor 1 agar siswa lebih mudah memvisualisasikannya.', '2026-06-14 09:38:33', 0),
(4, 6, 9, 'Ide bagus Mr. Adithya. Saya akan tambahkan strukturnya di versi revisi nanti sore.', '2026-06-14 09:38:33', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_edited` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `forum_replies`
--

INSERT INTO `forum_replies` (`id`, `topic_id`, `actor_id`, `message`, `created_at`, `is_edited`) VALUES
(1, 1, 4, 'Menurut saya, kita harus sepakat dulu soal taksonomi bloom yang akan dipakai, apakah minimal C4 untuk semua jenjang kelas?', '2026-06-14 09:38:33', 0),
(2, 1, 5, 'Setuju Ms. Dina, dan mungkin butuh simulasi atau bedah soal bersama minggu depan. Untuk kelas X mungkin komposisi HOTS nya 30% saja cukup.', '2026-06-14 09:38:33', 0),
(3, 1, 2, 'Baik, usulan Ms. Rina dan Ms. Dina akan diagendakan oleh bagian kurikulum pada rapat pleno minggu depan.', '2026-06-14 09:38:33', 0),
(4, 1, 10, 'Terima kasih, saya tunggu jadwal simulasi bedah soalnya. Mr. Adithya mungkin bisa share template yang biasa dipakai di MGMP.', '2026-06-14 09:38:33', 0),
(5, 2, 6, 'Rekan-rekan, belakangan ini saya melihat indikasi jawaban esai siswa sangat persis dengan pola AI. Bagaimana kita menyikapinya?', '2026-06-14 09:38:33', 0),
(6, 2, 8, 'Benar Mr. Adithya. Mungkin kita perlu mengubah model asesmen menjadi lebih berbasis proyek atau presentasi lisan.', '2026-06-14 09:38:33', 0),
(7, 2, 9, 'Saya setuju dengan Ms. Linda. Kita bisa kombinasikan dengan pertanyaan pemantik di kelas untuk validasi pemahaman mereka.', '2026-06-14 09:38:33', 0),
(8, 3, 7, 'Bapak Ibu, mohon ketersediaannya untuk mengecek irisan materi antara Sejarah dan Sosiologi agar tidak terjadi repetisi dalam penyusunan instrumen soal UTS.', '2026-06-14 09:38:33', 0),
(9, 3, 10, 'Siap Ms. Linda, saya akan cek kembali KD Sosiologi kelas XI yang beririsan dengan Sejarah.', '2026-06-14 09:38:33', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `kategori_forum` varchar(50) DEFAULT 'Umum',
  `actor_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `forum_topics`
--

INSERT INTO `forum_topics` (`id`, `title`, `kategori_forum`, `actor_id`, `created_at`) VALUES
(1, 'Standar Pembuatan Soal HOTS untuk Ujian Akhir Sekolah', 'Umum', 2, '2026-06-14 09:38:33'),
(2, 'Diskusi Evaluasi Penggunaan ChatGPT oleh Siswa', 'Umum', 6, '2026-06-14 09:38:33'),
(3, 'Penyelarasan Kisi-Kisi Lintas Mata Pelajaran', 'Umum', 7, '2026-06-14 09:38:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `identities`
--

CREATE TABLE `identities` (
  `id` int(11) NOT NULL,
  `actor_type` enum('STAFF','TEACHER') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `identities`
--

INSERT INTO `identities` (`id`, `actor_type`, `created_at`) VALUES
(1, 'STAFF', '2026-06-14 09:38:33'),
(2, 'STAFF', '2026-06-14 09:38:33'),
(3, 'STAFF', '2026-06-14 09:38:33'),
(4, 'TEACHER', '2026-06-14 09:38:33'),
(5, 'TEACHER', '2026-06-14 09:38:33'),
(6, 'TEACHER', '2026-06-14 09:38:33'),
(7, 'TEACHER', '2026-06-14 09:38:33'),
(8, 'TEACHER', '2026-06-14 09:38:33'),
(9, 'TEACHER', '2026-06-14 09:38:33'),
(10, 'TEACHER', '2026-06-14 09:38:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kms_explicit`
--

CREATE TABLE `kms_explicit` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('SOP','Template') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploader_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kms_explicit`
--

INSERT INTO `kms_explicit` (`id`, `title`, `type`, `file_path`, `uploader_id`, `created_at`, `is_archived`) VALUES
(1, 'Panduan Penulisan Soal Pilihan Ganda (SOP)', 'SOP', 'Panduan_Penulisan_Soal_Pilihan_Ganda.docx', 2, '2026-06-14 09:38:33', 0),
(2, 'Template Kisi-Kisi Penulisan Soal', 'Template', 'Template_Kisi_Kisi_Soal.docx', 2, '2026-06-14 09:38:33', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `action` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `logs`
--

INSERT INTO `logs` (`id`, `actor_id`, `action`, `ip_address`, `created_at`) VALUES
(1, 2, 'Menghapus mata pelajaran: Bahasa Arab (ARB)', '::1', '2026-04-14 07:43:54'),
(2, 2, 'Menghapus mata pelajaran: Antropologi (ANT)', '::1', '2026-04-14 07:43:58'),
(3, 2, 'Menghapus mata pelajaran: Bahasa Jepang (JPN)', '::1', '2026-04-14 07:44:02'),
(4, 2, 'Menghapus mata pelajaran: Bahasa Jerman (GER)', '::1', '2026-04-14 07:44:05'),
(5, 2, 'Menghapus mata pelajaran: Bahasa Mandarin (MAN)', '::1', '2026-04-14 07:44:08'),
(6, 2, 'Menghapus mata pelajaran: Informatika (INF)', '::1', '2026-04-14 07:44:14'),
(7, 4, 'Teacher login (PIN)', '::1', '2026-04-14 07:44:50'),
(8, 1, 'Staff login (Standard)', '::1', '2026-06-14 06:07:10'),
(9, 4, 'Teacher login (PIN)', '::1', '2026-06-14 06:09:08'),
(10, 1, 'Staff login (Standard)', '::1', '2026-06-14 06:24:19'),
(11, 4, 'Teacher login (PIN)', '::1', '2026-06-14 06:35:40'),
(12, 4, 'Membuat soal baru: Tugas Latihan Soal Matematika', '::1', '2026-06-14 06:37:53'),
(13, 4, 'Menambahkan komentar pada soal ID 1', '::1', '2026-06-14 06:38:42'),
(14, 2, 'Staff login (Standard)', '::1', '2026-06-14 06:41:38'),
(15, 2, 'Mengubah status soal ID 1 menjadi Verified', '::1', '2026-06-14 06:41:59'),
(16, 1, 'Staff login (Standard)', '::1', '2026-06-14 06:42:17'),
(17, 1, 'Mengarsipkan soal: Tugas Latihan Soal Matematika', '::1', '2026-06-14 06:53:42'),
(18, 1, 'Memulihkan soal dari arsip: Tugas Latihan Soal Matematika', '::1', '2026-06-14 06:59:52'),
(19, 1, 'Mengarsipkan soal: Tugas Latihan Soal Matematika', '::1', '2026-06-14 07:00:02'),
(20, 4, 'Teacher login (PIN)', '::1', '2026-06-14 07:01:41'),
(21, 4, 'Membuat soal baru: Template Tugas Matematika', '::1', '2026-06-14 07:26:32'),
(22, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 07:40:55'),
(23, 1, 'Staff login (Standard)', '::1', '2026-06-14 07:41:30'),
(24, 2, 'Staff login (Standard)', '::1', '2026-06-14 07:41:44'),
(25, 2, 'Mengubah status soal ID 2 menjadi Verified', '::1', '2026-06-14 07:41:55'),
(26, 4, 'Teacher login (PIN)', '::1', '2026-06-14 07:42:10'),
(27, 4, 'Memulai diskusi forum: Pemabhasan Jenis Soal di Mapel Sains', '::1', '2026-06-14 07:42:44'),
(28, 4, 'Membalas diskusi forum ID 1', '::1', '2026-06-14 07:44:09'),
(29, 4, 'Menambahkan komentar pada soal ID 2', '::1', '2026-06-14 07:45:51'),
(30, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 07:45:59'),
(31, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 07:46:42'),
(32, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 07:53:38'),
(33, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 07:54:25'),
(34, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 08:01:05'),
(35, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 08:10:14'),
(36, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 08:10:24'),
(37, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 08:10:33'),
(38, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 08:10:43'),
(39, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 08:10:58'),
(40, 4, 'Memulihkan soal dari arsip: Tugas Latihan Soal Matematika', '::1', '2026-06-14 08:11:17'),
(41, 4, 'Merevisi metadata soal: Template Tugas Matematika', '::1', '2026-06-14 08:11:35'),
(42, 4, 'Teacher login (PIN)', '::1', '2026-06-14 09:07:50'),
(43, 1, 'Staff login (Standard)', '::1', '2026-06-16 11:15:07'),
(44, 2, 'Staff login (Standard)', '::1', '2026-06-16 11:15:34'),
(45, 1, 'Staff login (Standard)', '::1', '2026-06-16 11:37:58'),
(46, 2, 'Staff login (Standard)', '::1', '2026-06-16 11:38:05'),
(47, 2, 'Mengubah status soal ID 2 menjadi Draft', '::1', '2026-06-16 11:38:25'),
(48, 6, 'Teacher login (PIN)', '::1', '2026-06-16 11:38:34'),
(49, 1, 'Staff login (Standard)', '::1', '2026-06-16 11:38:52'),
(50, 4, 'Teacher login (PIN)', '::1', '2026-06-16 11:39:12'),
(51, 5, 'Teacher login (PIN)', '::1', '2026-06-16 11:40:29'),
(52, 5, 'Mengubah status soal ID 2 menjadi Review', '::1', '2026-06-16 11:40:43'),
(53, 2, 'Staff login (Standard)', '::1', '2026-06-16 11:40:55'),
(54, 2, 'Mengubah status soal ID 2 menjadi Draft', '::1', '2026-06-16 11:49:21'),
(55, 2, 'Staff login (Standard)', '::1', '2026-06-16 11:49:30'),
(56, 5, 'Teacher login (PIN)', '::1', '2026-06-16 11:51:03'),
(57, 5, 'Mengubah status soal ID 2 menjadi Review', '::1', '2026-06-16 11:56:59'),
(58, 2, 'Staff login (Standard)', '::1', '2026-06-16 11:57:06'),
(59, 5, 'Teacher login (PIN)', '::1', '2026-06-16 11:58:25'),
(60, 1, 'Staff login (Standard)', '::1', '2026-06-16 12:02:22'),
(61, 3, 'Staff login (Standard)', '::1', '2026-06-16 12:03:13'),
(62, 1, 'Staff login (Standard)', '::1', '2026-06-16 12:03:57'),
(63, 1, 'Staff login (Standard)', '::1', '2026-06-16 12:05:23'),
(64, 5, 'Teacher login (PIN)', '::1', '2026-06-16 12:06:14'),
(65, 2, 'Staff login (Standard)', '::1', '2026-06-16 12:06:20'),
(66, 3, 'Staff login (Standard)', '::1', '2026-06-16 12:07:06'),
(67, 1, 'Staff login (Standard)', '::1', '2026-06-16 12:07:17'),
(68, 1, 'Staff login (Standard)', '::1', '2026-06-16 12:11:13'),
(69, 2, 'Staff login (Standard)', '::1', '2026-06-16 12:11:23'),
(70, 3, 'Staff login (Standard)', '::1', '2026-06-16 12:11:35'),
(71, 1, 'Staff login (Standard)', '::1', '2026-06-16 12:16:44'),
(72, 5, 'Teacher login (PIN)', '::1', '2026-06-16 12:16:58'),
(73, 1, 'Staff login (Standard)', '::1', '2026-06-16 12:23:23'),
(74, 3, 'Staff login (Standard)', '::1', '2026-06-16 12:23:30'),
(75, 6, 'Teacher login (PIN)', '::1', '2026-06-17 07:22:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `actor_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 2, 'Soal baru \'Tugas Latihan Soal Matematika\' butuh review Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 06:37:53'),
(2, 4, 'Status soal \'Tugas Latihan Soal Matematika\' diperbarui menjadi Verified.', 'view-soal.php?id=1', 1, '2026-06-14 06:41:58'),
(3, 2, 'Soal baru \'Template Tugas Matematika\' butuh review Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 07:26:32'),
(4, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 07:40:55'),
(5, 4, 'Status soal \'Template Tugas Matematika\' diperbarui menjadi Verified.', 'view-soal.php?id=2', 1, '2026-06-14 07:41:55'),
(6, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 07:45:59'),
(7, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 07:46:42'),
(8, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 07:53:38'),
(9, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 07:54:25'),
(10, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 08:01:05'),
(11, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 08:10:14'),
(12, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 08:10:24'),
(13, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 08:10:33'),
(14, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 08:10:43'),
(15, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 08:10:58'),
(16, 2, 'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.', 'bank-soal.php?status=Review', 1, '2026-06-14 08:11:35'),
(17, 5, 'Status soal \'Soal Fisika XI - Dinamika Partikel\' diperbarui menjadi Draft.', 'view-soal.php?id=2', 1, '2026-06-16 11:38:25'),
(18, 5, 'Status soal \'Soal Fisika XI - Dinamika Partikel\' diperbarui menjadi Draft.', 'view-soal.php?id=2', 1, '2026-06-16 11:49:21'),
(19, 2, 'Soal \'Soal Fisika XI - Dinamika Partikel\' diajukan untuk direview.', 'view-soal.php?id=2', 1, '2026-06-16 11:56:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `materi` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `explanation` longtext NOT NULL,
  `difficulty` enum('Mudah','Sedang','Sulit') DEFAULT 'Sedang',
  `tingkat_kognitif` enum('C1 - Mengingat','C2 - Memahami','C3 - Mengaplikasikan','C4 - Menganalisis','C5 - Mengevaluasi','C6 - Mencipta') DEFAULT 'C2 - Memahami',
  `jenis_soal` enum('Pilihan Ganda','Essay','Isian Singkat','Praktikum','Lainnya') DEFAULT 'Pilihan Ganda',
  `tags` varchar(255) DEFAULT NULL,
  `status` enum('Draft','Review','Verified') DEFAULT 'Draft',
  `is_archived` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `download_count` int(11) DEFAULT 0,
  `uploader_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `questions`
--

INSERT INTO `questions` (`id`, `title`, `class_id`, `category_id`, `materi`, `content`, `file_path`, `original_name`, `file_type`, `explanation`, `difficulty`, `tingkat_kognitif`, `jenis_soal`, `tags`, `status`, `is_archived`, `view_count`, `download_count`, `uploader_id`, `created_at`, `updated_at`) VALUES
(1, 'Soal Matematika Umum X - Eksponen', 1, 1, 'Eksponen dan Logaritma', NULL, 'Soal_Matematika_Umum_X.docx', 'Soal_Matematika_Umum_X.docx', 'Word', '<p><strong>Pembahasan Lengkap:</strong></p><p>Untuk menyelesaikan soal nomor 1, kita gunakan sifat dasar eksponen: a<sup>m</sup> / a<sup>n</sup> = a<sup>m-n</sup>. Jadi (a^3 b^-2 c) / (a b^-4 c^2) = a^(3-1) b^(-2-(-4)) c^(1-2) = a^2 b^2 c^-1.</p><p>Perhatikan bahwa banyak siswa terkecoh pada tanda negatif b, sehingga perlu penekanan ekstra saat mengajar.</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'PTS,SemesterGanjil', 'Verified', 0, 0, 1, 4, '2026-06-14 09:38:33', '2026-06-16 11:53:53'),
(2, 'Soal Fisika XI - Dinamika Partikel', 2, 3, 'Hukum Newton', NULL, 'Soal_Fisika_XI_Dinamika.docx', 'Soal_Fisika_XI_Dinamika.docx', 'Word', '<p><strong>Kunci Jawaban & Panduan Menilai:</strong></p><p>Pada soal gaya gesek kinetis, perhatikan gaya normalnya terlebih dahulu sebelum menghitung gesekan. N = W = m*g = 10 * 10 = 100 N.</p><p>F_gesek_max = 0.4 * 100 = 40 N. Karena F_tarik (50 N) > F_gesek_max (40 N), balok bergerak. Maka gaya gesek yang bekerja adalah gaya gesek kinetis = 0.2 * 100 = 20 N.</p><p>Jawaban yang benar adalah A.</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'HOTS,Dinamika', 'Review', 0, 0, 0, 5, '2026-06-14 09:38:33', '2026-06-16 11:56:59'),
(3, 'Soal Biologi XII - Metabolisme Sel', 3, 5, 'Anabolisme & Katabolisme', NULL, 'Soal_Biologi_XII_Sel.docx', 'Soal_Biologi_XII_Sel.docx', 'Word', '<p><strong>Analisis Soal:</strong></p><p>1. Faktor yang memengaruhi enzim: Suhu, pH, Konsentrasi, Inhibitor. Warna substrat tidak memengaruhi. Jawaban: D.</p><p>2. Reaksi terang terjadi di Grana. Reaksi gelap di Stroma. Jawaban: B.</p><p>3. Glikolisis menghasilkan 2 ATP, 2 Asam Piruvat, 2 NADH. Jawaban: A.</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'UAS,Biologi', 'Verified', 0, 0, 0, 6, '2026-06-14 09:38:33', '2026-06-14 09:38:33'),
(4, 'Soal Sejarah XI - Kemerdekaan', 2, 12, 'Proklamasi Kemerdekaan', NULL, 'Soal_Sejarah_XI_Kemerdekaan.docx', 'Soal_Sejarah_XI_Kemerdekaan.docx', 'Word', '<p><strong>Kunci Jawaban Esai:</strong></p><p>1. Rengasdengklok mendesak Soekarno-Hatta untuk segera memproklamasikan kemerdekaan agar tidak terpengaruh janji Jepang.</p><p>2. Perjanjian Linggarjati mengakui kekuasaan RI secara de facto atas Jawa, Madura, dan Sumatera.</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'SejarahWajib,Kemerdekaan', 'Verified', 0, 0, 0, 7, '2026-06-14 09:38:33', '2026-06-14 09:38:33'),
(5, 'Soal Bahasa Inggris X - Narrative', 1, 7, 'Narrative Text', NULL, 'Soal_Bahasa_Inggris_X_Narrative.docx', 'Soal_Bahasa_Inggris_X_Narrative.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Soal nomor 1 mencari <em>Moral Value</em>. Jawabannya tersirat di paragraf terakhir. Soal nomor 2 adalah mencari <em>Main Character</em>, yang dijelaskan di awal paragraf (Poor Widow).</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'Reading,Narrative', 'Draft', 0, 0, 0, 8, '2026-06-14 09:38:33', '2026-06-14 09:38:33'),
(6, 'Soal Kimia XI - Hidrokarbon', 2, 4, 'Senyawa Hidrokarbon', NULL, 'Soal_Kimia_XI_Hidrokarbon.docx', 'Soal_Kimia_XI_Hidrokarbon.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Deret homolog alkana: CnH2n+2. Jika n=5, maka senyawanya adalah C5H12 yang disebut Pentana. Jawaban: D.</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'Hidrokarbon,Kimia', 'Verified', 0, 0, 0, 9, '2026-06-14 09:38:33', '2026-06-14 09:38:33'),
(7, 'Soal Sosiologi XII - Perubahan', 3, 15, 'Perubahan Sosial', NULL, 'Soal_Sosiologi_XII_Perubahan.docx', 'Soal_Sosiologi_XII_Perubahan.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Faktor eksternal berasal dari luar masyarakat, contohnya pengaruh kebudayaan lain, peperangan, dan bencana alam. Penemuan baru dan konflik termasuk internal. Jawaban: C.</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'Sosiologi,PerubahanSosial', 'Review', 0, 0, 0, 10, '2026-06-14 09:38:33', '2026-06-14 09:38:33'),
(8, 'Soal Ekonomi XI - Pajak', 2, 16, 'Perpajakan', NULL, 'Soal_Ekonomi_XI_Pajak.docx', 'Soal_Ekonomi_XI_Pajak.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Pajak yang dipungut oleh pemerintah daerah disebut Pajak Daerah (contoh: pajak kendaraan, restoran). Jawaban: B.</p>', 'Sedang', 'C2 - Memahami', 'Pilihan Ganda', 'Ekonomi,Pajak', 'Verified', 0, 0, 0, 4, '2026-06-14 09:38:33', '2026-06-14 09:38:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `question_status_logs`
--

CREATE TABLE `question_status_logs` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `question_status_logs`
--

INSERT INTO `question_status_logs` (`id`, `question_id`, `actor_id`, `old_status`, `new_status`, `notes`, `created_at`) VALUES
(1, 1, 4, NULL, 'Draft', 'Soal pertama kali dibuat sebagai Draft', '2026-06-14 06:37:53'),
(2, 1, 2, 'Draft', 'Verified', 'Sudah Benar', '2026-06-14 06:41:58'),
(3, 2, 4, NULL, 'Draft', 'Soal pertama kali dibuat sebagai Draft', '2026-06-14 07:26:32'),
(4, 2, 4, 'Draft', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 07:40:55'),
(5, 2, 2, 'Review', 'Verified', '', '2026-06-14 07:41:55'),
(6, 2, 4, 'Verified', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 07:45:59'),
(7, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 07:46:42'),
(8, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 07:53:38'),
(9, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 07:54:25'),
(10, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 08:01:05'),
(11, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 08:10:14'),
(12, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 08:10:24'),
(13, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 08:10:33'),
(14, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 08:10:43'),
(15, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 08:10:58'),
(16, 2, 4, 'Review', 'Review', 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.', '2026-06-14 08:11:35'),
(17, 2, 2, 'Review', 'Draft', 'coab sesuaikan lagi', '2026-06-16 11:38:25'),
(18, 2, 5, 'Draft', 'Review', '', '2026-06-16 11:40:43'),
(19, 2, 2, 'Review', 'Draft', 'coba review lagi', '2026-06-16 11:49:21'),
(20, 2, 5, 'Draft', 'Review', '', '2026-06-16 11:56:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(2, 'Admin Akademik / Administrasi'),
(1, 'Admin Sistem'),
(4, 'Kepala Sekolah');

-- --------------------------------------------------------

--
-- Struktur dari tabel `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `identity_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `staff`
--

INSERT INTO `staff` (`id`, `identity_id`, `username`, `password`, `full_name`, `role_id`, `created_at`) VALUES
(1, 1, 'admin', 'admin123', 'Superadmin Sistem', 1, '2026-04-14 07:43:29'),
(2, 2, 'administrasi', 'admin123', 'Admin Akademik (Administrasi)', 2, '2026-04-14 07:43:29'),
(3, 3, 'kepsek', 'admin123', 'Drs. Yohanes Darsono (Kepsek)', 4, '2026-04-14 07:43:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `identity_id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `pin` varchar(10) DEFAULT '123456',
  `is_archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `teachers`
--

INSERT INTO `teachers` (`id`, `identity_id`, `nip`, `full_name`, `pin`, `created_at`) VALUES
(1, 4, '19800101201001', 'Dina Sisilia, S.Pd', '123456', '2026-06-14 09:38:33'),
(2, 5, '19810202201002', 'Rina Natalia, M.Pd', '123456', '2026-06-14 09:38:33'),
(3, 6, '19820303201003', 'Adithya Kusuma, S.Si', '123456', '2026-06-14 09:38:33'),
(4, 7, '19830404201004', 'Linda Lusiana, S.Pd', '123456', '2026-06-14 09:38:33'),
(5, 8, '19840505201005', 'Cicilia Dewi Andriani, M.Pd', '123456', '2026-06-14 09:38:33'),
(6, 9, '19850606201006', 'Mega Devinta, S.Pd', '123456', '2026-06-14 09:38:33'),
(7, 10, '19860707201007', 'Indayan Budi, S.Pd', '123456', '2026-06-14 09:38:33');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks untuk tabel `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indeks untuk tabel `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indeks untuk tabel `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indeks untuk tabel `identities`
--
ALTER TABLE `identities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kms_explicit`
--
ALTER TABLE `kms_explicit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploader_id` (`uploader_id`);

--
-- Indeks untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indeks untuk tabel `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `uploader_id` (`uploader_id`);

--
-- Indeks untuk tabel `question_status_logs`
--
ALTER TABLE `question_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `actor_id` (`actor_id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indeks untuk tabel `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `identity_id` (`identity_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indeks untuk tabel `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `identity_id` (`identity_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `identities`
--
ALTER TABLE `identities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `kms_explicit`
--
ALTER TABLE `kms_explicit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `question_status_logs`
--
ALTER TABLE `question_status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `discussions`
--
ALTER TABLE `discussions`
  ADD CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussions_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD CONSTRAINT `forum_topics_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `kms_explicit`
--
ALTER TABLE `kms_explicit`
  ADD CONSTRAINT `kms_explicit_ibfk_1` FOREIGN KEY (`uploader_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `questions_ibfk_3` FOREIGN KEY (`uploader_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `question_status_logs`
--
ALTER TABLE `question_status_logs`
  ADD CONSTRAINT `question_status_logs_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_status_logs_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
