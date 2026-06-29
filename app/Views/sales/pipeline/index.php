<?php
/**
 * @var mixed $pipeline
 * @var mixed $statuses
 */
/**
 * View: sales/pipeline/index.php
 * Kanban board berdasarkan status lead — visual pipeline penjualan
 * Variabel: $pipeline (array[status => leads]), $statuses
 */
ob_start();

// Warna kolom per status pipeline
$colColors = [
    'Baru'           => ['bg' => '#f1f5f9', 'border' => '#cbd5e1', 'badge' => 'secondary'],
    'Dihubungi'      => ['bg' => '#eff6ff', 'border' => '#93c5fd', 'badge' => 'info'],
    'Tertarik'       => ['bg' => '#eef2ff', 'border' => '#a5b4fc', 'badge' => 'primary'],
    'Demo'           => ['bg' => '#fffbeb', 'border' => '#fcd34d', 'badge' => 'warning'],
    'Negosiasi'      => ['bg' => '#fff7ed', 'border' => '#fdba74', 'badge' => 'warning'],
    'Deal'           => ['bg' => '#f0fdf4', 'border' => '#86efac', 'badge' => 'success'],
    'Tidak Tertarik' => ['bg' => '#fef2f2', 'border' => '#fca5a5', 'badge' => 'danger'],
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Pipeline Leads</h2>
        <p class="text-secondary mb-0">Visual progres semua prospek Anda</p>
    </div>
    <a href="/sales/leads/new" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Lead
    </a>
</div>

<!-- Kanban — scroll horizontal di mobile -->
<div class="d-flex gap-3 pb-3 overflow-auto" style="align-items:flex-start; min-height:60vh;">
    <?php foreach ($statuses as $status):
        $col   = $colColors[$status] ?? ['bg' => '#f1f5f9', 'border' => '#cbd5e1', 'badge' => 'secondary'];
        $leads = $pipeline[$status] ?? [];
    ?>
    <div class="flex-shrink-0 rounded-4 p-3"
         style="width:230px; background:<?= $col['bg'] ?>; border:1px solid <?= $col['border'] ?>;">

        <!-- Header kolom -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-semibold small"><?= esc($status) ?></span>
            <span class="badge bg-<?= $col['badge'] ?>-subtle text-<?= $col['badge'] ?> rounded-pill">
                <?= count($leads) ?>
            </span>
        </div>

        <!-- Kartu lead -->
        <?php if (empty($leads)): ?>
            <div class="text-muted text-center small py-3" style="opacity:.5;">
                <i class="bi bi-inbox d-block mb-1"></i>Kosong
            </div>
        <?php else: ?>
            <?php foreach ($leads as $lead): ?>
            <a href="/sales/leads/<?= $lead['id'] ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-3 mb-2 p-3"
                     style="transition:box-shadow .15s;"
                     onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
                     onmouseout="this.style.boxShadow=''">

                    <!-- Nama bisnis -->
                    <div class="fw-semibold text-dark small mb-1 text-truncate">
                        <?= esc($lead['business_name']) ?>
                    </div>

                    <!-- Pemilik -->
                    <div class="text-muted" style="font-size:11px;">
                        <i class="bi bi-person me-1"></i><?= esc($lead['owner_name']) ?>
                    </div>

                    <!-- WhatsApp -->
                    <div class="text-muted" style="font-size:11px;">
                        <i class="bi bi-whatsapp me-1 text-success"></i><?= esc($lead['whatsapp']) ?>
                    </div>

                    <!-- Jadwal follow-up jika ada -->
                    <?php if (!empty($lead['next_followup_date'])): ?>
                        <?php
                        $isOverdue = strtotime($lead['next_followup_date']) < strtotime('today');
                        $color     = $isOverdue ? 'text-danger' : 'text-warning';
                        ?>
                        <div class="mt-2 <?= $color ?>" style="font-size:11px;">
                            <i class="bi bi-calendar-event me-1"></i>
                            <?= date('d M', strtotime($lead['next_followup_date'])) ?>
                            <?= $isOverdue ? '(terlambat)' : '' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<?= ob_get_clean() ?>
