<?php
/** @var int $totalRestaurants */
/** @var int $totalLeads */
/** @var float $monthlyRevenue */
/** @var int $activeSubscriptions */
/** @var array $recentRestaurants */
/** @var array $recentLeads */
/** @var int $followupToday */
/** @var int $dealsThisMonth */
/** @var array $dailyLabels */
/** @var array $dailyNewLeads */
/** @var array $dailyNewRestaurants */
/** @var array $dailyNewSubscriptions */
/** @var array $monthlyLabels */
/** @var array $monthlyRevenueData */

// Default dummy data jika belum di-passing dari controller
$totalRestaurants    = $totalRestaurants    ?? 0;
$totalLeads          = $totalLeads          ?? 0;
$activeSubscriptions = $activeSubscriptions ?? 0;
$monthlyRevenue      = $monthlyRevenue      ?? 0;
$followupToday       = $followupToday       ?? 0;
$dealsThisMonth      = $dealsThisMonth      ?? 0;
$recentRestaurants   = $recentRestaurants   ?? [];
$recentLeads         = $recentLeads         ?? [];
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grafik Harian
    const ctxDaily = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctxDaily, {
        type: 'line',
        data: {
            labels: <?= json_encode($dailyLabels ?? []) ?>,
            datasets: [
                {
                    label: 'Leads Baru',
                    data: <?= json_encode($dailyNewLeads ?? []) ?>,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Restoran Baru',
                    data: <?= json_encode($dailyNewRestaurants ?? []) ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Langganan baru',
                    data: <?= json_encode($dailyNewSubscriptions ?? []) ?>,
                    borderColor: '#b18903',
                    backgroundColor: 'rgba(4, 43, 107, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // Grafik Bulanan
    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: <?= json_encode($monthlyLabels ?? []) ?>,
            datasets: [{
                label: 'Pendapatan Langganan (Rp)',
                data: <?= json_encode($monthlyRevenueData ?? []) ?>,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>

<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark" style="font-size:24px;">Super Admin Dashboard</h2>
    <p class="text-secondary mb-0">Overview performa platform RESTOCRM</p>
</div>

<!-- Variasi Stats Cards Row -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Restoran (Gradient Blue) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="text-white-50 small fw-semibold">TOTAL RESTORAN</div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-shop fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold mb-1"><?= number_format($totalRestaurants) ?></div>
                <div class="text-white-50 small"><i class="bi bi-arrow-up-short"></i> <?= $dealsThisMonth ?> deal bulan ini</div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Langganan Aktif (Gradient Teal) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="text-white-50 small fw-semibold">LANGGANAN AKTIF</div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold mb-1"><?= number_format($activeSubscriptions) ?></div>
                <div class="text-white-50 small"><i class="bi bi-activity"></i> Termasuk Trial</div>
            </div>
        </div>
    </div>
    
    <!-- Card 3: Total Leads (Gradient Orange) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="text-white-50 small fw-semibold">TOTAL LEADS</div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-funnel fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold mb-1"><?= number_format($totalLeads) ?></div>
                <div class="text-white-50 small"><i class="bi bi-people"></i> Tersebar di funnel</div>
            </div>
        </div>
    </div>

    <!-- Card 4: Pendapatan SaaS (Gradient Purple) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="text-white-50 small fw-semibold">PENDAPATAN BULAN INI</div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold mb-1 text-truncate">Rp <?= number_format((float) $monthlyRevenue, 0, ',', '.') ?></div>
                <div class="text-white-50 small"><i class="bi bi-graph-up"></i> Hanya yang sudah Lunas</div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Row -->
<div class="row g-4 mb-4">
    <!-- Charts Area -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Laporan Grafik</h5>
                <ul class="nav nav-pills" id="chartTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill py-1 px-3" id="daily-tab" data-bs-toggle="pill" data-bs-target="#daily" type="button" role="tab" aria-selected="true">Harian</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill py-1 px-3" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly" type="button" role="tab" aria-selected="false">Bulanan</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="chartTabsContent">
                    <div class="tab-pane fade show active" id="daily" role="tabpanel">
                        <div style="height: 300px;">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="monthly" role="tabpanel">
                        <div style="height: 300px;">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-up Hari Ini -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white" style="border-top: 4px solid #ef4444 !important;">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-alarm fs-2"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="font-size: 3rem;"><?= (int)$followupToday ?></h3>
                <div class="text-secondary fw-medium mb-3">Leads Perlu Follow-up Hari Ini</div>
                <a href="/admin/leads" class="btn btn-danger rounded-pill px-4 w-100">Lihat Leads</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Restoran Terbaru -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Restoran Terdaftar Terbaru</h5>
                <a href="/admin/restaurants" class="btn btn-sm btn-light rounded-pill px-3 text-primary fw-medium">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th class="border-0 rounded-start">Nama Restoran</th>
                                <th class="border-0">Lokasi</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 rounded-end text-end">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRestaurants as $resto): ?>
                                <tr>
                                    <td class="border-bottom-0 fw-semibold text-dark"><?= esc($resto['name']) ?></td>
                                    <td class="border-bottom-0 text-secondary"><i class="bi bi-geo-alt me-1"></i><?= esc(substr($resto['address'] ?? '-', 0, 30)) ?></td>
                                    <td class="border-bottom-0">
                                        <?= $resto['is_active'] 
                                            ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Aktif</span>'
                                            : '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">Nonaktif</span>' ?>
                                    </td>
                                    <td class="border-bottom-0 text-end text-muted small"><?= date('d M Y', strtotime($resto['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentRestaurants)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data restoran</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads Terpanas -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Pipeline Leads</h5>
                <a href="/admin/leads" class="btn btn-sm btn-light rounded-pill px-3 text-primary fw-medium">Detail</a>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush gap-2">
                    <?php foreach ($recentLeads as $lead): ?>
                        <li class="list-group-item d-flex align-items-center justify-content-between border-0 bg-light rounded-3 p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 150px;"><?= esc($lead['business_name']) ?></div>
                                    <div class="small text-secondary"><?= esc($lead['owner_name']) ?></div>
                                </div>
                            </div>
                            <span class="badge bg-white text-dark border shadow-sm rounded-pill px-2 py-1" style="font-size: 11px;">
                                <?= esc($lead['status']) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($recentLeads)): ?>
                        <li class="list-group-item text-center text-muted border-0 bg-light rounded-3 p-3">Belum ada leads</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .card { transition: transform 0.2s ease-in-out; }
    .card:hover { transform: translateY(-2px); }
    .table > :not(caption) > * > * { padding: 1rem 0.5rem; border-bottom-color: #f1f5f9; }
</style>
