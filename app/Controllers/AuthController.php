<?php

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (isset($_SESSION['admin_id'])) $this->redirect('dashboard');
        $this->view('layouts/auth', ['pageTitle' => 'Login Admin', 'content' => 'auth/login',
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function login(): void
    {
        $this->validateCsrf();

        // ── Rate limiting: maks 5 percobaan per 5 menit per IP ──
        require_once ROOT_PATH . '/core/RateLimiter.php';
        $limiter = new RateLimiter(5, 300);
        $rlKey   = RateLimiter::key('login');

        if ($limiter->tooManyAttempts($rlKey)) {
            $wait = $limiter->availableIn($rlKey);
            $this->flash('error', "Terlalu banyak percobaan login. Coba lagi dalam {$wait} detik.");
            $this->redirect('login');
            return;
        }

        $username   = $this->input('username');
        $password   = $this->input('password');
        $adminModel = $this->model('AdminModel');
        $admin      = $adminModel->findByUsername($username);

        if ($admin && password_verify($password, $admin['password'])) {
            $limiter->clear($rlKey); // Reset setelah berhasil
            // Load data toko
            $storeModel = $this->model('StoreModel');
            $store      = $storeModel->find($admin['store_id']);

            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['store_id']    = $admin['store_id'];
            $_SESSION['name']        = $admin['name'];
            $_SESSION['store_name']  = $store['name']        ?? APP_NAME;
            $_SESSION['store_logo']  = $store['logo']        ?? null;
            $_SESSION['store_color'] = $store['theme_color'] ?? '#3b82f6';

            // Load roles & permissions ke session
            $_SESSION['roles']       = $adminModel->getRoleNames($admin['id']);
            $_SESSION['permissions'] = $adminModel->getPermissionNames($admin['id']);

            // Fallback: admin belum punya role → auto-assign role Admin
            if (empty($_SESSION['roles'])) {
                $roleModel = $this->model('RoleModel');
                $adminRole = $roleModel->queryOne(
                    "SELECT id FROM roles WHERE store_id = ? AND name = 'Admin'",
                    [$admin['store_id']]
                );

                if ($adminRole) {
                    $roleModel->create([
                        'admin_id' => $admin['id'],
                        'role_id'  => $adminRole['id'],
                    ]);
                    $_SESSION['roles']       = $adminModel->getRoleNames($admin['id']);
                    $_SESSION['permissions'] = $adminModel->getPermissionNames($admin['id']);
                } else {
                    // Emergency fallback: beri semua permission
                    $permModel             = $this->model('PermissionModel');
                    $allPerms              = $permModel->all();
                    $_SESSION['permissions'] = array_column($allPerms, 'name');
                    $_SESSION['roles']       = ['Admin'];
                }
            }

            $this->redirect('dashboard');
        }

        $limiter->hit($rlKey);
        $remaining = $limiter->remainingAttempts($rlKey);
        $msg = 'Username atau password salah.';
        if ($remaining <= 2) $msg .= " ({$remaining} percobaan tersisa)";
        $this->flash('error', $msg);
        $this->redirect('login');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('login');
    }
}
