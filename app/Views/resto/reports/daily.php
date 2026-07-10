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
<style>
    .report-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px 24px;
        padding-bottom: 24px;
        margin-bottom: 24px;
        border-bottom: 1px solid #e2e8f0;
    }
    @media (min-width: 992px) {
        .report-stats { grid-template-columns: repeat(4, 1fr); }
    }
    .report-stat-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }
    .report-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }
    .report-stat-value.sm { font-size: 1.35rem; }
    .report-stat-icon {
        color: #FFC327;
        font-size: 15px;
        margin-right: 6px;
    }
    .report-section {
        margin-bottom: 32px;
    }
    .report-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
    }
    .report-filter {
        padding-bottom: 20px;
        margin-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Laporan Harian</h2>
        <p class="text-secondary mb-0"><?= date('d F Y', strtotime($date)) ?></p>
    </div>
    <div class="btn-group" role="group" aria-label="Jenis laporan">
        <a href="/resto/reports/daily" class="btn btn-primary btn-sm active">Harian</a>
        <a href="/resto/reports/monthly" class="btn btn-outline-secondary btn-sm">Bulanan</a>
    </div>
</div>

<!-- Filter tanggal -->
<div class="report-filter">
    <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label small mb-1">Pilih Tanggal</label>
            <input type="date" name="date" class="form-control form-control-sm"
                   value="<?= esc($date) ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
    </form>
</div>

<!-- Ringkasan statistik -->
<div class="report-stats">
    <div>
        <div class="report-stat-label"><i class="bi bi-receipt report-stat-icon"></i>TOTAL PESANAN</div>
        <div class="report-stat-value"><?= (int)($summary->total_orders ?? 0) ?></div>
    </div>
    <div>
        <div class="report-stat-label"><i class="bi bi-cash-stack report-stat-icon"></i>TOTAL PENDAPATAN</div>
        <div class="report-stat-value sm">Rp <?= number_format((float)($summary->total_revenue ?? 0) / 1000, 0, ',', '.') ?>k</div>
    </div>
    <div>
        <div class="report-stat-label"><i class="bi bi-cash report-stat-icon"></i>TOTAL CASH</div>
        <div class="report-stat-value sm">Rp <?= number_format((float)($summary->cash_revenue ?? 0) / 1000, 0, ',', '.') ?>k</div>
    </div>
    <div>
        <div class="report-stat-label"><i class="bi bi-qr-code report-stat-icon"></i>TOTAL QRIS</div>
        <div class="report-stat-value sm">Rp <?= number_format((float)($summary->qris_revenue ?? 0) / 1000, 0, ',', '.') ?>k</div>
    </div>
</div>

<div class="row g-4">
    <!-- Top menu hari ini -->
    <div class="col-lg-5">
        <div class="report-section h-100">
            <div class="report-section-title">Menu Terlaris Hari Ini</div>
            <?php if (empty($topItems)): ?>
                <div class="text-center text-muted py-4">Belum ada data</div>
            <?php else: ?>
                <?php foreach ($topItems as $i => $item): ?>
                <div class="d-flex justify-content-between align-items-center py-3 <?= $i > 0 ? 'border-top' : '' ?>" style="border-color:#e2e8f0 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold text-secondary" style="font-size:13px;min-width:20px;">#<?= $i + 1 ?></span>
                        <div>
                            <div class="fw-semibold small"><?= esc($item['name']) ?></div>
                            <div class="text-muted" style="font-size:11px">
                                Rp <?= number_format((float)$item['revenue'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                    <span class="text-secondary small fw-semibold"><?= (int)$item['qty'] ?>x</span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Daftar transaksi hari ini -->
    <div class="col-lg-7">
        <div class="report-section">
            <div class="report-section-title d-flex align-items-center gap-2">
                Transaksi Hari Ini
                <span class="badge bg-secondary"><?= count($orders) ?></span>
            </div>
            <div class="table-responsive" style="max-height:420px;overflow-y:auto">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">KODE</th>
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">PELANGGAN</th>
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">TOTAL</th>
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">BAYAR</th>
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">STATUS</th>
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
<?= ob_get_clean() ?>
