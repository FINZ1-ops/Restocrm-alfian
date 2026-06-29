<?php
/**
 * @var mixed $currentStatus
 * @var mixed $statusCounts
 * @var mixed $cnt
 * @var mixed $currentSearch
 * @var mixed $leads
 * @var mixed $pager
 */
$statusColors = [
    'Baru'           => 'secondary',
    'Dihubungi'      => 'info',
    'Tertarik'       => 'primary',
    'Demo'           => 'warning',
    'Negosiasi'      => 'warning text-dark',
    'Deal'           => 'success',
    'Tidak Tertarik' => 'danger',
];
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;">Kelola Leads</h2>
        <p class="text-secondary mb-0" style="font-size:13px;">Kelola semua prospek bisnis Anda</p>
    </div>
    <a href="/admin/leads/new" class="btn btn-primary shadow-sm rounded-pill px-4 fw-medium">
        <i class="bi bi-plus-lg me-1"></i> Tambah Lead
    </a>
</div>

<!-- Status Filter Pills -->
<div class="d-flex gap-2 flex-wrap mb-4">
    <a href="/admin/leads" class="btn btn-sm rounded-pill <?= empty($currentStatus) ? 'btn-dark' : 'btn-outline-secondary' ?> px-3">
        Semua
    </a>
    <?php foreach ($statusCounts ?? [] as $st => $cnt): ?>
        <a href="/admin/leads?status=<?= urlencode($st) ?>"
           class="btn btn-sm rounded-pill <?= ($currentStatus ?? '') === $st ? 'btn-dark' : 'btn-outline-secondary' ?> px-3">
            <?= $st ?> <span class="badge bg-secondary ms-1 rounded-pill"><?= $cnt ?></span>
        </a>
    <?php endforeach; ?>
</div>

<!-- Search -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <?php if (!empty($currentStatus)): ?>
                <input type="hidden" name="status" value="<?= esc($currentStatus) ?>">
            <?php endif; ?>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama bisnis, pemilik, atau WhatsApp..." value="<?= esc($currentSearch ?? '') ?>">
            </div>
            <button class="btn btn-primary px-4 rounded-3">Cari</button>
            <?php if (!empty($currentSearch)): ?>
                <a href="/admin/leads<?= !empty($currentStatus) ? '?status='.urlencode($currentStatus) : '' ?>" class="btn btn-light px-4 rounded-3 text-secondary">Reset</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4 border-0">Informasi Bisnis</th>
                        <th class="border-0">Kontak</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Follow Up</th>
                        <th class="text-end pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($leads)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data lead yang ditemukan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($leads as $lead): ?>
                            <?php $color = $statusColors[$lead['status']] ?? 'secondary'; ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark fs-6"><?= esc($lead['business_name']) ?></div>
                                    <div class="text-muted small">
                                        <?= esc($lead['business_type'] ?? 'Belum ditentukan') ?> 
                                        <?= !empty($lead['city']) ? ' • ' . esc($lead['city']) : '' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($lead['owner_name']) ?></div>
                                    <a href="https://wa.me/<?= preg_replace('/\D/','',$lead['whatsapp']) ?>" target="_blank" class="text-success text-decoration-none small fw-medium">
                                        <i class="bi bi-whatsapp"></i> <?= esc($lead['whatsapp']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= explode(' ', $color)[0] ?> border border-<?= explode(' ', $color)[0] ?> rounded-pill px-3 py-2">
                                        <?= $lead['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($lead['next_followup_date'])): ?>
                                        <?php 
                                        $fu = new DateTime($lead['next_followup_date']); 
                                        $now = new DateTime(); 
                                        $isPast = $fu < $now;
                                        ?>
                                        <div class="<?= $isPast ? 'text-danger fw-bold' : 'text-secondary' ?> small d-flex align-items-center">
                                            <i class="bi <?= $isPast ? 'bi-exclamation-circle-fill' : 'bi-calendar3' ?> me-1"></i>
                                            <?= $fu->format('d M Y') ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="/admin/leads/<?= $lead['id'] ?>" class="btn btn-sm btn-light text-primary rounded-circle" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/leads/<?= $lead['id'] ?>/edit" class="btn btn-sm btn-light text-warning rounded-circle" data-bs-toggle="tooltip" title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php 
                                        $isConverted = strpos($lead['notes'] ?? '', '[Dikonversi ke restoran ID:') !== false;
                                        if ($lead['status'] === 'Deal' && !$isConverted): 
                                        ?>
                                            <a href="/admin/leads/<?= $lead['id'] ?>/convert" class="btn btn-sm btn-success rounded-circle" data-bs-toggle="tooltip" title="Konversi ke Restoran">
                                                <i class="bi bi-check2-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                        <form method="POST" action="/admin/leads/<?= $lead['id'] ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lead ini?')" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button class="btn btn-sm btn-light text-danger rounded-circle" data-bs-toggle="tooltip" title="Hapus Data">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pager)): ?>
            <div class="p-4 d-flex justify-content-center">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
