<?php
/**
 * @var mixed $errors
 * @var mixed $old
 * @var mixed $plan
 * @var mixed $field
 */
/**
 * View: admin/plans/form
 * Form tambah & edit paket langganan
 * Variabel dari controller:
 *   $plan  — data paket (null jika tambah baru, array jika edit)
 *   $errors — array pesan validasi (jika ada)
 *   $old   — input lama untuk repopulate form saat validasi gagal
 */
$plan   = $plan ?? null;
$old    = $old ?? [];
$isEdit = !empty($plan) && is_array($plan);
$title  = $isEdit ? ('Edit Paket: ' . esc($plan['name'])) : 'Tambah Paket Baru';
$action = $isEdit ? ('/admin/plans/' . $plan['id']) : '/admin/plans';

// Helper: ambil nilai lama saat validasi gagal, fallback ke data plan, lalu default
$val = fn($field, $default = '') => old($field, $plan[$field] ?? $old[$field] ?? $default);
?>

<!-- Header -->
<div class="mb-4">
    <a href="/admin/plans" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Paket Langganan
    </a>
    <h2 class="fw-bold mb-0 mt-2 text-dark" style="font-size:20px;"><?= $title ?></h2>
</div>

<!-- Tampilkan error validasi jika ada -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger rounded-3 mb-4">
        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-2"></i>Perbaiki kesalahan berikut:</div>
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
        <!-- Method spoofing untuk PUT karena form HTML hanya support GET/POST -->
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="row g-4">

        <!-- Kolom kiri: info dasar paket -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">Informasi Paket</h6>
                </div>
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Paket <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3"
                               value="<?= esc($val('name')) ?>"
                               placeholder="Contoh: Basic, Pro, Premium" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-3" rows="2"
                                  placeholder="Deskripsi singkat paket ini..."><?= esc($val('description')) ?></textarea>
                    </div>

                    <!-- Harga -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-medium">Harga Bulanan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" name="price_monthly" class="form-control border-start-0 rounded-end-3"
                                       min="0" value="<?= esc($val('price_monthly', 0)) ?>" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium">Harga Tahunan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" name="price_yearly" class="form-control border-start-0 rounded-end-3"
                                       min="0" value="<?= esc($val('price_yearly', 0)) ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Batas fitur -->
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-medium">
                                Maks Meja <span class="text-danger">*</span>
                                <span class="text-muted fw-normal" style="font-size:11px;">(999 = unlimited)</span>
                            </label>
                            <input type="number" name="max_tables" class="form-control rounded-3"
                                   min="1" max="999" value="<?= esc($val('max_tables', 10)) ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium">
                                Maks Menu <span class="text-danger">*</span>
                                <span class="text-muted fw-normal" style="font-size:11px;">(999 = unlimited)</span>
                            </label>
                            <input type="number" name="max_menus" class="form-control rounded-3"
                                   min="1" max="999" value="<?= esc($val('max_menus', 50)) ?>" required>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Kolom kanan: fitur tambahan & status -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">Fitur & Status</h6>
                </div>
                <div class="card-body p-4">

                    <!-- Toggle CRM Pelanggan -->
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox"
                                   name="has_crm" id="hasCrm" value="1"
                                   <?= $val('has_crm', 0) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-medium" for="hasCrm">
                                CRM Pelanggan
                            </label>
                            <div class="text-muted small mt-1">
                                Aktifkan fitur riwayat & data pelanggan per restoran
                            </div>
                        </div>
                    </div>

                    <!-- Toggle aktif/nonaktif -->
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox"
                                   name="is_active" id="isActive" value="1"
                                   <?= $val('is_active', 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-medium" for="isActive">
                                Paket Aktif
                            </label>
                            <div class="text-muted small mt-1">
                                Nonaktifkan agar paket tidak muncul saat pilih langganan
                            </div>
                        </div>
                    </div>

                    <!-- Preview ringkasan paket -->
                    <div class="border rounded-3 p-3" id="planPreview">
                        <p class="text-muted small fw-semibold mb-2 text-uppercase" style="font-size:10px;letter-spacing:.5px;">
                            Preview
                        </p>
                        <div class="fw-bold mb-1" id="previewName">—</div>
                        <div class="small text-muted" id="previewPrice">—</div>
                        <ul class="list-unstyled small mt-2 mb-0" id="previewFeatures"></ul>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Tombol aksi -->
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium">
            <i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Paket' ?>
        </button>
        <a href="/admin/plans" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
    </div>

</form>

<script>
// Update preview secara live saat user mengetik
function updatePreview() {
    const name       = document.querySelector('[name="name"]').value || '—';
    const monthly    = parseInt(document.querySelector('[name="price_monthly"]').value) || 0;
    const maxTables  = document.querySelector('[name="max_tables"]').value;
    const maxMenus   = document.querySelector('[name="max_menus"]').value;
    const hasCrm     = document.getElementById('hasCrm').checked;

    document.getElementById('previewName').textContent = name;
    document.getElementById('previewPrice').textContent =
        'Rp ' + monthly.toLocaleString('id-ID') + ' / bulan';

    const features = [
        (maxTables >= 999 ? 'Unlimited' : maxTables) + ' meja',
        (maxMenus  >= 999 ? 'Unlimited' : maxMenus)  + ' menu',
        hasCrm ? '✓ CRM Pelanggan' : '— Tanpa CRM',
    ];
    document.getElementById('previewFeatures').innerHTML =
        features.map(f => `<li class="text-muted"><i class="bi bi-dot"></i>${f}</li>`).join('');
}

// Pasang event listener ke semua field yang mempengaruhi preview
['name', 'price_monthly', 'max_tables', 'max_menus'].forEach(name => {
    document.querySelector(`[name="${name}"]`)?.addEventListener('input', updatePreview);
});
document.getElementById('hasCrm')?.addEventListener('change', updatePreview);

// Jalankan sekali saat halaman load untuk isi preview dengan data existing
updatePreview();
</script>