-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: kms_bsoal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'MTK-U','Matematika (Umum)',NULL),(2,'MTK-P','Matematika (Peminatan)',NULL),(3,'FIS','Fisika',NULL),(4,'KIM','Kimia',NULL),(5,'BIO','Biologi',NULL),(6,'BIN','Bahasa Indonesia',NULL),(7,'BIG','Bahasa Inggris',NULL),(12,'SEJ-I','Sejarah Indonesia',NULL),(13,'SEJ-P','Sejarah (Peminatan)',NULL),(14,'GEO','Geografi',NULL),(15,'SOS','Sosiologi',NULL),(16,'EKO','Ekonomi',NULL),(18,'KRISTEN','Pendidikan Agama Kristen',NULL),(19,'PPKN','PPKn',NULL),(20,'SENI','Seni Budaya',NULL),(21,'PJOK','PJOK',NULL),(22,'PKWU','Prakarya dan Kewirausahaan',NULL),(24,'BK','Bimbingan Konseling (BK)',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,'X'),(2,'XI'),(3,'XII');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discussions`
--

DROP TABLE IF EXISTS `discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discussions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_edited` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `discussions_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discussions_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discussions`
--

LOCK TABLES `discussions` WRITE;
/*!40000 ALTER TABLE `discussions` DISABLE KEYS */;
INSERT INTO `discussions` VALUES (1,1,5,'Mr., soal nomor 2 apakah tidak terlalu sulit untuk kelas 10 semester awal? Sepertinya angka logaritmanya terlalu besar.','2026-06-14 09:38:33',0),(2,1,4,'Bisa disederhanakan Ms. Rina, nanti saya revisi angka eksponennya agar tidak terlalu pecah dan sesuai dengan jam terbang siswa di minggu pertama.','2026-06-14 09:38:33',0),(3,6,6,'Ms. Mega, mungkin bisa ditambahkan gambar struktur molekul untuk soal nomor 1 agar siswa lebih mudah memvisualisasikannya.','2026-06-14 09:38:33',0),(4,6,9,'Ide bagus Mr. Adithya. Saya akan tambahkan strukturnya di versi revisi nanti sore.','2026-06-14 09:38:33',0);
/*!40000 ALTER TABLE `discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_replies`
--

DROP TABLE IF EXISTS `forum_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_edited` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `forum_replies_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_replies_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_replies`
--

LOCK TABLES `forum_replies` WRITE;
/*!40000 ALTER TABLE `forum_replies` DISABLE KEYS */;
INSERT INTO `forum_replies` VALUES (1,1,4,'Menurut saya, kita harus sepakat dulu soal taksonomi bloom yang akan dipakai, apakah minimal C4 untuk semua jenjang kelas?','2026-06-14 09:38:33',0),(2,1,5,'Setuju Ms. Dina, dan mungkin butuh simulasi atau bedah soal bersama minggu depan. Untuk kelas X mungkin komposisi HOTS nya 30% saja cukup.','2026-06-14 09:38:33',0),(3,1,2,'Baik, usulan Ms. Rina dan Ms. Dina akan diagendakan oleh bagian kurikulum pada rapat pleno minggu depan.','2026-06-14 09:38:33',0),(4,1,10,'Terima kasih, saya tunggu jadwal simulasi bedah soalnya. Mr. Adithya mungkin bisa share template yang biasa dipakai di MGMP.','2026-06-14 09:38:33',0),(5,2,6,'Rekan-rekan, belakangan ini saya melihat indikasi jawaban esai siswa sangat persis dengan pola AI. Bagaimana kita menyikapinya?','2026-06-14 09:38:33',0),(6,2,8,'Benar Mr. Adithya. Mungkin kita perlu mengubah model asesmen menjadi lebih berbasis proyek atau presentasi lisan.','2026-06-14 09:38:33',0),(7,2,9,'Saya setuju dengan Ms. Linda. Kita bisa kombinasikan dengan pertanyaan pemantik di kelas untuk validasi pemahaman mereka.','2026-06-14 09:38:33',0),(8,3,7,'Bapak Ibu, mohon ketersediaannya untuk mengecek irisan materi antara Sejarah dan Sosiologi agar tidak terjadi repetisi dalam penyusunan instrumen soal UTS.','2026-06-14 09:38:33',0),(9,3,10,'Siap Ms. Linda, saya akan cek kembali KD Sosiologi kelas XI yang beririsan dengan Sejarah.','2026-06-14 09:38:33',0);
/*!40000 ALTER TABLE `forum_replies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum_topics`
--

DROP TABLE IF EXISTS `forum_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `kategori_forum` varchar(50) DEFAULT 'Umum',
  `actor_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `forum_topics_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum_topics`
--

LOCK TABLES `forum_topics` WRITE;
/*!40000 ALTER TABLE `forum_topics` DISABLE KEYS */;
INSERT INTO `forum_topics` VALUES (1,'Standar Pembuatan Soal HOTS untuk Ujian Akhir Sekolah','Umum',2,'2026-06-14 09:38:33'),(2,'Diskusi Evaluasi Penggunaan ChatGPT oleh Siswa','Umum',6,'2026-06-14 09:38:33'),(3,'Penyelarasan Kisi-Kisi Lintas Mata Pelajaran','Umum',7,'2026-06-14 09:38:33');
/*!40000 ALTER TABLE `forum_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `identities`
--

