-- ============================================================
-- MIGRATION: Sistem Paket (Basic / Pro / Bisnis)
-- Jalankan di: phpMyAdmin atau docker exec mysql
-- ============================================================

ALTER TABLE `stores`
  ADD COLUMN `plan`            ENUM('basic','pro','bisnis') NOT NULL DEFAULT 'basic'
    COMMENT 'Paket toko: basic/pro/bisnis',
  ADD COLUMN `plan_expires_at` DATE DEFAULT NULL
    COMMENT 'NULL = selamanya / tidak ada batas';

-- Semua toko yang ada default ke basic
UPDATE `stores` SET `plan` = 'basic' WHERE `plan` IS NULL OR `plan` = '';

SELECT id, name, plan, plan_expires_at FROM stores;
SELECT 'Migration paket selesai!' AS status;
