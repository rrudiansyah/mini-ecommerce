<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
    <h2 style="margin:0;font-size:18px;font-weight:800">Pesanan #<?= $order['id'] ?></h2>
    <a id="printButton" href="<?= BASE_URL ?>/orders/print-invoice/<?= $order['id'] ?>" class="btn btn-primary no-print" target="_blank">🖨️ Print Invoice</a>
    <a href="<?= BASE_URL ?>/orders/print-receipt/<?= $order['id'] ?>" class="btn no-print" target="_blank" style="background:#1a1a1a;color:white">🧾 Print Struk</a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:20px">
    <div class="card">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:700;margin-bottom:4px">Pelanggan</div>
        <div style="font-weight:700"><?= htmlspecialchars($order['customer_name']) ?></div>
        <div style="color:var(--muted);font-size:13px"><?= $order['customer_phone'] ?? '-' ?></div>
    </div>
    <div class="card">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:700;margin-bottom:4px">Waktu</div>
        <div style="font-weight:700"><?= date('d M Y', strtotime($order['created_at'])) ?></div>
        <div style="color:var(--muted);font-size:13px"><?= date('H:i', strtotime($order['created_at'])) ?></div>
    </div>
    <?php if ($order['note']): ?>
    <div class="card">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:700;margin-bottom:4px">Catatan</div>
        <div style="font-size:13px"><?= htmlspecialchars($order['note']) ?></div>
    </div>
    <?php endif; ?>
</div>

<div class="section" style="padding:0;overflow:hidden;margin-bottom:16px">
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($item['product_name']) ?>
                    <?php if (!empty($item['variant_label'])): ?>
                    <span style="font-size:12px;color:#6b7280;display:block">(<?= htmlspecialchars($item['variant_label']) ?>)</span>
                    <?php endif; ?>
                </td>
                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                <td><?= $item['qty'] ?></td>
                <td><strong>Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></strong></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#faf8f4">
                    <td colspan="3" style="font-weight:700;padding:14px">Total</td>
                    <td style="font-weight:800;font-size:16px;padding:14px">Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Update Status -->
<div class="card no-print" style="margin-bottom:16px">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:14px">📋 Update Status Pesanan</h3>
    <form method="POST" action="<?= BASE_URL ?>/orders/update-status/<?= $order['id'] ?>">
        <?php echo $csrf_field ?? ''; ?>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="margin:0;flex:1;min-width:140px">
                <label>Status</label>
                <select name="status">
                    <?php foreach (['pending','proses','selesai','batal'] as $s): ?>
                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
</div>

<!-- Pembayaran -->
<div class="card no-print">
    <h3 style="font-size:14px;font-weight:700;margin-bottom:16px">💳 Pembayaran</h3>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:20px">
        <div>
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:700;margin-bottom:6px">Status Pembayaran</div>
            <span style="display:inline-block;padding:6px 14px;border-radius:100px;font-size:12px;font-weight:700;
                background:<?= ($order['payment_status'] ?? 'unpaid') === 'paid' ? '#dcfce7;color:#16a34a' : (($order['payment_status'] ?? 'unpaid') === 'failed' ? '#fee2e2;color:#991b1b' : '#fef3c7;color:#92400e') ?>">
                <?php if (($order['payment_status'] ?? 'unpaid') === 'paid'): ?>✓ LUNAS
                <?php elseif (($order['payment_status'] ?? 'unpaid') === 'failed'): ?>✗ GAGAL
                <?php else: ?>⏳ BELUM DIBAYAR<?php endif; ?>
            </span>
        </div>
        <div>
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:700;margin-bottom:6px">Metode</div>
            <div style="font-weight:600"><?= ($order['payment_method'] ?? null) ? htmlspecialchars($order['payment_method']) : '—' ?></div>
        </div>
        <?php if ($order['payment_date'] ?? null): ?>
        <div>
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);font-weight:700;margin-bottom:6px">Dibayar</div>
            <div style="font-size:13px"><?= date('d M Y H:i', strtotime($order['payment_date'])) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/orders/record-payment/<?= $order['id'] ?>">
        <?php echo $csrf_field ?? ''; ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;align-items:flex-end;margin-bottom:16px">
            <div class="form-group" style="margin:0">
                <label>Status Pembayaran</label>
                <select name="payment_status" id="paymentStatus" onchange="onPaymentChange()" required>
                    <option value="unpaid" <?= ($order['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Belum Dibayar</option>
                    <option value="paid"   <?= ($order['payment_status'] ?? '') === 'paid'   ? 'selected' : '' ?>>Lunas</option>
                    <option value="failed" <?= ($order['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Gagal</option>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label>Metode Pembayaran</label>
                <select name="payment_method" id="paymentMethod" onchange="onPaymentChange()">
                    <option value="" <?= empty($order['payment_method']) ? 'selected' : '' ?>>— Pilih Metode —</option>
                    <?php foreach (['Tunai','Transfer','Kartu Kredit','Qris'] as $m): ?>
                    <option value="<?= $m ?>" <?= ($order['payment_method'] ?? '') === $m ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Catat Pembayaran</button>
        </div>
        <?php include ROOT_PATH . '/app/Views/orders/_change_calculator.php'; ?>
    </form>
</div>

<div style="margin-top:16px">
    <a href="<?= BASE_URL ?>/orders" class="btn no-print">← Kembali</a>
</div>

<script>
updateCalcTotal(<?= (int) $order['total'] ?>);
(function() {
    if (document.getElementById('paymentStatus').value === 'paid') onPaymentChange();
})();
if (window.location.search.indexOf('print=1') !== -1) {
    document.getElementById('printButton').click();
}
</script>
