<?php
/**
 * @var mixed $restaurant
 * @var mixed $qris
 * @var mixed $errors
 */
/**
 * View: resto/profile/edit.php
 * Form edit profil restoran (nama, alamat, WA, logo, jam buka)
 * + pengaturan QRIS (merchant name, gambar QRIS, toggle aktif)
 * Variabel: $restaurant, $qris, $errors
 */
ob_start(); ?>

<div class="mb-4">
    <h2 class="fw-bold mb-1">Profil Restoran</h2>
    <p class="text-secondary mb-0">Kelola informasi dan pengaturan restoran Anda</p>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="/resto/profile" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">

        <!-- Kolom utama: info restoran -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header">Informasi Restoran</div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Nama Restoran <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="<?= esc($restaurant['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="2"><?= esc($restaurant['address'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-whatsapp text-success"></i></span>
                            <input type="text" name="whatsapp" class="form-control"
                                   value="<?= esc($restaurant['whatsapp'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"><?= esc($restaurant['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Operasional</label>
                        <input type="text" name="opening_hours" class="form-control"
                               value="<?= esc($restaurant['opening_hours'] ?? '') ?>"
                               placeholder="Mis: 08.00 - 22.00 WIB">
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom kanan: logo + QRIS -->
        <div class="col-lg-4">
            <!-- Logo -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header">Logo Restoran</div>
                <div class="card-body p-4 text-center">
                    <?php if (!empty($restaurant['logo'])): ?>
                        <img src="/<?= esc($restaurant['logo']) ?>"
                             class="img-thumbnail mb-3 rounded-3" style="max-height:120px;">
                    <?php else: ?>
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3"
                             style="height:100px;">
                            <i class="bi bi-shop fs-1 text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="logo" class="form-control form-control-sm" accept="image/*">
                    <small class="text-muted d-block mt-1">JPG/PNG, maks 2MB</small>
                </div>
            </div>

            <!-- QRIS -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header">Pengaturan QRIS</div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Nama Merchant</label>
                        <input type="text" name="merchant_name" class="form-control"
                               value="<?= esc($qris['merchant_name'] ?? '') ?>"
                               placeholder="Nama di struk QRIS">
                    </div>
                    <div class="mb-3">
                        <?php if (!empty($qris['qris_image'])): ?>
                            <p class="small text-muted mb-1">QRIS saat ini:</p>
                            <img src="/<?= esc($qris['qris_image']) ?>"
                                 class="img-thumbnail rounded-3 mb-2" style="max-height:150px;">
                        <?php endif; ?>
                        <label class="form-label">Upload Gambar QRIS</label>
                        <input type="file" name="qris_image" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">Gambar QR dari bank/e-wallet Anda</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="qris_active"
                               id="qrisActive" value="1"
                               <?= !empty($qris['is_active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="qrisActive">Aktifkan QRIS</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
        </button>
    </div>
</form>

<?= ob_get_clean() ?>
