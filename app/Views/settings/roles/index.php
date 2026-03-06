<div class="section">
    <div class="section-header">
        <h2>👥 Kelola Role</h2>
        <a href="<?= BASE_URL ?>/settings/roles/create" class="btn btn-primary">+ Buat Role Baru</a>
    </div>

    <?php if (empty($roles)): ?>
        <div class="empty-state">
            <p>Belum ada role. Buat role pertama Anda sekarang!</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Role</th>
                        <th>Deskripsi</th>
                        <th style="text-align: center;">Permissions</th>
                        <th style="text-align: center;">Pengguna</th>
                        <th style="text-align: right; width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($role['name']) ?></strong>
                                <?php if ($role['is_system']): ?>
                                    <span class="badge" style="background: #f59e0b; color: white; font-size: 11px;">Sistem</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #64748b; font-size: 14px;">
                                <?= htmlspecialchars($role['description'] ?? '-') ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-number"><?= $role['permission_count'] ?></span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-number"><?= $role['admin_count'] ?></span>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= BASE_URL ?>/settings/roles/<?= $role['id'] ?>/permissions" class="btn btn-sm" style="background: #3b82f6; color: white;">
                                    Permissions
                                </a>
                                <?php if (!$role['is_system']): ?>
                                    <a href="<?= BASE_URL ?>/settings/roles/edit/<?= $role['id'] ?>" class="btn btn-sm" style="background: #10b981; color: white;">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?= BASE_URL ?>/settings/roles/delete/<?= $role['id'] ?>" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus role ini?');">
    <?php echo $csrf_field ?? ''; ?>
                                        <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white;">
                                            Hapus
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    margin: 0;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
}

.badge-number {
    display: inline-block;
    background: #e0e7ff;
    color: #4f46e5;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    margin-right: 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-sm:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.table-responsive {
    overflow-x: auto;
}
</style>
