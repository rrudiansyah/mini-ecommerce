-- Tambah kolom yang kurang di tabel products VPS

ALTER TABLE `products`
  ADD COLUMN `stock`        int            NOT NULL DEFAULT '-1'   AFTER `is_available`,
  ADD COLUMN `stock_min`    int            NOT NULL DEFAULT '0'    AFTER `stock`,
  ADD COLUMN `unit`         varchar(20)    NOT NULL DEFAULT 'pcs'  AFTER `stock_min`,
  ADD COLUMN `has_variants` tinyint(1)     NOT NULL DEFAULT '0'    AFTER `unit`,
  ADD COLUMN `deleted_at`   datetime       DEFAULT NULL            AFTER `created_at`;

-- Verifikasi
DESCRIBE products;
