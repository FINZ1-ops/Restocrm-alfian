<?php
/**
 * View: resto/staff/form
 * Form tambah & edit staff (kasir/dapur)
 * Variabel: $staff (array|null), $errors (array), $old (array)
 */
$isEdit = !empty($staff);
if ($isEdit) {
    $title  = 'Edit Staff: ' . esc($staff['name']);
    $action = '/resto/staff/' . $staff['id'];
} else {
    $staff  = [];
    $title  = 'Tambah Staff Baru';
    $action = '/resto/staff';
}
$getVal = function($field, $default = '') use ($staff, $old) {
    return old($field, $staff[$field] ?? $old[$field] ?? $default);
};
?>

<div class="mb-4">
    <a href="/resto/staff" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Staff
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

<div class="row g-4">
    <!-- Form -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-person-badge me-2 text-primary"></i>Data Staff
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $action ?>">
                    <?= csrf_field() ?>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="_method" value="PUT">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3"
                               value="<?= esc($getVal('name')) ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3"
                               value="<?= esc($getVal('email')) ?>" required>
                        <div class="form-text">Digunakan untuk login ke sistem.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Password <?= !$isEdit ? '<span class="text-danger">*</span>' : '' ?>
                        </label>
                        <input type="password" name="password" class="form-control rounded-3"
                               <?= !$isEdit ? 'required' : '' ?>
                               placeholder="<?= $isEdit ? 'Kosongkan jika tidak ingin ubah password' : 'Min. 6 karakter' ?>">
                        <?php if ($isEdit): ?>
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select rounded-3" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="kasir" <?= $getVal('role') === 'kasir' ? 'selected' : '' ?>>
                                Kasir — konfirmasi pembayaran
                            </option>
                            <option value="dapur" <?= $getVal('role') === 'dapur' ? 'selected' : '' ?>>
                                Dapur — proses pesanan
                            </option>
                        </select>
                    </div>

                    <?php if ($isEdit): ?>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="is_active" id="isActive" value="1"
                                   <?= $getVal('is_active', 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-medium" for="isActive">Akun Aktif</label>
                            <div class="form-text">Nonaktifkan agar staff tidak bisa login.</div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Staff' ?>
                        </button>
                        <a href="/resto/staff" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Panduan singkat -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-4" style="background:#f8fafc;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Panduan Role
                </h6>
                <div class="mb-4">
                    <span class="badge bg-primary rounded-pill px-3 mb-2">Kasir</span>
                    <ul class="text-muted small mb-0 ps-3">
                        <li>Melihat semua order masuk</li>
                        <li>Konfirmasi pembayaran Cash → Lunas</li>
                        <li>Verifikasi bukti QRIS → Setuju / Tolak</li>
                        <li>Melihat detail struk/order</li>
                    </ul>
                </div>
                <div>
                    <span class="badge bg-warning text-dark rounded-pill px-3 mb-2">Dapur</span>
                    <ul class="text-muted small mb-0 ps-3">
                        <li>Melihat antrian pesanan masuk</li>
                        <li>Update status → Diproses</li>
                        <li>Update status → Siap Disajikan</li>
                        <li>Melihat catatan khusus per item</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>