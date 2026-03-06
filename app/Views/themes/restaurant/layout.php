<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($store['name'] ?? '') ?> — Restoran</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <style>
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    :root{
        --bg:#f8faf5;--bg2:#f0f5e8;--white:#fff;
        --green:#2d6a4f;--green-lt:#52b788;--green-dk:#1b4332;
        --lime:#d8f3dc;--lime2:#b7e4c7;
        --text:#1a2e1e;--muted:#5a7a62;--border:#c8dfc8;
        --radius:16px;--shadow:0 4px 28px rgba(45,106,79,.10);
        --font-d:'Cormorant Garamond',serif;--font-b:'Plus Jakarta Sans',sans-serif;
    }
    html{scroll-behavior:smooth;}
    body{background:var(--bg);color:var(--text);font-family:var(--font-b);overflow-x:hidden;}

    /* NAV */
    nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:16px 52px;display:flex;justify-content:space-between;align-items:center;transition:all .3s;}
    nav.scrolled{background:rgba(248,250,245,.93);backdrop-filter:blur(12px);box-shadow:0 1px 0 var(--border);}
    .nav-logo{font-family:var(--font-d);font-size:24px;font-weight:600;color:var(--green);letter-spacing:.02em;}
    .nav-links{display:flex;gap:32px;}
    .nav-links a{color:var(--muted);text-decoration:none;font-size:13px;font-weight:600;transition:color .2s;}
    .nav-links a:hover{color:var(--green);}
    .cart-nav-btn{position:relative;background:var(--green);color:#fff;border:none;padding:10px 20px;border-radius:100px;font-family:var(--font-b);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:8px;}
    .cart-nav-btn:hover{background:var(--green-dk);transform:translateY(-1px);}
    .cart-badge{background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:none;align-items:center;justify-content:center;}

    /* HERO */
    #hero{min-height:100vh;display:flex;align-items:center;padding:120px 80px 80px;position:relative;overflow:hidden;}
    .hero-leaf{position:absolute;font-size:120px;opacity:.06;pointer-events:none;}
    .leaf-1{top:10%;right:5%;transform:rotate(20deg);}
    .leaf-2{bottom:10%;left:3%;transform:rotate(-15deg);}
    .leaf-3{top:50%;right:25%;font-size:60px;opacity:.04;}
    .hero-content{position:relative;z-index:1;max-width:680px;}
    .hero-pill{display:inline-flex;align-items:center;gap:8px;background:var(--lime);color:var(--green);padding:7px 16px;border-radius:100px;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:20px;opacity:0;animation:fadeUp .7s .1s forwards;}
    .hero-title{font-family:var(--font-d);font-size:clamp(3.5rem,6vw,5.5rem);line-height:1.05;color:var(--text);margin-bottom:18px;opacity:0;animation:fadeUp .7s .25s forwards;}
    .hero-title em{color:var(--green);font-style:italic;}
    .hero-sub{font-size:16px;color:var(--muted);max-width:480px;line-height:1.8;margin-bottom:36px;opacity:0;animation:fadeUp .7s .4s forwards;}
    .hero-actions{display:flex;gap:16px;align-items:center;opacity:0;animation:fadeUp .7s .55s forwards;}
    .btn-p{background:var(--green);color:#fff;padding:14px 30px;border-radius:100px;font-weight:700;font-size:15px;text-decoration:none;transition:all .2s;border:none;cursor:pointer;font-family:var(--font-b);}
    .btn-p:hover{background:var(--green-dk);transform:translateY(-2px);box-shadow:0 8px 24px rgba(45,106,79,.3);}
    .btn-s{color:var(--green);font-size:14px;font-weight:600;text-decoration:none;}
    .hero-stats{display:flex;gap:40px;margin-top:48px;padding-top:32px;border-top:1px solid var(--border);opacity:0;animation:fadeUp .7s .7s forwards;}
    .stat-n{font-family:var(--font-d);font-size:2.2rem;font-weight:600;color:var(--green);}
    .stat-l{font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-top:2px;}

    /* MENU */
    #menu{padding:100px 80px;background:var(--white);}
    .sec-hd{text-align:center;margin-bottom:52px;}
    .sec-tag{display:inline-block;background:var(--lime);color:var(--green);padding:5px 16px;border-radius:100px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;margin-bottom:12px;}
    .sec-title{font-family:var(--font-d);font-size:clamp(2.2rem,4vw,3.2rem);color:var(--text);}
    .cat-tabs{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-bottom:44px;}
    .cat-tab{padding:9px 22px;border-radius:100px;border:1.5px solid var(--border);background:transparent;color:var(--muted);font-family:var(--font-b);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;}
    .cat-tab:hover{border-color:var(--green);color:var(--green);}
    .cat-tab.active{background:var(--green);color:#fff;border-color:var(--green);}
    .menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;max-width:1100px;margin:0 auto;}
    .menu-card{background:var(--bg);border:1.5px solid var(--border);border-radius:var(--radius);overflow:hidden;cursor:pointer;transition:all .25s;}
    .menu-card:hover{transform:translateY(-4px);box-shadow:var(--shadow);border-color:var(--green-lt);}
    .menu-img{aspect-ratio:16/9;background:var(--bg2);display:flex;align-items:center;justify-content:center;font-size:52px;overflow:hidden;position:relative;}
    .menu-img img{width:100%;height:100%;object-fit:cover;}
    .menu-body{padding:18px 20px 20px;}
    .menu-cat-tag{font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--green-lt);margin-bottom:4px;}
    .menu-name{font-family:var(--font-d);font-size:20px;font-weight:600;color:var(--text);margin-bottom:6px;}
    .menu-desc{font-size:13px;color:var(--muted);margin-bottom:14px;line-height:1.6;}
    .menu-footer{display:flex;align-items:center;justify-content:space-between;}
    .menu-price{font-size:17px;font-weight:700;color:var(--green);}
    .add-btn{width:36px;height:36px;border-radius:50%;border:2px solid var(--green);background:transparent;color:var(--green);font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;}
    .add-btn:hover{background:var(--green);color:#fff;}

    /* CART */
    .overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;opacity:0;pointer-events:none;transition:opacity .3s;}
    .overlay.open{opacity:1;pointer-events:all;}
    .cart-sidebar{position:fixed;top:0;right:0;width:min(420px,100vw);height:100vh;background:var(--white);border-left:1px solid var(--border);z-index:201;display:flex;flex-direction:column;transform:translateX(100%);transition:transform .35s cubic-bezier(.4,0,.2,1);}
    .overlay.open .cart-sidebar{transform:translateX(0);}
    .cart-head{padding:22px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
    .cart-head h2{font-family:var(--font-d);font-size:22px;color:var(--text);}
    .cart-close{background:var(--bg);border:none;color:var(--muted);width:32px;height:32px;border-radius:50%;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
    .cart-close:hover{background:var(--border);}
    .cart-items{flex:1;overflow-y:auto;padding:14px 20px;}
    .cart-item{display:flex;gap:12px;align-items:center;padding:12px 0;border-bottom:1px solid var(--border);}
    .cart-item-icon{width:48px;height:48px;background:var(--bg);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;overflow:hidden;}
    .cart-item-icon img{width:100%;height:100%;object-fit:cover;}
    .cart-item-info{flex:1;}
    .cart-item-name{font-size:14px;font-weight:600;color:var(--text);}
    .cart-item-price{font-size:12px;color:var(--green);margin-top:2px;}
    .qty-ctrl{display:flex;align-items:center;gap:8px;}
    .qty-btn{width:28px;height:28px;border-radius:50%;border:1.5px solid var(--border);background:var(--bg);color:var(--text);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;}
    .qty-btn:hover{border-color:var(--green);color:var(--green);}
    .qty-num{font-size:14px;font-weight:700;min-width:18px;text-align:center;}
    .cart-empty{text-align:center;padding:48px 20px;color:var(--muted);font-size:14px;}
    .cart-footer{padding:18px 20px 24px;border-top:1px solid var(--border);}
    .cart-total{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;}
    .cart-total-lbl{font-size:13px;color:var(--muted);font-weight:600;}
    .cart-total-val{font-family:var(--font-d);font-size:24px;color:var(--green);}
    .btn-checkout{width:100%;padding:14px;background:var(--green);color:#fff;border:none;border-radius:100px;font-family:var(--font-b);font-size:14px;font-weight:700;cursor:pointer;transition:all .2s;}
    .btn-checkout:hover{background:var(--green-dk);}
    .btn-checkout:disabled{opacity:.45;cursor:not-allowed;}
    .order-form{display:none;}.order-form.show{display:block;}
    .form-field{margin-bottom:14px;}
    .form-field label{display:block;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;}
    .form-field input,.form-field textarea{width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:var(--font-b);font-size:14px;color:var(--text);background:var(--bg);outline:none;transition:border-color .2s;}
    .form-field input:focus,.form-field textarea:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(45,106,79,.1);}
    .form-field textarea{resize:none;height:70px;}
    .form-summary{background:var(--bg);border-radius:10px;padding:14px;border:1.5px solid var(--border);margin-bottom:14px;font-size:13px;color:var(--text);line-height:1.9;}
    .btn-order{width:100%;padding:13px;background:var(--green);color:#fff;border:none;border-radius:100px;font-family:var(--font-b);font-size:14px;font-weight:700;cursor:pointer;transition:all .2s;}
    .btn-order:hover{background:var(--green-dk);}
    .btn-order:disabled{opacity:.45;cursor:not-allowed;}
    .btn-back{width:100%;padding:11px;background:transparent;border:1.5px solid var(--border);border-radius:100px;color:var(--muted);font-family:var(--font-b);font-size:13px;cursor:pointer;margin-top:8px;transition:all .2s;}
    .btn-back:hover{border-color:var(--green);color:var(--green);}
    .order-success{display:none;text-align:center;padding:24px 0;}.order-success.show{display:block;}
    .btn-wa{display:inline-flex;align-items:center;gap:10px;background:#25D366;color:#fff;padding:12px 24px;border-radius:100px;font-size:14px;font-weight:700;text-decoration:none;transition:all .2s;font-family:var(--font-b);}
    .btn-wa:hover{background:#1fba58;}
    .btn-again{display:block;width:100%;padding:11px;background:transparent;border:1.5px solid var(--border);border-radius:100px;color:var(--muted);font-family:var(--font-b);font-size:13px;cursor:pointer;margin-top:10px;transition:all .2s;}
    .btn-again:hover{border-color:var(--green);color:var(--green);}

    footer{background:var(--text);color:#fff;padding:60px 80px 36px;text-align:center;}
    .footer-logo{font-family:var(--font-d);font-size:28px;color:#fff;margin-bottom:8px;}
    .footer-tag{font-size:13px;color:#7a9a62;margin-bottom:24px;}
    .footer-info{font-size:13px;color:#7a9a62;line-height:2.2;}
    .footer-copy{font-size:11px;color:#3d5a30;margin-top:32px;}
    .toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--text);color:#fff;padding:11px 22px;border-radius:100px;font-size:13px;font-weight:600;z-index:500;transition:transform .3s;white-space:nowrap;box-shadow:0 8px 28px rgba(26,46,30,.25);}
    .toast.show{transform:translateX(-50%) translateY(0);}

    @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    @media(max-width:900px){
        #hero{padding:100px 24px 60px;}
        #menu{padding:60px 24px;}
        nav{padding:16px 20px;}
        .nav-links{display:none;}
        footer{padding:48px 24px 32px;}
        .hero-stats{gap:24px;}
    }
    .hidden-cat{display:none!important;}
    </style>
</head>
<body>
<?php require ROOT_PATH . '/app/Views/demo/_banner.php'; ?>


<nav id="nav">
    <div class="nav-logo">🌿 <?= htmlspecialchars($store['name'] ?? '') ?></div>
    <div class="nav-links">
        <a href="#menu">Menu</a>
        <a href="#footer">Kontak</a>
    </div>
    <button class="cart-nav-btn" onclick="toggleCart()">
        🛒 Keranjang
        <span class="cart-badge" id="cartBadge">0</span>
    </button>
</nav>

<section id="hero">
    <div class="hero-leaf leaf-1">🌿</div>
    <div class="hero-leaf leaf-2">🍃</div>
    <div class="hero-leaf leaf-3">🌱</div>
    <div class="hero-content">
        <div class="hero-pill">🍽️ Masakan Segar & Alami</div>
        <h1 class="hero-title">Cita Rasa <em>Alami</em><br>di Setiap Suapan</h1>
        <p class="hero-sub">Dibuat dari bahan-bahan segar pilihan setiap hari. Setiap hidangan adalah pengalaman rasa yang tak terlupakan.</p>
        <div class="hero-actions">
            <a href="#menu" class="btn-p">🍴 Lihat Menu</a>
            <a href="#menu" class="btn-s">Pesan Sekarang →</a>
        </div>
        <div class="hero-stats">
            <div><div class="stat-n"><?= count($products) ?>+</div><div class="stat-l">Menu Pilihan</div></div>
            <div><div class="stat-n"><?= count($categories) ?></div><div class="stat-l">Kategori</div></div>
            <div><div class="stat-n">100%</div><div class="stat-l">Bahan Segar</div></div>
        </div>
    </div>
</section>

<section id="menu">
    <div class="sec-hd">
        <div class="sec-tag">Menu Kami</div>
        <h2 class="sec-title">Pilihan Hidangan</h2>
    </div>
    <?php if(empty($menu)): ?>
    <p style="text-align:center;color:var(--muted);padding:40px">Menu belum tersedia.</p>
    <?php else: ?>
    <?php if(count($menu)>1): ?>
    <div class="cat-tabs">
        <?php $f=true; foreach($menu as $cid=>$cat): ?>
        <button class="cat-tab <?=$f?'active':''?>" onclick="switchCat(this,'cat-<?=$cid?>')">
            <?=htmlspecialchars($cat['icon'] ?? '')?> <?=htmlspecialchars($cat['name'] ?? '')?>
        </button>
        <?php $f=false; endforeach; ?>
    </div>
    <?php endif; ?>
    <?php $f=true; foreach($menu as $cid=>$cat): ?>
    <div class="menu-grid <?=$f?'':'hidden-cat'?>" id="cat-<?=$cid?>">
        <?php foreach($cat['products'] as $p): ?>
        <div class="menu-card" onclick="addToCart(<?=$p['id']?>,'<?=htmlspecialchars($p['name'] ?? '', ENT_QUOTES)?>',<?=$p['price']?>,'<?=htmlspecialchars($p['image']??'',ENT_QUOTES)?>','<?=htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES)?>')">
            <div class="menu-img">
                <?php if(!empty($p['image'])): ?><img src="<?=BASE_URL?>/<?=htmlspecialchars($p['image'] ?? '')?>" alt="<?=htmlspecialchars($p['name'] ?? '')?>" onerror="this.parentElement.innerHTML='<?=htmlspecialchars($cat['icon'] ?? '')?>'">
                <?php else: ?><?=htmlspecialchars($cat['icon'] ?? '')?><?php endif; ?>
            </div>
            <div class="menu-body">
                <div class="menu-cat-tag"><?=htmlspecialchars($cat['name'] ?? '')?></div>
                <div class="menu-name"><?=htmlspecialchars($p['name'] ?? '')?></div>
                <?php if(!empty($p['description'])): ?><div class="menu-desc"><?=htmlspecialchars($p['description'] ?? '')?></div><?php endif; ?>
                <div class="menu-footer">
                    <div class="menu-price">Rp <?=number_format($p['price'],0,',','.')?></div>
                    <button class="add-btn" onclick="event.stopPropagation();addToCart(<?=$p['id']?>,'<?=htmlspecialchars($p['name'] ?? '', ENT_QUOTES)?>',<?=$p['price']?>,'<?=htmlspecialchars($p['image']??'',ENT_QUOTES)?>','<?=htmlspecialchars($cat['icon'] ?? '', ENT_QUOTES)?>')">+</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php $f=false; endforeach; ?>
    <?php endif; ?>
</section>

<footer id="footer">
    <div class="footer-logo">🌿 <?=htmlspecialchars($store['name'] ?? '')?></div>
    <p class="footer-tag">Restoran & Warung Makan Segar</p>
    <div class="footer-info">
        <?php if(!empty($store['address'])): ?>📍 <?=htmlspecialchars($store['address'] ?? '')?><br><?php endif; ?>
        <?php if(!empty($store['phone'])): ?>📞 <?=htmlspecialchars($store['phone'] ?? '')?><?php endif; ?>
    </div>
    <p class="footer-copy">© <?=date('Y')?> <?=htmlspecialchars($store['name'] ?? '')?>. All rights reserved.</p>
</footer>

<!-- CART -->
<div class="overlay" id="overlay" onclick="closeOnOverlay(event)">
    <div class="cart-sidebar">
        <div class="cart-head"><h2>Pesananmu</h2><button class="cart-close" onclick="toggleCart()">✕</button></div>
        <div id="cartView">
            <div class="cart-items" id="cartItems"><div class="cart-empty">🍽️ Belum ada pesanan</div></div>
            <div class="cart-footer">
                <div class="cart-total"><span class="cart-total-lbl">Total</span><span class="cart-total-val" id="cartTotal">Rp 0</span></div>
                <button class="btn-checkout" id="checkoutBtn" onclick="showForm()" disabled>Pesan Sekarang →</button>
            </div>
        </div>
        <div id="formView" class="order-form">
            <div class="cart-items">
                <div class="form-field"><label>Nama *</label><input type="text" id="custName" placeholder="Nama kamu"></div>
                <div class="form-field"><label>No. WhatsApp</label><input type="tel" id="custPhone" placeholder="08xxxxxxxxxx"></div>
                <div class="form-field"><label>Catatan</label><textarea id="custNote" placeholder="Contoh: tidak pakai pedas, extra nasi..."></textarea></div>
                <div class="form-summary" id="orderSummary"></div>
            </div>
            <div class="cart-footer">
                <button class="btn-order" onclick="submitOrder()" id="submitBtn">✓ Konfirmasi Pesanan</button>
                <button class="btn-back" onclick="showCart()">← Kembali</button>
            </div>
        </div>
        <div class="order-success" id="successView">
            <div class="cart-items" style="display:flex;align-items:center;justify-content:center;">
                <div style="text-align:center;">
                    <div style="font-size:52px;margin-bottom:12px">🎉</div>
                    <div style="font-family:var(--font-d);font-size:22px;color:var(--text);margin-bottom:8px">Pesanan Masuk!</div>
                    <p style="font-size:13px;color:var(--muted);margin-bottom:22px;line-height:1.7">Segera konfirmasi via WhatsApp agar pesananmu diproses.</p>
                    <a href="#" id="waLink" class="btn-wa" target="_blank">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Konfirmasi via WhatsApp
                    </a>
                    <button class="btn-again" onclick="resetOrder()">Pesan Lagi</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="toast" id="toast"></div>

<script>
let cart={};
window.addEventListener('scroll',()=>document.getElementById('nav').classList.toggle('scrolled',scrollY>50));
function switchCat(btn,id){document.querySelectorAll('[id^="cat-"]').forEach(s=>s.classList.add('hidden-cat'));document.querySelectorAll('.cat-tab').forEach(b=>b.classList.remove('active'));document.getElementById(id)?.classList.remove('hidden-cat');btn.classList.add('active');}
function addToCart(id,name,price,image,icon){cart[id]?cart[id].qty++:cart[id]={id,name,price,image,icon,qty:1};renderCart();showToast(icon+' '+name+' ditambahkan!');}
function changeQty(id,d){if(!cart[id])return;cart[id].qty+=d;if(cart[id].qty<=0)delete cart[id];renderCart();}
function renderCart(){
    const items=Object.values(cart),total=items.reduce((s,i)=>s+i.price*i.qty,0),count=items.reduce((s,i)=>s+i.qty,0);
    document.getElementById('cartTotal').textContent=fmt(total);
    document.getElementById('checkoutBtn').disabled=items.length===0;
    const badge=document.getElementById('cartBadge');badge.textContent=count;badge.style.display=count>0?'flex':'none';
    document.getElementById('cartItems').innerHTML=items.length===0?'<div class="cart-empty">🍽️ Belum ada pesanan</div>':items.map(i=>`<div class="cart-item"><div class="cart-item-icon">${i.image?`<img src="<?=BASE_URL?>/${i.image}" onerror="this.parentElement.innerHTML='${i.icon}'">`:`${i.icon}`}</div><div class="cart-item-info"><div class="cart-item-name">${i.name}</div><div class="cart-item-price">${fmt(i.price)}</div></div><div class="qty-ctrl"><button class="qty-btn" onclick="changeQty(${i.id},-1)">−</button><span class="qty-num">${i.qty}</span><button class="qty-btn" onclick="changeQty(${i.id},1)">+</button></div></div>`).join('');
}
function toggleCart(){document.getElementById('overlay').classList.toggle('open');}
function closeOnOverlay(e){if(e.target===document.getElementById('overlay'))toggleCart();}
function showForm(){document.getElementById('cartView').style.display='none';document.getElementById('formView').classList.add('show');document.getElementById('orderSummary').innerHTML=Object.values(cart).map(i=>`${i.icon} ${i.name} ×${i.qty} = ${fmt(i.price*i.qty)}`).join('<br>')+'<br><strong>Total: '+fmt(Object.values(cart).reduce((s,i)=>s+i.price*i.qty,0))+'</strong>';}
function showCart(){document.getElementById('cartView').style.display='';document.getElementById('formView').classList.remove('show');}
async function submitOrder(){
    const name=document.getElementById('custName').value.trim();
    if(!name){showToast('⚠️ Nama wajib diisi!');return;}
    const btn=document.getElementById('submitBtn');btn.disabled=true;btn.textContent='Memproses...';
    try{
        const res=await fetch('<?=BASE_URL?>/toko/<?=$store['slug']?>/order',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({customer_name:name,customer_phone:document.getElementById('custPhone').value.trim(),note:document.getElementById('custNote').value.trim(),items:Object.values(cart).map(i=>({product_id:i.id,qty:i.qty}))})});
        const data=await res.json();
        if(data.success){document.getElementById('formView').classList.remove('show');document.getElementById('successView').classList.add('show');document.getElementById('waLink').href=data.wa_url;cart={};renderCart();}
        else showToast('❌ '+(data.message||'Terjadi kesalahan.'));
    }catch(e){showToast('❌ Koneksi gagal.');}
    btn.disabled=false;btn.textContent='✓ Konfirmasi Pesanan';
}
function resetOrder(){document.getElementById('successView').classList.remove('show');showCart();toggleCart();document.getElementById('custName').value='';document.getElementById('custPhone').value='';document.getElementById('custNote').value='';}
function fmt(n){return'Rp '+n.toLocaleString('id-ID');}
function showToast(msg){const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2500);}
</script>
</body>
</html>
