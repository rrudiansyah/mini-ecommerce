<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->requirePermission('dashboard.view');
        $storeId      = $_SESSION['store_id'];
        $orderModel   = $this->model('OrderModel');
        $stats        = $orderModel->stats($storeId);
        $recentOrders = array_slice($orderModel->allWithItems($storeId), 0, 5);

        $this->view('layouts/main', [
            'pageTitle'    => 'Dashboard',
            'content'      => 'dashboard/index',
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
            'csrf_field'  => $this->csrfField(),
        ]);
    }
}

