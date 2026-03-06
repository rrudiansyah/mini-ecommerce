<?php

class UserController extends Controller
{
    private object $adminModel;
    private object $roleModel;

    public function __construct()
    {
        $this->requireAuth();
        $this->adminModel = $this->model('AdminModel');
        $this->roleModel = $this->model('RoleModel');
    }

    /**
     * List all users for the current store
     */
    public function index(): void
    {
        $this->requireRole('Admin');
        $storeId = $_SESSION['store_id'];

        // Get all admins for this store with their roles
        $admins = $this->adminModel->query(
            "SELECT a.*, GROUP_CONCAT(r.name SEPARATOR ', ') as roles
             FROM admins a
             LEFT JOIN admin_roles ar ON ar.admin_id = a.id
             LEFT JOIN roles r ON r.id = ar.role_id AND r.store_id = a.store_id
             WHERE a.store_id = ?
             GROUP BY a.id
             ORDER BY a.created_at DESC",
            [$storeId]
        );

        // Get available roles
        $roles = $this->roleModel->where('store_id', $storeId);

        $this->view('layouts/main', [
            'pageTitle' => 'Kelola Pengguna',
            'content' => 'users/index',
            'admins' => $admins,
            'roles' => $roles,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    /**
     * Show create user form
     */
    public function create(): void
    {
        $this->requireRole('Admin');
        $storeId = $_SESSION['store_id'];
        $roles = $this->roleModel->where('store_id', $storeId);

        $this->view('layouts/main', [
            'pageTitle' => 'Tambah Pengguna',
            'content' => 'users/form',
            'user' => null,
            'roles' => $roles,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    /**
     * Store new user
     */
    public function store(): void
    {
        $this->validateCsrf();
        $this->requireRole('Admin');

        $storeId = $_SESSION['store_id'];
        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');
        $roleId = $this->input('role_id');

        // Validate
        if (!$name || !$email || !$password || !$roleId) {
            $this->flash('error', 'Semua field harus diisi.');
            $this->redirect('users/create');
            return;
        }

        // Check if email already exists
        $existing = $this->adminModel->findByEmail($email);
        if ($existing) {
            $this->flash('error', 'Email sudah terdaftar.');
            $this->redirect('users/create');
            return;
        }

        // Verify role belongs to this store
        $role = $this->roleModel->find($roleId);
        if (!$role || $role['store_id'] != $storeId) {
            $this->flash('error', 'Role tidak valid.');
            $this->redirect('users/create');
            return;
        }

        try {
            // Create admin
            $adminId = $this->adminModel->create([
                'store_id' => $storeId,
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'is_active' => 1,
            ]);

            // Assign role
            $this->adminModel->query(
                "INSERT INTO admin_roles (admin_id, role_id) VALUES (?, ?)",
                [$adminId, $roleId]
            );

            $this->flash('success', 'Pengguna berhasil dibuat.');
            $this->redirect('users');
        } catch (Exception $e) {
            $this->flash('error', 'Gagal membuat pengguna: ' . $e->getMessage());
            $this->redirect('users/create');
        }
    }

    /**
     * Show edit user form
     */
    public function edit(string $id): void
    {
        $this->requireRole('Admin');

        $storeId = $_SESSION['store_id'];
        $user = $this->adminModel->find($id);

        // Verify user belongs to this store
        if (!$user || $user['store_id'] != $storeId) {
            $this->flash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('users');
            return;
        }

        // Get user's current roles
        $userRoles = $this->adminModel->getRoles($id);
        $currentRoleId = !empty($userRoles) ? $userRoles[0]['id'] : null;

        // Get available roles
        $roles = $this->roleModel->where('store_id', $storeId);

        $this->view('layouts/main', [
            'pageTitle' => 'Edit Pengguna',
            'content' => 'users/form',
            'user' => $user,
            'roles' => $roles,
            'currentRoleId' => $currentRoleId,
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    /**
     * Update user
     */
    public function update(string $id): void
    {
        $this->validateCsrf();
        $this->requireRole('Admin');

        $storeId = $_SESSION['store_id'];
        $user = $this->adminModel->find($id);

        // Verify user belongs to this store
        if (!$user || $user['store_id'] != $storeId) {
            $this->flash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('users');
            return;
        }

        $name = $this->input('name');
        $email = $this->input('email');
        $password = $this->input('password');
        $roleId = $this->input('role_id');
        $isActive = $this->input('is_active') ? 1 : 0;

        // Validate
        if (!$name || !$email || !$roleId) {
            $this->flash('error', 'Semua field harus diisi.');
            $this->redirect('users/edit/' . $id);
            return;
        }

        // Check if email changed and already exists
        if ($email !== $user['email']) {
            $existing = $this->adminModel->findByEmail($email);
            if ($existing) {
                $this->flash('error', 'Email sudah terdaftar.');
                $this->redirect('users/edit/' . $id);
                return;
            }
        }

        // Verify role belongs to this store
        $role = $this->roleModel->find($roleId);
        if (!$role || $role['store_id'] != $storeId) {
            $this->flash('error', 'Role tidak valid.');
            $this->redirect('users/edit/' . $id);
            return;
        }

        try {
            // Update admin
            $data = [
                'name' => $name,
                'email' => $email,
                'is_active' => $isActive,
            ];

            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $this->adminModel->update($id, $data);

            // Update role
            $this->adminModel->query(
                "DELETE FROM admin_roles WHERE admin_id = ?",
                [$id]
            );

            $this->adminModel->query(
                "INSERT INTO admin_roles (admin_id, role_id) VALUES (?, ?)",
                [$id, $roleId]
            );

            $this->flash('success', 'Pengguna berhasil diperbarui.');
            $this->redirect('users');
        } catch (Exception $e) {
            $this->flash('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
            $this->redirect('users/edit/' . $id);
        }
    }

    /**
     * Delete user
     */
    public function delete(string $id): void
    {
        $this->validateCsrf();
        $this->requireRole('Admin');

        $storeId = $_SESSION['store_id'];
        $user = $this->adminModel->find($id);

        // Verify user belongs to this store
        if (!$user || $user['store_id'] != $storeId) {
            $this->flash('error', 'Pengguna tidak ditemukan.');
            $this->redirect('users');
            return;
        }

        // Prevent deleting self
        if ($user['id'] == $_SESSION['admin_id']) {
            $this->flash('error', 'Tidak bisa menghapus pengguna sendiri.');
            $this->redirect('users');
            return;
        }

        try {
            // Delete role assignments first
            $this->adminModel->query("DELETE FROM admin_roles WHERE admin_id = ?", [$id]);

            // Delete user
            $this->adminModel->query("DELETE FROM admins WHERE id = ?", [$id]);

            $this->flash('success', 'Pengguna berhasil dihapus.');
            $this->redirect('users');
        } catch (Exception $e) {
            $this->flash('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
            $this->redirect('users');
        }
    }
}
