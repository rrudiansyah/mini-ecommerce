-- ═══════════════════════════════════════════════════════
-- Migration: Multi-tenant
-- Jalankan via phpMyAdmin atau:
-- docker compose exec db mysql -u root -psecret ecommerce_builder < migration_multitenant.sql
-- ═══════════════════════════════════════════════════════

-- 1. Tambah kolom slug ke stores (untuk URL publik: /toko/{slug})
ALTER TABLE stores
    ADD COLUMN slug VARCHAR(100) UNIQUE NULL AFTER name,
    ADD COLUMN description TEXT NULL AFTER address;

-- Generate slug dari nama toko yang sudah ada
UPDATE stores SET slug = LOWER(REPLACE(REPLACE(REPLACE(name, ' ', '-'), '.', ''), ',', '')) WHERE slug IS NULL;

-- Jadikan NOT NULL setelah terisi
ALTER TABLE stores MODIFY COLUMN slug VARCHAR(100) UNIQUE NOT NULL;

-- 2. Tambah tabel super_admins (pemilik sistem, bukan pemilik toko)
CREATE TABLE IF NOT EXISTS super_admins (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    username   VARCHAR(50) UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT NOW()
);

-- Seed super admin (password: superadmin123)
INSERT INTO super_admins (name, username, password) VALUES
('Super Admin', 'superadmin', '$2y$10$TKh8H1.PfunktKOHhivR5etLVCEHCVB5HXCABT5yVFCUmEVtHQkF.');

-- 3. Tambah kolom is_active ke admins (untuk disable user per toko)
ALTER TABLE admins
    ADD COLUMN is_active TINYINT DEFAULT 1 AFTER password;
