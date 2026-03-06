<div class="page-header" style="justify-content:space-between; align-items:center">
    <p style="color:#64748b; font-size:14px">Total <strong><?= count($stores) ?></strong> toko terdaftar</p>
    <a href="<?= BASE_URL ?>/superadmin/stores/create" class="btn btn-primary">+ Tambah Toko Baru</a>
</div>

<table class="table">
    <thead>
        <tr><th>Toko</th><th>Niche</th><th>URL Publik</th><th>Admin</th><th>Produk</th><th>Pesanan</th><th>Omzet</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php foreach ($stores as $s): ?>
    <tr>
        <td>
            <strong><?= htmlspecialchars($s['name']) ?></strong><br>
            <small style="color:#64748b; font-family:monospace"><?= htmlspecialchars($s['phone'] ?? '-') ?></small>
        </td>
        <td><span class="badge badge-proses"><?= $s['niche'] ?></span></td>
        <td>
            <a href="<?= BASE_URL ?>/toko/<?= $s['slug'] ?>" target="_blank"
               style="color:#3b82f6; font-size:12px; font-family:monospace">
                /toko/<?= $s['slug'] ?> ↗
            </a>
        </td>
        <td><?= $s['admin_count'] ?> user</td>
        <td><?= $s['product_count'] ?></td>
        <td><?= $s['order_count'] ?></td>
        <td style="white-space:nowrap">Rp <?= number_format($s['total_revenue'], 0, ',', '.') ?></td>
        <td><span class="badge badge-<?= $s['is_active'] ? 'selesai' : 'batal' ?>"><?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
        <td style="white-space:nowrap">
            <a href="<?= BASE_URL ?>/demo/<?= $s['niche'] ?>" target="_blank"
               class="btn btn-sm" style="background:#6366f1;color:#fff" title="Preview Tema">🎨</a>
            <a href="<?= BASE_URL ?>/superadmin/stores/toggle/<?= $s['id'] ?>" class="btn btn-sm"
               onclick="return confirm('<?= $s['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> toko ini?')">
                <?= $s['is_active'] ? '⏸' : '▶' ?>
            </a>
            <form method="POST" action="<?= BASE_URL ?>/superadmin/stores/delete/<?= $s['id'] ?>" style="display:inline"
                  onsubmit="return confirm('HAPUS toko <?= htmlspecialchars($s['name'], ENT_QUOTES) ?>? Semua data akan hilang!')">
                <?php echo $csrf_field ?? ''; ?>
                <button class="btn btn-sm btn-danger">🗑</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
