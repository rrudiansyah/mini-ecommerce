<?php

class SettingController extends Controller
{
    private object $roleModel;
    private object $permissionModel;
    private object $adminModel;

    public function __construct()
    {
        $this->requireSuperAdmin();
        $this->roleModel       = $this->model('RoleModel');
        $this->permissionModel = $this->model('PermissionModel');
        $this->adminModel      = $this->model('AdminModel');
    }


    /**
     * Settings dashboard
     */
    public function index(): void
    {
        $this->view('layouts/main', [
            'csrf_field' => $this->csrfField(),
            'pageTitle' => 'Pengaturan',
            'content' => 'settings/index'
        ]);
    }

    /**
     * List all roles for current store
     */
    public function rolesIndex(): void
    {
        $roles = $this->roleModel->getByStoreWithPermissions($_SESSION['store_id']);

        // Get admin count for each role
        foreach ($roles as &$role) {
            $admins = $this->roleModel->queryOne(
                "SELECT COUNT(*) as count FROM admin_roles WHERE role_id = ?",
                [$role['id']]
            );
            $role['admin_count'] = $admins['count'] ?? 0;
        }

        $this->view('layouts/main', [
            'csrf_field' => $this->csrfField(),
            'pageTitle' => 'Kelola Role',
            'content' => 'settings/roles/index',
            'roles' => $roles
        ]);
    }

    /**
     * Show create role form
     */
    public function roleCreate(): void
    {
        $this->view('layouts/main', [
            'csrf_field' => $this->csrfField(),
            'pageTitle' => 'Buat Role Baru',
            'content' => 'settings/roles/form',
            'role' => null,
            'isEdit' => false
        ]);
    }

    /**
     * Save new role
     */
    public function roleStore(): void
    {
        if (!$this->isPost()) {
            $this->redirect('settings/roles');
            return;
        }
        $this->validateCsrf();

        $name = $this->input('name');
        $description = $this->input('description');

        if (empty($name)) {
            $this->flash('error', 'Nama role tidak boleh kosong.');
            $this->redirect('settings/roles/create');
            return;
        }

        // Check for duplicate role name in store
        $existing = $this->roleModel->queryOne(
            "SELECT id FROM roles WHERE store_id = ? AND name = ?",
            [$_SESSION['store_id'], $name]
        );

        if ($existing) {
            $this->flash('error', 'Role dengan nama ini sudah ada di toko Anda.');
            $this->redirect('settings/roles/create');
            return;
        }

        // Create role
        $this->roleModel->create([
            'store_id' => $_SESSION['store_id'],
            'name' => $name,
            'description' => $description ?: null,
            'is_system' => 0
        ]);

        $this->flash('success', 'Role berhasil dibuat!');
        $this->redirect('settings/roles');
    }

    /**
     * Show edit role form
     */
    public function roleEdit(string $id): void
    {
        $role = $this->roleModel->find((int)$id);

        if (!$role || $role['store_id'] != $_SESSION['store_id']) {
            $this->flash('error', 'Role tidak ditemukan.');
            $this->redirect('settings/roles');
            return;
        }

        $this->view('layouts/main', [
            'csrf_field' => $this->csrfField(),
            'pageTitle' => 'Edit Role: ' . $role['name'],
            'content' => 'settings/roles/form',
            'role' => $role,
            'isEdit' => true
        ]);
    }

    /**
     * Update role
     */
    public function roleUpdate(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('settings/roles');
            return;
        }
        $this->validateCsrf();

        $role = $this->roleModel->find((int)$id);

        if (!$role || $role['store_id'] != $_SESSION['store_id']) {
            $this->flash('error', 'Role tidak ditemukan.');
            $this->redirect('settings/roles');
            return;
        }

        // Prevent editing system roles
        if ($role['is_system']) {
            $this->flash('error', 'Anda tidak dapat mengedit role sistem.');
            $this->redirect('settings/roles');
            return;
        }

        $name = $this->input('name');
        $description = $this->input('description');

        if (empty($name)) {
            $this->flash('error', 'Nama role tidak boleh kosong.');
            $this->redirect('settings/roles/edit/' . $id);
            return;
        }

