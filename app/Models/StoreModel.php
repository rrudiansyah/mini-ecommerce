<?php

class StoreModel extends Model
{
    protected string $table = 'stores';

    public function findByNiche(string $niche): array
    {
        return $this->where('niche', $niche);
    }

    public function findBySlug(string $slug): array|false
    {
        return $this->queryOne("SELECT * FROM stores WHERE slug = ? AND is_active = 1", [$slug]);
    }

    public function active(): array
    {
        return $this->where('is_active', 1);
    }

    public function allWithStats(): array
    {
        return $this->query(
            "SELECT s.*,
                COUNT(DISTINCT a.id)  AS admin_count,
                COUNT(DISTINCT p.id)  AS product_count,
                COUNT(DISTINCT o.id)  AS order_count,
                COALESCE(SUM(CASE WHEN o.status = 'selesai' THEN o.total ELSE 0 END), 0) AS total_revenue
             FROM stores s
             LEFT JOIN admins a   ON a.store_id = s.id
             LEFT JOIN products p ON p.store_id = s.id
             LEFT JOIN orders o   ON o.store_id = s.id
             GROUP BY s.id
             ORDER BY s.created_at DESC"
        );
    }

    public function generateSlug(string $name): string
    {
        // Buat slug dari nama toko
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        // Cek duplikat, tambah angka jika perlu
        $original = $slug;
        $i = 1;
        while ($this->queryOne("SELECT id FROM stores WHERE slug = ?", [$slug])) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }
}
