<?php

class ProductStockModel extends Model
{
    protected string $table = 'products';

    // ── Semua produk HPP Manual milik toko ──────────────
    public function allByStore(int $storeId): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.store_id = ?
               AND (p.hpp_type IS NULL OR p.hpp_type != 'auto')
               AND (p.has_variants = 0 OR p.has_variants IS NULL)
               AND (p.deleted_at IS NULL OR p.deleted_at = '0000-00-00 00:00:00')
             ORDER BY p.name ASC",
            [$storeId]
        );
    }

    // ── Produk stok menipis ──────────────────────────────
    public function lowStock(int $storeId): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.store_id = ?
               AND (p.hpp_type IS NULL OR p.hpp_type != 'auto')
               AND (p.has_variants = 0 OR p.has_variants IS NULL)
               AND p.stock >= 0
               AND p.stock_min > 0
               AND p.stock <= p.stock_min
               AND (p.deleted_at IS NULL OR p.deleted_at = '0000-00-00 00:00:00')
             ORDER BY p.stock ASC",
            [$storeId]
        );
    }

    // ── Tambah stok masuk ────────────────────────────────
    public function stockIn(int $productId, int $qty, string $notes, int $storeId, int $adminId): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT stock FROM products WHERE id = ? AND store_id = ?");
        $stmt->execute([$productId, $storeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $before = (int)$row['stock'];
        $before = $before < 0 ? 0 : $before;
        $after  = $before + $qty;

        $db->prepare("UPDATE products SET stock = ?, is_available = 1 WHERE id = ?")
           ->execute([$after, $productId]);

        $db->prepare(
            "INSERT INTO product_stock_logs
             (store_id, product_id, type, qty, stock_before, stock_after, notes, created_by)
             VALUES (?, ?, 'in', ?, ?, ?, ?, ?)"
        )->execute([$storeId, $productId, $qty, $before, $after, $notes ?: 'Stok masuk', $adminId]);

        return true;
    }

    // ── Kurangi stok (saat order selesai) ────────────────
    public function deductStock(int $productId, int $qty, int $storeId, int $orderId, int $adminId): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || (int)$row['stock'] < 0) return false;

        $before = (int)$row['stock'];
        $after  = max(0, $before - $qty);

        $db->prepare("UPDATE products SET stock = ? WHERE id = ?")->execute([$after, $productId]);

        if ($after === 0) {
            $db->prepare("UPDATE products SET is_available = 0 WHERE id = ?")->execute([$productId]);
        }

        $db->prepare(
            "INSERT INTO product_stock_logs
             (store_id, product_id, type, qty, stock_before, stock_after, notes, order_id, created_by)
             VALUES (?, ?, 'out', ?, ?, ?, ?, ?, ?)"
        )->execute([$storeId, $productId, $qty, $before, $after, 'Pesanan #'.$orderId, $orderId, $adminId]);

        return true;
    }

    // ── Restore stok (saat order dibatalkan) ─────────────
    public function restoreStock(int $productId, int $qty, int $storeId): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || (int)$row['stock'] < 0) return false;

        $before = (int)$row['stock'];
        $after  = $before + $qty;

        $db->prepare("UPDATE products SET stock = ?, is_available = 1 WHERE id = ?")
           ->execute([$after, $productId]);

        return true;
    }

    // ── Penyesuaian stok manual ──────────────────────────
    public function adjust(int $productId, int $newStock, string $notes, int $storeId, int $adminId): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT stock FROM products WHERE id = ? AND store_id = ?");
        $stmt->execute([$productId, $storeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $before = (int)$row['stock'];
        $db->prepare("UPDATE products SET stock = ?, is_available = ? WHERE id = ?")
           ->execute([$newStock, $newStock > 0 ? 1 : 0, $productId]);

        $db->prepare(
            "INSERT INTO product_stock_logs
             (store_id, product_id, type, qty, stock_before, stock_after, notes, created_by)
             VALUES (?, ?, 'adjustment', ?, ?, ?, ?, ?)"
        )->execute([$storeId, $productId, abs($newStock - $before), $before, $newStock,
                    $notes ?: 'Penyesuaian stok', $adminId]);

        return true;
    }

    // ── Riwayat log stok produk ──────────────────────────
    public function logs(int $storeId, int $productId = 0, string $type = '', int $limit = 100): array
    {
        $sql    = "SELECT psl.*, p.name AS product_name, p.stock AS current_stock
                   FROM product_stock_logs psl
                   JOIN products p ON p.id = psl.product_id
                   WHERE psl.store_id = ?";
        $params = [$storeId];

        if ($productId) { $sql .= " AND psl.product_id = ?"; $params[] = $productId; }
        if ($type)      { $sql .= " AND psl.type = ?";        $params[] = $type; }

        $sql .= " ORDER BY psl.created_at DESC LIMIT ?";
        $params[] = $limit;

        $db   = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
