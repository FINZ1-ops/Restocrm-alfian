<?php
/** @var array $lead */
/** @var array $followups */

$isEdit = !empty($lead);
$action = $isEdit ? '/admin/leads/' . $lead['id'] : '/admin/leads';
$method = $isEdit ? 'PUT' : 'POST';

$statuses = ['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'];
$sources  = ['Referral','Instagram','Facebook','Google','Pameran','Cold Call','Lainnya'];
$bizTypes = ['Restoran','Cafe','Angkringan','Warung','Food Court','Catering','Lainnya'];

$v = fn($k) => old($k, $isEdit ? ($lead[$k] ?? '') : '');
?>
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10">
        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="/admin/leads" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="bi bi-arrow-left text-secondary fs-5"></i>
            </a>
            <div>
                <h2 class="fw-bold mb-0 text-dark fs-3"><?= $isEdit ? 'Edit Prospek' : 'Prospek Baru' ?></h2>
                <p class="text-muted mb-0 fs-6">Lengkapi informasi prospek bisnis dengan detail</p>
            </div>
        </div>

        <!-- Alert Errors -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm border-0 rounded-4 mb-4 d-flex align-items-start gap-3 p-4">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-danger mt-1"></i>
                <div>
                    <h5 class="fw-bold mb-2">Terjadi Kesalahan!</h5>
                    <ul class="mb-0 ps-3 text-danger-emphasis">
                        <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $action ?>">
            <?= csrf_field() ?>
            <?php if ($isEdit): ?><input type="hidden" name="_method" value="<?= $method ?>"/><?php endif; ?>

            <!-- Business Info Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-shop me-2"></i>Informasi Bisnis</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="business_name" class="form-control bg-light border-0" id="bName" value="<?= esc($v('business_name')) ?>" placeholder="Nama Bisnis" required>
                                <label for="bName">Nama Bisnis <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="business_type" class="form-select bg-light border-0" id="bType">
                                    <option value="">-- Pilih Tipe --</option>
                                    <?php foreach ($bizTypes as $bt): ?>
                                        <option value="<?= $bt ?>" <?= $v('business_type') === $bt ? 'selected' : '' ?>><?= $bt ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="bType">Tipe Bisnis</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="owner_name" class="form-control bg-light border-0" id="oName" value="<?= esc($v('owner_name')) ?>" placeholder="Pemilik" required>
                                <label for="oName">Nama Pemilik <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="whatsapp" class="form-control bg-light border-0" id="wa" value="<?= esc($v('whatsapp')) ?>" placeholder="WhatsApp" required>
                                <label for="wa">Nomor WhatsApp <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="city" class="form-control bg-light border-0" id="city" value="<?= esc($v('city')) ?>" placeholder="Kota">
                                <label for="city">Kota / Kabupaten</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="lead_source" class="form-select bg-light border-0" id="src">
                                    <option value="">-- Pilih Sumber --</option>
                                    <?php foreach ($sources as $src): ?>
                                        <option value="<?= $src ?>" <?= $v('lead_source') === $src ? 'selected' : '' ?>><?= $src ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="src">Sumber Lead</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="address" class="form-control bg-light border-0" id="addr" style="height: 100px" placeholder="Alamat"><?= esc($v('address')) ?></textarea>
                                <label for="addr">Alamat Lengkap</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-warning mb-0"><i class="bi bi-funnel-fill me-2"></i>Status & Penugasan</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="status" class="form-select bg-light border-0" id="status" required>
                                    <?php foreach ($statuses as $st): ?>
                                        <option value="<?= $st ?>" <?= $v('status') === $st ? 'selected' : '' ?>><?= $st ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="status">Status Pipeline <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="assigned_to" class="form-select bg-light border-0" id="assign">
                                    <option value="">-- Belum Ditugaskan --</option>
                                    <?php foreach ($salesUsers ?? [] as $su): ?>
                                        <option value="<?= $su['id'] ?>" <?= $v('assigned_to') == $su['id'] ? 'selected' : '' ?>>
                                            <?= esc($su['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="assign">Tugaskan ke Sales</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="date" name="next_followup_date" class="form-control bg-light border-0" id="fuDate" value="<?= esc($v('next_followup_date')) ?>">
                                <label for="fuDate">Jadwal Follow Up</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="notes" class="form-control bg-light border-0" id="notes" style="height: 100px" placeholder="Catatan"><?= esc($v('notes')) ?></textarea>
                                <label for="notes">Catatan Tambahan Internal</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-3 justify-content-end mb-5">
                <a href="/admin/leads" class="btn btn-light btn-lg px-5 rounded-pill shadow-sm text-secondary fw-bold">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm fw-bold">
                    <i class="bi bi-save me-2"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Tambahkan Lead' ?>
                </button>
            </div>
        </form>
    </div>
</div>
