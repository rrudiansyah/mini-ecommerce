<?php

class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(): void
    {
        ob_start(); // Buffer semua output — pastikan header() selalu bisa dipanggil
        $httpMethod = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = preg_replace('#^/public#', '', $uri);
        $uri = '/' . trim($uri, '/');

        // Route yang lebih spesifik (tanpa param) diprioritaskan
        $routes = $this->routes[$httpMethod] ?? [];
        uksort($routes, function ($a, $b) {
            $aScore = substr_count($a, '{');
            $bScore = substr_count($b, '{');
            if ($aScore !== $bScore) return $aScore - $bScore;
            return strlen($b) - strlen($a);
        });

        foreach ($routes as $route => $action) {
            $pattern = preg_quote($route, '#');
            $pattern = preg_replace('/\\\{[^}]+\\\}/', '([^/]+)', $pattern);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $controllerFile = ROOT_PATH . "/app/Controllers/{$action['controller']}.php";
                if (!file_exists($controllerFile)) {
                    $this->abort(404, "Halaman tidak ditemukan.");
                    return;
                }

                try {
                    // Load ApiController dulu jika controller yang dipanggil adalah turunannya
                    $apiBaseFile = ROOT_PATH . '/app/Controllers/ApiController.php';
                    if (file_exists($apiBaseFile)) {
                        require_once $apiBaseFile;
                    }
                    require_once $controllerFile;
                    $ctrl = new $action['controller']();

                    if (!method_exists($ctrl, $action['method'])) {
                        $this->abort(404, "Halaman tidak ditemukan.");
                        return;
                    }

                    call_user_func_array([$ctrl, $action['method']], $matches);

                } catch (AppException $e) {
                    // Error yang sudah di-handle (dari Model, validasi, dll)
                    $this->handleAppError($e);

                } catch (PDOException $e) {
                    // PDO error yang tidak tertangkap di Model
                    $this->handleAppError(
                        new AppException("Terjadi kesalahan pada database. Silakan coba lagi.", 500, $e)
                    );

                } catch (\Throwable $e) {
                    // Error tak terduga lainnya
                    $this->handleUnexpectedError($e);
                }

                return;
            }
        }

        $this->abort(404, "Halaman tidak ditemukan.");
    }

    private function handleAppError(AppException $e): void
    {
        // Jika request AJAX/JSON — kembalikan JSON
        $isJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
               || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');

        if ($isJson) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }

        // Jika request biasa — flash error dan redirect back
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'] = ['type' => 'error', 'message' => $e->getMessage()];

        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/dashboard');
        header("Location: {$referer}");
        exit;
    }

    private function handleUnexpectedError(\Throwable $e): void
    {
        // Log detail error
        $logDir = ROOT_PATH . '/storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        @error_log(
            sprintf("[%s] UNEXPECTED: %s in %s:%d\n%s\n",
                date('Y-m-d H:i:s'),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            ),
            3,
            $logDir . '/app_error.log'
        );

        $isJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
               || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');

        if ($isJson) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.']);
            exit;
        }

        http_response_code(500);
        echo $this->errorPage(
            'Terjadi Kesalahan',
            'Terjadi kesalahan pada server. Tim kami sedang menanganinya.',
            defined('APP_ENV') && APP_ENV === 'development' ? $e->getMessage() . "\n\n" . $e->getTraceAsString() : null
        );
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo $this->errorPage(
            $code === 404 ? 'Halaman Tidak Ditemukan' : 'Error ' . $code,
            $message
        );
        exit;
    }

    private function errorPage(string $title, string $message, ?string $detail = null): string
    {
        $detailHtml = $detail
            ? '<pre style="background:#f1f5f9;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;text-align:left;margin-top:16px;max-width:700px">' . htmlspecialchars($detail) . '</pre>'
            : '';

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width,initial-scale=1">
            <title>{$title}</title>
            <style>
                *{margin:0;padding:0;box-sizing:border-box}
                body{font-family:'Segoe UI',sans-serif;background:#f8fafc;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
                .box{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:48px 40px;max-width:520px;width:100%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,.06)}
                .icon{font-size:48px;margin-bottom:16px}
                h1{font-size:22px;color:#1e293b;margin-bottom:10px}
                p{font-size:15px;color:#64748b;line-height:1.6;margin-bottom:24px}
                .btn{display:inline-block;padding:10px 24px;background:#3b82f6;color:#fff;border-radius:100px;text-decoration:none;font-size:14px;font-weight:600;transition:background .2s}
                .btn:hover{background:#2563eb}
                .btn-sec{background:#f1f5f9;color:#475569;margin-left:10px}
                .btn-sec:hover{background:#e2e8f0}
            </style>
        </head>
        <body>
            <div class="box">
                <div class="icon">⚠️</div>
                <h1>{$title}</h1>
                <p>{$message}</p>
                <a href="javascript:history.back()" class="btn btn-sec">← Kembali</a>
                <a href="{$_SERVER['REQUEST_URI']}" onclick="window.location.reload();return false" class="btn">Coba Lagi</a>
                {$detailHtml}
            </div>
        </body>
        </html>
        HTML;
    }
}
