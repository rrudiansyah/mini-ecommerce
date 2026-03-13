<?php
// app/Views/variants/index.php
require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
$canManage = PlanHelper::canFeature('variants');
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:10px">
    <div>
        <h2 style="margin:0;font-size:20px;font-weight:700">🎨 Manajemen Varian Produk</h2>
        <p style="margin:4px 0 0;color:#888;font-size:13px">Kelola tipe varian (Ukuran, Warna, dll) untuk produk toko Anda</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>/products" class="btn btn-sm">← Produk</a>
        <?php if ($canManage): ?>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('addTypeModal').style.display='flex'">
            + Tambah Tipe Varian
        </button>
        <?php else: ?>
        <span class="btn btn-sm" style="opacity:.5;cursor:not-allowed;background:#e5e7eb;color:#666"
              title="<?= htmlspecialchars(PlanHelper::upgradeMessage('variants')) ?>">
            🔒 Tambah (Pro+)
        </span>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($types)): ?>
<div style="text-align:center;padding:60px 20px;background:#f9fafb;border-radius:12px;border:2px dashed #e5e7eb">
    <div style="font-size:48px;margin-bottom:16px">🎨</div>
    <h3 style="margin:0 0 8px;color:#374151">Belum ada tipe varian</h3>
    <p style="color:#9ca3af;margin:0 0 20px">Buat tipe varian seperti "Ukuran" (S,M,L,XL) atau "Warna" (Merah,Biru,Hitam)</p>
    <?php if ($canManage): ?>
    <button class="btn btn-primary" onclick="document.getElementById('addTypeModal').style.display='flex'">
        + Buat Tipe Varian Pertama
    </button>
    <?php endif; ?>
</div>
<?php else: ?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
    <?php foreach ($types as $type): ?>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:700"><?= htmlspecialchars($type['name']) ?></h3>
                <span style="font-size:12px;color:#888"><?= count($type['options_list']) ?> opsi</span>
            </div>
            <?php if ($canManage): ?>
            <div style="display:flex;gap:6px">
                <button class="btn btn-sm" style="background:#f0f9ff;color:#0369a1;border:1px solid #bae6fd"
                        onclick="editType(<?= $type['id'] ?>, '<?= htmlspecialchars((string)($type['name'] ?? ''), ENT_QUOTES) ?>',
                        '<?= implode(',', array_column($type['options_list'], 'value')) ?>')">
                    ✏️ Edit
                </button>
                <form method="POST" action="<?= BASE_URL ?>/variants/type/delete/<?= $type['id'] ?>"
                      onsubmit="return confirm('Hapus tipe varian ini? Semua varian produk yang terkait akan ikut terhapus.')">
                    <?= $csrf_field ?>
                    <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca">
                        🗑 Hapus
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:6px">
            <?php foreach ($type['options_list'] as $opt): ?>
            <span style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:20px;padding:4px 12px;font-size:13px;font-weight:500">
                <?= htmlspecialchars((string)($opt['value'] ?? '')) ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<!-- Info cara pakai -->
<div style="margin-top:24px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:16px">
    <h4 style="margin:0 0 8px;color:#0369a1">💡 Cara Pakai Varian</h4>
    <ol style="margin:0;padding-left:20px;color:#0369a1;font-size:13px;line-height:1.8">
        <li>Buat tipe varian di halaman ini (misal: <strong>Ukuran</strong> dengan opsi <strong>S, M, L, XL</strong>)</li>
        <li>Buka menu <strong>Produk → Edit Produk</strong></li>
        <li>Aktifkan <strong>"Produk ini memiliki varian"</strong></li>
        <li>Pilih tipe varian dan isi harga + stok per varian</li>
    </ol>
</div>

<!-- Modal Tambah Tipe -->
<div id="addTypeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:28px;width:100%;max-width:460px;margin:16px">
        <h3 style="margin:0 0 20px;font-size:18px">+ Tambah Tipe Varian</h3>
        <form method="POST" action="<?= BASE_URL ?>/variants/type/store">
            <?= $csrf_field ?>
            <div class="form-group" style="margin-bottom:16px">
                <label style="font-weight:600;display:block;margin-bottom:6px">Nama Tipe *</label>
                <input type="text" name="type_name" placeholder="misal: Ukuran / Warna / Rasa"
                       required style="width:100%;padding:10px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;box-sizing:border-box">
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label style="font-weight:600;display:block;margin-bottom:6px">Opsi * <small style="color:#888;font-weight:400">(pisahkan dengan koma)</small></label>
                <input type="text" name="options" placeholder="misal: S, M, L, XL"
                       required style="width:100%;padding:10px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;box-sizing:border-box">
                <small style="color:#888">Contoh: S, M, L, XL &nbsp;|&nbsp; Merah, Biru, Hitam, Putih</small>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" class="btn" onclick="document.getElementById('addTypeModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Tipe -->
<div id="editTypeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:28px;width:100%;max-width:460px;margin:16px">
        <h3 style="margin:0 0 20px;font-size:18px">✏️ Edit Tipe Varian</h3>
        <form method="POST" id="editTypeForm" action="">
            <?= $csrf_field ?>
            <div class="form-group" style="margin-bottom:16px">
                <label style="font-weight:600;display:block;margin-bottom:6px">Nama Tipe *</label>
                <input type="text" name="type_name" id="editTypeName"
                       required style="width:100%;padding:10px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;box-sizing:border-box">
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label style="font-weight:600;display:block;margin-bottom:6px">Opsi * <small style="color:#888;font-weight:400">(pisahkan dengan koma)</small></label>
                <input type="text" name="options" id="editTypeOptions"
                       required style="width:100%;padding:10px;border:1.5px solid #ddd;border-radius:8px;font-size:14px;box-sizing:border-box">
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" class="btn" onclick="document.getElementById('editTypeModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editType(id, name, options) {
    document.getElementById('editTypeName').value = name;
    document.getElementById('editTypeOptions').value = options;
    document.getElementById('editTypeForm').action = '<?= BASE_URL ?>/variants/type/update/' + id;
    document.getElementById('editTypeModal').style.display = 'flex';
}
// Tutup modal klik di luar
['addTypeModal','editTypeModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
