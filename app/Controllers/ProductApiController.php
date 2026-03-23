<?php

class ProductApiController extends ApiController
{
    public function index(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];

        $productModel = $this->model('ProductModel');
        $products     = $productModel->allWithCategory($storeId);

        $this->success(array_map(function($p) {
            return [
                'id'           => $p['id'],
                'name'         => $p['name'],
                'price'        => (float) $p['price'],
                'stock'        => (int) $p['stock'],
                'is_available' => (bool) $p['is_available'],
                'category'     => $p['category_name'] ?? 'Tanpa Kategori',
                'image'        => $p['image'] ?? null,
            ];
        }, $products));
    }

    public function toggleAvailable(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];
        $id       = (int) ($_GET['id'] ?? 0);

        if (!$id) $this->error('ID produk tidak valid.');

        $productModel = $this->model('ProductModel');
        $product      = $productModel->find($id);

        if (!$product || (int)$product['store_id'] !== $storeId) {
            $this->error('Produk tidak ditemukan.', 404);
        }

        $newStatus = $product['is_available'] ? 0 : 1;
        $productModel->update($id, ['is_available' => $newStatus]);

        $this->success(
            ['is_available' => (bool) $newStatus],
            $newStatus ? 'Produk diaktifkan.' : 'Produk dinonaktifkan.'
        );
    }

    public function updateStock(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200); exit;
        }

        $authUser = $this->getAuthUser();
        $storeId  = (int) $authUser['store_id'];
        $id       = (int) ($_GET['id'] ?? 0);

        if (!$id) $this->error('ID produk tidak valid.');

        $body  = $this->getBody();
        $stock = $body['stock'] ?? null;

        if ($stock === null || !is_numeric($stock)) {
            $this->error('Nilai stok tidak valid.');
        }

        $productModel = $this->model('ProductModel');
        $product      = $productModel->find($id);

        if (!$product || (int)$product['store_id'] !== $storeId) {
            $this->error('Produk tidak ditemukan.', 404);
        }

        $productModel->update($id, ['stock' => (int) $stock]);

        $this->success(
            ['stock' => (int) $stock],
            'Stok produk berhasil diupdate.'
        );
    }
}