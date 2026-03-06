<?php

// ─────────────────────────────────────────────────────────────
// Load .env jika ada (untuk development lokal tanpa Docker)
// Di Docker, env vars sudah di-inject langsung oleh docker-compose
// ─────────────────────────────────────────────────────────────
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER)) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

// Helper: ambil env variable dengan fallback
function env(string $key, mixed $default = null): mixed {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ─────────────────────────────────────────────────────────────
// Database
// ─────────────────────────────────────────────────────────────
define('DB_HOST',    env('DB_HOST', 'db'));         // 'db' = nama service Docker
define('DB_USER',    env('DB_USER', 'admin'));
define('DB_PASS',    env('DB_PASSWORD', 'admin123'));
define('DB_NAME',    env('DB_NAME', 'ecommerce_builder'));
define('DB_CHARSET', 'utf8mb4');

// ─────────────────────────────────────────────────────────────
// App
// ─────────────────────────────────────────────────────────────
define('BASE_URL',   rtrim(env('APP_URL', 'http://localhost'), '/'));
define('ROOT_PATH',  dirname(__DIR__));
define('APP_NAME',   env('APP_NAME', 'Mini E-Commerce Builder'));
define('APP_ENV',    env('APP_ENV', 'development'));

// Upload
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads/');
define('UPLOAD_URL',  BASE_URL  . '/uploads/');
define('MAX_FILE_SIZE', (int) env('MAX_FILE_SIZE', 2097152)); // 2MB default
