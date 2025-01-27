/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `main_academics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_academics` (
  `academic_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('undergraduate','postgraduate','phd','erasmus','researcher','staff coupon','staff card application','staff entry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `a_m` mediumint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`academic_id`),
  UNIQUE KEY `main_academics_email_unique` (`email`),
  UNIQUE KEY `main_academics_a_m_unique` (`a_m`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_id` bigint unsigned NOT NULL,
  `is_permanent` tinyint(1) NOT NULL DEFAULT '1',
  `location` char(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `main_addresses_academic_id_foreign` (`academic_id`),
  CONSTRAINT `main_addresses_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `main_card_applicants` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_card_applicants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_card_applicants` (
  `academic_id` bigint unsigned NOT NULL,
  `first_year` year NOT NULL,
  `department_id` tinyint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`academic_id`),
  KEY `main_card_applicants_department_id_foreign` (`department_id`),
  CONSTRAINT `main_card_applicants_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `main_academics` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `main_card_applicants_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `main_departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_card_application_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_card_application_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `card_application_id` bigint unsigned NOT NULL,
  `status` enum('submitted','accepted','rejected','incomplete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `file_name` char(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` char(27) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `main_card_application_documents_card_application_id_foreign` (`card_application_id`),
  CONSTRAINT `main_card_application_documents_card_application_id_foreign` FOREIGN KEY (`card_application_id`) REFERENCES `main_card_applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_card_application_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_card_application_staff` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'George',
  PRIMARY KEY (`id`),
  UNIQUE KEY `main_card_application_staff_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_card_application_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_card_application_update` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `card_application_staff_id` tinyint unsigned DEFAULT NULL COMMENT 'null if it is the student otherwise staff',
  `card_application_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('temporary saved','submitted','checking','temporary checked','accepted','rejected','incomplete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'temporary saved',
  PRIMARY KEY (`id`),
  KEY `main_card_application_checking_card_application_staff_id_foreign` (`card_application_staff_id`),
  KEY `main_card_application_checking_card_application_id_foreign` (`card_application_id`),
  CONSTRAINT `main_card_application_checking_card_application_id_foreign` FOREIGN KEY (`card_application_id`) REFERENCES `main_card_applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `main_card_application_checking_card_application_staff_id_foreign` FOREIGN KEY (`card_application_staff_id`) REFERENCES `main_card_application_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_card_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_card_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expiration_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `main_card_applications_academic_id_foreign` (`academic_id`),
  CONSTRAINT `main_card_applications_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `main_card_applicants` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_coupon_owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_coupon_owners` (
  `academic_id` bigint unsigned NOT NULL,
  `money` int unsigned NOT NULL DEFAULT '0',
  `BREAKFAST` int unsigned NOT NULL DEFAULT '0',
  `LUNCH` int unsigned NOT NULL DEFAULT '0',
  `DINNER` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`academic_id`),
  CONSTRAINT `main_coupon_owners_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `main_academics` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_coupon_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_coupon_staff` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('undergraduate','postgraduate','phd','erasmus','researcher','staff coupon','staff card application','staff entry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'George',
  PRIMARY KEY (`id`),
  UNIQUE KEY `main_coupon_staff_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_coupon_transactions`;
/*!50001 DROP VIEW IF EXISTS `main_coupon_transactions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `main_coupon_transactions` AS SELECT 
 1 AS `id`,
 1 AS `created_at`,
 1 AS `academic_id`,
 1 AS `transaction`,
 1 AS `other_person_id`,
 1 AS `money`,
 1 AS `BREAKFAST`,
 1 AS `LUNCH`,
 1 AS `DINNER`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `main_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_departments` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` char(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_entry_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_entry_staff` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('undergraduate','postgraduate','phd','erasmus','researcher','staff coupon','staff card application','staff entry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'George',
  PRIMARY KEY (`id`),
  UNIQUE KEY `main_entry_staff_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `main_password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_purchase_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_purchase_coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_id` bigint unsigned NOT NULL,
  `coupon_staff_id` tinyint unsigned NOT NULL,
  `money` int unsigned NOT NULL DEFAULT '0',
  `BREAKFAST` tinyint unsigned NOT NULL DEFAULT '0',
  `LUNCH` tinyint unsigned NOT NULL DEFAULT '0',
  `DINNER` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `main_purchase_coupons_academic_id_foreign` (`academic_id`),
  KEY `main_purchase_coupons_coupon_staff_id_foreign` (`coupon_staff_id`),
  CONSTRAINT `main_purchase_coupons_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `main_coupon_owners` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `main_purchase_coupons_coupon_staff_id_foreign` FOREIGN KEY (`coupon_staff_id`) REFERENCES `main_coupon_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_transfer_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_transfer_coupon` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `receiver_id` bigint unsigned NOT NULL,
  `BREAKFAST` tinyint unsigned NOT NULL DEFAULT '0',
  `LUNCH` tinyint unsigned NOT NULL DEFAULT '0',
  `DINNER` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `main_transfer_coupon_sender_id_foreign` (`sender_id`),
  KEY `main_transfer_coupon_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `main_transfer_coupon_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `main_coupon_owners` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `main_transfer_coupon_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `main_coupon_owners` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_usage_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_usage_cards` (
  `date` date NOT NULL,
  `academic_id` bigint unsigned NOT NULL,
  `period` enum('breakfast','lunch','dinner') COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` time NOT NULL,
  `entry_staff_id` tinyint unsigned NOT NULL,
  PRIMARY KEY (`date`,`academic_id`,`period`),
  KEY `main_usage_cards_academic_id_foreign` (`academic_id`),
  KEY `main_usage_cards_entry_staff_id_foreign` (`entry_staff_id`),
  CONSTRAINT `main_usage_cards_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `main_card_applicants` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `main_usage_cards_entry_staff_id_foreign` FOREIGN KEY (`entry_staff_id`) REFERENCES `main_entry_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_usage_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_usage_coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `academic_id` bigint unsigned NOT NULL,
  `entry_staff_id` tinyint unsigned NOT NULL,
  `period` enum('breakfast','lunch','dinner') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `main_usage_coupons_academic_id_foreign` (`academic_id`),
  KEY `main_usage_coupons_entry_staff_id_foreign` (`entry_staff_id`),
  CONSTRAINT `main_usage_coupons_academic_id_foreign` FOREIGN KEY (`academic_id`) REFERENCES `main_coupon_owners` (`academic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `main_usage_coupons_entry_staff_id_foreign` FOREIGN KEY (`entry_staff_id`) REFERENCES `main_entry_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `main_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `main_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('undergraduate','postgraduate','phd','erasmus','researcher','staff coupon','staff card application','staff entry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `main_users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP VIEW IF EXISTS `main_coupon_transactions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`food`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `main_coupon_transactions` AS select `main_transfer_coupon`.`id` AS `id`,`main_transfer_coupon`.`created_at` AS `created_at`,`main_transfer_coupon`.`sender_id` AS `academic_id`,'sending' AS `transaction`,`main_transfer_coupon`.`receiver_id` AS `other_person_id`,0 AS `money`,(cast(`main_transfer_coupon`.`BREAKFAST` as signed) * -(1)) AS `BREAKFAST`,(cast(`main_transfer_coupon`.`LUNCH` as signed) * -(1)) AS `LUNCH`,(cast(`main_transfer_coupon`.`DINNER` as signed) * -(1)) AS `DINNER` from `main_transfer_coupon` union select `main_transfer_coupon`.`id` AS `id`,`main_transfer_coupon`.`created_at` AS `created_at`,`main_transfer_coupon`.`receiver_id` AS `academic_id`,'receiving' AS `transaction`,`main_transfer_coupon`.`sender_id` AS `other_person_id`,0 AS `money`,`main_transfer_coupon`.`BREAKFAST` AS `BREAKFAST`,`main_transfer_coupon`.`LUNCH` AS `LUNCH`,`main_transfer_coupon`.`DINNER` AS `DINNER` from `main_transfer_coupon` union select `main_purchase_coupons`.`id` AS `id`,`main_purchase_coupons`.`created_at` AS `created_at`,`main_purchase_coupons`.`academic_id` AS `academic_id`,'buying' AS `transaction`,0 AS `other_person_id`,(`main_purchase_coupons`.`money` / 100) AS `money`,`main_purchase_coupons`.`BREAKFAST` AS `BREAKFAST`,`main_purchase_coupons`.`LUNCH` AS `LUNCH`,`main_purchase_coupons`.`DINNER` AS `DINNER` from `main_purchase_coupons` union select `main_usage_coupons`.`id` AS `id`,`main_usage_coupons`.`created_at` AS `created_at`,`main_usage_coupons`.`academic_id` AS `academic_id`,'using' AS `transaction`,0 AS `other_person_id`,0 AS `money`,(case when (`main_usage_coupons`.`period` = 'BREAKFAST') then -(1) else 0 end) AS `BREAKFAST`,(case when (`main_usage_coupons`.`period` = 'LUNCH') then -(1) else 0 end) AS `LUNCH`,(case when (`main_usage_coupons`.`period` = 'DINNER') then -(1) else 0 end) AS `DINNER` from `main_usage_coupons` order by `created_at` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (1,'2022_04_14_083353_create_academics_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (2,'2022_04_14_083400_create_departments_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (3,'2022_04_14_083404_create_card_applicants_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (4,'2022_04_14_083410_create_card_application_staff_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (5,'2022_04_14_083459_create_coupon_staff_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (6,'2022_04_14_083510_create_entry_staff_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (7,'2022_05_06_121658_alter_card_applicants_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (8,'2022_05_06_123043_create_addresses_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_12_13_171738_remove_cellphone_from_card_applicants_table',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_01_13_140927_alter_academic_add_father_name',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_01_13_141015_alter_card_application_staff_add_father_name',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_01_13_141042_alter_entry_staff_add_father_name',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_01_13_141055_alter_coupon_staff_add_father_name',1);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (14,'2022_04_14_083415_create_card_applications_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (15,'2022_04_14_083426_create_card_application_documents_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (16,'2022_04_14_083448_create_coupon_owners_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (17,'2022_04_14_083545_create_purchase_coupons_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (18,'2022_04_14_083556_create_usage_cards_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (19,'2022_04_14_083608_create_usage_coupons_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (20,'2022_04_14_101359_create_transfer_coupons_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (21,'2022_05_06_113120_alter_card_applications_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (22,'2022_05_06_130407_create_card_application_checking_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (23,'2022_05_06_135934_create_has_card_applicant_comments_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (24,'2024_03_13_171140_alter_card_application_documents_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (25,'2024_03_23_185226_alter_card_application_checking_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (26,'2024_03_23_185227_alter_has_card_applicant_comments_table',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (27,'2024_03_23_185228_move_status_from_card__application_to_card_application_checking',2);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (29,'2014_10_12_000000_create_users_table',3);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (30,'2014_10_12_100000_create_password_resets_table',3);
INSERT INTO `main_migrations` (`id`, `migration`, `batch`) VALUES (32,'2024_11_12_092021_create_coupon_transactions_view',4);
