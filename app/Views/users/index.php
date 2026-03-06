<div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px">
    <h2 style="margin:0">Kelola Pengguna</h2>
    <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary">+ Tambah Pengguna</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($admins)): ?>
        <tr>
            <td colspan="5" style="text-align:center; color:#999">Tidak ada pengguna</td>
        </tr>
        <?php else: ?>
            <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?= htmlspecialchars($admin['name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td>
                    <span style="background:#e5e7eb; padding:4px 12px; border-radius:4px; font-size:12px;">
                        <?= htmlspecialchars($admin['roles'] ?? 'No Role') ?>
                    </span>
                </td>
                <td>
                    <span style="padding:4px 12px; border-radius:4px; font-size:12px; background:<?= $admin['is_active'] ? '#dcfce7; color:#166534' : '#fee2e2; color:#991b1b' ?>; font-weight:bold;">
                        <?= $admin['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>/users/edit/<?= $admin['id'] ?>" class="btn btn-sm">Edit</a>
                    <?php if ($admin['id'] !== $_SESSION['admin_id']): ?>
                    <form method="POST" action="<?= BASE_URL ?>/users/delete/<?= $admin['id'] ?>" style="display:inline" onsubmit="return confirm('Hapus pengguna ini?')">
    <?php echo $csrf_field ?? ''; ?>
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<style>
.page-header { flex-wrap: nowrap; }
.page-header h2 { font-size: 24px; }
.btn-sm { padding: 6px 12px; font-size: 13px; }
.btn-danger { background: #dc2626; color: white; }
.btn-danger:hover { background: #b91c1c; }
</style>
