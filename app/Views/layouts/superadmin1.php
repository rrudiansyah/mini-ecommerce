<?php $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Super Admin' ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
    <style>
        .sidebar { background: #0f172a; }
        .sidebar-logo { color: #f59e0b; border-color: #1e293b; }
        .sidebar-logo small { display:block; font-size:10px; color:#475569; letter-spacing:.1em; text-transform:uppercase; margin-top:2px; }
        .sidebar nav a { color: #94a3b8; }
        .sidebar nav a:hover { background: #1e293b; color: #fff; }
        .topbar { border-bottom-color: #e2e8f0; }
        .superadmin-badge { background:#fef3c7; color:#92400e; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:700; }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">
        ⚡ <?= APP_NAME ?>
        <small>Super Admin Panel</small>
    </div>
    <nav>
        <a href="<?= BASE_URL ?>/superadmin/dashboard">📊 Dashboard</a>
        <a href="<?= BASE_URL ?>/superadmin/stores">🏪 Kelola Toko</a>
        <a href="<?= BASE_URL ?>/superadmin/stores/create">➕ Tambah Toko</a>
        <a href="<?= BASE_URL ?>/superadmin/logout" style="margin-top:auto">🚪 Logout</a>
    </nav>
</aside>
<main class="main-content">
    <div class="topbar">
        <h1><?= $pageTitle ?></h1>
        <span>
            <?= htmlspecialchars($_SESSION['superadmin_name'] ?? 'Super Admin') ?>
            <span class="superadmin-badge">SUPER ADMIN</span>
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
