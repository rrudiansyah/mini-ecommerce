<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <div>
        <h2 style="margin:0;font-size:20px;font-weight:700">📋 Riwayat Stok Produk</h2>
        <p style="margin:4px 0 0;color:#888;font-size:13px">Log perubahan stok produk jadi</p>
    </div>
    <a href="<?= BASE_URL ?>/product-stock" class="btn btn-sm"
       style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;padding:8px 16px;font-size:13px;text-decoration:none;color:#374151">
        ← Kembali
    </a>
</div>

<!-- Filter -->
<div style="background:#fff;border:1px solid #e8e6e0;border-radius:12px;padding:16px;margin-bottom:20px">
  <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
    <div>
      <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Produk</label>
      <select name="product_id" style="padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px">
        <option value="">— Semua Produk —</option>
        <?php foreach ($products as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $filterProd == $p['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label style="font-size:12px;font-weight:600;display:block;margin-bottom:4px">Tipe</label>
      <select name="type" style="padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px">
        <option value="">— Semua —</option>
        <option value="in"         <?= $filterType === 'in'         ? 'selected' : '' ?>>Masuk</option>
        <option value="out"        <?= $filterType === 'out'        ? 'selected' : '' ?>>Keluar</option>
        <option value="adjustment" <?= $filterType === 'adjustment' ? 'selected' : '' ?>>Penyesuaian</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary btn-sm" style="padding:8px 18px">Filter</button>
    <a href="<?= BASE_URL ?>/product-stock/logs" style="padding:8px 14px;font-size:13px;color:#6b7280;text-decoration:none">Reset</a>
  </form>
</div>

<!-- Tabel Log -->
<div style="background:#fff;border-radius:14px;border:1px solid #e8e6e0;overflow:hidden">
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead>
      <tr style="background:#f8f7f4;border-bottom:2px solid #e8e6e0">
        <th style="padding:12px 16px;text-align:left">Waktu</th>
        <th style="padding:12px 16px;text-align:left">Produk</th>
        <th style="padding:12px 16px;text-align:center">Tipe</th>
        <th style="padding:12px 16px;text-align:center">Qty</th>
        <th style="padding:12px 16px;text-align:center">Sebelum</th>
        <th style="padding:12px 16px;text-align:center">Sesudah</th>
        <th style="padding:12px 16px;text-align:left">Catatan</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($logs)): ?>
      <tr>
        <td colspan="7" style="padding:40px;text-align:center;color:#9ca3af">Belum ada riwayat stok.</td>
      </tr>
      <?php else: ?>
      <?php foreach ($logs as $log): ?>
      <?php
        $typeColors = [
          'in'         => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Masuk'],
          'out'        => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Keluar'],
          'adjustment' => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Penyesuaian'],
        ];
        $tc = $typeColors[$log['type']] ?? ['bg'=>'#f3f4f6','color'=>'#374151','label'=>$log['type']];
      ?>
      <tr style="border-bottom:1px solid #f0ede8">
        <td style="padding:12px 16px;color:#6b7280;white-space:nowrap">
          <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
        </td>
        <td style="padding:12px 16px;font-weight:600"><?= htmlspecialchars($log['product_name']) ?></td>
        <td style="padding:12px 16px;text-align:center">
          <span style="background:<?= $tc['bg'] ?>;color:<?= $tc['color'] ?>;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600">
            <?= $tc['label'] ?>
          </span>
        </td>
        <td style="padding:12px 16px;text-align:center;font-weight:700;color:<?= $log['type']==='in'?'#16a34a':($log['type']==='out'?'#dc2626':'#d97706') ?>">
          <?= $log['type'] === 'in' ? '+' : ($log['type'] === 'out' ? '-' : '±') ?><?= $log['qty'] ?>
        </td>
        <td style="padding:12px 16px;text-align:center;color:#6b7280"><?= $log['stock_before'] ?></td>
        <td style="padding:12px 16px;text-align:center;font-weight:600"><?= $log['stock_after'] ?></td>
        <td style="padding:12px 16px;color:#6b7280"><?= htmlspecialchars($log['notes'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
