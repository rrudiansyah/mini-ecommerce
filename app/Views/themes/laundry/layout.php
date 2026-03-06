<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($store['name'] ?? '') ?> — Laundry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&family=Fraunces:ital,wght@0,300;0,600;1,300&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
        --bg:       #f7fbff;
        --bg2:      #eef5fc;
        --white:    #ffffff;
        --blue:     #2e86de;
        --blue-lt:  #5ba4e8;
        --blue-dk:  #1a5fa8;
        --sky:      #dbeafe;
        --sky2:     #bfdbfe;
        --text:     #1e3a5f;
        --muted:    #6b8cae;
        --border:   #d0e4f7;
        --success:  #10b981;
        --radius:   16px;
        --shadow:   0 4px 24px rgba(46,134,222,.10);
        --font-d:   'Fraunces', serif;
        --font-b:   'Nunito', sans-serif;
    }

    html { scroll-behavior: smooth; }
    body {
        background: var(--bg);
        color: var(--text);
        font-family: var(--font-b);
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* ── NAVBAR ─────────────────────────────── */
    nav {
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 100;
        padding: 16px 48px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all .3s;
    }
    nav.scrolled {
        background: rgba(247,251,255,.92);
        backdrop-filter: blur(12px);
        box-shadow: 0 1px 0 var(--border);
    }
    .nav-logo {
        font-family: var(--font-d);
        font-size: 22px;
        color: var(--blue);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .nav-logo-icon {
        width: 36px; height: 36px;
        background: var(--blue);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .nav-links { display: flex; gap: 32px; align-items: center; }
    .nav-links a {
        color: var(--muted);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: .03em;
        transition: color .2s;
    }
    .nav-links a:hover { color: var(--blue); }
    .nav-order-btn {
        background: var(--blue);
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 100px;
        font-family: var(--font-b);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        letter-spacing: .03em;
    }
    .nav-order-btn:hover { background: var(--blue-dk); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(46,134,222,.35); }

    /* ── HERO ───────────────────────────────── */
    #hero {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 60px;
        padding: 120px 80px 80px;
        position: relative;
        overflow: hidden;
    }
    /* Bubble decorations */
    .hero-bubble {
        position: absolute;
        border-radius: 50%;
        background: var(--sky);
        opacity: .5;
        pointer-events: none;
    }
    .hero-bubble-1 { width: 400px; height: 400px; top: -100px; right: -80px; }
    .hero-bubble-2 { width: 200px; height: 200px; bottom: 60px; left: -60px; background: var(--sky2); }
    .hero-bubble-3 { width: 120px; height: 120px; top: 40%; right: 45%; opacity: .3; }

    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--sky);
        color: var(--blue);
        padding: 6px 14px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        margin-bottom: 20px;
        opacity: 0;
        animation: fadeUp .7s .1s ease forwards;
    }
    .hero-title {
        font-family: var(--font-d);
        font-size: clamp(2.8rem, 5vw, 4.2rem);
        line-height: 1.1;
        color: var(--text);
        margin-bottom: 20px;
        opacity: 0;
        animation: fadeUp .7s .25s ease forwards;
    }
    .hero-title em { color: var(--blue); font-style: italic; }
    .hero-sub {
        font-size: 16px;
        color: var(--muted);
        max-width: 440px;
        margin-bottom: 36px;
        opacity: 0;
        animation: fadeUp .7s .4s ease forwards;
    }
    .hero-actions {
        display: flex;
        gap: 14px;
        align-items: center;
        opacity: 0;
        animation: fadeUp .7s .55s ease forwards;
    }
    .btn-primary {
        background: var(--blue);
        color: #fff;
        padding: 14px 30px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        transition: all .2s;
        border: none;
        cursor: pointer;
        font-family: var(--font-b);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-primary:hover { background: var(--blue-dk); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(46,134,222,.35); }
    .btn-secondary {
        color: var(--blue);
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: gap .2s;
    }
    .btn-secondary:hover { gap: 10px; }

    /* Hero visual */
    .hero-visual {
        position: relative;
        opacity: 0;
        animation: fadeLeft .8s .3s ease forwards;
    }
    .hero-card-main {
        background: var(--white);
        border-radius: 24px;
        padding: 32px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        position: relative;
        z-index: 1;
    }
    .washing-anim {
        font-size: 80px;
        text-align: center;
        margin-bottom: 16px;
        display: block;
        animation: spin 4s linear infinite;
    }
    @keyframes spin {
        0%, 100% { transform: rotate(-8deg); }
        50% { transform: rotate(8deg); }
    }
    .hero-card-label {
        text-align: center;
        font-size: 14px;
        color: var(--muted);
        font-weight: 600;
    }
    .hero-card-title {
        text-align: center;
        font-family: var(--font-d);
        font-size: 22px;
        color: var(--text);
        margin-top: 4px;
    }
    /* Floating badges */
    .float-badge {
        position: absolute;
        background: var(--white);
        border-radius: 14px;
        padding: 10px 16px;
        box-shadow: 0 8px 24px rgba(46,134,222,.15);
        border: 1px solid var(--border);
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
        animation: floatY 3s ease-in-out infinite;
    }
    .float-badge-1 { top: -16px; right: -16px; animation-delay: 0s; }
    .float-badge-2 { bottom: -16px; left: -16px; animation-delay: 1s; }
    .float-badge .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--success); }
    @keyframes floatY {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    /* Hero trust bar */
    .hero-trust {
        display: flex;
        gap: 24px;
        margin-top: 32px;
        padding-top: 28px;
        border-top: 1px solid var(--border);
        opacity: 0;
        animation: fadeUp .7s .7s ease forwards;
    }
    .trust-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--muted); font-weight: 600; }
    .trust-icon { font-size: 18px; }

    /* ── SERVICES ───────────────────────────── */
    #services {
        padding: 100px 80px;
        background: var(--white);
    }
    .section-header { text-align: center; margin-bottom: 56px; }
    .section-tag {
        display: inline-block;
        background: var(--sky);
        color: var(--blue);
        padding: 5px 16px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }
    .section-title {
        font-family: var(--font-d);
        font-size: clamp(2rem, 3.5vw, 2.8rem);
        color: var(--text);
        line-height: 1.2;
    }
    .section-sub { font-size: 15px; color: var(--muted); margin-top: 12px; }

    /* Service cards */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .service-card {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 28px;
        transition: all .25s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .service-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--blue);
        transform: scaleX(0);
        transition: transform .25s;
        transform-origin: left;
    }
    .service-card:hover { border-color: var(--blue-lt); transform: translateY(-4px); box-shadow: var(--shadow); }
    .service-card:hover::before { transform: scaleX(1); }
    .service-icon { font-size: 36px; margin-bottom: 14px; display: block; }
    .service-cat {
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--blue);
        font-weight: 700;
        margin-bottom: 6px;
    }
    .service-name { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
    .service-desc { font-size: 13px; color: var(--muted); line-height: 1.6; margin-bottom: 16px; }
    .service-footer { display: flex; align-items: center; justify-content: space-between; }
    .service-price { font-size: 18px; font-weight: 800; color: var(--blue); }
    .service-price small { font-size: 12px; font-weight: 600; color: var(--muted); }
    .add-btn {
        width: 36px; height: 36px;
        border-radius: 50%;
        border: 2px solid var(--blue);
        background: transparent;
        color: var(--blue);
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s;
        font-weight: 300;
    }
    .add-btn:hover { background: var(--blue); color: #fff; }

    /* ── HOW IT WORKS ───────────────────────── */
    #how {
        padding: 100px 80px;
        background: var(--bg2);
    }
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        max-width: 1000px;
        margin: 0 auto;
        position: relative;
    }
    .steps-grid::before {
        content: '';
        position: absolute;
        top: 28px;
        left: 10%;
        right: 10%;
        height: 2px;
        background: var(--border);
        z-index: 0;
    }
    .step {
        text-align: center;
        position: relative;
        z-index: 1;
    }
    .step-num {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: var(--white);
        border: 2px solid var(--blue);
        color: var(--blue);
        font-weight: 800;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 4px 12px rgba(46,134,222,.15);
    }
    .step-icon { font-size: 28px; margin-bottom: 10px; display: block; }
    .step-title { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
    .step-desc { font-size: 12px; color: var(--muted); line-height: 1.5; }

    /* ── ORDER FORM ─────────────────────────── */
    #order {
        padding: 100px 80px;
        background: var(--white);
    }
    .order-wrap {
        max-width: 760px;
        margin: 0 auto;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 48px;
        box-shadow: var(--shadow);
    }
    .order-steps {
        display: flex;
        gap: 0;
        margin-bottom: 36px;
        background: var(--bg2);
        border-radius: 100px;
        padding: 4px;
    }
    .order-step-btn {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 700;
        color: var(--muted);
        transition: all .2s;
        cursor: default;
    }
    .order-step-btn.active { background: var(--white); color: var(--blue); box-shadow: 0 2px 8px rgba(46,134,222,.15); }
    .order-step-btn.done { color: var(--success); }

    /* Form panels */
    .order-panel { display: none; }
    .order-panel.active { display: block; animation: fadeUp .3s ease; }

    .form-field { margin-bottom: 18px; }
    .form-field label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 7px;
    }
    .form-field input,
    .form-field select,
    .form-field textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-family: var(--font-b);
        font-size: 14px;
        color: var(--text);
        background: var(--white);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-field input:focus,
    .form-field select:focus,
    .form-field textarea:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(46,134,222,.12);
    }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-field textarea { resize: none; height: 80px; }

    /* Selected services summary */
    .selected-list { margin-bottom: 20px; }
    .selected-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: var(--white);
        border: 1.5px solid var(--border);
        border-radius: 12px;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .selected-item-left { display: flex; align-items: center; gap: 10px; }
    .selected-item-name { font-weight: 700; color: var(--text); }
    .selected-item-price { font-size: 13px; color: var(--muted); }
    .qty-ctrl { display: flex; align-items: center; gap: 8px; }
    .qty-btn {
        width: 28px; height: 28px;
        border-radius: 50%;
        border: 1.5px solid var(--border);
        background: var(--bg);
        color: var(--text);
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .15s;
        line-height: 1;
    }
    .qty-btn:hover { border-color: var(--blue); color: var(--blue); background: var(--sky); }
    .qty-num { font-size: 15px; font-weight: 700; min-width: 20px; text-align: center; }

    /* Order total bar */
    .order-total-bar {
        background: var(--sky);
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .order-total-label { font-size: 13px; color: var(--blue); font-weight: 700; }
    .order-total-value { font-family: var(--font-d); font-size: 22px; color: var(--blue-dk); font-weight: 600; }

    .btn-order {
        width: 100%;
        padding: 15px;
        background: var(--blue);
        color: #fff;
        border: none;
        border-radius: 100px;
        font-family: var(--font-b);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        letter-spacing: .03em;
    }
    .btn-order:hover { background: var(--blue-dk); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(46,134,222,.35); }
    .btn-order:disabled { opacity: .45; cursor: not-allowed; transform: none; box-shadow: none; }
    .btn-back-step {
        width: 100%;
        padding: 12px;
        background: transparent;
        border: 1.5px solid var(--border);
        border-radius: 100px;
        color: var(--muted);
        font-family: var(--font-b);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 10px;
        transition: all .2s;
    }
    .btn-back-step:hover { border-color: var(--blue); color: var(--blue); }

    /* ── SUCCESS ────────────────────────────── */
    .order-success { display: none; text-align: center; padding: 20px 0; }
    .order-success.show { display: block; animation: fadeUp .4s ease; }
    .success-circle {
        width: 80px; height: 80px;
        background: #d1fae5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 20px;
    }
    .success-title { font-family: var(--font-d); font-size: 26px; color: var(--text); margin-bottom: 8px; }
    .success-sub { font-size: 14px; color: var(--muted); line-height: 1.7; margin-bottom: 28px; }
    .btn-wa {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #25D366;
        color: #fff;
        padding: 14px 28px;
        border-radius: 100px;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s;
        margin-bottom: 12px;
        font-family: var(--font-b);
    }
    .btn-wa:hover { background: #1fba58; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(37,211,102,.3); }
    .btn-again {
        display: block;
        width: 100%;
        padding: 12px;
        background: transparent;
        border: 1.5px solid var(--border);
        border-radius: 100px;
        color: var(--muted);
        font-family: var(--font-b);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-again:hover { border-color: var(--blue); color: var(--blue); }

    /* ── EMPTY STATE ────────────────────────── */
    .empty-services {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
    }
    .empty-services-icon { font-size: 48px; margin-bottom: 12px; }

    /* ── FOOTER ─────────────────────────────── */
    footer {
        background: var(--text);
        color: #fff;
        padding: 60px 80px 40px;
        text-align: center;
    }
    .footer-logo {
        font-family: var(--font-d);
        font-size: 26px;
        color: #fff;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .footer-tagline { font-size: 14px; color: #8ba4c0; margin-bottom: 28px; }
    .footer-info { font-size: 13px; color: #8ba4c0; line-height: 2.2; }
    .footer-copy { font-size: 12px; color: #3d5a7a; margin-top: 36px; }

    /* ── TOAST ──────────────────────────────── */
    .toast {
        position: fixed;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: var(--text);
        color: #fff;
        padding: 12px 24px;
        border-radius: 100px;
        font-size: 14px;
        font-weight: 600;
        z-index: 500;
        transition: transform .3s;
        white-space: nowrap;
        box-shadow: 0 8px 32px rgba(30,58,95,.25);
    }
    .toast.show { transform: translateX(-50%) translateY(0); }
    .toast.blue { background: var(--blue); }

    /* ── ANIMATIONS ─────────────────────────── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeLeft {
        from { opacity: 0; transform: translateX(30px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* ── RESPONSIVE ─────────────────────────── */
    @media (max-width: 900px) {
        #hero { grid-template-columns: 1fr; padding: 100px 24px 60px; gap: 40px; }
        .hero-visual { display: none; }
        #services, #how, #order { padding: 60px 24px; }
        nav { padding: 16px 24px; }
        .nav-links { display: none; }
        footer { padding: 48px 24px 32px; }
        .steps-grid { grid-template-columns: repeat(2, 1fr); }
        .steps-grid::before { display: none; }
        .order-wrap { padding: 28px 20px; }
        .form-row { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>
<?php require ROOT_PATH . '/app/Views/demo/_banner.php'; ?>


<!-- ── NAVBAR ──────────────────────────────────── -->
<nav id="navbar">
    <div class="nav-logo">
        <div class="nav-logo-icon">🧺</div>
        <?= htmlspecialchars($store['name'] ?? '') ?>
    </div>
    <div class="nav-links">
        <a href="#services">Layanan</a>
        <a href="#how">Cara Kerja</a>
        <a href="#order">Pesan Sekarang</a>
    </div>
    <button class="nav-order-btn" onclick="scrollToOrder()">Pesan Sekarang →</button>
</nav>

<!-- ── HERO ────────────────────────────────────── -->
<section id="hero">
    <div class="hero-bubble hero-bubble-1"></div>
    <div class="hero-bubble hero-bubble-2"></div>
    <div class="hero-bubble hero-bubble-3"></div>

    <div>
        <div class="hero-eyebrow">✨ Laundry Terpercaya</div>
        <h1 class="hero-title">
            Bersih, <em>Wangi,</em><br>& Tepat Waktu
        </h1>
        <p class="hero-sub">
            Serahkan cucian kotormu kepada kami. Kami jaga kebersihan dan kerapian setiap helai pakaianmu dengan standar profesional.
        </p>
        <div class="hero-actions">
            <a href="#order" class="btn-primary">🧺 Pesan Sekarang</a>
            <a href="#services" class="btn-secondary">Lihat Layanan →</a>
        </div>
        <div class="hero-trust">
            <div class="trust-item"><span class="trust-icon">⚡</span> Proses Cepat</div>
            <div class="trust-item"><span class="trust-icon">🌿</span> Ramah Lingkungan</div>
            <div class="trust-item"><span class="trust-icon">🚚</span> Antar Jemput</div>
        </div>
    </div>

    <div class="hero-visual">
        <div class="hero-card-main">
            <span class="washing-anim">🫧</span>
            <div class="hero-card-label">Sedang diproses</div>
            <div class="hero-card-title"><?= htmlspecialchars($store['name'] ?? '') ?></div>
        </div>
        <div class="float-badge float-badge-1">
            <div class="dot"></div> Tersedia sekarang
        </div>
        <div class="float-badge float-badge-2">
            🧽 Bersih & Wangi
        </div>
    </div>
</section>

<!-- ── SERVICES ─────────────────────────────────── -->
<section id="services">
    <div class="section-header">
        <div class="section-tag">Layanan Kami</div>
        <h2 class="section-title">Pilihan Paket Laundry</h2>
        <p class="section-sub">Pilih layanan yang sesuai kebutuhanmu, lalu langsung order!</p>
    </div>

    <?php if (empty($menu)): ?>
    <div class="empty-services">
        <div class="empty-services-icon">🧺</div>
        <p>Layanan belum tersedia. Hubungi kami untuk info lebih lanjut.</p>
    </div>
    <?php else: ?>

    <!-- Category tabs jika lebih dari 1 -->
    <?php if (count($menu) > 1): ?>
    <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-bottom:36px;">
        <?php $first = true; foreach ($menu as $catId => $cat): ?>
        <button class="nav-order-btn" style="background:<?= $first ? 'var(--blue)' : 'var(--white)' ?>; color:<?= $first ? '#fff' : 'var(--blue)' ?>; border:1.5px solid var(--blue);"
            onclick="switchCat(this, 'cat-<?= $catId ?>')">
            <?= htmlspecialchars($cat['icon'] ?? '') ?> <?= htmlspecialchars($cat['name'] ?? '') ?>
        </button>
        <?php $first = false; endforeach; ?>
    </div>
    <?php endif; ?>

    <?php $first = true; foreach ($menu as $catId => $cat): ?>
    <div class="services-grid cat-section <?= $first ? '' : 'hidden-cat' ?>" id="cat-<?= $catId ?>">
        <?php foreach ($cat['products'] as $p): ?>
        <div class="service-card" onclick="addToOrder(<?= $p['id'] ?>, '<?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES) ?>', <?= $p['price'] ?>, '<?= htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES) ?>')">
            <span class="service-icon"><?= htmlspecialchars($cat['icon'] ?? '') ?></span>
            <div class="service-cat"><?= htmlspecialchars($cat['name'] ?? '') ?></div>
            <div class="service-name"><?= htmlspecialchars($p['name'] ?? '') ?></div>
            <?php if (!empty($p['description'])): ?>
            <div class="service-desc"><?= htmlspecialchars($p['description'] ?? '') ?></div>
            <?php endif; ?>
            <div class="service-footer">
                <div class="service-price">
                    Rp <?= number_format($p['price'], 0, ',', '.') ?>
                    <small>/kg</small>
                </div>
                <button class="add-btn" onclick="event.stopPropagation(); addToOrder(<?= $p['id'] ?>, '<?= htmlspecialchars($p['name'] ?? '', ENT_QUOTES) ?>', <?= $p['price'] ?>, '<?= htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES) ?>')">+</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php $first = false; endforeach; ?>

    <?php endif; ?>
</section>

<!-- ── HOW IT WORKS ──────────────────────────────── -->
<section id="how">
    <div class="section-header">
        <div class="section-tag">Cara Kerja</div>
        <h2 class="section-title">Mudah dalam 4 Langkah</h2>
    </div>
    <div class="steps-grid">
        <div class="step">
            <div class="step-num">1</div>
            <span class="step-icon">📋</span>
            <div class="step-title">Pesan Online</div>
            <div class="step-desc">Isi form order dengan data lengkap dan pilih layanan</div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <span class="step-icon">🚗</span>
            <div class="step-title">Antar Jemput</div>
            <div class="step-desc">Kami jemput cucianmu di alamat yang kamu tentukan</div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <span class="step-icon">🧼</span>
            <div class="step-title">Proses Cuci</div>
            <div class="step-desc">Dicuci bersih dengan deterjen premium & pengering</div>
        </div>
        <div class="step">
            <div class="step-num">4</div>
            <span class="step-icon">✅</span>
            <div class="step-title">Terima Bersih</div>
            <div class="step-desc">Pakaian bersih, wangi, dan rapi diantarkan kembali</div>
        </div>
    </div>
</section>

<!-- ── ORDER FORM ───────────────────────────────── -->
<section id="order">
    <div class="section-header">
        <div class="section-tag">Pesan Sekarang</div>
        <h2 class="section-title">Buat Pesanan Laundry</h2>
        <p class="section-sub">Isi form di bawah, kami akan segera menghubungimu</p>
    </div>

    <div class="order-wrap">

        <!-- Step indicator -->
        <div class="order-steps">
            <div class="order-step-btn active" id="step-ind-1">1 · Pilih Layanan</div>
            <div class="order-step-btn" id="step-ind-2">2 · Data Kamu</div>
            <div class="order-step-btn" id="step-ind-3">3 · Konfirmasi</div>
        </div>

        <!-- PANEL 1: Pilih layanan -->
        <div class="order-panel active" id="panel-1">
            <p style="font-size:13px; color:var(--muted); margin-bottom:16px">
                Pilih layanan yang ingin kamu pesan. Kamu bisa pilih lebih dari satu.
            </p>
            <div class="selected-list" id="selectedList">
                <div id="emptyCart" style="text-align:center; padding:32px; color:var(--muted); font-size:14px; background:var(--bg2); border-radius:12px; border:1.5px dashed var(--border)">
                    🧺 Belum ada layanan dipilih.<br>
                    <small>Klik kartu layanan di atas untuk menambahkan</small>
                </div>
            </div>
            <div class="order-total-bar" id="totalBar" style="display:none">
                <span class="order-total-label">Estimasi Total</span>
                <span class="order-total-value" id="totalVal">Rp 0</span>
            </div>
            <button class="btn-order" onclick="goStep(2)" id="btnStep1" disabled>
                Lanjut Isi Data →
            </button>
        </div>

        <!-- PANEL 2: Data pelanggan -->
        <div class="order-panel" id="panel-2">
            <div class="form-row">
                <div class="form-field">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="custName" placeholder="Nama kamu" required>
                </div>
                <div class="form-field">
                    <label>Nomor WhatsApp *</label>
                    <input type="tel" id="custPhone" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>
            <div class="form-field">
                <label>Alamat Penjemputan *</label>
                <textarea id="custAddress" placeholder="Jl. Contoh No. 10, RT/RW, Kelurahan..."></textarea>
            </div>
            <div class="form-row">
                <div class="form-field">
                    <label>Estimasi Berat (kg)</label>
                    <input type="number" id="custWeight" placeholder="Contoh: 3" min="1" step="0.5">
                </div>
                <div class="form-field">
                    <label>Waktu Penjemputan</label>
                    <input type="datetime-local" id="custPickup">
                </div>
            </div>
            <div class="form-field">
                <label>Catatan Tambahan</label>
                <textarea id="custNote" placeholder="Contoh: pisahkan warna gelap, jangan diperas..."></textarea>
            </div>
            <button class="btn-order" onclick="goStep(3)">Lihat Ringkasan →</button>
            <button class="btn-back-step" onclick="goStep(1)">← Kembali</button>
        </div>

        <!-- PANEL 3: Ringkasan & konfirmasi -->
        <div class="order-panel" id="panel-3">
            <div style="background:var(--bg2); border-radius:14px; padding:20px; margin-bottom:20px;">
                <div style="font-size:12px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:14px;">Ringkasan Pesanan</div>
                <div id="summaryItems" style="font-size:14px; color:var(--text); line-height:2;"></div>
                <div style="border-top:1.5px solid var(--border); margin-top:12px; padding-top:12px; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:13px; color:var(--muted); font-weight:700;">Estimasi Total</span>
                    <span style="font-family:var(--font-d); font-size:22px; color:var(--blue);" id="summaryTotal">Rp 0</span>
                </div>
            </div>
            <div style="background:var(--bg2); border-radius:14px; padding:20px; margin-bottom:24px;">
                <div style="font-size:12px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--muted); margin-bottom:14px;">Data Penjemputan</div>
                <div id="summaryCustomer" style="font-size:14px; color:var(--text); line-height:2;"></div>
            </div>
            <button class="btn-order" onclick="submitOrder()" id="submitBtn">
                ✓ Konfirmasi Pesanan
            </button>
            <button class="btn-back-step" onclick="goStep(2)">← Ubah Data</button>
        </div>

        <!-- SUCCESS -->
        <div class="order-success" id="successView">
            <div class="success-circle">✅</div>
            <div class="success-title">Pesanan Diterima!</div>
            <p class="success-sub">
                Pesanan laundry kamu sudah masuk.<br>
                Konfirmasi via WhatsApp agar kami segera menjemput.
            </p>
            <a href="#" id="waLink" class="btn-wa" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Konfirmasi via WhatsApp
            </a>
            <button class="btn-again" onclick="resetOrder()">Buat Pesanan Baru</button>
        </div>

    </div>
</section>

<!-- ── FOOTER ───────────────────────────────────── -->
<footer>
    <div class="footer-logo">🧺 <?= htmlspecialchars($store['name'] ?? '') ?></div>
    <p class="footer-tagline">Laundry bersih, hidup lebih mudah.</p>
    <div class="footer-info">
        <?php if (!empty($store['address'])): ?>
            📍 <?= htmlspecialchars($store['address'] ?? '') ?><br>
        <?php endif; ?>
        <?php if (!empty($store['phone'])): ?>
            📞 <?= htmlspecialchars($store['phone'] ?? '') ?>
        <?php endif; ?>
    </div>
    <p class="footer-copy">© <?= date('Y') ?> <?= htmlspecialchars($store['name'] ?? '') ?>. All rights reserved.</p>
</footer>

<div class="toast" id="toast"></div>

<style>
.hidden-cat { display: none !important; }
</style>

<script>
// ── STATE ──────────────────────────────────────────
let cart   = {};   // { id: { id, name, price, icon, qty } }
let currentStep = 1;

// ── NAVBAR ────────────────────────────────────────
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
});

function scrollToOrder() {
    document.getElementById('order').scrollIntoView({ behavior: 'smooth' });
}

// ── CATEGORY TABS ─────────────────────────────────
function switchCat(btn, id) {
    document.querySelectorAll('.cat-section').forEach(s => s.classList.add('hidden-cat'));
    document.querySelectorAll('.nav-order-btn[onclick^="switchCat"]').forEach(b => {
        b.style.background = 'var(--white)';
        b.style.color = 'var(--blue)';
    });
    document.getElementById(id)?.classList.remove('hidden-cat');
    btn.style.background = 'var(--blue)';
    btn.style.color = '#fff';
}

// ── CART ──────────────────────────────────────────
function addToOrder(id, name, price, icon) {
    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { id, name, price, icon, qty: 1 };
    }
    renderCart();
    showToast(icon + ' ' + name + ' ditambahkan!', 'blue');
    document.getElementById('order').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function renderCart() {
    const list  = document.getElementById('selectedList');
    const empty = document.getElementById('emptyCart');
    const bar   = document.getElementById('totalBar');
    const btn   = document.getElementById('btnStep1');
    const items = Object.values(cart);
    const total = items.reduce((s, i) => s + i.price * i.qty, 0);

    document.getElementById('totalVal').textContent = formatRp(total);
    btn.disabled = items.length === 0;
    bar.style.display = items.length > 0 ? 'flex' : 'none';

    if (items.length === 0) {
        if (!document.getElementById('emptyCart')) {
            list.innerHTML = `<div id="emptyCart" style="text-align:center; padding:32px; color:var(--muted); font-size:14px; background:var(--bg2); border-radius:12px; border:1.5px dashed var(--border)">
                🧺 Belum ada layanan dipilih.<br><small>Klik kartu layanan di atas untuk menambahkan</small></div>`;
        }
        return;
    }

    list.innerHTML = items.map(item => `
        <div class="selected-item">
            <div class="selected-item-left">
                <span style="font-size:22px">${item.icon}</span>
                <div>
                    <div class="selected-item-name">${item.name}</div>
                    <div class="selected-item-price">${formatRp(item.price)}/kg</div>
                </div>
            </div>
            <div class="qty-ctrl">
                <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
            </div>
        </div>
    `).join('');
}

// ── STEPS ─────────────────────────────────────────
function goStep(step) {
    // Validasi step 2
    if (step === 3) {
        const name    = document.getElementById('custName').value.trim();
        const phone   = document.getElementById('custPhone').value.trim();
        const address = document.getElementById('custAddress').value.trim();
        if (!name || !phone || !address) {
            showToast('⚠️ Nama, WhatsApp, dan alamat wajib diisi!');
            return;
        }
        // Isi ringkasan
        const items = Object.values(cart);
        document.getElementById('summaryItems').innerHTML =
            items.map(i => `<span>${i.icon} ${i.name} ×${i.qty} = ${formatRp(i.price * i.qty)}</span><br>`).join('');
        document.getElementById('summaryTotal').textContent =
            formatRp(items.reduce((s, i) => s + i.price * i.qty, 0));

        const pickup   = document.getElementById('custPickup').value;
        const weight   = document.getElementById('custWeight').value;
        document.getElementById('summaryCustomer').innerHTML = `
            👤 ${name}<br>
            📞 ${phone}<br>
            📍 ${address}
            ${weight ? '<br>⚖️ Est. ' + weight + ' kg' : ''}
            ${pickup ? '<br>🕐 ' + new Date(pickup).toLocaleString('id-ID') : ''}
        `;
    }

    // Sembunyikan semua panel
    document.querySelectorAll('.order-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + step)?.classList.add('active');

    // Update step indicator
    for (let i = 1; i <= 3; i++) {
        const ind = document.getElementById('step-ind-' + i);
        ind.classList.remove('active', 'done');
        if (i < step) ind.classList.add('done');
        else if (i === step) ind.classList.add('active');
    }
    currentStep = step;
}

// ── SUBMIT ────────────────────────────────────────
async function submitOrder() {
    const btn     = document.getElementById('submitBtn');
    const name    = document.getElementById('custName').value.trim();
    const phone   = document.getElementById('custPhone').value.trim();
    const address = document.getElementById('custAddress').value.trim();
    const weight  = document.getElementById('custWeight').value.trim();
    const pickup  = document.getElementById('custPickup').value;
    const note    = document.getElementById('custNote').value.trim();

    const items = Object.values(cart).map(i => ({ product_id: i.id, qty: i.qty }));

    btn.disabled    = true;
    btn.textContent = 'Memproses...';

    // Gabungkan info pickup ke note
    let fullNote = note;
    if (address) fullNote = 'Alamat: ' + address + (fullNote ? '\n' + fullNote : '');
    if (weight)  fullNote += '\nEst. berat: ' + weight + ' kg';
    if (pickup)  fullNote += '\nWaktu jemput: ' + new Date(pickup).toLocaleString('id-ID');

    try {
        const res = await fetch('<?= BASE_URL ?>/toko/<?= $store['slug'] ?>/order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ customer_name: name, customer_phone: phone, note: fullNote, items }),
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('panel-3').classList.remove('active');
            const sv = document.getElementById('successView');
            sv.classList.add('show');
            document.getElementById('waLink').href = data.wa_url;
            cart = {};
            renderCart();
        } else {
            showToast('❌ ' + (data.message || 'Terjadi kesalahan.'));
        }
    } catch (e) {
        showToast('❌ Koneksi gagal, coba lagi.');
    }

    btn.disabled    = false;
    btn.textContent = '✓ Konfirmasi Pesanan';
}

function resetOrder() {
    document.getElementById('successView').classList.remove('show');
    goStep(1);
    document.getElementById('custName').value    = '';
    document.getElementById('custPhone').value   = '';
    document.getElementById('custAddress').value = '';
    document.getElementById('custWeight').value  = '';
    document.getElementById('custPickup').value  = '';
    document.getElementById('custNote').value    = '';
}

// ── HELPERS ───────────────────────────────────────
function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'toast ' + type;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}
</script>

</body>
</html>
