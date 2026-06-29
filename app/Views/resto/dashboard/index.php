<?php
/** @var int $todayOrders */
/** @var float $todayRevenue */
/** @var int $monthOrders */
/** @var float $monthRevenue */
/** @var float $cashToday */
/** @var float $qrisToday */
/** @var array $topMenus */
/** @var array $recentOrders */
/** @var int $totalCustomers */
ob_start();
?>
<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark" style="font-size:24px;">Dashboard Restoran</h2>
    <p class="text-secondary mb-0">Ringkasan penjualan <?= esc(session('restaurant_name') ?? '') ?></p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-secondary small">Pesanan Hari Ini</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $todayOrders ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-secondary small">Omzet Hari Ini</div>
                <div class="fs-5 fw-bold text-success">Rp <?= number_format((float) $todayRevenue, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-secondary small">Pesanan Bulan Ini</div>
                <div class="fs-3 fw-bold text-dark"><?= (int) $monthOrders ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="text-secondary small">Total Pelanggan</div>
                <div class="fs-3 fw-bold text-primary"><?= (int) $totalCustomers ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 fw-semibold">Pembayaran Hari Ini</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-cash-coin text-success me-2"></i>Tunai</span>
                    <strong>Rp <?= number_format((float) $cashToday, 0, ',', '.') ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-qr-code text-primary me-2"></i>QRIS</span>
                    <strong>Rp <?= number_format((float) $qrisToday, 0, ',', '.') ?></strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 fw-semibold">Menu Terlaris (Bulan Ini)</div>
            <div class="card-body p-0">
                <?php if (empty($topMenus)): ?>
                    <p class="text-muted p-3 mb-0">Belum ada data penjualan.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($topMenus as $menu): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= esc($menu['name']) ?></span>
                                <span class="text-muted"><?= (int) ($menu['total_qty'] ?? 0) ?> pcs</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 fw-semibold">Pesanan Terbaru</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><code><?= esc($order['order_code']) ?></code></td>
                            <td><?= esc($order['customer_name']) ?></td>
                            <td>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></td>
                            <td><span class="badge bg-secondary"><?= esc($order['order_status']) ?></span></td>
                            <td class="text-muted small"><?= esc($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= ob_get_clean() ?>
