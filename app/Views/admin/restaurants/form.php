<?php
/**
 * @var mixed $plans
 * @var mixed $errors
 * @var mixed $old
 * @var mixed $field
 */
/**
 * View: admin/restaurants/form
 * Form tambah & edit restoran (diakses Super Admin)
 * Variabel: $restaurant (array|null), $plans (array), $errors (array), $old (array)
 */

$isEdit = !empty($restaurant);
if ($isEdit) {
    $title  = 'Edit Restoran: ' . esc($restaurant['name']);
    $action = '/admin/restaurants/' . $restaurant['id'];
} else {
    $restaurant = [];
    $title      = 'Tambah Restoran';
    $action     = '/admin/restaurants';
}
$val = fn($field, $default = '') => old($field, $restaurant[$field] ?? $old[$field] ?? $default);
?>

<!-- Header -->
<div class="mb-4">
    <a href="/admin/restaurants" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Restoran
    </a>
    <h2 class="fw-bold mb-1 mt-2 text-dark" style="font-size:20px;"><?= $title ?></h2>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger rounded-3 mb-4">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-2"></i>Perbaiki kesalahan:</div>
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $e): ?>
                <li class="small"><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $action ?>">
    <?= csrf_field() ?>
    <?php if ($isEdit): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="row g-4">
        <!-- Kolom kiri: info utama -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-shop me-2 text-primary"></i>Informasi Restoran
                    </h6>
                </div>
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Restoran <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3"
                               value="<?= esc($val('name')) ?>" required autofocus>
                    </div>

                    <?php if (!$isEdit): ?>
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Slug (URL) <span class="text-danger">*</span>
                            <span class="text-muted fw-normal" style="font-size:11px;">huruf kecil, angka, tanda -</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted small">/menu/</span>
                            <input type="text" name="slug" id="slugInput" class="form-control rounded-end-3"
                                   value="<?= esc($val('slug')) ?>" required>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-medium">No. WhatsApp <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-whatsapp text-success"></i>
                            </span>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="<?= esc($val('whatsapp')) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Alamat</label>
                        <textarea name="address" class="form-control rounded-3" rows="2"><?= esc($val('address')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Jam Operasional</label>
                        <input type="text" name="opening_hours" class="form-control rounded-3"
                               value="<?= esc($val('opening_hours')) ?>"
                               placeholder="Contoh: 08.00 - 22.00 WIB">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-3" rows="3"><?= esc($val('description')) ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- Kolom kanan: akun admin + paket (hanya saat tambah baru) -->
        <div class="col-lg-4">

            <?php if (!$isEdit): ?>
            <!-- Akun Admin Resto -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-badge me-2 text-primary"></i>Akun Admin
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Admin <span class="text-danger">*</span></label>
                        <input type="email" name="admin_email" class="form-control rounded-3"
                               value="<?= esc($val('admin_email')) ?>"
                               placeholder="admin@restoran.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Password</label>
                        <input type="text" name="admin_password" class="form-control rounded-3"
                               value="<?= esc($val('admin_password', 'resto123')) ?>">
                        <div class="form-text">Default: <code>resto123</code></div>
                    </div>
                </div>
            </div>

            <!-- Pilih Paket -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Paket Langganan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <select name="plan_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih Paket --</option>
                        <?php foreach ($plans as $plan): ?>
                            <option value="<?= $plan['id'] ?>"
                                <?= $val('plan_id') == $plan['id'] ? 'selected' : '' ?>>
                                <?= esc($plan['name']) ?>
                                — Rp <?= number_format($plan['price_monthly'], 0, ',', '.') ?>/bln
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>Trial 30 hari otomatis diaktifkan.
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Status aktif (hanya saat edit) -->
            <?php if ($isEdit): ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">Status</h6>
                </div>
                <div class="card-body p-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="is_active" id="isActive" value="1"
                               <?= $val('is_active', 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="isActive">Restoran Aktif</label>
                    </div>
                    <div class="form-text">Nonaktifkan agar restoran tidak bisa menerima order.</div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Tombol aksi -->
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium">
            <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Restoran' ?>
        </button>
        <a href="/admin/restaurants" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
    </div>

</form>

<?php if (!$isEdit): ?>
<script>
// Auto-generate slug dari nama restoran
document.querySelector('[name="name"]').addEventListener('input', function () {
    document.getElementById('slugInput').value = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
});
</script>
<?php endif; ?>
