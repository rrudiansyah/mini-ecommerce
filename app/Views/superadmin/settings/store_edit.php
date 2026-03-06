<div style="max-width:680px">
    <a href="<?= BASE_URL ?>/superadmin/settings" class="btn btn-sm" style="margin-bottom:20px">← Kembali</a>

    <form method="POST" action="<?= BASE_URL ?>/superadmin/settings/store/<?= $store['id'] ?>/save" enctype="multipart/form-data">
    <?php echo $csrf_field ?? ''; ?>

        <!-- Info Toko -->
        <div class="section" style="margin-bottom:28px">
            <h2 style="margin-bottom:20px">📋 Info Toko</h2>

            <div class="form-group">
                <label>Nama Toko *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($store['name']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nomor WhatsApp / Telepon</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($store['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Niche</label>
                    <input type="text" value="<?= htmlspecialchars($store['niche']) ?>" disabled
                           style="background:#f1f5f9; color:#64748b; cursor:not-allowed">
                    <small style="color:#94a3b8; font-size:11px">Niche tidak dapat diubah</small>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" rows="2" placeholder="Jl. ..."><?= htmlspecialchars($store['address'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Deskripsi Toko</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat toko..."><?= htmlspecialchars($store['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Slug (URL Publik)</label>
                <div style="display:flex; align-items:center; gap:10px">
                    <span style="font-size:13px; color:#64748b; background:#f1f5f9; padding:9px 12px; border-radius:8px; border:1px solid #e2e8f0">
                        <?= BASE_URL ?>/toko/
                    </span>
                    <code style="font-size:14px; color:#3b82f6; font-weight:700"><?= htmlspecialchars($store['slug']) ?></code>
                </div>
                <small style="color:#94a3b8; font-size:11px">Slug otomatis diperbarui jika nama toko diubah</small>
            </div>
        </div>

        <!-- Tampilan & Tema -->
        <div class="section" style="margin-bottom:28px">
            <h2 style="margin-bottom:20px">🎨 Tampilan & Tema</h2>

            <div class="form-row">
                <div class="form-group">
                    <label>Warna Tema</label>
                    <div style="display:flex; align-items:center; gap:12px">
                        <input type="color" name="theme_color"
                               value="<?= htmlspecialchars($store['theme_color'] ?? '#3b82f6') ?>"
                               id="themeColor"
                               style="width:52px; height:40px; padding:2px; border-radius:8px; cursor:pointer; border:1.5px solid #e2e8f0"
                               oninput="updatePreview(this.value)">
                        <div id="colorPreview" style="padding:8px 20px; border-radius:8px; color:#fff; font-size:13px; font-weight:700; background:<?= htmlspecialchars($store['theme_color'] ?? '#3b82f6') ?>">
                            <?= htmlspecialchars($store['theme_color'] ?? '#3b82f6') ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Logo Toko</label>
                    <div style="display:flex; align-items:center; gap:12px">
                        <?php if (!empty($store['logo'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($store['logo']) ?>"
                             id="logoPreview"
                             style="width:52px; height:52px; border-radius:8px; object-fit:cover; border:1.5px solid #e2e8f0">
                        <?php else: ?>
                        <div id="logoPreview" style="width:52px; height:52px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:24px; border:1.5px dashed #e2e8f0">🏪</div>
                        <?php endif; ?>
                        <input type="file" name="logo" accept="image/*" onchange="previewLogo(this)"
                               style="font-size:13px; color:#64748b">
                    </div>
                    <small style="color:#94a3b8; font-size:11px">JPG, PNG, WebP. Maks. 2MB</small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= BASE_URL ?>/superadmin/settings" class="btn">Batal</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
function updatePreview(val) {
    const preview = document.getElementById('colorPreview');
    preview.style.background = val;
    preview.textContent = val;
}
function previewLogo(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const el = document.getElementById('logoPreview');
        el.outerHTML = `<img id="logoPreview" src="${e.target.result}" style="width:52px; height:52px; border-radius:8px; object-fit:cover; border:1.5px solid #e2e8f0">`;
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
