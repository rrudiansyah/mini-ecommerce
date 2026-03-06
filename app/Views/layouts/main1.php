<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';
$menuPerms = AuthHelper::getMenuPermissions();
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin' ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo"><?= APP_NAME ?></div>
    <nav>
        <?php if ($menuPerms['dashboard'] ?? false): ?>
        <a href="<?= BASE_URL ?>/dashboard">📊 Dashboard</a>
        <?php endif; ?>

        <?php if ($menuPerms['products']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/products">📦 Produk</a>
        <?php endif; ?>

        <?php if ($menuPerms['categories']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/categories">🗂 Kategori</a>
        <?php endif; ?>

        <?php if ($menuPerms['orders']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/orders">🛒 Pesanan</a>
        <?php endif; ?>

        <?php if ($menuPerms['reports'] ?? false): ?>
        <a href="<?= BASE_URL ?>/reports">📈 Laporan</a>
        <?php endif; ?>

        <?php if (AuthHelper::can('admins.manage')): ?>
        <a href="<?= BASE_URL ?>/users">👥 Pengguna</a>
        <?php endif; ?>

        <?php if (AuthHelper::can('settings.view')): ?>
        <a href="<?= BASE_URL ?>/settings">⚙️ Pengaturan</a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/logout">🚪 Logout</a>
    </nav>
</aside>
<main class="main-content">
    <div class="topbar">
        <h1><?= $pageTitle ?></h1>
        <span>Halo, <?= $_SESSION['name'] ?? 'Admin' ?> 👋
            <?= AuthHelper::getRoleBadge(AuthHelper::getRoles()[0] ?? 'Unknown') ?>
        </span>
    </div>
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
    <?php endif; ?>
    <div class="content">
        <?php require ROOT_PATH . "/app/Views/{$content}.php"; ?>
    </div>
</main>
</body>
</html>