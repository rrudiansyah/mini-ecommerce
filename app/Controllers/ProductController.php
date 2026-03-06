<?php

class ProductController extends Controller
{
    private object $productModel;
    private object $categoryModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->productModel  = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index(): void
    {
        $this->requirePermission('products.read');
        $products = $this->productModel->allWithCategory($_SESSION['store_id']);
        $this->view('layouts/main', [
            'pageTitle' => 'Produk', 'content' => 'products/index', 'products' => $products,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('products.create');
        $categories = $this->categoryModel->byStore($_SESSION['store_id']);
        $this->view('layouts/main', [
            'pageTitle' => 'Tambah Produk', 'content' => 'products/form',
            'categories' => $categories, 'product' => null,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->requirePermission('products.create');
        $data = $this->inputs(['name', 'description', 'price', 'category_id']);
        $data['store_id']     = $_SESSION['store_id'];
        $data['is_available'] = 1;
        $image = $this->uploadImage('image', 'products');
        if ($image) $data['image'] = $image;
        $this->productModel->create($data);
        $this->flash('success', 'Produk berhasil ditambahkan!');
        $this->redirect('products');
    }

    public function edit(string $id): void
    {
        $this->requirePermission('products.update');
        $product    = $this->productModel->find((int)$id);
        $categories = $this->categoryModel->byStore($_SESSION['store_id']);
        if (!$product) { $this->flash('error', 'Produk tidak ditemukan.'); $this->redirect('products'); }
        $this->view('layouts/main', [
            'pageTitle' => 'Edit Produk', 'content' => 'products/form',
            'categories' => $categories, 'product' => $product,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function update(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('products.update');
        $data = $this->inputs(['name', 'description', 'price', 'category_id', 'is_available']);
        $image = $this->uploadImage('image', 'products');
        if ($image) $data['image'] = $image;
        $this->productModel->update((int)$id, $data);
        $this->flash('success', 'Produk berhasil diperbarui!');
        $this->redirect('products');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('products.delete');
        $this->productModel->delete((int)$id);
        $this->flash('success', 'Produk berhasil dihapus.');
        $this->redirect('products');
    }
}
