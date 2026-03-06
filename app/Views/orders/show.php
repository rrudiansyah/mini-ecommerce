<div class="order-detail">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px">
        <h2 style="margin:0">Pesanan #<?= $order['id'] ?></h2>
        <a id="printButton" href="<?= BASE_URL ?>/orders/print-invoice/<?= $order['id'] ?>" class="btn btn-primary" target="_blank">🖨️ Print Invoice</a>
    </div>

    <div class="order-info">
        <p><strong>Pelanggan:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>No. HP:</strong> <?= $order['customer_phone'] ?? '-' ?></p>
        <p><strong>Catatan:</strong> <?= $order['note'] ? htmlspecialchars($order['note']) : '-' ?></p>
        <p><strong>Waktu:</strong> <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
    </div>

    <table class="table" style="margin-top:20px">
        <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
            <td><?= $item['qty'] ?></td>
            <td>Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td><strong>Rp <?= number_format($order['total'], 0, ',', '.') ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Update Status -->
    <form method="POST" action="<?= BASE_URL ?>/orders/update-status/<?= $order['id'] ?>" style="margin-top:20px">
    <?php echo $csrf_field ?? ''; ?>
        <div class="form-row" style="align-items:flex-end">
            <div class="form-group">
                <label>Update Status Pesanan</label>
                <select name="status">
                    <?php foreach (['pending','proses','selesai','batal'] as $s): ?>
                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>

    <!-- Pembayaran -->
    <div style="background:#f8fafc; padding:20px; border-radius:12px; margin-top:25px; border:1px solid #e2e8f0">
        <h3 style="margin-top:0; margin-bottom:16px">💳 Pembayaran</h3>

        <!-- Info pembayaran saat ini -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px">
            <div>
                <p style="margin:0 0 6px; font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:#64748b; font-weight:700">Status Pembayaran</p>
                <span style="display:inline-block; padding:6px 14px; border-radius:100px; font-size:13px; font-weight:700;
                    background:<?= ($order['payment_status'] ?? 'unpaid') === 'paid' ? '#dcfce7; color:#16a34a' : (($order['payment_status'] ?? 'unpaid') === 'failed' ? '#fee2e2; color:#991b1b' : '#fef3c7; color:#92400e') ?>">
                    <?php if (($order['payment_status'] ?? 'unpaid') === 'paid'): ?>✓ LUNAS
                    <?php elseif (($order['payment_status'] ?? 'unpaid') === 'failed'): ?>✗ GAGAL
                    <?php else: ?>⏳ BELUM DIBAYAR<?php endif; ?>
                </span>
            </div>
            <div>
                <p style="margin:0 0 6px; font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:#64748b; font-weight:700">Metode</p>
                <p style="margin:0; font-weight:600"><?= ($order['payment_method'] ?? null) ? htmlspecialchars($order['payment_method']) : '—' ?></p>
            </div>
        </div>

        <?php if ($order['payment_date'] ?? null): ?>
        <p style="font-size:13px; color:#64748b; margin-bottom:20px">
            📅 Dibayar: <?= date('d M Y H:i', strtotime($order['payment_date'])) ?>
        </p>
        <?php endif; ?>

        <!-- Form catat pembayaran -->
        <form method="POST" action="<?= BASE_URL ?>/orders/record-payment/<?= $order['id'] ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr auto; gap:12px; align-items:flex-end; margin-bottom:16px">
                <div>
                    <label style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; display:block; margin-bottom:6px">Status Pembayaran</label>
                    <select name="payment_status" id="paymentStatus" onchange="onPaymentChange()" required>
                        <option value="unpaid" <?= ($order['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Belum Dibayar</option>
                        <option value="paid"   <?= ($order['payment_status'] ?? '') === 'paid'   ? 'selected' : '' ?>>Lunas</option>
                        <option value="failed" <?= ($order['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Gagal</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; display:block; margin-bottom:6px">Metode Pembayaran</label>
                    <select name="payment_method" id="paymentMethod" onchange="onPaymentChange()">
                        <option value="" <?= empty($order['payment_method']) ? 'selected' : '' ?>>— Pilih Metode —</option>
                        <?php foreach (['Tunai','Transfer','Kartu Kredit','Qris'] as $m): ?>
                        <option value="<?= $m ?>" <?= ($order['payment_method'] ?? '') === $m ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Catat Pembayaran</button>
            </div>

            <!-- Kalkulator Kembalian -->
            <?php include ROOT_PATH . '/app/Views/orders/_change_calculator.php'; ?>
        </form>
    </div>

    <a href="<?= BASE_URL ?>/orders" class="btn" style="margin-top:20px">← Kembali</a>
</div>

<script>
// Set total dari data PHP
updateCalcTotal(<?= (int) $order['total'] ?>);

// Jika sudah ada metode tersimpan, trigger tampil kalkulator
(function() {
    const status = document.getElementById('paymentStatus').value;
    const method = document.getElementById('paymentMethod').value;
    if (status === 'paid') onPaymentChange();
})();

// Auto-print jika URL ada ?print=1
if (window.location.search.indexOf('print=1') !== -1) {
    document.getElementById('printButton').click();
}
</script>

<style>
h2 { color: #1e293b; }
h3 { color: #1e293b; }
select, input { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
</style>
