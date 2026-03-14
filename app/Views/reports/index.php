<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.stat-card h3 {
    margin: 0;
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
}
.stat-card .value {
    font-size: 28px;
    font-weight: bold;
    color: #333;
}
.section-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
    margin-top: 30px;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 10px;
}
@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .stats-grid { grid-template-columns: 1fr; }
}
</style>

<!-- Stats Card -->
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Pesanan</h3>
        <div class="value"><?= $stats['total_orders'] ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Revenue</h3>
        <div class="value">Rp <?= number_format($stats['total_revenue'], 0, ',', '.') ?></div>
    </div>
    <div class="stat-card">
        <h3>Pesanan Pending</h3>
        <div class="value"><?= $stats['pending_orders'] ?></div>
    </div>
    <div class="stat-card">
        <h3>Revenue Hari Ini</h3>
        <div class="value">Rp <?= number_format($stats['today_revenue'], 0, ',', '.') ?></div>
    </div>
</div>

<!-- Sales by Category -->
<div class="section-title">Penjualan Per Kategori</div>
<table class="table">
    <thead>
        <tr>
            <th>Kategori</th>
            <th>Total Pesanan</th>
            <th>Total Item</th>
            <th>Revenue</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($categoryStats)): ?>
        <tr><td colspan="4" style="text-align:center; padding:20px">Belum ada data penjualan.</td></tr>
        <?php else: ?>
            <?php foreach ($categoryStats as $cat): ?>
            <tr>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td><?= $cat['total_orders'] ?></td>
                <td><?= $cat['total_items'] ?></td>
                <td>Rp <?= number_format($cat['total_revenue'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Top Products -->
<div class="section-title">Produk Terlaris</div>
<table class="table">
    <thead>
        <tr>
            <th>Produk</th>
            <th>Kategori</th>
            <th>Total Terjual</th>
            <th>Total Pesanan</th>
            <th>Revenue</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($topProducts)): ?>
        <tr><td colspan="5" style="text-align:center; padding:20px">Belum ada data penjualan.</td></tr>
        <?php else: ?>
            <?php foreach ($topProducts as $prod): ?>
            <tr>
                <td><?= htmlspecialchars($prod['name']) ?></td>
                <td><?= htmlspecialchars($prod['category'] ?? '-') ?></td>
                <td><?= $prod['total_qty'] ?></td>
                <td><?= $prod['total_orders'] ?></td>
                <td>Rp <?= number_format($prod['total_revenue'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>/reports/sales" class="btn btn-primary">📈 Laporan Penjualan Detail →</a>
    <a href="<?= BASE_URL ?>/reports/ingredients" class="btn" style="background:#fff8f0;border:1.5px solid #fed7aa;color:#92400e;font-weight:700">
        📊 Laporan Bahan & HPP →
    </a>
</div>

<?php if (($menuPerms['variants'] ?? false)): ?>
<a href="<?= BASE_URL ?>/reports/variants" style="display:block;background:#fdf4ff;border:1.5px solid #e879f9;border-radius:12px;padding:20px;text-decoration:none;transition:transform .2s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
    <div style="font-size:32px;margin-bottom:8px">🎨</div>
    <div style="font-weight:700;font-size:15px;color:#1f2937">Stok Varian</div>
    <div style="font-size:13px;color:#9ca3af;margin-top:4px">Pantau stok per ukuran/warna</div>
</a>
<?php endif; ?>