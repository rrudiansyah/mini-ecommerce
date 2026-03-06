<?php if (!empty($isDemo)): ?>
<?php
$icons = ['coffee'=>'☕','laundry'=>'🧺','barbershop'=>'✂️','restaurant'=>'🍛','fashion'=>'👗','bakery'=>'🍰'];
$currentNiche = $demoNiche ?? '';
$allNiches = $allNiches ?? [];
?>
<!-- DEMO BANNER -->
<div id="demoBanner" style="
    position:fixed; top:0; left:0; right:0; z-index:99999;
    background:#1e293b; color:#fff;
    display:flex; align-items:center; justify-content:space-between;
    padding:0 20px; height:48px; box-shadow:0 2px 12px rgba(0,0,0,.3);
    font-family:'Plus Jakarta Sans',system-ui,sans-serif; font-size:13px;
">
    <!-- Kiri: label demo -->
    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0">
        <span style="background:#f59e0b;color:#000;padding:3px 10px;border-radius:100px;font-size:11px;font-weight:800;letter-spacing:.06em">DEMO</span>
        <span style="color:#94a3b8;display:none" id="demoDesktop">Ini adalah tampilan demo. Pesanan tidak diproses.</span>
    </div>

    <!-- Tengah: switcher tema -->
    <div style="display:flex;align-items:center;gap:6px;overflow-x:auto;padding:0 8px;scrollbar-width:none">
        <?php foreach ($allNiches as $niche => $info): ?>
        <a href="<?= BASE_URL ?>/demo/<?= $niche ?>"
           style="
               display:inline-flex;align-items:center;gap:5px;
               padding:5px 12px;border-radius:100px;font-size:12px;font-weight:700;
               text-decoration:none;white-space:nowrap;transition:all .2s;
               <?= $niche === $currentNiche
                   ? 'background:#3b82f6;color:#fff;'
                   : 'background:rgba(255,255,255,.08);color:#94a3b8;' ?>
           "
           onmouseover="if('<?= $niche ?>'!=='<?= $currentNiche ?>') this.style.background='rgba(255,255,255,.15)'"
           onmouseout="if('<?= $niche ?>'!=='<?= $currentNiche ?>') this.style.background='rgba(255,255,255,.08)'">
            <?= $info['icon'] ?> <?= $info['label'] ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Kanan: tombol kembali -->
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
        <a href="<?= BASE_URL ?>/demo"
           style="color:#94a3b8;text-decoration:none;font-size:12px;font-weight:600;transition:color .2s"
           onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">
            ← Semua Tema
        </a>
    </div>
</div>

<!-- Dorong konten ke bawah agar tidak tertimpa banner -->
<style>
body { padding-top: 48px !important; }
nav, #nav { top: 48px !important; }
@media(min-width:640px){ #demoDesktop{ display:inline !important; } }
</style>

<!-- Intercept order submit — tampilkan modal demo -->
<div id="demoModal" style="
    display:none;position:fixed;inset:0;z-index:999999;
    background:rgba(0,0,0,.6);backdrop-filter:blur(4px);
    align-items:center;justify-content:center;
">
    <div style="background:#fff;border-radius:20px;padding:40px 36px;max-width:400px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.3)">
        <div style="font-size:48px;margin-bottom:16px">🎯</div>
        <h2 style="font-size:20px;font-weight:800;color:#1e293b;margin-bottom:10px">Ini Mode Demo</h2>
        <p style="font-size:14px;color:#64748b;line-height:1.7;margin-bottom:24px">
            Pesanan tidak diproses karena ini hanya tampilan demo.<br>
            Beli produk ini untuk mengaktifkan fitur pemesanan nyata.
        </p>
        <button onclick="closeDemoModal()"
            style="background:#3b82f6;color:#fff;border:none;padding:12px 28px;border-radius:100px;font-size:14px;font-weight:700;cursor:pointer;width:100%;margin-bottom:10px">
            Lihat Tema Lainnya
        </button>
        <a href="<?= BASE_URL ?>/demo"
           style="display:block;font-size:13px;color:#64748b;text-decoration:none;margin-top:4px">← Kembali ke Daftar Tema</a>
    </div>
</div>

<script>
// Override fetch untuk intercept order di demo mode
const _origFetch = window.fetch;
window.fetch = function(url, opts) {
    if (typeof url === 'string' && url.includes('/order')) {
        showDemoModal();
        return Promise.resolve(new Response(JSON.stringify({
            success: false, demo: true,
            message: 'Mode demo — pesanan tidak diproses.'
        }), { headers: { 'Content-Type': 'application/json' } }));
    }
    return _origFetch.apply(this, arguments);
};
function showDemoModal() {
    document.getElementById('demoModal').style.display = 'flex';
}
function closeDemoModal() {
    document.getElementById('demoModal').style.display = 'none';
}
document.getElementById('demoModal').addEventListener('click', function(e) {
    if (e.target === this) closeDemoModal();
});
</script>
<?php endif; ?>
