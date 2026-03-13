<?php

class ProductController extends Controller
{
    private object $productModel;
    private object $categoryModel;
    private RecipeModel $recipeModel;
    private IngredientModel $ingredientModel;
    private VariantModel $variantModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->productModel    = $this->model('ProductModel');
        $this->categoryModel   = $this->model('CategoryModel');
        $this->recipeModel     = $this->model('RecipeModel');
        $this->ingredientModel = $this->model('IngredientModel');
        require_once ROOT_PATH . '/app/Models/VariantModel.php';
        $this->variantModel    = new VariantModel();
    }

    public function index(): void
    {
        $this->requirePermission('products.read');
        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        $storeId  = $_SESSION['store_id'];
        $products = $this->productModel->allWithCategory($storeId);
        $count    = count($products);

        // Attach variants ke setiap produk untuk tampil stok
        foreach ($products as &$p) {
            $p['variants'] = !empty($p['has_variants'])
                ? $this->variantModel->byProduct((int)$p['id'])
                : [];
        }
        unset($p);

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
        $variantTypes = $this->variantModel->typesByStore($storeId);
        $this->view('layouts/main', [
            'pageTitle'    => 'Tambah Produk',
            'content'      => 'products/form',
            'categories'   => $categories,
            'ingredients'  => $ingredients,
            'variantTypes' => $variantTypes,
            'product'      => null,
            'recipe'       => [],
            'variants'     => [],
            'csrf_field'   => $this->csrfField(),
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

        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        $hppType = $this->input('hpp_type', 'manual');
        if ($hppType === 'auto' && !PlanHelper::canFeature('hpp_auto')) $hppType = 'manual';
        $data = $this->inputs(['name', 'description', 'price', 'category_id']);
        $data['store_id']     = $_SESSION['store_id'];
        $data['is_available'] = 1;
        $data['hpp_type']     = $hppType;
        $data['hpp']          = $hppType === 'manual' ? (float)$this->input('hpp', 0) : 0;
        // Stok produk: -1 = tidak ditrack, >= 0 = ditrack
        $data['stock'] = $hppType === 'manual' && $this->input('stock') !== null
            ? (int)$this->input('stock', -1)
            : -1;

        $image = $this->uploadImage('image', 'products');
        if ($image) $data['image'] = $image;

        $productId = $this->productModel->create($data);

        if ($hppType === 'auto' && $productId) {
            $items = $this->_parseRecipeInput();
            $this->recipeModel->saveRecipe((int)$productId, $items);
            $hpp = $this->recipeModel->calcHpp((int)$productId);
            $this->productModel->update((int)$productId, ['hpp' => $hpp, 'hpp_type' => 'auto']);
        }

        // Simpan varian jika ada
        $hasVariants = (int)$this->input('has_variants', 0);
        if ($hasVariants && !PlanHelper::canFeature('variants')) $hasVariants = 0;
        if ($hasVariants && $productId) {
            $this->productModel->update((int)$productId, ['has_variants' => 1]);
            $this->_saveVariants((int)$productId);
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
        $variantTypes = $this->variantModel->typesByStore($storeId);
        $variants     = $this->variantModel->byProduct((int)$id);
        $this->view('layouts/main', [
            'pageTitle'    => 'Edit Produk',
            'content'      => 'products/form',
            'categories'   => $categories,
            'ingredients'  => $ingredients,
            'variantTypes' => $variantTypes,
            'product'      => $product,
            'recipe'       => $recipe,
            'variants'     => $variants,
            'csrf_field'   => $this->csrfField(),
        ]);
    }

    public function update(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('products.update');

        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        $hppType = $this->input('hpp_type', 'manual');
        if ($hppType === 'auto' && !PlanHelper::canFeature('hpp_auto')) $hppType = 'manual';
        $data = $this->inputs(['name', 'description', 'price', 'category_id', 'is_available']);
        $data['hpp_type'] = $hppType;
        $data['hpp']      = $hppType === 'manual' ? (float)$this->input('hpp', 0) : 0;
        $data['stock']    = $hppType === 'manual' && $this->input('stock') !== null
            ? (int)$this->input('stock', -1)
            : -1;

        $image = $this->uploadImage('image', 'products');
        if ($image) $data['image'] = $image;

        $this->productModel->update((int)$id, $data);

        if ($hppType === 'auto') {
            $items = $this->_parseRecipeInput();
            $this->recipeModel->saveRecipe((int)$id, $items);
            $hpp = $this->recipeModel->calcHpp((int)$id);
            $this->productModel->update((int)$id, ['hpp' => $hpp, 'hpp_type' => 'auto']);
        } else {
            $this->recipeModel->saveRecipe((int)$id, []);
        }

        // Update varian
        $hasVariants = (int)$this->input('has_variants', 0);
        $this->productModel->update((int)$id, ['has_variants' => $hasVariants]);
        if ($hasVariants) {
            $this->_saveVariants((int)$id);
        } else {
            $this->variantModel->saveVariants((int)$id, []);
        }

        $this->flash('success', 'Produk berhasil diperbarui!');
        $this->redirect('products');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('products.delete');

        $product = $this->productModel->find((int)$id);
        if (!$product || $product['store_id'] != $_SESSION['store_id']) {
            $this->flash('error', 'Produk tidak ditemukan.');
            $this->redirect('products');
            return;
        }

        // Cek apakah pernah dipesan
        $db   = \Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
        $stmt->execute([(int)$id]);
        $hasOrders = (int)$stmt->fetchColumn() > 0;

        $this->productModel->softDelete((int)$id);

        if ($hasOrders) {
            $this->flash('warning', '"' . htmlspecialchars($product['name']) . '" tidak dapat dihapus permanen karena sudah pernah dipesan. Produk disembunyikan dari toko.');
        } else {
            $this->flash('success', 'Produk "' . htmlspecialchars($product['name']) . '" berhasil dihapus.');
        }
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

    private function _saveVariants(int $productId): void
    {
        $labels    = $_POST['variant_label']  ?? [];
        $prices    = $_POST['variant_price']  ?? [];
        $stocks    = $_POST['variant_stock']  ?? [];
        $skus      = $_POST['variant_sku']    ?? [];

        $hpps = $_POST['variant_hpp'] ?? [];

        $variants = [];
        foreach ($labels as $i => $label) {
            if (trim($label)) {
                $variants[] = [
                    'label'      => $label,
                    'price'      => (float)($prices[$i] ?? 0),
                    'stock'      => (int)($stocks[$i] ?? 0),
                    'sku'        => $skus[$i] ?? '',
                    'hpp'        => (float)($hpps[$i] ?? 0),
                    'option_ids' => $_POST['variant_options'][$i] ?? [],
                ];
            }
        }
        $this->variantModel->saveVariants($productId, $variants);
    }
}
