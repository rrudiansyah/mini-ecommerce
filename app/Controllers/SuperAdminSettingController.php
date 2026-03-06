<?php

class SuperAdminSettingController extends Controller
{
    private object $storeModel;
    private object $roleModel;
    private object $permissionModel;

    public function __construct()
    {
        $this->requireSuperAdmin();
        $this->storeModel      = $this->model('StoreModel');
        $this->roleModel       = $this->model('RoleModel');
        $this->permissionModel = $this->model('PermissionModel');
    }

    // ── Halaman utama pengaturan ─────────────────────────
    public function index(): void
    {
        $this->view('layouts/superadmin', [
            'pageTitle' => 'Pengaturan',
            'content'   => 'superadmin/settings/index',
            'appName'   => APP_NAME,
            'appUrl'    => BASE_URL,
            'appEnv'    => APP_ENV,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── Simpan pengaturan sistem (APP_NAME, dll) ─────────
    public function saveSystem(): void
    {
        if (!$this->isPost()) $this->redirect('superadmin/settings');
        $this->validateCsrf();

        $appName = trim($this->input('app_name'));
        $appUrl  = rtrim(trim($this->input('app_url')), '/');

        if (empty($appName)) {
            $this->flash('error', 'Nama aplikasi tidak boleh kosong.');
            $this->redirect('superadmin/settings');
            return;
        }

        $this->updateEnv('APP_NAME', '"' . $appName . '"');
        if (!empty($appUrl)) $this->updateEnv('APP_URL', $appUrl);

        $this->flash('success', 'Pengaturan sistem berhasil disimpan. Restart container agar perubahan berlaku.');
        $this->redirect('superadmin/settings');
    }

    // ── Form edit info & tema toko ───────────────────────
    public function storeEdit(string $id): void
    {
        $store = $this->storeModel->find((int)$id);
        if (!$store) {
            $this->flash('error', 'Toko tidak ditemukan.');
            $this->redirect('superadmin/settings');
            return;
        }

        $this->view('layouts/superadmin', [
            'pageTitle' => 'Edit Toko — ' . $store['name'],
            'content'   => 'superadmin/settings/store_edit',
            'store'     => $store,
        ]);
    }

    // ── Simpan info & tema toko ──────────────────────────
    public function storeSave(string $id): void
    {
        if (!$this->isPost()) $this->redirect('superadmin/settings');
        $this->validateCsrf();

        $store = $this->storeModel->find((int)$id);
        if (!$store) {
            $this->flash('error', 'Toko tidak ditemukan.');
            $this->redirect('superadmin/settings');
            return;
        }

        $name        = trim($this->input('name'));
        $address     = trim($this->input('address'));
        $phone       = trim($this->input('phone'));
        $description = trim($this->input('description'));
        $themeColor  = trim($this->input('theme_color', '#3b82f6'));

        if (empty($name)) {
            $this->flash('error', 'Nama toko tidak boleh kosong.');
            $this->redirect('superadmin/settings/store/' . $id);
            return;
        }

        // Update slug jika nama berubah
        $data = [
            'name'        => $name,
            'address'     => $address ?: null,
            'phone'       => $phone ?: null,
            'description' => $description ?: null,
            'theme_color' => $themeColor,
        ];

        if ($name !== $store['name']) {
            $data['slug'] = $this->storeModel->generateSlug($name);
        }

        // Handle upload logo
        if (!empty($_FILES['logo']['name'])) {
            $logo = $this->uploadLogo($_FILES['logo'], (int)$id);
            if ($logo) $data['logo'] = $logo;
            else {
                $this->flash('error', 'Gagal upload logo. Pastikan format JPG/PNG dan ukuran < 2MB.');
                $this->redirect('superadmin/settings/store/' . $id);
                return;
            }
        }

        $this->storeModel->update((int)$id, $data);
        $this->flash('success', 'Pengaturan toko "' . $name . '" berhasil disimpan.');
        $this->redirect('superadmin/settings/store/' . $id);
    }

    // ── Role & Permission per toko ───────────────────────
    public function storeRoles(string $id): void
    {
                $store = $this->storeModel->find((int)$id);
        if (!$store) {
            $this->flash('error', 'Toko tidak ditemukan.');
            $this->redirect('superadmin/settings');
            return;
        }

        $roles = $this->roleModel->getByStoreWithPermissions((int)$id);
        foreach ($roles as &$role) {
            $cnt = $this->roleModel->queryOne(
                "SELECT COUNT(*) as c FROM admin_roles WHERE role_id = ?", [$role['id']]
            );
            $role['admin_count'] = $cnt['c'] ?? 0;
        }

        $allPermissions = $this->permissionModel->groupByModule();

        $this->view('layouts/superadmin', [
            'pageTitle'      => 'Role & Permission — ' . $store['name'],
            'content'        => 'superadmin/settings/roles',
            'store'          => $store,
            'roles'          => $roles,
            'allPermissions' => $allPermissions,
        ]);
    }

    // ── Toggle permission (AJAX) ─────────────────────────
    public function togglePermission(string $roleId): void
    {
        $this->validateCsrf();
        header('Content-Type: application/json');

        $role = $this->roleModel->find((int)$roleId);
        if (!$role) { echo json_encode(['error' => 'Role tidak ditemukan']); exit; }

        $permId = (int)($_POST['permission_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if (!$permId || !in_array($action, ['add', 'remove'])) {
            echo json_encode(['error' => 'Data tidak valid']); exit;
        }

        if ($action === 'add')    $this->roleModel->assignPermission((int)$roleId, $permId);
        else                       $this->roleModel->removePermission((int)$roleId, $permId);

        echo json_encode(['success' => true]);
        exit;
    }

    // ── Helper: update .env ──────────────────────────────
    private function updateEnv(string $key, string $value): void
    {
        $envFile = ROOT_PATH . '/.env';
        if (!file_exists($envFile)) return;

        $content = file_get_contents($envFile);
        $pattern = "/^{$key}=.*/m";
        $replace = "{$key}={$value}";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replace, $content);
        } else {
            $content .= "\n{$replace}";
        }

        file_put_contents($envFile, $content);
    }

    // ── Helper: upload logo ──────────────────────────────
    private function uploadLogo(array $file, int $storeId): string|false
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) return false;
        if ($file['size'] > 2097152) return false;

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'store_' . $storeId . '_logo_' . time() . '.' . $ext;
        $dest     = UPLOAD_PATH . 'logos/' . $filename;

        if (!is_dir(UPLOAD_PATH . 'logos')) mkdir(UPLOAD_PATH . 'logos', 0755, true);
        if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

        return 'uploads/logos/' . $filename;
    }
}
