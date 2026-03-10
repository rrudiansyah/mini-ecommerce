<?php
// app/Views/inventory/form.php
$isEdit = !empty($ingredient);
$units  = ['gr','ml','pcs','kg','liter','sdm','sdt','lembar','buah','botol','sachet','porsi'];
?>
<div style="max-width:560px">
  <div style="margin-bottom:24px">
    <a href="<?= BASE_URL ?>/inventory" style="color:#888;font-size:14px;text-decoration:none">← Kembali ke Stok</a>
    <h2 style="margin:8px 0 0;font-size:20px;font-weight:700"><?= $isEdit ? '✏️ Edit Bahan Baku' : '➕ Tambah Bahan Baku' ?></h2>
  </div>

  <div style="background:#fff;border-radius:14px;border:1px solid #e8e6e0;padding:28px">
    <form method="POST" action="<?= BASE_URL ?>/inventory/<?= $isEdit ? 'update/'.$ingredient['id'] : 'store' ?>">
      <?php echo $csrf_field ?? ''; ?>

      <div style="margin-bottom:18px">
        <label style="font-size:13px;font-weight:700;display:block;margin-bottom:7px">Nama Bahan <span style="color:#dc2626">*</span></label>
        <input type="text" name="name" required autofocus
               value="<?= htmlspecialchars($ingredient['name'] ?? '') ?>"
               placeholder="cth: Biji Kopi Arabica, Susu Cair, Es Batu..."
               style="width:100%;padding:10px 14px;border:1.5px solid #ddd;border-radius:10px;font-size:14px">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px">
        <div>
          <label style="font-size:13px;font-weight:700;display:block;margin-bottom:7px">Satuan <span style="color:#dc2626">*</span></label>
          <select name="unit" style="width:100%;padding:10px 14px;border:1.5px solid #ddd;border-radius:10px;font-size:14px;background:#fff">
            <?php foreach ($units as $u): ?>
              <option value="<?= $u ?>" <?= ($ingredient['unit'] ?? 'pcs') === $u ? 'selected' : '' ?>><?= $u ?></option>
            <?php endforeach; ?>
          </select>
          <div style="font-size:11px;color:#aaa;margin-top:4px">gr=gram, ml=mililiter, pcs=buah</div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:700;display:block;margin-bottom:7px">Harga Beli per Unit (Rp)</label>
          <input type="number" name="cost_per_unit" min="0" step="1"
                 value="<?= $ingredient['cost_per_unit'] ?? 0 ?>"
                 placeholder="0"
                 style="width:100%;padding:10px 14px;border:1.5px solid #ddd;border-radius:10px;font-size:14px">
          <div style="font-size:11px;color:#aaa;margin-top:4px">Dipakai untuk hitung HPP otomatis</div>
        </div>
      </div>

      <?php if (!$isEdit): ?>
      <div style="margin-bottom:18px">
        <label style="font-size:13px;font-weight:700;display:block;margin-bottom:7px">Stok Awal</label>
        <input type="number" name="stock" min="0" step="0.001" value="0"
               style="width:100%;padding:10px 14px;border:1.5px solid #ddd;border-radius:10px;font-size:14px">
      </div>
      <?php endif; ?>

      <div style="margin-bottom:18px">
        <label style="font-size:13px;font-weight:700;display:block;margin-bottom:7px">Alert Stok Minimum</label>
        <input type="number" name="stock_min" min="0" step="0.001"
               value="<?= $ingredient['stock_min'] ?? 0 ?>"
               placeholder="0 = tidak ada alert"
               style="width:100%;padding:10px 14px;border:1.5px solid #ddd;border-radius:10px;font-size:14px">
        <div style="font-size:11px;color:#aaa;margin-top:4px">Notifikasi muncul saat stok ≤ nilai ini. Isi 0 untuk menonaktifkan.</div>
      </div>

      <div style="margin-bottom:24px">
        <label style="font-size:13px;font-weight:700;display:block;margin-bottom:7px">Catatan</label>
        <textarea name="notes" rows="2" placeholder="Opsional..."
                  style="width:100%;padding:10px 14px;border:1.5px solid #ddd;border-radius:10px;font-size:14px;resize:vertical"><?= htmlspecialchars($ingredient['notes'] ?? '') ?></textarea>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" style="flex:1;padding:12px">
          <?= $isEdit ? '💾 Simpan Perubahan' : '➕ Tambah Bahan' ?>
        </button>
        <a href="<?= BASE_URL ?>/inventory" class="btn" style="flex:1;padding:12px;text-align:center">Batal</a>
      </div>
    </form>
  </div>

  <?php if ($isEdit): ?>
  <!-- Info: stok hanya bisa diubah via stock-in atau adjustment -->
  <div style="margin-top:16px;background:#f0f7ff;border:1px solid #bcd;border-radius:10px;padding:14px;font-size:13px;color:#2563a8">
    💡 Untuk mengubah jumlah stok, gunakan tombol <strong>📥 Masuk</strong> atau <strong>🔧 Penyesuaian</strong> di halaman daftar bahan.
  </div>
  <?php endif; ?>
</div>
