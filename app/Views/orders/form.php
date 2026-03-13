<form method="POST" action="<?= BASE_URL ?>/orders/store" id="orderForm">
    <?php echo $csrf_field ?? ''; ?>
    <div class="form-row">
        <div class="form-group">
            <label for="customer_name">Nama Pelanggan *</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>
        <div class="form-group">
            <label for="customer_phone">No. HP</label>
            <input type="tel" id="customer_phone" name="customer_phone">
        </div>
    </div>

    <div class="form-group">
        <label for="note">Catatan</label>
        <textarea id="note" name="note" rows="2"></textarea>
    </div>

    <!-- ── PRODUK ─────────────────────────────────────────── -->
    <div class="form-group" style="margin-top:25px">
        <label><strong>Produk</strong></label>

        <!-- Filter Kategori -->
        <div id="categoryFilter" style="display:flex;flex-wrap:wrap;gap:8px;margin:12px 0">
            <button type="button" class="cat-btn active" data-cat="all"
                    onclick="filterCategory('all', this)"
                    style="padding:6px 16px;border-radius:20px;border:1.5px solid #2563a8;background:#2563a8;color:#fff;cursor:pointer;font-size:13px;font-weight:600">
                Semua
            </button>
            <?php foreach ($categories ?? [] as $cat): ?>
            <button type="button" class="cat-btn" data-cat="<?= $cat['id'] ?>"
                    onclick="filterCategory(<?= $cat['id'] ?>, this)"
                    style="padding:6px 16px;border-radius:20px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;cursor:pointer;font-size:13px;font-weight:500">
                <?= htmlspecialchars($cat['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Grid Produk -->
        <div id="productGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-bottom:16px;max-height:280px;overflow-y:auto;padding:4px">
            <?php foreach ($products ?? [] as $p): ?>
            <?php
                $hasVar = !empty($p['has_variants']);
                $variants = $p['variants'] ?? [];
            ?>
            <div class="prod-card" data-cat="<?= $p['category_id'] ?>"
                 style="border:1.5px solid #e5e7eb;border-radius:10px;padding:12px;cursor:pointer;background:#fff;transition:all .15s;user-select:none"
                 onclick="<?= $hasVar ? "openVariantPicker({$p['id']}, '".addslashes($p['name'])."', {$p['price']})" : "addProduct({$p['id']}, '".addslashes($p['name'])."', {$p['price']}, 0, '')" ?>">
                <div style="font-size:11px;color:#9ca3af;margin-bottom:4px"><?= htmlspecialchars($p['category_name'] ?? '') ?></div>
                <div style="font-weight:600;font-size:13px;line-height:1.3;margin-bottom:6px"><?= htmlspecialchars($p['name']) ?></div>
                <div style="font-weight:700;color:#2563a8;font-size:13px">Rp <?= number_format($p['price'],0,',','.') ?></div>
                <?php if ($hasVar): ?>
                <div style="font-size:11px;color:#6b7280;margin-top:4px">🎨 <?= count($variants) ?> varian</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Tabel Pesanan -->
        <table class="table" style="margin-bottom:10px">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="productsTable"></tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right"><strong>Total:</strong></td>
                    <td><strong id="totalPrice">Rp 0</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Modal Pilih Varian -->
    <div id="modalVariant" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:2000;align-items:center;justify-content:center">
        <div style="background:#fff;border-radius:16px;padding:28px;width:400px;max-width:95vw">
            <h3 id="variantModalTitle" style="margin:0 0 16px;font-size:16px;font-weight:700"></h3>
            <div id="variantList" style="display:flex;flex-direction:column;gap:8px;max-height:300px;overflow-y:auto"></div>
            <button type="button" onclick="document.getElementById('modalVariant').style.display='none'"
                    style="margin-top:16px;width:100%;padding:10px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;cursor:pointer">
                Batal
            </button>
        </div>
    </div>

    <!-- Pembayaran -->
    <div class="form-row" style="margin-top:20px">
        <div class="form-group">
            <label>Status Pembayaran</label>
            <select name="payment_status" id="paymentStatus" onchange="onPaymentChange()">
                <option value="unpaid">Belum Dibayar</option>
                <option value="paid">Lunas</option>
                <option value="failed">Gagal</option>
            </select>
        </div>
        <div class="form-group">
            <label>Metode Pembayaran</label>
            <select name="payment_method" id="paymentMethod" onchange="onPaymentChange()">
                <option value="">— Pilih Metode —</option>
                <option value="Tunai">Tunai</option>
                <option value="Transfer">Transfer</option>
                <option value="Kartu Kredit">Kartu Kredit</option>
                <option value="Qris">Qris</option>
            </select>
        </div>
    </div>

    <?php include ROOT_PATH . '/app/Views/orders/_change_calculator.php'; ?>

    <div class="form-actions">
        <a href="<?= BASE_URL ?>/orders" class="btn">Batal</a>
        <button type="submit" class="btn btn-primary">Buat Pesanan</button>
    </div>
</form>

<style>
.cat-btn.active { background:#2563a8!important;color:#fff!important;border-color:#2563a8!important; }
.cat-btn:hover  { border-color:#2563a8!important;color:#2563a8!important; }
.prod-card:hover { border-color:#2563a8!important;background:#eff6ff!important; }
#productsTable input[type="number"] { width:70px; padding:5px; }
</style>

<script>
const products   = <?= json_encode($products ?? []) ?>;
const variantsMap = {};
<?php foreach ($products ?? [] as $p): ?>
<?php if (!empty($p['has_variants']) && !empty($p['variants'])): ?>
variantsMap[<?= $p['id'] ?>] = <?= json_encode($p['variants']) ?>;
<?php endif; ?>
<?php endforeach; ?>

// ── Filter Kategori ──────────────────────────────────────
function filterCategory(catId, btn) {
    // Update tombol aktif
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Filter kartu produk
    document.querySelectorAll('.prod-card').forEach(card => {
        const show = catId === 'all' || card.dataset.cat == catId;
        card.style.display = show ? '' : 'none';
    });
}

// ── Tambah produk ke tabel ───────────────────────────────
function addProduct(id, name, price, variantId, variantLabel) {
    const table = document.getElementById('productsTable');
    const label = variantLabel ? `${name} (${variantLabel})` : name;
    const displayPrice = price;

    // Cek apakah produk+varian sudah ada → tambah qty saja
    let found = false;
    table.querySelectorAll('tr').forEach(row => {
        if (row.dataset.productId == id && row.dataset.variantId == variantId) {
            const qtyInput = row.querySelector('.product-qty');
            qtyInput.value = parseInt(qtyInput.value) + 1;
            updateRowByTr(row);
            found = true;
        }
    });
    if (found) return;

    const row = document.createElement('tr');
    row.dataset.productId = id;
    row.dataset.variantId = variantId || 0;
    row.innerHTML = `
        <td>
            <input type="hidden" name="product_id[]" value="${id}">
            <input type="hidden" name="variant_id[]" value="${variantId || ''}">
            <input type="hidden" name="variant_label[]" value="${variantLabel || ''}">
            <span style="font-size:14px">${label}</span>
        </td>
        <td><span class="product-price">Rp ${fmt(displayPrice)}</span>
            <input type="hidden" name="price_override[]" value="${displayPrice}">
        </td>
        <td><input type="number" name="qty[]" class="product-qty" min="1" value="1"
                   onchange="updateRowByTr(this.closest('tr'))"></td>
        <td><span class="product-subtotal">Rp ${fmt(displayPrice)}</span></td>
        <td><button type="button" class="btn btn-sm btn-danger"
                    onclick="this.closest('tr').remove();updateTotal()">Hapus</button></td>`;
    table.appendChild(row);
    updateTotal();
}

function updateRowByTr(row) {
    const price = parseFloat(row.querySelector('input[name="price_override[]"]').value) || 0;
    const qty   = parseInt(row.querySelector('.product-qty').value) || 0;
    row.querySelector('.product-subtotal').textContent = `Rp ${fmt(price * qty)}`;
    updateTotal();
}

// ── Modal Varian ─────────────────────────────────────────
function openVariantPicker(productId, productName, basePrice) {
    const variants = variantsMap[productId] || [];
    document.getElementById('variantModalTitle').textContent = productName;
    const list = document.getElementById('variantList');
    list.innerHTML = '';
    variants.forEach(v => {
        const price = v.price > 0 ? v.price : basePrice;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;cursor:pointer;font-size:14px;width:100%;text-align:left';
        btn.innerHTML = `<span style="font-weight:600">${v.label}</span><span style="color:#2563a8;font-weight:700">Rp ${fmt(price)}</span>`;
        btn.onmouseover = () => btn.style.borderColor = '#2563a8';
        btn.onmouseout  = () => btn.style.borderColor = '#e5e7eb';
        btn.onclick = () => {
            addProduct(productId, productName, price, v.id, v.label);
            document.getElementById('modalVariant').style.display = 'none';
        };
        list.appendChild(btn);
    });
    document.getElementById('modalVariant').style.display = 'flex';
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('#productsTable tr').forEach(row => {
        const price = parseFloat(row.querySelector('input[name="price_override[]"]')?.value) || 0;
        const qty   = parseInt(row.querySelector('.product-qty')?.value) || 0;
        total += price * qty;
    });
    document.getElementById('totalPrice').textContent = `Rp ${fmt(total)}`;
    updateCalcTotal(total);
}

function fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }

// Submit validation
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('#productsTable tr');
    if (!rows.length) { e.preventDefault(); alert('Tambahkan minimal 1 produk.'); return; }
    const status = document.getElementById('paymentStatus').value;
    const method = document.getElementById('paymentMethod').value;
    if (status === 'paid' && !method) {
        if (!confirm('Metode pembayaran belum dipilih. Lanjutkan?')) { e.preventDefault(); return; }
    }
    if (status === 'paid' && (method === 'Tunai' || method === '')) {
        const paid  = parseCash?.() || 0;
        const total = getCurrentTotal?.() || 0;
        if (paid > 0 && paid < total) {
            e.preventDefault();
            alert(`Uang kurang! Tagihan Rp ${fmt(total)}, diterima Rp ${fmt(paid)}.`);
        }
    }
});

// Tutup modal klik luar
document.getElementById('modalVariant').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
