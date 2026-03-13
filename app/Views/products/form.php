<?php
// app/Views/products/form.php
$isEdit   = !empty($product);
require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
$hppType     = $product['hpp_type'] ?? 'manual';
$canHppAuto  = PlanHelper::canFeature('hpp_auto');
$canVariants = PlanHelper::canFeature('variants');
if ($hppType === 'auto' && !$canHppAuto) $hppType = 'manual';
$recipeMap = [];
foreach (($recipe ?? []) as $r) {
    $recipeMap[$r['ingredient_id']] = $r;
}
?>

<form method="POST" novalidate
      action="<?= BASE_URL ?>/products/<?= $isEdit ? 'update/' . $product['id'] : 'store' ?>"
      enctype="multipart/form-data">
  <?php echo $csrf_field ?? ''; ?>

  <div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start">

    <!-- ── Kolom Kiri ── -->
    <div>
      <!-- Info Dasar -->
      <div style="background:#fff;border-radius:14px;border:1px solid #e8e6e0;padding:24px;margin-bottom:20px">
        <h3 style="margin:0 0 18px;font-size:15px;font-weight:700">📦 Informasi Produk</h3>
        <div class="form-group">
          <label>Nama Produk *</label>
          <input type="text" name="name" required value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="cth: Kopi Latte, Es Kopi Susu...">
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="description" rows="2"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Harga Jual (Rp) *</label>
            <input type="number" name="price" required min="0" id="inputPrice" value="<?= $product['price'] ?? '' ?>" oninput="recalc()">
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="category_id">
              <option value="">— Pilih Kategori —</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Foto Produk</label>
          <input type="file" name="image" accept="image/*">
          <?php if (!empty($product['image'])): ?>
            <img src="<?= BASE_URL ?>/<?= $product['image'] ?>" width="80" style="margin-top:8px;border-radius:8px;display:block">
          <?php endif; ?>
        </div>
        <?php if ($isEdit): ?>
        <div class="form-group">
          <label>Status</label>
          <select name="is_available">
            <option value="1" <?= $product['is_available'] ? 'selected' : '' ?>>Tersedia</option>
            <option value="0" <?= !$product['is_available'] ? 'selected' : '' ?>>Habis</option>
          </select>
        </div>
        <?php endif; ?>
      </div>

      <!-- ── HPP Section ── -->
      <div style="background:#fff;border-radius:14px;border:1px solid #e8e6e0;padding:24px">
        <h3 style="margin:0 0 6px;font-size:15px;font-weight:700">💰 HPP (Harga Pokok Produksi)</h3>
        <p style="font-size:12px;color:#888;margin:0 0 18px">Digunakan untuk menghitung keuntungan per produk.</p>

        <!-- Toggle -->
        <div style="display:flex;gap:10px;margin-bottom:20px">
          <label style="flex:1;cursor:pointer">
            <input type="radio" name="hpp_type" value="manual" <?= $hppType === 'manual' ? 'checked' : '' ?> onchange="switchHpp('manual')" style="display:none">
            <div class="hppcard <?= $hppType === 'manual' ? 'hppcard-on' : '' ?>" id="card_manual">
              <div style="font-size:22px;margin-bottom:5px">✏️</div>
              <strong style="font-size:13px">Input Manual</strong>
              <div style="font-size:11px;color:#888;margin-top:2px">Masukkan HPP langsung</div>
            </div>
          </label>
          <label style="flex:1;cursor:pointer">
            <input type="radio" name="hpp_type" value="auto"
                   <?= $hppType === 'auto' ? 'checked' : '' ?>
                   <?= !$canHppAuto ? 'disabled' : '' ?>
                   onchange="switchHpp('auto')" style="display:none">
            <div class="hppcard <?= $hppType === 'auto' ? 'hppcard-on' : '' ?>" id="card_auto">
              <div style="font-size:22px;margin-bottom:5px">⚗️</div>
              <strong style="font-size:13px">Dari Resep Bahan</strong>
              <?php if (!$canHppAuto): ?>
              <span style="display:block;font-size:11px;color:#ef4444;margin-top:2px">
                  🔒 Upgrade ke Pro
              </span>
              <?php endif; ?>
              <div style="font-size:11px;color:#888;margin-top:2px">Hitung otomatis dari stok bahan</div>
            </div>
          </label>
        </div>

        <!-- Manual HPP -->
        <div id="panel_manual" <?= $hppType === 'auto' ? 'style="display:none"' : '' ?>>
          <div class="form-group" style="margin-bottom:0">
            <label>Nilai HPP (Rp)</label>
            <input type="number" name="hpp" id="inputHpp" min="0" step="100" oninput="recalc()" value="<?= $product['hpp'] ?? 0 ?>" placeholder="cth: 8000">
          </div>
        </div>

        <!-- Auto HPP / BOM Recipe -->
        <!-- Stok Produk (hanya untuk HPP Manual) -->
        <div id="panel_stock" style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:14px;margin-bottom:16px;<?= $hppType === 'auto' ? 'display:none' : '' ?>">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
                <div>
                    <span style="font-weight:700;font-size:14px">📦 Stok Produk</span>
                    <p style="margin:4px 0 0;font-size:12px;color:#166534">
                        Kelola stok di menu
                        <a href="<?= BASE_URL ?>/product-stock" target="_blank" style="font-weight:700;color:#15803d;text-decoration:underline">Stok Produk →</a>
                    </p>
                </div>
                <div style="text-align:right">
                    <?php if (isset($product['stock']) && (int)$product['stock'] >= 0): ?>
                        <span style="font-size:20px;font-weight:700;color:#15803d"><?= (int)$product['stock'] ?></span>
                        <span style="font-size:12px;color:#6b7280"> pcs tersisa</span>
                    <?php else: ?>
                        <span style="font-size:12px;color:#6b7280">⬜ Belum ditrack</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

                <div id="panel_auto" <?= $hppType === 'manual' ? 'style="display:none"' : '' ?>>
          <?php if (empty($ingredients)): ?>
            <div style="background:#fff8f0;border:1px solid #fed7aa;border-radius:10px;padding:14px;font-size:13px;color:#92400e">
              ⚠️ Belum ada bahan baku.
              <a href="<?= BASE_URL ?>/inventory/create" target="_blank" style="color:#c2410c;font-weight:700">Tambah bahan baku dulu →</a>
            </div>
          <?php else: ?>
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:13px;color:#92400e">HPP Otomatis (dari resep):</span>
              <strong style="font-size:18px;color:#b45309" id="autoHppVal">Rp 0</strong>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
              <label style="font-size:13px;font-weight:700">Bahan & Takaran per 1 porsi</label>
              <button type="button" onclick="addRow()" class="btn btn-sm btn-primary">+ Tambah Bahan</button>
            </div>
            <div id="recipeRows">
              <?php
              $rows = !empty($recipe) ? $recipe : [null];
              foreach ($rows as $r):
              ?>
              <div class="rrow" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                <select name="recipe_ingredient[]" class="rsel" onchange="rowSelChange(this);recalc();"
                        style="flex:1;padding:8px 10px;border:1.5px solid #ddd;border-radius:8px;font-size:13px;background:#fff">
                  <option value="">— Pilih Bahan —</option>
                  <?php foreach ($ingredients as $ing): ?>
                    <option value="<?= $ing['id'] ?>" data-cost="<?= $ing['cost_per_unit'] ?>" data-unit="<?= htmlspecialchars($ing['unit']) ?>"
                            <?= ($r && $r['ingredient_id'] == $ing['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($ing['name']) ?> (<?= $ing['unit'] ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <input type="number" name="recipe_qty[]" step="0.001" min="0.001" oninput="recalc()"
                       value="<?= $r ? $r['qty_used'] : '' ?>"
                       style="width:90px;padding:8px 10px;border:1.5px solid #ddd;border-radius:8px;font-size:13px"
                       placeholder="Qty">
                <span class="runit" style="min-width:32px;font-size:12px;color:#888"><?= $r ? htmlspecialchars($r['unit'] ?? '') : '' ?></span>
                <button type="button" onclick="this.closest('.rrow').remove();recalc();"
                        style="background:#fee2e2;border:none;border-radius:8px;padding:8px 10px;cursor:pointer;color:#dc2626">✕</button>
              </div>
              <?php endforeach; ?>
            </div>
            <div style="font-size:11px;color:#2563a8;background:#eff6ff;border-radius:8px;padding:8px 12px;margin-top:4px">
              💡 Harga/unit bahan diatur di <a href="<?= BASE_URL ?>/inventory" target="_blank">Manajemen Stok Bahan</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- ── Sidebar: Ringkasan ── -->
    <div style="position:sticky;top:80px">
      <div style="background:#fff;border-radius:14px;border:1px solid #e8e6e0;padding:24px">
        <h3 style="margin:0 0 16px;font-size:15px;font-weight:700">📊 Profitabilitas</h3>
        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f0ede8">
          <span style="font-size:13px;color:#888">Harga Jual</span>
          <span style="font-weight:700" id="sPrice">Rp 0</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f0ede8">
          <span style="font-size:13px;color:#888">HPP</span>
          <span style="font-weight:700;color:#dc2626" id="sHpp">Rp 0</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #f0ede8">
          <span style="font-size:13px;color:#888">Margin Kotor</span>
          <span style="font-weight:800;color:#16a34a" id="sMargin">Rp 0</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:9px 0">
          <span style="font-size:13px;color:#888">Margin %</span>
          <span style="font-weight:800" id="sMarginPct">0%</span>
        </div>
        <div style="height:8px;background:#f0ede8;border-radius:100px;overflow:hidden;margin:10px 0 4px">
          <div id="mbar" style="height:100%;border-radius:100px;width:0%;transition:width .3s;background:#22c55e"></div>
        </div>
        <div id="mmsg" style="font-size:11px;color:#888;text-align:center;min-height:16px"></div>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f0ede8">
          <button type="submit" class="btn btn-primary" style="width:100%;padding:13px">
            <?= $isEdit ? '💾 Simpan Perubahan' : '➕ Tambah Produk' ?>
          </button>
          <a href="<?= BASE_URL ?>/products" class="btn" style="display:block;width:100%;padding:11px;text-align:center;margin-top:8px">Batal</a>
        </div>
      </div>
    </div>

  </div>

<style>
.hppcard{border:2px solid #e8e6e0;border-radius:12px;padding:14px;text-align:center;background:#fafafa;transition:all .2s}
.hppcard-on{border-color:#2563a8;background:#eff6ff}
.hppcard:hover{border-color:#93c5fd}
</style>

<!-- ── SECTION VARIAN PRODUK ─────────────────────────── -->
<?php
require_once ROOT_PATH . '/app/Helpers/PlanHelper.php';
$canVariants  = PlanHelper::canFeature('variants');
$hasVariants  = (int)($product['has_variants'] ?? 0);
$variantTypes = $variantTypes ?? [];
$variants     = $variants ?? [];
?>

<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:24px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <div>
            <h3 style="margin:0;font-size:16px;font-weight:700">🎨 Varian Produk</h3>
            <p style="margin:4px 0 0;color:#888;font-size:13px">Ukuran, warna, atau pilihan lain</p>
        </div>
        <?php if (!$canVariants): ?>
        <span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700">
            🔒 Fitur Pro+
        </span>
        <?php endif; ?>
    </div>

    <?php if (!$canVariants): ?>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:16px;text-align:center">
        <p style="margin:0;color:#92400e"><?= htmlspecialchars(PlanHelper::upgradeMessage('variants')) ?></p>
    </div>
    <?php else: ?>

    <!-- Toggle has_variants -->
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:16px">
        <input type="checkbox" name="has_variants" value="1" id="hasVariantsToggle"
               <?= $hasVariants ? 'checked' : '' ?>
               onchange="document.getElementById('variantSection').style.display=this.checked?'block':'none'"
               style="width:18px;height:18px">
        <span style="font-weight:600;font-size:14px">Produk ini memiliki varian (ukuran, warna, dll)</span>
    </label>

    <div id="variantSection" style="display:<?= $hasVariants ? 'block' : 'none' ?>">

        <?php if (empty($variantTypes)): ?>
        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px;margin-bottom:16px">
            <p style="margin:0;color:#0369a1;font-size:13px">
                ⚠️ Belum ada tipe varian. 
                <a href="<?= BASE_URL ?>/variants" target="_blank" style="font-weight:700">Buat tipe varian dulu →</a>
            </p>
        </div>
        <?php else: ?>

        <!-- Builder varian -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:16px">
            <h4 style="margin:0 0 12px;font-size:14px;font-weight:700">Generate Varian dari Tipe</h4>
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:12px" id="typeSelectors">
                <?php foreach ($variantTypes as $type): ?>
                <div>
                    <label style="font-weight:600;font-size:13px;display:block;margin-bottom:6px">
                        <?= htmlspecialchars($type['name']) ?>
                    </label>
                    <div style="display:flex;flex-wrap:wrap;gap:6px">
                        <?php foreach ($type['options_list'] as $opt): ?>
                        <label style="display:flex;align-items:center;gap:4px;cursor:pointer;
                               background:#fff;border:1.5px solid #e5e7eb;border-radius:6px;padding:4px 10px;font-size:13px">
                            <input type="checkbox" class="opt-check" data-type="<?= $type['id'] ?>"
                                   value="<?= $opt['id'] ?>:<?= htmlspecialchars($opt['value']) ?>">
                            <?= htmlspecialchars($opt['value']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="generateVariants()">
                ⚡ Generate Varian
            </button>
        </div>

        <?php endif; ?>

        <!-- Tabel varian -->
        <table style="width:100%;border-collapse:collapse;font-size:13px" id="variantTable">
            <thead>
                <tr style="background:#f3f4f6">
                    <th style="padding:8px 12px;text-align:left;font-weight:700">Varian</th>
                    <th style="padding:8px 12px;text-align:left;font-weight:700">SKU</th>
                    <th style="padding:8px 12px;text-align:left;font-weight:700">Harga Jual (Rp) <small style="font-weight:400;color:#888">0=pakai harga produk</small></th>
                    <th style="padding:8px 12px;text-align:left;font-weight:700">HPP (Rp) <small style="font-weight:400;color:#888">0=tidak dihitung</small></th>
                    <th style="padding:8px 12px;text-align:left;font-weight:700">Stok</th>
                    <th style="padding:8px 12px;text-align:left;font-weight:700">Margin</th>
                    <th style="padding:8px 12px;text-align:center;font-weight:700">Aksi</th>
                </tr>
            </thead>
            <tbody id="variantBody">
                <?php foreach ($variants as $i => $v): ?>
                <tr class="variant-row">
                    <td style="padding:6px 8px">
                        <input type="text" name="variant_label[]" value="<?= htmlspecialchars($v['label']) ?>"
                               placeholder="misal: M / Merah-L"
                               style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
                    </td>
                    <td style="padding:6px 8px">
                        <input type="text" name="variant_sku[]" value="<?= htmlspecialchars($v['sku'] ?? '') ?>"
                               placeholder="SKU-001"
                               style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
                    </td>
                    <td style="padding:6px 8px">
                        <input type="number" name="variant_price[]" value="<?= $v['price'] ?>"
                               min="0" step="500" placeholder="0" onchange="recalcVariantMargin(this)"
                               style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
                    </td>
                    <td style="padding:6px 8px">
                        <input type="number" name="variant_hpp[]" value="<?= $v['hpp'] ?? 0 ?>"
                               min="0" step="500" placeholder="0" onchange="recalcVariantMargin(this)"
                               style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
                    </td>
                    <td style="padding:6px 8px">
                        <input type="number" name="variant_stock[]" value="<?= $v['stock'] ?>"
                               min="0" placeholder="0"
                               style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
                    </td>
                    <td style="padding:6px 8px">
                        <?php
                            $vPrice = (float)$v['price'];
                            $vHpp   = (float)($v['hpp'] ?? 0);
                            $vMargin = ($vPrice > 0 && $vHpp > 0) ? round(($vPrice - $vHpp) / $vPrice * 100) : null;
                        ?>
                        <?php if ($vMargin !== null): ?>
                        <span style="font-size:12px;font-weight:700;color:<?= $vMargin >= 20 ? '#16a34a' : ($vMargin >= 0 ? '#d97706' : '#dc2626') ?>">
                            <?= $vMargin ?>%
                        </span>
                        <?php else: ?>
                        <span style="font-size:12px;color:#9ca3af">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:6px 8px;text-align:center">
                        <button type="button" onclick="this.closest('tr').remove()"
                                style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:6px;padding:4px 10px;cursor:pointer">
                            🗑
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="button" class="btn btn-sm" style="margin-top:10px" onclick="addVariantRow()">
            + Tambah Baris
        </button>
    </div>
    <?php endif; ?>
</div>

<script>
function addVariantRow(label='', price='0', stock='0', sku='', hpp='0') {
    const row = `<tr class="variant-row">
        <td style="padding:6px 8px">
            <input type="text" name="variant_label[]" value="${label}" placeholder="misal: M / Merah-L"
                   style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
        </td>
        <td style="padding:6px 8px">
            <input type="text" name="variant_sku[]" value="${sku}" placeholder="SKU-001"
                   style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
        </td>
        <td style="padding:6px 8px">
            <input type="number" name="variant_price[]" value="${price}" min="0" step="500" placeholder="0"
                   onchange="recalcVariantMargin(this)"
                   style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
        </td>
        <td style="padding:6px 8px">
            <input type="number" name="variant_hpp[]" value="${hpp}" min="0" step="500" placeholder="0"
                   onchange="recalcVariantMargin(this)"
                   style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
        </td>
        <td style="padding:6px 8px">
            <input type="number" name="variant_stock[]" value="${stock}" min="0" placeholder="0"
                   style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:6px">
        </td>
        <td class="variant-margin-cell" style="padding:6px 8px;font-size:12px;font-weight:700;color:#9ca3af">—</td>
        <td style="padding:6px 8px;text-align:center">
            <button type="button" onclick="this.closest('tr').remove()"
                    style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:6px;padding:4px 10px;cursor:pointer">🗑</button>
        </td>
    </tr>`;
    document.getElementById('variantBody').insertAdjacentHTML('beforeend', row);
}

function recalcVariantMargin(el) {
    const row    = el.closest('tr');
    const price  = parseFloat(row.querySelector('input[name="variant_price[]"]').value) || 0;
    const hpp    = parseFloat(row.querySelector('input[name="variant_hpp[]"]').value)   || 0;
    const cell   = row.querySelector('.variant-margin-cell');
    if (!cell) return;
    if (price > 0 && hpp > 0) {
        const margin = Math.round((price - hpp) / price * 100);
        const color  = margin >= 20 ? '#16a34a' : (margin >= 0 ? '#d97706' : '#dc2626');
        cell.textContent = margin + '%';
        cell.style.color = color;
    } else {
        cell.textContent = '—';
        cell.style.color = '#9ca3af';
    }
}

function generateVariants() {
    // Ambil semua tipe yang ada opsi terpilih
    const typeMap = {};
    document.querySelectorAll('.opt-check:checked').forEach(cb => {
        const type = cb.dataset.type;
        if (!typeMap[type]) typeMap[type] = [];
        typeMap[type].push(cb.value.split(':')[1]);
    });

    const groups = Object.values(typeMap);
    if (groups.length === 0) {
        alert('Pilih minimal satu opsi dulu!');
        return;
    }

    // Cartesian product
    const combos = groups.reduce((acc, group) => {
        const result = [];
        acc.forEach(a => group.forEach(b => result.push(a ? a + ' / ' + b : b)));
        return result;
    }, ['']);

    // Hapus varian existing, tambah yang baru
    document.getElementById('variantBody').innerHTML = '';
    combos.forEach(label => addVariantRow(label));
}
</script>

<script>
// Inline ingredient options for dynamic rows
const ingOpts = [
<?php foreach ($ingredients as $ing): ?>
  {id:<?= $ing['id'] ?>,name:<?= json_encode($ing['name']) ?>,unit:<?= json_encode($ing['unit']) ?>,cost:<?= (float)$ing['cost_per_unit'] ?>},
<?php endforeach; ?>
];

function addRow() {
  const opts = ingOpts.map(i=>`<option value="${i.id}" data-cost="${i.cost}" data-unit="${i.unit}">${i.name} (${i.unit})</option>`).join('');
  const d = document.createElement('div');
  d.className='rrow';
  d.style.cssText='display:flex;gap:8px;align-items:center;margin-bottom:8px';
  d.innerHTML=`
    <select name="recipe_ingredient[]" class="rsel" onchange="rowSelChange(this);recalc();"
            style="flex:1;padding:8px 10px;border:1.5px solid #ddd;border-radius:8px;font-size:13px;background:#fff">
      <option value="">— Pilih Bahan —</option>${opts}
    </select>
    <input type="number" name="recipe_qty[]" step="0.001" min="0.001" oninput="recalc()"
           style="width:90px;padding:8px 10px;border:1.5px solid #ddd;border-radius:8px;font-size:13px" placeholder="Qty">
    <span class="runit" style="min-width:32px;font-size:12px;color:#888"></span>
    <button type="button" onclick="this.closest('.rrow').remove();recalc();"
            style="background:#fee2e2;border:none;border-radius:8px;padding:8px 10px;cursor:pointer;color:#dc2626">✕</button>
  `;
  document.getElementById('recipeRows').appendChild(d);
}

function rowSelChange(sel) {
  const opt = sel.options[sel.selectedIndex];
  sel.closest('.rrow').querySelector('.runit').textContent = opt.dataset.unit||'';
}
// init units on load
document.querySelectorAll('.rsel').forEach(rowSelChange);

function switchHpp(t) {
  document.getElementById('panel_manual').style.display = t==='manual' ? '' : 'none';
  document.getElementById('panel_stock').style.display  = t==='manual' ? '' : 'none';
  document.getElementById('panel_auto').style.display   = t==='auto'   ? '' : 'none';
  document.getElementById('card_manual').className = 'hppcard'+(t==='manual'?' hppcard-on':'');
  document.getElementById('card_auto').className   = 'hppcard'+(t==='auto'  ?' hppcard-on':'');
  recalc();
}

function recalc() {
  const price  = parseFloat(document.getElementById('inputPrice').value)||0;
  const type   = document.querySelector('input[name="hpp_type"]:checked')?.value||'manual';
  let hpp = 0;
  if (type==='manual') {
    hpp = parseFloat(document.getElementById('inputHpp')?.value)||0;
  } else {
    document.querySelectorAll('.rrow').forEach(row=>{
      const sel = row.querySelector('.rsel');
      const qty = parseFloat(row.querySelector('input[name="recipe_qty[]"]')?.value)||0;
      const opt = sel?.options[sel.selectedIndex];
      hpp += qty * (parseFloat(opt?.dataset?.cost)||0);
    });
    const el = document.getElementById('autoHppVal');
    if(el) el.textContent='Rp '+hpp.toLocaleString('id-ID');
  }
  const margin = price-hpp;
  const pct    = price>0?Math.round((margin/price)*100):0;

  document.getElementById('sPrice').textContent    = 'Rp '+price.toLocaleString('id-ID');
  document.getElementById('sHpp').textContent      = 'Rp '+hpp.toLocaleString('id-ID');
  document.getElementById('sMargin').textContent   = 'Rp '+margin.toLocaleString('id-ID');
  const p=document.getElementById('sMarginPct');
  p.textContent=pct+'%';
  p.style.color=pct<0?'#dc2626':pct<20?'#d97706':'#16a34a';

  const bar=document.getElementById('mbar');
  bar.style.width=Math.min(100,Math.max(0,pct))+'%';
  bar.style.background=pct<0?'#dc2626':pct<20?'#f59e0b':'#22c55e';

  const msg=document.getElementById('mmsg');
  if(!price)msg.textContent='';
  else if(pct<0) msg.textContent='⚠️ Harga di bawah HPP!';
  else if(pct<15)msg.textContent='⚠️ Margin rendah';
  else if(pct<30)msg.textContent='✅ Margin baik';
  else msg.textContent='🚀 Margin sangat baik!';
}
document.getElementById('inputPrice')?.addEventListener('input',recalc);
recalc();
</script>

<script>
document.querySelector('form[novalidate]').addEventListener('submit', function(e) {
    const name  = document.querySelector('input[name="name"]');
    const price = document.querySelector('input[name="price"]');
    if (!name.value.trim()) {
        e.preventDefault(); name.focus(); alert('Nama produk wajib diisi'); return;
    }
    if (!price.value || parseFloat(price.value) <= 0) {
        e.preventDefault(); price.focus(); alert('Harga wajib diisi'); return;
    }
    const hasVariants = document.getElementById('hasVariants')?.checked;
    if (hasVariants) {
        const labels = document.querySelectorAll('input[name="variant_label[]"]');
        for (const l of labels) {
            if (!l.value.trim()) {
                e.preventDefault(); l.focus(); alert('Nama varian tidak boleh kosong'); return;
            }
        }
    }
});
</script>
</form>