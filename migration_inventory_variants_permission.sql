-- ============================================================
-- Fix: Tambah permission inventory & variants yang belum ada
-- ============================================================

-- Tambah permission inventory
INSERT IGNORE INTO `permissions` (`name`, `description`) VALUES
  ('inventory.read',   'Lihat stok bahan baku'),
  ('inventory.create', 'Tambah & kelola stok bahan baku'),
  ('inventory.update', 'Edit bahan baku'),
  ('inventory.delete', 'Hapus bahan baku');

-- Tambah permission variants
INSERT IGNORE INTO `permissions` (`name`, `description`) VALUES
  ('variants.read',   'Lihat varian produk'),
  ('variants.manage', 'Kelola varian produk');

-- Tambah permission reports export
INSERT IGNORE INTO `permissions` (`name`, `description`) VALUES
  ('reports.export', 'Export laporan ke Excel/PDF');

-- Assign semua permission baru ke role Admin (semua toko)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.name IN (
  'inventory.read','inventory.create','inventory.update','inventory.delete',
  'variants.read','variants.manage','reports.export'
)
WHERE r.name = 'Admin';

-- Verifikasi
SELECT p.name, COUNT(rp.role_id) AS assigned_roles
FROM permissions p
LEFT JOIN role_permissions rp ON rp.permission_id = p.id
WHERE p.name LIKE 'inventory.%' OR p.name LIKE 'variants.%' OR p.name = 'reports.export'
GROUP BY p.id, p.name
ORDER BY p.name;
