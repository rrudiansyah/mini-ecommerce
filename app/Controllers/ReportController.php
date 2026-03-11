<?php

class ReportController extends Controller
{
    private object $orderModel;
    private object $productModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->orderModel = $this->model('OrderModel');
        $this->productModel = $this->model('ProductModel');
    }

    public function index(): void
    {
        $this->requirePermission('reports.view');
        $storeId = $_SESSION['store_id'];
        $db = Database::getInstance();

        // Get stats
        $stats = $this->orderModel->stats($storeId);

        // Get sales by category
        $stmt = $db->prepare("
            SELECT c.name, COUNT(DISTINCT o.id) as total_orders, COALESCE(SUM(oi.qty), 0) as total_items, COALESCE(SUM(oi.qty * oi.price), 0) as total_revenue
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            LEFT JOIN order_items oi ON oi.product_id = p.id
            LEFT JOIN orders o ON o.id = oi.order_id AND o.status = 'selesai'
            WHERE c.store_id = ?
            GROUP BY c.id
            ORDER BY total_revenue DESC
        ");
        $stmt->execute([$storeId]);
        $categoryStats = $stmt->fetchAll();

        // Get top products
        $stmt = $db->prepare("
            SELECT p.id, p.name, c.name as category, COUNT(DISTINCT o.id) as total_orders, COALESCE(SUM(oi.qty), 0) as total_qty, COALESCE(SUM(oi.qty * oi.price), 0) as total_revenue
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN order_items oi ON oi.product_id = p.id
            LEFT JOIN orders o ON o.id = oi.order_id AND o.status = 'selesai'
            WHERE p.store_id = ?
            GROUP BY p.id
            ORDER BY total_revenue DESC
            LIMIT 10
        ");
        $stmt->execute([$storeId]);
        $topProducts = $stmt->fetchAll();

        $this->view('layouts/main', [
            'pageTitle' => 'Laporan & Analytics',
            'content' => 'reports/index',
            'stats' => $stats,
            'categoryStats' => $categoryStats,
            'topProducts' => $topProducts,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function sales(): void
    {
        $this->requirePermission('reports.sales');
        $storeId = $_SESSION['store_id'];
        $db = Database::getInstance();

        $month = $_GET['month'] ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        // Daily sales for the month
        $stmt = $db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as total_orders, COALESCE(SUM(total), 0) as total_revenue
            FROM orders
            WHERE store_id = ? AND status = 'selesai' AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$storeId, $startDate, $endDate]);
        $dailySales = $stmt->fetchAll();

        // Monthly summary for last 12 months
        $stmt = $db->prepare("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total_orders, COALESCE(SUM(total), 0) as total_revenue
            FROM orders
            WHERE store_id = ? AND status = 'selesai' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month DESC
        ");
        $stmt->execute([$storeId]);
        $monthlySales = $stmt->fetchAll();

        $this->view('layouts/main', [
            'pageTitle' => 'Laporan Penjualan',
            'content' => 'reports/sales',
            'month' => $month,
            'dailySales' => $dailySales,
            'monthlySales' => $monthlySales,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── GET /reports/ingredients — Laporan Penggunaan Bahan ──
    public function ingredients(): void
    {
        $this->requirePermission('inventory.logs');
        $storeId = $_SESSION['store_id'];
        $db      = Database::getInstance();

        $month     = $_GET['month'] ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));

        // Total penggunaan bahan per bahan dalam periode
        $stmt = $db->prepare("
            SELECT
                i.id,
                i.name,
                i.unit,
                i.stock,
                i.stock_min,
                i.cost_per_unit,
                COALESCE(SUM(CASE WHEN sl.type='out' THEN sl.qty ELSE 0 END), 0) AS total_used,
                COALESCE(SUM(CASE WHEN sl.type='in'  THEN sl.qty ELSE 0 END), 0) AS total_in,
                COALESCE(SUM(CASE WHEN sl.type='out' THEN sl.qty * i.cost_per_unit ELSE 0 END), 0) AS total_cost
            FROM ingredients i
            LEFT JOIN stock_logs sl
                ON sl.ingredient_id = i.id
                AND DATE(sl.created_at) BETWEEN ? AND ?
            WHERE i.store_id = ?
            GROUP BY i.id
            ORDER BY total_used DESC
        ");
        $stmt->execute([$startDate, $endDate, $storeId]);
        $usageData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total HPP dari pesanan yang selesai bulan ini
        $stmt2 = $db->prepare("
            SELECT
                COALESCE(SUM(p.hpp * oi.qty), 0) AS total_hpp,
                COALESCE(SUM(oi.qty * oi.price), 0) AS total_revenue,
                COUNT(DISTINCT o.id) AS total_orders
            FROM orders o
            JOIN order_items oi ON oi.order_id = o.id
            JOIN products p ON p.id = oi.product_id
            WHERE o.store_id = ? AND o.status = 'selesai'
              AND DATE(o.created_at) BETWEEN ? AND ?
        ");
        $stmt2->execute([$storeId, $startDate, $endDate]);
        $hppSummary = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Top produk dengan HPP tertinggi
        $stmt3 = $db->prepare("
            SELECT
                p.name,
                p.hpp,
                p.hpp_type,
                p.price,
                COALESCE(SUM(oi.qty), 0) AS total_sold,
                COALESCE(SUM(p.hpp * oi.qty), 0) AS total_hpp_cost,
                COALESCE(SUM(oi.qty * oi.price), 0) AS total_revenue
            FROM products p
            LEFT JOIN order_items oi ON oi.product_id = p.id
            LEFT JOIN orders o ON o.id = oi.order_id
                AND o.status = 'selesai'
                AND DATE(o.created_at) BETWEEN ? AND ?
            WHERE p.store_id = ?
            GROUP BY p.id
            ORDER BY total_hpp_cost DESC
            LIMIT 15
        ");
        $stmt3->execute([$startDate, $endDate, $storeId]);
        $productHpp = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        $this->view('layouts/main', [
            'pageTitle'  => 'Laporan Penggunaan Bahan & HPP',
            'content'    => 'reports/ingredients',
            'month'      => $month,
            'usageData'  => $usageData,
            'hppSummary' => $hppSummary,
            'productHpp' => $productHpp,
            'csrf_field' => $this->csrfField(),
        ]);
    }
}
