/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `auth_academics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_academics` (
  `academic_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '''undergraduate'',''postgraduate'',''phd'',''erasmus'',''researcher'',''staff coupon'',''staff card application'',''staff entry''',
  `a_m` mediumint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'George',
  PRIMARY KEY (`academic_id`),
  UNIQUE KEY `auth_academics_email_unique` (`email`),
  UNIQUE KEY `auth_academics_a_m_unique` (`a_m`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_id` bigint unsigned NOT NULL,
  `is_permanent` tinyint(1) NOT NULL DEFAULT '1',
  `location` char(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_addresses_academic_id_foreign` (`academic_id`),
  CONSTRAINT `auth_addresses_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `auth_card_applicants` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_card_applicants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_card_applicants` (
  `academic_id` bigint unsigned NOT NULL,
  `first_year` year NOT NULL,
  `department_id` tinyint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`academic_id`),
  KEY `auth_card_applicants_department_id_foreign` (`department_id`),
  CONSTRAINT `auth_card_applicants_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `auth_academics` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_card_applicants_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `auth_departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_card_application_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_card_application_staff` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('undergraduate','postgraduate','phd','erasmus','researcher','staff coupon','staff card application','staff entry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'George',
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_card_application_staff_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_coupon_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_coupon_staff` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('undergraduate','postgraduate','phd','erasmus','researcher','staff coupon','staff card application','staff entry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'George',
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_coupon_staff_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_departments` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` char(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_entry_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_entry_staff` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('undergraduate','postgraduate','phd','erasmus','researcher','staff coupon','staff card application','staff entry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'George',
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_entry_staff_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `auth_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (1,'2022_04_14_083353_create_academics_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (2,'2022_04_14_083400_create_departments_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (3,'2022_04_14_083404_create_card_applicants_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (4,'2022_04_14_083410_create_card_application_staff_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (5,'2022_04_14_083459_create_coupon_staff_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (6,'2022_04_14_083510_create_entry_staff_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (7,'2022_05_06_121658_alter_card_applicants_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (8,'2022_05_06_123043_create_addresses_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_12_13_171738_remove_cellphone_from_card_applicants_table',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_01_13_140927_alter_academic_add_father_name',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_01_13_141015_alter_card_application_staff_add_father_name',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_01_13_141042_alter_entry_staff_add_father_name',1);
INSERT INTO `auth_migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_01_13_141055_alter_coupon_staff_add_father_name',1);
