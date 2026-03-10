<?php

class OrderController extends Controller
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
        $this->requirePermission('orders.read');
        $keyword = $_GET['keyword'] ?? null;
        $status = $_GET['status'] ?? null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;

        if ($keyword || $status || $dateFrom || $dateTo) {
            $orders = $this->orderModel->search($_SESSION['store_id'], $keyword, $status, $dateFrom, $dateTo);
        } else {
            $orders = $this->orderModel->allWithItems($_SESSION['store_id']);
        }

        $this->view('layouts/main', [
            'pageTitle' => 'Pesanan',
            'content' => 'orders/index',
            'orders' => $orders,
            'filters' => [
                'keyword' => $keyword,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ]
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('orders.create');
        $products = $this->productModel->available($_SESSION['store_id']);
        $this->view('layouts/main', [
            'pageTitle' => 'Buat Pesanan Baru',
            'content' => 'orders/form',
            'products' => $products,
            'order' => null,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->requirePermission('orders.create');
        $customerName = $this->input('customer_name');
        $customerPhone = $this->input('customer_phone');
        $note = $this->input('note');

        // payment inputs (optional during order creation)
        $paymentStatus = $this->input('payment_status') ?? 'unpaid';
        $paymentMethod = trim($this->input('payment_method') ?? '');

        $productIds = is_array($_POST['product_id'] ?? null) ? $_POST['product_id'] : [];
        $quantities = is_array($_POST['qty'] ?? null) ? $_POST['qty'] : [];

        // Validate customer name
        if (empty($customerName)) {
            $this->flash('error', 'Nama pelanggan tidak boleh kosong.');
            $this->redirect('orders/create');
            return;
        }

        // Validate products selected
        if (empty($productIds)) {
            $this->flash('error', 'Pilih minimal 1 produk untuk order.');
            $this->redirect('orders/create');
            return;
        }

        // Prepare order data
        $orderData = [
            'store_id' => $_SESSION['store_id'],
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'note' => $note,
            'total' => 0,
        ];

        // Calculate total and create order
        $db = Database::getInstance();
        $total = 0;
        $orderItems = [];

        foreach ($productIds as $index => $productId) {
            $product = $this->productModel->find((int)$productId);
            if (!$product || !isset($quantities[$index])) continue;

            $qty = (int)$quantities[$index];
            if ($qty <= 0) continue;

            $subtotal = $product['price'] * $qty;
            $total += $subtotal;
            $orderItems[] = [
                'product_id' => (int)$productId,
                'qty' => $qty,
                'price' => $product['price'],
            ];
        }

        if (empty($orderItems) || $total == 0) {
            $this->flash('error', 'Total order harus lebih dari 0.');
            $this->redirect('orders/create');
            return;
        }

        $orderData['total'] = $total;

        // attach initial payment information
        $allowedStatuses = ['unpaid','paid','failed'];
        if (!in_array($paymentStatus, $allowedStatuses)) {
            $paymentStatus = 'unpaid';
        }
        $orderData['payment_status'] = $paymentStatus;
        $methods = ['Tunai','Transfer','Kartu Kredit','Qris'];
        if (!empty($paymentMethod) && !in_array($paymentMethod, $methods)) {
            $paymentMethod = null; // ignore invalid method
        }
        $orderData['payment_method'] = $paymentMethod ?: null;
        if ($paymentStatus === 'paid') {
            $orderData['payment_date'] = date('Y-m-d H:i:s');
        }

        // Create order
        $orderId = $this->orderModel->create($orderData);

        // Create order items
        foreach ($orderItems as $item) {
            $item['order_id'] = $orderId;
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$item['order_id'], $item['product_id'], $item['qty'], $item['price']]);
        }

        $this->flash('success', 'Pesanan berhasil dibuat!');
        // redirect back to detail page and trigger print automatically
        $this->redirect('orders/' . $orderId . '?print=1');
    }

    public function show(string $id): void
    {
        $order = $this->orderModel->find((int)$id);
        $items = $this->orderModel->detail((int)$id);
        if (!$order) { $this->flash('error', 'Pesanan tidak ditemukan.'); $this->redirect('orders'); }
        $this->view('layouts/main', ['pageTitle' => 'Detail Pesanan #' . $id, 'content' => 'orders/show', 'order' => $order, 'items' => $items, 'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function updateStatus(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('orders.update_status');
        $status  = $this->input('status');
        $allowed = ['pending', 'proses', 'selesai', 'batal'];
        if (!in_array($status, $allowed)) $this->json(['error' => 'Status tidak valid.'], 400);

        // Ambil status lama sebelum diupdate
        $order = $this->orderModel->find((int)$id);
        $wasSelesai = ($order && $order['status'] === 'selesai');

        $this->orderModel->updateStatus((int)$id, $status);

        // Kurangi stok otomatis saat pertama kali jadi 'selesai'
        if ($status === 'selesai' && !$wasSelesai) {
            $this->_deductStockForOrder((int)$id);
        }

        $this->flash('success', 'Status pesanan diperbarui.');
        $this->redirect('orders/' . $id);
    }

    // ── Helper: kurangi stok bahan baku sesuai resep ────────
    private function _deductStockForOrder(int $orderId): void
    {
        $storeId   = $_SESSION['store_id'];
        $adminId   = $_SESSION['admin_id'] ?? 0;
        $items     = $this->orderModel->detail($orderId);
        $recipeModel = $this->model('RecipeModel');

        foreach ($items as $item) {
            $recipeModel->deductForOrder(
                (int)$item['product_id'],
                (int)$item['qty'],
                $storeId,
                $orderId,
                $adminId
            );
        }
    }

    public function export(string $format): void
    {
                $this->requirePermission('orders.export');
        $orders = $this->orderModel->allWithItems($_SESSION['store_id']);

        if ($format === 'csv') {
            $this->exportCSV($orders);
        } elseif ($format === 'excel') {
            $this->exportExcel($orders);
        } else {
            $this->flash('error', 'Format export tidak valid.');
            $this->redirect('orders');
        }
    }

    private function exportCSV(array $orders): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d_H-i-s') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Pelanggan', 'No. HP', 'Total Items', 'Total (Rp)', 'Status', 'Waktu'], ';');

        foreach ($orders as $order) {
            fputcsv($output, [
                $order['id'],
                $order['customer_name'],
                $order['customer_phone'] ?? '-',
                $order['total_items'],
                str_replace('.', ',', $order['total']),
                $order['status'],
                date('d/m/Y H:i', strtotime($order['created_at']))
            ], ';');
        }

        fclose($output);
        exit;
    }

    private function exportExcel(array $orders): void
    {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d_H-i-s') . '.xls"');

        echo "ID\tPelanggan\tNo. HP\tTotal Items\tTotal (Rp)\tStatus\tWaktu\n";

        foreach ($orders as $order) {
            echo $order['id'] . "\t";
            echo $order['customer_name'] . "\t";
            echo ($order['customer_phone'] ?? '-') . "\t";
            echo $order['total_items'] . "\t";
            echo str_replace('.', ',', $order['total']) . "\t";
            echo $order['status'] . "\t";
            echo date('d/m/Y H:i', strtotime($order['created_at'])) . "\n";
        }

        exit;
    }

    public function recordPayment(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('orders.record_payment');
        $order = $this->orderModel->find((int)$id);
        if (!$order) {
            $this->flash('error', 'Pesanan tidak ditemukan.');
            $this->redirect('orders');
            return;
        }

        $paymentStatus = trim($this->input('payment_status') ?? '');
        $paymentMethod = trim($this->input('payment_method') ?? '');
        $allowed = ['unpaid', 'paid', 'failed'];
        $methods = ['Tunai','Transfer','Kartu Kredit','Qris'];

        if (empty($paymentStatus) || !in_array($paymentStatus, $allowed)) {
            $this->flash('error', 'Status pembayaran harus dipilih dengan benar.');
            $this->redirect('orders/' . $id);
            return;
        }

        if (!empty($paymentMethod) && !in_array($paymentMethod, $methods)) {
            $this->flash('error', 'Metode pembayaran tidak valid.');
            $this->redirect('orders/' . $id);
            return;
        }

        $data = [
            'payment_status' => $paymentStatus,
            'payment_method' => !empty($paymentMethod) ? $paymentMethod : null,
        ];

        if ($paymentStatus === 'paid') {
            $data['payment_date'] = date('Y-m-d H:i:s');
        }

        $this->orderModel->update((int)$id, $data);
        $this->flash('success', 'Pembayaran pesanan berhasil dicatat!');
        $this->redirect('orders/' . $id);
    }

    public function printInvoice(string $id): void
    {
        $this->requirePermission('orders.view_invoice');
        $order = $this->orderModel->find((int)$id);
        $items = $this->orderModel->detail((int)$id);

        if (!$order) {
            $this->flash('error', 'Pesanan tidak ditemukan.');
            $this->redirect('orders');
            return;
        }

        $storeModel = $this->model('StoreModel');
        $store = $storeModel->find((int)$_SESSION['store_id']);

        $this->view('layouts/blank', [
            'pageTitle' => 'Invoice #' . $id,
            'content' => 'orders/invoice',
            'order' => $order,
            'items' => $items,
            'store' => $store,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function printReceipt(string $id): void
    {
        $this->requirePermission('orders.view_invoice');
        $order = $this->orderModel->find((int)$id);
        $items = $this->orderModel->detail((int)$id);

        if (!$order) {
            $this->flash('error', 'Pesanan tidak ditemukan.');
            $this->redirect('orders');
            return;
        }

        $storeModel = $this->model('StoreModel');
        $store = $storeModel->find((int)$_SESSION['store_id']);

        $this->view('layouts/blank', [
            'pageTitle' => 'Struk #' . $id,
            'content' => 'orders/receipt',
            'order' => $order,
            'items' => $items,
            'store' => $store,
        ]);
    }

    public function countPending(): void
    {
        if (empty($_SESSION['admin_id'])) {
            $this->json(['count' => 0]);
            return;
        }
        $storeId = (int)($_SESSION['store_id'] ?? 0);
        $count = $this->orderModel->countPending($storeId);
        $this->json(['count' => $count]);
    }

    public function offlinePage(): void
    {
        $file = ROOT_PATH . '/public/offline.php';
        if (file_exists($file)) require $file;
        else echo '<h1>Offline</h1><p>Tidak ada koneksi internet.</p>';
        exit;
    }
}

