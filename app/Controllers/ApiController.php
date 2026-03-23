<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiController extends Controller
{
    protected function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
        echo json_encode($data);
        exit;
    }

    protected function success(mixed $data = null, string $message = 'OK'): void
    {
        $this->json(['success' => true, 'message' => $message, 'data' => $data]);
    }

    protected function error(string $message, int $status = 400): void
    {
        $this->json(['success' => false, 'message' => $message], $status);
    }

    protected function generateToken(array $payload): string
    {
        $secret = $_ENV['JWT_SECRET'];
        $expiry = (int)($_ENV['JWT_EXPIRY'] ?? 86400);

        $payload = array_merge($payload, [
            'iat' => time(),
            'exp' => time() + $expiry,
        ]);

        return JWT::encode($payload, $secret, 'HS256');
    }

    protected function getAuthUser(): array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!str_starts_with($header, 'Bearer ')) {
            $this->error('Token tidak ditemukan.', 401);
        }

        $token = substr($header, 7);

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            $this->error('Token tidak valid atau sudah expired.', 401);
        }
    }

    protected function getBody(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }
}