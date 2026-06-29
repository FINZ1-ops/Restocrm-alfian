<?php
/** @var array $lead */
/** @var array $followups */
$statusColors = [
    'Baru'           => 'secondary',
    'Dihubungi'      => 'info',
    'Tertarik'       => 'primary',
    'Demo'           => 'warning',
    'Negosiasi'      => 'warning text-dark',
    'Deal'           => 'success',
    'Tidak Tertarik' => 'danger',
];
$color = $statusColors[$lead['status']] ?? 'secondary';

// WhatsApp formatting
$waNumber = preg_replace('/\D/', '', $lead['whatsapp'] ?? '');
if (strpos($waNumber, '0') === 0) {
    $waNumber = '62' . substr($waNumber, 1);
}

?>
<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/admin/leads" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
            <i class="bi bi-arrow-left text-secondary fs-5"></i>
        </a>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h2 class="fw-bold mb-0 text-dark fs-3"><?= esc($lead['business_name'] ?? 'Tidak Bernama') ?></h2>
                <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= explode(' ', $color)[0] ?> border border-<?= explode(' ', $color)[0] ?> rounded-pill px-3 py-1">
                    <?= esc($lead['status']) ?>
                </span>
            </div>
            <p class="text-muted mb-0 mt-1 fs-6"><i class="bi bi-person-fill me-1"></i><?= esc($lead['owner_name'] ?? 'Tanpa Nama Pemilik') ?></p>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <a href="/admin/leads/<?= esc($lead['id']) ?>/edit" class="btn btn-light border shadow-sm rounded-pill px-4 fw-medium text-dark">
            <i class="bi bi-pencil me-2"></i> Edit Data
        </a>
        <?php 
            $isConverted = strpos($lead['notes'] ?? '', '[Dikonversi ke restoran ID:') !== false;
            if ($lead['status'] === 'Deal' && !$isConverted): 
        ?>
            <form method="POST" action="/admin/leads/<?= esc($lead['id']) ?>/convert" class="d-inline" onsubmit="return confirm('Proses konversi akan memindahkan prospek ini menjadi Restoran aktif. Lanjutkan?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success shadow-sm rounded-pill px-4 fw-bold">
                    <i class="bi bi-arrow-right-circle-fill me-2"></i> Konversi Restoran
                </button>
            </form>
        <?php endif; ?>
        <form method="POST" action="/admin/leads/<?= esc($lead['id']) ?>" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lead ini?');">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-outline-danger shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-trash me-2"></i> Hapus
            </button>
        </form>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Left Column: Lead Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-info-circle-fill me-2"></i>Profil Prospek</h5>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="mb-4">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:11px; letter-spacing:1px;">Kontak Utama</small>
                    <div class="d-flex align-items-center mt-2">
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3" style="width:45px; height:45px;">
                            <i class="bi bi-whatsapp text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark"><?= esc($lead['whatsapp'] ?? '-') ?></div>
                            <?php if (!empty($waNumber)): ?>
                                <a href="https://wa.me/<?= $waNumber ?>" target="_blank" class="text-success text-decoration-none small fw-bold">Kirim Pesan WA</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <hr class="border-light opacity-50">

                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size:11px; letter-spacing:1px;">Lokasi & Alamat</small>
                    <p class="fw-bold text-dark mb-0 mt-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= !empty($lead['city']) ? esc($lead['city']) : 'Belum diisi' ?></p>
                    <p class="text-secondary small mt-1 ms-4"><?= !empty($lead['address']) ? esc($lead['address']) : '-' ?></p>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:11px; letter-spacing:1px;">Tipe Bisnis</small>
                        <p class="fw-bold text-dark mb-0 mt-1"><i class="bi bi-shop-window text-info me-1"></i> <?= !empty($lead['business_type']) ? esc($lead['business_type']) : '-' ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:11px; letter-spacing:1px;">Sumber Lead</small>
                        <p class="fw-bold text-dark mb-0 mt-1"><i class="bi bi-megaphone-fill text-warning me-1"></i> <?= !empty($lead['lead_source']) ? esc($lead['lead_source']) : '-' ?></p>
                    </div>
                </div>

                <div class="bg-light rounded-4 p-3 mt-4 border border-secondary border-opacity-10">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size:11px; letter-spacing:1px;"><i class="bi bi-journal-text me-1"></i> Catatan Internal</small>
                    <p class="mb-0 small text-dark fst-italic">
                        "<?= !empty($lead['notes']) ? nl2br(esc($lead['notes'])) : 'Tidak ada catatan.' ?>"
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Follow up & History -->
    <div class="col-lg-8">
        <!-- Add Followup Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary bg-opacity-10 border border-primary border-opacity-25">
            <div class="card-body p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-chat-left-text-fill me-2"></i>Catat Aktivitas Follow Up</h5>
                <form method="POST" action="/admin/leads/<?= esc($lead['id']) ?>/followup">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="datetime-local" name="followup_date" id="fuDate" class="form-control bg-white border-0 shadow-sm" value="<?= date('Y-m-d\TH:i') ?>" required>
                                <label for="fuDate">Tanggal & Waktu</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="method" class="form-select bg-white border-0 shadow-sm" id="fuMethod" required>
                                    <option>WhatsApp</option><option>Telepon</option><option>Email</option>
                                    <option>Kunjungan</option><option>Zoom Meeting</option>
                                </select>
                                <label for="fuMethod">Metode Kontak</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="status" class="form-select bg-white border-0 shadow-sm" id="fuStatus">
                                    <option value="">-- Tetap (<?= esc($lead['status']) ?>) --</option>
                                    <?php foreach (['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'] as $s): ?>
                                        <option value="<?= $s ?>"><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="fuStatus">Update Status Pipeline?</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="notes" class="form-control bg-white border-0 shadow-sm" id="fuNotes" style="height: 100px" required placeholder="Apa poin penting dari interaksi ini?"></textarea>
                                <label for="fuNotes">Hasil Pembicaraan / Catatan <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="fw-semibold text-secondary small mb-0">Follow Up Berikutnya:</label>
                                <input type="date" name="next_followup_date" class="form-control form-control-sm bg-white border-0 shadow-sm" style="width: auto;">
                            </div>
                            <button class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Simpan Follow Up
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Followup History -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Riwayat Interaksi</h5>
                <span class="badge bg-secondary rounded-pill px-3"><?= count($followups ?? []) ?> Aktivitas</span>
            </div>
            <div class="card-body p-4 pt-2">
                <?php if (empty($followups)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-x text-muted opacity-25" style="font-size:4rem;"></i>
                        <p class="text-secondary mt-3 mb-0 fw-medium">Belum ada riwayat follow up yang tercatat.</p>
                    </div>
                <?php else: ?>
                    <div class="position-relative ms-3 mt-3">
                        <div class="position-absolute h-100 border-start border-2 border-primary border-opacity-25" style="left: 6px; top: 0;"></div>
                        
                        <?php foreach ($followups as $index => $fu): ?>
                            <div class="position-relative ps-4 <?= $index !== count($followups) - 1 ? 'mb-4' : '' ?>">
                                <!-- Timeline dot -->
                                <div class="position-absolute bg-primary rounded-circle" style="width: 14px; height: 14px; left: 0; top: 8px; box-shadow: 0 0 0 4px white;"></div>
                                
                                <div class="card border-0 bg-light rounded-4 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-white text-primary border border-primary border-opacity-25 rounded-pill px-3 shadow-sm">
                                                <i class="bi <?= strtolower($fu['method']) == 'whatsapp' ? 'bi-whatsapp text-success' : 'bi-chat-text' ?> me-1"></i>
                                                <?= esc($fu['method']) ?>
                                            </span>
                                            <span class="text-muted small fw-medium">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?= date('d M Y - H:i', strtotime($fu['followup_date'])) ?>
                                            </span>
                                        </div>
                                        <p class="mb-3 text-dark fs-6 mt-2"><?= nl2br(esc($fu['notes'])) ?></p>
                                        
                                        <?php if (!empty($fu['next_followup_date'])): ?>
                                            <div class="bg-white rounded px-3 py-2 d-inline-block border shadow-sm">
                                                <small class="text-warning fw-bold">
                                                    <i class="bi bi-alarm-fill me-1"></i> 
                                                    Tindakan Lanjutan: <span class="text-dark ms-1"><?= date('d M Y', strtotime($fu['next_followup_date'])) ?></span>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
