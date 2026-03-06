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
             WHERE p.store_id = ?
             ORDER BY p.id DESC",
            [$storeId]
        );
    }

    public function available(int $storeId): array
    {
        return $this->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.store_id = ? AND p.is_available = 1
             ORDER BY c.name, p.name",
            [$storeId]
        );
    }

    public function toggleAvailable(int $id): bool
    {
        $stmt = Database::getInstance()->prepare(
            "UPDATE products SET is_available = NOT is_available WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
}
