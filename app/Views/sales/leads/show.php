<?php
/**
 * @var mixed $lead
 * @var mixed $followups
 */
/**
 * View: sales/leads/show.php
 * Detail lead: info prospek + riwayat follow-up + form follow-up baru
 * Variabel: $lead, $followups
 */
ob_start();

$statusColors = [
    'Baru'          => 'secondary',
    'Dihubungi'     => 'info',
    'Tertarik'      => 'primary',
    'Demo'          => 'warning',
    'Negosiasi'     => 'orange',
    'Deal'          => 'success',
    'Tidak Tertarik'=> 'danger',
];
?>

<div class="mb-4">
    <a href="/sales/leads" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Leads
    </a>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <h2 class="fw-bold mb-0"><?= esc($lead['business_name']) ?></h2>
        <div class="d-flex gap-2">
            <?php 
                $isConverted = strpos($lead['notes'] ?? '', '[Dikonversi ke restoran ID:') !== false;
                if ($lead['status'] === 'Deal' && !$isConverted): 
            ?>
                <a href="/admin/leads/<?= $lead['id'] ?>/convert" class="btn btn-success btn-sm">
                    <i class="bi bi-arrow-right-circle me-1"></i>Konversi ke Restoran
                </a>
            <?php endif; ?>
            <a href="/sales/leads/<?= $lead['id'] ?>/edit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom kiri: info lead + form followup -->
    <div class="col-lg-5">
        <!-- Info Lead -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Info Prospek</span>
                <?php $sc = $statusColors[$lead['status']] ?? 'secondary'; ?>
                <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?>"><?= esc($lead['status']) ?></span>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted fw-normal">Nama Bisnis</dt>
                    <dd class="col-7 fw-semibold"><?= esc($lead['business_name']) ?></dd>

                    <dt class="col-5 text-muted fw-normal">Pemilik</dt>
                    <dd class="col-7"><?= esc($lead['owner_name']) ?></dd>

                    <dt class="col-5 text-muted fw-normal">WhatsApp</dt>
                    <dd class="col-7">
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $lead['whatsapp']) ?>"
                           target="_blank" class="text-success text-decoration-none">
                            <i class="bi bi-whatsapp me-1"></i><?= esc($lead['whatsapp']) ?>
                        </a>
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Kota</dt>
                    <dd class="col-7"><?= esc($lead['city'] ?: '—') ?></dd>

                    <dt class="col-5 text-muted fw-normal">Jenis Bisnis</dt>
                    <dd class="col-7"><?= esc($lead['business_type'] ?: '—') ?></dd>

                    <dt class="col-5 text-muted fw-normal">Sumber</dt>
                    <dd class="col-7"><?= esc($lead['lead_source'] ?: '—') ?></dd>

                    <?php if ($lead['next_followup_date']): ?>
                    <dt class="col-5 text-muted fw-normal">Follow-up</dt>
                    <dd class="col-7 fw-semibold text-warning">
                        <i class="bi bi-calendar-event me-1"></i>
                        <?= date('d M Y', strtotime($lead['next_followup_date'])) ?>
                    </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- Form: catat follow-up baru -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header">Catat Follow-up</div>
            <div class="card-body p-4">
                <form method="POST" action="/sales/leads/<?= $lead['id'] ?>/followup">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="followup_date" class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode <span class="text-danger">*</span></label>
                        <select name="method" class="form-select" required>
                            <?php foreach (['WhatsApp','Telepon','Email','Meeting','Visit'] as $m): ?>
                                <option value="<?= $m ?>"><?= $m ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Hasil percakapan, ketertarikan, dsb."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Follow-up Berikutnya</label>
                        <input type="date" name="next_followup_date" class="form-control">
                    </div>

                    <!-- Update status lead sekaligus -->
                    <div class="mb-3">
                        <label class="form-label">Update Status Lead</label>
                        <select name="status" class="form-select">
                            <?php
                            $statuses = ['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'];
                            foreach ($statuses as $s): ?>
                                <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>>
                                    <?= $s ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>Simpan Follow-up
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom kanan: timeline riwayat followup -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header">Riwayat Follow-up (<?= count($followups) ?>)</div>
            <div class="card-body p-4">
                <?php if (empty($followups)): ?>
                    <p class="text-muted text-center py-3 small">
                        <i class="bi bi-clock-history d-block fs-2 mb-2"></i>
                        Belum ada catatan follow-up
                    </p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($followups as $fu): ?>
                        <div class="d-flex gap-3 mb-4">
                            <!-- Ikon metode -->
                            <div class="flex-shrink-0">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:36px;height:36px;font-size:16px;">
                                    <?php
                                    $icons = [
                                        'WhatsApp' => 'bi-whatsapp',
                                        'Telepon'  => 'bi-telephone',
                                        'Email'    => 'bi-envelope',
                                        'Meeting'  => 'bi-people',
                                        'Visit'    => 'bi-geo-alt',
                                    ];
                                    ?>
                                    <i class="bi <?= $icons[$fu['method']] ?? 'bi-chat' ?>"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-semibold small"><?= esc($fu['method']) ?></span>
                                    <span class="text-muted small">
                                        <?= date('d M Y', strtotime($fu['followup_date'])) ?>
                                    </span>
                                </div>
                                <?php if (!empty($fu['notes'])): ?>
                                    <p class="text-secondary small mb-1"><?= esc($fu['notes']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($fu['next_followup_date'])): ?>
                                    <small class="text-warning">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Next: <?= date('d M Y', strtotime($fu['next_followup_date'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= ob_get_clean() ?>
