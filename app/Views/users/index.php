<?php
require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
$adminCount = count($admins);
$limit      = $planLimit ?? PlanHelper::limit('admins');
$overLimit  = $planOverLimit ?? PlanHelper::isOverLimit('admins', $adminCount);
$limitLabel = $limit === -1 ? '∞' : $limit;
?>

<?php if ($overLimit): ?>
<div style="background:#fff8f0;border:1.5px solid #fed7aa;border-radius:12px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:12px">
  <span style="font-size:22px">🔒</span>
  <div>
    <strong style="color:#92400e">Batas pengguna paket <?= htmlspecialchars($planName ?? PlanHelper::planName()) ?> tercapai (<?= $adminCount ?>/<?= $limitLabel ?>)</strong>
    <div style="font-size:13px;color:#b45309;margin-top:2px">Hubungi Super Admin untuk upgrade ke paket Pro (maks 5 user) atau Bisnis (unlimited).</div>
  </div>
</div>
<?php endif; ?>

<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:16px">
  <div style="display:flex;align-items:center;gap:10px">
    <span style="font-size:13px;color:#888"><?= $adminCount ?>/<?= $limitLabel ?> pengguna</span>
    <?= PlanHelper::badge() ?>
  </div>
  <?php if ($overLimit): ?>
    <button class="btn btn-primary" disabled style="opacity:.5;cursor:not-allowed">🔒 Tambah Pengguna</button>
  <?php else: ?>
    <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary">+ Tambah Pengguna</a>
  <?php endif; ?>
</div>

<div class="section" style="padding:0;overflow:hidden">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (empty($admins)): ?>
                <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--muted)">Tidak ada pengguna</td></tr>
                <?php else: ?>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($admin['name']) ?></strong></td>
                        <td style="color:var(--muted)"><?= htmlspecialchars($admin['email']) ?></td>
                        <td><span class="badge badge-proses"><?= htmlspecialchars($admin['roles'] ?? 'No Role') ?></span></td>
                        <td><span class="badge badge-<?= $admin['is_active'] ? 'selesai' : 'batal' ?>"><?= $admin['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                        <td style="white-space:nowrap">
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
    </div>
</div>
