<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
</head>
<body class="auth-page">
    <div class="auth-box">
        <h2><?= APP_NAME ?></h2>
        <?php if ($flash = $_SESSION['flash'] ?? null): unset($_SESSION['flash']); ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>
        <?php require ROOT_PATH . "/app/Views/{$content}.php"; ?>
    </div>
</body>
</html>