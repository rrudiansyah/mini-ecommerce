/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 8.0.44 : Database - ecommerce_builder
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`ecommerce_builder` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `ecommerce_builder`;

/*Table structure for table `admin_roles` */

DROP TABLE IF EXISTS `admin_roles`;

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `admin_roles` */

insert  into `admin_roles`(`id`,`admin_id`,`role_id`,`created_at`) values 
(1,1,1,'2026-03-04 04:14:42'),
(5,2,3,'2026-03-05 03:42:52'),
(14,13,2,'2026-03-11 03:07:29'),
(19,17,16,'2026-03-13 07:20:09'),
(20,18,17,'2026-03-13 07:20:42'),
(21,19,18,'2026-03-13 07:21:52');

/*Table structure for table `admins` */

DROP TABLE IF EXISTS `admins`;

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `admins` */

insert  into `admins`(`id`,`store_id`,`name`,`username`,`email`,`password`,`is_active`,`created_at`) values 
(1,1,'Admin Kopi','admin','admin@kopinusantara.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-03-03 23:06:39'),
(2,1,'rudi','rudi','rudi@mail.com','$2y$10$oBiEbQQKtivBAZITQN1fYe8g99IBxOKYrY0OKBeM4QRX5OltZ2Ep.',1,'2026-03-04 04:41:32'),
(13,1,'rambo','rambo','rambo@mail.com','$2y$10$nX8YOwvKHVPoJQ8aa72D3OzvwCMl0r2FPsqyFpqNckZ7FF0KHfCXK',1,'2026-03-11 03:07:29'),
(17,17,'admincoffeeshopbasic','admincoffeeshopbasic',NULL,'$2y$10$t.v0fadq4O3ykbmwWS5CNOULbj2gUz/fnNatyC.VfSJA1vEmeg2Mi',1,'2026-03-13 07:20:09'),
(18,18,'admincoffeeshoppro','admincoffeeshoppro',NULL,'$2y$10$NkpDeQU9tt91gQCjKgW0yObS.rwZtQOAqeJXNlA6QUPiDqLS.9vuq',1,'2026-03-13 07:20:42'),
(19,19,'admincoffeeshpbisnis','admincoffeeshpbisnis',NULL,'$2y$10$QSoxG1OZas.LvcybrAehUuh1Xxpst9Tcn8gsmcIiePY.QJHa4ry6a',1,'2026-03-13 07:21:52');

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`store_id`,`name`,`icon`) values 
(1,1,'Kopi Panas','â˜•'),
(2,1,'Kopi Dingin','ðŸ§Š'),
(3,1,'Non-Kopi','ðŸµ'),
(4,1,'Makanan','ðŸ°');

/*Table structure for table `ingredients` */

DROP TABLE IF EXISTS `ingredients`;

CREATE TABLE `ingredients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `unit` varchar(30) NOT NULL DEFAULT 'pcs',
  `stock` decimal(10,3) NOT NULL DEFAULT '0.000',
  `stock_min` decimal(10,3) NOT NULL DEFAULT '0.000',
  `cost_per_unit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `ingredients` */

insert  into `ingredients`(`id`,`store_id`,`name`,`unit`,`stock`,`stock_min`,`cost_per_unit`,`notes`,`created_at`,`updated_at`) values 
(4,1,'Susu Cair','ml',820.000,100.000,30.00,'','2026-03-10 19:49:40','2026-03-13 07:17:51'),
(5,1,'Biji Kopi Arabica','gr',970.000,10.000,186.00,'','2026-03-10 19:52:00','2026-03-13 07:17:51'),
(7,1,'Cup + tutup','pcs',148.000,10.000,1500.00,'','2026-03-12 01:17:52','2026-03-12 02:10:29'),
(8,1,'Matcha','gr',10.000,0.000,1000.00,'','2026-03-13 06:39:22','2026-03-13 06:39:22'),
(9,1,'Susu foam (busa susu)','ml',995.000,0.000,1000.00,'','2026-03-13 07:09:58','2026-03-13 07:17:51');

