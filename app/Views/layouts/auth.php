<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
    <style>
    /* ── RESET AUTH PAGE ─────────────────────────── */
    body.auth-page {
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f0ede8;
        position: relative;
        overflow: hidden;
    }

    /* ── BACKGROUND BLOBS ────────────────────────── */
    body.auth-page::before,
    body.auth-page::after {
        content: '';
        position: fixed;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        z-index: 0;
    }
    body.auth-page::before {
        width: 500px; height: 500px;
        background: radial-gradient(ellipse, rgba(201,168,76,.2), transparent 70%);
        top: -150px; left: -100px;
        animation: blobA 14s ease-in-out infinite;
    }
    body.auth-page::after {
        width: 400px; height: 400px;
        background: radial-gradient(ellipse, rgba(196,75,43,.12), transparent 70%);
        bottom: -100px; right: -80px;
        animation: blobA 18s ease-in-out infinite reverse;
    }
    @keyframes blobA {
        0%,100% { transform: translate(0,0) scale(1); }
        50% { transform: translate(30px,-20px) scale(1.08); }
    }

    /* ── CARD ────────────────────────────────────── */
    .auth-box {
        position: relative; z-index: 1;
        width: 100%;
        max-width: 420px;
        margin: 24px 16px;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(15,14,12,.10), 0 4px 16px rgba(15,14,12,.06);
        overflow: hidden;
        animation: cardIn .6s cubic-bezier(.34,1.56,.64,1) both;
    }
    @keyframes cardIn {
        from { opacity:0; transform: translateY(32px) scale(.97); }
        to   { opacity:1; transform: translateY(0) scale(1); }
    }

    /* ── TOP ACCENT BAR ──────────────────────────── */
    .auth-top-bar {
        height: 4px;
        background: linear-gradient(90deg, #c9a84c, #c44b2b, #3d6b4f);
    }

    /* ── HEADER ──────────────────────────────────── */
    .auth-header {
        padding: 36px 40px 0;
        text-align: center;
    }
    .auth-logo {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px; height: 64px;
        border-radius: 18px;
        background: linear-gradient(135deg, #0f0e0c, #2d2b28);
        font-size: 28px;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px rgba(15,14,12,.15);
    }
    .auth-app-name {
        font-family: 'Instrument Serif', serif;
        font-size: 13px;
        font-style: italic;
        color: #c9a84c;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .auth-title {
        font-family: 'Instrument Serif', serif;
        font-size: 28px;
        font-weight: 400;
        letter-spacing: -.02em;
        color: #0f0e0c;
        line-height: 1.2;
        margin: 0 0 6px;
    }
    .auth-subtitle {
        font-size: 13px;
        color: #9a9080;
        font-weight: 400;
        margin: 0 0 28px;
    }

    /* ── ALERT ───────────────────────────────────── */
    .auth-body {
        padding: 0 40px 36px;
    }
    .alert {
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert::before { font-size: 15px; }
    .alert-danger  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-danger::before { content: '⚠️'; }
    .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-success::before { content: '✓'; }
    .alert-info    { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
    .alert-info::before { content: 'ℹ'; }

    /* ── OVERRIDE FORM STYLES ────────────────────── */
    body.auth-page .form-group {
        margin-bottom: 16px;
    }
    body.auth-page .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #7a7060;
        margin-bottom: 6px;
    }
    body.auth-page .form-group input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e8e0d0;
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        color: #0f0e0c;
        background: #faf8f4;
        outline: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
        box-sizing: border-box;
    }
    body.auth-page .form-group input:focus {
        border-color: #c9a84c;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(201,168,76,.12);
    }
    body.auth-page .form-group input::placeholder {
        color: #c0b8a8;
    }

    /* ── BUTTON ──────────────────────────────────── */
    body.auth-page .btn-primary.btn-block,
    body.auth-page button[type="submit"] {
        width: 100%;
        padding: 14px;
        background: #0f0e0c;
        color: #faf8f4;
        border: none;
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .02em;
        cursor: pointer;
        transition: all .25s;
        margin-top: 8px;
        box-shadow: 0 4px 16px rgba(15,14,12,.15);
    }
    body.auth-page .btn-primary.btn-block:hover,
    body.auth-page button[type="submit"]:hover {
        background: #c44b2b;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(196,75,43,.25);
    }
    body.auth-page .btn-primary.btn-block:active,
    body.auth-page button[type="submit"]:active {
        transform: translateY(0);
    }

    /* ── HIDE DEFAULT H2 from admin.css ─────────── */
    .auth-box > h2 { display: none; }

    /* ── FOOTER LINK ─────────────────────────────── */
    .auth-footer {
        padding: 0 40px 28px;
        text-align: center;
    }
    .auth-footer a {
        font-size: 13px;
        color: #9a9080;
        text-decoration: none;
        transition: color .2s;
    }
    .auth-footer a:hover { color: #0f0e0c; }

    /* ── SUPERADMIN BADGE ────────────────────────── */
    body.auth-page h2 {
        display: none; /* hidden — replaced by auth-header */
    }

    /* ── DIVIDER ─────────────────────────────────── */
    .auth-divider {
        height: 1px;
        background: #f0ede8;
        margin: 0 40px 24px;
    }

    /* ── RESPONSIVE ──────────────────────────────── */
    @media (max-width: 480px) {
        .auth-box {
            margin: 16px 12px;
            border-radius: 20px;
        }
        .auth-header { padding: 28px 28px 0; }
        .auth-body   { padding: 0 28px 28px; }
        .auth-footer { padding: 0 28px 24px; }
        .auth-divider { margin: 0 28px 20px; }
        .auth-logo { width: 56px; height: 56px; font-size: 24px; }
        .auth-title { font-size: 24px; }
    }

    @media (min-width: 768px) {
        .auth-box { max-width: 440px; }
    }

    @media (min-width: 1024px) {
        body.auth-page { background: #ebe7e0; }
        .auth-box { max-width: 460px; }
    }
    </style>
</head>
<body class="auth-page">

<div class="auth-box">
    <!-- Top accent bar -->
    <div class="auth-top-bar"></div>

    <!-- Header -->
    <div class="auth-header">
        <div class="auth-logo">🏪</div>
        <div class="auth-app-name"><?= htmlspecialchars(APP_NAME ?? 'Mini E-Commerce') ?></div>
        <h1 class="auth-title"><?= htmlspecialchars($pageTitle ?? 'Login') ?></h1>
        <p class="auth-subtitle">Masukkan kredensial Anda untuk melanjutkan</p>
    </div>

    <!-- Flash message -->
    <div class="auth-body">
        <?php if ($flash = $_SESSION['flash'] ?? null): unset($_SESSION['flash']); ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info') ?>">
            <?= htmlspecialchars($flash['message'] ?? '') ?>
        </div>
        <?php endif; ?>

        <?php require ROOT_PATH . "/app/Views/{$content}.php"; ?>
    </div>

</div>

</body>
</html>