        // Check for duplicate name (excluding current role)
        $existing = $this->roleModel->queryOne(
            "SELECT id FROM roles WHERE store_id = ? AND name = ? AND id != ?",
            [$_SESSION['store_id'], $name, $id]
        );

        if ($existing) {
            $this->flash('error', 'Role dengan nama ini sudah ada di toko Anda.');
            $this->redirect('settings/roles/edit/' . $id);
            return;
        }

        // Update role
        $this->roleModel->update((int)$id, [
            'name' => $name,
            'description' => $description ?: null
        ]);

        $this->flash('success', 'Role berhasil diperbarui!');
        $this->redirect('settings/roles');
    }

    /**
     * Delete role
     */
    public function roleDelete(string $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('settings/roles');
            return;
        }
        $this->validateCsrf();

        $role = $this->roleModel->find((int)$id);

        if (!$role || $role['store_id'] != $_SESSION['store_id']) {
            $this->flash('error', 'Role tidak ditemukan.');
            $this->redirect('settings/roles');
            return;
        }

        // Prevent deletion of system roles
        if ($role['is_system']) {
            $this->flash('error', 'Anda tidak dapat menghapus role sistem.');
            $this->redirect('settings/roles');
            return;
        }

        // Check if role is assigned to any admin
        $adminCount = $this->roleModel->queryOne(
            "SELECT COUNT(*) as count FROM admin_roles WHERE role_id = ?",
            [(int)$id]
        );

        if ($adminCount['count'] > 0) {
            $this->flash('error', 'Tidak dapat menghapus role yang sedang digunakan oleh ' . $adminCount['count'] . ' pengguna.');
            $this->redirect('settings/roles');
            return;
        }

        // Delete role and its permissions
        $this->roleModel->delete((int)$id);

        $this->flash('success', 'Role berhasil dihapus!');
        $this->redirect('settings/roles');
    }

    /**
     * Show permission assignment interface for role
     */
    public function rolePermissions(string $id): void
    {
        $role = $this->roleModel->find((int)$id);

        if (!$role || $role['store_id'] != $_SESSION['store_id']) {
            $this->flash('error', 'Role tidak ditemukan.');
            $this->redirect('settings/roles');
            return;
        }

        // Get all permissions grouped by module
        $allPermissions = $this->permissionModel->groupByModule();

        // Get current role's permissions
        $rolePermissions = $this->roleModel->getPermissionsForRole((int)$id);
        $rolePermissionIds = array_column($rolePermissions, 'id');

        $this->view('layouts/main', [
            'csrf_field' => $this->csrfField(),
            'pageTitle' => 'Kelola Permission: ' . $role['name'],
            'content' => 'settings/roles/permissions',
            'role' => $role,
            'allPermissions' => $allPermissions,
            'rolePermissionIds' => $rolePermissionIds
        ]);
    }

    /**
     * Update role permissions via AJAX
     */
    public function updateRolePermissions(string $id): void
    {
        if (!$this->isPost()) {
            $this->json(['error' => 'Method tidak diperbolehkan'], 405);
            return;
        }
        $this->validateCsrf();

        $role = $this->roleModel->find((int)$id);

        if (!$role || $role['store_id'] != $_SESSION['store_id']) {
            $this->json(['error' => 'Role tidak ditemukan'], 404);
            return;
        }

        // Get permission ID and action
        $permissionId = isset($_POST['permission_id']) ? (int)$_POST['permission_id'] : 0;
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if (!$permissionId || !in_array($action, ['add', 'remove'])) {
            $this->json(['error' => 'Data tidak valid'], 400);
            return;
        }

        // Verify permission exists
        $permission = $this->permissionModel->find($permissionId);
        if (!$permission) {
            $this->json(['error' => 'Permission tidak ditemukan'], 404);
            return;
        }

        // Update database
        if ($action === 'add') {
            $this->roleModel->assignPermission((int)$id, $permissionId);
            $message = 'Permission berhasil ditambahkan';
        } else {
            $this->roleModel->removePermission((int)$id, $permissionId);
            $message = 'Permission berhasil dihapus';
        }

        $this->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