/*Table structure for table `order_items` */

DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `variant_id` int DEFAULT NULL,
  `variant_label` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `order_items` */

insert  into `order_items`(`id`,`order_id`,`product_id`,`qty`,`price`,`variant_id`,`variant_label`) values 
(5,5,4,1,28000.00,NULL,NULL),
(6,6,6,1,20000.00,NULL,NULL),
(7,7,4,1,28000.00,NULL,NULL),
(8,7,1,1,18000.00,NULL,NULL),
(9,8,1,1,18000.00,NULL,NULL),
(11,10,3,1,22000.00,NULL,NULL),
(12,11,4,1,28000.00,NULL,NULL),
(13,11,3,1,22000.00,NULL,NULL),
(14,11,5,1,27000.00,NULL,NULL),
(15,12,1,1,18000.00,NULL,NULL),
(16,13,7,1,25000.00,NULL,NULL),
(20,17,7,1,25000.00,NULL,NULL),
(21,17,6,1,20000.00,NULL,NULL),
(22,18,7,1,25000.00,NULL,NULL),
(23,18,6,1,20000.00,NULL,NULL),
(36,28,2,1,25000.00,NULL,NULL);

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `orders` */

insert  into `orders`(`id`,`store_id`,`customer_name`,`customer_phone`,`total`,`status`,`note`,`created_at`,`payment_method`,`payment_date`,`payment_status`) values 
(5,1,'Rudi','',28000.00,'selesai','','2026-03-04 03:19:31',NULL,'2026-03-04 03:23:04','paid'),
(6,1,'andre','',20000.00,'selesai','','2026-03-04 22:52:33','Tunai','2026-03-04 22:58:45','paid'),
(7,1,'bambang','',46000.00,'selesai','','2026-03-04 23:17:39',NULL,NULL,'unpaid'),
(8,1,'Budi','',18000.00,'selesai','less sugar','2026-03-05 22:15:18','Tunai','2026-03-05 22:56:55','paid'),
(10,1,'andre','0811',22000.00,'selesai','','2026-03-09 15:41:36','Tunai','2026-03-09 15:41:36','paid'),
(11,1,'julfan','0811',77000.00,'selesai','','2026-03-09 16:14:15','Tunai','2026-03-09 16:36:37','paid'),
(12,1,'Santi','',18000.00,'selesai','','2026-03-10 19:38:08',NULL,'2026-03-10 19:40:15','paid'),
(13,1,'Santi','0811',25000.00,'selesai','','2026-03-10 19:57:25','Tunai','2026-03-10 19:57:25','paid'),
(17,1,'Rudi','0811',45000.00,'selesai','','2026-03-12 02:07:30','Tunai','2026-03-12 02:07:30','paid'),
(18,1,'ali','0811',45000.00,'selesai','','2026-03-12 02:10:15','Tunai','2026-03-12 02:10:15','paid'),
(28,1,'hari','',25000.00,'selesai','','2026-03-13 07:17:13','Tunai','2026-03-13 07:17:58','paid');

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`description`,`created_at`) values 
(1,'dashboard.view','View dashboard','2026-03-04 04:14:42'),
(2,'products.read','View products list','2026-03-04 04:14:42'),
(3,'products.create','Create new product','2026-03-04 04:14:42'),
(4,'products.update','Edit existing product','2026-03-04 04:14:42'),
(5,'products.delete','Delete product','2026-03-04 04:14:42'),
(6,'categories.read','View categories list','2026-03-04 04:14:42'),
(7,'categories.create','Create new category','2026-03-04 04:14:42'),
(8,'categories.update','Edit existing category','2026-03-04 04:14:42'),
(9,'categories.delete','Delete category','2026-03-04 04:14:42'),
(10,'orders.read','View orders','2026-03-04 04:14:42'),
(11,'orders.create','Create new order','2026-03-04 04:14:42'),
(12,'orders.update','Edit order details','2026-03-04 04:14:42'),
(13,'orders.update_status','Update order status','2026-03-04 04:14:42'),
(14,'orders.record_payment','Record payment','2026-03-04 04:14:42'),
(15,'orders.view_invoice','View and print invoices','2026-03-04 04:14:42'),
(16,'reports.view','View reports','2026-03-04 04:14:42'),
(17,'reports.sales','View sales analytics','2026-03-04 04:14:42'),
(18,'admins.manage','Manage users and roles','2026-03-04 04:14:42'),
(19,'settings.view','View settings','2026-03-04 04:14:42'),
(20,'settings.update','Update settings','2026-03-04 04:14:42'),
(21,'inventory.read','Lihat daftar bahan baku & stok','2026-03-10 19:20:44'),
(22,'inventory.manage','Tambah/edit/hapus bahan baku','2026-03-10 19:20:44'),
(23,'inventory.stock_in','Input stok masuk','2026-03-10 19:20:44'),
(24,'inventory.logs','Lihat riwayat stok','2026-03-10 19:20:44'),
(25,'variants.read','Lihat varian produk','2026-03-11 20:10:42'),
(26,'variants.manage','Kelola varian produk','2026-03-11 20:10:42'),
(27,'inventory.create','Tambah & kelola stok bahan baku','2026-03-11 22:32:28'),
(28,'inventory.update','Edit bahan baku','2026-03-11 22:32:28'),
(29,'inventory.delete','Hapus bahan baku','2026-03-11 22:32:28'),
(30,'reports.export','Export laporan ke Excel/PDF','2026-03-11 22:32:28');

