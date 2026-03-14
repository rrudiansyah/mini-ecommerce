<?php
// app/Views/reports/variants.php
$rows     = $variantStock ?? [];
$lowThreshold = 5;

// Kelompokkan per produk
$byProduct = [];
foreach ($rows as $r) {
    $pid = $r['product_id'];
    if (!isset($byProduct[$pid])) {
        $byProduct[$pid] = [
            'name'     => $r['product_name'],
            'category' => $r['category_name'] ?? '-',
            'variants' => [],
            'total'    => 0,
            'low'      => 0,
        ];
    }
    $byProduct[$pid]['variants'][] = $r;
    $byProduct[$pid]['total']     += (int)$r['stock'];
    if ((int)$r['stock'] <= $lowThreshold) $byProduct[$pid]['low']++;
}

$totalVariants = count($rows);
$totalLow      = count(array_filter($rows, fn($r) => (int)$r['stock'] <= $lowThreshold && (int)$r['stock'] > 0));
$totalHabis    = count(array_filter($rows, fn($r) => (int)$r['stock'] === 0));
?>

<!-- Header -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:10px">
    <div>
        <h2 style="margin:0;font-size:20px;font-weight:700">🎨 Laporan Stok Varian</h2>
        <p style="margin:4px 0 0;color:#888;font-size:13px">Stok per varian semua produk toko</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>/reports" class="btn btn-sm">← Laporan</a>
        <a href="<?= BASE_URL ?>/products" class="btn btn-sm">📦 Produk</a>
    </div>
</div>

<!-- Ringkasan -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:24px">
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#1f2937"><?= count($byProduct) ?></div>
        <div style="font-size:12px;color:#6b7280;margin-top:2px">Produk dengan Varian</div>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#1f2937"><?= $totalVariants ?></div>
        <div style="font-size:12px;color:#6b7280;margin-top:2px">Total SKU Varian</div>
    </div>
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#d97706"><?= $totalLow ?></div>
        <div style="font-size:12px;color:#d97706;margin-top:2px">⚠️ Stok Menipis</div>
    </div>
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#dc2626"><?= $totalHabis ?></div>
        <div style="font-size:12px;color:#dc2626;margin-top:2px">⛔ Stok Habis</div>
    </div>
</div>

<!-- Filter -->
<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
    <button onclick="filterStock('all')" id="f-all" class="btn btn-primary btn-sm">Semua</button>
    <button onclick="filterStock('low')" id="f-low" class="btn btn-sm">⚠️ Menipis</button>
    <button onclick="filterStock('habis')" id="f-habis" class="btn btn-sm">⛔ Habis</button>
    <input type="text" id="searchVariant" placeholder="🔍 Cari produk / varian..."
           oninput="searchVariants(this.value)"
           style="padding:6px 12px;border:1.5px solid #ddd;border-radius:8px;font-size:13px;flex:1;min-width:180px">
</div>

<!-- Tabel per produk -->
<?php if (empty($byProduct)): ?>
<div style="text-align:center;padding:60px;background:#f9fafb;border-radius:12px;border:2px dashed #e5e7eb">
    <div style="font-size:48px">🎨</div>
    <p style="color:#6b7280;margin:12px 0">Belum ada produk dengan varian</p>
    <a href="<?= BASE_URL ?>/products" class="btn btn-primary">+ Buat Produk dengan Varian</a>
</div>
<?php else: ?>

