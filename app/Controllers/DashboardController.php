<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->requirePermission('dashboard.view');
        $storeId        = $_SESSION['store_id'];
        $orderModel     = $this->model('OrderModel');
        $ingredientModel= $this->model('IngredientModel');
        $stats          = $orderModel->stats($storeId);
        $recentOrders   = array_slice($orderModel->allWithItems($storeId), 0, 5);
        $lowStock       = $ingredientModel->lowStock($storeId);

        // Stok varian menipis (aman jika tabel belum ada)
        $lowStockVariants = [];
        try {
            require_once ROOT_PATH . '/app/Models/VariantModel.php';
            $variantModel     = new VariantModel();
            $lowStockVariants = $variantModel->lowStock($storeId, 5);
        } catch (Throwable $e) {
            // tabel product_variants belum ada — abaikan
        }

        $this->view('layouts/main', [
            'pageTitle'         => 'Dashboard',
            'content'           => 'dashboard/index',
            'stats'             => $stats,
            'recentOrders'      => $recentOrders,
            'lowStock'          => $lowStock,
            'lowStockVariants'  => $lowStockVariants,
            'csrf_field'        => $this->csrfField(),
        ]);
    }
}