/*Table structure for table `product_recipes` */

DROP TABLE IF EXISTS `product_recipes`;

CREATE TABLE `product_recipes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `qty_used` decimal(10,3) NOT NULL DEFAULT '1.000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_recipe` (`product_id`,`ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `pr_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pr_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `product_recipes` */

insert  into `product_recipes`(`id`,`product_id`,`ingredient_id`,`qty_used`) values 
(7,1,5,50.000),
(8,1,7,1.000),
(9,7,5,50.000),
(10,7,7,1.000),
(11,7,4,10.000),
(12,5,8,20.000),
(13,5,7,1.000),
(17,2,5,30.000),
(18,2,4,150.000),
(19,2,9,5.000);

/*Table structure for table `product_stock_logs` */

DROP TABLE IF EXISTS `product_stock_logs`;

CREATE TABLE `product_stock_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `product_id` int NOT NULL,
  `type` enum('in','out','adjustment') NOT NULL DEFAULT 'in',
  `qty` int NOT NULL DEFAULT '0',
  `stock_before` int NOT NULL DEFAULT '0',
  `stock_after` int NOT NULL DEFAULT '0',
  `notes` varchar(255) DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `created_by` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `product_stock_logs` */

insert  into `product_stock_logs`(`id`,`store_id`,`product_id`,`type`,`qty`,`stock_before`,`stock_after`,`notes`,`order_id`,`created_by`,`created_at`) values 
(1,1,6,'adjustment',0,8,8,'Penyesuaian stok',NULL,1,'2026-03-12 06:57:37'),
(2,1,6,'in',1,8,9,'',NULL,1,'2026-03-12 06:58:04'),
(3,15,10,'in',10,20,30,'Stok masuk',NULL,15,'2026-03-12 14:04:53'),
(4,15,14,'in',40,0,40,'Stok masuk',NULL,15,'2026-03-12 14:46:07'),
(5,15,14,'adjustment',0,40,40,'Penyesuaian stok',NULL,15,'2026-03-12 14:46:18'),
(6,16,16,'in',20,0,20,'Stok masuk',NULL,16,'2026-03-13 03:38:27'),
(7,16,16,'out',1,20,19,'Pesanan #27',27,16,'2026-03-13 03:39:04'),
(8,1,1,'in',19,0,19,'Stok masuk',NULL,1,'2026-03-13 03:45:39'),
(9,1,1,'adjustment',19,19,0,'Penyesuaian stok',NULL,1,'2026-03-13 03:45:56');

/*Table structure for table `product_variant_options` */

DROP TABLE IF EXISTS `product_variant_options`;

CREATE TABLE `product_variant_options` (
  `variant_id` int NOT NULL,
  `option_id` int NOT NULL,
  PRIMARY KEY (`variant_id`,`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `product_variant_options` */

/*Table structure for table `product_variants` */

DROP TABLE IF EXISTS `product_variants`;

CREATE TABLE `product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `sku` varchar(100) DEFAULT NULL COMMENT 'Kode unik varian',
  `label` varchar(150) NOT NULL COMMENT 'S / Merah-L / dll (auto-generated)',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '0 = pakai harga produk',
  `stock` int NOT NULL DEFAULT '0',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `hpp` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `product_variants` */

insert  into `product_variants`(`id`,`product_id`,`sku`,`label`,`price`,`stock`,`is_active`,`created_at`,`hpp`) values 
(1,10,'SKU-01','S',0.00,9,1,'2026-03-12 14:26:09',0.00),
(6,12,'SKU-02','S',100000.00,10,1,'2026-03-12 15:03:10',0.00),
(7,12,'SKU-03','M',110000.00,6,1,'2026-03-12 15:03:10',0.00),
(11,13,'SKU-03','M',40000.00,10,1,'2026-03-13 03:27:58',30000.00),
(12,15,'SKU-03','S',50000.00,10,1,'2026-03-13 03:28:38',30000.00),
(13,15,'SKU-04','M',60000.00,9,1,'2026-03-13 03:28:38',35000.00);

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

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
  `hpp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `has_variants` tinyint NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '-1' COMMENT '-1 = tidak ditrack, >= 0 = ditrack',
  `stock_min` int NOT NULL DEFAULT '0',
  `hpp_type` enum('manual','auto') NOT NULL DEFAULT 'manual',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_deleted_at` (`deleted_at`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`store_id`,`category_id`,`name`,`description`,`price`,`image`,`is_available`,`created_at`,`hpp`,`has_variants`,`deleted_at`,`stock`,`stock_min`,`hpp_type`) values 
(1,1,1,'Americano','Espresso + air panas',18000.00,NULL,1,'2026-03-03 23:06:39',10800.00,0,NULL,-1,0,'auto'),
(2,1,1,'Cappuccino','Espresso + steamed milk + foam',25000.00,NULL,1,'2026-03-03 23:06:39',15080.00,0,NULL,-1,0,'auto'),
(3,1,2,'Es Kopi Susu','Kopi + susu + es batu',22000.00,NULL,0,'2026-03-03 23:06:39',0.00,0,'2026-03-12 01:15:57',-1,0,'manual'),
(4,1,2,'Cold Brew','Kopi cold brew 12 jam',28000.00,NULL,1,'2026-03-03 23:06:39',0.00,0,NULL,-1,0,'manual'),
(5,1,3,'Matcha Latte','Matcha premium + susu',27000.00,NULL,1,'2026-03-03 23:06:39',21500.00,0,NULL,-1,0,'auto'),
(6,1,4,'Croissant','Croissant butter fresh',20000.00,NULL,1,'2026-03-03 23:06:39',15000.00,0,NULL,9,0,'manual'),
(7,1,1,'Coffee Latte','Kopi Arabica dengan susu segar',25000.00,NULL,1,'2026-03-04 01:07:08',11100.00,0,NULL,-1,0,'auto'),
(11,1,4,'Roti Bakar','',15000.00,NULL,1,'2026-03-12 07:31:19',10000.00,0,NULL,-1,0,'manual');

/*Table structure for table `role_permissions` */

DROP TABLE IF EXISTS `role_permissions`;

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
) ENGINE=InnoDB AUTO_INCREMENT=509 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_permissions` */

insert  into `role_permissions`(`id`,`role_id`,`permission_id`,`created_at`) values 
(1,1,18,'2026-03-04 04:14:42'),
(2,1,7,'2026-03-04 04:14:42'),
(3,1,9,'2026-03-04 04:14:42'),
(4,1,6,'2026-03-04 04:14:42'),
(5,1,8,'2026-03-04 04:14:42'),
(6,1,1,'2026-03-04 04:14:42'),
(7,1,11,'2026-03-04 04:14:42'),
(8,1,10,'2026-03-04 04:14:42'),
(9,1,14,'2026-03-04 04:14:42'),
(10,1,12,'2026-03-04 04:14:42'),
(11,1,13,'2026-03-04 04:14:42'),
(12,1,15,'2026-03-04 04:14:42'),
(13,1,3,'2026-03-04 04:14:42'),
(14,1,5,'2026-03-04 04:14:42'),
(15,1,2,'2026-03-04 04:14:42'),
(16,1,4,'2026-03-04 04:14:42'),
(17,1,17,'2026-03-04 04:14:42'),
(18,1,16,'2026-03-04 04:14:42'),
(19,1,20,'2026-03-04 04:14:42'),
(20,1,19,'2026-03-04 04:14:42'),
(21,2,1,'2026-03-04 04:14:42'),
(22,2,2,'2026-03-04 04:14:42'),
(23,2,3,'2026-03-04 04:14:42'),
(24,2,4,'2026-03-04 04:14:42'),
(25,2,5,'2026-03-04 04:14:42'),
(26,2,6,'2026-03-04 04:14:42'),
(27,2,7,'2026-03-04 04:14:42'),
(28,2,8,'2026-03-04 04:14:42'),
(29,2,9,'2026-03-04 04:14:42'),
(30,2,10,'2026-03-04 04:14:42'),
(31,2,11,'2026-03-04 04:14:42'),
(32,2,12,'2026-03-04 04:14:42'),
(33,2,13,'2026-03-04 04:14:42'),
(34,2,14,'2026-03-04 04:14:42'),
(35,2,16,'2026-03-04 04:14:42'),
(36,2,19,'2026-03-04 04:14:42'),
(37,3,1,'2026-03-04 04:14:42'),
(38,3,2,'2026-03-04 04:14:42'),
(39,3,6,'2026-03-04 04:14:42'),
(40,3,10,'2026-03-04 04:14:42'),
(41,3,11,'2026-03-04 04:14:42'),
(42,3,13,'2026-03-04 04:14:42'),
(43,3,14,'2026-03-04 04:14:42'),
(44,4,1,'2026-03-04 04:14:42'),
(45,4,2,'2026-03-04 04:14:42'),
(46,4,6,'2026-03-04 04:14:42'),
(47,4,10,'2026-03-04 04:14:42'),
(48,4,16,'2026-03-04 04:14:42'),
(49,3,15,'2026-03-05 03:45:39'),
(243,1,23,'2026-03-10 19:20:44'),
(244,1,21,'2026-03-10 19:20:44'),
(245,1,22,'2026-03-10 19:20:44'),
(246,1,24,'2026-03-10 19:20:44'),
(282,1,26,'2026-03-11 20:10:42'),
(290,1,25,'2026-03-11 20:10:42'),
(325,1,27,'2026-03-11 22:32:28'),
(334,1,29,'2026-03-11 22:32:28'),
(343,1,28,'2026-03-11 22:32:28'),
(352,1,30,'2026-03-11 22:32:28'),
(417,16,1,'2026-03-13 07:20:09'),
(418,16,2,'2026-03-13 07:20:09'),
(419,16,3,'2026-03-13 07:20:09'),
(420,16,4,'2026-03-13 07:20:09'),
(421,16,5,'2026-03-13 07:20:09'),
(422,16,6,'2026-03-13 07:20:09'),
(423,16,7,'2026-03-13 07:20:09'),
(424,16,8,'2026-03-13 07:20:09'),
(425,16,9,'2026-03-13 07:20:09'),
(426,16,10,'2026-03-13 07:20:09'),
(427,16,11,'2026-03-13 07:20:09'),
(428,16,12,'2026-03-13 07:20:09'),
(429,16,13,'2026-03-13 07:20:09'),
(430,16,14,'2026-03-13 07:20:09'),
(431,16,15,'2026-03-13 07:20:09'),
(432,16,16,'2026-03-13 07:20:09'),
(433,16,17,'2026-03-13 07:20:09'),
(434,16,18,'2026-03-13 07:20:09'),
(435,16,19,'2026-03-13 07:20:09'),
(436,16,20,'2026-03-13 07:20:09'),
(437,16,21,'2026-03-13 07:20:09'),
(438,16,22,'2026-03-13 07:20:09'),
(439,16,23,'2026-03-13 07:20:09'),
(440,16,24,'2026-03-13 07:20:09'),
(441,16,25,'2026-03-13 07:20:09'),
(442,16,26,'2026-03-13 07:20:09'),
(443,16,27,'2026-03-13 07:20:09'),
(444,16,28,'2026-03-13 07:20:09'),
(445,16,29,'2026-03-13 07:20:09'),
(446,16,30,'2026-03-13 07:20:09'),
(448,17,1,'2026-03-13 07:20:42'),
(449,17,2,'2026-03-13 07:20:42'),
(450,17,3,'2026-03-13 07:20:42'),
(451,17,4,'2026-03-13 07:20:42'),
(452,17,5,'2026-03-13 07:20:42'),
(453,17,6,'2026-03-13 07:20:42'),
(454,17,7,'2026-03-13 07:20:42'),
(455,17,8,'2026-03-13 07:20:42'),
(456,17,9,'2026-03-13 07:20:42'),
(457,17,10,'2026-03-13 07:20:42'),
(458,17,11,'2026-03-13 07:20:42'),
(459,17,12,'2026-03-13 07:20:42'),
(460,17,13,'2026-03-13 07:20:42'),
(461,17,14,'2026-03-13 07:20:42'),
(462,17,15,'2026-03-13 07:20:42'),
(463,17,16,'2026-03-13 07:20:42'),
(464,17,17,'2026-03-13 07:20:42'),
(465,17,18,'2026-03-13 07:20:42'),
(466,17,19,'2026-03-13 07:20:42'),
(467,17,20,'2026-03-13 07:20:42'),
(468,17,21,'2026-03-13 07:20:42'),
(469,17,22,'2026-03-13 07:20:42'),
(470,17,23,'2026-03-13 07:20:42'),
(471,17,24,'2026-03-13 07:20:42'),
(472,17,25,'2026-03-13 07:20:42'),
(473,17,26,'2026-03-13 07:20:42'),
(474,17,27,'2026-03-13 07:20:42'),
(475,17,28,'2026-03-13 07:20:42'),
(476,17,29,'2026-03-13 07:20:42'),
(477,17,30,'2026-03-13 07:20:42'),
(479,18,1,'2026-03-13 07:21:52'),
(480,18,2,'2026-03-13 07:21:52'),
(481,18,3,'2026-03-13 07:21:52'),
(482,18,4,'2026-03-13 07:21:52'),
(483,18,5,'2026-03-13 07:21:52'),
(484,18,6,'2026-03-13 07:21:52'),
(485,18,7,'2026-03-13 07:21:52'),
(486,18,8,'2026-03-13 07:21:52'),
(487,18,9,'2026-03-13 07:21:52'),
(488,18,10,'2026-03-13 07:21:52'),
(489,18,11,'2026-03-13 07:21:52'),
(490,18,12,'2026-03-13 07:21:52'),
(491,18,13,'2026-03-13 07:21:52'),
(492,18,14,'2026-03-13 07:21:52'),
(493,18,15,'2026-03-13 07:21:52'),
(494,18,16,'2026-03-13 07:21:52'),
(495,18,17,'2026-03-13 07:21:52'),
(496,18,18,'2026-03-13 07:21:52'),
(497,18,19,'2026-03-13 07:21:52'),
(498,18,20,'2026-03-13 07:21:52'),
(499,18,21,'2026-03-13 07:21:52'),
(500,18,22,'2026-03-13 07:21:52'),
(501,18,23,'2026-03-13 07:21:52'),
(502,18,24,'2026-03-13 07:21:52'),
(503,18,25,'2026-03-13 07:21:52'),
(504,18,26,'2026-03-13 07:21:52'),
(505,18,27,'2026-03-13 07:21:52'),
(506,18,28,'2026-03-13 07:21:52'),
(507,18,29,'2026-03-13 07:21:52'),
(508,18,30,'2026-03-13 07:21:52');

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`store_id`,`name`,`description`,`is_system`,`created_at`,`updated_at`) values 
(1,1,'Admin','Full system access',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),
(2,1,'Manager','Operational management',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),
(3,1,'Staff','Basic operations and order management',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),
(4,1,'Viewer','Read-only access',1,'2026-03-04 04:14:42','2026-03-04 04:14:42'),
(16,17,'Admin','Administrator toko',1,'2026-03-13 07:20:09','2026-03-13 07:20:09'),
(17,18,'Admin','Administrator toko',1,'2026-03-13 07:20:42','2026-03-13 07:20:42'),
(18,19,'Admin','Administrator toko',1,'2026-03-13 07:21:52','2026-03-13 07:21:52');

