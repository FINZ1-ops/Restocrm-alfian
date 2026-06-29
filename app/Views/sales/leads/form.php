<?php
/**
 * @var mixed $lead
 * @var mixed $salesUsers
 * @var mixed $errors
 * @var mixed $old
 */
/**
 * View: sales/leads/form.php
 * Form tambah & edit lead prospek (digunakan sales)
 * Variabel: $lead (null=tambah), $salesUsers, $errors, $old
 */
ob_start();

$allStatuses   = ['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'];
$businessTypes = ['Restoran','Café','Angkringan','Warung Makan','Food Court','UMKM Kuliner','Lainnya'];
$leadSources   = ['Walk-in','Referral','Social Media','Cold Call','WhatsApp','Website','Event'];
$methods       = ['WhatsApp','Telepon','Email','Meeting','Visit'];
?>

<div class="mb-4">
    <a href="/sales/leads" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Leads
    </a>
    <h2 class="fw-bold mb-1 mt-2"><?= $lead ? 'Edit Lead' : 'Tambah Lead' ?></h2>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4" style="max-width:640px">
    <div class="card-body p-4">
        <form method="POST" action="<?= $lead ? '/sales/leads/' . $lead['id'] : '/sales/leads' ?>">
            <?= csrf_field() ?>
            <?php if ($lead): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Nama Bisnis <span class="text-danger">*</span></label>
                <input type="text" name="business_name" class="form-control"
                       value="<?= esc($lead['business_name'] ?? $old['business_name'] ?? '') ?>"
                       required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Pemilik <span class="text-danger">*</span></label>
                <input type="text" name="owner_name" class="form-control"
                       value="<?= esc($lead['owner_name'] ?? $old['owner_name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                    <input type="text" name="whatsapp" class="form-control"
                           value="<?= esc($lead['whatsapp'] ?? $old['whatsapp'] ?? '') ?>"
                           placeholder="08xxxxxxxxxx" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" class="form-control"
                           value="<?= esc($lead['city'] ?? $old['city'] ?? '') ?>"
                           placeholder="Mis: Jakarta">
                </div>
                <div class="col-6">
                    <label class="form-label">Jenis Bisnis</label>
                    <select name="business_type" class="form-select">
                        <option value="">-- Pilih --</option>
                        <?php foreach ($businessTypes as $bt): ?>
                            <option value="<?= $bt ?>"
                                <?= ($lead['business_type'] ?? $old['business_type'] ?? '') === $bt ? 'selected' : '' ?>>
                                <?= $bt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Sumber Lead</label>
                    <select name="lead_source" class="form-select">
                        <option value="">-- Pilih --</option>
                        <?php foreach ($leadSources as $ls): ?>
                            <option value="<?= $ls ?>"
                                <?= ($lead['lead_source'] ?? $old['lead_source'] ?? '') === $ls ? 'selected' : '' ?>>
                                <?= $ls ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach ($allStatuses as $s): ?>
                            <option value="<?= $s ?>"
                                <?= ($lead['status'] ?? $old['status'] ?? 'Baru') === $s ? 'selected' : '' ?>>
                                <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="2"><?= esc($lead['address'] ?? $old['address'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Follow-up Berikutnya</label>
                <input type="date" name="next_followup_date" class="form-control"
                       value="<?= esc($lead['next_followup_date'] ?? $old['next_followup_date'] ?? '') ?>">
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i><?= $lead ? 'Simpan Perubahan' : 'Tambah Lead' ?>
                </button>
                <a href="/sales/leads" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= ob_get_clean() ?>
