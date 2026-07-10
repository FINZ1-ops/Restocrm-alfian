<?php
/**
 * @var mixed $daily
 * @var mixed $month
 * @var mixed $summary
 * @var mixed $topMenus
 * @var mixed $m
 */
/**
 * View: resto/reports/monthly
 * Laporan bulanan — grafik pendapatan harian + top menu + ringkasan
 */
ob_start();

// Siapkan data untuk Chart.js
$chartLabels  = array_column($daily ?? [], 'order_date');
$chartRevenue = array_column($daily ?? [], 'revenue');
$chartOrders  = array_column($daily ?? [], 'total_orders');
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
        <h2 class="fw-bold mb-1 text-dark">Laporan Bulanan</h2>
        <p class="text-secondary mb-0"><?= date('F Y', strtotime($month . '-01')) ?></p>
    </div>
    <div class="btn-group" role="group" aria-label="Jenis laporan">
        <a href="/resto/reports/daily" class="btn btn-outline-secondary btn-sm">Harian</a>
        <a href="/resto/reports/monthly" class="btn btn-primary btn-sm active">Bulanan</a>
    </div>
</div>

<!-- Filter bulan -->
<div class="report-filter">
    <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
        <div>
            <label class="form-label small mb-1">Pilih Bulan</label>
            <input type="month" name="month" class="form-control form-control-sm"
                   value="<?= esc($month) ?>" max="<?= date('Y-m') ?>">
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
        <div class="report-stat-label"><i class="bi bi-graph-up report-stat-icon"></i>TOTAL PENDAPATAN</div>
        <div class="report-stat-value sm">Rp <?= number_format((float)($summary->total_revenue ?? 0) / 1000, 0, ',', '.') ?>k</div>
    </div>
    <div>
        <div class="report-stat-label"><i class="bi bi-cash report-stat-icon"></i>CASH</div>
        <div class="report-stat-value sm">Rp <?= number_format((float)($summary->cash_revenue ?? 0) / 1000, 0, ',', '.') ?>k</div>
    </div>
    <div>
        <div class="report-stat-label"><i class="bi bi-qr-code report-stat-icon"></i>QRIS</div>
        <div class="report-stat-value sm">Rp <?= number_format((float)($summary->qris_revenue ?? 0) / 1000, 0, ',', '.') ?>k</div>
    </div>
</div>

<!-- Grafik pendapatan harian -->
<div class="report-section">
    <div class="report-section-title">Grafik Pendapatan Harian</div>
    <?php if (empty($daily)): ?>
        <div class="text-center text-muted py-4">Tidak ada data bulan ini.</div>
    <?php else: ?>
        <div style="position: relative; height: 280px;">
            <canvas id="revenueChart"></canvas>
        </div>
    <?php endif; ?>
</div>

<div class="row g-4">
    <!-- Top menu bulan ini -->
    <div class="col-lg-5">
        <div class="report-section h-100">
            <div class="report-section-title">Menu Terlaris Bulan Ini</div>
            <?php if (empty($topMenus)): ?>
                <div class="text-center text-muted py-4">Belum ada data</div>
            <?php else: ?>
                <?php foreach ($topMenus as $i => $m): ?>
                <div class="d-flex justify-content-between align-items-center py-3 <?= $i > 0 ? 'border-top' : '' ?>" style="border-color:#e2e8f0 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold text-secondary" style="font-size:13px;min-width:20px;">#<?= $i + 1 ?></span>
                        <div>
                            <div class="fw-semibold small"><?= esc($m['name']) ?></div>
                            <div class="text-muted" style="font-size:11px">
                                Rp <?= number_format((float)$m['revenue'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                    <span class="text-secondary small fw-semibold"><?= (int)$m['qty'] ?>x</span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel breakdown harian -->
    <div class="col-lg-7">
        <div class="report-section">
            <div class="report-section-title">Detail Per Hari</div>
            <div class="table-responsive" style="max-height:380px;overflow-y:auto">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">TANGGAL</th>
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">PESANAN</th>
                            <th class="text-secondary fw-semibold py-2" style="font-size: 11px; letter-spacing: 0.5px; position: sticky; top: 0; background: #f5f5f5; z-index: 1;">PENDAPATAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daily)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        <?php else: ?>
                            <?php foreach ($daily as $d): ?>
                            <tr>
                                <td class="small"><?= date('d M Y', strtotime($d['order_date'])) ?></td>
                                <td><?= (int)$d['total_orders'] ?></td>
                                <td class="fw-semibold small">
                                    Rp <?= number_format((float)$d['revenue'], 0, ',', '.') ?>
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

<?php if (!empty($daily)): ?>
<!-- Chart.js untuk grafik pendapatan -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_map(fn($d) => date('d/m', strtotime($d)), $chartLabels)) ?>,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: <?= json_encode($chartRevenue) ?>,
            backgroundColor: 'rgba(255,195,39,0.35)',
            borderColor: '#ffc327',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + (v/1000).toLocaleString('id-ID') + 'k'
                }
            }
        }
    }
});
</script>
<?php endif; ?>
<?= ob_get_clean() ?>
