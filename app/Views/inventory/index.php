<?php
// app/Views/inventory/index.php
$lowCount = count($lowStock);
?>

<?php if ($lowCount > 0): ?>
<div class="alert alert-warning" style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
  <span style="font-size:24px">⚠️</span>
  <div>
    <strong><?= $lowCount ?> bahan</strong> stoknya menipis atau habis:
    <?= implode(', ', array_map(fn($i) => '<strong>'.htmlspecialchars($i['name']).'</strong>', $lowStock)) ?>
  </div>
</div>
<?php endif; ?>

<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
  <div>
    <h2 style="margin:0;font-size:20px;font-weight:700">📦 Manajemen Stok Bahan</h2>
    <p style="margin:4px 0 0;color:#888;font-size:13px">Kelola bahan baku, input stok masuk, dan pantau HPP otomatis</p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>/inventory/logs" class="btn btn-sm">📋 Riwayat Stok</a>
    <a href="<?= BASE_URL ?>/inventory/create" class="btn btn-primary">+ Tambah Bahan</a>
  </div>
</div>

<!-- Modal: Stok Masuk -->
<div id="modalStockIn" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:420px;margin:16px">
    <h3 style="margin:0 0 20px;font-size:17px">📥 Input Stok Masuk</h3>
    <form method="POST" action="<?= BASE_URL ?>/inventory/stock-in">
      <?php echo $csrf_field ?? ''; ?>
      <input type="hidden" name="ingredient_id" id="si_id">
      <div style="margin-bottom:14px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Bahan: <strong id="si_name"></strong></label>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Stok saat ini: <span id="si_stock" style="color:#2563a8"></span></label>
      </div>
      <div style="margin-bottom:14px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Jumlah Masuk <span id="si_unit" style="color:#888"></span></label>
        <input type="number" name="qty" step="0.001" min="0.001" required
               style="width:100%;padding:10px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:15px"
               placeholder="Contoh: 500">
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Keterangan</label>
        <input type="text" name="notes" value="Stok masuk"
               style="width:100%;padding:10px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:14px">
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" style="flex:1">✅ Simpan</button>
        <button type="button" onclick="closeModal('modalStockIn')" class="btn" style="flex:1">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Penyesuaian Stok -->
<div id="modalAdjust" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:420px;margin:16px">
    <h3 style="margin:0 0 20px;font-size:17px">🔧 Penyesuaian Stok</h3>
    <form method="POST" id="adjustForm" action="">
      <?php echo $csrf_field ?? ''; ?>
      <div style="margin-bottom:14px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Bahan: <strong id="adj_name"></strong></label>
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Stok saat ini: <span id="adj_stock" style="color:#2563a8"></span></label>
      </div>
      <div style="margin-bottom:14px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Stok Baru <span id="adj_unit" style="color:#888"></span></label>
        <input type="number" name="new_stock" id="adj_new" step="0.001" min="0" required
               style="width:100%;padding:10px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:15px">
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px">Alasan Penyesuaian</label>
        <input type="text" name="notes" placeholder="cth: stock opname, hilang/rusak..."
               style="width:100%;padding:10px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:14px">
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-warning" style="flex:1">🔧 Sesuaikan</button>
        <button type="button" onclick="closeModal('modalAdjust')" class="btn" style="flex:1">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Tabel Bahan Baku -->
