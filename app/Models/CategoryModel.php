<?php

class CategoryModel extends Model
{
    protected string $table = 'categories';

    public function byStore(int $storeId): array
    {
        return $this->where('store_id', $storeId);
    }

    public function withProductCount(int $storeId): array
    {
        return $this->query(
            "SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             WHERE c.store_id = ?
             GROUP BY c.id
             ORDER BY c.name",
            [$storeId]
        );
    }
}
