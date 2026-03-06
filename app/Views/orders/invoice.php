<div style="padding:0; background:white" class="invoice">
    <div style="text-align:center; margin-bottom:30px; padding-bottom:20px; border-bottom:2px solid #333">
        <h1 style="margin:0; font-size:28px"><?= htmlspecialchars($store['name'] ?? APP_NAME) ?></h1>
        <?php if (!empty($store['address'])): ?>
        <p style="margin:4px 0; color:#555; font-size:14px">📍 <?= htmlspecialchars($store['address']) ?></p>
        <?php endif; ?>
        <?php if (!empty($store['phone'])): ?>
        <p style="margin:4px 0; color:#555; font-size:14px">📞 <?= htmlspecialchars($store['phone']) ?></p>
        <?php endif; ?>
        <p style="margin:8px 0 0 0; color:#888; font-size:12px">Invoice / Pesanan</p>
    </div>

    <!-- Invoice Header -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px; margin-bottom:30px">
        <div>
            <h4 style="margin:0 0 10px 0; color:#333">Informasi Pesanan</h4>
            <p style="margin:5px 0"><strong>No. Pesanan:</strong> #<?= $order['id'] ?></p>
            <p style="margin:5px 0"><strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
            <p style="margin:5px 0"><strong>Status:</strong> <span style="background:#e5e7eb; padding:3px 8px; border-radius:3px"><?= ucfirst($order['status']) ?></span></p>
        </div>
        <div>
            <h4 style="margin:0 0 10px 0; color:#333">Detail Pelanggan</h4>
            <p style="margin:5px 0"><strong>Nama:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p style="margin:5px 0"><strong>No. HP:</strong> <?= $order['customer_phone'] ?? '-' ?></p>
            <?php if ($order['note']): ?>
            <p style="margin:5px 0"><strong>Catatan:</strong> <?= htmlspecialchars($order['note']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Items Table -->
    <h4 style="margin:20px 0 10px 0; color:#333">Detail Pesanan</h4>
    <table style="width:100%; border-collapse:collapse; margin-bottom:30px">
        <thead>
            <tr style="background:#f5f5f5; border-bottom:2px solid #333">
                <th style="padding:10px; text-align:left; border-bottom:2px solid #333">Produk</th>
                <th style="padding:10px; text-align:right; border-bottom:2px solid #333">Harga</th>
                <th style="padding:10px; text-align:center; border-bottom:2px solid #333">Qty</th>
                <th style="padding:10px; text-align:right; border-bottom:2px solid #333">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr style="border-bottom:1px solid #e5e7eb">
                <td style="padding:10px"><?= htmlspecialchars($item['product_name']) ?></td>
                <td style="padding:10px; text-align:right">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                <td style="padding:10px; text-align:center"><?= $item['qty'] ?></td>
                <td style="padding:10px; text-align:right">Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals -->
    <div style="display:grid; grid-template-columns:1fr auto; gap:20px; margin-bottom:30px">
        <div></div>
        <div style="min-width:300px">
            <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #e5e7eb">
                <span><strong>Subtotal:</strong></span>
                <span>Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:10px 0">
                <span style="font-size:18px"><strong>Total:</strong></span>
                <span style="font-size:18px"><strong>Rp <?= number_format($order['total'], 0, ',', '.') ?></strong></span>
            </div>
        </div>
    </div>

    <!-- Payment Info -->
    <div style="background:#f5f5f5; padding:15px; border-radius:4px; margin-bottom:30px">
        <h4 style="margin:0 0 10px 0; color:#333">Informasi Pembayaran</h4>
        <p style="margin:5px 0">
            <strong>Status Pembayaran:</strong>
            <span style="background:<?= ($order['payment_status'] ?? 'unpaid') === 'paid' ? '#dcfce7' : '#fee2e2' ?>; color:<?= ($order['payment_status'] ?? 'unpaid') === 'paid' ? '#166534' : '#991b1b' ?>; padding:4px 8px; border-radius:3px; font-weight:bold">
                <?= ($order['payment_status'] ?? 'unpaid') === 'paid' ? '✓ LUNAS' : '✗ BELUM DIBAYAR' ?>
            </span>
        </p>
        <?php if ($order['payment_method'] ?? null): ?>
        <p style="margin:5px 0"><strong>Metode:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
        <?php endif; ?>
        <?php if ($order['payment_date'] ?? null): ?>
        <p style="margin:5px 0"><strong>Tanggal Pembayaran:</strong> <?= date('d M Y H:i', strtotime($order['payment_date'])) ?></p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div style="text-align:center; padding-top:20px; border-top:2px solid #e5e7eb; color:#666; font-size:12px">
        <p style="margin:5px 0">Terima kasih atas pesanan Anda. Invoice ini dicetak otomatis.</p>
        <p style="margin:5px 0">Tanggal Cetak: <?= date('d M Y H:i') ?></p>
    </div>

    <div class="no-print" style="text-align:center; margin-top:30px">
        <button class="btn btn-primary" onclick="window.print()">🖨️ Cetak Invoice</button>
        <a href="javascript:window.history.back()" class="btn">← Kembali</a>
    </div>
</div>

<style>
.invoice { font-family:Arial, sans-serif; }
.invoice h1, .invoice h4 { font-family:Arial, sans-serif; }
@media print {
    .invoice { background:white; }
    .no-print { display:none !important; }
}
</style>
