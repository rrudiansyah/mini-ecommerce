-- Tambah kolom hpp ke product_variants
ALTER TABLE `product_variants` ADD COLUMN `hpp` decimal(10,2) NOT NULL DEFAULT '0.00';

SELECT 'Migration variant hpp selesai!' AS status;
