<?php

// ── Output Buffering — harus PERTAMA sebelum apapun ──────────────
ob_start();

// ── Session Security ──────────────────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '0');   // off agar session tidak drop saat redirect
ini_set('session.gc_maxlifetime', '7200');
// cookie_secure: aktif hanya jika benar-benar HTTPS dari browser (bukan proxy internal)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
if ($isHttps) {
    ini_set('session.cookie_secure', '1');
}
session_start();

// ── Bootstrap ────────────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/database.php';

// ── Composer Autoload ─────────────────────────────────────────────
$vendorAutoload = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// ── Buat folder storage otomatis ─────────────────────────────────
foreach (['/storage', '/storage/logs', '/storage/rate_limit'] as $dir) {
    $full = ROOT_PATH . $dir;
    if (!is_dir($full)) {
        mkdir($full, 0755, true);
        if ($dir === '/storage') {
            file_put_contents($full . '/.htaccess', "Deny from all\n");
        }
    }
}

// ── Error display sesuai environment ─────────────────────────────
if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// ── Load core ────────────────────────────────────────────────────
foreach (['AppException', 'Database', 'Model', 'Controller', 'Router'] as $class) {
    require_once ROOT_PATH . "/core/{$class}.php";
}

$router = new Router();
require_once ROOT_PATH . '/routes/web.php';
$router->dispatch();

// Flush buffer jika belum di-flush oleh redirect/json
if (ob_get_level() > 0) ob_end_flush();
