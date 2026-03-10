<?php
// app/Views/reports/ingredients.php
$totalUsedCost  = array_sum(array_column($usageData, 'total_cost'));
$totalIn        = array_sum(array_column($usageData, 'total_in'));
$hpp            = (float)($hppSummary['total_hpp'] ?? 0);
$revenue        = (float)($hppSummary['total_revenue'] ?? 0);
$grossProfit    = $revenue - $hpp;
$margin         = $revenue > 0 ? round($grossProfit / $revenue * 100, 1) : 0;
?>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px">
  <div>
    <h2 style="margin:0;font-size:20px;font-weight:700">📊 Laporan Bahan & HPP</h2>
    <p style="margin:4px 0 0;color:#888;font-size:13px">Penggunaan bahan baku & analisis harga pokok produksi</p>
  </div>
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="month" name="month" value="<?= htmlspecialchars($month) ?>"
             style="padding:8px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:14px">
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
    <a href="<?= BASE_URL ?>/inventory" class="btn btn-sm">📦 Kelola Stok</a>
    <a href="<?= BASE_URL ?>/inventory/logs" class="btn btn-sm">📋 Stock Log</a>
  </div>
</div>

<!-- ── Summary Cards ── -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:28px">
  <div style="background:#fff;border:1px solid #e8e6e0;border-radius:14px;padding:20px">
    <div style="font-size:24px;margin-bottom:8px">📦</div>
    <div style="font-size:12px;color:#888;margin-bottom:4px">Total Pesanan Selesai</div>
    <div style="font-size:24px;font-weight:800;color:#0f0e0c"><?= number_format($hppSummary['total_orders'] ?? 0) ?></div>
    <div style="font-size:11px;color:#aaa;margin-top:2px">bulan <?= date('F Y', strtotime($month.'-01')) ?></div>
  </div>
  <div style="background:#fff;border:1px solid #e8e6e0;border-radius:14px;padding:20px">
    <div style="font-size:24px;margin-bottom:8px">💸</div>
    <div style="font-size:12px;color:#888;margin-bottom:4px">Total HPP</div>
    <div style="font-size:22px;font-weight:800;color:#dc2626">Rp <?= number_format($hpp, 0, ',', '.') ?></div>
    <div style="font-size:11px;color:#aaa;margin-top:2px">biaya pokok produksi</div>
  </div>
  <div style="background:#fff;border:1px solid #e8e6e0;border-radius:14px;padding:20px">
    <div style="font-size:24px;margin-bottom:8px">💰</div>
    <div style="font-size:12px;color:#888;margin-bottom:4px">Total Pendapatan</div>
    <div style="font-size:22px;font-weight:800;color:#2563a8">Rp <?= number_format($revenue, 0, ',', '.') ?></div>
    <div style="font-size:11px;color:#aaa;margin-top:2px">dari pesanan selesai</div>
  </div>
  <div style="background:<?= $margin >= 30 ? '#f0fdf4' : ($margin >= 15 ? '#fffbeb' : '#fff1f2') ?>;border:1px solid <?= $margin >= 30 ? '#bbf7d0' : ($margin >= 15 ? '#fde68a' : '#fecdd3') ?>;border-radius:14px;padding:20px">
    <div style="font-size:24px;margin-bottom:8px"><?= $margin >= 30 ? '🟢' : ($margin >= 15 ? '🟡' : '🔴') ?></div>
    <div style="font-size:12px;color:#888;margin-bottom:4px">Gross Margin</div>
    <div style="font-size:24px;font-weight:800;color:<?= $margin >= 30 ? '#16a34a' : ($margin >= 15 ? '#d97706' : '#dc2626') ?>"><?= $margin ?>%</div>
    <div style="font-size:11px;color:#aaa;margin-top:2px">Rp <?= number_format($grossProfit, 0, ',', '.') ?> laba kotor</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px">

  <!-- ── Tabel Penggunaan Bahan ── -->
  <div style="background:#fff;border:1px solid #e8e6e0;border-radius:14px;overflow:hidden">
    <div style="padding:18px 20px;border-bottom:1px solid #f0ede8;display:flex;align-items:center;justify-content:space-between">
      <h3 style="margin:0;font-size:15px;font-weight:700">🧪 Penggunaan Bahan Baku</h3>
      <span style="font-size:12px;color:#888"><?= date('F Y', strtotime($month.'-01')) ?></span>
    </div>
    <div style="overflow-x:auto">
      <table class="table" style="margin:0">
        <thead>
          <tr>
            <th>Bahan</th>
            <th style="text-align:right">Dipakai</th>
            <th style="text-align:right">Stok Kini</th>
            <th style="text-align:right">Biaya</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($usageData)): ?>
          <tr><td colspan="5" style="text-align:center;padding:30px;color:#aaa">Belum ada data</td></tr>
        <?php endif; ?>
        <?php foreach ($usageData as $row): ?>
          <?php
            $isEmpty = (float)$row['stock'] <= 0;
            $isLow   = (float)$row['stock_min'] > 0 && (float)$row['stock'] <= (float)$row['stock_min'];
          ?>
          <tr>
            <td>
              <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($row['name']) ?></div>
              <?php if ($row['cost_per_unit'] > 0): ?>
              <div style="font-size:11px;color:#aaa">Rp <?= number_format($row['cost_per_unit'],0,',','.') ?>/<?= $row['unit'] ?></div>
              <?php endif; ?>
            </td>
            <td style="text-align:right;font-size:13px">
              <?php if ($row['total_used'] > 0): ?>
                <span style="font-weight:700;color:#dc2626">-<?= rtrim(rtrim(number_format((float)$row['total_used'],3),'0'),'.') ?></span>
                <span style="color:#aaa;font-size:11px"> <?= $row['unit'] ?></span>
              <?php else: ?>
                <span style="color:#aaa">—</span>
              <?php endif; ?>
              <?php if ($row['total_in'] > 0): ?>
                <div style="font-size:11px;color:#16a34a">+<?= rtrim(rtrim(number_format((float)$row['total_in'],3),'0'),'.') ?> masuk</div>
              <?php endif; ?>
            </td>
            <td style="text-align:right;font-size:13px;font-weight:700;color:<?= $isEmpty ? '#dc2626' : ($isLow ? '#d97706' : '#16a34a') ?>">
              <?= rtrim(rtrim(number_format((float)$row['stock'],3),'0'),'.') ?>
              <span style="font-size:10px;font-weight:400;color:#aaa"> <?= $row['unit'] ?></span>
            </td>
            <td style="text-align:right;font-size:12px;color:#555">
              <?= $row['total_cost'] > 0 ? 'Rp '.number_format($row['total_cost'],0,',','.') : '—' ?>
            </td>
            <td>
              <?php if ($isEmpty): ?>
                <span class="badge badge-batal" style="font-size:10px">Habis</span>
              <?php elseif ($isLow): ?>
                <span class="badge badge-pending" style="background:#fef3c7;color:#92400e;font-size:10px">Menipis</span>
              <?php else: ?>
                <span class="badge badge-selesai" style="font-size:10px">Aman</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <?php if ($totalUsedCost > 0): ?>
        <tfoot>
          <tr style="background:#fafafa;font-weight:700">
            <td colspan="3" style="text-align:right;font-size:13px;color:#888">Total biaya bahan</td>
            <td style="text-align:right;color:#dc2626">Rp <?= number_format($totalUsedCost,0,',','.') ?></td>
            <td></td>
          </tr>
        </tfoot>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <!-- ── HPP per Produk ── -->
  <div style="background:#fff;border:1px solid #e8e6e0;border-radius:14px;overflow:hidden">
    <div style="padding:18px 20px;border-bottom:1px solid #f0ede8;display:flex;align-items:center;justify-content:space-between">
      <h3 style="margin:0;font-size:15px;font-weight:700">💹 HPP & Margin per Produk</h3>
      <span style="font-size:12px;color:#888">Terjual bulan ini</span>
    </div>
    <div style="overflow-x:auto">
      <table class="table" style="margin:0">
        <thead>
          <tr>
            <th>Produk</th>
            <th style="text-align:right">HPP</th>
            <th style="text-align:right">Harga Jual</th>
            <th style="text-align:right">Terjual</th>
            <th style="text-align:right">Margin</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($productHpp)): ?>
          <tr><td colspan="5" style="text-align:center;padding:30px;color:#aaa">Belum ada data</td></tr>
        <?php endif; ?>
        <?php foreach ($productHpp as $p): ?>
          <?php
            $hpp_val  = (float)$p['hpp'];
            $price    = (float)$p['price'];
            $mgn      = $price > 0 ? round(($price - $hpp_val) / $price * 100, 1) : 0;
            $mgnColor = $mgn >= 40 ? '#16a34a' : ($mgn >= 20 ? '#d97706' : '#dc2626');
            $sold     = (int)$p['total_sold'];
          ?>
          <tr>
            <td>
              <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($p['name']) ?></div>
              <div style="font-size:10px;margin-top:2px">
                <?php if ($p['hpp_type'] === 'auto'): ?>
                  <span style="background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:100px;font-weight:600">⚗️ Dari resep</span>
                <?php else: ?>
                  <span style="background:#f3f4f6;color:#555;padding:1px 6px;border-radius:100px;font-weight:600">✏️ Manual</span>
                <?php endif; ?>
              </div>
            </td>
            <td style="text-align:right;font-size:13px">
              <?= $hpp_val > 0 ? 'Rp '.number_format($hpp_val,0,',','.') : '<span style="color:#aaa">—</span>' ?>
            </td>
            <td style="text-align:right;font-size:13px;font-weight:600">
              Rp <?= number_format($price,0,',','.') ?>
            </td>
            <td style="text-align:right;font-size:13px;color:#888">
              <?= $sold > 0 ? $sold . ' pcs' : '—' ?>
            </td>
            <td style="text-align:right">
              <?php if ($hpp_val > 0 && $price > 0): ?>
                <span style="font-weight:700;color:<?= $mgnColor ?>;font-size:13px"><?= $mgn ?>%</span>
                <div style="font-size:10px;color:#aaa">Rp <?= number_format($price - $hpp_val, 0, ',', '.') ?></div>
              <?php else: ?>
                <span style="color:#aaa;font-size:12px">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Keterangan margin -->
    <div style="padding:14px 20px;border-top:1px solid #f0ede8;display:flex;gap:14px;flex-wrap:wrap">
      <div style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="color:#16a34a;font-weight:700">●</span> ≥40% Margin bagus</div>
      <div style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="color:#d97706;font-weight:700">●</span> 20–40% Perlu dioptimasi</div>
      <div style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="color:#dc2626;font-weight:700">●</span> &lt;20% Terlalu tipis</div>
    </div>
  </div>

