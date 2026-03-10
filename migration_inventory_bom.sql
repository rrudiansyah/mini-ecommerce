-- ============================================================
-- MIGRATION: Inventory BOM + HPP lengkap
-- Jalankan setelah migration_inventory.sql
-- ============================================================

-- Pastikan tabel sudah ada (idempotent)
CREATE TABLE IF NOT EXISTS `ingredients` (
  `id`            int NOT NULL AUTO_INCREMENT,
  `store_id`      int NOT NULL,
  `name`          varchar(150) NOT NULL,
  `unit`          varchar(30)  NOT NULL DEFAULT 'pcs',
  `stock`         decimal(10,3) NOT NULL DEFAULT 0,
  `stock_min`     decimal(10,3) NOT NULL DEFAULT 0,
  `cost_per_unit` decimal(10,2) NOT NULL DEFAULT 0,
  `notes`         text,
  `created_at`    datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_recipes` (
  `id`            int NOT NULL AUTO_INCREMENT,
  `product_id`    int NOT NULL,
  `ingredient_id` int NOT NULL,
  `qty_used`      decimal(10,3) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_recipe` (`product_id`, `ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `pr_ibfk_1` FOREIGN KEY (`product_id`)    REFERENCES `products`    (`id`) ON DELETE CASCADE,
  CONSTRAINT `pr_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `stock_logs` (
  `id`            int NOT NULL AUTO_INCREMENT,
  `store_id`      int NOT NULL,
  `ingredient_id` int NOT NULL,
  `type`          enum('in','out','adjustment') NOT NULL,
  `qty`           decimal(10,3) NOT NULL,
  `stock_before`  decimal(10,3) NOT NULL DEFAULT 0,
  `stock_after`   decimal(10,3) NOT NULL DEFAULT 0,
  `notes`         varchar(255),
  `order_id`      int DEFAULT NULL,
  `created_by`    int DEFAULT NULL,
  `created_at`    datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `sl_ibfk_1` FOREIGN KEY (`store_id`)      REFERENCES `stores`      (`id`) ON DELETE CASCADE,
  CONSTRAINT `sl_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambah kolom HPP ke products (skip jika sudah ada)
ALTER TABLE `products`
  ADD COLUMN IF NOT EXISTS `hpp`      decimal(10,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `hpp_type` enum('manual','auto') NOT NULL DEFAULT 'manual';

-- Permissions baru
INSERT IGNORE INTO `permissions` (`name`, `description`) VALUES
  ('inventory.read',       'Lihat daftar bahan baku & stok'),
  ('inventory.manage',     'Tambah/edit/hapus bahan baku'),
  ('inventory.stock_in',   'Input stok masuk'),
  ('inventory.logs',       'Lihat riwayat stok'),
  ('reports.sales',        'Lihat laporan penjualan');

-- Beri semua permission inventory ke role 'owner' / 'admin' (id=1)
-- Sesuaikan role_id jika berbeda di sistem Anda
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.name IN ('owner','admin','manager')
  AND p.name LIKE 'inventory.%';

-- ============================================================
-- DATA CONTOH: Coffee Shop bahan baku
-- (Uncomment dan jalankan untuk demo)
-- ============================================================
/*
-- Ganti store_id=1 sesuai ID toko Anda
INSERT INTO `ingredients` (store_id, name, unit, stock, stock_min, cost_per_unit, notes) VALUES
  (1, 'Biji Kopi Arabica',  'gr',  500,  50,  350,  'Supplier: Toko Kopi Sejati'),
  (1, 'Biji Kopi Robusta',  'gr',  500,  50,  180,  NULL),
  (1, 'Susu Cair Full Cream','ml', 2000, 200,  15,  '1 liter = Rp 15.000'),
  (1, 'Susu Kental Manis',  'ml',  800, 100,  12,  NULL),
  (1, 'Es Batu',            'gr', 5000, 500,   2,  NULL),
  (1, 'Gula Pasir',         'gr', 2000, 200,   8,  NULL),
  (1, 'Sirup Vanilla',      'ml',  500,  50,  40,  NULL),
  (1, 'Cup 16oz',           'pcs', 100,  20, 800,  NULL),
  (1, 'Cup 12oz',           'pcs', 100,  20, 650,  NULL),
  (1, 'Sedotan',            'pcs', 500,  50,  50,  NULL),
  (1, 'Tutup Cup',          'pcs', 200,  30, 150,  NULL),
  (1, 'Kantong Plastik',    'pcs', 300,  50, 200,  NULL);

-- Contoh resep: Kopi Latte (product_id=1, sesuaikan)
-- INSERT INTO product_recipes (product_id, ingredient_id, qty_used) VALUES
--   (1, 1,  18),   -- 18gr biji kopi arabica
--   (1, 3, 150),   -- 150ml susu cair
--   (1, 5, 200),   -- 200gr es batu
--   (1, 8,   1),   -- 1 cup 16oz
--   (1, 10,  1),   -- 1 sedotan
--   (1, 11,  1);   -- 1 tutup cup
*/
