<?php

class IngredientModel extends Model
{
    protected string $table = 'ingredients';

    // ── Semua bahan milik toko ───────────────────────────
    public function allByStore(int $storeId): array
    {
        return $this->query(
            "SELECT * FROM ingredients WHERE store_id = ? ORDER BY name ASC",
            [$storeId]
        );
    }

    // ── Bahan yang stoknya di bawah minimum (untuk alert) ─
    public function lowStock(int $storeId): array
    {
        return $this->query(
            "SELECT * FROM ingredients
             WHERE store_id = ? AND stock_min > 0 AND stock <= stock_min
             ORDER BY name ASC",
            [$storeId]
        );
    }

    // ── Kurangi stok (dipanggil saat pesanan selesai) ────
    public function deductStock(int $id, float $qty, int $storeId, int $orderId, int $adminId): bool
    {
        $db = Database::getInstance();

        // ambil stok sebelumnya
        $stmt = $db->prepare("SELECT stock FROM ingredients WHERE id = ? AND store_id = ?");
        $stmt->execute([$id, $storeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $before = (float)$row['stock'];
        $after  = max(0, $before - $qty);

        // update stok
        $db->prepare("UPDATE ingredients SET stock = ? WHERE id = ?")->execute([$after, $id]);

        // catat log
        $db->prepare(
            "INSERT INTO stock_logs (store_id, ingredient_id, type, qty, stock_before, stock_after, notes, order_id, created_by)
             VALUES (?, ?, 'out', ?, ?, ?, ?, ?, ?)"
        )->execute([$storeId, $id, $qty, $before, $after, 'Pesanan #'.$orderId, $orderId, $adminId]);

        return true;
    }

    // ── Tambah stok masuk ────────────────────────────────
    public function stockIn(int $id, float $qty, string $notes, int $storeId, int $adminId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT stock FROM ingredients WHERE id = ? AND store_id = ?");
        $stmt->execute([$id, $storeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $before = (float)$row['stock'];
        $after  = $before + $qty;

        $db->prepare("UPDATE ingredients SET stock = ? WHERE id = ?")->execute([$after, $id]);
        $db->prepare(
            "INSERT INTO stock_logs (store_id, ingredient_id, type, qty, stock_before, stock_after, notes, created_by)
             VALUES (?, ?, 'in', ?, ?, ?, ?, ?)"
        )->execute([$storeId, $id, $qty, $before, $after, $notes, $adminId]);

        return true;
    }

    // ── Penyesuaian stok (adjustment) ───────────────────
    public function adjust(int $id, float $newStock, string $notes, int $storeId, int $adminId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT stock FROM ingredients WHERE id = ? AND store_id = ?");
        $stmt->execute([$id, $storeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $before = (float)$row['stock'];
        $db->prepare("UPDATE ingredients SET stock = ? WHERE id = ?")->execute([$newStock, $id]);
        $db->prepare(
            "INSERT INTO stock_logs (store_id, ingredient_id, type, qty, stock_before, stock_after, notes, created_by)
             VALUES (?, ?, 'adjustment', ?, ?, ?, ?, ?)"
        )->execute([$storeId, $id, abs($newStock - $before), $before, $newStock, $notes ?: 'Penyesuaian stok', $adminId]);

        return true;
    }
}
