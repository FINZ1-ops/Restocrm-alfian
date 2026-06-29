<?php
/**
 * @var mixed $errors
 * @var mixed $plan
 */
/**
 * View: admin/leads/convert
 * Form konversi lead Deal → Restoran + Admin Resto + Subscription Trial
 * Variabel: $lead (array), $plans (array)
 */

$lead  = $lead  ?? [];
$plans = $plans ?? [];
?>

<!-- Header -->
<div class="mb-4">
    <a href="/admin/leads/<?= $lead['id'] ?>" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail Lead
    </a>
    <h2 class="fw-bold mb-1 mt-2 text-dark" style="font-size:20px;">Konversi Lead ke Restoran</h2>
    <p class="text-secondary mb-0" style="font-size:13px;">
        Proses ini akan membuat restoran baru, akun admin, dan langganan Trial 30 hari secara otomatis.
    </p>
</div>

<!-- Info lead yang akan dikonversi -->
<div class="alert border-0 rounded-4 mb-4" style="background:#f0fdf4;">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white fw-bold"
             style="width:44px;height:44px;font-size:18px;flex-shrink:0;">
            <?= strtoupper(substr($lead['business_name'], 0, 1)) ?>
        </div>
        <div>
            <div class="fw-bold text-dark"><?= esc($lead['business_name']) ?></div>
            <div class="text-muted small">
                <?= esc($lead['owner_name']) ?> &middot;
                <i class="bi bi-whatsapp text-success me-1"></i><?= esc($lead['whatsapp']) ?>
                <?php if (!empty($lead['city'])): ?>
                    &middot; <i class="bi bi-geo-alt me-1"></i><?= esc($lead['city']) ?>
                <?php endif; ?>
            </div>
        </div>
        <span class="badge bg-success ms-auto">Deal</span>
    </div>
</div>

<!-- Error validasi -->
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

<form method="POST" action="/admin/leads/<?= $lead['id'] ?>/convert">
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- Kolom kiri: data restoran -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-shop me-2 text-primary"></i>Data Restoran
                    </h6>
                </div>
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Nama Restoran <span class="text-danger">*</span>
                        </label>
                        <!-- Pre-fill dari data lead -->
                        <input type="text" name="name" class="form-control rounded-3"
                               value="<?= esc(old('name', $lead['business_name'])) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Slug (URL) <span class="text-danger">*</span>
                            <span class="text-muted fw-normal" style="font-size:11px;">
                                hanya huruf kecil, angka, dan tanda -
                            </span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted small">/menu/</span>
                            <input type="text" name="slug" id="slugInput" class="form-control rounded-end-3"
                                   value="<?= esc(old('slug', trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($lead['business_name'])), '-'))) ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">No. WhatsApp <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-whatsapp text-success"></i>
                            </span>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="<?= esc(old('whatsapp', $lead['whatsapp'])) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Alamat</label>
                        <textarea name="address" class="form-control rounded-3" rows="2"><?= esc(old('address', $lead['address'] ?? '')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Jam Operasional</label>
                        <input type="text" name="opening_hours" class="form-control rounded-3"
                               value="<?= esc(old('opening_hours')) ?>"
                               placeholder="Contoh: 08.00 - 22.00 WIB">
                    </div>

                </div>
            </div>
        </div>

        <!-- Kolom kanan: akun admin + paket -->
        <div class="col-lg-5">

            <!-- Akun Admin Resto -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-badge me-2 text-primary"></i>Akun Admin Resto
                    </h6>
                </div>
                <div class="card-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Email Admin <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="admin_email" class="form-control rounded-3"
                               value="<?= esc(old('admin_email')) ?>"
                               placeholder="admin@namarestoran.com" required>
                        <div class="form-text">Email ini digunakan untuk login ke dashboard restoran.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Password</label>
                        <input type="text" name="admin_password" class="form-control rounded-3"
                               value="<?= esc(old('admin_password', 'resto123')) ?>">
                        <div class="form-text">Default: <code>resto123</code> — bisa diubah nanti.</div>
                    </div>

                </div>
            </div>

            <!-- Pilih Paket -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Paket Langganan
                    </h6>
                </div>
                <div class="card-body p-4">

                    <?php if (empty($plans)): ?>
                        <p class="text-muted small mb-0">Belum ada paket aktif. Tambahkan paket dulu.</p>
                    <?php else: ?>
                        <?php foreach ($plans as $loop => $plan): ?>
                        <label class="d-flex align-items-start gap-3 p-3 border rounded-3 mb-2 cursor-pointer
                                      <?= old('plan_id') == $plan['id'] ? 'border-primary bg-primary bg-opacity-10' : '' ?>"
                               style="cursor:pointer;" id="plan-label-<?= $plan['id'] ?>">
                            <input type="radio" name="plan_id" value="<?= $plan['id'] ?>"
                                   class="form-check-input mt-1 flex-shrink-0"
                                   <?= old('plan_id') == $plan['id'] || (!old('plan_id') && $loop === 0) ? 'checked' : '' ?>
                                   onchange="highlightPlan(<?= $plan['id'] ?>)"
                                   required>
                            <div>
                                <div class="fw-semibold"><?= esc($plan['name']) ?></div>
                                <div class="text-muted small">
                                    <?= $plan['max_tables'] >= 999 ? 'Unlimited' : $plan['max_tables'] ?> meja &middot;
                                    <?= $plan['max_menus']  >= 999 ? 'Unlimited' : $plan['max_menus']  ?> menu
                                    <?= $plan['has_crm'] ? ' &middot; CRM ✓' : '' ?>
                                </div>
                                <div class="text-primary fw-semibold small mt-1">
                                    Rp <?= number_format($plan['price_monthly'], 0, ',', '.') ?>/bulan
                                </div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                        <div class="form-text mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Langganan dimulai sebagai <strong>Trial 30 hari</strong>.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <!-- Tombol aksi -->
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold"
                onclick="return confirm('Proses konversi akan membuat restoran, akun admin, dan langganan Trial. Lanjutkan?')">
            <i class="bi bi-arrow-right-circle-fill me-2"></i>Konversi Sekarang
        </button>
        <a href="/admin/leads/<?= $lead['id'] ?>" class="btn btn-outline-secondary rounded-pill px-4">
            Batal
        </a>
    </div>

</form>

<script>
// Highlight border saat pilih paket
function highlightPlan(selectedId) {
    document.querySelectorAll('[id^="plan-label-"]').forEach(el => {
        el.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    const selected = document.getElementById('plan-label-' + selectedId);
    if (selected) {
        selected.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    }
}

// Auto-generate slug dari nama restoran
document.querySelector('[name="name"]').addEventListener('input', function () {
    const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
    document.getElementById('slugInput').value = slug;
});

// Pilih paket pertama secara default
const firstRadio = document.querySelector('[name="plan_id"]');
if (firstRadio) {
    firstRadio.checked = true;
    highlightPlan(firstRadio.value);
}
</script>