<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin' ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
</head>
<body>
<main class="main-content" style="padding:0; margin:0">
    <div class="content" style="max-width:800px; margin:0 auto; padding:20px">
        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
        <?php endif; ?>
        <?php require ROOT_PATH . "/app/Views/{$content}.php"; ?>
    </div>
</main>

<style>
@media print {
    .btn, a.btn, .no-print { display:none !important; }
    body { margin:0; padding:0; }
    .main-content { padding:0; margin:0; }
}
</style>
</body>
</html>
