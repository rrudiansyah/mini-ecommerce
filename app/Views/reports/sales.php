<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px">
    <h2>Laporan Penjualan - <?= date('F Y', strtotime($month . '-01')) ?></h2>
    <div>
        <a href="<?= BASE_URL ?>/reports" class="btn">← Kembali ke Analytics</a>
    </div>
</div>

<!-- Month Selector -->
<div style="background:#f5f5f5; padding:15px; border-radius:6px; margin-bottom:20px">
    <form method="GET" action="<?= BASE_URL ?>/reports/sales" style="display:flex; gap:10px; align-items:flex-end">
        <div>
            <label style="font-weight:bold; display:block; margin-bottom:5px">Pilih Bulan</label>
            <input type="month" name="month" value="<?= $month ?>" style="padding:8px; border:1px solid #ddd; border-radius:4px">
        </div>
        <button type="submit" class="btn btn-primary">Lihat</button>
    </form>
</div>

<!-- Daily Sales Chart -->
<div style="background:white; padding:20px; border-radius:8px; margin-bottom:30px; box-shadow:0 1px 3px rgba(0,0,0,0.1)">
    <h3 style="margin-top:0">Penjualan Harian</h3>

    <div style="overflow-x:auto">
        <table class="table" style="margin-bottom:0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Total Pesanan</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalOrders = 0;
                $totalRevenue = 0;
                if (empty($dailySales)):
                ?>
                <tr><td colspan="3" style="text-align:center; padding:20px">Tidak ada data penjualan untuk bulan ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($dailySales as $day):
                        $totalOrders += $day['total_orders'];
                        $totalRevenue += $day['total_revenue'];
                    ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($day['date'])) ?></td>
                        <td><?= $day['total_orders'] ?></td>
                        <td>Rp <?= number_format($day['total_revenue'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background:#f5f5f5; font-weight:bold">
                        <td>Total</td>
                        <td><?= $totalOrders ?></td>
                        <td>Rp <?= number_format($totalRevenue, 0, ',', '.') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Monthly Sales -->
<div style="background:white; padding:20px; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.1)">
    <h3 style="margin-top:0">Perbandingan Penjualan 12 Bulan Terakhir</h3>

    <table class="table" style="margin-bottom:0">
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Total Pesanan</th>
                <th>Total Revenue</th>
                <th>Rata-rata per Pesanan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($monthlySales)): ?>
            <tr><td colspan="4" style="text-align:center; padding:20px">Tidak ada data penjualan.</td></tr>
            <?php else: ?>
                <?php foreach ($monthlySales as $m): ?>
                <tr>
                    <td><?= date('F Y', strtotime($m['month'] . '-01')) ?></td>
                    <td><?= $m['total_orders'] ?></td>
                    <td>Rp <?= number_format($m['total_revenue'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($m['total_orders'] > 0 ? $m['total_revenue'] / $m['total_orders'] : 0, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
h2 { margin-bottom:10px; margin-top:0; }
h3 { color:#333; }
</style>
