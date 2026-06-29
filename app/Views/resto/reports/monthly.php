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
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Laporan Bulanan</h2>
        <p class="text-secondary mb-0"><?= date('F Y', strtotime($month . '-01')) ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="/resto/reports/daily" class="btn btn-outline-secondary btn-sm">Harian</a>
        <a href="/resto/reports/monthly" class="btn btn-primary btn-sm">Bulanan</a>
    </div>
</div>

<!-- Filter bulan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2 align-items-end flex-wrap">
            <div>
                <label class="form-label small mb-1">Pilih Bulan</label>
                <input type="month" name="month" class="form-control form-control-sm"
                       value="<?= esc($month) ?>" max="<?= date('Y-m') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>
</div>

<!-- Statistik ringkasan -->
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
            <div class="stat-icon bg-success-subtle"><i class="bi bi-graph-up text-success"></i></div>
            <div class="stat-value" style="font-size:16px">
                Rp <?= number_format((float)($summary->total_revenue ?? 0) / 1000, 0, ',', '.') ?>k
            </div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle"><i class="bi bi-cash text-warning"></i></div>
            <div class="stat-value" style="font-size:16px">
                Rp <?= number_format((float)($summary->cash_revenue ?? 0) / 1000, 0, ',', '.') ?>k
            </div>
            <div class="stat-label">Cash</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-subtle"><i class="bi bi-qr-code text-info"></i></div>
            <div class="stat-value" style="font-size:16px">
                Rp <?= number_format((float)($summary->qris_revenue ?? 0) / 1000, 0, ',', '.') ?>k
            </div>
            <div class="stat-label">QRIS</div>
        </div>
    </div>
</div>

<!-- Grafik pendapatan harian -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header">Grafik Pendapatan Harian</div>
    <div class="card-body">
        <?php if (empty($daily)): ?>
            <div class="text-center text-muted py-4">Tidak ada data bulan ini.</div>
        <?php else: ?>
            <canvas id="revenueChart" height="80"></canvas>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Top menu bulan ini -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header">Menu Terlaris Bulan Ini</div>
            <div class="card-body p-0">
                <?php if (empty($topMenus)): ?>
                    <div class="text-center text-muted py-4">Belum ada data</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($topMenus as $i => $m): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary rounded-circle"
                                      style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;">
                                    <?= $i + 1 ?>
                                </span>
                                <div>
                                    <div class="fw-semibold small"><?= esc($m['name']) ?></div>
                                    <div class="text-muted" style="font-size:11px">
                                        Rp <?= number_format((float)$m['revenue'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary"><?= (int)$m['qty'] ?>x</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabel breakdown harian -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header">Detail Per Hari</div>
            <div class="table-responsive" style="max-height:380px;overflow-y:auto">
                <table class="table table-hover mb-0">
                    <thead class="sticky-top">
                        <tr>
                            <th>Tanggal</th>
                            <th>Pesanan</th>
                            <th>Pendapatan</th>
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
            backgroundColor: 'rgba(99,102,241,0.2)',
            borderColor: '#6366f1',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    // Format angka ribuan di tooltip
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
<?php
$content = ob_get_clean();
echo view('layouts/Layout', ['title' => 'Laporan Bulanan', 'content' => $content]);
?>
