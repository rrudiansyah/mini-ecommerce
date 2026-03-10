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
            <thead><tr><th>No</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php $no = 1; foreach ($recentOrders as $order): ?>
            <tr>
                <td><?= $no++ ?></td>
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
