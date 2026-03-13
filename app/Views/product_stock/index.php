<?php
$canManage = ($menuPerms['product_stock']['manage'] ?? false);
?>

<!-- Alert stok menipis -->
<?php if (!empty($lowStock)): ?>
<div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:16px;margin-bottom:20px">
    <strong style="color:#c2410c">⚠️ Stok Produk Menipis (<?= count($lowStock) ?> produk)</strong>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px">
        <?php foreach ($lowStock as $p): ?>
        <span style="background:#fff;border:1px solid #fed7aa;border-radius:20px;padding:4px 12px;font-size:12px;color:#92400e">
            <?= htmlspecialchars($p['name']) ?> — sisa <?= $p['stock'] ?> pcs
        </span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Header -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <div>
        <h2 style="margin:0;font-size:20px;font-weight:700">📦 Stok Produk</h2>
        <p style="margin:4px 0 0;color:#888;font-size:13px">
            Kelola stok produk jadi (HPP Manual, tanpa varian) &nbsp;·&nbsp;
            <a href="<?= BASE_URL ?>/variants" style="color:#6b7280;font-size:12px">
                Stok varian → menu 🎨 Varian Produk
            </a>
        </p>
    </div>
    <a href="<?= BASE_URL ?>/product-stock/logs" class="btn btn-sm"
       style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:8px 16px;font-size:13px;text-decoration:none;color:#374151">
        📋 Riwayat Stok
    </a>
</div>

<!-- Modal Stock In -->
<div id="modalStockIn" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;width:420px;max-width:95vw">
    <h3 style="margin:0 0 20px;font-size:16px">➕ Tambah Stok Masuk</h3>
    <form method="POST" action="<?= BASE_URL ?>/product-stock/stock-in">
      <?= $csrf_field ?>
      <input type="hidden" name="product_id" id="stockInProductId">
      <div style="margin-bottom:14px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Produk</label>
        <input type="text" id="stockInProductName" readonly
               style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;box-sizing:border-box;font-size:14px">
      </div>
      <div style="margin-bottom:14px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Jumlah Masuk *</label>
        <input type="number" name="qty" min="1" required placeholder="cth: 10"
               style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;box-sizing:border-box;font-size:14px">
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Catatan</label>
        <input type="text" name="notes" placeholder="cth: Restock dari supplier"
               style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;box-sizing:border-box;font-size:14px">
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" style="flex:1;padding:11px">Simpan</button>
        <button type="button" onclick="closeModal('modalStockIn')"
                style="flex:1;padding:11px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Adjust -->
<div id="modalAdjust" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;width:420px;max-width:95vw">
    <h3 style="margin:0 0 20px;font-size:16px">⚖️ Sesuaikan Stok</h3>
    <form method="POST" id="formAdjust">
      <?= $csrf_field ?>
      <div style="margin-bottom:14px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Produk</label>
        <input type="text" id="adjustProductName" readonly
               style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;box-sizing:border-box;font-size:14px">
      </div>
      <div style="display:flex;gap:12px;margin-bottom:14px">
        <div style="flex:1">
          <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Stok Baru *</label>
          <input type="number" name="new_stock" min="0" required
                 style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;box-sizing:border-box;font-size:14px">
        </div>
        <div style="flex:1">
          <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Stok Min</label>
          <input type="number" name="stock_min" min="0" id="adjustStockMin"
                 style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;box-sizing:border-box;font-size:14px">
        </div>
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Catatan</label>
        <input type="text" name="notes" placeholder="cth: Hitung fisik stok"
               style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;box-sizing:border-box;font-size:14px">
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" style="flex:1;padding:11px">Simpan</button>
        <button type="button" onclick="closeModal('modalAdjust')"
                style="flex:1;padding:11px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Tabel Stok Produk -->
