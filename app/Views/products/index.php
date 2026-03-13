<?php
require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
$productCount = count($products);
$limit        = $planLimit ?? PlanHelper::limit('products');
$overLimit    = $planOverLimit ?? PlanHelper::isOverLimit('products', $productCount);
$limitLabel   = $limit === -1 ? '∞' : $limit;
?>

<?php if ($overLimit): ?>
<div style="background:#fff8f0;border:1.5px solid #fed7aa;border-radius:12px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:12px">
  <span style="font-size:22px">🔒</span>
  <div style="flex:1">
    <strong style="color:#92400e">Batas produk paket <?= htmlspecialchars($planName ?? PlanHelper::planName()) ?> tercapai (<?= $productCount ?>/<?= $limitLabel ?>)</strong>
    <div style="font-size:13px;color:#b45309;margin-top:2px">Hubungi Super Admin untuk upgrade ke paket Pro (maks 100 produk) atau Bisnis (unlimited).</div>
  </div>
</div>
<?php endif; ?>

<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px">
  <div style="display:flex;align-items:center;gap:10px">
    <span style="font-size:13px;color:#888">
      <?= $productCount ?> / <?= $limitLabel ?> produk
      <?php if ($limit !== -1): ?>
      <span style="display:inline-block;background:#e5e7eb;border-radius:100px;height:6px;width:80px;vertical-align:middle;margin-left:6px">
        <span style="display:block;background:<?= $overLimit ? '#ef4444' : ($productCount/$limit > 0.8 ? '#f59e0b' : '#22c55e') ?>;border-radius:100px;height:6px;width:<?= min(100, round($productCount/$limit*100)) ?>%"></span>
      </span>
      <?php endif; ?>
    </span>
    <?= PlanHelper::badge() ?>
  </div>
  <?php if ($overLimit): ?>
    <button class="btn btn-primary" disabled title="<?= htmlspecialchars(PlanHelper::upgradeMessage('products')) ?>" style="opacity:.5;cursor:not-allowed">🔒 Tambah Produk</button>
  <?php else: ?>
    <a href="<?= BASE_URL ?>/products/create" class="btn btn-primary">+ Tambah Produk</a>
  <?php endif; ?>
</div>
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?php if ($p['image']): ?><img src="<?= BASE_URL ?>/<?= $p['image'] ?>" width="48" height="48" style="border-radius:8px;object-fit:cover"><?php else: ?><div style="width:48px;height:48px;background:#f4f2ee;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px">📦</div><?php endif; ?></td>
            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
            <td><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
            <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
            <td>
                <?php if (!empty($p['has_variants'])): ?>
                    <?php
                    $totalStok = 0;
                    $lowCount  = 0;
                    foreach ($p['variants'] ?? [] as $v) {
                        $totalStok += (int)$v['stock'];
                        if ((int)$v['stock'] <= 5) $lowCount++;
                    }
                    ?>
                    <span style="font-weight:700;<?= $totalStok == 0 ? 'color:#dc2626' : ($lowCount > 0 ? 'color:#d97706' : 'color:#16a34a') ?>">
                        <?= $totalStok ?> pcs
                    </span>
                    <?php if ($lowCount > 0): ?>
                    <br><small style="color:#d97706;font-size:11px">⚠️ <?= $lowCount ?> varian menipis</small>
                    <?php endif; ?>
                    <br>
                    <button onclick="toggleVariantStock(<?= $p['id'] ?>)"
                            style="font-size:11px;color:#6b7280;background:none;border:none;cursor:pointer;padding:2px 0;text-decoration:underline">
                        lihat detail
                    </button>
                    <div id="vstk-<?= $p['id'] ?>" style="display:none;margin-top:6px">
                        <?php foreach ($p['variants'] ?? [] as $v): ?>
                        <div style="display:flex;justify-content:space-between;align-items:center;
                                    padding:4px 8px;border-radius:6px;margin-bottom:3px;
                                    background:<?= (int)$v['stock'] == 0 ? '#fef2f2' : ((int)$v['stock'] <= 5 ? '#fffbeb' : '#f0fdf4') ?>">
                            <span style="font-size:12px"><?= htmlspecialchars($v['label']) ?></span>
                            <span style="font-weight:700;font-size:12px;color:<?= (int)$v['stock'] == 0 ? '#dc2626' : ((int)$v['stock'] <= 5 ? '#d97706' : '#16a34a') ?>">
                                <?= $v['stock'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ((int)($p['stock'] ?? -1) >= 0): ?>
                    <?php $stk = (int)$p['stock']; ?>
                    <span style="font-weight:700;font-size:15px;
                        color:<?= $stk == 0 ? '#dc2626' : ($stk <= 5 ? '#d97706' : '#16a34a') ?>">
                        <?= $stk ?> pcs
                    </span>
                    <?php if ($stk == 0): ?>
                    <br><small style="color:#dc2626;font-size:11px">⛔ Habis</small>
                    <?php elseif ($stk <= 5): ?>
                    <br><small style="color:#d97706;font-size:11px">⚠️ Menipis</small>
                    <?php endif; ?>
                <?php else: ?>
                    <span style="color:#9ca3af;font-size:13px">— tidak ditrack</span>
                <?php endif; ?>
            </td>
            <td><span class="badge badge-<?= $p['is_available'] ? 'selesai' : 'batal' ?>"><?= $p['is_available'] ? 'Tersedia' : 'Habis' ?></span></td>
            <td style="white-space:nowrap">
                <a href="<?= BASE_URL ?>/products/edit/<?= $p['id'] ?>" class="btn btn-sm">Edit</a>
                <form method="POST" action="<?= BASE_URL ?>/products/delete/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Hapus produk ini?')">
                    <?php echo $csrf_field ?? ''; ?>
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function toggleVariantStock(id) {
    const el  = document.getElementById('vstk-' + id);
    const btn = event.target;
    if (el.style.display === 'none') {
        el.style.display = 'block';
        btn.textContent  = 'sembunyikan';
    } else {
        el.style.display = 'none';
        btn.textContent  = 'lihat detail';
    }
}
</script>
