<?php

class AuthApiController extends ApiController
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $body     = $this->getBody();
        $username = trim($body['username'] ?? '');
        $password = trim($body['password'] ?? '');

        if (!$username || !$password) {
            $this->error('Username dan password wajib diisi.');
        }

        $adminModel = $this->model('AdminModel');
        $user       = $adminModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->error('Username atau password salah.', 401);
        }

        $storeModel = $this->model('StoreModel');
        $store      = $storeModel->find($user['store_id']);

        if (!$store || !$store['is_active']) {
            $this->error('Toko tidak aktif. Hubungi administrator.', 403);
        }

        $token = $this->generateToken([
            'user_id'  => $user['id'],
            'store_id' => $user['store_id'],
            'name'     => $user['name'],
            'role'     => $user['role'] ?? 'kasir',
        ]);

        $this->success([
            'token' => $token,
            'user'  => [
                'id'       => $user['id'],
                'name'     => $user['name'],
                'role'     => $user['role'] ?? 'kasir',
                'store_id' => $user['store_id'],
                'store'    => $store['name'],
            ],
        ], 'Login berhasil.');
    }

    public function me(): void
    {
        $user = $this->getAuthUser();
        $this->success($user);
    }
}