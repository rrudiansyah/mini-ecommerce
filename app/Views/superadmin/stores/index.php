<div class="page-header">
    <p style="color:var(--muted);font-size:13px">Total <strong><?= count($stores) ?></strong> toko terdaftar</p>
    <a href="<?= BASE_URL ?>/superadmin/stores/create" class="btn btn-primary">+ Tambah Toko</a>
</div>

<div class="section" style="padding:0;overflow:hidden">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Toko</th><th>Niche</th><th>URL Publik</th><th>Admin</th><th>Produk</th><th>Pesanan</th><th>Omzet</th><th>Paket</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($stores as $s): ?>
            <?php
              $plan = $s['plan'] ?? 'basic';
              $planLabels = ['basic'=>['label'=>'Basic','color'=>'#6b7280','emoji'=>'🥉'],
                             'pro'  =>['label'=>'Pro',  'color'=>'#2563a8','emoji'=>'🥈'],
                             'bisnis'=>['label'=>'Bisnis','color'=>'#d97706','emoji'=>'🥇']];
              $pl = $planLabels[$plan] ?? $planLabels['basic'];
              $isExpired = !empty($s['plan_expires_at']) && strtotime($s['plan_expires_at']) < time();
            ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($s['name']) ?></strong><br>
                    <small style="color:var(--muted)"><?= htmlspecialchars($s['phone'] ?? '-') ?></small>
                </td>
                <td><span class="badge badge-proses"><?= $s['niche'] ?></span></td>
                <td>
                    <a href="<?= BASE_URL ?>/toko/<?= $s['slug'] ?>" target="_blank"
                       style="color:var(--sky);font-size:12px;font-family:monospace">
                        /toko/<?= $s['slug'] ?> ↗
                    </a>
                </td>
                <td><?= $s['admin_count'] ?> user</td>
                <td><?= $s['product_count'] ?></td>
                <td><?= $s['order_count'] ?></td>
                <td style="white-space:nowrap">Rp <?= number_format($s['total_revenue'], 0, ',', '.') ?></td>
                <td>
                    <span style="background:<?= $pl['color'] ?>;color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:100px">
                        <?= $pl['emoji'] ?> <?= $pl['label'] ?>
                    </span>
                    <?php if ($isExpired): ?>
                      <div style="font-size:10px;color:#ef4444;margin-top:2px">⚠ Kedaluwarsa</div>
                    <?php elseif (!empty($s['plan_expires_at'])): ?>
                      <div style="font-size:10px;color:#888;margin-top:2px">s/d <?= date('d/m/Y', strtotime($s['plan_expires_at'])) ?></div>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge-<?= $s['is_active'] ? 'selesai' : 'batal' ?>"><?= $s['is_active'] ? 'Aktif' : 'Off' ?></span></td>
                <td style="white-space:nowrap">
                    <!-- Tombol upgrade plan -->
                    <button onclick="openPlanModal(<?= $s['id'] ?>, '<?= addslashes($s['name']) ?>', '<?= $plan ?>', '<?= $s['plan_expires_at'] ?? '' ?>')"
                            class="btn btn-sm" style="background:#7c3aed;color:#fff;border-color:#7c3aed" title="Atur Paket">⬆ Paket</button>
                    <a href="<?= BASE_URL ?>/demo/<?= $s['niche'] ?>" target="_blank" class="btn btn-sm" style="background:#6366f1;color:#fff;border-color:#6366f1" title="Preview">🎨</a>
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
    </div>
</div>

<!-- ── Modal Upgrade Plan ─────────────────────────────────── -->
<div id="modalPlan" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:440px;margin:16px">
    <h3 style="margin:0 0 6px;font-size:17px;font-weight:700">⬆ Atur Paket Toko</h3>
    <p id="mp_name" style="margin:0 0 20px;color:#888;font-size:13px"></p>
    <form method="POST" id="planForm" action="">
      <?php echo $csrf_field ?? ''; ?>
      <div style="margin-bottom:16px">
        <label style="font-size:13px;font-weight:700;display:block;margin-bottom:8px">Paket</label>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
          <?php
          $planOpts = [
            'basic'  => ['emoji'=>'🥉','label'=>'Basic', 'desc'=>'20 produk, 1 admin','color'=>'#6b7280'],
            'pro'    => ['emoji'=>'🥈','label'=>'Pro',   'desc'=>'100 produk, 5 admin, stok HPP','color'=>'#2563a8'],
            'bisnis' => ['emoji'=>'🥇','label'=>'Bisnis','desc'=>'Unlimited + semua fitur','color'=>'#d97706'],
          ];
          foreach ($planOpts as $key => $opt): ?>
          <label style="cursor:pointer">
            <input type="radio" name="plan" value="<?= $key ?>" id="plan_<?= $key ?>" style="display:none">
            <div class="plan-card" id="pc_<?= $key ?>"
                 style="border:2px solid #e8e6e0;border-radius:12px;padding:14px 10px;text-align:center;transition:all .15s">
              <div style="font-size:26px;margin-bottom:6px"><?= $opt['emoji'] ?></div>
              <div style="font-weight:700;font-size:13px;color:<?= $opt['color'] ?>"><?= $opt['label'] ?></div>
              <div style="font-size:10px;color:#888;margin-top:4px;line-height:1.4"><?= $opt['desc'] ?></div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:13px;font-weight:700;display:block;margin-bottom:7px">Berlaku s/d (kosongkan = selamanya)</label>
        <input type="date" name="plan_expires_at" id="mp_expires"
               style="width:100%;padding:10px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:14px">
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" style="flex:1">💾 Simpan Paket</button>
        <button type="button" onclick="document.getElementById('modalPlan').style.display='none'" class="btn" style="flex:1">Batal</button>
      </div>
    </form>
  </div>
</div>

<style>
.plan-card:hover { border-color:#7c3aed!important; }
.plan-card.selected { border-color:#7c3aed!important; background:#f5f3ff!important; }
</style>
<script>
function openPlanModal(id, name, currentPlan, expires) {
    document.getElementById('mp_name').textContent = 'Toko: ' + name;
    document.getElementById('planForm').action = '<?= BASE_URL ?>/superadmin/stores/plan/' + id;
    document.getElementById('mp_expires').value = expires || '';
    // select current plan
    ['basic','pro','bisnis'].forEach(p => {
        const radio = document.getElementById('plan_' + p);
        const card  = document.getElementById('pc_' + p);
        radio.checked = (p === currentPlan);
        card.classList.toggle('selected', p === currentPlan);
    });
    document.getElementById('modalPlan').style.display = 'flex';
    // bind radio clicks
    ['basic','pro','bisnis'].forEach(p => {
        document.getElementById('plan_' + p).addEventListener('change', function() {
            ['basic','pro','bisnis'].forEach(pp =>
                document.getElementById('pc_' + pp).classList.remove('selected'));
            document.getElementById('pc_' + p).classList.add('selected');
        });
        document.getElementById('pc_' + p).addEventListener('click', function() {
            document.getElementById('plan_' + p).checked = true;
            document.getElementById('plan_' + p).dispatchEvent(new Event('change'));
        });
    });
}
document.getElementById('modalPlan').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