/*Table structure for table `stock_logs` */

DROP TABLE IF EXISTS `stock_logs`;

CREATE TABLE `stock_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  `type` enum('in','out','adjustment') NOT NULL,
  `qty` decimal(10,3) NOT NULL,
  `stock_before` decimal(10,3) NOT NULL DEFAULT '0.000',
  `stock_after` decimal(10,3) NOT NULL DEFAULT '0.000',
  `notes` varchar(255) DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `sl_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sl_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `stock_logs` */

insert  into `stock_logs`(`id`,`store_id`,`ingredient_id`,`type`,`qty`,`stock_before`,`stock_after`,`notes`,`order_id`,`created_by`,`created_at`) values 
(3,1,5,'out',50.000,1000.000,950.000,'Pesanan #13',13,1,'2026-03-10 19:57:36'),
(4,1,4,'out',10.000,1000.000,990.000,'Pesanan #13',13,1,'2026-03-10 19:57:36'),
(5,1,5,'adjustment',0.000,950.000,950.000,'rusak',NULL,1,'2026-03-10 20:02:33'),
(6,1,5,'adjustment',860.000,950.000,90.000,'rusak',NULL,1,'2026-03-10 20:02:58'),
(8,1,7,'in',50.000,100.000,150.000,'Stok masuk',NULL,1,'2026-03-12 01:18:10'),
(9,1,5,'out',50.000,90.000,40.000,'Pesanan #17',17,1,'2026-03-12 02:07:45'),
(10,1,7,'out',1.000,150.000,149.000,'Pesanan #17',17,1,'2026-03-12 02:07:45'),
(11,1,4,'out',10.000,990.000,980.000,'Pesanan #17',17,1,'2026-03-12 02:07:45'),
(12,1,5,'out',50.000,40.000,0.000,'Pesanan #18',18,1,'2026-03-12 02:10:29'),
(13,1,7,'out',1.000,149.000,148.000,'Pesanan #18',18,1,'2026-03-12 02:10:29'),
(14,1,4,'out',10.000,980.000,970.000,'Pesanan #18',18,1,'2026-03-12 02:10:29'),
(15,1,5,'in',1000.000,0.000,1000.000,'Stok masuk',NULL,1,'2026-03-12 02:39:01'),
(16,1,5,'out',30.000,1000.000,970.000,'Pesanan #28',28,1,'2026-03-13 07:17:51'),
(17,1,4,'out',150.000,970.000,820.000,'Pesanan #28',28,1,'2026-03-13 07:17:51'),
(18,1,9,'out',5.000,1000.000,995.000,'Pesanan #28',28,1,'2026-03-13 07:17:51');