<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Nama Bahan</th>
        <th>Satuan</th>
        <th style="text-align:right">Stok</th>
        <th style="text-align:right">Stok Min</th>
        <th style="text-align:right">Harga/Unit</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($ingredients)): ?>
      <tr><td colspan="7" style="text-align:center;padding:40px;color:#aaa">
        Belum ada bahan baku. <a href="<?= BASE_URL ?>/inventory/create">Tambah sekarang →</a>
      </td></tr>
    <?php endif; ?>
    <?php foreach ($ingredients as $ing): ?>
      <?php
        $isLow    = $ing['stock_min'] > 0 && $ing['stock'] <= $ing['stock_min'];
        $isEmpty  = $ing['stock'] <= 0;
        $rowStyle = $isEmpty ? 'background:#fff8f8' : ($isLow ? 'background:#fffbf0' : '');
      ?>
      <tr style="<?= $rowStyle ?>">
        <td>
          <strong><?= htmlspecialchars($ing['name']) ?></strong>
          <?php if ($ing['notes']): ?>
            <div style="font-size:11px;color:#aaa;margin-top:2px"><?= htmlspecialchars($ing['notes']) ?></div>
          <?php endif; ?>
        </td>
        <td><span style="background:#f0f0f0;padding:2px 8px;border-radius:100px;font-size:12px"><?= htmlspecialchars($ing['unit']) ?></span></td>
        <td style="text-align:right;font-weight:700;<?= $isEmpty ? 'color:#dc2626' : ($isLow ? 'color:#d97706' : 'color:#16a34a') ?>">
          <?= rtrim(rtrim(number_format((float)$ing['stock'], 3), '0'), '.') ?>
          <?php if ($isEmpty): ?><span style="font-size:10px;background:#dc2626;color:#fff;padding:1px 6px;border-radius:100px;margin-left:4px;font-weight:600">HABIS</span>
          <?php elseif ($isLow): ?><span style="font-size:10px;background:#f59e0b;color:#fff;padding:1px 6px;border-radius:100px;margin-left:4px;font-weight:600">MENIPIS</span>
          <?php endif; ?>
        </td>
        <td style="text-align:right;color:#888;font-size:13px"><?= $ing['stock_min'] > 0 ? rtrim(rtrim(number_format((float)$ing['stock_min'], 3), '0'), '.') : '-' ?></td>
        <td style="text-align:right;font-size:13px">
          <?= $ing['cost_per_unit'] > 0 ? 'Rp ' . number_format($ing['cost_per_unit'], 0, ',', '.') : '-' ?>
        </td>
        <td>
          <?php if ($isEmpty): ?>
            <span class="badge badge-batal">Habis</span>
          <?php elseif ($isLow): ?>
            <span class="badge badge-pending" style="background:#fef3c7;color:#92400e">Menipis</span>
          <?php else: ?>
            <span class="badge badge-selesai">Aman</span>
          <?php endif; ?>
        </td>
        <td style="white-space:nowrap">
          <button onclick="openStockIn(<?= $ing['id'] ?>, '<?= addslashes($ing['name']) ?>', '<?= $ing['unit'] ?>', '<?= $ing['stock'] ?>')"
                  class="btn btn-sm btn-primary" title="Tambah stok masuk">📥 Masuk</button>
          <button onclick="openAdjust(<?= $ing['id'] ?>, '<?= addslashes($ing['name']) ?>', '<?= $ing['unit'] ?>', '<?= $ing['stock'] ?>')"
                  class="btn btn-sm" title="Sesuaikan stok">🔧</button>
          <a href="<?= BASE_URL ?>/inventory/edit/<?= $ing['id'] ?>" class="btn btn-sm">Edit</a>
          <form method="POST" action="<?= BASE_URL ?>/inventory/delete/<?= $ing['id'] ?>" style="display:inline"
                onsubmit="return confirm('Hapus bahan ini? Semua resep yang menggunakan bahan ini akan ikut terhapus.')">
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
function openStockIn(id, name, unit, stock) {
  document.getElementById('si_id').value    = id;
  document.getElementById('si_name').textContent  = name;
  document.getElementById('si_unit').textContent  = '(' + unit + ')';
  document.getElementById('si_stock').textContent = stock + ' ' + unit;
  const m = document.getElementById('modalStockIn');
  m.style.display = 'flex';
}
function openAdjust(id, name, unit, stock) {
  document.getElementById('adj_name').textContent  = name;
  document.getElementById('adj_unit').textContent  = '(' + unit + ')';
  document.getElementById('adj_stock').textContent = stock + ' ' + unit;
  document.getElementById('adj_new').value = stock;
  document.getElementById('adjustForm').action = '<?= BASE_URL ?>/inventory/adjust/' + id;
  const m = document.getElementById('modalAdjust');
  m.style.display = 'flex';
}
function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}
// close on backdrop click
['modalStockIn','modalAdjust'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) closeModal(id);
  });
});
</script>
