<?php

class DashboardApiController extends ApiController
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // Verifikasi token
        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];

        // Ambil statistik dari model yang sudah ada
        $orderModel      = $this->model('OrderModel');
        $ingredientModel = $this->model('IngredientModel');

        $stats        = $orderModel->stats($storeId);
        $recentOrders = array_slice($orderModel->allWithItems($storeId), 0, 5);
        $lowStock     = $ingredientModel->lowStock($storeId);

        // Stok varian menipis
        $lowStockVariants = [];
        try {
            require_once ROOT_PATH . '/app/Models/VariantModel.php';
            $variantModel     = new VariantModel();
            $lowStockVariants = $variantModel->lowStock($storeId, 5);
        } catch (\Throwable $e) {
            // abaikan jika tabel belum ada
        }

        $this->success([
            'stats'             => [
                'total_orders'   => (int) $stats['total_orders'],
                'total_revenue'  => (float) $stats['total_revenue'],
                'pending_orders' => (int) $stats['pending_orders'],
                'today_revenue'  => (float) $stats['today_revenue'],
            ],
            'recent_orders'     => array_map(function($order) {
                return [
                    'id'            => $order['id'],
                    'customer_name' => $order['customer_name'],
                    'total'         => (float) $order['total'],
                    'status'        => $order['status'],
                    'created_at'    => $order['created_at'],
                    'total_items'   => (int) $order['total_items'],
                ];
            }, $recentOrders),
            'low_stock'         => $lowStock,
            'low_stock_variants'=> $lowStockVariants,
        ]);
    }
}