/*Table structure for table `stores` */

DROP TABLE IF EXISTS `stores`;

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
  `plan` enum('basic','pro','bisnis') NOT NULL DEFAULT 'basic' COMMENT 'Paket toko: basic/pro/bisnis',
  `plan_expires_at` date DEFAULT NULL COMMENT 'NULL = selamanya / tidak ada batas',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `slug_2` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `stores` */

insert  into `stores`(`id`,`name`,`slug`,`niche`,`logo`,`theme_color`,`address`,`description`,`phone`,`is_active`,`created_at`,`plan`,`plan_expires_at`) values 
(1,'Kopi Nusantara','kopi-nusantara','coffee',NULL,'#b45309','Jl. Merdeka No. 10, Banda Aceh',NULL,'0812-0000-0001',1,'2026-03-03 23:06:39','pro','2026-03-31'),
(17,'Coffee Shop Basic','coffee-shop-basic','coffee',NULL,'#3b82f6',NULL,NULL,NULL,1,'2026-03-13 07:20:09','basic',NULL),
(18,'Coffee Shop Pro','coffee-shop-pro','coffee',NULL,'#3b82f6',NULL,NULL,NULL,1,'2026-03-13 07:20:42','pro','2026-03-31'),
(19,'Coffee Shop Bisnis','coffee-shop-bisnis','coffee',NULL,'#3b82f6',NULL,NULL,NULL,1,'2026-03-13 07:21:52','bisnis','2026-03-31');