</div>

<!-- ── Tips HPP ── -->
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:20px">
  <h3 style="margin:0 0 12px;font-size:14px;font-weight:700;color:#1d4ed8">💡 Tips Optimasi HPP</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
    <div style="font-size:13px;color:#1e40af">
      <strong>⚗️ HPP dari Resep (Auto)</strong><br>
      <span style="color:#3b82f6">Atur resep bahan di setiap produk agar HPP dihitung otomatis & akurat. Stok bahan akan berkurang saat pesanan selesai.</span>
    </div>
    <div style="font-size:13px;color:#1e40af">
      <strong>✏️ HPP Manual</strong><br>
      <span style="color:#3b82f6">Cocok untuk produk sederhana atau niche non-F&B. Input langsung nilai HPP per produk.</span>
    </div>
    <div style="font-size:13px;color:#1e40af">
      <strong>📦 Alert Stok Minimum</strong><br>
      <span style="color:#3b82f6">Set stok minimum setiap bahan agar mendapat notifikasi otomatis saat stok menipis.</span>
    </div>
  </div>
  <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>/products" class="btn btn-sm btn-primary">⚗️ Atur Resep Produk</a>
    <a href="<?= BASE_URL ?>/inventory" class="btn btn-sm">📦 Kelola Stok Bahan</a>
    <a href="<?= BASE_URL ?>/inventory/logs" class="btn btn-sm">📋 Riwayat Stok</a>
  </div>
</div>
