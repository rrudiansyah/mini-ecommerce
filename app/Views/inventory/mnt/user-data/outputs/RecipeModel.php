<?php

class RecipeModel extends Model
{
    protected string $table = 'product_recipes';

    // ── Resep lengkap sebuah produk ──────────────────────
    public function byProduct(int $productId): array
    {
        return $this->query(
            "SELECT pr.*, i.name AS ingredient_name, i.unit, i.stock, i.cost_per_unit
             FROM product_recipes pr
             JOIN ingredients i ON pr.ingredient_id = i.id
             WHERE pr.product_id = ?
             ORDER BY i.name ASC",
            [$productId]
        );
    }

    // ── Hitung HPP otomatis dari resep ───────────────────
    public function calcHpp(int $productId): float
    {
        $rows = $this->query(
            "SELECT pr.qty_used, i.cost_per_unit
             FROM product_recipes pr
             JOIN ingredients i ON pr.ingredient_id = i.id
             WHERE pr.product_id = ?",
            [$productId]
        );
        $total = 0.0;
        foreach ($rows as $r) {
            $total += (float)$r['qty_used'] * (float)$r['cost_per_unit'];
        }
        return $total;
    }

    // ── Simpan resep (replace semua, lalu insert baru) ───
    public function saveRecipe(int $productId, array $items): void
    {
        $db = Database::getInstance();
        $db->prepare("DELETE FROM product_recipes WHERE product_id = ?")->execute([$productId]);

        $stmt = $db->prepare(
            "INSERT INTO product_recipes (product_id, ingredient_id, qty_used) VALUES (?, ?, ?)"
        );
        foreach ($items as $item) {
            if (!empty($item['ingredient_id']) && isset($item['qty_used']) && $item['qty_used'] > 0) {
                $stmt->execute([$productId, (int)$item['ingredient_id'], (float)$item['qty_used']]);
            }
        }
    }

    // ── Kurangi stok semua bahan sesuai resep x qty pesanan ──
    public function deductForOrder(int $productId, int $qtyOrdered, int $storeId, int $orderId, int $adminId): void
    {
        $ingredientModel = new IngredientModel();
        $items = $this->byProduct($productId);
        foreach ($items as $item) {
            $used = (float)$item['qty_used'] * $qtyOrdered;
            $ingredientModel->deductStock((int)$item['ingredient_id'], $used, $storeId, $orderId, $adminId);
        }
    }

    // ── Cek apakah stok semua bahan cukup ────────────────
    public function checkStock(int $productId, int $qty = 1): array
    {
        $items = $this->byProduct($productId);
        $issues = [];
        foreach ($items as $item) {
            $needed = (float)$item['qty_used'] * $qty;
            if ((float)$item['stock'] < $needed) {
                $issues[] = [
                    'ingredient' => $item['ingredient_name'],
                    'needed'     => $needed,
                    'stock'      => $item['stock'],
                    'unit'       => $item['unit'],
                ];
            }
        }
        return $issues;
    }
}
