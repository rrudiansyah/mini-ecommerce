<a href="<?= BASE_URL ?>/superadmin/settings" class="btn btn-sm" style="margin-bottom:20px">← Kembali</a>

<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px">
    <div style="width:48px; height:48px; border-radius:10px; background:<?= htmlspecialchars($store['theme_color'] ?? '#3b82f6') ?>; display:flex; align-items:center; justify-content:center; font-size:22px; color:#fff">
        <?= ['coffee'=>'☕','laundry'=>'🧺','barbershop'=>'✂️','restaurant'=>'🍽️','fashion'=>'👗','bakery'=>'🍰'][$store['niche']] ?? '🏪' ?>
    </div>
    <div>
        <h2 style="margin:0; font-size:18px"><?= htmlspecialchars($store['name']) ?></h2>
        <p style="margin:0; font-size:13px; color:#64748b"><?= count($roles) ?> role terdaftar</p>
    </div>
</div>

<?php foreach ($roles as $role): ?>
<div class="section" style="margin-bottom:20px; border:1.5px solid #e2e8f0; border-radius:12px; overflow:hidden">
    <!-- Role header -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 18px; background:#f8fafc; border-bottom:1px solid #e2e8f0; cursor:pointer"
         onclick="toggleRole(<?= $role['id'] ?>)">
        <div style="display:flex; align-items:center; gap:10px">
            <span style="font-size:18px"><?= $role['is_system'] ? '🔒' : '🔧' ?></span>
            <div>
                <strong style="font-size:15px"><?= htmlspecialchars($role['name']) ?></strong>
                <?php if ($role['is_system']): ?>
                <span class="badge badge-proses" style="margin-left:6px; font-size:10px">Sistem</span>
                <?php endif; ?>
                <div style="font-size:12px; color:#64748b; margin-top:1px">
                    <?= $role['admin_count'] ?> pengguna &nbsp;·&nbsp;
                    <?= count($role['permissions']) ?> permission aktif
                    <?php if (!empty($role['description'])): ?>
                    &nbsp;·&nbsp; <?= htmlspecialchars($role['description']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <span id="arrow-<?= $role['id'] ?>" style="color:#64748b; transition:transform .2s; display:inline-block">▼</span>
    </div>

    <!-- Permission grid -->
    <div id="perm-<?= $role['id'] ?>" style="display:none; padding:18px">
        <?php
        $rolePermIds = array_column($role['permissions'], 'id');
        foreach ($allPermissions as $module => $perms):
        ?>
        <div style="margin-bottom:16px">
            <div style="font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#94a3b8; margin-bottom:8px">
                <?= ucfirst($module) ?>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px">
                <?php foreach ($perms as $perm): ?>
                <?php $checked = in_array($perm['id'], $rolePermIds); ?>
                <label style="display:flex; align-items:center; gap:6px; padding:6px 12px; border-radius:8px; border:1.5px solid <?= $checked ? '#86efac' : '#e2e8f0' ?>; background:<?= $checked ? '#f0fdf4' : '#fff' ?>; cursor:pointer; font-size:12px; font-weight:600; transition:all .15s; user-select:none"
                       id="lbl-<?= $perm['id'] ?>-<?= $role['id'] ?>">
                    <input type="checkbox"
                           <?= $checked ? 'checked' : '' ?>
                           onchange="togglePerm(this, <?= $role['id'] ?>, <?= $perm['id'] ?>)"
                           style="display:none">
                    <span id="dot-<?= $perm['id'] ?>-<?= $role['id'] ?>"><?= $checked ? '✓' : '○' ?></span>
                    <?= htmlspecialchars(str_replace($module . '.', '', $perm['name'])) ?>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<script>
function toggleRole(id) {
    const el    = document.getElementById('perm-' + id);
    const arrow = document.getElementById('arrow-' + id);
    const open  = el.style.display === 'none';
    el.style.display    = open ? 'block' : 'none';
    arrow.style.transform = open ? 'rotate(180deg)' : '';
}

async function togglePerm(cb, roleId, permId) {
    const action  = cb.checked ? 'add' : 'remove';
    const lbl     = document.getElementById(`lbl-${permId}-${roleId}`);
    const dot     = document.getElementById(`dot-${permId}-${roleId}`);

    try {
        const fd = new FormData();
        fd.append('_csrf_token', '<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>');
        fd.append('permission_id', permId);
        fd.append('action', action);

        const res  = await fetch(`<?= BASE_URL ?>/superadmin/settings/roles/${roleId}/permission`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            const on = cb.checked;
            lbl.style.borderColor = on ? '#86efac' : '#e2e8f0';
            lbl.style.background  = on ? '#f0fdf4' : '#fff';
            dot.textContent       = on ? '✓' : '○';
        } else {
            cb.checked = !cb.checked; // rollback
            alert('Gagal: ' + (data.error ?? 'Unknown error'));
        }
    } catch (e) {
        cb.checked = !cb.checked;
        alert('Koneksi gagal: ' + e.message);
    }
}
</script>
