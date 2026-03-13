<div class="page-header">
    <a href="<?= BASE_URL ?>/orders/create" class="btn btn-primary">+ Buat Pesanan</a>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button class="btn btn-sm no-print" onclick="window.print()">🖨️ Print</button>
        <a href="<?= BASE_URL ?>/orders/export/csv" class="btn btn-sm no-print">📋 CSV</a>
        <a href="<?= BASE_URL ?>/orders/export/excel" class="btn btn-sm no-print">📊 Excel</a>
    </div>
</div>

<div class="filter-box no-print">
    <form method="GET" action="<?= BASE_URL ?>/orders">
        <div class="filter-grid">
            <div class="form-group" style="margin:0">
                <label>Cari Pelanggan</label>
                <input type="text" name="keyword" placeholder="Nama atau No. HP" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>Status</label>
                <select name="status">
                    <option value="">— Semua —</option>
                    <?php foreach (['pending','proses','selesai','batal'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Dari Tanggal</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin:0">
                <label>Sampai Tanggal</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end">
                <button type="submit" class="btn btn-primary" style="flex:1">Filter</button>
                <?php if ($filters['keyword'] || $filters['status'] || $filters['date_from'] || $filters['date_to']): ?>
                <a href="<?= BASE_URL ?>/orders" class="btn" style="flex:1;text-align:center">Reset</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="section" style="padding:0;overflow:hidden">
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>#</th><th>Pelanggan</th><th>No. HP</th><th>Items</th><th>Total</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php if (empty($orders)): ?>
            <tr><td colspan="8" style="text-align:center;padding:24px;color:var(--muted)">Tidak ada pesanan yang sesuai filter.</td></tr>
            <?php else: ?>
                <?php $no = 1; foreach ($orders as $o): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($o['customer_name']) ?></strong></td>
                    <td><?= $o['customer_phone'] ?? '-' ?></td>
                    <td><?= $o['total_items'] ?> item</td>
                    <td>Rp <?= number_format($o['total'], 0, ',', '.') ?></td>
                    <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                    <td style="white-space:nowrap"><?= date('d/m/y H:i', strtotime($o['created_at'])) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/orders/<?= $o['id'] ?>" class="btn btn-sm">Detail</a>
                        <a href="<?= BASE_URL ?>/orders/print-receipt/<?= $o['id'] ?>" class="btn btn-sm" target="_blank"
                           style="background:#1a1a1a;color:white;margin-left:4px;font-size:12px">
                            Struk
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
