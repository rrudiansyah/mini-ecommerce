<?php

class OrderModel extends Model
{
    protected string $table = 'orders';

    public function allWithItems(int $storeId): array
    {
        return $this->query(
            "SELECT o.*, COUNT(oi.id) AS total_items
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             WHERE o.store_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC",
            [$storeId]
        );
    }

    public function detail(int $orderId): array
    {
        return $this->query(
            "SELECT oi.*, p.name AS product_name, p.image
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    public function stats(int $storeId): array
    {
        $db = Database::getInstance();
        $totalOrders = $this->count('store_id', $storeId);

        $stmt = $db->prepare("SELECT COALESCE(SUM(total), 0) FROM orders WHERE store_id = ? AND status = 'selesai'");
        $stmt->execute([$storeId]);
        $totalRevenue = $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE store_id = ? AND status = 'pending'");
        $stmt->execute([$storeId]);
        $pendingOrders = $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COALESCE(SUM(total), 0) FROM orders WHERE store_id = ? AND status = 'selesai' AND DATE(created_at) = CURDATE()");
        $stmt->execute([$storeId]);
        $todayRevenue = $stmt->fetchColumn();

        return [
            'total_orders'   => $totalOrders,
            'total_revenue'  => $totalRevenue,
            'pending_orders' => $pendingOrders,
            'today_revenue'  => $todayRevenue,
        ];
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    public function search(int $storeId, ?string $keyword = null, ?string $status = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $sql = "SELECT o.*, COUNT(oi.id) AS total_items
                FROM orders o
                LEFT JOIN order_items oi ON oi.order_id = o.id
                WHERE o.store_id = ?";
        $params = [$storeId];

        if (!empty($keyword)) {
            $sql .= " AND (o.customer_name LIKE ? OR o.customer_phone LIKE ?)";
            $params[] = "%{$keyword}%";
            $params[] = "%{$keyword}%";
        }

        if (!empty($status)) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        if (!empty($dateFrom)) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $sql .= " AND DATE(o.created_at) <= ?";
            $params[] = $dateTo;
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

        return $this->query($sql, $params);
    }
}
