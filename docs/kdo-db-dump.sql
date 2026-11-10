/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.16-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: kdo_db
-- ------------------------------------------------------
-- Server version	10.11.16-MariaDB

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
-- Current Database: `kdo_db`
--

/*!40000 DROP DATABASE IF EXISTS `kdo_db`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `kdo_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `kdo_db`;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kendaraan`
--

DROP TABLE IF EXISTS `kendaraan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kendaraan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_polisi` varchar(15) NOT NULL,
  `merek` varchar(255) NOT NULL,
  `tipe` varchar(255) NOT NULL,
  `tahun_produksi` smallint(5) unsigned NOT NULL,
  `warna` varchar(255) NOT NULL,
  `kondisi` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `perangkat_daerah_id` bigint(20) unsigned NOT NULL,
  `pegawai_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kendaraan_nomor_polisi_unique` (`nomor_polisi`),
  KEY `kendaraan_perangkat_daerah_id_foreign` (`perangkat_daerah_id`),
  KEY `kendaraan_pegawai_id_foreign` (`pegawai_id`),
  KEY `kendaraan_kondisi_index` (`kondisi`),
  CONSTRAINT `kendaraan_pegawai_id_foreign` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kendaraan_perangkat_daerah_id_foreign` FOREIGN KEY (`perangkat_daerah_id`) REFERENCES `perangkat_daerah` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kendaraan`
--

LOCK TABLES `kendaraan` WRITE;
/*!40000 ALTER TABLE `kendaraan` DISABLE KEYS */;
INSERT INTO `kendaraan` VALUES
(1,'DK 1234 AB','Toyota','Avanza 1.5 G',2021,'Putih','baik',1,1,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(2,'DK 5678 CD','Toyota','Hiace Commuter',2019,'Putih','rusak_ringan',1,NULL,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(3,'DK 2345 EF','Mitsubishi','Pajero Sport Dakar',2020,'Hitam','baik',2,3,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(4,'DK 3456 GH','Toyota','Kijang Innova Reborn',2018,'Silver','baik',2,4,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(5,'DK 7890 IJ','Suzuki','Ertiga',2017,'Abu-abu','rusak_ringan',2,5,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(6,'DK 4567 KL','Toyota','Hiace Ambulance',2020,'Putih','baik',3,6,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(7,'DK 8901 MN','Nissan','Urvan',2016,'Putih','rusak_berat',3,7,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(8,'DK 1122 OP','Honda','BR-V',2022,'Merah','baik',3,NULL,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(9,'DK 2233 QR','Toyota','Fortuner VRZ',2021,'Hitam','baik',4,8,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(10,'DK 3344 ST','Daihatsu','Gran Max Minibus',2018,'Putih','rusak_ringan',4,9,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(11,'DK 5566 UV','Isuzu','Elf Long Chasis',2015,'Putih','rusak_berat',4,NULL,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(12,'DK 6677 WX','Toyota','Hilux Double Cabin',2020,'Putih','baik',5,10,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(13,'DK 8899 YZ','Mitsubishi','Triton HDX',2019,'Putih','rusak_ringan',5,11,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(14,'DK 9900 AA','Toyota','Vios',2018,'Silver','baik',6,12,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(15,'DK 1010 BC','Daihatsu','Luxio',2016,'Hitam','baik',6,NULL,'2026-11-09 22:15:33','2026-11-09 22:15:33');
/*!40000 ALTER TABLE `kendaraan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_11_10_051235_create_personal_access_tokens_table',1),
(5,'2026_11_10_051410_create_perangkat_daerah_table',1),
(6,'2026_11_10_051411_create_pegawai_table',1),
(7,'2026_11_10_051412_create_kendaraan_table',1),
(8,'2026_11_10_051413_add_role_to_users_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pegawai`
--

DROP TABLE IF EXISTS `pegawai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pegawai` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `nip` varchar(18) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `golongan` varchar(10) NOT NULL,
  `perangkat_daerah_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pegawai_nip_unique` (`nip`),
  KEY `pegawai_perangkat_daerah_id_foreign` (`perangkat_daerah_id`),
  CONSTRAINT `pegawai_perangkat_daerah_id_foreign` FOREIGN KEY (`perangkat_daerah_id`) REFERENCES `perangkat_daerah` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pegawai`
--

LOCK TABLES `pegawai` WRITE;
/*!40000 ALTER TABLE `pegawai` DISABLE KEYS */;
INSERT INTO `pegawai` VALUES
(1,'Budi Santoso','197504121998031001','Kepala Bagian Umum','IV/b',1,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(2,'Siti Rahmawati','198009232005042002','Kepala Sub Bagian Kepegawaian','III/d',1,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(3,'Ahmad Hidayat','196811051994031004','Kepala Dinas Pendidikan','IV/c',2,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(4,'Dewi Lestari','199001172015032007','Pengelola Barang Milik Daerah','III/a',2,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(5,'Rizki Pratama','198506302010121003','Kepala Sub Bagian Sarana Prasarana','III/c',2,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(6,'Endang Kusmiati','197208151999022001','Kepala Dinas Kesehatan','IV/b',3,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(7,'Agus Wibowo','199303282018101002','Analis Kesehatan','III/a',3,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(8,'Hendra Gunawan','197907092006041005','Kepala Bidang Angkutan Darat','IV/a',4,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(9,'Maya Puspita','198812012013062001','Pengadministrasi Perhubungan','III/b',4,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(10,'Slamet Riyadi','197402202000011006','Kepala Bidang Bina Marga','IV/a',5,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(11,'Fitri Handayani','199205162016112003','Pengelola Keuangan','III/a',5,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(12,'Taufik Hidayat','198103112008091004','Kepala Badan Kepegawaian Daerah','IV/b',6,'2026-11-09 22:15:33','2026-11-09 22:15:33');
/*!40000 ALTER TABLE `pegawai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perangkat_daerah`
--

DROP TABLE IF EXISTS `perangkat_daerah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `perangkat_daerah` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `perangkat_daerah_kode_unique` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perangkat_daerah`
--

LOCK TABLES `perangkat_daerah` WRITE;
/*!40000 ALTER TABLE `perangkat_daerah` DISABLE KEYS */;
INSERT INTO `perangkat_daerah` VALUES
(1,'SETDA','Sekretariat Daerah','2026-11-09 22:15:33','2026-11-09 22:15:33'),
(2,'DISDIK','Dinas Pendidikan','2026-11-09 22:15:33','2026-11-09 22:15:33'),
(3,'DINKES','Dinas Kesehatan','2026-11-09 22:15:33','2026-11-09 22:15:33'),
(4,'DISHUB','Dinas Perhubungan','2026-11-09 22:15:33','2026-11-09 22:15:33'),
(5,'DPUPR','Dinas Pekerjaan Umum dan Penataan Ruang','2026-11-09 22:15:33','2026-11-09 22:15:33'),
(6,'BKD','Badan Kepegawaian Daerah','2026-11-09 22:15:33','2026-11-09 22:15:33');
/*!40000 ALTER TABLE `perangkat_daerah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'viewer',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Administrator KDO','admin@kdo.test','2026-11-09 22:15:33','$2y$12$yUspcaTtm0KyAlsXNT7xweLy5u7xSnBWP1tPeEAdzV7dLrjzradw.','admin',NULL,'2026-11-09 22:15:33','2026-11-09 22:15:33'),
(2,'Pengamat KDO','viewer@kdo.test','2026-11-09 22:15:34','$2y$12$PDZ6SrzkF/9I8oGb66500.g3MPXPIMnbRWVMOS8gKkz8KRXMG4JYK','viewer',NULL,'2026-11-09 22:15:34','2026-11-09 22:15:34');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'kdo_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-10 12:15:55
