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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">

    <!-- PWA -->
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    <meta name="theme-color" content="#c9a84c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TokoAdmin">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/pwa/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= BASE_URL ?>/pwa/icon-192.png">
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

        <?php if ($menuPerms['variants'] ?? false): ?>
        <a href="<?= BASE_URL ?>/variants" <?= str_contains($currentUrl, '/variants') ? 'class="active"' : '' ?>>
            🎨 Varian Produk
        </a>
        <?php endif; ?>
        <?php if ($menuPerms['product_stock']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/product-stock" <?= str_contains($currentUrl, '/product-stock') ? 'class="active"' : '' ?>>
            📦 Stok Produk
        </a>
        <?php endif; ?>

        <?php if ($menuPerms['inventory']['visible'] ?? false): ?>
        <a href="<?= BASE_URL ?>/inventory" <?= str_contains($currentUrl, '/inventory') ? 'class="active"' : '' ?>
           style="position:relative">
            📦 Stok Bahan
            <?php if (!empty($navLowStockCount)): ?>
            <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                         background:#ef4444;color:#fff;font-size:10px;font-weight:700;
                         min-width:18px;height:18px;border-radius:100px;
                         display:flex;align-items:center;justify-content:center;padding:0 4px">
                <?= (int)$navLowStockCount ?>
            </span>
            <?php endif; ?>
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

<!-- ── BOTTOM NAVIGATION (mobile only) ── -->
<nav class="bottom-nav" id="bottomNav">
    <?php if ($menuPerms['dashboard'] ?? false): ?>
    <a href="<?= BASE_URL ?>/dashboard" class="bottom-nav-item <?= str_contains($currentUrl, '/dashboard') ? 'active' : '' ?>">
        <span class="bottom-nav-icon">📊</span>
        <span class="bottom-nav-label">Dashboard</span>
    </a>
    <?php endif; ?>

    <?php if ($menuPerms['orders']['visible'] ?? false): ?>
    <a href="<?= BASE_URL ?>/orders" class="bottom-nav-item <?= str_contains($currentUrl, '/orders') ? 'active' : '' ?>" id="ordersNavBtn">
        <span class="bottom-nav-icon">🛒</span>
        <span class="bottom-nav-label">Pesanan</span>
        <span class="bottom-nav-badge" id="orderBadge" style="display:none">0</span>
    </a>
    <?php endif; ?>

    <?php if ($menuPerms['products']['visible'] ?? false): ?>
    <a href="<?= BASE_URL ?>/products" class="bottom-nav-item <?= str_contains($currentUrl, '/products') ? 'active' : '' ?>">
        <span class="bottom-nav-icon">📦</span>
        <span class="bottom-nav-label">Produk</span>
    </a>
    <?php endif; ?>

    <?php if ($menuPerms['reports'] ?? false): ?>
    <a href="<?= BASE_URL ?>/reports" class="bottom-nav-item <?= str_contains($currentUrl, '/reports') ? 'active' : '' ?>">
        <span class="bottom-nav-icon">📈</span>
        <span class="bottom-nav-label">Laporan</span>
    </a>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/logout" class="bottom-nav-item">
        <span class="bottom-nav-icon">🚪</span>
        <span class="bottom-nav-label">Keluar</span>
    </a>
</nav>

<!-- ── PWA INSTALL BANNER ── -->
<div class="pwa-install-banner" id="pwaInstallBanner" style="display:none">
    <div class="pwa-install-content">
        <img src="<?= BASE_URL ?>/pwa/icon-72.png" alt="icon" class="pwa-install-icon">
        <div class="pwa-install-text">
            <strong>Install Aplikasi</strong>
            <span>Akses toko lebih cepat dari homescreen</span>
        </div>
    </div>
    <div class="pwa-install-actions">
        <button class="pwa-btn-install" id="pwaInstallBtn">Install</button>
        <button class="pwa-btn-dismiss" id="pwaDismissBtn">✕</button>
    </div>
</div>

<style>
/* ── Bottom Navigation ── */
.bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #1a1a2e;
    border-top: 1px solid #2d2d4e;
    z-index: 1000;
    padding-bottom: env(safe-area-inset-bottom);
}

.bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    padding: 8px 4px;
    text-decoration: none;
    color: #6b7280;
    font-size: 10px;
    position: relative;
    transition: color 0.2s;
    min-width: 0;
}

.bottom-nav-item.active {
    color: #c9a84c;
}

.bottom-nav-item:active {
    background: rgba(201, 168, 76, 0.1);
}

.bottom-nav-icon {
    font-size: 20px;
    margin-bottom: 2px;
    line-height: 1;
}

.bottom-nav-label {
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.bottom-nav-badge {
    position: absolute;
    top: 4px;
    right: calc(50% - 18px);
    background: #ef4444;
    color: white;
    font-size: 9px;
    font-weight: 700;
    min-width: 16px;
    height: 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}

@media (max-width: 767px) {
    .bottom-nav {
        display: flex;
    }
    .main-content {
        padding-bottom: calc(70px + env(safe-area-inset-bottom)) !important;
    }
    .sidebar-toggle {
        display: flex !important;
    }
}

/* ── PWA Install Banner ── */
.pwa-install-banner {
    position: fixed;
    bottom: 70px;
    left: 12px;
    right: 12px;
    background: #1a1a2e;
    border: 1px solid #c9a84c;
    border-radius: 16px;
    padding: 14px 16px;
    z-index: 999;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);   opacity: 1; }
}

