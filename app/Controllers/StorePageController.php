<?php

class StorePageController extends Controller
{
    // Halaman publik toko berdasarkan slug: /toko/{slug}
    public function show(string $slug): void
    {
        $storeModel = $this->model('StoreModel');
        $store = $storeModel->findBySlug($slug);

        if (!$store) {
            http_response_code(404);
            $this->view('layouts/blank', [
                'pageTitle' => 'Toko Tidak Ditemukan',
                'content'   => 'store/404',
                'csrf_field'  => $this->csrfField(),
        ]);
            return;
        }

        $productModel  = $this->model('ProductModel');
        $categoryModel = $this->model('CategoryModel');

        $products   = $productModel->available($store['id']);
        $categories = $categoryModel->byStore($store['id']);

        // Kelompokkan produk per kategori
        $menu = [];
        foreach ($categories as $cat) {
            $menu[$cat['id']] = [
                'name'     => $cat['name'],
                'icon'     => $cat['icon'],
                'products' => [],
            ];
        }
        foreach ($products as $p) {
            if (isset($menu[$p['category_id']])) {
                $menu[$p['category_id']]['products'][] = $p;
            }
        }
        $menu = array_filter($menu, fn($c) => !empty($c['products']));

        // Pilih template berdasarkan niche
        $themeMap = [
            'coffee'     => 'themes/coffee/layout',
            'laundry'    => 'themes/laundry/layout',
            'barbershop' => 'themes/barbershop/layout',
            'restaurant' => 'themes/restaurant/layout',
            'fashion'    => 'themes/fashion/layout',
            'bakery'     => 'themes/bakery/layout',
        ];
        $theme = $themeMap[$store['niche']] ?? 'themes/coffee/layout';

        $this->view($theme, [
            'store'      => $store,
            'products'   => $products,
            'categories' => $categories,
            'menu'       => $menu,
            'pageTitle'  => $store['name'],
        ]);
    }

    // Proses order dari halaman publik (AJAX)
    public function order(string $slug): void
    {
        header('Content-Type: application/json');

        $storeModel = $this->model('StoreModel');
        $store = $storeModel->findBySlug($slug);

        if (!$store) {
            echo json_encode(['success' => false, 'message' => 'Toko tidak ditemukan.']);
            exit;
        }

        $raw   = file_get_contents('php://input');
        $input = json_decode($raw, true);

        $name  = trim($input['customer_name'] ?? '');
        $phone = trim($input['customer_phone'] ?? '');
        $note  = trim($input['note'] ?? '');
        $items = $input['items'] ?? [];

        if (empty($name) || empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Nama dan item wajib diisi.']);
            exit;
        }

        $productModel = $this->model('ProductModel');
        $validItems   = [];
        $total        = 0;

        foreach ($items as $item) {
            $product = $productModel->find((int)($item['product_id'] ?? 0));
            if (!$product || !$product['is_available'] || $product['store_id'] != $store['id']) continue;

            $qty      = max(1, (int)($item['qty'] ?? 1));
            $subtotal = $product['price'] * $qty;
            $total   += $subtotal;

            $validItems[] = [
                'product_id' => $product['id'],
                'qty'        => $qty,
                'price'      => $product['price'],
                'subtotal'   => $subtotal,
                'name'       => $product['name'],
            ];
        }

        if (empty($validItems)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada produk valid.']);
            exit;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO orders (store_id, customer_name, customer_phone, total, status, note, created_at)
             VALUES (?, ?, ?, ?, 'pending', ?, NOW())"
        );
        $stmt->execute([$store['id'], $name, $phone, $total, $note]);
        $orderId = (int)$db->lastInsertId();

        $stmtItem = $db->prepare(
            "INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)"
        );
        foreach ($validItems as $item) {
            $stmtItem->execute([$orderId, $item['product_id'], $item['qty'], $item['price']]);
        }

        // Build WhatsApp URL
        $waPhone = preg_replace('/[^0-9]/', '', $store['phone'] ?? '');
        if (str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);

        $lines   = ["☕ *Pesanan Baru — {$store['name']}*", "━━━━━━━━━━━━━━━━", "🧾 No. Order : *#{$orderId}*", "👤 Nama : *{$name}*", "", "*Detail Pesanan:*"];
        foreach ($validItems as $item) {
            $lines[] = "• {$item['name']} x{$item['qty']} = Rp " . number_format($item['subtotal'], 0, ',', '.');
        }
        $lines[] = "━━━━━━━━━━━━━━━━";
        $lines[] = "💰 *Total : Rp " . number_format($total, 0, ',', '.') . "*";
        if ($note) $lines[] = "📝 Catatan : {$note}";
        $lines[] = "\n_Mohon konfirmasi pesanan ini. Terima kasih!_ 🙏";

        $waUrl = 'https://wa.me/' . $waPhone . '?text=' . rawurlencode(implode("\n", $lines));

        echo json_encode([
            'success'   => true,
            'order_id'  => $orderId,
            'total_fmt' => 'Rp ' . number_format($total, 0, ',', '.'),
            'wa_url'    => $waUrl,
        ]);
        exit;
    }
}
