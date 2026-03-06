<?php

class SuperAuthController extends Controller
{
    public function loginForm(): void
    {
        if (!empty($_SESSION['superadmin_id'])) {
            $this->redirect('superadmin/dashboard');
        }
        $this->view('layouts/auth', [
            'pageTitle' => 'Super Admin Login',
            'content'   => 'superadmin/login',
            'csrf_field'  => $this->csrfField(),
        ]);
    }

    public function login(): void
    {
        $this->validateCsrf();

        // ── Rate limiting ────────────────────────────────────────
        require_once ROOT_PATH . '/core/RateLimiter.php';
        $limiter = new RateLimiter(5, 300);
        $rlKey   = RateLimiter::key('superadmin_login');

        if ($limiter->tooManyAttempts($rlKey)) {
            $wait = $limiter->availableIn($rlKey);
            $this->flash('error', "Terlalu banyak percobaan. Coba lagi dalam {$wait} detik.");
            $this->redirect('superadmin/login');
            return;
        }

        $username = $this->input('username');
        $password = $this->input('password');

        $model = $this->model('SuperAdminModel');
        $admin = $model->findByUsername($username);

        if ($admin && password_verify($password, $admin['password'])) {
            $limiter->clear($rlKey);
            $_SESSION['superadmin_id']   = $admin['id'];
            $_SESSION['superadmin_name'] = $admin['name'];
            $this->redirect('superadmin/dashboard');
        }

        $limiter->hit($rlKey);
        $this->flash('error', 'Username atau password salah.');
        $this->redirect('superadmin/login');
    }

    public function logout(): void
    {
        unset($_SESSION['superadmin_id'], $_SESSION['superadmin_name']);
        $this->redirect('superadmin/login');
    }
}
