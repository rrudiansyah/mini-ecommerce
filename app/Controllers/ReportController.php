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
}