DROP TABLE IF EXISTS `identities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `identities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_type` enum('STAFF','TEACHER') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `identities`
--

LOCK TABLES `identities` WRITE;
/*!40000 ALTER TABLE `identities` DISABLE KEYS */;
INSERT INTO `identities` VALUES (1,'STAFF','2026-06-14 09:38:33'),(2,'STAFF','2026-06-14 09:38:33'),(3,'STAFF','2026-06-14 09:38:33'),(4,'TEACHER','2026-06-14 09:38:33'),(5,'TEACHER','2026-06-14 09:38:33'),(6,'TEACHER','2026-06-14 09:38:33'),(7,'TEACHER','2026-06-14 09:38:33'),(8,'TEACHER','2026-06-14 09:38:33'),(9,'TEACHER','2026-06-14 09:38:33'),(10,'TEACHER','2026-06-14 09:38:33');
/*!40000 ALTER TABLE `identities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kms_explicit`
--

DROP TABLE IF EXISTS `kms_explicit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kms_explicit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` enum('SOP','Template') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploader_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploader_id` (`uploader_id`),
  CONSTRAINT `kms_explicit_ibfk_1` FOREIGN KEY (`uploader_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kms_explicit`
--

LOCK TABLES `kms_explicit` WRITE;
/*!40000 ALTER TABLE `kms_explicit` DISABLE KEYS */;
INSERT INTO `kms_explicit` VALUES (1,'Panduan Penulisan Soal Pilihan Ganda (SOP)','SOP','Panduan_Penulisan_Soal_Pilihan_Ganda.docx',2,'2026-06-14 09:38:33'),(2,'Template Kisi-Kisi Penulisan Soal','Template','Template_Kisi_Kisi_Soal.docx',2,'2026-06-14 09:38:33');
/*!40000 ALTER TABLE `kms_explicit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_id` int(11) DEFAULT NULL,
  `action` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (1,2,'Menghapus mata pelajaran: Bahasa Arab (ARB)','::1','2026-04-14 07:43:54'),(2,2,'Menghapus mata pelajaran: Antropologi (ANT)','::1','2026-04-14 07:43:58'),(3,2,'Menghapus mata pelajaran: Bahasa Jepang (JPN)','::1','2026-04-14 07:44:02'),(4,2,'Menghapus mata pelajaran: Bahasa Jerman (GER)','::1','2026-04-14 07:44:05'),(5,2,'Menghapus mata pelajaran: Bahasa Mandarin (MAN)','::1','2026-04-14 07:44:08'),(6,2,'Menghapus mata pelajaran: Informatika (INF)','::1','2026-04-14 07:44:14'),(7,4,'Teacher login (PIN)','::1','2026-04-14 07:44:50'),(8,1,'Staff login (Standard)','::1','2026-06-14 06:07:10'),(9,4,'Teacher login (PIN)','::1','2026-06-14 06:09:08'),(10,1,'Staff login (Standard)','::1','2026-06-14 06:24:19'),(11,4,'Teacher login (PIN)','::1','2026-06-14 06:35:40'),(12,4,'Membuat soal baru: Tugas Latihan Soal Matematika','::1','2026-06-14 06:37:53'),(13,4,'Menambahkan komentar pada soal ID 1','::1','2026-06-14 06:38:42'),(14,2,'Staff login (Standard)','::1','2026-06-14 06:41:38'),(15,2,'Mengubah status soal ID 1 menjadi Verified','::1','2026-06-14 06:41:59'),(16,1,'Staff login (Standard)','::1','2026-06-14 06:42:17'),(17,1,'Mengarsipkan soal: Tugas Latihan Soal Matematika','::1','2026-06-14 06:53:42'),(18,1,'Memulihkan soal dari arsip: Tugas Latihan Soal Matematika','::1','2026-06-14 06:59:52'),(19,1,'Mengarsipkan soal: Tugas Latihan Soal Matematika','::1','2026-06-14 07:00:02'),(20,4,'Teacher login (PIN)','::1','2026-06-14 07:01:41'),(21,4,'Membuat soal baru: Template Tugas Matematika','::1','2026-06-14 07:26:32'),(22,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 07:40:55'),(23,1,'Staff login (Standard)','::1','2026-06-14 07:41:30'),(24,2,'Staff login (Standard)','::1','2026-06-14 07:41:44'),(25,2,'Mengubah status soal ID 2 menjadi Verified','::1','2026-06-14 07:41:55'),(26,4,'Teacher login (PIN)','::1','2026-06-14 07:42:10'),(27,4,'Memulai diskusi forum: Pemabhasan Jenis Soal di Mapel Sains','::1','2026-06-14 07:42:44'),(28,4,'Membalas diskusi forum ID 1','::1','2026-06-14 07:44:09'),(29,4,'Menambahkan komentar pada soal ID 2','::1','2026-06-14 07:45:51'),(30,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 07:45:59'),(31,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 07:46:42'),(32,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 07:53:38'),(33,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 07:54:25'),(34,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 08:01:05'),(35,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 08:10:14'),(36,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 08:10:24'),(37,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 08:10:33'),(38,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 08:10:43'),(39,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 08:10:58'),(40,4,'Memulihkan soal dari arsip: Tugas Latihan Soal Matematika','::1','2026-06-14 08:11:17'),(41,4,'Merevisi metadata soal: Template Tugas Matematika','::1','2026-06-14 08:11:35'),(42,4,'Teacher login (PIN)','::1','2026-06-14 09:07:50');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,2,'Soal baru \'Tugas Latihan Soal Matematika\' butuh review Anda.','bank-soal.php?status=Review',1,'2026-06-14 06:37:53'),(2,4,'Status soal \'Tugas Latihan Soal Matematika\' diperbarui menjadi Verified.','view-soal.php?id=1',1,'2026-06-14 06:41:58'),(3,2,'Soal baru \'Template Tugas Matematika\' butuh review Anda.','bank-soal.php?status=Review',1,'2026-06-14 07:26:32'),(4,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',1,'2026-06-14 07:40:55'),(5,4,'Status soal \'Template Tugas Matematika\' diperbarui menjadi Verified.','view-soal.php?id=2',1,'2026-06-14 07:41:55'),(6,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 07:45:59'),(7,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 07:46:42'),(8,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 07:53:38'),(9,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 07:54:25'),(10,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 08:01:05'),(11,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 08:10:14'),(12,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 08:10:24'),(13,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 08:10:33'),(14,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 08:10:43'),(15,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 08:10:58'),(16,2,'Soal \'Template Tugas Matematika\' telah direvisi dan butuh review ulang Anda.','bank-soal.php?status=Review',0,'2026-06-14 08:11:35');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_status_logs`
--

DROP TABLE IF EXISTS `question_status_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_status_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) DEFAULT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `question_status_logs_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `question_status_logs_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_status_logs`
--

LOCK TABLES `question_status_logs` WRITE;
/*!40000 ALTER TABLE `question_status_logs` DISABLE KEYS */;
INSERT INTO `question_status_logs` VALUES (1,1,4,NULL,'Draft','Soal pertama kali dibuat sebagai Draft','2026-06-14 06:37:53'),(2,1,2,'Draft','Verified','Sudah Benar','2026-06-14 06:41:58'),(3,2,4,NULL,'Draft','Soal pertama kali dibuat sebagai Draft','2026-06-14 07:26:32'),(4,2,4,'Draft','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 07:40:55'),(5,2,2,'Review','Verified','','2026-06-14 07:41:55'),(6,2,4,'Verified','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 07:45:59'),(7,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 07:46:42'),(8,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 07:53:38'),(9,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 07:54:25'),(10,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 08:01:05'),(11,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 08:10:14'),(12,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 08:10:24'),(13,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 08:10:33'),(14,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 08:10:43'),(15,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 08:10:58'),(16,2,4,'Review','Review','Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.','2026-06-14 08:11:35');
/*!40000 ALTER TABLE `question_status_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  KEY `category_id` (`category_id`),
  KEY `uploader_id` (`uploader_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `questions_ibfk_3` FOREIGN KEY (`uploader_id`) REFERENCES `identities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,'Soal Matematika Umum X - Eksponen',1,1,'Eksponen dan Logaritma',NULL,'Soal_Matematika_Umum_X.docx','Soal_Matematika_Umum_X.docx','Word','<p><strong>Pembahasan Lengkap:</strong></p><p>Untuk menyelesaikan soal nomor 1, kita gunakan sifat dasar eksponen: a<sup>m</sup> / a<sup>n</sup> = a<sup>m-n</sup>. Jadi (a^3 b^-2 c) / (a b^-4 c^2) = a^(3-1) b^(-2-(-4)) c^(1-2) = a^2 b^2 c^-1.</p><p>Perhatikan bahwa banyak siswa terkecoh pada tanda negatif b, sehingga perlu penekanan ekstra saat mengajar.</p>','Sedang','C2 - Memahami','Pilihan Ganda','PTS,SemesterGanjil','Verified',0,0,0,4,'2026-06-14 09:38:33','2026-06-14 09:38:33'),(2,'Soal Fisika XI - Dinamika Partikel',2,3,'Hukum Newton',NULL,'Soal_Fisika_XI_Dinamika.docx','Soal_Fisika_XI_Dinamika.docx','Word','<p><strong>Kunci Jawaban & Panduan Menilai:</strong></p><p>Pada soal gaya gesek kinetis, perhatikan gaya normalnya terlebih dahulu sebelum menghitung gesekan. N = W = m*g = 10 * 10 = 100 N.</p><p>F_gesek_max = 0.4 * 100 = 40 N. Karena F_tarik (50 N) > F_gesek_max (40 N), balok bergerak. Maka gaya gesek yang bekerja adalah gaya gesek kinetis = 0.2 * 100 = 20 N.</p><p>Jawaban yang benar adalah A.</p>','Sedang','C2 - Memahami','Pilihan Ganda','HOTS,Dinamika','Review',0,0,0,5,'2026-06-14 09:38:33','2026-06-14 09:38:33'),(3,'Soal Biologi XII - Metabolisme Sel',3,5,'Anabolisme & Katabolisme',NULL,'Soal_Biologi_XII_Sel.docx','Soal_Biologi_XII_Sel.docx','Word','<p><strong>Analisis Soal:</strong></p><p>1. Faktor yang memengaruhi enzim: Suhu, pH, Konsentrasi, Inhibitor. Warna substrat tidak memengaruhi. Jawaban: D.</p><p>2. Reaksi terang terjadi di Grana. Reaksi gelap di Stroma. Jawaban: B.</p><p>3. Glikolisis menghasilkan 2 ATP, 2 Asam Piruvat, 2 NADH. Jawaban: A.</p>','Sedang','C2 - Memahami','Pilihan Ganda','UAS,Biologi','Verified',0,0,0,6,'2026-06-14 09:38:33','2026-06-14 09:38:33'),(4,'Soal Sejarah XI - Kemerdekaan',2,12,'Proklamasi Kemerdekaan',NULL,'Soal_Sejarah_XI_Kemerdekaan.docx','Soal_Sejarah_XI_Kemerdekaan.docx','Word','<p><strong>Kunci Jawaban Esai:</strong></p><p>1. Rengasdengklok mendesak Soekarno-Hatta untuk segera memproklamasikan kemerdekaan agar tidak terpengaruh janji Jepang.</p><p>2. Perjanjian Linggarjati mengakui kekuasaan RI secara de facto atas Jawa, Madura, dan Sumatera.</p>','Sedang','C2 - Memahami','Pilihan Ganda','SejarahWajib,Kemerdekaan','Verified',0,0,0,7,'2026-06-14 09:38:33','2026-06-14 09:38:33'),(5,'Soal Bahasa Inggris X - Narrative',1,7,'Narrative Text',NULL,'Soal_Bahasa_Inggris_X_Narrative.docx','Soal_Bahasa_Inggris_X_Narrative.docx','Word','<p><strong>Pembahasan:</strong></p><p>Soal nomor 1 mencari <em>Moral Value</em>. Jawabannya tersirat di paragraf terakhir. Soal nomor 2 adalah mencari <em>Main Character</em>, yang dijelaskan di awal paragraf (Poor Widow).</p>','Sedang','C2 - Memahami','Pilihan Ganda','Reading,Narrative','Draft',0,0,0,8,'2026-06-14 09:38:33','2026-06-14 09:38:33'),(6,'Soal Kimia XI - Hidrokarbon',2,4,'Senyawa Hidrokarbon',NULL,'Soal_Kimia_XI_Hidrokarbon.docx','Soal_Kimia_XI_Hidrokarbon.docx','Word','<p><strong>Pembahasan:</strong></p><p>Deret homolog alkana: CnH2n+2. Jika n=5, maka senyawanya adalah C5H12 yang disebut Pentana. Jawaban: D.</p>','Sedang','C2 - Memahami','Pilihan Ganda','Hidrokarbon,Kimia','Verified',0,0,0,9,'2026-06-14 09:38:33','2026-06-14 09:38:33'),(7,'Soal Sosiologi XII - Perubahan',3,15,'Perubahan Sosial',NULL,'Soal_Sosiologi_XII_Perubahan.docx','Soal_Sosiologi_XII_Perubahan.docx','Word','<p><strong>Pembahasan:</strong></p><p>Faktor eksternal berasal dari luar masyarakat, contohnya pengaruh kebudayaan lain, peperangan, dan bencana alam. Penemuan baru dan konflik termasuk internal. Jawaban: C.</p>','Sedang','C2 - Memahami','Pilihan Ganda','Sosiologi,PerubahanSosial','Review',0,0,0,10,'2026-06-14 09:38:33','2026-06-14 09:38:33'),(8,'Soal Ekonomi XI - Pajak',2,16,'Perpajakan',NULL,'Soal_Ekonomi_XI_Pajak.docx','Soal_Ekonomi_XI_Pajak.docx','Word','<p><strong>Pembahasan:</strong></p><p>Pajak yang dipungut oleh pemerintah daerah disebut Pajak Daerah (contoh: pajak kendaraan, restoran). Jawaban: B.</p>','Sedang','C2 - Memahami','Pilihan Ganda','Ekonomi,Pajak','Verified',0,0,0,4,'2026-06-14 09:38:33','2026-06-14 09:38:33');
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (2,'Admin Akademik / Administrasi'),(1,'Admin Sistem'),(4,'Kepala Sekolah');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identity_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `identity_id` (`identity_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,1,'admin','admin123','Superadmin Sistem',1,'2026-04-14 07:43:29'),(2,2,'administrasi','admin123','Admin Akademik (Administrasi)',2,'2026-04-14 07:43:29'),(3,3,'kepsek','admin123','Drs. H. M. Husain (Kepsek)',4,'2026-04-14 07:43:29');
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identity_id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `pin` varchar(10) DEFAULT '123456',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nip` (`nip`),
  KEY `identity_id` (`identity_id`),
  CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (1,4,'19800101201001','Dina Sisilia, S.Pd','123456','2026-06-14 09:38:33'),(2,5,'19810202201002','Rina Natalia, M.Pd','123456','2026-06-14 09:38:33'),(3,6,'19820303201003','Adithya Kusuma, S.Si','123456','2026-06-14 09:38:33'),(4,7,'19830404201004','Linda Lusiana, S.Pd','123456','2026-06-14 09:38:33'),(5,8,'19840505201005','Cicilia Dewi Andriani, M.Pd','123456','2026-06-14 09:38:33'),(6,9,'19850606201006','Mega Devinta, S.Pd','123456','2026-06-14 09:38:33'),(7,10,'19860707201007','Indayan Budi, S.Pd','123456','2026-06-14 09:38:33');
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-14 16:38:41
