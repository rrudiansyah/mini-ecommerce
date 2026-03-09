<?php
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Super Admin') ?> — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
    <style>
        .sidebar { background: #0a0908; }
        .sidebar-logo { color: #c9a84c; border-bottom: 1px solid rgba(255,255,255,.07); }
        .sidebar nav a.active::before { background: #c9a84c; }
        .topbar { border-bottom: 1px solid #e8e3db; }
        .sidebar-sa-badge {
            display: inline-block;
            font-size: 9px; font-weight: 800;
            letter-spacing: .1em; text-transform: uppercase;
            background: rgba(201,168,76,.15);
            color: #c9a84c;
            padding: 3px 8px; border-radius: 100px;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<!-- Mobile toggle -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
    <span></span><span></span><span></span>
</button>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        ⚡ <?= htmlspecialchars(APP_NAME) ?>
        <span class="sidebar-sa-badge">Super Admin</span>
    </div>
    <nav>
        <a href="<?= BASE_URL ?>/superadmin/dashboard"
           <?= str_contains($currentUrl, '/superadmin/dashboard') ? 'class="active"' : '' ?>>
            📊 Dashboard
        </a>
        <a href="<?= BASE_URL ?>/superadmin/stores"
           <?= str_contains($currentUrl, '/superadmin/stores') && !str_contains($currentUrl, '/create') ? 'class="active"' : '' ?>>
            🏪 Kelola Toko
        </a>
        <a href="<?= BASE_URL ?>/superadmin/stores/create"
           <?= str_contains($currentUrl, '/superadmin/stores/create') ? 'class="active"' : '' ?>>
            ➕ Tambah Toko
        </a>
        <a href="<?= BASE_URL ?>/superadmin/settings"
           <?= str_contains($currentUrl, '/superadmin/settings') ? 'class="active"' : '' ?>>
            ⚙️ Pengaturan
        </a>
        <a href="<?= BASE_URL ?>/superadmin/logout" class="nav-logout" style="margin-top:8px">
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
                <?= htmlspecialchars($_SESSION['superadmin_name'] ?? 'Super Admin') ?>
                <span class="superadmin-badge">SUPER ADMIN</span>
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
const toggle  = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');

function openSidebar()  { sidebar.classList.add('open');    overlay.classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow = ''; }

toggle.addEventListener('click',  () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
overlay.addEventListener('click', closeSidebar);
sidebar.querySelectorAll('nav a').forEach(a => a.addEventListener('click', () => { if (window.innerWidth < 768) closeSidebar(); }));
</script>
</body>
</html>
