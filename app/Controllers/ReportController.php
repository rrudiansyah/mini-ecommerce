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
    // ── GET /reports/sales/export?month=2026-03&format=excel|pdf ──
    public function exportSales(): void
    {
        $this->requirePermission('reports.sales');
        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        if (!PlanHelper::canFeature('export')) {
            $this->flash('error', PlanHelper::upgradeMessage('export'));
            $this->redirect('reports/sales');
            return;
        }

        $storeId   = $_SESSION['store_id'];
        $format    = $_GET['format'] ?? 'excel';
        $month     = $_GET['month'] ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));
        $db        = Database::getInstance();

        // Ambil data pesanan harian
        $stmt = $db->prepare("
            SELECT
                DATE(o.created_at)                          AS tanggal,
                COUNT(DISTINCT o.id)                        AS total_pesanan,
                COALESCE(SUM(oi.qty), 0)                    AS total_item,
                COALESCE(SUM(o.total), 0)                   AS total_revenue,
                COALESCE(SUM(CASE WHEN o.payment_status='paid' THEN o.total ELSE 0 END), 0) AS revenue_lunas
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.store_id = ? AND o.status = 'selesai'
              AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY DATE(o.created_at)
            ORDER BY tanggal ASC
        ");
        $stmt->execute([$storeId, $startDate, $endDate]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Ambil nama toko
        require_once ROOT_PATH . '/app/Models/StoreModel.php';
        $storeModel = new StoreModel();
        $store = $storeModel->find($storeId);
        $storeName = $store['name'] ?? 'Toko';
        $monthLabel = date('F Y', strtotime($startDate));

        if ($format === 'excel') {
            $filename = 'laporan-penjualan-' . $month . '.xls';
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            echo $storeName . "	Laporan Penjualan " . $monthLabel . "
";
            echo "Diekspor pada: " . date('d/m/Y H:i') . "

";
            echo "Tanggal	Total Pesanan	Total Item	Total Revenue (Rp)	Revenue Lunas (Rp)
";

            $grandTotal = 0;
            $grandOrders = 0;
            foreach ($rows as $r) {
                $grandTotal  += $r['total_revenue'];
                $grandOrders += $r['total_pesanan'];
                echo date('d/m/Y', strtotime($r['tanggal'])) . "	"
                    . $r['total_pesanan'] . "	"
                    . $r['total_item'] . "	"
                    . number_format($r['total_revenue'], 0, ',', '.') . "	"
                    . number_format($r['revenue_lunas'], 0, ',', '.') . "
";
            }
            echo "
TOTAL	" . $grandOrders . "		"
                . number_format($grandTotal, 0, ',', '.') . "
";
            exit;
        }

        // Format PDF — render view khusus print
        $this->view('reports/export_sales_pdf', [
            'pageTitle'  => 'Export Laporan Penjualan',
            'rows'       => $rows,
            'month'      => $month,
            'monthLabel' => $monthLabel,
            'storeName'  => $storeName,
        ]);
    }

    // ── GET /reports/ingredients/export?month=2026-03&format=excel|pdf ──
    public function exportIngredients(): void
    {
        $this->requirePermission('inventory.read');
        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        if (!PlanHelper::canFeature('export')) {
            $this->flash('error', PlanHelper::upgradeMessage('export'));
            $this->redirect('reports/ingredients');
            return;
        }

        $storeId   = $_SESSION['store_id'];
        $format    = $_GET['format'] ?? 'excel';
        $month     = $_GET['month'] ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));
        $db        = Database::getInstance();

        $stmt = $db->prepare("
            SELECT
                i.name, i.unit, i.stock, i.cost_per_unit,
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
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        require_once ROOT_PATH . '/app/Models/StoreModel.php';
        $storeModel = new StoreModel();
        $store = $storeModel->find($storeId);
        $storeName  = $store['name'] ?? 'Toko';
        $monthLabel = date('F Y', strtotime($startDate));

        if ($format === 'excel') {
            $filename = 'laporan-bahan-' . $month . '.xls';
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            echo $storeName . "	Laporan Penggunaan Bahan " . $monthLabel . "
";
            echo "Diekspor pada: " . date('d/m/Y H:i') . "

";
            echo "Nama Bahan	Satuan	Stok Saat Ini	Masuk	Terpakai	Harga/Unit (Rp)	Total Biaya (Rp)
";

            foreach ($rows as $r) {
                echo $r['name'] . "	"
                    . $r['unit'] . "	"
                    . number_format($r['stock'], 2, ',', '.') . "	"
                    . number_format($r['total_in'], 2, ',', '.') . "	"
                    . number_format($r['total_used'], 2, ',', '.') . "	"
                    . number_format($r['cost_per_unit'], 0, ',', '.') . "	"
                    . number_format($r['total_cost'], 0, ',', '.') . "
";
            }
            exit;
        }

        // PDF view
        $this->view('reports/export_ingredients_pdf', [
            'pageTitle'  => 'Export Laporan Bahan',
            'rows'       => $rows,
            'month'      => $month,
            'monthLabel' => $monthLabel,
            'storeName'  => $storeName,
        ]);
    }
    // ── Laporan Stok Varian ───────────────────────────────
    public function variants(): void
    {
        $this->requirePermission('variants.read');
        require_once ROOT_PATH . '/app/Models/VariantModel.php';
        $variantModel = new VariantModel();
        $storeId      = $_SESSION['store_id'];
        $variantStock = $variantModel->stockReportByStore($storeId);

        $this->view('layouts/main', [
            'pageTitle'    => 'Laporan Stok Varian',
            'content'      => 'reports/variants',
            'variantStock' => $variantStock,
            'csrf_field'   => $this->csrfField(),
        ]);
    }
}