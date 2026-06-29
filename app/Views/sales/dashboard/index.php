<?php
/**
 * View: sales/dashboard/index
 * Dashboard Sales — ringkasan pipeline, follow-up hari ini, leads terbaru
 * Variabel: $statusCounts, $totalLeads, $followupToday, $recentLeads
 */

$statusColors = [
    'Baru'           => ['bg' => 'secondary', 'icon' => 'bi-circle'],
    'Dihubungi'      => ['bg' => 'info',      'icon' => 'bi-telephone'],
    'Tertarik'       => ['bg' => 'primary',   'icon' => 'bi-star'],
    'Demo'           => ['bg' => 'warning',   'icon' => 'bi-camera-video'],
    'Negosiasi'      => ['bg' => 'orange',    'icon' => 'bi-chat-dots'],
    'Deal'           => ['bg' => 'success',   'icon' => 'bi-check-circle'],
    'Tidak Tertarik' => ['bg' => 'danger',    'icon' => 'bi-x-circle'],
];
?>

<!-- Sambutan -->
<div class="mb-4">
    <h2 class="fw-bold mb-1">Halo, <?= esc(session('name')) ?>! 👋</h2>
    <p class="text-secondary mb-0">Berikut ringkasan aktivitas leads kamu hari ini.</p>
</div>

<!-- Stat cards ringkasan -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-subtle">
                <i class="bi bi-funnel text-primary"></i>
            </div>
            <div class="stat-value"><?= (int)$totalLeads ?></div>
            <div class="stat-label">Total Leads</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-subtle">
                <i class="bi bi-check-circle text-success"></i>
            </div>
            <div class="stat-value"><?= (int)($statusCounts['Deal'] ?? 0) ?></div>
            <div class="stat-label">Deal</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-subtle">
                <i class="bi bi-calendar-event text-warning"></i>
            </div>
            <div class="stat-value"><?= count($followupToday) ?></div>
            <div class="stat-label">Follow-up Hari Ini</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-subtle">
                <i class="bi bi-star text-info"></i>
            </div>
            <div class="stat-value"><?= (int)($statusCounts['Tertarik'] ?? 0) ?></div>
            <div class="stat-label">Tertarik</div>
        </div>
    </div>
</div>

<!-- Pipeline mini -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Pipeline Saya</span>
        <a href="/sales/pipeline" class="btn btn-sm btn-outline-primary rounded-pill">
            Lihat Kanban <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-4">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($statusCounts as $status => $count): ?>
                <?php $color = $statusColors[$status]['bg'] ?? 'secondary'; ?>
                <a href="/sales/leads?status=<?= urlencode($status) ?>"
                   class="text-decoration-none">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border"
                         style="background:#f8fafc; min-width:120px;">
                        <span class="badge bg-<?= $color ?> rounded-pill"><?= (int)$count ?></span>
                        <span class="small fw-medium text-dark"><?= esc($status) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Follow-up yang harus dilakukan -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-alarm text-warning me-2"></i>Follow-up Menunggu
                </span>
                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">
                    <?= count($followupToday) ?>
                </span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($followupToday)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-check2-all fs-2 d-block mb-2 text-success"></i>
                        <p class="mb-0 small">Semua follow-up sudah selesai!</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($followupToday as $lead): ?>
                            <?php
                            $isOverdue = strtotime($lead['next_followup_date']) < strtotime('today');
                            ?>
                            <a href="/sales/leads/<?= $lead['id'] ?>"
                               class="list-group-item list-group-item-action px-4 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold small"><?= esc($lead['business_name']) ?></div>
                                        <div class="text-muted" style="font-size:12px;">
                                            <i class="bi bi-person me-1"></i><?= esc($lead['owner_name']) ?>
                                        </div>
                                        <div class="text-muted" style="font-size:12px;">
                                            <i class="bi bi-whatsapp text-success me-1"></i><?= esc($lead['whatsapp']) ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-<?= $lead['status'] === 'Deal' ? 'success' : 'primary' ?>-subtle
                                                    text-<?= $lead['status'] === 'Deal' ? 'success' : 'primary' ?>
                                                    mb-1">
                                            <?= esc($lead['status']) ?>
                                        </div>
                                        <div class="<?= $isOverdue ? 'text-danger' : 'text-warning' ?>"
                                             style="font-size:11px;">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            <?= date('d M', strtotime($lead['next_followup_date'])) ?>
                                            <?= $isOverdue ? '(terlambat)' : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($followupToday)): ?>
            <div class="card-footer bg-white text-center py-2">
                <a href="/sales/leads" class="text-primary small text-decoration-none">
                    Lihat semua leads <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Leads terbaru -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-clock-history text-primary me-2"></i>Leads Terbaru
                </span>
                <a href="/sales/leads/new" class="btn btn-sm btn-primary rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i>Tambah
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentLeads)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        <p class="mb-0 small">Belum ada leads.</p>
                        <a href="/sales/leads/new" class="btn btn-primary btn-sm mt-3 rounded-pill">
                            Tambah Lead Pertama
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentLeads as $lead): ?>
                            <?php $sc = $statusColors[$lead['status']]['bg'] ?? 'secondary'; ?>
                            <a href="/sales/leads/<?= $lead['id'] ?>"
                               class="list-group-item list-group-item-action px-4 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold small"><?= esc($lead['business_name']) ?></div>
                                        <div class="text-muted" style="font-size:12px;">
                                            <?= esc($lead['city'] ?: '—') ?> &middot;
                                            <?= esc($lead['business_type'] ?: '—') ?>
                                        </div>
                                    </div>
                                    <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?>
                                                border border-<?= $sc ?> border-opacity-25">
                                        <?= esc($lead['status']) ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>