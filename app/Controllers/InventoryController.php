<?php

class InventoryController extends Controller
{
    private IngredientModel $ingredientModel;
    private RecipeModel     $recipeModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->ingredientModel = $this->model('IngredientModel');
        $this->recipeModel     = $this->model('RecipeModel');
    }

    // ── Cek plan sebelum semua aksi inventory ────────────
    private function requireInventoryPlan(): void
    {
        require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
        if (!PlanHelper::canFeature('inventory')) {
            $this->flash('error', PlanHelper::upgradeMessage('inventory'));
            $this->redirect('dashboard');
        }
    }

    // ── GET /inventory ──────────────────────────────────
    public function index(): void
    {
        $this->requireInventoryPlan();
        $this->requirePermission('inventory.read');
        $storeId     = $_SESSION['store_id'];
        $ingredients = $this->ingredientModel->allByStore($storeId);
        $lowStock    = $this->ingredientModel->lowStock($storeId);

        $this->view('layouts/main', [
            'pageTitle'   => 'Manajemen Stok Bahan',
            'content'     => 'inventory/index',
            'ingredients' => $ingredients,
            'lowStock'    => $lowStock,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── GET /inventory/create ───────────────────────────
    public function create(): void
    {
        $this->requirePermission('inventory.create');
        $this->view('layouts/main', [
            'pageTitle'  => 'Tambah Bahan Baku',
            'content'    => 'inventory/form',
            'ingredient' => null,
            'csrf_field' => $this->csrfField(),
        ]);
    }

    // ── POST /inventory/store ───────────────────────────
    public function store(): void
    {
        $this->validateCsrf();
        $this->requirePermission('inventory.create');

        $data = [
            'store_id'      => $_SESSION['store_id'],
            'name'          => trim($this->input('name')),
            'unit'          => trim($this->input('unit', 'pcs')),
            'stock'         => (float)$this->input('stock', 0),
            'stock_min'     => (float)$this->input('stock_min', 0),
            'cost_per_unit' => (float)$this->input('cost_per_unit', 0),
            'notes'         => trim($this->input('notes', '')),
        ];

        if (empty($data['name'])) {
            $this->flash('error', 'Nama bahan tidak boleh kosong.');
            $this->redirect('inventory/create');
        }

        $id = $this->ingredientModel->create($data);
        // Catat log stok awal
        if ($data['stock'] > 0) {
            $db = Database::getInstance();
            $db->prepare("INSERT INTO stock_logs (store_id, ingredient_id, type, qty, stock_before, stock_after, notes, created_by) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$_SESSION['store_id'], $id, 'in', $data['stock'], 0, $data['stock'], 'Stok awal saat penambahan bahan', $_SESSION['admin_id'] ?? null]);
        }
        $this->flash('success', 'Bahan baku "' . $data['name'] . '" berhasil ditambahkan.');
        $this->redirect('inventory');
    }

    // ── GET /inventory/edit/{id} ────────────────────────
    public function edit(string $id): void
    {
        $this->requirePermission('inventory.create');
        $ingredient = $this->ingredientModel->find((int)$id);
        if (!$ingredient || $ingredient['store_id'] != $_SESSION['store_id']) {
            $this->flash('error', 'Bahan tidak ditemukan.');
            $this->redirect('inventory');
        }
        $this->view('layouts/main', [
            'pageTitle'  => 'Edit Bahan Baku',
            'content'    => 'inventory/form',
            'ingredient' => $ingredient,
            'csrf_field' => $this->csrfField(),
        ]);
    }

    // ── POST /inventory/update/{id} ─────────────────────
    public function update(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('inventory.create');

        $ingredient = $this->ingredientModel->find((int)$id);
        if (!$ingredient || $ingredient['store_id'] != $_SESSION['store_id']) {
            $this->flash('error', 'Bahan tidak ditemukan.');
            $this->redirect('inventory');
        }

        $this->ingredientModel->update((int)$id, [
            'name'          => trim($this->input('name')),
            'unit'          => trim($this->input('unit', 'pcs')),
            'stock_min'     => (float)$this->input('stock_min', 0),
            'cost_per_unit' => (float)$this->input('cost_per_unit', 0),
            'notes'         => trim($this->input('notes', '')),
        ]);

        $this->flash('success', 'Bahan baku berhasil diperbarui.');
        $this->redirect('inventory');
    }

    // ── POST /inventory/delete/{id} ─────────────────────
    public function delete(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('inventory.create');

        $ingredient = $this->ingredientModel->find((int)$id);
        if (!$ingredient || $ingredient['store_id'] != $_SESSION['store_id']) {
            $this->json(['error' => 'Bahan tidak ditemukan.'], 404);
        }

        $this->ingredientModel->delete((int)$id);
        $this->flash('success', 'Bahan baku dihapus.');
        $this->redirect('inventory');
    }

    // ── POST /inventory/stock-in ────────────────────────
    public function stockIn(): void
    {
        $this->validateCsrf();
        $this->requirePermission('inventory.create');

        $id      = (int)$this->input('ingredient_id');
        $qty     = (float)$this->input('qty');
        $notes   = trim($this->input('notes', 'Stok masuk'));
        $storeId = $_SESSION['store_id'];
        $adminId = $_SESSION['admin_id'] ?? 0;

        $ingredient = $this->ingredientModel->find($id);
        if (!$ingredient || $ingredient['store_id'] != $storeId || $qty <= 0) {
            $this->flash('error', 'Data tidak valid.');
            $this->redirect('inventory');
        }

        $this->ingredientModel->stockIn($id, $qty, $notes, $storeId, $adminId);
        $this->flash('success', 'Stok ' . $ingredient['name'] . ' berhasil ditambah ' . $qty . ' ' . $ingredient['unit'] . '.');
        $this->redirect('inventory');
    }

    // ── POST /inventory/adjust/{id} ─────────────────────
    public function adjust(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('inventory.create');

        $storeId   = $_SESSION['store_id'];
        $adminId   = $_SESSION['admin_id'] ?? 0;
        $newStock  = (float)$this->input('new_stock');
        $notes     = trim($this->input('notes', ''));

        $ingredient = $this->ingredientModel->find((int)$id);
        if (!$ingredient || $ingredient['store_id'] != $storeId) {
            $this->flash('error', 'Bahan tidak ditemukan.');
            $this->redirect('inventory');
        }

        $this->ingredientModel->adjust((int)$id, $newStock, $notes, $storeId, $adminId);
        $this->flash('success', 'Stok ' . $ingredient['name'] . ' disesuaikan menjadi ' . $newStock . ' ' . $ingredient['unit'] . '.');
        $this->redirect('inventory');
    }

    // ── GET /inventory/logs ─────────────────────────────
    public function logs(): void
    {
        $this->requirePermission('inventory.read');
        $storeId = $_SESSION['store_id'];
        $ingId   = (int)($_GET['ingredient_id'] ?? 0);
        $type    = $_GET['type'] ?? '';
        $limit   = 100;

        $sql = "SELECT sl.*, i.name AS ingredient_name, i.unit
                FROM stock_logs sl
                JOIN ingredients i ON sl.ingredient_id = i.id
                WHERE sl.store_id = ?";
        $params = [$storeId];
        if ($ingId)   { $sql .= " AND sl.ingredient_id = ?"; $params[] = $ingId; }
        if ($type)    { $sql .= " AND sl.type = ?";           $params[] = $type; }
        $sql .= " ORDER BY sl.created_at DESC LIMIT ?";
        $params[] = $limit;

        $db   = Database::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ingredients = $this->ingredientModel->allByStore($storeId);

        $this->view('layouts/main', [
            'pageTitle'   => 'Riwayat Stok',
            'content'     => 'inventory/logs',
            'logs'        => $logs,
            'ingredients' => $ingredients,
            'filterIng'   => $ingId,
            'filterType'  => $type,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── GET /inventory/api/list (JSON — untuk select2 di resep) ──
    public function apiList(): void
    {
        $storeId = $_SESSION['store_id'] ?? 0;
        $items   = $this->ingredientModel->allByStore($storeId);
        $this->json($items);
    }
}
