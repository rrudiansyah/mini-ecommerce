<?php
// app/Views/inventory/logs.php
?>
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
  <div>
    <a href="<?= BASE_URL ?>/inventory" style="color:#888;font-size:13px;text-decoration:none">← Kembali ke Stok</a>
    <h2 style="margin:6px 0 0;font-size:20px;font-weight:700">📋 Riwayat Keluar-Masuk Stok</h2>
  </div>
</div>

<!-- Filter -->
<div style="background:#fff;border-radius:12px;border:1px solid #e8e6e0;padding:16px 20px;margin-bottom:20px">
  <form method="GET" action="" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <div>
      <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;color:#888">Bahan</label>
      <select name="ingredient_id" style="padding:8px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:13px;min-width:180px;background:#fff">
        <option value="">Semua Bahan</option>
        <?php foreach ($ingredients as $ing): ?>
          <option value="<?= $ing['id'] ?>" <?= $filterIng == $ing['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($ing['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;color:#888">Tipe</label>
      <select name="type" style="padding:8px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:13px;background:#fff">
        <option value="">Semua</option>
        <option value="in"          <?= $filterType === 'in'          ? 'selected' : '' ?>>📥 Masuk</option>
        <option value="out"         <?= $filterType === 'out'         ? 'selected' : '' ?>>📤 Keluar (Pesanan)</option>
        <option value="adjustment"  <?= $filterType === 'adjustment'  ? 'selected' : '' ?>>🔧 Penyesuaian</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="<?= BASE_URL ?>/inventory/logs" class="btn btn-sm">Reset</a>
  </form>
</div>

<div class="table-wrap">
  <table class="table">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Bahan</th>
        <th>Tipe</th>
        <th style="text-align:right">Jumlah</th>
        <th style="text-align:right">Sebelum</th>
        <th style="text-align:right">Sesudah</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($logs)): ?>
      <tr><td colspan="7" style="text-align:center;padding:40px;color:#aaa">Belum ada riwayat stok.</td></tr>
    <?php endif; ?>
    <?php foreach ($logs as $log): ?>
      <tr>
        <td style="font-size:12px;color:#888;white-space:nowrap"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
        <td><strong><?= htmlspecialchars($log['ingredient_name']) ?></strong></td>
        <td>
          <?php if ($log['type'] === 'in'): ?>
            <span class="badge badge-selesai">📥 Masuk</span>
          <?php elseif ($log['type'] === 'out'): ?>
            <span class="badge badge-batal">📤 Pesanan</span>
          <?php else: ?>
            <span class="badge badge-pending" style="background:#ede9fe;color:#5b21b6">🔧 Penyesuaian</span>
          <?php endif; ?>
        </td>
        <td style="text-align:right;font-weight:700;<?= $log['type'] === 'in' ? 'color:#16a34a' : 'color:#dc2626' ?>">
          <?= $log['type'] === 'in' ? '+' : '-' ?><?= rtrim(rtrim(number_format((float)$log['qty'], 3), '0'), '.') ?>
          <?= htmlspecialchars($log['unit']) ?>
        </td>
        <td style="text-align:right;font-size:13px;color:#888"><?= rtrim(rtrim(number_format((float)$log['stock_before'], 3), '0'), '.') ?></td>
        <td style="text-align:right;font-size:13px;font-weight:600"><?= rtrim(rtrim(number_format((float)$log['stock_after'], 3), '0'), '.') ?></td>
        <td style="font-size:12px;color:#666">
          <?= htmlspecialchars($log['notes'] ?? '-') ?>
          <?php if ($log['order_id']): ?>
            <a href="<?= BASE_URL ?>/orders/<?= $log['order_id'] ?>" style="color:#2563a8;font-size:11px;margin-left:4px">#<?= $log['order_id'] ?></a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
