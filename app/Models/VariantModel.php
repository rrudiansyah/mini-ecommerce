<?php

class VariantModel extends Model
{
    protected string $table = 'product_variants';

    // ── Semua varian milik produk ─────────────────────────
    public function byProduct(int $productId): array
    {
        return $this->query(
            "SELECT pv.*, GROUP_CONCAT(vo.value ORDER BY vt.name SEPARATOR ' / ') AS options_label
             FROM product_variants pv
             LEFT JOIN product_variant_options pvo ON pvo.variant_id = pv.id
             LEFT JOIN variant_options vo ON vo.id = pvo.option_id
             LEFT JOIN variant_types vt ON vt.id = vo.variant_type_id
             WHERE pv.product_id = ?
             GROUP BY pv.id
             ORDER BY pv.id ASC",
            [$productId]
        );
    }

    // ── Tipe varian milik toko ────────────────────────────
    public function typesByStore(int $storeId): array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT vt.*, GROUP_CONCAT(vo.id, ':', vo.value ORDER BY vo.id SEPARATOR '|') AS options
             FROM variant_types vt
             LEFT JOIN variant_options vo ON vo.variant_type_id = vt.id
             WHERE vt.store_id = ?
             GROUP BY vt.id
             ORDER BY vt.name ASC"
        );
        $stmt->execute([$storeId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse options string jadi array
        foreach ($rows as &$row) {
            $row['options_list'] = [];
            if (!empty($row['options'])) {
                foreach (explode('|', (string)$row['options']) as $opt) {
                    if (!str_contains($opt, ':')) continue;
                    [$id, $val] = explode(':', $opt, 2);
                    $row['options_list'][] = ['id' => (int)$id, 'value' => (string)$val];
                }
            }
        }
        return $rows;
    }

    // ── Simpan tipe varian + opsinya ──────────────────────
    public function saveType(int $storeId, string $name, array $options): int
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO variant_types (store_id, name) VALUES (?, ?)");
        $stmt->execute([$storeId, $name]);
        $typeId = (int)$db->lastInsertId();

        foreach ($options as $opt) {
            $opt = trim($opt);
            if ($opt) {
                $db->prepare("INSERT INTO variant_options (variant_type_id, value) VALUES (?, ?)")
                   ->execute([$typeId, $opt]);
            }
        }
        return $typeId;
    }

    // ── Update tipe varian ────────────────────────────────
    public function updateType(int $typeId, string $name, array $options): void
    {
        $db = Database::getInstance();
        $db->prepare("UPDATE variant_types SET name = ? WHERE id = ?")->execute([$name, $typeId]);

        // Hapus opsi lama, insert baru
        $db->prepare("DELETE FROM variant_options WHERE variant_type_id = ?")->execute([$typeId]);
        foreach ($options as $opt) {
            $opt = trim($opt);
            if ($opt) {
                $db->prepare("INSERT INTO variant_options (variant_type_id, value) VALUES (?, ?)")
                   ->execute([$typeId, $opt]);
            }
        }
    }

    // ── Hapus tipe varian ─────────────────────────────────
    public function deleteType(int $typeId): void
    {
        $db = Database::getInstance();
        $db->prepare("DELETE FROM variant_options WHERE variant_type_id = ?")->execute([$typeId]);
        $db->prepare("DELETE FROM variant_types WHERE id = ?")->execute([$typeId]);
    }

    // ── Simpan semua varian produk ────────────────────────
    public function saveVariants(int $productId, array $variants): void
    {
        $db = Database::getInstance();

        // Hapus varian lama
        $oldVariants = $this->byProduct($productId);
        foreach ($oldVariants as $old) {
            $db->prepare("DELETE FROM product_variant_options WHERE variant_id = ?")->execute([$old['id']]);
        }
        $db->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$productId]);

        foreach ($variants as $v) {
            $label   = trim($v['label'] ?? '');
            $price   = (float)($v['price'] ?? 0);
            $stock   = (int)($v['stock'] ?? 0);
            $sku     = trim($v['sku'] ?? '') ?: null;
            $options = $v['option_ids'] ?? [];

            if (!$label) continue;

            $stmt = $db->prepare(
                "INSERT INTO product_variants (product_id, sku, label, price, stock, hpp) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $hpp = (float)($v['hpp'] ?? 0);
            $stmt->execute([$productId, $sku, $label, $price, $stock, $hpp]);
            $variantId = (int)$db->lastInsertId();

            foreach ($options as $optId) {
                $db->prepare("INSERT INTO product_variant_options (variant_id, option_id) VALUES (?, ?)")
                   ->execute([$variantId, (int)$optId]);
            }
        }
    }

    // ── Total stok semua varian produk ────────────────────
    public function totalStock(int $productId): int
    {
        $row = $this->queryOne(
            "SELECT COALESCE(SUM(stock), 0) AS total FROM product_variants WHERE product_id = ? AND is_active = 1",
            [$productId]
        );
        return (int)($row['total'] ?? 0);
    }

    // ── Kurangi stok varian saat pesanan ─────────────────
    public function deductStock(int $variantId, int $qty): bool
    {
        $db   = Database::getInstance();
        $row  = $this->queryOne("SELECT stock FROM product_variants WHERE id = ?", [$variantId]);
        if (!$row) return false;

        $newStock = max(0, (int)$row['stock'] - $qty);
        $db->prepare("UPDATE product_variants SET stock = ? WHERE id = ?")->execute([$newStock, $variantId]);
        return true;
    }

    // ── Cari varian berdasarkan opsi yang dipilih ─────────
    public function findByOptions(int $productId, array $optionIds): ?array
    {
        if (empty($optionIds)) return null;
        $db      = Database::getInstance();
        $count   = count($optionIds);
        $in      = implode(',', array_map('intval', $optionIds));

        $stmt = $db->prepare(
            "SELECT pv.id FROM product_variants pv
             WHERE pv.product_id = ?
               AND (SELECT COUNT(*) FROM product_variant_options pvo
                    WHERE pvo.variant_id = pv.id AND pvo.option_id IN ($in)) = ?
             LIMIT 1"
        );
        $stmt->execute([$productId, $count]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return $this->find((int)$row['id']);
    }
    // ── Stok semua varian milik toko (untuk laporan) ──────
    public function stockReportByStore(int $storeId): array
    {
        return $this->query(
            "SELECT pv.id, pv.label, pv.stock, pv.sku, pv.price,
                    p.id AS product_id, p.name AS product_name, p.price AS base_price,
                    c.name AS category_name
             FROM product_variants pv
             JOIN products p ON p.id = pv.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.store_id = ? AND pv.is_active = 1
             ORDER BY p.name ASC, pv.label ASC",
            [$storeId]
        );
    }

    // ── Varian dengan stok rendah (≤ threshold) ───────────
    public function lowStock(int $storeId, int $threshold = 5): array
    {
        return $this->query(
            "SELECT pv.id, pv.label, pv.stock,
                    p.id AS product_id, p.name AS product_name
             FROM product_variants pv
             JOIN products p ON p.id = pv.product_id
             WHERE p.store_id = ? AND pv.is_active = 1 AND pv.stock <= ?
             ORDER BY pv.stock ASC, p.name ASC",
            [$storeId, $threshold]
        );
    }

    // ── Update stok langsung (untuk adjustment) ───────────
    public function updateStock(int $variantId, int $stock): bool
    {
        $db = Database::getInstance();
        return (bool)$db->prepare("UPDATE product_variants SET stock = ? WHERE id = ?")
                        ->execute([$stock, $variantId]);
    }
}