/*Table structure for table `super_admins` */

DROP TABLE IF EXISTS `super_admins`;

CREATE TABLE `super_admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `super_admins` */

insert  into `super_admins`(`id`,`name`,`username`,`password`,`created_at`) values 
(1,'Super Admin','superadmin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-03-05 04:57:54');

/*Table structure for table `variant_options` */

DROP TABLE IF EXISTS `variant_options`;

CREATE TABLE `variant_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `variant_type_id` int NOT NULL,
  `value` varchar(50) NOT NULL COMMENT 'S / M / L / Merah / dll',
  PRIMARY KEY (`id`),
  KEY `variant_type_id` (`variant_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `variant_options` */

insert  into `variant_options`(`id`,`variant_type_id`,`value`) values 
(1,1,'S | Merah'),
(2,2,'Merah'),
(3,3,'Reguler'),
(4,4,'Large'),
(5,5,'S'),
(6,6,'M');

/*Table structure for table `variant_types` */

DROP TABLE IF EXISTS `variant_types`;

CREATE TABLE `variant_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `store_id` int NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Ukuran / Warna / Rasa',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `variant_types` */

insert  into `variant_types`(`id`,`store_id`,`name`,`created_at`) values 
(1,14,'Ukuran','2026-03-11 20:33:21'),
(2,14,'Warna','2026-03-11 20:43:23'),
(3,1,'Ukuran','2026-03-11 22:03:23'),
(4,1,'Ukuran','2026-03-11 22:03:33'),
(5,15,'Ukuran','2026-03-12 14:07:39'),
(6,15,'Ukuran','2026-03-12 14:07:49');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
