<?php

class VariantController extends Controller
{
    private VariantModel $variantModel;

    public function __construct()
    {
        $this->requireAuth();
        require_once ROOT_PATH . '/app/Models/VariantModel.php';
        $this->variantModel = new VariantModel();
    }

    // ── GET /variants — Daftar tipe varian toko ──────────
    public function index(): void
    {
        $this->requirePermission('variants.read');
        $storeId = $_SESSION['store_id'];
        $types   = $this->variantModel->typesByStore($storeId);

        $this->view('layouts/main', [
            'pageTitle'  => 'Manajemen Varian',
            'content'    => 'variants/index',
            'types'      => $types,
            'csrf_field' => $this->csrfField(),
        ]);
    }

    // ── POST /variants/type/store — Simpan tipe varian baru ──
    public function typeStore(): void
    {
        $this->validateCsrf();
        $this->requirePermission('variants.manage');

        $storeId = $_SESSION['store_id'];
        $name    = trim($this->input('type_name') ?? '');
        $options = array_filter(array_map('trim', explode(',', $this->input('options') ?? '')));

        if (!$name || empty($options)) {
            $this->flash('error', 'Nama tipe dan opsi wajib diisi.');
            $this->redirect('variants');
            return;
        }

        $this->variantModel->saveType($storeId, $name, $options);
        $this->flash('success', "Tipe varian \"$name\" berhasil ditambahkan.");
        $this->redirect('variants');
    }

    // ── POST /variants/type/update/{id} ──────────────────
    public function typeUpdate(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('variants.manage');

        $name    = trim($this->input('type_name') ?? '');
        $options = array_filter(array_map('trim', explode(',', $this->input('options') ?? '')));

        if (!$name || empty($options)) {
            $this->flash('error', 'Nama tipe dan opsi wajib diisi.');
            $this->redirect('variants');
            return;
        }

        $this->variantModel->updateType((int)$id, $name, $options);
        $this->flash('success', 'Tipe varian berhasil diperbarui.');
        $this->redirect('variants');
    }

    // ── POST /variants/type/delete/{id} ──────────────────
    public function typeDelete(string $id): void
    {
        $this->validateCsrf();
        $this->requirePermission('variants.manage');
        $this->variantModel->deleteType((int)$id);
        $this->flash('success', 'Tipe varian berhasil dihapus.');
        $this->redirect('variants');
    }

    // ── GET /variants/api/types — JSON untuk form produk ──
    public function apiTypes(): void
    {
        $this->requireAuth();
        $types = $this->variantModel->typesByStore($_SESSION['store_id']);
        $this->json(['types' => $types]);
    }
    // ── POST /variants/stock/update — Update stok via AJAX ──
    public function stockUpdate(): void
    {
        header('Content-Type: application/json');
        $this->requireAuth();

        $raw   = file_get_contents('php://input');
        $input = json_decode($raw, true);

        $variantId = (int)($input['variant_id'] ?? 0);
        $stock     = (int)($input['stock'] ?? -1);

        if (!$variantId || $stock < 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            exit;
        }

        // Pastikan varian milik toko yang login
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT pv.id FROM product_variants pv
             JOIN products p ON p.id = pv.product_id
             WHERE pv.id = ? AND p.store_id = ?"
        );
        $stmt->execute([$variantId, $_SESSION['store_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Varian tidak ditemukan.']);
            exit;
        }

        $this->variantModel->updateStock($variantId, $stock);
        echo json_encode(['success' => true, 'stock' => $stock]);
        exit;
    }
}