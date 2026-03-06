<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Tema — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh}

    /* HERO */
    .hero{background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);color:#fff;padding:72px 40px 60px;text-align:center;position:relative;overflow:hidden}
    .hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 30% 50%,rgba(59,130,246,.15),transparent 60%),radial-gradient(ellipse at 70% 50%,rgba(139,92,246,.12),transparent 60%);pointer-events:none}
    .hero-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#94a3b8;padding:6px 16px;border-radius:100px;font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:20px}
    .hero h1{font-size:clamp(2rem,5vw,3.2rem);font-weight:800;line-height:1.1;margin-bottom:14px}
    .hero h1 span{background:linear-gradient(135deg,#60a5fa,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
    .hero p{font-size:16px;color:#94a3b8;max-width:520px;margin:0 auto;line-height:1.7}

    /* GRID */
    .container{max-width:1100px;margin:0 auto;padding:52px 24px}
    .section-title{font-size:13px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#64748b;margin-bottom:24px;display:flex;align-items:center;gap:8px}
    .section-title::after{content:'';flex:1;height:1px;background:#e2e8f0}

    .themes-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px}
    .theme-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;overflow:hidden;cursor:pointer;text-decoration:none;color:inherit;transition:all .25s;display:block}
    .theme-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.10);border-color:#94a3b8}

    /* Preview mockup */
    .theme-preview{height:180px;position:relative;display:flex;align-items:center;justify-content:center;font-size:64px;overflow:hidden}
    .theme-preview-coffee  {background:linear-gradient(135deg,#0e0c0a,#1c1916)}
    .theme-preview-laundry {background:linear-gradient(135deg,#dbeafe,#f7fbff)}
    .theme-preview-barbershop{background:linear-gradient(135deg,#d4e4c8,#f5f7f2)}
    .theme-preview-restaurant{background:linear-gradient(135deg,#d8f3dc,#f8faf5)}
    .theme-preview-fashion {background:linear-gradient(135deg,#d4e4c8,#f5f7f2)}
    .theme-preview-bakery  {background:linear-gradient(135deg,#d4e4c8,#f5f7f2)}

    /* Browser mockup di dalam preview */
    .browser-mock{width:200px;background:rgba(255,255,255,.9);border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,.2);overflow:hidden}
    .browser-mock-coffee{background:rgba(30,25,20,.95)}
    .browser-bar{height:24px;background:rgba(0,0,0,.1);display:flex;align-items:center;gap:4px;padding:0 8px}
    .browser-bar-coffee{background:rgba(0,0,0,.3)}
    .browser-dot{width:6px;height:6px;border-radius:50%;background:#ef4444}
    .browser-dot:nth-child(2){background:#f59e0b}
    .browser-dot:nth-child(3){background:#22c55e}
    .browser-content{padding:10px 10px 6px;font-size:8px;line-height:1.6}
    .bc-title{font-weight:700;font-size:9px;margin-bottom:4px;color:#1e293b}
    .bc-title-dark{color:#e8dfd4}
    .bc-row{display:flex;gap:4px;margin-bottom:3px}
    .bc-chip{padding:2px 6px;border-radius:4px;font-size:7px;font-weight:600}
    .bc-chip-gold{background:#c9a96e;color:#0e0c0a}
    .bc-chip-blue{background:#2e86de;color:#fff}
    .bc-chip-green{background:#3a7d44;color:#fff}

    .theme-info{padding:18px 20px 20px}
    .theme-icon-row{display:flex;align-items:center;gap:10px;margin-bottom:8px}
    .theme-icon{font-size:24px}
    .theme-name{font-size:17px;font-weight:700;color:#1e293b}
    .theme-desc{font-size:13px;color:#64748b;line-height:1.5;margin-bottom:14px}
    .theme-btn{display:inline-flex;align-items:center;gap:6px;background:#1e293b;color:#fff;padding:9px 18px;border-radius:100px;font-size:13px;font-weight:700;transition:all .2s}
    .theme-card:hover .theme-btn{background:#3b82f6;gap:10px}

    footer{text-align:center;padding:32px;border-top:1px solid #e2e8f0;color:#94a3b8;font-size:13px}
    </style>
</head>
<body>

<div class="hero">
    <div class="hero-badge">✨ Live Preview</div>
    <h1>Coba Semua <span>Tema Toko</span><br>Sebelum Beli</h1>
    <p>Klik tema di bawah untuk melihat tampilan lengkap dengan data contoh. Semua tema tersedia di <?= htmlspecialchars(APP_NAME) ?>.</p>
</div>

<div class="container">
    <div class="section-title">6 Tema Tersedia</div>
    <div class="themes-grid">

        <!-- Coffee -->
        <a href="<?= BASE_URL ?>/demo/coffee" class="theme-card">
            <div class="theme-preview theme-preview-coffee">
                <div class="browser-mock browser-mock-coffee">
                    <div class="browser-bar browser-bar-coffee"><div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div></div>
                    <div class="browser-content">
                        <div class="bc-title bc-title-dark">☕ Kopi Nusantara</div>
                        <div class="bc-row"><span class="bc-chip bc-chip-gold">Americano</span><span class="bc-chip bc-chip-gold">Latte</span></div>
                        <div class="bc-row"><span class="bc-chip bc-chip-gold">Cold Brew</span></div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-icon-row"><span class="theme-icon">☕</span><span class="theme-name">Coffee Shop</span></div>
                <p class="theme-desc"><?= $niches['coffee']['desc'] ?></p>
                <span class="theme-btn">Preview →</span>
            </div>
        </a>

        <!-- Laundry -->
        <a href="<?= BASE_URL ?>/demo/laundry" class="theme-card">
            <div class="theme-preview theme-preview-laundry">
                <div class="browser-mock">
                    <div class="browser-bar"><div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div></div>
                    <div class="browser-content">
                        <div class="bc-title">🧺 Clean & Fresh</div>
                        <div class="bc-row"><span class="bc-chip bc-chip-blue">Kiloan</span><span class="bc-chip bc-chip-blue">Express</span></div>
                        <div class="bc-row"><span class="bc-chip bc-chip-blue">Satuan</span></div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-icon-row"><span class="theme-icon">🧺</span><span class="theme-name">Laundry</span></div>
                <p class="theme-desc"><?= $niches['laundry']['desc'] ?></p>
                <span class="theme-btn">Preview →</span>
            </div>
        </a>

        <!-- Barbershop -->
        <a href="<?= BASE_URL ?>/demo/barbershop" class="theme-card">
            <div class="theme-preview theme-preview-barbershop">
                <div class="browser-mock">
                    <div class="browser-bar"><div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div></div>
                    <div class="browser-content">
                        <div class="bc-title">✂️ Rapi Barbershop</div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Fade</span><span class="bc-chip bc-chip-green">Undercut</span></div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Creambath</span></div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-icon-row"><span class="theme-icon">✂️</span><span class="theme-name">Barbershop</span></div>
                <p class="theme-desc"><?= $niches['barbershop']['desc'] ?></p>
                <span class="theme-btn">Preview →</span>
            </div>
        </a>

        <!-- Restaurant -->
        <a href="<?= BASE_URL ?>/demo/restaurant" class="theme-card">
            <div class="theme-preview theme-preview-restaurant">
                <div class="browser-mock">
                    <div class="browser-bar"><div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div></div>
                    <div class="browser-content">
                        <div class="bc-title">🍛 Warung Segar</div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Nasi Goreng</span></div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Soto</span><span class="bc-chip bc-chip-green">Ayam</span></div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-icon-row"><span class="theme-icon">🍛</span><span class="theme-name">Restoran</span></div>
                <p class="theme-desc"><?= $niches['restaurant']['desc'] ?></p>
                <span class="theme-btn">Preview →</span>
            </div>
        </a>

        <!-- Fashion -->
        <a href="<?= BASE_URL ?>/demo/fashion" class="theme-card">
            <div class="theme-preview theme-preview-fashion">
                <div class="browser-mock">
                    <div class="browser-bar"><div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div></div>
                    <div class="browser-content">
                        <div class="bc-title">👗 Green Thrift</div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Jaket</span><span class="bc-chip bc-chip-green">Jeans</span></div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Tote Bag</span></div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-icon-row"><span class="theme-icon">👗</span><span class="theme-name">Fashion & Thrift</span></div>
                <p class="theme-desc"><?= $niches['fashion']['desc'] ?></p>
                <span class="theme-btn">Preview →</span>
            </div>
        </a>

        <!-- Bakery -->
        <a href="<?= BASE_URL ?>/demo/bakery" class="theme-card">
            <div class="theme-preview theme-preview-bakery">
                <div class="browser-mock">
                    <div class="browser-bar"><div class="browser-dot"></div><div class="browser-dot"></div><div class="browser-dot"></div></div>
                    <div class="browser-content">
                        <div class="bc-title">🍰 Sweet Corner</div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Croissant</span></div>
                        <div class="bc-row"><span class="bc-chip bc-chip-green">Brownies</span><span class="bc-chip bc-chip-green">Roti</span></div>
                    </div>
                </div>
            </div>
            <div class="theme-info">
                <div class="theme-icon-row"><span class="theme-icon">🍰</span><span class="theme-name">Bakery & Kue</span></div>
                <p class="theme-desc"><?= $niches['bakery']['desc'] ?></p>
                <span class="theme-btn">Preview →</span>
            </div>
        </a>

    </div>
</div>

<footer>
    <?= htmlspecialchars(APP_NAME) ?> &nbsp;·&nbsp; Semua tema tersedia lengkap setelah pembelian
</footer>

</body>
</html>
