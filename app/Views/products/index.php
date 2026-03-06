<div class="page-header">
    <a href="<?= BASE_URL ?>/products/create" class="btn btn-primary">+ Tambah Produk</a>
</div>
<table class="table">
    <thead><tr><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($products as $p): ?>
    <tr>
        <td><?php if ($p['image']): ?><img src="<?= BASE_URL ?>/<?= $p['image'] ?>" width="50" style="border-radius:6px"><?php endif; ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['category_name'] ?? '-') ?></td>
        <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
        <td><span class="badge badge-<?= $p['is_available'] ? 'selesai' : 'batal' ?>"><?= $p['is_available'] ? 'Tersedia' : 'Habis' ?></span></td>
        <td>
            <a href="<?= BASE_URL ?>/products/edit/<?= $p['id'] ?>" class="btn btn-sm">Edit</a>
            <form method="POST" action="<?= BASE_URL ?>/products/delete/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Hapus produk ini?')">
    <?php echo $csrf_field ?? ''; ?>
                <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>