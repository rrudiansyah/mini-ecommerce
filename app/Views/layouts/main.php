<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';
$menuPerms = AuthHelper::getMenuPermissions();
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
</head>
<body>

<!-- Mobile sidebar toggle -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
    <span></span><span></span><span></span>
</button>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <?php
        $sLogo  = $_SESSION['store_logo']  ?? null;
        $sName  = $_SESSION['store_name']  ?? APP_NAME;
        $sColor = $_SESSION['store_color'] ?? '#c9a84c';
        ?>
        <?php if ($sLogo): ?>
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($sLogo) ?>"
                 alt="<?= htmlspecialchars($sName) ?>"
                 class="sidebar-logo-img">
        <?php else: ?>
            <div class="sidebar-logo-fallback" style="background:<?= htmlspecialchars($sColor) ?>">
                <?= mb_strtoupper(mb_substr($sName, 0, 1)) ?>
            </div>
        <?php endif; ?>
        <span class="sidebar-store-name"><?= htmlspecialchars($sName) ?></span>
    </div>

    <nav>
        <?php if ($menuPerms['dashboard'] ?? false): ?>
        <a href="<?= BASE_URL ?>/dashboard" <?= str_contains($currentUrl, '/dashboard') ? 'class="active"' : '' ?>>
            📊 Dashboard
        </a>
        <?php endif; ?>

        <?php if ($menuPerms['products']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/products" <?= str_contains($currentUrl, '/products') ? 'class="active"' : '' ?>>
            📦 Produk
        </a>
        <?php endif; ?>

        <?php if ($menuPerms['categories']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/categories" <?= str_contains($currentUrl, '/categories') ? 'class="active"' : '' ?>>
            🗂 Kategori
        </a>
        <?php endif; ?>

        <?php if ($menuPerms['orders']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/orders" <?= str_contains($currentUrl, '/orders') ? 'class="active"' : '' ?>>
            🛒 Pesanan
        </a>
        <?php endif; ?>

        <?php if ($menuPerms['reports'] ?? false): ?>
        <a href="<?= BASE_URL ?>/reports" <?= str_contains($currentUrl, '/reports') ? 'class="active"' : '' ?>>
            📈 Laporan
        </a>
        <?php endif; ?>

        <?php if (AuthHelper::can('admins.manage')): ?>
        <a href="<?= BASE_URL ?>/users" <?= str_contains($currentUrl, '/users') ? 'class="active"' : '' ?>>
            👥 Pengguna
        </a>
        <?php endif; ?>

        <?php if (AuthHelper::isSuperAdmin()): ?>
        <a href="<?= BASE_URL ?>/settings" <?= str_contains($currentUrl, '/settings') ? 'class="active"' : '' ?>>
            ⚙️ Pengaturan
        </a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/logout" class="nav-logout" style="margin-top:8px">
            🚪 Logout
        </a>
    </nav>
</aside>

<!-- Main -->
<main class="main-content">
    <div class="topbar">
        <h1><?= htmlspecialchars($pageTitle ?? '') ?></h1>
        <div class="topbar-right">
            <div class="topbar-user">
                Halo, <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?> 👋
                <?= AuthHelper::getRoleBadge(AuthHelper::getRoles()[0] ?? 'Unknown') ?>
            </div>
        </div>
    </div>

    <?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <div class="content">
        <?php require ROOT_PATH . "/app/Views/{$content}.php"; ?>
    </div>
</main>

<script>
// Mobile sidebar toggle
const toggle   = document.getElementById('sidebarToggle');
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebarOverlay');

function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
}

toggle.addEventListener('click', () => {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
});
overlay.addEventListener('click', closeSidebar);

// Close on nav click (mobile)
sidebar.querySelectorAll('nav a').forEach(a => {
    a.addEventListener('click', () => {
        if (window.innerWidth < 768) closeSidebar();
    });
});

// Mark active nav
const path = window.location.pathname;
sidebar.querySelectorAll('nav a').forEach(a => {
    const href = a.getAttribute('href');
    if (href && href !== '/' && path.startsWith(href.replace(window.location.origin, ''))) {
        a.classList.add('active');
    }
});
</script>
</body>
</html>