<div style="background:#fff;border-radius:14px;border:1px solid #e8e6e0;overflow:hidden">
  <table style="width:100%;border-collapse:collapse;font-size:14px">
    <thead>
      <tr style="background:#f8f7f4;border-bottom:2px solid #e8e6e0">
        <th style="padding:14px 16px;text-align:left;font-weight:700">Nama Produk</th>
        <th style="padding:14px 16px;text-align:left;font-weight:700">Kategori</th>
        <th style="padding:14px 16px;text-align:center;font-weight:700">Satuan</th>
        <th style="padding:14px 16px;text-align:center;font-weight:700">Stok</th>
        <th style="padding:14px 16px;text-align:center;font-weight:700">Stok Min</th>
        <th style="padding:14px 16px;text-align:right;font-weight:700">Harga/Unit (HPP)</th>
        <th style="padding:14px 16px;text-align:center;font-weight:700">Status</th>
        <?php if ($canManage): ?>
        <th style="padding:14px 16px;text-align:center;font-weight:700">Aksi</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($products)): ?>
      <tr>
        <td colspan="8" style="padding:40px;text-align:center;color:#9ca3af">
          <div style="font-size:32px;margin-bottom:8px">📦</div>
          Belum ada produk HPP Manual.<br>
          <small>Produk dengan HPP "Input Manual" akan muncul di sini.</small>
        </td>
      </tr>
      <?php else: ?>
      <?php foreach ($products as $p):
          $stock    = (int)$p['stock'];
          $stockMin = (int)($p['stock_min'] ?? 0);
          $tracked  = $stock >= 0;
          $isLow    = $tracked && $stockMin > 0 && $stock <= $stockMin;
          $isEmpty  = $tracked && $stock === 0;
      ?>
      <tr style="border-bottom:1px solid #f0ede8;<?= $isEmpty ? 'background:#fff5f5' : ($isLow ? 'background:#fffbeb' : '') ?>">
        <td style="padding:14px 16px;font-weight:600"><?= htmlspecialchars($p['name']) ?></td>
        <td style="padding:14px 16px;color:#6b7280"><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
        <td style="padding:14px 16px;text-align:center;color:#6b7280">pcs</td>
        <td style="padding:14px 16px;text-align:center">
          <?php if (!$tracked): ?>
            <span style="color:#9ca3af;font-size:13px">— tidak ditrack</span>
          <?php else: ?>
            <span style="font-weight:700;font-size:16px;color:<?= $isEmpty ? '#dc2626' : ($isLow ? '#d97706' : '#16a34a') ?>">
              <?= $stock ?>
            </span>
            <?php if ($isEmpty): ?>
              <span style="display:block;font-size:11px;color:#dc2626">⛔ Habis</span>
            <?php elseif ($isLow): ?>
              <span style="display:block;font-size:11px;color:#d97706">⚠️ Menipis</span>
            <?php endif; ?>
          <?php endif; ?>
        </td>
        <td style="padding:14px 16px;text-align:center;color:#6b7280">
          <?= $stockMin > 0 ? $stockMin : '<span style="color:#d1d5db">—</span>' ?>
        </td>
        <td style="padding:14px 16px;text-align:right;color:#374151">
          <?= $p['hpp'] > 0 ? 'Rp ' . number_format($p['hpp'], 0, ',', '.') : '<span style="color:#d1d5db">—</span>' ?>
        </td>
        <td style="padding:14px 16px;text-align:center">
          <?php if ($p['is_available']): ?>
            <span style="background:#dcfce7;color:#166534;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600">Tersedia</span>
          <?php else: ?>
            <span style="background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600">Habis</span>
          <?php endif; ?>
        </td>
        <?php if ($canManage): ?>
        <td style="padding:14px 16px;text-align:center">
          <?php if (($p['hpp_type'] ?? '') === 'auto'): ?>
            <span style="font-size:12px;color:#9ca3af;font-style:italic">🔒 Stok dari resep</span>
          <?php else: ?>
          <div style="display:flex;gap:6px;justify-content:center">
            <button onclick="openStockIn(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')"
                    style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:7px;padding:6px 12px;cursor:pointer;font-size:13px">
                ➕ Masuk
            </button>
            <button onclick="openAdjust(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= $stock ?>, <?= $stockMin ?>)"
                    style="background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:7px;padding:6px 12px;cursor:pointer;font-size:13px">
                ⚖️ Sesuaikan
            </button>
          </div>
          <?php endif; ?>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
function openStockIn(id, name) {
    document.getElementById('stockInProductId').value = id;
    document.getElementById('stockInProductName').value = name;
    document.getElementById('modalStockIn').style.display = 'flex';
}
function openAdjust(id, name, stock, stockMin) {
    document.getElementById('adjustProductName').value = name;
    document.getElementById('adjustStockMin').value = stockMin;
    document.querySelector('#formAdjust input[name="new_stock"]').value = stock < 0 ? 0 : stock;
    document.getElementById('formAdjust').action = '<?= BASE_URL ?>/product-stock/adjust/' + id;
    document.getElementById('modalAdjust').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
// Tutup modal klik luar
['modalStockIn','modalAdjust'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});
</script>
