<?php

class OrderApiController extends ApiController
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];

        $orderModel = $this->model('OrderModel');

        $keyword  = $_GET['keyword']   ?? null;
        $status   = $_GET['status']    ?? null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo   = $_GET['date_to']   ?? null;

        if ($keyword || $status || $dateFrom || $dateTo) {
            $orders = $orderModel->search($storeId, $keyword, $status, $dateFrom, $dateTo);
        } else {
            $orders = $orderModel->allWithItems($storeId);
        }

        $this->success(array_map(function($order) {
            return [
                'id'             => $order['id'],
                'customer_name'  => $order['customer_name'],
                'customer_phone' => $order['customer_phone'] ?? null,
                'total'          => (float) $order['total'],
                'status'         => $order['status'],
                'payment_status' => $order['payment_status'] ?? 'unpaid',
                'payment_method' => $order['payment_method'] ?? null,
                'note'           => $order['note'] ?? null,
                'total_items'    => (int) $order['total_items'],
                'created_at'     => $order['created_at'],
            ];
        }, $orders));
    }

    public function show(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];
        $orderId  = (int) ($_GET['id'] ?? 0);

        if (!$orderId) {
            $this->error('ID pesanan tidak valid.');
        }

        $orderModel = $this->model('OrderModel');
        $order      = $orderModel->find($orderId);

        if (!$order || (int)$order['store_id'] !== $storeId) {
            $this->error('Pesanan tidak ditemukan.', 404);
        }

        $items = $orderModel->detail($orderId);

        $this->success([
            'id'             => $order['id'],
            'customer_name'  => $order['customer_name'],
            'customer_phone' => $order['customer_phone'] ?? null,
            'total'          => (float) $order['total'],
            'status'         => $order['status'],
            'payment_status' => $order['payment_status'] ?? 'unpaid',
            'payment_method' => $order['payment_method'] ?? null,
            'note'           => $order['note'] ?? null,
            'created_at'     => $order['created_at'],
            'items'          => array_map(function($item) {
                return [
                    'id'           => $item['id'],
                    'product_name' => $item['product_name'],
                    'qty'          => (int) $item['qty'],
                    'price'        => (float) $item['price'],
                    'subtotal'     => (float) ($item['qty'] * $item['price']),
                ];
            }, $items),
        ]);
    }

    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];
        $orderId  = (int) ($_GET['id'] ?? 0);

        if (!$orderId) {
            $this->error('ID pesanan tidak valid.');
        }

        $body   = $this->getBody();
        $status = trim($body['status'] ?? '');

        $allowed = ['pending', 'proses', 'selesai', 'batal'];
        if (!in_array($status, $allowed)) {
            $this->error('Status tidak valid. Pilihan: ' . implode(', ', $allowed));
        }

        $orderModel = $this->model('OrderModel');
        $order      = $orderModel->find($orderId);

        if (!$order || (int)$order['store_id'] !== $storeId) {
            $this->error('Pesanan tidak ditemukan.', 404);
        }

        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $orderModel->updateStatus($orderId, $status);

            // Kurangi stok saat status berubah menjadi selesai
            if ($status === 'selesai') {
                $items = $orderModel->detail($orderId);
                foreach ($items as $item) {
                    $db->prepare("
                        UPDATE products
                        SET stock = GREATEST(-1, stock - ?)
                        WHERE id = ? AND stock > 0
                    ")->execute([$item['qty'], $item['product_id']]);

                    $db->prepare("
                        UPDATE products
                        SET is_available = 0
                        WHERE id = ? AND stock = 0
                    ")->execute([$item['product_id']]);
                }
            }

            // Kembalikan stok jika pesanan dibatalkan
            if ($status === 'batal') {
                $items = $orderModel->detail($orderId);
                foreach ($items as $item) {
                    $db->prepare("
                        UPDATE products
                        SET stock = stock + ?
                        WHERE id = ? AND stock >= 0
                    ")->execute([$item['qty'], $item['product_id']]);

                    $db->prepare("
                        UPDATE products
                        SET is_available = 1
                        WHERE id = ? AND stock > 0
                    ")->execute([$item['product_id']]);
                }
            }

            $db->commit();
            $this->success(null, 'Status pesanan berhasil diupdate.');

        } catch (\Throwable $e) {
            $db->rollBack();
            $this->error($e->getMessage() . ' di ' . $e->getFile() . ' baris ' . $e->getLine(), 500);
        }
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];

        $body          = $this->getBody();
        $customerName  = trim($body['customer_name']  ?? '');
        $customerPhone = trim($body['customer_phone'] ?? '');
        $paymentMethod = trim($body['payment_method'] ?? '');
        $note          = trim($body['note']           ?? '');
        $items         = $body['items']               ?? [];

        // Validasi
        if (!$customerName) {
            $this->error('Nama pelanggan wajib diisi.');
        }
        if (empty($items)) {
            $this->error('Pilih minimal satu produk.');
        }

        // Validasi setiap item
        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['qty']) || $item['qty'] < 1) {
                $this->error('Data produk tidak valid.');
            }
        }

        $productModel = $this->model('ProductModel');
        $orderModel   = $this->model('OrderModel');
        $db           = Database::getInstance();

        // Hitung total & validasi produk
        $total        = 0;
        $orderItems   = [];

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $qty       = (int) $item['qty'];
            $product   = $productModel->find($productId);

            if (!$product || (int)$product['store_id'] !== $storeId) {
                $this->error("Produk ID {$productId} tidak ditemukan.");
            }
            if (!$product['is_available']) {
                $this->error("Produk \"{$product['name']}\" tidak tersedia.");
            }

            $subtotal    = $product['price'] * $qty;
            $total      += $subtotal;
            $orderItems[] = [
                'product_id' => $productId,
                'qty'        => $qty,
                'price'      => $product['price'],
                'subtotal'   => $subtotal,
            ];
        }

        // Simpan order dalam transaksi
        try {
            $db->beginTransaction();

            // Insert order
            $stmt = $db->prepare("
                INSERT INTO orders
                    (store_id, customer_name, customer_phone, total,
                    status, payment_method, payment_status, note, created_at)
                VALUES (?, ?, ?, ?, 'pending', ?, 'unpaid', ?, NOW())
            ");
            $stmt->execute([
                $storeId, $customerName, $customerPhone,
                $total, $paymentMethod, $note,
            ]);
            $orderId = (int) $db->lastInsertId();

            // Insert order items
            foreach ($orderItems as $item) {
                $stmt = $db->prepare("
                    INSERT INTO order_items
                        (order_id, product_id, qty, price)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['qty'],
                    $item['price'],
                ]);
            }

            $db->commit();

            $this->success([
                'order_id' => $orderId,
                'total'    => $total,
                'status'   => 'pending',
            ], 'Pesanan berhasil dibuat.');

        } catch (\Throwable $e) {
            $db->rollBack();
            $this->error($e->getMessage() . ' di ' . $e->getFile() . ' baris ' . $e->getLine(), 500);
        }
    }

    public function updatePaymentStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];
        $orderId  = (int) ($_GET['id'] ?? 0);

        if (!$orderId) $this->error('ID pesanan tidak valid.');

        $body          = $this->getBody();
        $paymentStatus = trim($body['payment_status'] ?? '');
        $paymentMethod = trim($body['payment_method'] ?? '');

        $allowed = ['unpaid', 'paid', 'failed'];
        if (!in_array($paymentStatus, $allowed)) {
            $this->error('Status pembayaran tidak valid. Pilihan: ' . implode(', ', $allowed));
        }

        $orderModel = $this->model('OrderModel');
        $order      = $orderModel->find($orderId);

        if (!$order || (int)$order['store_id'] !== $storeId) {
            $this->error('Pesanan tidak ditemukan.', 404);
        }

        $updateData = ['payment_status' => $paymentStatus];

        // Catat tanggal pembayaran jika paid
        if ($paymentStatus === 'paid') {
            $updateData['payment_date'] = date('Y-m-d H:i:s');
        }

        // Update metode pembayaran jika dikirim
        if ($paymentMethod) {
            $updateData['payment_method'] = $paymentMethod;
        }

        $orderModel->update($orderId, $updateData);

        $this->success(
            ['payment_status' => $paymentStatus],
            'Status pembayaran berhasil diupdate.'
        );
    }
}