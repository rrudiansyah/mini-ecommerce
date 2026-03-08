-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: ecommerce_builder
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_roles`
--

DROP TABLE IF EXISTS `admin_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admin_id` int NOT NULL,
  `role_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_admin_role` (`admin_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `admin_roles_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `admin_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,1,1,'2026-03-04 04:14:42'),(5,2,3,'2026-03-05 03:42:52'),(7,4,6,'2026-03-05 07:18:00'),(8,7,7,'2026-03-05 07:39:13'),(9,8,8,'2026-03-05 08:00:11'),(10,9,9,'2026-03-05 22:13:44'),(11,10,10,'2026-03-06 01:13:37'),(12,11,11,'2026-03-06 03:39:53'),(13,12,12,'2026-03-06 04:08:34');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `username_2` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,1,'Admin Kopi','admin','admin@kopinusantara.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-03-03 23:06:39'),(2,1,'rudi','rudi','rudi@mail.com','$2y$10$oBiEbQQKtivBAZITQN1fYe8g99IBxOKYrY0OKBeM4QRX5OltZ2Ep.',1,'2026-03-04 04:41:32'),(4,4,'erni','erni','admin@email.com','$2y$10$MGO9X4RJ9KB/3JWO5Z0eqezi/FX3Skjnk3guqAGUWpEfYmir2Ne2O',1,'2026-03-05 07:18:00'),(7,7,'neni','neni','rudi1@mail.com','$2y$10$mA5FsD43p6V47LSJHdc6x.Dp0GEXZ6.zMiQjE.6tiBbxiT5vb0hka',1,'2026-03-05 07:39:13'),(8,9,'rahmad','admingarudi','admin@garudi.com','$2y$10$PobxcperWY/vX/UGQ30nYu6Oya2XpiII7O2JTNJD9B1GCt5wl2uuK',1,'2026-03-05 08:00:10'),(9,10,'adminrumahfashion','adminrumahfashion',NULL,'$2y$10$vePEA4huzRbOO.zrbv/T6OxZi107huw/Vav.exANVOtPE6F.1vnXe',1,'2026-03-05 22:13:44'),(10,11,'admin','adminrestoenak',NULL,'$2y$10$awQn41FhfyzbIOhLlev4NuEzzbHCXRO5a1qEveE.fXkoBLpegUPV.',1,'2026-03-06 01:13:37'),(11,12,'adminpangkasremaja','adminpangkasremaja',NULL,'$2y$10$wmK1M/zNwILfdtlq1w/NyOTfwP0CsyHoN1pg0kVWPGtLaxLx55.My',1,'2026-03-06 03:39:53'),(12,13,'adminkopikilat','adminkopikilat',NULL,'$2y$10$R5UakUqVnVM3QL9REq/prON7Ali.zrBjOUk/BY2sqIFBw6YlAR8h6',1,'2026-03-06 04:08:34');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,1,'Kopi Panas','â˜•'),(2,1,'Kopi Dingin','ðŸ§Š'),(3,1,'Non-Kopi','ðŸµ'),(4,1,'Makanan','ðŸ°'),(5,7,'Cuci Kiloan',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (5,5,4,1,28000.00),(6,6,6,1,20000.00),(7,7,4,1,28000.00),(8,7,1,1,18000.00),(9,8,1,1,18000.00),(10,9,8,2,7000.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','proses','selesai','batal') DEFAULT 'pending',
  `note` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_status` enum('unpaid','paid','failed') DEFAULT 'unpaid',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (5,1,'Rudi','',28000.00,'selesai','','2026-03-04 03:19:31',NULL,'2026-03-04 03:23:04','paid'),(6,1,'andre','',20000.00,'selesai','','2026-03-04 22:52:33','Tunai','2026-03-04 22:58:45','paid'),(7,1,'bambang','',46000.00,'selesai','','2026-03-04 23:17:39',NULL,NULL,'unpaid'),(8,1,'Budi','',18000.00,'pending','less sugar','2026-03-05 22:15:18','Tunai','2026-03-05 22:56:55','paid'),(9,7,'Rudi','0811',14000.00,'proses','','2026-03-06 03:58:59','Tunai','2026-03-06 03:58:59','paid');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard.view','View dashboard','2026-03-04 04:14:42'),(2,'products.read','View products list','2026-03-04 04:14:42'),(3,'products.create','Create new product','2026-03-04 04:14:42'),(4,'products.update','Edit existing product','2026-03-04 04:14:42'),(5,'products.delete','Delete product','2026-03-04 04:14:42'),(6,'categories.read','View categories list','2026-03-04 04:14:42'),(7,'categories.create','Create new category','2026-03-04 04:14:42'),(8,'categories.update','Edit existing category','2026-03-04 04:14:42'),(9,'categories.delete','Delete category','2026-03-04 04:14:42'),(10,'orders.read','View orders','2026-03-04 04:14:42'),(11,'orders.create','Create new order','2026-03-04 04:14:42'),(12,'orders.update','Edit order details','2026-03-04 04:14:42'),(13,'orders.update_status','Update order status','2026-03-04 04:14:42'),(14,'orders.record_payment','Record payment','2026-03-04 04:14:42'),(15,'orders.view_invoice','View and print invoices','2026-03-04 04:14:42'),(16,'reports.view','View reports','2026-03-04 04:14:42'),(17,'reports.sales','View sales analytics','2026-03-04 04:14:42'),(18,'admins.manage','Manage users and roles','2026-03-04 04:14:42'),(19,'settings.view','View settings','2026-03-04 04:14:42'),(20,'settings.update','Update settings','2026-03-04 04:14:42');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,'Americano','Espresso + air panas',18000.00,NULL,1,'2026-03-03 23:06:39'),(2,1,1,'Cappuccino','Espresso + steamed milk + foam',25000.00,NULL,1,'2026-03-03 23:06:39'),(3,1,2,'Es Kopi Susu','Kopi + susu + es batu',22000.00,NULL,1,'2026-03-03 23:06:39'),(4,1,2,'Cold Brew','Kopi cold brew 12 jam',28000.00,NULL,1,'2026-03-03 23:06:39'),(5,1,3,'Matcha Latte','Matcha premium + susu',27000.00,NULL,1,'2026-03-03 23:06:39'),(6,1,4,'Croissant','Croissant butter fresh',20000.00,NULL,1,'2026-03-03 23:06:39'),(7,1,1,'Coffee Latte','Kopi Arabica dengan susu segar',20000.00,NULL,1,'2026-03-04 01:07:08'),(8,7,5,'Cuci + Setrika (Regular)','',7000.00,NULL,1,'2026-03-06 03:46:25');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1,18,'2026-03-04 04:14:42'),(2,1,7,'2026-03-04 04:14:42'),(3,1,9,'2026-03-04 04:14:42'),(4,1,6,'2026-03-04 04:14:42'),(5,1,8,'2026-03-04 04:14:42'),(6,1,1,'2026-03-04 04:14:42'),(7,1,11,'2026-03-04 04:14:42'),(8,1,10,'2026-03-04 04:14:42'),(9,1,14,'2026-03-04 04:14:42'),(10,1,12,'2026-03-04 04:14:42'),(11,1,13,'2026-03-04 04:14:42'),(12,1,15,'2026-03-04 04:14:42'),(13,1,3,'2026-03-04 04:14:42'),(14,1,5,'2026-03-04 04:14:42'),(15,1,2,'2026-03-04 04:14:42'),(16,1,4,'2026-03-04 04:14:42'),(17,1,17,'2026-03-04 04:14:42'),(18,1,16,'2026-03-04 04:14:42'),(19,1,20,'2026-03-04 04:14:42'),(20,1,19,'2026-03-04 04:14:42'),(21,2,1,'2026-03-04 04:14:42'),(22,2,2,'2026-03-04 04:14:42'),(23,2,3,'2026-03-04 04:14:42'),(24,2,4,'2026-03-04 04:14:42'),(25,2,5,'2026-03-04 04:14:42'),(26,2,6,'2026-03-04 04:14:42'),(27,2,7,'2026-03-04 04:14:42'),(28,2,8,'2026-03-04 04:14:42'),(29,2,9,'2026-03-04 04:14:42'),(30,2,10,'2026-03-04 04:14:42'),(31,2,11,'2026-03-04 04:14:42'),(32,2,12,'2026-03-04 04:14:42'),(33,2,13,'2026-03-04 04:14:42'),(34,2,14,'2026-03-04 04:14:42'),(35,2,16,'2026-03-04 04:14:42'),(36,2,19,'2026-03-04 04:14:42'),(37,3,1,'2026-03-04 04:14:42'),(38,3,2,'2026-03-04 04:14:42'),(39,3,6,'2026-03-04 04:14:42'),(40,3,10,'2026-03-04 04:14:42'),(41,3,11,'2026-03-04 04:14:42'),(42,3,13,'2026-03-04 04:14:42'),(43,3,14,'2026-03-04 04:14:42'),(44,4,1,'2026-03-04 04:14:42'),(45,4,2,'2026-03-04 04:14:42'),(46,4,6,'2026-03-04 04:14:42'),(47,4,10,'2026-03-04 04:14:42'),(48,4,16,'2026-03-04 04:14:42'),(49,3,15,'2026-03-05 03:45:39'),(81,6,1,'2026-03-05 07:18:00'),(82,6,2,'2026-03-05 07:18:00'),(83,6,3,'2026-03-05 07:18:00'),(84,6,4,'2026-03-05 07:18:00'),(85,6,5,'2026-03-05 07:18:00'),(86,6,6,'2026-03-05 07:18:00'),(87,6,7,'2026-03-05 07:18:00'),(88,6,8,'2026-03-05 07:18:00'),(89,6,9,'2026-03-05 07:18:00'),(90,6,10,'2026-03-05 07:18:00'),(91,6,11,'2026-03-05 07:18:00'),(92,6,12,'2026-03-05 07:18:00'),(93,6,13,'2026-03-05 07:18:00'),(94,6,14,'2026-03-05 07:18:00'),(95,6,15,'2026-03-05 07:18:00'),(96,6,16,'2026-03-05 07:18:00'),(97,6,17,'2026-03-05 07:18:00'),(98,6,18,'2026-03-05 07:18:00'),(99,6,19,'2026-03-05 07:18:00'),(100,6,20,'2026-03-05 07:18:00'),(112,7,1,'2026-03-05 07:39:13'),(113,7,2,'2026-03-05 07:39:13'),(114,7,3,'2026-03-05 07:39:13'),(115,7,4,'2026-03-05 07:39:13'),(116,7,5,'2026-03-05 07:39:13'),(117,7,6,'2026-03-05 07:39:13'),(118,7,7,'2026-03-05 07:39:13'),(119,7,8,'2026-03-05 07:39:13'),(120,7,9,'2026-03-05 07:39:13'),(121,7,10,'2026-03-05 07:39:13'),(122,7,11,'2026-03-05 07:39:13'),(123,7,12,'2026-03-05 07:39:13'),(124,7,13,'2026-03-05 07:39:13'),(125,7,14,'2026-03-05 07:39:13'),(126,7,15,'2026-03-05 07:39:13'),(127,7,16,'2026-03-05 07:39:13'),(128,7,17,'2026-03-05 07:39:13'),(129,7,18,'2026-03-05 07:39:13'),(130,7,19,'2026-03-05 07:39:13'),(131,7,20,'2026-03-05 07:39:13'),(143,8,1,'2026-03-05 08:00:11'),(144,8,2,'2026-03-05 08:00:11'),(145,8,3,'2026-03-05 08:00:11'),(146,8,4,'2026-03-05 08:00:11'),(147,8,5,'2026-03-05 08:00:11'),(148,8,6,'2026-03-05 08:00:11'),(149,8,7,'2026-03-05 08:00:11'),(150,8,8,'2026-03-05 08:00:11'),(151,8,9,'2026-03-05 08:00:11'),(152,8,10,'2026-03-05 08:00:11'),(153,8,11,'2026-03-05 08:00:11'),(154,8,12,'2026-03-05 08:00:11'),(155,8,13,'2026-03-05 08:00:11'),(156,8,14,'2026-03-05 08:00:11'),(157,8,15,'2026-03-05 08:00:11'),(158,8,16,'2026-03-05 08:00:11'),(159,8,17,'2026-03-05 08:00:11'),(160,8,18,'2026-03-05 08:00:11'),(161,8,19,'2026-03-05 08:00:11'),(162,8,20,'2026-03-05 08:00:11'),(163,9,1,'2026-03-05 22:13:44'),(164,9,2,'2026-03-05 22:13:44'),(165,9,3,'2026-03-05 22:13:44'),(166,9,4,'2026-03-05 22:13:44'),(167,9,5,'2026-03-05 22:13:44'),(168,9,6,'2026-03-05 22:13:44'),(169,9,7,'2026-03-05 22:13:44'),(170,9,8,'2026-03-05 22:13:44'),(171,9,9,'2026-03-05 22:13:44'),(172,9,10,'2026-03-05 22:13:44'),(173,9,11,'2026-03-05 22:13:44'),(174,9,12,'2026-03-05 22:13:44'),(175,9,13,'2026-03-05 22:13:44'),(176,9,14,'2026-03-05 22:13:44'),(177,9,15,'2026-03-05 22:13:44'),(178,9,16,'2026-03-05 22:13:44'),(179,9,17,'2026-03-05 22:13:44'),(180,9,18,'2026-03-05 22:13:44'),(181,9,19,'2026-03-05 22:13:44'),(182,9,20,'2026-03-05 22:13:44'),(183,10,1,'2026-03-06 01:13:37'),(184,10,2,'2026-03-06 01:13:37'),(185,10,3,'2026-03-06 01:13:37'),(186,10,4,'2026-03-06 01:13:37'),(187,10,5,'2026-03-06 01:13:37'),(188,10,6,'2026-03-06 01:13:37'),(189,10,7,'2026-03-06 01:13:37'),(190,10,8,'2026-03-06 01:13:37'),(191,10,9,'2026-03-06 01:13:37'),(192,10,10,'2026-03-06 01:13:37'),(193,10,11,'2026-03-06 01:13:37'),(194,10,12,'2026-03-06 01:13:37'),(195,10,13,'2026-03-06 01:13:37'),(196,10,14,'2026-03-06 01:13:37'),(197,10,15,'2026-03-06 01:13:37'),(198,10,16,'2026-03-06 01:13:37'),(199,10,17,'2026-03-06 01:13:37'),(200,10,18,'2026-03-06 01:13:37'),(201,10,19,'2026-03-06 01:13:37'),(202,10,20,'2026-03-06 01:13:37'),(203,11,1,'2026-03-06 03:39:53'),(204,11,2,'2026-03-06 03:39:53'),(205,11,3,'2026-03-06 03:39:53'),(206,11,4,'2026-03-06 03:39:53'),(207,11,5,'2026-03-06 03:39:53'),(208,11,6,'2026-03-06 03:39:53'),(209,11,7,'2026-03-06 03:39:53'),(210,11,8,'2026-03-06 03:39:53'),(211,11,9,'2026-03-06 03:39:53'),(212,11,10,'2026-03-06 03:39:53'),(213,11,11,'2026-03-06 03:39:53'),(214,11,12,'2026-03-06 03:39:53'),(215,11,13,'2026-03-06 03:39:53'),(216,11,14,'2026-03-06 03:39:53'),(217,11,15,'2026-03-06 03:39:53'),(218,11,16,'2026-03-06 03:39:53'),(219,11,17,'2026-03-06 03:39:53'),(220,11,18,'2026-03-06 03:39:53'),(221,11,19,'2026-03-06 03:39:53'),(222,11,20,'2026-03-06 03:39:53'),(223,12,1,'2026-03-06 04:08:34'),(224,12,2,'2026-03-06 04:08:34'),(225,12,3,'2026-03-06 04:08:34'),(226,12,4,'2026-03-06 04:08:34'),(227,12,5,'2026-03-06 04:08:34'),(228,12,6,'2026-03-06 04:08:34'),(229,12,7,'2026-03-06 04:08:34'),(230,12,8,'2026-03-06 04:08:34'),(231,12,9,'2026-03-06 04:08:34'),(232,12,10,'2026-03-06 04:08:34'),(233,12,11,'2026-03-06 04:08:34'),(234,12,12,'2026-03-06 04:08:34'),(235,12,13,'2026-03-06 04:08:34'),(236,12,14,'2026-03-06 04:08:34'),(237,12,15,'2026-03-06 04:08:34'),(238,12,16,'2026-03-06 04:08:34'),(239,12,17,'2026-03-06 04:08:34'),(240,12,18,'2026-03-06 04:08:34'),(241,12,19,'2026-03-06 04:08:34'),(242,12,20,'2026-03-06 04:08:34');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_store_role` (`store_id`,`name`),
  CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,1,'Admin','Full system access',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),(2,1,'Manager','Operational management',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),(3,1,'Staff','Basic operations and order management',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),(4,1,'Viewer','Read-only access',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),(6,4,'Admin','Administrator toko',1,'2026-03-05 07:18:00','2026-03-05 07:18:00'),(7,7,'Admin','Administrator toko',1,'2026-03-05 07:39:13','2026-03-05 07:39:13'),(8,9,'Admin','Administrator toko',1,'2026-03-05 08:00:11','2026-03-05 08:00:11'),(9,10,'Admin','Administrator toko',1,'2026-03-05 22:13:44','2026-03-05 22:13:44'),(10,11,'Admin','Administrator toko',1,'2026-03-06 01:13:37','2026-03-06 01:13:37'),(11,12,'Admin','Administrator toko',1,'2026-03-06 03:39:53','2026-03-06 03:39:53'),(12,13,'Admin','Administrator toko',1,'2026-03-06 04:08:34','2026-03-06 04:08:34');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `niche` enum('coffee','barbershop','restaurant','fashion','bakery','laundry') NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `theme_color` varchar(20) DEFAULT '#3b82f6',
  `address` text,
  `description` text,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `slug_2` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stores`
--

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;
INSERT INTO `stores` VALUES (1,'Kopi Nusantara','kopi-nusantara','coffee',NULL,'#b45309','Jl. Merdeka No. 10, Banda Aceh',NULL,'0812-0000-0001',1,'2026-03-03 23:06:39'),(4,'Toko Roti Bakery','toko-roti-bakery','bakery',NULL,'#3b82f6','Jl.binjai',NULL,'0811',1,'2026-03-05 07:18:00'),(7,'Laundry Bersih','laundry-bersih-2','laundry',NULL,'#3b82f6','medan',NULL,'0811',1,'2026-03-05 07:39:13'),(9,'Garudi','garudi-1','restaurant',NULL,'#3b82f6','medan',NULL,'0811',1,'2026-03-05 08:00:10'),(10,'Rumah Fashion','rumah-fashion','fashion',NULL,'#3b82f6','medan',NULL,'0811',1,'2026-03-05 22:13:44'),(11,'Resto Enak','resto-enak','restaurant','uploads/logos/store_11_logo_1772760678.png','#3b82f6',NULL,NULL,'0811',1,'2026-03-06 01:13:37'),(12,'Pangkas Remaja','pangkas-remaja','barbershop',NULL,'#3b82f6','medan',NULL,'0811',1,'2026-03-06 03:39:53'),(13,'Kopi Kilat','kopi-kilat','coffee',NULL,'#3b82f6','medan',NULL,'0811',1,'2026-03-06 04:08:34');
/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `super_admins`
--

DROP TABLE IF EXISTS `super_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `super_admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `super_admins`
--

LOCK TABLES `super_admins` WRITE;
/*!40000 ALTER TABLE `super_admins` DISABLE KEYS */;
INSERT INTO `super_admins` VALUES (1,'Super Admin','superadmin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-03-05 04:57:54');
/*!40000 ALTER TABLE `super_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'ecommerce_builder'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-08  0:39:13
