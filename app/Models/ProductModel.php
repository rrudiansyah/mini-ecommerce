<?php

class ProductModel extends Model
{
    protected string $table = 'products';

    public function allWithCategory(int $storeId): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.store_id = ? AND p.deleted_at IS NULL
             ORDER BY p.id DESC",
            [$storeId]
        );
    }

    public function available(int $storeId): array
    {
        // stock = -1 berarti tidak ditrack (selalu tampil)
        // stock = 0  berarti habis (sembunyikan)
        // stock > 0  berarti tersedia
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.store_id = ? AND p.is_available = 1
               AND p.deleted_at IS NULL
               AND (p.stock = -1 OR p.stock > 0)
             ORDER BY c.name, p.name",
            [$storeId]
        );
    }

    // Kurangi stok produk (untuk HPP manual saat order selesai)
    public function deductStock(int $productId, int $qty): void
    {
        $db = Database::getInstance();
        $db->prepare(
            "UPDATE products SET stock = GREATEST(-1, stock - ?)
             WHERE id = ? AND stock > 0"
        )->execute([$qty, $productId]);

        // Auto nonaktif jika stok = 0
        $db->prepare(
            "UPDATE products SET is_available = 0
             WHERE id = ? AND stock = 0"
        )->execute([$productId]);
    }

    // Restore stok saat order dibatalkan
    public function restoreStock(int $productId, int $qty): void
    {
        $db = Database::getInstance();
        $db->prepare(
            "UPDATE products SET stock = stock + ?, is_available = 1
             WHERE id = ? AND stock >= 0"
        )->execute([$qty, $productId]);
    }

    public function toggleAvailable(int $id): bool
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE products SET is_available = NOT is_available WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
    // Soft delete — tandai deleted_at, jangan hapus fisik
    public function softDelete(int $id): bool
    {
        $db   = Database::getInstance();
        // Cek apakah pernah dipesan
        $stmt = $db->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
        $stmt->execute([$id]);
        $hasOrders = (int)$stmt->fetchColumn() > 0;

        if ($hasOrders) {
            // Soft delete: sembunyikan dari tampilan
            $db->prepare("UPDATE products SET deleted_at = NOW(), is_available = 0 WHERE id = ?")
               ->execute([$id]);
            return true;
        }

        // Tidak pernah dipesan — hapus fisik + data terkait
        $db->prepare("DELETE FROM recipe_items WHERE product_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM product_variant_options pvo
                      USING product_variant_options pvo
                      JOIN product_variants pv ON pv.id = pvo.variant_id
                      WHERE pv.product_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
        return true;
    }
}