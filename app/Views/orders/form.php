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

    <div class="form-group" style="margin-top:25px; margin-bottom:10px">
        <label style="margin-bottom:10px"><strong>Produk</strong></label>
        <button type="button" class="btn btn-sm" onclick="addProductRow()">+ Tambah Produk</button>
        <table class="table" style="margin-top:10px; margin-bottom:10px">
            <thead>
                <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th>Aksi</th></tr>
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

    <!-- Kalkulator Kembalian -->
    <?php include ROOT_PATH . '/app/Views/orders/_change_calculator.php'; ?>

    <div class="form-actions">
        <a href="<?= BASE_URL ?>/orders" class="btn">Batal</a>
        <button type="submit" class="btn btn-primary">Buat Pesanan</button>
    </div>
</form>

<style>
#productsTable input[type="number"] { width:70px; padding:5px; }
</style>

<script>
const products = <?= json_encode($products ?? []) ?>;
let rowCount = 0;

function addProductRow() {
    if (!products.length) { alert('Tidak ada produk tersedia.'); return; }
    const table = document.getElementById('productsTable');
    const row   = document.createElement('tr');
    let opts = '<option value="">— Pilih Produk —</option>';
    products.forEach(p => { opts += `<option value="${p.id}" data-price="${p.price}">${p.name} (Rp ${fmt(p.price)})</option>`; });
    row.innerHTML = `
        <td><select name="product_id[]" class="product-select" onchange="updateRow(this)">${opts}</select></td>
        <td><span class="product-price">-</span></td>
        <td><input type="number" name="qty[]" class="product-qty" min="1" value="1" onchange="updateRow(this)"></td>
        <td><span class="product-subtotal">Rp 0</span></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">Hapus</button></td>`;
    table.appendChild(row);
}

function updateRow(el) {
    const row   = el.closest('tr');
    const opt   = row.querySelector('.product-select').options[row.querySelector('.product-select').selectedIndex];
    const price = parseFloat(opt.dataset.price) || 0;
    const qty   = parseInt(row.querySelector('.product-qty').value) || 0;
    row.querySelector('.product-price').textContent    = price > 0 ? `Rp ${fmt(price)}` : '-';
    row.querySelector('.product-subtotal').textContent = `Rp ${fmt(price * qty)}`;
    updateTotal();
}

function removeRow(btn) { btn.closest('tr').remove(); updateTotal(); }

function updateTotal() {
    let total = 0;
    document.querySelectorAll('#productsTable tr').forEach(row => {
        const opt   = row.querySelector('.product-select').options[row.querySelector('.product-select').selectedIndex];
        const price = parseFloat(opt.dataset.price) || 0;
        const qty   = parseInt(row.querySelector('.product-qty').value) || 0;
        total += price * qty;
    });
    document.getElementById('totalPrice').textContent = `Rp ${fmt(total)}`;
    updateCalcTotal(total);
}

function fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }

document.getElementById('orderForm').addEventListener('submit', function(e) {
    const table = document.getElementById('productsTable');
    if (!table.querySelector('tr')) { e.preventDefault(); alert('Tambahkan minimal 1 produk.'); return; }
    let hasProd = false;
    table.querySelectorAll('tr').forEach(row => { if (row.querySelector('.product-select').value) hasProd = true; });
    if (!hasProd) { e.preventDefault(); alert('Pilih produk yang valid.'); return; }
    const status = document.getElementById('paymentStatus').value;
    const method = document.getElementById('paymentMethod').value;
    if (status === 'paid' && !method) {
        if (!confirm('Metode pembayaran belum dipilih. Lanjutkan?')) { e.preventDefault(); return; }
    }
    // Cegah kurang bayar tunai
    if (status === 'paid' && (method === 'Tunai' || method === '')) {
        const paid = parseCash();
        const total = getCurrentTotal();
        if (paid > 0 && paid < total) {
            e.preventDefault();
            alert(`Uang kurang! Tagihan Rp ${fmt(total)}, diterima Rp ${fmt(paid)}.`);
        }
    }
});
</script>
