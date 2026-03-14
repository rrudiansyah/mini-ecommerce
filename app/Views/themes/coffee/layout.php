<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($store['name'] ?? '') ?> — Premium Coffee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
    /* ── RESET & BASE ─────────────────────────────── */
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
        --bg:        #0e0c0a;
        --bg2:       #141210;
        --bg3:       #1c1916;
        --surface:   #221f1b;
        --border:    #2e2a25;
        --gold:      #c9a96e;
        --gold-light:#e2c48a;
        --cream:     #f5efe6;
        --text:      #e8dfd4;
        --muted:     #8a7d6e;
        --danger:    #e05555;
        --success:   #5a9e6f;
        --radius:    14px;
        --font-serif:'Playfair Display', serif;
        --font-sans: 'DM Sans', sans-serif;
    }

    html { scroll-behavior: smooth; }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: var(--font-sans);
        font-weight: 400;
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* ── NAVBAR ───────────────────────────────────── */
    nav {
        position: fixed;
        top: 0; left: 0; right: 0;
        z-index: 100;
        padding: 18px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background .3s, box-shadow .3s;
    }
    nav.scrolled {
        background: rgba(14,12,10,.92);
        backdrop-filter: blur(12px);
        box-shadow: 0 1px 0 var(--border);
    }
    .nav-logo {
        font-family: var(--font-serif);
        font-size: 22px;
        color: var(--gold);
        letter-spacing: .02em;
    }
    .nav-links { display: flex; gap: 32px; align-items: center; }
    .nav-links a {
        color: var(--muted);
        text-decoration: none;
        font-size: 14px;
        letter-spacing: .05em;
        text-transform: uppercase;
        transition: color .2s;
    }
    .nav-links a:hover { color: var(--cream); }
    .cart-btn {
        position: relative;
        background: var(--gold);
        color: #0e0c0a;
        border: none;
        padding: 10px 20px;
        border-radius: 100px;
        font-family: var(--font-sans);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        letter-spacing: .04em;
        transition: background .2s, transform .15s;
    }
    .cart-btn:hover { background: var(--gold-light); transform: scale(1.03); }
    .cart-count {
        position: absolute;
        top: -6px; right: -6px;
        background: var(--danger);
        color: #fff;
        border-radius: 50%;
        width: 18px; height: 18px;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        display: none;
    }

    /* ── HERO ─────────────────────────────────────── */
    #hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        padding: 120px 24px 80px;
    }
    .hero-bg {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 50% 40%, rgba(201,169,110,.08) 0%, transparent 70%),
            radial-gradient(ellipse 40% 40% at 20% 80%, rgba(201,169,110,.05) 0%, transparent 60%);
    }
    /* Coffee bean decorative dots */
    .hero-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, rgba(201,169,110,.15) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: radial-gradient(ellipse 70% 70% at 50% 50%, black, transparent);
    }
    .hero-content { position: relative; z-index: 1; max-width: 680px; }
    .hero-eyebrow {
        font-family: var(--font-sans);
        font-size: 12px;
        letter-spacing: .25em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 20px;
        opacity: 0;
        animation: fadeUp .8s .2s ease forwards;
    }
    .hero-title {
        font-family: var(--font-serif);
        font-size: clamp(3rem, 8vw, 5.5rem);
        line-height: 1.05;
        color: var(--cream);
        margin-bottom: 24px;
        opacity: 0;
        animation: fadeUp .8s .35s ease forwards;
    }
    .hero-title em { color: var(--gold); font-style: italic; }
    .hero-subtitle {
        font-size: 16px;
        color: var(--muted);
        max-width: 420px;
        margin: 0 auto 40px;
        opacity: 0;
        animation: fadeUp .8s .5s ease forwards;
    }
    .hero-cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--gold);
        color: #0e0c0a;
        padding: 15px 34px;
        border-radius: 100px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        letter-spacing: .03em;
        transition: all .2s;
        opacity: 0;
        animation: fadeUp .8s .65s ease forwards;
    }
    .hero-cta:hover { background: var(--gold-light); transform: translateY(-2px); box-shadow: 0 12px 30px rgba(201,169,110,.3); }
    .hero-scroll {
        position: absolute;
        bottom: 36px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: var(--muted);
        font-size: 11px;
        letter-spacing: .15em;
        text-transform: uppercase;
        opacity: 0;
        animation: fadeUp .8s 1s ease forwards;
    }
    .scroll-line {
        width: 1px;
        height: 40px;
        background: linear-gradient(to bottom, var(--gold), transparent);
        animation: scrollPulse 2s ease-in-out infinite;
    }

    /* ── ABOUT ────────────────────────────────────── */
    #about {
        padding: 100px 40px;
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: center;
    }
    .about-label {
        font-size: 11px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 16px;
    }
    .about-title {
        font-family: var(--font-serif);
        font-size: clamp(2rem, 4vw, 2.8rem);
        color: var(--cream);
        line-height: 1.2;
        margin-bottom: 20px;
    }
    .about-text { color: var(--muted); font-size: 15px; line-height: 1.8; margin-bottom: 16px; }
    .about-stats {
        display: flex;
        gap: 40px;
        margin-top: 36px;
        padding-top: 36px;
        border-top: 1px solid var(--border);
    }
    .stat-item { text-align: center; }
    .stat-num {
        font-family: var(--font-serif);
        font-size: 2.2rem;
        color: var(--gold);
        line-height: 1;
    }
    .stat-lbl { font-size: 12px; color: var(--muted); letter-spacing: .1em; text-transform: uppercase; margin-top: 4px; }
    .about-visual {
        position: relative;
        aspect-ratio: 4/5;
        background: var(--surface);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .about-visual-inner {
        font-size: 100px;
        opacity: .15;
    }
    .about-badge {
        position: absolute;
        bottom: 24px;
        right: 24px;
        background: var(--gold);
        color: #0e0c0a;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 13px;
        text-align: center;
    }
    .about-badge strong { display: block; font-family: var(--font-serif); font-size: 22px; }

    /* ── MENU ─────────────────────────────────────── */
    #menu {
        padding: 100px 40px;
        background: var(--bg2);
    }
    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }
    .section-label {
        font-size: 11px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 14px;
    }
    .section-title {
        font-family: var(--font-serif);
        font-size: clamp(2rem, 4vw, 3rem);
        color: var(--cream);
    }
    /* Category tabs */
    .menu-tabs {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 48px;
    }
    .menu-tab {
        padding: 9px 22px;
        border-radius: 100px;
        border: 1px solid var(--border);
        background: transparent;
        color: var(--muted);
        font-family: var(--font-sans);
        font-size: 13px;
        cursor: pointer;
        transition: all .2s;
        letter-spacing: .03em;
    }
    .menu-tab:hover { border-color: var(--gold); color: var(--gold); }
    .menu-tab.active { background: var(--gold); color: #0e0c0a; border-color: var(--gold); font-weight: 500; }

    /* Product grid */
    .menu-section { display: none; max-width: 1100px; margin: 0 auto; }
    .menu-section.active { display: block; }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }
    .product-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform .25s, box-shadow .25s, border-color .25s;
        cursor: pointer;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,.4);
        border-color: rgba(201,169,110,.3);
    }
    .product-img {
        aspect-ratio: 4/3;
        background: var(--bg3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 56px;
        overflow: hidden;
        position: relative;
    }
    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s;
    }
    .product-card:hover .product-img img { transform: scale(1.06); }
    .product-body { padding: 18px 20px 20px; }
    .product-name {
        font-family: var(--font-serif);
        font-size: 17px;
        color: var(--cream);
        margin-bottom: 6px;
    }
    .product-desc { font-size: 13px; color: var(--muted); margin-bottom: 16px; line-height: 1.5; }
    .product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .product-price {
        font-size: 16px;
        font-weight: 500;
        color: var(--gold);
    }
    .add-btn {
        width: 36px; height: 36px;
        border-radius: 50%;
        border: 1px solid var(--gold);
        background: transparent;
        color: var(--gold);
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s;
        font-weight: 300;
        line-height: 1;
    }
    .add-btn:hover { background: var(--gold); color: #0e0c0a; }

    /* ── CART SIDEBAR ─────────────────────────────── */
    .cart-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.6);
        z-index: 200;
        opacity: 0;
        pointer-events: none;
        transition: opacity .3s;
    }
    .cart-overlay.open { opacity: 1; pointer-events: all; }
    .cart-sidebar {
        position: fixed;
        top: 0; right: 0;
        width: min(420px, 100vw);
        height: 100vh;
        background: var(--bg2);
        border-left: 1px solid var(--border);
        z-index: 201;
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform .35s cubic-bezier(.4,0,.2,1);
    }
    .cart-overlay.open .cart-sidebar { transform: translateX(0); }
    .cart-head {
        padding: 24px 24px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cart-head h2 { font-family: var(--font-serif); font-size: 20px; color: var(--cream); }
    .cart-close {
        background: var(--surface);
        border: none;
        color: var(--muted);
        width: 34px; height: 34px;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s;
    }
    .cart-close:hover { background: var(--border); color: var(--cream); }
    .cart-items { flex: 1; overflow-y: auto; padding: 16px 24px; }
    .cart-item {
        display: flex;
        gap: 14px;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }
    .cart-item-icon {
        width: 52px; height: 52px;
        background: var(--surface);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        overflow: hidden;
    }
    .cart-item-icon img { width: 100%; height: 100%; object-fit: cover; }
    .cart-item-info { flex: 1; min-width: 0; }
    .cart-item-name { font-size: 14px; font-weight: 500; color: var(--cream); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cart-item-price { font-size: 13px; color: var(--gold); margin-top: 2px; }
    .qty-ctrl { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
    .qty-btn {
        width: 28px; height: 28px;
        border-radius: 50%;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text);
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .15s;
        line-height: 1;
    }
    .qty-btn:hover { border-color: var(--gold); color: var(--gold); }
    .qty-num { font-size: 15px; font-weight: 500; min-width: 18px; text-align: center; }
    .cart-empty {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
    }
    .cart-empty-icon { font-size: 48px; margin-bottom: 12px; }
    .cart-footer {
        padding: 20px 24px 28px;
        border-top: 1px solid var(--border);
    }
    .cart-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .cart-total-label { font-size: 14px; color: var(--muted); }
    .cart-total-value { font-family: var(--font-serif); font-size: 22px; color: var(--gold); }

    /* ── ORDER FORM ───────────────────────────────── */
    .order-form { display: none; }
    .order-form.show { display: block; }
    .form-field { margin-bottom: 14px; }
    .form-field label { display: block; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
    .form-field input,
    .form-field textarea {
        width: 100%;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 11px 14px;
        color: var(--text);
        font-family: var(--font-sans);
        font-size: 14px;
        outline: none;
        transition: border-color .2s;
    }
    .form-field input:focus,
    .form-field textarea:focus { border-color: var(--gold); }
    .form-field textarea { resize: none; height: 72px; }

    .btn-order {
        width: 100%;
        padding: 14px;
        background: var(--gold);
        color: #0e0c0a;
        border: none;
        border-radius: 100px;
        font-family: var(--font-sans);
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        letter-spacing: .03em;
        transition: all .2s;
        margin-top: 4px;
    }
    .btn-order:hover { background: var(--gold-light); }
    .btn-order:disabled { opacity: .5; cursor: not-allowed; }
    .btn-back {
        width: 100%;
        padding: 10px;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: 100px;
        color: var(--muted);
        font-size: 14px;
        cursor: pointer;
        margin-top: 8px;
        transition: all .2s;
    }
    .btn-back:hover { border-color: var(--gold); color: var(--gold); }
    .btn-checkout {
        width: 100%;
        padding: 14px;
        background: var(--gold);
        color: #0e0c0a;
        border: none;
        border-radius: 100px;
        font-family: var(--font-sans);
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-checkout:hover { background: var(--gold-light); }

    /* ── SUCCESS STATE ────────────────────────────── */
    .order-success { display: none; text-align: center; padding: 20px 0; }
    .order-success.show { display: block; }
    .success-icon { font-size: 52px; margin-bottom: 12px; }
    .success-title { font-family: var(--font-serif); font-size: 22px; color: var(--cream); margin-bottom: 8px; }
    .success-sub { font-size: 14px; color: var(--muted); margin-bottom: 24px; line-height: 1.6; }
    .btn-wa {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #25D366;
        color: #fff;
        padding: 13px 28px;
        border-radius: 100px;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        transition: all .2s;
        margin-bottom: 12px;
    }
    .btn-wa:hover { background: #20bd5a; transform: scale(1.02); }
    .btn-order-again {
        display: block;
        width: 100%;
        padding: 11px;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: 100px;
        color: var(--muted);
        font-size: 14px;
        cursor: pointer;
        transition: all .2s;
        font-family: var(--font-sans);
    }
    .btn-order-again:hover { border-color: var(--gold); color: var(--gold); }

    /* ── FOOTER ───────────────────────────────────── */
    footer {
        background: var(--bg2);
        border-top: 1px solid var(--border);
        padding: 60px 40px 40px;
        text-align: center;
    }
    .footer-logo {
        font-family: var(--font-serif);
        font-size: 28px;
        color: var(--gold);
        margin-bottom: 12px;
    }
    .footer-tagline { font-size: 14px; color: var(--muted); margin-bottom: 32px; }
    .footer-info { font-size: 13px; color: var(--muted); line-height: 2; }
    .footer-copy { font-size: 12px; color: #3d3730; margin-top: 40px; }

    /* ── TOAST ────────────────────────────────────── */
    .toast {
        position: fixed;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%) translateY(80px);
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 12px 24px;
        border-radius: 100px;
        font-size: 14px;
        z-index: 500;
        transition: transform .3s ease;
        white-space: nowrap;
        box-shadow: 0 8px 32px rgba(0,0,0,.4);
    }
    .toast.show { transform: translateX(-50%) translateY(0); }
    .toast.gold { border-color: var(--gold); color: var(--gold); }

    /* ── ANIMATIONS ───────────────────────────────── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes scrollPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .3; }
    }

    /* ── RESPONSIVE ───────────────────────────────── */
    @media (max-width: 768px) {
        nav { padding: 16px 20px; }
        .nav-links { display: none; }
        #about { grid-template-columns: 1fr; gap: 40px; padding: 60px 24px; }
        .about-visual { display: none; }
        #menu { padding: 60px 20px; }
        footer { padding: 40px 20px 30px; }
    }
    </style>
</head>
<body>
<?php require ROOT_PATH . '/app/Views/demo/_banner.php'; ?>


<!-- ── NAVBAR ──────────────────────────────────────── -->
<nav id="navbar">
    <div class="nav-logo">☕ <?= htmlspecialchars($store['name'] ?? '') ?></div>
    <div class="nav-links">
        <a href="#about">Tentang</a>
        <a href="#menu">Menu</a>
        <a href="#footer">Kontak</a>
    </div>
    <button class="cart-btn" onclick="toggleCart()">
        🛒 Keranjang
        <span class="cart-count" id="cartCount">0</span>
    </button>
</nav>

<!-- ── HERO ────────────────────────────────────────── -->
<section id="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <p class="hero-eyebrow">Established · Premium Coffee</p>
        <h1 class="hero-title">
            Setiap Tegukan<br>adalah <em>Pengalaman</em>
        </h1>
        <p class="hero-subtitle">
            Biji kopi pilihan, diracik dengan penuh perhatian untuk menghadirkan cita rasa terbaik di setiap cangkir.
        </p>
        <a href="#menu" class="hero-cta">
            Lihat Menu ↓
        </a>
    </div>
    <div class="hero-scroll">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>

<!-- ── ABOUT ───────────────────────────────────────── -->
<section id="about">
    <div>
        <p class="about-label">Tentang Kami</p>
        <h2 class="about-title">Kopi Bukan Sekadar Minuman</h2>
        <p class="about-text">
            Kami percaya bahwa secangkir kopi yang baik dimulai dari biji yang baik. Setiap produk kami dipilih dengan cermat dari perkebunan terbaik di Nusantara.
        </p>
        <p class="about-text">
            Dari proses roasting hingga penyajian, kami menjaga setiap detail agar kamu mendapatkan pengalaman kopi yang tak terlupakan.
        </p>
        <div class="about-stats">
            <div class="stat-item">
                <div class="stat-num"><?= count($products) ?>+</div>
                <div class="stat-lbl">Menu</div>
            </div>
            <div class="stat-item">
                <div class="stat-num"><?= count($categories) ?></div>
                <div class="stat-lbl">Kategori</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">100%</div>
                <div class="stat-lbl">Fresh</div>
            </div>
        </div>
    </div>
    <div class="about-visual">
        <div class="about-visual-inner">☕</div>
        <div class="about-badge">
            <strong>Premium</strong>
            Since 2024
        </div>
    </div>
</section>

<!-- ── MENU ────────────────────────────────────────── -->
<section id="menu">
    <div class="section-header">
        <p class="section-label">Pilihan Kami</p>
        <h2 class="section-title">Menu Unggulan</h2>
    </div>

    <?php if (empty($menu)): ?>
        <p style="text-align:center; color:var(--muted); padding: 40px">Menu belum tersedia.</p>
    <?php else: ?>

    <!-- Category tabs -->
    <div class="menu-tabs">
        <?php $first = true; foreach ($menu as $catId => $cat): ?>
        <button class="menu-tab <?= $first ? 'active' : '' ?>"
                onclick="switchTab(this, 'cat-<?= $catId ?>')">
            <?= htmlspecialchars($cat['icon'] ?? '') ?> <?= htmlspecialchars($cat['name'] ?? '') ?>
        </button>
        <?php $first = false; endforeach; ?>
    </div>

    <!-- Product sections per category -->
    <?php $first = true; foreach ($menu as $catId => $cat): ?>
    <div class="menu-section <?= $first ? 'active' : '' ?>" id="cat-<?= $catId ?>">
        <div class="product-grid">
            <?php foreach ($cat['products'] as $p): ?>
            <div class="product-card" <?php if(!empty($p['has_variants'])): ?>onclick="openVariantModal(<?=$p['id']?>,'<?=htmlspecialchars($p['name']??'',ENT_QUOTES)?>',<?=$p['price']?>,'<?=htmlspecialchars($p['image']??'',ENT_QUOTES)?>',<?=htmlspecialchars(json_encode(array_values($p['variants']??[])),ENT_QUOTES)?>)"<?php else: ?>onclick="addToCart(<?=$p['id']?>,'<?=htmlspecialchars($p['name']??'',ENT_QUOTES)?>',<?=$p['price']?>,'<?=htmlspecialchars($p['image']??'',ENT_QUOTES)?>')"<?php endif; ?>>
                <div class="product-img">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['image'] ?? '') ?>"
                             alt="<?= htmlspecialchars($p['name'] ?? '') ?>"
                             onerror="this.parentElement.innerHTML='☕'">
                    <?php else: ?>
                        ☕
                    <?php endif; ?>
                </div>
                <div class="product-body">
                    <div class="product-name"><?= htmlspecialchars($p['name'] ?? '') ?></div>
                    <?php if (!empty($p['description'])): ?>
                    <div class="product-desc"><?= htmlspecialchars($p['description'] ?? '') ?></div>
                    <?php endif; ?>
                    <div class="product-footer">
                        <div class="product-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <button class="add-btn" <?php if(!empty($p['has_variants'])): ?>onclick="event.stopPropagation();openVariantModal(<?=$p['id']?>,'<?=htmlspecialchars($p['name']??'',ENT_QUOTES)?>',<?=$p['price']?>,'<?=htmlspecialchars($p['image']??'',ENT_QUOTES)?>',<?=htmlspecialchars(json_encode(array_values($p['variants']??[])),ENT_QUOTES)?>)"<?php else: ?>onclick="event.stopPropagation();addToCart(<?=$p['id']?>,'<?=htmlspecialchars($p['name']??'',ENT_QUOTES)?>',<?=$p['price']?>,'<?=htmlspecialchars($p['image']??'',ENT_QUOTES)?>')"<?php endif; ?>>+</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php $first = false; endforeach; ?>

    <?php endif; ?>
</section>

<!-- ── FOOTER ──────────────────────────────────────── -->
<footer id="footer">
    <div class="footer-logo">☕ <?= htmlspecialchars($store['name'] ?? '') ?></div>
    <p class="footer-tagline">Premium Coffee Experience</p>
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

<!-- ── CART SIDEBAR ────────────────────────────────── -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCartOnOverlay(event)">
    <div class="cart-sidebar">
        <div class="cart-head">
            <h2>Pesanan Kamu</h2>
            <button class="cart-close" onclick="toggleCart()">✕</button>
        </div>

        <!-- STATE 1: Cart items -->
        <div id="cartView">
            <div class="cart-items" id="cartItems">
                <div class="cart-empty">
                    <div class="cart-empty-icon">🛒</div>
                    <p>Keranjang masih kosong</p>
                </div>
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span class="cart-total-label">Total</span>
                    <span class="cart-total-value" id="cartTotal">Rp 0</span>
                </div>
                <button class="btn-checkout" onclick="showOrderForm()" id="checkoutBtn" disabled>
                    Lanjut Pesan →
                </button>
            </div>
        </div>

        <!-- STATE 2: Order form -->
        <div id="orderFormView" class="order-form">
            <div class="cart-items">
                <p style="font-size:13px; color:var(--muted); margin-bottom:20px">
                    Lengkapi data untuk melanjutkan pesanan
                </p>
                <div class="form-field">
                    <label>Nama Kamu *</label>
                    <input type="text" id="customerName" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="form-field">
                    <label>Nomor WhatsApp</label>
                    <input type="tel" id="customerPhone" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-field">
                    <label>Catatan</label>
                    <textarea id="orderNote" placeholder="Contoh: less sugar, extra shot..."></textarea>
                </div>
                <!-- Order summary -->
                <div style="margin-top:8px; padding:14px; background:var(--surface); border-radius:10px; border:1px solid var(--border);">
                    <div style="font-size:12px; color:var(--muted); letter-spacing:.08em; text-transform:uppercase; margin-bottom:10px;">Ringkasan</div>
                    <div id="orderSummary" style="font-size:13px; color:var(--text); line-height:1.8;"></div>
                    <div style="border-top:1px solid var(--border); margin-top:10px; padding-top:10px; display:flex; justify-content:space-between;">
                        <span style="font-size:13px; color:var(--muted)">Total</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-size:18px;" id="formTotal">Rp 0</span>
                    </div>
                </div>
            </div>
            <div class="cart-footer">
                <button class="btn-order" onclick="submitOrder()" id="submitBtn">
                    Konfirmasi Pesanan ✓
                </button>
                <button class="btn-back" onclick="showCartView()">← Kembali ke Keranjang</button>
            </div>
        </div>

        <!-- STATE 3: Success -->
        <div id="successView" class="order-success">
            <div class="cart-items" style="display:flex; align-items:center; justify-content:center;">
                <div>
                    <div class="success-icon">🎉</div>
                    <div class="success-title">Pesanan Masuk!</div>
                    <p class="success-sub">
                        Pesanan kamu sudah kami terima.<br>
                        Klik tombol di bawah untuk konfirmasi via WhatsApp.
                    </p>
                    <div style="text-align:center;">
                        <a href="#" id="waLink" class="btn-wa" target="_blank">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Konfirmasi via WhatsApp
                        </a>
                        <button class="btn-order-again" onclick="orderAgain()">Pesan Lagi</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ── TOAST ────────────────────────────────────────── -->
<div class="toast" id="toast"></div>

<script>
// ── STATE ──────────────────────────────────────────────
let cart = {};  // { productId: { id, name, price, image, qty } }

// ── NAVBAR SCROLL ──────────────────────────────────────
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 60);
});

// ── MENU TABS ─────────────────────────────────────────
function switchTab(btn, sectionId) {
    document.querySelectorAll('.menu-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.menu-section').forEach(s => s.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(sectionId)?.classList.add('active');
}

// ── CART ──────────────────────────────────────────────
function addToCart(id, name, price, image) {
    if (cart[id]) {
        cart[id].qty++;
    } else {
        cart[id] = { id, name, price, image, qty: 1 };
    }
    renderCart();
    showToast('☕ ' + name + ' ditambahkan!', 'gold');
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const total     = getTotal();
    const count     = Object.values(cart).reduce((s, i) => s + i.qty, 0);

    // Update badge
    const badge = document.getElementById('cartCount');
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';

    // Update total
    document.getElementById('cartTotal').textContent = formatRp(total);
    document.getElementById('checkoutBtn').disabled = count === 0;

    if (count === 0) {
        container.innerHTML = `<div class="cart-empty"><div class="cart-empty-icon">🛒</div><p>Keranjang masih kosong</p></div>`;
        return;
    }

    container.innerHTML = Object.values(cart).map(item => `
        <div class="cart-item">
            <div class="cart-item-icon">
                ${item.image
                    ? `<img src="<?= BASE_URL ?>/${item.image}" onerror="this.parentElement.innerHTML='☕'">`
                    : '☕'}
            </div>
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">${formatRp(item.price)}</div>
            </div>
            <div class="qty-ctrl">
                <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
                <span class="qty-num">${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
            </div>
        </div>
    `).join('');
}

function getTotal() {
    return Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);
}

function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

// ── CART SIDEBAR ──────────────────────────────────────
function toggleCart() {
    document.getElementById('cartOverlay').classList.toggle('open');
}
function closeCartOnOverlay(e) {
    if (e.target === document.getElementById('cartOverlay')) toggleCart();
}

// ── ORDER FORM ────────────────────────────────────────
function showOrderForm() {
    document.getElementById('cartView').style.display = 'none';
    document.getElementById('orderFormView').classList.add('show');

    // Isi ringkasan
    const summary = Object.values(cart).map(i =>
        `${i.name} ×${i.qty} = ${formatRp(i.price * i.qty)}`
    ).join('<br>');
    document.getElementById('orderSummary').innerHTML = summary;
    document.getElementById('formTotal').textContent = formatRp(getTotal());
}

function showCartView() {
    document.getElementById('cartView').style.display = '';
    document.getElementById('orderFormView').classList.remove('show');
}

// ── SUBMIT ORDER ──────────────────────────────────────
async function submitOrder() {
    const name  = document.getElementById('customerName').value.trim();
    const phone = document.getElementById('customerPhone').value.trim();
    const note  = document.getElementById('orderNote').value.trim();

    if (!name) {
        showToast('⚠️ Nama wajib diisi!');
        document.getElementById('customerName').focus();
        return;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    const items = Object.values(cart).map(i => ({
        product_id:    i.id,
        qty:           i.qty,
        variant_id:    i.variant_id    || null,
        variant_label: i.variant_label || '',
    }));

    try {
        const res = await fetch('<?= BASE_URL ?>/toko/<?= $store['slug'] ?>/order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ customer_name: name, customer_phone: phone, note, items }),
        });
        const data = await res.json();

        if (data.success) {
            // Tampilkan success state
            document.getElementById('orderFormView').classList.remove('show');
            const sv = document.getElementById('successView');
            sv.classList.add('show');
            sv.style.display = '';

            // Set link WA
            document.getElementById('waLink').href = data.wa_url;

            // Reset cart
            cart = {};
            renderCart();
        } else {
            showToast('❌ ' + (data.message || 'Terjadi kesalahan.'));
        }
    } catch (err) {
        showToast('❌ Koneksi gagal, coba lagi.');
    }

    btn.disabled = false;
    btn.textContent = 'Konfirmasi Pesanan ✓';
}

function orderAgain() {
    document.getElementById('successView').classList.remove('show');
    document.getElementById('successView').style.display = 'none';
    showCartView();
    document.getElementById('customerName').value = '';
    document.getElementById('customerPhone').value = '';
    document.getElementById('orderNote').value = '';
    toggleCart();
}

// ── TOAST ─────────────────────────────────────────────
function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + type;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}
</script>