.pwa-install-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
}

.pwa-install-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    flex-shrink: 0;
}

.pwa-install-text {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.pwa-install-text strong {
    color: white;
    font-size: 13px;
}

.pwa-install-text span {
    color: #9ca3af;
    font-size: 11px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pwa-install-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
}

.pwa-btn-install {
    background: #c9a84c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}

.pwa-btn-dismiss {
    background: transparent;
    color: #6b7280;
    border: none;
    font-size: 16px;
    cursor: pointer;
    padding: 4px;
}

@media (min-width: 768px) {
    .pwa-install-banner {
        bottom: 24px;
        left: auto;
        right: 24px;
        max-width: 360px;
    }
}
</style>

<script>
// ── Service Worker Registration ───────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => {
                console.log('[PWA] SW registered:', reg.scope);

                // Cek update SW
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            console.log('[PWA] New version available');
                        }
                    });
                });
            })
            .catch(err => console.log('[PWA] SW registration failed:', err));
    });
}

// ── PWA Install Prompt ────────────────────────────────────────────────
let deferredPrompt = null;
const installBanner = document.getElementById('pwaInstallBanner');
const installBtn    = document.getElementById('pwaInstallBtn');
const dismissBtn    = document.getElementById('pwaDismissBtn');

// Simpan dismissed state di sessionStorage (tidak butuh HTTPS)
function isDismissed() {
    try { return sessionStorage.getItem('pwa-dismissed') === '1'; } catch(e) { return false; }
}
function setDismissed() {
    try { sessionStorage.setItem('pwa-dismissed', '1'); } catch(e) {}
}

// Deteksi iOS Safari
const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
const isSafari = /safari/i.test(navigator.userAgent) && !/chrome|crios/i.test(navigator.userAgent);
const isInStandaloneMode = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

// iOS Safari — tampilkan petunjuk manual
if (isIOS && isSafari && !isInStandaloneMode && !isDismissed()) {
    setTimeout(() => {
        if (installBanner) {
            if (installBtn) {
                installBtn.textContent = 'Cara Install';
                installBtn.addEventListener('click', () => {
                    alert('Cara install di iOS Safari:\n\n1. Tap tombol Share (⬆️) di bawah\n2. Scroll ke bawah\n3. Tap "Add to Home Screen"\n4. Tap "Add"');
                    if (installBanner) installBanner.style.display = 'none';
                    setDismissed();
                }, { once: true });
            }
            installBanner.style.display = 'flex';
        }
    }, 3000);
}

// Android/Chrome — gunakan beforeinstallprompt
window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredPrompt = e;
    if (!isDismissed() && !isInStandaloneMode) {
        setTimeout(() => {
            if (installBanner) installBanner.style.display = 'flex';
        }, 3000);
    }
});

if (installBtn && !isIOS) {
    installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        if (installBanner) installBanner.style.display = 'none';
        setDismissed();
    });
}

// Tombol X — fix dismiss
if (dismissBtn) {
    dismissBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (installBanner) installBanner.style.display = 'none';
        setDismissed();
    });
}

// Sembunyikan banner jika sudah terinstall
window.addEventListener('appinstalled', () => {
    if (installBanner) installBanner.style.display = 'none';
    setDismissed();
});

// ── Push Notification Setup ───────────────────────────────────────
async function subscribePushNotification() {
    if (!('PushManager' in window)) return;

    try {
        const reg = await navigator.serviceWorker.ready;
        const permission = await Notification.requestPermission();

        if (permission !== 'granted') return;

        // VAPID public key — ganti dengan key Anda sendiri
        // Generate di: https://web-push-codelab.glitch.me/
        const VAPID_PUBLIC_KEY = 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U';

        const subscription = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });

        // Kirim subscription ke server
        await fetch('/push/subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(subscription)
        });

        console.log('[PWA] Push subscribed');
    } catch (err) {
        console.log('[PWA] Push subscription failed:', err);
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; i++) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Auto subscribe push notification
if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.ready.then(() => {
        if (Notification.permission === 'default') {
            // Tanya permission setelah 5 detik
            setTimeout(subscribePushNotification, 5000);
        } else if (Notification.permission === 'granted') {
            subscribePushNotification();
        }
    });
}

// ── Order Badge (polling setiap 30 detik) ────────────────────────
const orderBadge = document.getElementById('orderBadge');
if (orderBadge) {
    async function checkNewOrders() {
        try {
            const res = await fetch('/orders/count-pending', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const count = data.count || 0;
            if (count > 0) {
                orderBadge.textContent = count > 99 ? '99+' : count;
                orderBadge.style.display = 'flex';
            } else {
                orderBadge.style.display = 'none';
            }
        } catch (e) {}
    }
    checkNewOrders();
    setInterval(checkNewOrders, 30000);
}
</script>
</body>
</html>
