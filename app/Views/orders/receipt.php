<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Struk #<?= $order['id'] ?></title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    background: #f0f0f0;
    display: flex;
    justify-content: center;
    padding: 20px;
}

.receipt-wrapper {
    background: white;
    width: 58mm;
    min-height: 100px;
    padding: 4mm 3mm;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
}

/* Header toko */
.store-header {
    text-align: center;
    border-bottom: 1px dashed #333;
    padding-bottom: 6px;
    margin-bottom: 6px;
}

.store-logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
    margin-bottom: 4px;
}

.store-name {
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.3;
}

.store-info {
    font-size: 10px;
    color: #444;
    line-height: 1.6;
    margin-top: 2px;
}

/* Info pesanan */
.order-info {
    font-size: 10px;
    border-bottom: 1px dashed #333;
    padding-bottom: 6px;
    margin-bottom: 6px;
    line-height: 1.8;
}

.order-info .row {
    display: flex;
    justify-content: space-between;
}

/* Items */
.items {
    border-bottom: 1px dashed #333;
    padding-bottom: 6px;
    margin-bottom: 6px;
}

.item {
    margin-bottom: 4px;
}

.item-name {
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.item-detail {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #444;
}

/* Total */
.totals {
    border-bottom: 1px dashed #333;
    padding-bottom: 6px;
    margin-bottom: 6px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    margin-bottom: 2px;
}

.total-row.grand {
    font-size: 13px;
    font-weight: bold;
    margin-top: 4px;
    padding-top: 4px;
    border-top: 1px solid #333;
}

/* Pembayaran */
.payment-info {
    font-size: 10px;
    border-bottom: 1px dashed #333;
    padding-bottom: 6px;
    margin-bottom: 6px;
    line-height: 1.8;
}

.payment-info .row {
    display: flex;
    justify-content: space-between;
}

.badge-paid {
    background: #000;
    color: #fff;
    padding: 1px 5px;
    font-size: 9px;
    font-weight: bold;
}

.badge-unpaid {
    border: 1px solid #000;
    padding: 1px 5px;
    font-size: 9px;
}

/* Footer */
.receipt-footer {
    text-align: center;
    font-size: 10px;
    color: #555;
    line-height: 1.8;
}

.receipt-footer .thank-you {
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 2px;
}

/* Print buttons (tidak ikut dicetak) */
.print-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-top: 16px;
}

.btn-print {
    background: #1a1a1a;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 13px;
    cursor: pointer;
    border-radius: 6px;
    font-family: Arial, sans-serif;
}

