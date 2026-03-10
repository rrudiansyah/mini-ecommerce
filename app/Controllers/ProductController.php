<?php

class ProductController extends Controller
{
    private object $productModel;
    private object $categoryModel;
    private RecipeModel $recipeModel;
    private IngredientModel $ingredientModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->productModel    = $this->model('ProductModel');
        $this->categoryModel   = $this->model('CategoryModel');
        $this->recipeModel     = $this->model('RecipeModel');
        $this->ingredientModel = $this->model('IngredientModel');
    }

    public function index(): void
    {
        $this->requirePermission('products.read');
        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        $storeId  = $_SESSION['store_id'];
        $products = $this->productModel->allWithCategory($storeId);
        $count    = count($products);
        $this->view('layouts/main', [
            'pageTitle'    => 'Produk',
            'content'      => 'products/index',
            'products'     => $products,
            'planLimit'    => PlanHelper::limit('products'),
            'planOverLimit'=> PlanHelper::isOverLimit('products', $count),
            'planName'     => PlanHelper::planName(),
            'csrf_field'   => $this->csrfField(),
        ]);
    }

    public function create(): void
    {
        $this->requirePermission('products.create');
        $storeId     = $_SESSION['store_id'];
        $categories  = $this->categoryModel->byStore($storeId);
        $ingredients = $this->ingredientModel->allByStore($storeId);
        $this->view('layouts/main', [
            'pageTitle'   => 'Tambah Produk',
            'content'     => 'products/form',
            'categories'  => $categories,
            'ingredients' => $ingredients,
            'product'     => null,
            'recipe'      => [],
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $this->requirePermission('products.create');

        // Cek limit paket
        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        $count = count($this->productModel->allWithCategory($_SESSION['store_id']));
        if (PlanHelper::isOverLimit('products', $count)) {
            $this->flash('error', PlanHelper::upgradeMessage('products'));
            $this->redirect('products');
            return;
        }

        $hppType = $this->input('hpp_type', 'manual');
        $data = $this->inputs(['name', 'description', 'price', 'category_id']);
        $data['store_id']     = $_SESSION['store_id'];
        $data['is_available'] = 1;
        $data['hpp_type']     = $hppType;
        $data['hpp']          = $hppType === 'manual' ? (float)$this->input('hpp', 0) : 0;

        $image = $this->uploadImage('image', 'products');
        if ($image) $data['image'] = $image;

        $productId = $this->productModel->create($data);

        if ($hppType === 'auto' && $productId) {
            $items = $this->_parseRecipeInput();
            $this->recipeModel->saveRecipe((int)$productId, $items);
            $hpp = $this->recipeModel->calcHpp((int)$productId);
            $this->productModel->update((int)$productId, ['hpp' => $hpp]);
        }

        $this->flash('success', 'Produk berhasil ditambahkan!');
        $this->redirect('products');
    }

    public function edit(string $id): void
    {
        $this->requirePermission('products.update');
        $storeId     = $_SESSION['store_id'];
        $product     = $this->productModel->find((int)$id);
        $categories  = $this->categoryModel->byStore($storeId);
        $ingredients = $this->ingredientModel->allByStore($storeId);
        $recipe      = $this->recipeModel->byProduct((int)$id);

        if (!$product) {
            $this->flash('error', 'Produk tidak ditemukan.');
            $this->redirect('products');
        }
        $this->view('layouts/main', [
            'pageTitle'   => 'Edit Produk',
            'content'     => 'products/form',
            'categories'  => $categories,
            'ingredients' => $ingredients,
            'product'     => $product,
            'recipe'      => $recipe,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function update(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('products.update');

        $hppType = $this->input('hpp_type', 'manual');
        $data = $this->inputs(['name', 'description', 'price', 'category_id', 'is_available']);
        $data['hpp_type'] = $hppType;
        $data['hpp']      = $hppType === 'manual' ? (float)$this->input('hpp', 0) : 0;

        $image = $this->uploadImage('image', 'products');
        if ($image) $data['image'] = $image;

        $this->productModel->update((int)$id, $data);

        if ($hppType === 'auto') {
            $items = $this->_parseRecipeInput();
            $this->recipeModel->saveRecipe((int)$id, $items);
            $hpp = $this->recipeModel->calcHpp((int)$id);
            $this->productModel->update((int)$id, ['hpp' => $hpp]);
        } else {
            $this->recipeModel->saveRecipe((int)$id, []);
        }

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

    private function _parseRecipeInput(): array
    {
        $ingredientIds = $_POST['recipe_ingredient'] ?? [];
        $qtys          = $_POST['recipe_qty']        ?? [];
        $items = [];
        foreach ($ingredientIds as $i => $ingId) {
            $qty = (float)($qtys[$i] ?? 0);
            if ($ingId && $qty > 0) {
                $items[] = ['ingredient_id' => (int)$ingId, 'qty_used' => $qty];
            }
        }
        return $items;
    }
}
