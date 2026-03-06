<?php

class CategoryController extends Controller
{
    private object $categoryModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index(): void
    {
        $this->requirePermission('categories.read');
        $categories = $this->categoryModel->withProductCount($_SESSION['store_id']);
        $this->view('layouts/main', [
            'pageTitle' => 'Kategori',
            'content' => 'categories/index',
            'categories' => $categories,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->requirePermission('categories.create');
        $data = $this->inputs(['name']);
        $data['store_id'] = $_SESSION['store_id'];

        if (empty($data['name'])) {
            $this->flash('error', 'Nama kategori tidak boleh kosong.');
            $this->redirect('categories');
            return;
        }

        $this->categoryModel->create($data);
        $this->flash('success', 'Kategori berhasil ditambahkan!');
        $this->redirect('categories');
    }

    public function update(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('categories.update');
        $category = $this->categoryModel->find((int)$id);

        if (!$category) {
            $this->flash('error', 'Kategori tidak ditemukan.');
            $this->redirect('categories');
            return;
        }

        $data = $this->inputs(['name']);

        if (empty($data['name'])) {
            $this->flash('error', 'Nama kategori tidak boleh kosong.');
            $this->redirect('categories');
            return;
        }

        $this->categoryModel->update((int)$id, $data);
        $this->flash('success', 'Kategori berhasil diperbarui!');
        $this->redirect('categories');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('categories.delete');
        $category = $this->categoryModel->find((int)$id);

        if (!$category) {
            $this->flash('error', 'Kategori tidak ditemukan.');
            $this->redirect('categories');
            return;
        }

        $this->categoryModel->delete((int)$id);
        $this->flash('success', 'Kategori berhasil dihapus.');
        $this->redirect('categories');
    }
}
