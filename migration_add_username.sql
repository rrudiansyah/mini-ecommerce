-- Migration: Tambah kolom username ke tabel admins
-- Jalankan: docker compose exec db mysql -u root -psecret ecommerce_builder < migration_add_username.sql

ALTER TABLE admins
    ADD COLUMN username VARCHAR(50) UNIQUE NULL AFTER name;

-- Set username dari bagian sebelum @ pada email yang sudah ada
UPDATE admins SET username = SUBSTRING_INDEX(email, '@', 1) WHERE username IS NULL;

-- Setelah data terisi, jadikan NOT NULL
ALTER TABLE admins MODIFY COLUMN username VARCHAR(50) UNIQUE NOT NULL;

-- Update seed admin agar punya username eksplisit
UPDATE admins SET username = 'admin' WHERE email = 'admin@kopinusantara.com';
