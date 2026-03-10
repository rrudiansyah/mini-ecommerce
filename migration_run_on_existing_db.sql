-- ============================================================
-- JALANKAN INI jika DB sudah ada dan tidak mau reset container
-- Command: docker exec -i ecommerce_db mysql -u root -psecret ecommerce_builder < migration_run_on_existing_db.sql
-- ============================================================

-- 1. Tabel ingredients
CREATE TABLE IF NOT EXISTS `ingredients` (
  `id`            int NOT NULL AUTO_INCREMENT,
  `store_id`      int NOT NULL,
  `name`          varchar(150) NOT NULL,
  `unit`          varchar(30)  NOT NULL DEFAULT 'pcs',
  `stock`         decimal(10,3) NOT NULL DEFAULT '0.000',
  `stock_min`     decimal(10,3) NOT NULL DEFAULT '0.000',
  `cost_per_unit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes`         text,
  `created_at`    datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel product_recipes
CREATE TABLE IF NOT EXISTS `product_recipes` (
  `id`            int NOT NULL AUTO_INCREMENT,
  `product_id`    int NOT NULL,
  `ingredient_id` int NOT NULL,
  `qty_used`      decimal(10,3) NOT NULL DEFAULT '1.000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_recipe` (`product_id`,`ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `pr_ibfk_1` FOREIGN KEY (`product_id`)    REFERENCES `products`    (`id`) ON DELETE CASCADE,
  CONSTRAINT `pr_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel stock_logs
CREATE TABLE IF NOT EXISTS `stock_logs` (
  `id`            int NOT NULL AUTO_INCREMENT,
  `store_id`      int NOT NULL,
  `ingredient_id` int NOT NULL,
  `type`          enum('in','out','adjustment') NOT NULL,
  `qty`           decimal(10,3) NOT NULL,
  `stock_before`  decimal(10,3) NOT NULL DEFAULT '0.000',
  `stock_after`   decimal(10,3) NOT NULL DEFAULT '0.000',
  `notes`         varchar(255) DEFAULT NULL,
  `order_id`      int DEFAULT NULL,
  `created_by`    int DEFAULT NULL,
  `created_at`    datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `sl_ibfk_1` FOREIGN KEY (`store_id`)      REFERENCES `stores`      (`id`) ON DELETE CASCADE,
  CONSTRAINT `sl_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Kolom HPP di products
ALTER TABLE `products`
  ADD COLUMN IF NOT EXISTS `hpp`      decimal(10,2) NOT NULL DEFAULT '0.00',
  ADD COLUMN IF NOT EXISTS `hpp_type` enum('manual','auto') NOT NULL DEFAULT 'manual';

-- 5. Permissions inventory
INSERT IGNORE INTO `permissions` (`name`, `description`) VALUES
  ('inventory.read',    'Lihat daftar bahan baku & stok'),
  ('inventory.manage',  'Tambah/edit/hapus bahan baku'),
  ('inventory.stock_in','Input stok masuk'),
  ('inventory.logs',    'Lihat riwayat stok');

-- 6. Beri permission inventory ke semua role owner (yang punya >= 18 permission)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT DISTINCT rp.role_id, p.id
FROM role_permissions rp
JOIN permissions p ON p.name LIKE 'inventory.%'
WHERE rp.role_id IN (
    SELECT role_id FROM role_permissions
    GROUP BY role_id HAVING COUNT(*) >= 18
);

SELECT 'Migration selesai!' AS status;
