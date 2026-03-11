<?php

abstract class Controller
{
    protected function model(string $model): object
    {
        $file = ROOT_PATH . "/app/Models/{$model}.php";
        if (!file_exists($file)) die("Model [{$model}] tidak ditemukan.");
        require_once $file;
        return new $model();
    }

    protected function view(string $view, array $data = []): void
    {
        // Auto-inject navLowStockCount untuk badge stok menipis di sidebar
        // Hanya hitung jika user sudah login, punya permission inventory, dan belum di-set
        if (!isset($data['navLowStockCount']) && !empty($_SESSION['store_id'])) {
            $perms = $_SESSION['permissions'] ?? [];
            if (in_array('inventory.read', $perms)) {
                $ingFile = ROOT_PATH . '/app/Models/IngredientModel.php';
                if (file_exists($ingFile)) {
                    require_once $ingFile;
                    $data['navLowStockCount'] = count((new IngredientModel())->lowStock((int)$_SESSION['store_id']));
                }
            }
        }
        if (!isset($data['navLowStockCount'])) {
            $data['navLowStockCount'] = 0;
        }

        extract($data);
        $file = ROOT_PATH . "/app/Views/{$view}.php";
        if (!file_exists($file)) die("View [{$view}] tidak ditemukan.");
        require $file; // pakai require, bukan require_once — izinkan render ulang
    }

    protected function redirect(string $path): void
    {
        // Bersihkan buffer output sebelum redirect
        if (ob_get_level() > 0) ob_clean();
        header("Location: " . BASE_URL . "/{$path}");
        exit;
    }

    protected function json(mixed $data, int $code = 200): void
    {
        if (ob_get_level() > 0) ob_clean();
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : $default;
    }

    protected function inputs(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->input($key);
        }
        return $result;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function uploadImage(string $inputName, string $folder = 'products'): string|false
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== 0) return false;
        $file    = $_FILES[$inputName];
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed) || $file['size'] > 2097152) return false;
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $dest     = ROOT_PATH . "/public/uploads/{$folder}/{$filename}";
        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0755, true);
        return move_uploaded_file($file['tmp_name'], $dest) ? "uploads/{$folder}/{$filename}" : false;
    }

    /**
     * Require basic authentication - checks if admin is logged in
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('login');
        }
    }


    // ── CSRF Protection ───────────────────────────────────────────

    /**
     * Generate CSRF token dan simpan di session
     */
    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Render hidden input CSRF untuk ditaruh di dalam form
     */
    protected function csrfField(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . $this->csrfToken() . '">';
    }

    /**
     * Validasi CSRF token dari POST request
     * Otomatis redirect + flash error jika tidak valid
     */
    protected function validateCsrf(): void
    {
        $token  = $_POST['_csrf_token'] ?? '';
        $stored = $_SESSION['csrf_token'] ?? '';

        if (empty($token) || empty($stored) || !hash_equals($stored, $token)) {
            // Regenerate token
            unset($_SESSION['csrf_token']);

            // Jika AJAX, return JSON
            $isJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
                   || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
            if ($isJson) {
                http_response_code(419);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Session expired. Silakan refresh halaman.']);
                exit;
            }

            $this->flash('error', 'Session expired. Silakan coba lagi.');
            // Bersihkan buffer sebelum redirect
            if (ob_get_level() > 0) ob_clean();
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . '/dashboard'));
            exit;
        }

       
    }

    /**
     * Require SuperAdmin session
     */
    protected function requireSuperAdmin(): void
    {
        if (empty($_SESSION['superadmin_id'])) {
            $this->redirect('superadmin/login');
        }
    }

    /**
     * Require specific permission
     */
    protected function requirePermission(string $permission): void
    {
        require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';

        if (!AuthHelper::can($permission)) {
            http_response_code(403);
            die('Akses ditolak: Anda tidak memiliki izin untuk mengakses fitur ini.');
        }
    }

    /**
     * Require one of several permissions
     */
    protected function requirePermissionAny(array $permissions): void
    {
        require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';

        if (!AuthHelper::canAny($permissions)) {
            http_response_code(403);
            die('Akses ditolak: Anda tidak memiliki izin untuk mengakses fitur ini.');
        }
    }

    /**
     * Require all of several permissions
     */
    protected function requirePermissionAll(array $permissions): void
    {
        require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';

        if (!AuthHelper::canAll($permissions)) {
            http_response_code(403);
            die('Akses ditolak: Anda tidak memiliki izin untuk mengakses fitur ini.');
        }
    }

    /**
     * Require specific role
     */
    protected function requireRole(string|array $roles): void
    {
        require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';

        $roles = is_string($roles) ? [$roles] : $roles;
        $userRoles = $_SESSION['roles'] ?? [];

        if (!array_intersect($roles, $userRoles)) {
            http_response_code(403);
            die('Akses ditolak: Role Anda tidak memiliki akses ke fitur ini.');
        }
    }

    /**
     * Check if user can perform an action (returns boolean)
     */
    protected function can(string $permission): bool
    {
        require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';
        return AuthHelper::can($permission);
    }

    /**
     * Check if user has a role (returns boolean)
     */
    protected function hasRole(string $role): bool
    {
        require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';
        return AuthHelper::hasRole($role);
    }
}

