<?php
/** @var int $totalRestaurants */
/** @var int $totalLeads */
/** @var float $monthlyRevenue */
/** @var int $activeSubscriptions */
/** @var array $recentRestaurants */
/** @var array $recentLeads */

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
<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark" style="font-size:24px;">Super Admin Dashboard</h2>
    <p class="text-secondary mb-0">Overview performa platform RESTOCRM</p>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="bi bi-shop fs-6"></i>
                    </div>
                    <div class="text-secondary small fw-semibold text-truncate">Total Restoran</div>
                </div>
                <div class="fs-4 fs-md-3 fw-bold text-dark mt-2"><?= number_format($totalRestaurants) ?></div>
                <div class="text-success" style="font-size: 12px; margin-top: 4px;"><i class="bi bi-arrow-up-short"></i><?= $dealsThisMonth ?> deal bulan ini</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="bi bi-cash-stack fs-6"></i>
                    </div>
                    <div class="text-secondary small fw-semibold text-truncate">Pendapatan SaaS</div>
                </div>
                <div class="fs-5 fs-md-4 fw-bold text-success mt-2 text-truncate">Rp <?= number_format((float) $monthlyRevenue, 0, ',', '.') ?></div>
                <div class="text-success" style="font-size: 12px; margin-top: 4px;"><i class="bi bi-arrow-up-short"></i>+8.5% dari bulan lalu</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="bi bi-funnel fs-6"></i>
                    </div>
                    <div class="text-secondary small fw-semibold text-truncate">Total Leads</div>
                </div>
                <div class="fs-4 fs-md-3 fw-bold text-dark mt-2"><?= number_format($totalLeads) ?></div>
                <div class="text-muted" style="font-size: 12px; margin-top: 4px;">Tersebar di berbagai funnel</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle">
                <i class="bi bi-alarm text-warning"></i>
            </div>
            <div class="stat-value"><?= (int)$followupToday ?></div>
            <div class="stat-label">Follow-up Hari Ini</div>
        </div>
    </div>
    
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="bi bi-check-circle fs-6"></i>
                    </div>
                    <div class="text-secondary small fw-semibold text-truncate">Langganan Aktif</div>
                </div>
                <div class="fs-4 fs-md-3 fw-bold text-dark mt-2"><?= number_format($activeSubscriptions) ?></div>
                <div class="text-danger" style="font-size: 12px; margin-top: 4px;"><i class="bi bi-arrow-down-short"></i>>Trial + Aktif</div>
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

