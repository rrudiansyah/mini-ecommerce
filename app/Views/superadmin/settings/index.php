<?php $stores = (new StoreModel())->allWithStats(); ?>

<!-- Pengaturan Sistem -->
<div class="section" style="margin-bottom:28px">
    <h2 style="margin-bottom:18px">⚙️ Pengaturan Sistem</h2>
    <form method="POST" action="<?= BASE_URL ?>/superadmin/settings/system">
    <?php echo $csrf_field ?? ''; ?>
        <div class="form-row">
            <div class="form-group">
                <label>Nama Aplikasi</label>
                <input type="text" name="app_name" value="<?= htmlspecialchars($appName) ?>" required>
            </div>
            <div class="form-group">
                <label>URL Aplikasi</label>
                <input type="text" name="app_url" value="<?= htmlspecialchars($appUrl) ?>">
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:12px; margin-top:4px">
            <button type="submit" class="btn btn-primary">Simpan Pengaturan Sistem</button>
            <span style="font-size:12px; color:#64748b">
                Environment: <strong><?= htmlspecialchars($appEnv) ?></strong>
                &nbsp;·&nbsp; Perlu restart container setelah simpan
            </span>
        </div>
    </form>
</div>

<hr style="border:none; border-top:1px solid #e2e8f0; margin-bottom:28px">

<!-- Daftar Toko -->
<div class="section">
    <h2 style="margin-bottom:18px">🏪 Pengaturan Toko</h2>
    <p style="font-size:13px; color:#64748b; margin-bottom:20px">Klik toko untuk mengatur info, tema, logo, dan role & permission.</p>

    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px">
        <?php foreach ($stores as $s): ?>
        <div style="background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px; padding:20px; transition:all .2s"
             onmouseover="this.style.borderColor='#3b82f6'; this.style.background='#eff6ff'"
             onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'">
            <!-- Header toko -->
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px">
                <?php if (!empty($s['logo'])): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($s['logo']) ?>"
                     style="width:44px; height:44px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0">
                <?php else: ?>
                <div style="width:44px; height:44px; border-radius:8px; background:<?= htmlspecialchars($s['theme_color'] ?? '#3b82f6') ?>; display:flex; align-items:center; justify-content:center; font-size:20px; color:#fff">
                    <?= ['coffee'=>'☕','laundry'=>'🧺','barbershop'=>'✂️','restaurant'=>'🍽️','fashion'=>'👗','bakery'=>'🍰'][$s['niche']] ?? '🏪' ?>
                </div>
                <?php endif; ?>
                <div>
                    <div style="font-weight:700; font-size:15px; color:#1e293b"><?= htmlspecialchars($s['name']) ?></div>
                    <div style="font-size:12px; color:#64748b"><?= $s['niche'] ?> &nbsp;·&nbsp;
                        <span style="color:<?= $s['is_active'] ? '#16a34a' : '#dc2626' ?>">
                            <?= $s['is_active'] ? '● Aktif' : '● Nonaktif' ?>
                        </span>
                    </div>
                </div>
            </div>
            <!-- Stats mini -->
            <div style="display:flex; gap:16px; font-size:12px; color:#64748b; margin-bottom:16px">
                <span>📦 <?= $s['product_count'] ?> produk</span>
                <span>🛒 <?= $s['order_count'] ?> pesanan</span>
            </div>
            <!-- Tombol aksi -->
            <div style="display:flex; gap:8px">
                <a href="<?= BASE_URL ?>/superadmin/settings/store/<?= $s['id'] ?>"
                   class="btn btn-primary btn-sm" style="flex:1; text-align:center">✏️ Info & Tema</a>
                <a href="<?= BASE_URL ?>/superadmin/settings/store/<?= $s['id'] ?>/roles"
                   class="btn btn-sm" style="flex:1; text-align:center">🔐 Role & Permission</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