.btn-print:hover { background: #333; }

.btn-back {
    background: #e5e7eb;
    color: #333;
    border: none;
    padding: 10px 20px;
    font-size: 13px;
    cursor: pointer;
    border-radius: 6px;
    font-family: Arial, sans-serif;
    text-decoration: none;
    display: inline-block;
}

/* Direct print button */
.btn-direct {
    background: #2563eb;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 13px;
    cursor: pointer;
    border-radius: 6px;
    font-family: Arial, sans-serif;
}

.separator {
    text-align: center;
    letter-spacing: 2px;
    font-size: 10px;
    color: #999;
    margin: 3px 0;
}

/* ── PRINT STYLES ── */
@media print {
    body {
        background: white;
        padding: 0;
    }

    .receipt-wrapper {
        box-shadow: none;
        width: 58mm;
        padding: 2mm 2mm;
    }

    .print-actions {
        display: none !important;
    }

    /* Thermal printer: no color ink, just black */
    * {
        color: black !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .badge-paid {
        background: black !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    @page {
        size: 58mm auto;
        margin: 0;
    }
}
</style>
</head>
<body>

<div>
    <!-- ══ RECEIPT ══ -->
    <div class="receipt-wrapper" id="receipt">

        <!-- Header Toko -->
        <div class="store-header">
            <?php if (!empty($store['logo'])): ?>
            <img src="<?= BASE_URL . '/' . htmlspecialchars($store['logo']) ?>" class="store-logo" alt="logo">
            <br>
            <?php endif; ?>
            <div class="store-name"><?= htmlspecialchars($store['name'] ?? 'Toko') ?></div>
            <?php if (!empty($store['address'])): ?>
            <div class="store-info"><?= htmlspecialchars($store['address']) ?></div>
            <?php endif; ?>
            <?php if (!empty($store['phone'])): ?>
            <div class="store-info">Telp: <?= htmlspecialchars($store['phone']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Info Pesanan -->
        <div class="order-info">
            <div class="row"><span>No. Pesanan</span><span>#<?= $order['id'] ?></span></div>
            <div class="row"><span>Tanggal</span><span><?= date('d/m/Y', strtotime($order['created_at'])) ?></span></div>
            <div class="row"><span>Jam</span><span><?= date('H:i', strtotime($order['created_at'])) ?></span></div>
            <div class="row"><span>Pelanggan</span><span><?= htmlspecialchars($order['customer_name']) ?></span></div>
            <?php if (!empty($order['customer_phone'])): ?>
            <div class="row"><span>No. HP</span><span><?= htmlspecialchars($order['customer_phone']) ?></span></div>
            <?php endif; ?>
        </div>

        <!-- Items -->
        <div class="items">
            <?php foreach ($items as $item): ?>
            <div class="item">
                <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                <div class="item-detail">
                    <span><?= $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                    <span>Rp <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Total -->
        <div class="totals">
            <div class="total-row grand">
                <span>TOTAL</span>
                <span>Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Pembayaran -->
        <div class="payment-info">
            <div class="row">
                <span>Status</span>
                <span>
                    <?php if (($order['payment_status'] ?? 'unpaid') === 'paid'): ?>
                    <span class="badge-paid">LUNAS</span>
                    <?php elseif (($order['payment_status'] ?? 'unpaid') === 'failed'): ?>
                    <span class="badge-unpaid">GAGAL</span>
                    <?php else: ?>
                    <span class="badge-unpaid">BELUM BAYAR</span>
                    <?php endif; ?>
                </span>
            </div>
            <?php if (!empty($order['payment_method'])): ?>
            <div class="row"><span>Metode</span><span><?= htmlspecialchars($order['payment_method']) ?></span></div>
            <?php endif; ?>
            <?php if (!empty($order['payment_date'])): ?>
            <div class="row"><span>Dibayar</span><span><?= date('d/m/Y H:i', strtotime($order['payment_date'])) ?></span></div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="thank-you">Terima Kasih!</div>
            <div>Simpan struk ini sebagai</div>
            <div>bukti pembayaran Anda.</div>
            <div class="separator">- - - - - - - - - - - -</div>
            <div style="font-size:9px">Dicetak: <?= date('d/m/Y H:i') ?></div>
        </div>

    </div><!-- end receipt-wrapper -->

    <!-- Tombol Aksi -->
    <div class="print-actions">
        <button class="btn-print" onclick="window.print()">🖨️ Print Struk</button>
        <button class="btn-direct" onclick="directPrint()">⚡ Direct Print</button>
        <a href="javascript:window.history.back()" class="btn-back">← Kembali</a>
    </div>
</div>

<script>
// Direct print via WebUSB / system dialog
function directPrint() {
    // Cek apakah browser support WebUSB
    if ('usb' in navigator) {
        // Gunakan ESC/POS commands untuk thermal printer
        printESCPOS();
    } else {
        // Fallback ke browser print dengan instruksi
        alert('Browser tidak mendukung direct print.\nPastikan:\n1. Set ukuran kertas = 58mm / Custom\n2. Hilangkan header & footer\n3. Set margin = None\n\nKlik OK untuk lanjut ke print dialog.');
        window.print();
    }
}

// ESC/POS direct print via WebUSB
async function printESCPOS() {
    try {
        const device = await navigator.usb.requestDevice({
            filters: [{ classCode: 7 }] // USB Printer class
        });

        await device.open();
        await device.selectConfiguration(1);
        await device.claimInterface(0);

        const encoder = new TextEncoder();
        const storeName = <?= json_encode($store['name'] ?? 'Toko') ?>;
        const storeAddr = <?= json_encode($store['address'] ?? '') ?>;
        const storePhone = <?= json_encode($store['phone'] ?? '') ?>;
        const orderId = <?= $order['id'] ?>;
        const customerName = <?= json_encode($order['customer_name']) ?>;
        const customerPhone = <?= json_encode($order['customer_phone'] ?? '') ?>;
        const orderDate = <?= json_encode(date('d/m/Y H:i', strtotime($order['created_at']))) ?>;
        const items = <?= json_encode(array_map(fn($i) => [
            'name' => $i['product_name'],
            'qty'  => $i['qty'],
            'price'=> $i['price'],
            'sub'  => $i['price'] * $i['qty']
        ], $items)) ?>;
        const total = <?= $order['total'] ?>;
        const payStatus = <?= json_encode(($order['payment_status'] ?? 'unpaid') === 'paid' ? 'LUNAS' : 'BELUM BAYAR') ?>;
        const payMethod = <?= json_encode($order['payment_method'] ?? '') ?>;

        // Format rupiah
        const rp = (n) => 'Rp ' + n.toLocaleString('id-ID');
        // Pad string kanan
        const pad = (str, len) => str.toString().padEnd(len).substring(0, len);
        // Pad string kiri
        const lpad = (str, len) => str.toString().padStart(len).substring(0, len);
        // Baris dengan kiri dan kanan
        const row = (left, right, width=32) => {
            const space = width - left.length - right.length;
            return left + ' '.repeat(Math.max(1, space)) + right + '\n';
        };

        let text = '';
        const ESC = '\x1B';
        const GS  = '\x1D';

        // Init printer
        text += ESC + '@';                    // Reset
        text += ESC + 'a' + '\x01';          // Center align
        text += ESC + '!' + '\x38';          // Double width+height bold
        text += storeName + '\n';
        text += ESC + '!' + '\x00';          // Normal
        if (storeAddr) text += storeAddr + '\n';
        if (storePhone) text += 'Telp: ' + storePhone + '\n';
        text += '-'.repeat(32) + '\n';

        // Left align
        text += ESC + 'a' + '\x00';
        text += row('No. Pesanan', '#' + orderId);
        text += row('Tanggal', orderDate);
        text += row('Pelanggan', customerName);
        if (customerPhone) text += row('No. HP', customerPhone);
        text += '-'.repeat(32) + '\n';

        // Items
        items.forEach(item => {
            text += item.name.substring(0, 32) + '\n';
            text += row('  ' + item.qty + ' x ' + rp(item.price), rp(item.sub));
        });

        text += '-'.repeat(32) + '\n';

        // Total — bold large
        text += ESC + 'a' + '\x02';          // Right align
        text += ESC + '!' + '\x30';          // Double width bold
        text += 'TOTAL: ' + rp(total) + '\n';
        text += ESC + '!' + '\x00';          // Normal
        text += ESC + 'a' + '\x00';          // Left align

        text += '-'.repeat(32) + '\n';
        text += row('Status', payStatus);
        if (payMethod) text += row('Metode', payMethod);
        text += '-'.repeat(32) + '\n';

        // Footer center
        text += ESC + 'a' + '\x01';
        text += 'Terima Kasih!\n';
        text += 'Simpan struk sebagai bukti.\n';
        text += '\n\n\n';

        // Cut paper
        text += GS + 'V' + '\x41' + '\x03';

        // Send to printer
        const data = encoder.encode(text);
        const endpointOut = device.configuration.interfaces[0].alternates[0].endpoints
            .find(e => e.direction === 'out');

        await device.transferOut(endpointOut.endpointNumber, data);
        await device.close();

    } catch (err) {
        if (err.name !== 'NotFoundError') {
            console.error(err);
            alert('Gagal connect ke printer: ' + err.message + '\n\nPastikan printer USB terhubung dan driver terinstall.');
        }
    }
}

// Auto print jika ada param ?autoprint=1
if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
    setTimeout(() => window.print(), 500);
}
</script>

</body>
</html>
