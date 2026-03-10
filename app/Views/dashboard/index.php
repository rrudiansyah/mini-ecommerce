<?php if (!empty($lowStock)): ?>
<div style="background:#fff8f0;border:1.5px solid #fed7aa;border-radius:14px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:flex-start;gap:14px">
  <span style="font-size:24px;flex-shrink:0">⚠️</span>
  <div style="flex:1">
    <div style="font-weight:700;font-size:14px;color:#92400e;margin-bottom:6px">
      <?= count($lowStock) ?> bahan stoknya menipis atau habis!
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:6px">
      <?php foreach ($lowStock as $b): ?>
        <?php $isEmpty = (float)$b['stock'] <= 0; ?>
        <span style="background:<?= $isEmpty ? '#fee2e2' : '#fef3c7' ?>;color:<?= $isEmpty ? '#b91c1c' : '#92400e' ?>;
                     border:1px solid <?= $isEmpty ? '#fca5a5' : '#fde68a' ?>;
                     border-radius:100px;padding:3px 12px;font-size:12px;font-weight:700">
          <?= htmlspecialchars($b['name']) ?>
          <?php if ($isEmpty): ?> — HABIS<?php else: ?>
            (<?= rtrim(rtrim(number_format((float)$b['stock'],3),'0'),'.') ?> <?= $b['unit'] ?>)
          <?php endif; ?>
        </span>
      <?php endforeach; ?>
    </div>
  </div>
  <a href="<?= BASE_URL ?>/inventory" style="background:#f97316;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;white-space:nowrap;text-decoration:none">
    📦 Isi Stok →
  </a>
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🛒</div>
        <div class="stat-label">Total Pesanan</div>
        <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-label">Pesanan Pending</div>
        <div class="stat-value"><?= number_format($stats['pending_orders']) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Pendapatan Hari Ini</div>
        <div class="stat-value">Rp <?= number_format($stats['today_revenue'], 0, ',', '.') ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">Rp <?= number_format($stats['total_revenue'], 0, ',', '.') ?></div>
    </div>
</div>

<div class="section">
    <h2>Pesanan Terbaru</h2>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>#</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                <td><span class="badge badge-<?= $order['status'] ?>"><?= $order['status'] ?></span></td>
                <td><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
                <td><a href="<?= BASE_URL ?>/orders/<?= $order['id'] ?>" class="btn btn-sm">Lihat</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
