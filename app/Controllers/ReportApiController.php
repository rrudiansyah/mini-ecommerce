<?php

class ReportApiController extends ApiController
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];

        $dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // awal bulan ini
        $dateTo   = $_GET['date_to']   ?? date('Y-m-d');  // hari ini

        $db = Database::getInstance();

        // Total penjualan per hari
        $stmt = $db->prepare("
            SELECT
                DATE(created_at) as tanggal,
                COUNT(*) as total_order,
                SUM(total) as total_omset
            FROM orders
            WHERE store_id = ?
                AND status = 'selesai'
                AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY tanggal ASC
        ");
        $stmt->execute([$storeId, $dateFrom, $dateTo]);
        $penjualanHarian = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Produk terlaris
        $stmt = $db->prepare("
            SELECT
                p.name as produk,
                SUM(oi.qty) as total_terjual,
                SUM(oi.qty * oi.price) as total_omset
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.store_id = ?
                AND o.status = 'selesai'
                AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY p.id, p.name
            ORDER BY total_terjual DESC
            LIMIT 10
        ");
        $stmt->execute([$storeId, $dateFrom, $dateTo]);
        $produkTerlaris = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ringkasan periode
        $stmt = $db->prepare("
            SELECT
                COUNT(*) as total_order,
                SUM(total) as total_omset,
                AVG(total) as rata_omset
            FROM orders
            WHERE store_id = ?
                AND status = 'selesai'
                AND DATE(created_at) BETWEEN ? AND ?
        ");
        $stmt->execute([$storeId, $dateFrom, $dateTo]);
        $ringkasan = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->success([
            'periode' => [
                'dari'  => $dateFrom,
                'sampai'=> $dateTo,
            ],
            'ringkasan' => [
                'total_order'  => (int)   ($ringkasan['total_order']  ?? 0),
                'total_omset'  => (float) ($ringkasan['total_omset']  ?? 0),
                'rata_omset'   => (float) ($ringkasan['rata_omset']   ?? 0),
            ],
            'penjualan_harian' => array_map(function($row) {
                return [
                    'tanggal'     => $row['tanggal'],
                    'total_order' => (int)   $row['total_order'],
                    'total_omset' => (float) $row['total_omset'],
                ];
            }, $penjualanHarian),
            'produk_terlaris' => array_map(function($row) {
                return [
                    'produk'       => $row['produk'],
                    'total_terjual'=> (int)   $row['total_terjual'],
                    'total_omset'  => (float) $row['total_omset'],
                ];
            }, $produkTerlaris),
        ]);
    }
}