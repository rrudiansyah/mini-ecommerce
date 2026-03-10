<?php

class SuperAdminController extends Controller
{
    private object $storeModel;
    private object $adminModel;

    public function __construct()
    {
        $this->requireSuperAdmin();
        $this->storeModel = $this->model('StoreModel');
        $this->adminModel = $this->model('AdminModel');
    }

    // ── Dashboard ────────────────────────────────────────
    public function dashboard(): void
    {
        $stores     = $this->storeModel->allWithStats();
        $totalStores   = count($stores);
        $activeStores  = count(array_filter($stores, fn($s) => $s['is_active']));
        $totalRevenue  = array_sum(array_column($stores, 'total_revenue'));
        $totalOrders   = array_sum(array_column($stores, 'order_count'));

        $this->view('layouts/superadmin', [
            'pageTitle'    => 'Super Admin Dashboard',
            'content'      => 'superadmin/dashboard/index',
            'stores'       => $stores,
            'totalStores'  => $totalStores,
            'activeStores' => $activeStores,
            'totalRevenue' => $totalRevenue,
            'totalOrders'  => $totalOrders,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── List Toko ────────────────────────────────────────
    public function stores(): void
    {
        $stores = $this->storeModel->allWithStats();
        $this->view('layouts/superadmin', [
            'pageTitle' => 'Kelola Toko',
            'content'   => 'superadmin/stores/index',
            'stores'    => $stores,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── Form Toko Baru ───────────────────────────────────
    public function storeCreate(): void
    {
        $this->view('layouts/superadmin', [
            'pageTitle' => 'Tambah Toko Baru',
            'content'   => 'superadmin/stores/create',
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    // ── Simpan Toko Baru ─────────────────────────────────
    public function storeStore(): void
    {
        if (!$this->isPost()) $this->redirect('superadmin/stores');
        $this->validateCsrf();

        $storeName  = $this->input('store_name');
        $niche      = $this->input('niche');
        $address    = $this->input('address');
        $phone      = $this->input('phone');
        $themeColor = $this->input('theme_color', '#3b82f6');
        $adminName  = $this->input('admin_name');
        $username   = $this->input('username');
        $email      = $this->input('email');
        $password   = $this->input('password');

        // Validasi
        if (empty($storeName) || empty($niche) || empty($adminName) || empty($username) || empty($password)) {
            $this->flash('error', 'Semua field wajib diisi.');
            $this->redirect('superadmin/stores/create');
            return;
        }

        // Cek username duplikat
        $existingUser = $this->adminModel->queryOne(
            "SELECT id FROM admins WHERE username = ?", [$username]
        );
        if ($existingUser) {
            $this->flash('error', "Username \"{$username}\" sudah digunakan.");
            $this->redirect('superadmin/stores/create');
            return;
        }

        $niches = ['coffee', 'barbershop', 'restaurant', 'fashion', 'bakery', 'laundry'];
        if (!in_array($niche, $niches)) {
            $this->flash('error', 'Niche tidak valid.');
            $this->redirect('superadmin/stores/create');
            return;
        }

        // Buat toko
        $slug    = $this->storeModel->generateSlug($storeName);
        $storeId = $this->storeModel->create([
            'name'        => $storeName,
            'slug'        => $slug,
            'niche'       => $niche,
            'address'     => $address ?: null,
            'phone'       => $phone ?: null,
            'theme_color' => $themeColor,
            'is_active'   => 1,
        ]);

        // Buat admin untuk toko tersebut
        $this->adminModel->create([
            'store_id'  => $storeId,
            'name'      => $adminName,
            'username'  => $username,
            'email'     => $email ?: null,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'is_active' => 1,
        ]);

        // Assign role Admin ke admin baru (jika RBAC aktif)
        try {
            $roleModel = $this->model('RoleModel');
            $adminRole = $roleModel->queryOne(
                "SELECT id FROM roles WHERE store_id = ? AND name = 'Admin'", [$storeId]
            );
            // Jika belum ada role Admin untuk toko ini, buat dulu
            if (!$adminRole) {
                $roleId = $roleModel->create([
                    'store_id'    => $storeId,
                    'name'        => 'Admin',
                    'description' => 'Administrator toko',
                    'is_system'   => 1,
                ]);
                // Copy semua permissions dari role Admin toko pertama
                $db = Database::getInstance();
                $db->exec("
                    INSERT INTO role_permissions (role_id, permission_id)
                    SELECT {$roleId}, permission_id FROM role_permissions
                    WHERE role_id = (SELECT id FROM roles WHERE name = 'Admin' AND id != {$roleId} LIMIT 1)
                ");
                $adminRole = ['id' => $roleId];
            }
            // Assign ke admin baru
            $newAdmin = $this->adminModel->queryOne(
                "SELECT id FROM admins WHERE username = ?", [$username]
            );
            if ($newAdmin && $adminRole) {
                $db = Database::getInstance();
                $db->prepare("INSERT IGNORE INTO admin_roles (admin_id, role_id) VALUES (?, ?)")
                   ->execute([$newAdmin['id'], $adminRole['id']]);
            }
        } catch (\Throwable $e) {
            // RBAC optional, lanjutkan meski gagal
        }

        $this->flash('success', "Toko \"{$storeName}\" berhasil dibuat! URL publik: /toko/{$slug}");
        $this->redirect('superadmin/stores');
    }

    // ── Toggle Aktif/Nonaktif Toko ───────────────────────
    public function storeToggle(string $id): void
    {
        $store = $this->storeModel->find((int)$id);
        if (!$store) {
            $this->flash('error', 'Toko tidak ditemukan.');
            $this->redirect('superadmin/stores');
            return;
        }

        $newStatus = $store['is_active'] ? 0 : 1;
        $this->storeModel->update((int)$id, ['is_active' => $newStatus]);
        $label = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $this->flash('success', "Toko \"{$store['name']}\" berhasil {$label}.");
        $this->redirect('superadmin/stores');
    }

    // ── Hapus Toko ───────────────────────────────────────
    public function storeDelete(string $id): void
    {
        if (!$this->isPost()) $this->redirect('superadmin/stores');
        $this->validateCsrf();

        $store = $this->storeModel->find((int)$id);
        if (!$store) {
            $this->flash('error', 'Toko tidak ditemukan.');
            $this->redirect('superadmin/stores');
            return;
        }

        $storeId = (int)$id;
        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // 1. Hapus order_items milik orders toko ini
            $db->exec("DELETE oi FROM order_items oi
                        INNER JOIN orders o ON oi.order_id = o.id
                        WHERE o.store_id = {$storeId}");

            // 2. Hapus orders
            $db->exec("DELETE FROM orders WHERE store_id = {$storeId}");

            // 3. Hapus products
            $db->exec("DELETE FROM products WHERE store_id = {$storeId}");

            // 4. Hapus categories
            $db->exec("DELETE FROM categories WHERE store_id = {$storeId}");

            // 5. Hapus admin_roles & admins toko ini
            $db->exec("DELETE ar FROM admin_roles ar
                        INNER JOIN admins a ON ar.admin_id = a.id
                        WHERE a.store_id = {$storeId}");
            $db->exec("DELETE FROM admins WHERE store_id = {$storeId}");

            // 6. Hapus roles & role_permissions toko ini
            $db->exec("DELETE rp FROM role_permissions rp
                        INNER JOIN roles r ON rp.role_id = r.id
                        WHERE r.store_id = {$storeId}");
            $db->exec("DELETE FROM roles WHERE store_id = {$storeId}");

            // 7. Hapus toko
            $this->storeModel->delete($storeId);

            $db->commit();

            // Hapus file logo jika ada
            if (!empty($store['logo'])) {
                $logoPath = ROOT_PATH . '/public/' . $store['logo'];
                if (file_exists($logoPath)) unlink($logoPath);
            }

            $this->flash('success', "Toko \"{$store['name']}\" dan semua datanya berhasil dihapus.");

        } catch (Exception $e) {
            $db->rollBack();
            $this->flash('error', 'Gagal menghapus toko: ' . $e->getMessage());
        }

        $this->redirect('superadmin/stores');
    }

    // ── Upgrade/Downgrade Plan Toko ──────────────────────
    public function storePlan(string $id): void
    {
        if (!$this->isPost()) $this->redirect('superadmin/stores');
        $this->validateCsrf();

        $store = $this->storeModel->find((int)$id);
        if (!$store) {
            $this->flash('error', 'Toko tidak ditemukan.');
            $this->redirect('superadmin/stores');
            return;
        }

        $plan    = $this->input('plan');
        $expires = $this->input('plan_expires_at') ?: null;
        $allowed = ['basic', 'pro', 'bisnis'];

        if (!in_array($plan, $allowed)) {
            $this->flash('error', 'Paket tidak valid.');
            $this->redirect('superadmin/stores');
            return;
        }

        $this->storeModel->update((int)$id, [
            'plan'            => $plan,
            'plan_expires_at' => $expires,
        ]);

        $labels = ['basic' => 'Basic', 'pro' => 'Pro', 'bisnis' => 'Bisnis'];
        $storeName = $store['name'];
        $planLabel = $labels[$plan];
        $this->flash('success', "Paket toko '{$storeName}' diubah ke {$planLabel}.");
        $this->redirect('superadmin/stores');
    }

    // ── Logout ───────────────────────────────────────────
    public function logout(): void
    {
        unset(
            $_SESSION['superadmin_id'],
            $_SESSION['superadmin_name']
        );
        $this->redirect('superadmin/login');
    }
}
