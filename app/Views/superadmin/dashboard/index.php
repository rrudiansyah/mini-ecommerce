<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🏪</div>
        <div class="stat-label">Total Toko</div>
        <div class="stat-value"><?= $totalStores ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Toko Aktif</div>
        <div class="stat-value"><?= $activeStores ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🛒</div>
        <div class="stat-label">Total Pesanan</div>
        <div class="stat-value"><?= number_format($totalOrders) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Total Omzet</div>
        <div class="stat-value" style="font-size:16px">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></div>
    </div>
</div>

<div class="section">
    <div class="page-header" style="margin-bottom:16px">
        <h2 style="margin:0">Semua Toko</h2>
        <a href="<?= BASE_URL ?>/superadmin/stores/create" class="btn btn-primary">+ Tambah Toko</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Toko</th><th>Niche</th><th>URL Publik</th><th>Produk</th><th>Pesanan</th><th>Omzet</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($stores as $s): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($s['name']) ?></strong><br>
                    <small style="color:var(--muted)"><?= htmlspecialchars($s['address'] ?? '-') ?></small>
                </td>
                <td><span class="badge badge-proses"><?= $s['niche'] ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/toko/<?= $s['slug'] ?>" target="_blank"
                       style="color:var(--sky);font-size:12px;font-family:monospace">
                        /toko/<?= $s['slug'] ?> ↗
                    </a>
                </td>
                <td><?= $s['product_count'] ?></td>
                <td><?= $s['order_count'] ?></td>
                <td style="white-space:nowrap">Rp <?= number_format($s['total_revenue'], 0, ',', '.') ?></td>
                <td><span class="badge badge-<?= $s['is_active'] ? 'selesai' : 'batal' ?>"><?= $s['is_active'] ? 'Aktif' : 'Off' ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/superadmin/stores/toggle/<?= $s['id'] ?>" class="btn btn-sm"
                       onclick="return confirm('<?= $s['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> toko ini?')">
                        <?= $s['is_active'] ? '⏸ Off' : '▶ On' ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
