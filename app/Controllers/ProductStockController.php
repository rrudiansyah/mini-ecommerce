<?php

class ProductStockController extends Controller
{
    private ProductStockModel $stockModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->stockModel = $this->model('ProductStockModel');
    }

    // ── GET /product-stock ───────────────────────────────
    public function index(): void
    {
        $this->requirePermission('inventory.read');

        $storeId  = $_SESSION['store_id'];
        $products = $this->stockModel->allByStore($storeId);
        $lowStock = $this->stockModel->lowStock($storeId);

        $this->view('layouts/main', [
            'pageTitle'  => 'Stok Produk',
            'content'    => 'product_stock/index',
            'products'   => $products,
            'lowStock'   => $lowStock,
            'csrf_field' => $this->csrfField(),
        ]);
    }

    // ── POST /product-stock/stock-in ─────────────────────
    public function stockIn(): void
    {
        $this->validateCsrf();
        $this->requirePermission('inventory.create');

        $storeId   = $_SESSION['store_id'];
        $adminId   = $_SESSION['admin_id'] ?? 0;
        $productId = (int)$this->input('product_id');
        $qty       = (int)$this->input('qty');
        $notes     = trim($this->input('notes', 'Stok masuk'));

        if ($qty <= 0) {
            $this->flash('error', 'Jumlah stok harus lebih dari 0.');
            $this->redirect('product-stock');
            return;
        }

        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT name FROM products WHERE id = ? AND store_id = ?");
        $stmt->execute([$productId, $storeId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $this->flash('error', 'Produk tidak ditemukan.');
            $this->redirect('product-stock');
            return;
        }

        $this->stockModel->stockIn($productId, $qty, $notes, $storeId, $adminId);
        $this->flash('success', 'Stok "' . htmlspecialchars($product['name']) . '" berhasil ditambah ' . $qty . ' pcs.');
        $this->redirect('product-stock');
    }

    // ── POST /product-stock/adjust/{id} ─────────────────
    public function adjust(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('inventory.create');

        $storeId  = $_SESSION['store_id'];
        $adminId  = $_SESSION['admin_id'] ?? 0;
        $newStock = (int)$this->input('new_stock');
        $stockMin = (int)$this->input('stock_min', 0);
        $notes    = trim($this->input('notes', ''));

        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT name FROM products WHERE id = ? AND store_id = ?");
        $stmt->execute([(int)$id, $storeId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            $this->flash('error', 'Produk tidak ditemukan.');
            $this->redirect('product-stock');
            return;
        }

        $db->prepare("UPDATE products SET stock_min = ? WHERE id = ?")->execute([$stockMin, (int)$id]);
        $this->stockModel->adjust((int)$id, $newStock, $notes, $storeId, $adminId);

        $this->flash('success', 'Stok "' . htmlspecialchars($product['name']) . '" disesuaikan menjadi ' . $newStock . ' pcs.');
        $this->redirect('product-stock');
    }

    // ── GET /product-stock/logs ──────────────────────────
    public function logs(): void
    {
        $this->requirePermission('inventory.read');

        $storeId   = $_SESSION['store_id'];
        $productId = (int)($_GET['product_id'] ?? 0);
        $type      = $_GET['type'] ?? '';
        $logs      = $this->stockModel->logs($storeId, $productId, $type);
        $products  = $this->stockModel->allByStore($storeId);

        $this->view('layouts/main', [
            'pageTitle'  => 'Riwayat Stok Produk',
            'content'    => 'product_stock/logs',
            'logs'       => $logs,
            'products'   => $products,
            'filterProd' => $productId,
            'filterType' => $type,
            'csrf_field' => $this->csrfField(),
        ]);
    }
}
