-- Tambah kolom variant ke order_items
ALTER TABLE `order_items` ADD COLUMN `variant_id`    INT DEFAULT NULL;
ALTER TABLE `order_items` ADD COLUMN `variant_label` VARCHAR(100) DEFAULT NULL;

SELECT 'Migration selesai!' AS STATUS;
