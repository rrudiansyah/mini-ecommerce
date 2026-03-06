<div class="page-header" style="display:flex; justify-content:space-between; align-items:center">
    <div>
        <a href="<?= BASE_URL ?>/orders/create" class="btn btn-primary">+ Buat Pesanan</a>
    </div>
    <div>
        <button class="btn btn-sm" onclick="window.print()">🖨️ Print</button>
        <a href="<?= BASE_URL ?>/orders/export/csv" class="btn btn-sm">📋 Export CSV</a>
        <a href="<?= BASE_URL ?>/orders/export/excel" class="btn btn-sm">📊 Export Excel</a>
    </div>
</div>

<!-- Filter Form -->
<div style="background:#f5f5f5; padding:15px; border-radius:6px; margin-bottom:20px">
    <form method="GET" action="<?= BASE_URL ?>/orders" style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:10px; align-items:flex-end">
        <div>
            <label style="font-size:13px; display:block; margin-bottom:5px"><strong>Cari Pelanggan</strong></label>
            <input type="text" name="keyword" placeholder="Nama atau No. HP" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
        </div>
        <div>
            <label style="font-size:13px; display:block; margin-bottom:5px"><strong>Status</strong></label>
            <select name="status">
                <option value="">— Semua Status —</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="proses" <?= ($filters['status'] ?? '') === 'proses' ? 'selected' : '' ?>>Proses</option>
                <option value="selesai" <?= ($filters['status'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="batal" <?= ($filters['status'] ?? '') === 'batal' ? 'selected' : '' ?>>Batal</option>
            </select>
        </div>
        <div>
            <label style="font-size:13px; display:block; margin-bottom:5px"><strong>Dari Tanggal</strong></label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
        </div>
        <div>
            <label style="font-size:13px; display:block; margin-bottom:5px"><strong>Sampai Tanggal</strong></label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="width:100%">Filter</button>
            <?php if ($filters['keyword'] || $filters['status'] || $filters['date_from'] || $filters['date_to']): ?>
            <a href="<?= BASE_URL ?>/orders" class="btn" style="margin-top:5px; width:100%; text-align:center">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<table class="table">
    <thead><tr><th>#</th><th>Pelanggan</th><th>No. HP</th><th>Items</th><th>Total</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php if (empty($orders)): ?>
    <tr><td colspan="8" style="text-align:center; padding:20px">Tidak ada pesanan yang sesuai filter.</td></tr>
    <?php else: ?>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= $o['customer_phone'] ?? '-' ?></td>
            <td><?= $o['total_items'] ?> item</td>
            <td>Rp <?= number_format($o['total'], 0, ',', '.') ?></td>
            <td><span class="badge badge-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
            <td><?= date('d/m/y H:i', strtotime($o['created_at'])) ?></td>
            <td><a href="<?= BASE_URL ?>/orders/<?= $o['id'] ?>" class="btn btn-sm">Detail</a></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<style>
@media print {
    .page-header { display:none; }
    .btn, a.btn { display:none; }
    body { background:white; }
    [style*="background:#f5f5f5"] { display:none; }
}
input, select { width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; }
</style>