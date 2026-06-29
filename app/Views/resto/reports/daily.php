<?php
/**
 * @var mixed $date
 * @var mixed $summary
 * @var mixed $topItems
 * @var mixed $item
 * @var mixed $orders
 */
/**
 * View: resto/reports/daily
 * Laporan harian — ringkasan, top menu, dan daftar order pada tanggal tertentu
 */
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Laporan Harian</h2>
        <p class="text-secondary mb-0"><?= date('d F Y', strtotime($date)) ?></p>
    </div>
    <!-- Navigasi antar-laporan -->
    <div class="d-flex gap-2">
        <a href="/resto/reports/daily" class="btn btn-primary btn-sm">Harian</a>
        <a href="/resto/reports/monthly" class="btn btn-outline-secondary btn-sm">Bulanan</a>
    </div>
</div>

<!-- Filter tanggal -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label small mb-1">Pilih Tanggal</label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="<?= esc($date) ?>" max="<?= date('Y-m-d') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>
</div>

<!-- Kartu statistik ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle"><i class="bi bi-receipt text-primary"></i></div>
            <div class="stat-value"><?= (int)($summary->total_orders ?? 0) ?></div>
            <div class="stat-label">Total Pesanan</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle"><i class="bi bi-cash-stack text-success"></i></div>
            <div class="stat-value" style="font-size:18px">
                Rp <?= number_format((float)($summary->total_revenue ?? 0) / 1000, 0, ',', '.') ?>k
            </div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle"><i class="bi bi-cash text-warning"></i></div>
            <div class="stat-value" style="font-size:18px">
                Rp <?= number_format((float)($summary->cash_revenue ?? 0) / 1000, 0, ',', '.') ?>k
            </div>
            <div class="stat-label">Total Cash</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-subtle"><i class="bi bi-qr-code text-info"></i></div>
            <div class="stat-value" style="font-size:18px">
                Rp <?= number_format((float)($summary->qris_revenue ?? 0) / 1000, 0, ',', '.') ?>k
            </div>
            <div class="stat-label">Total QRIS</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Top menu hari ini -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header">Menu Terlaris Hari Ini</div>
            <div class="card-body p-0">
                <?php if (empty($topItems)): ?>
                    <div class="text-center text-muted py-5">Belum ada data</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($topItems as $i => $item): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Nomor urut -->
                                <span class="badge bg-primary rounded-circle"
                                      style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;">
                                    <?= $i + 1 ?>
                                </span>
                                <div>
                                    <div class="fw-semibold small"><?= esc($item['name']) ?></div>
                                    <div class="text-muted" style="font-size:11px">
                                        Rp <?= number_format((float)$item['revenue'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary">
                                <?= (int)$item['qty'] ?>x
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Daftar transaksi hari ini -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                Transaksi Hari Ini
                <span class="badge bg-secondary ms-2"><?= count($orders) ?></span>
            </div>
            <div class="table-responsive" style="max-height:420px;overflow-y:auto">
                <table class="table table-hover mb-0">
                    <thead class="sticky-top">
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Tidak ada transaksi hari ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><code class="small"><?= esc($order['order_code']) ?></code></td>
                                <td class="small"><?= esc($order['customer_name']) ?></td>
                                <td class="fw-semibold small">
                                    Rp <?= number_format((float)$order['total'], 0, ',', '.') ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= strtoupper($order['payment_method']) ?></span></td>
                                <td>
                                    <?php
                                    $sc = match($order['payment_status']) {
                                        'Lunas'   => 'success',
                                        'Ditolak' => 'danger',
                                        'Menunggu Konfirmasi' => 'warning',
                                        default   => 'secondary',
                                    };
                                    ?>
                                    <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?>"
                                          style="font-size:10px">
                                        <?= esc($order['payment_status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
echo view('layouts/Layout', ['title' => 'Laporan Harian', 'content' => $content]);
?>