<div id="variantStockList">
<?php foreach ($byProduct as $pid => $prod): ?>
<?php
$totalStok = $prod['total'];
$lowCount  = $prod['low'];
$hasHabis  = count(array_filter($prod['variants'], fn($v) => (int)$v['stock'] === 0));
$statusClass = $hasHabis ? 'habis' : ($lowCount > 0 ? 'low' : 'ok');
?>
<div class="product-stock-card" data-status="<?= $statusClass ?>" data-name="<?= strtolower(htmlspecialchars($prod['name'])) ?>"
     style="background:#fff;border:1.5px solid <?= $hasHabis ? '#fecaca' : ($lowCount ? '#fde68a' : '#e5e7eb') ?>;
            border-radius:12px;margin-bottom:12px;overflow:hidden">

    <!-- Header produk -->
    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;
                background:<?= $hasHabis ? '#fef2f2' : ($lowCount ? '#fffbeb' : '#f9fafb') ?>;
                cursor:pointer" onclick="toggleCard(<?= $pid ?>)">
        <div>
            <span style="font-weight:700;font-size:15px"><?= htmlspecialchars($prod['name']) ?></span>
            <span style="font-size:12px;color:#9ca3af;margin-left:8px"><?= htmlspecialchars($prod['category']) ?></span>
            <?php if ($hasHabis): ?>
            <span style="background:#fee2e2;color:#dc2626;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:6px">⛔ Ada yang Habis</span>
            <?php elseif ($lowCount): ?>
            <span style="background:#fef3c7;color:#d97706;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:6px">⚠️ <?= $lowCount ?> Menipis</span>
            <?php endif; ?>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <span style="font-size:13px;color:#6b7280"><?= count($prod['variants']) ?> varian | Total: <strong><?= $totalStok ?></strong> pcs</span>
            <span id="arrow-<?= $pid ?>" style="color:#9ca3af;font-size:16px">▼</span>
        </div>
    </div>

    <!-- Tabel varian -->
    <div id="card-<?= $pid ?>" style="display:block;padding:0 18px 14px">
        <table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:10px">
            <thead>
                <tr style="background:#f3f4f6">
                    <th style="padding:8px 10px;text-align:left;font-weight:600">Varian</th>
                    <th style="padding:8px 10px;text-align:left;font-weight:600">SKU</th>
                    <th style="padding:8px 10px;text-align:right;font-weight:600">Harga Jual</th>
                    <th style="padding:8px 10px;text-align:center;font-weight:600">Stok</th>
                    <th style="padding:8px 10px;text-align:center;font-weight:600">Status</th>
                    <th style="padding:8px 10px;text-align:center;font-weight:600">Update Stok</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($prod['variants'] as $v): ?>
            <?php
            $stk     = (int)$v['stock'];
            $isEmpty = $stk === 0;
            $isLow   = $stk > 0 && $stk <= $lowThreshold;
            $price   = (float)$v['price'] > 0 ? $v['price'] : $prod['variants'][0]['base_price'] ?? 0;
            ?>
            <tr style="border-bottom:1px solid #f3f4f6" id="vrow-<?= $v['id'] ?>">
                <td style="padding:8px 10px;font-weight:600"><?= htmlspecialchars($v['label']) ?></td>
                <td style="padding:8px 10px;color:#9ca3af;font-size:12px"><?= $v['sku'] ? htmlspecialchars($v['sku']) : '—' ?></td>
                <td style="padding:8px 10px;text-align:right">
                    Rp <?= number_format((float)$v['price'] > 0 ? $v['price'] : 0, 0, ',', '.') ?>
                    <?php if ((float)$v['price'] == 0): ?>
                    <small style="color:#9ca3af">(ikut produk)</small>
                    <?php endif; ?>
                </td>
                <td style="padding:8px 10px;text-align:center">
                    <span id="stk-<?= $v['id'] ?>" style="font-weight:800;font-size:16px;
                        color:<?= $isEmpty ? '#dc2626' : ($isLow ? '#d97706' : '#16a34a') ?>">
                        <?= $stk ?>
                    </span>
                </td>
                <td style="padding:8px 10px;text-align:center">
                    <?php if ($isEmpty): ?>
                    <span style="background:#fee2e2;color:#dc2626;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700">⛔ Habis</span>
                    <?php elseif ($isLow): ?>
                    <span style="background:#fef3c7;color:#d97706;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700">⚠️ Menipis</span>
                    <?php else: ?>
                    <span style="background:#dcfce7;color:#16a34a;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700">✅ Oke</span>
                    <?php endif; ?>
                </td>
                <td style="padding:8px 10px;text-align:center">
                    <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                        <input type="number" id="inp-<?= $v['id'] ?>" value="<?= $stk ?>" min="0"
                               style="width:70px;padding:5px 8px;border:1.5px solid #ddd;border-radius:6px;text-align:center;font-size:13px">
                        <button onclick="updateStock(<?= $v['id'] ?>)" <?= $csrf_field_data ?? '' ?>
                                style="background:#3b82f6;color:#fff;border:none;border-radius:6px;padding:5px 10px;cursor:pointer;font-size:12px;font-weight:700">
                            Simpan
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<script>
// ── Toggle card ───────────────────────────────────────────
function toggleCard(id) {
    const card  = document.getElementById('card-' + id);
    const arrow = document.getElementById('arrow-' + id);
    if (card.style.display === 'none') {
        card.style.display = 'block';
        arrow.textContent = '▼';
    } else {
        card.style.display = 'none';
        arrow.textContent = '▶';
    }
}

// ── Filter stok ───────────────────────────────────────────
function filterStock(type) {
    document.querySelectorAll('[id^="f-"]').forEach(b => b.classList.remove('btn-primary'));
    document.getElementById('f-' + type).classList.add('btn-primary');

    document.querySelectorAll('.product-stock-card').forEach(card => {
        const status = card.dataset.status;
        if (type === 'all') card.style.display = '';
        else if (type === 'low')   card.style.display = (status === 'low')   ? '' : 'none';
        else if (type === 'habis') card.style.display = (status === 'habis') ? '' : 'none';
    });
}

// ── Cari produk/varian ────────────────────────────────────
function searchVariants(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.product-stock-card').forEach(card => {
        card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
}

// ── Update stok via AJAX ──────────────────────────────────
async function updateStock(variantId) {
    const inp   = document.getElementById('inp-' + variantId);
    const stock = parseInt(inp.value);
    if (isNaN(stock) || stock < 0) { alert('Stok tidak valid!'); return; }

    try {
        const res = await fetch('<?= BASE_URL ?>/variants/stock/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ variant_id: variantId, stock: stock,
                                   _csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || '' }),
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('stk-' + variantId).textContent = stock;
            showToastVariant(stock <= 0 ? '⛔ Stok diset 0' : '✅ Stok diperbarui → ' + stock, stock <= 0 ? 'red' : 'green');
        } else {
            alert('Gagal: ' + (data.message || 'Error'));
        }
    } catch(e) {
        alert('Koneksi gagal.');
    }
}

function showToastVariant(msg, type) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = `position:fixed;bottom:24px;right:24px;background:${type === 'red' ? '#dc2626' : '#16a34a'};
        color:#fff;padding:10px 20px;border-radius:10px;font-weight:700;z-index:9999;font-size:14px;
        animation:fadeIn .2s ease`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}
</script>