<!-- ── MODAL PILIH VARIAN ─────────────────────────────────── -->
<div id="variantModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99999;align-items:flex-end;justify-content:center">
    <div style="background:#fff;border-radius:20px 20px 0 0;padding:24px;width:100%;max-width:480px;max-height:85vh;overflow-y:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <h3 id="vmProductName" style="margin:0;font-size:18px;font-weight:700"></h3>
            <button onclick="closeVariantModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#888">✕</button>
        </div>
        <div id="vmVariantList" style="display:flex;flex-direction:column;gap:10px"></div>
        <p style="text-align:center;color:#9ca3af;font-size:12px;margin-top:12px">Pilih varian untuk melanjutkan</p>
    </div>
</div>

<script>
// ── Variant Modal ─────────────────────────────────────────
let _vmProduct = null;

function openVariantModal(productId, name, basePrice, image, variants) {
    _vmProduct = { id: productId, name, basePrice, image };
    document.getElementById('vmProductName').textContent = name;

    const list = document.getElementById('vmVariantList');
    list.innerHTML = variants.map(v => {
        const price   = v.price > 0 ? v.price : basePrice;
        const stock   = parseInt(v.stock);
        const outOfStock = stock === 0;
        const priceLabel = price !== basePrice
            ? 'Rp ' + price.toLocaleString('id-ID')
            : 'Rp ' + basePrice.toLocaleString('id-ID');
        const stockLabel = outOfStock ? '⛔ Habis' : (stock <= 5 ? '⚠️ Sisa ' + stock : '✅ Tersedia');

        return `<button onclick="${outOfStock ? '' : `addVariantToCart(${v.id},'${v.label.replace(/'/g,"\\'")}',${price})`}"
            style="display:flex;justify-content:space-between;align-items:center;
                   padding:14px 16px;border-radius:10px;border:1.5px solid ${outOfStock ? '#e5e7eb' : '#e5e7eb'};
                   background:${outOfStock ? '#f9fafb' : '#fff'};cursor:${outOfStock ? 'not-allowed' : 'pointer'};
                   opacity:${outOfStock ? '0.5' : '1'};width:100%;text-align:left;
                   transition:border-color .15s,background .15s"
            onmouseover="if(!${outOfStock})this.style.borderColor='var(--primary,#3b82f6)'"
            onmouseout="this.style.borderColor='#e5e7eb'">
            <div>
                <div style="font-weight:600;font-size:15px">${v.label}</div>
                <div style="font-size:13px;color:#6b7280;margin-top:2px">${stockLabel}</div>
            </div>
            <div style="font-weight:700;font-size:15px;color:var(--primary,#3b82f6)">${priceLabel}</div>
        </button>`;
    }).join('');

    document.getElementById('variantModal').style.display = 'flex';
}

function closeVariantModal() {
    document.getElementById('variantModal').style.display = 'none';
    _vmProduct = null;
}

function addVariantToCart(variantId, variantLabel, price) {
    if (!_vmProduct) return;
    const cartKey = _vmProduct.id + '_' + variantId;
    if (cart[cartKey]) {
        cart[cartKey].qty++;
    } else {
        cart[cartKey] = {
            id:           _vmProduct.id,
            variant_id:   variantId,
            variant_label: variantLabel,
            name:         _vmProduct.name + ' (' + variantLabel + ')',
            price:        price,
            image:        _vmProduct.image,
            qty:          1,
        };
    }
    closeVariantModal();
    renderCart();
    showToast('✅ ' + _vmProduct.name + ' (' + variantLabel + ') ditambahkan!');
}

// Tutup modal klik overlay
document.getElementById('variantModal').addEventListener('click', function(e) {
    if (e.target === this) closeVariantModal();
});
</script>

</body>
</html>
