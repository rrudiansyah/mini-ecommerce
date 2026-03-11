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

        $this->view('layouts/main', [
            'pageTitle'    => 'Dashboard',
            'content'      => 'dashboard/index',
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
            'lowStock'     => $lowStock,
            'csrf_field'   => $this->csrfField(),
        ]);
    }
}

