<?php
/**
 * @var mixed $table
 * @var mixed $errors
 * @var mixed $old
 */
/**
 * View: resto/tables/form.php
 * Form tambah & edit meja restoran
 * QR token di-generate otomatis oleh controller saat tambah baru
 * Variabel: $table (null=tambah), $errors, $old
 */
ob_start(); ?>

<div class="mb-4">
    <a href="/resto/tables" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Meja
    </a>
    <h2 class="fw-bold mb-1 mt-2"><?= $table ? 'Edit Meja' : 'Tambah Meja' ?></h2>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4" style="max-width:480px">
    <div class="card-body p-4">
        <form method="POST"
              action="<?= $table ? '/resto/tables/' . $table['id'] : '/resto/tables' ?>">
            <?= csrf_field() ?>
            <?php if ($table): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Nomor Meja <span class="text-danger">*</span></label>
                <input type="number" name="table_number" class="form-control" min="1"
                       value="<?= esc($table['table_number'] ?? $old['table_number'] ?? '') ?>"
                       required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Area <span class="text-danger">*</span></label>
                <input type="text" name="area_name" class="form-control"
                       value="<?= esc($table['area_name'] ?? $old['area_name'] ?? '') ?>"
                       placeholder="Mis: Indoor, Outdoor, VIP, Rooftop" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kapasitas (orang) <span class="text-danger">*</span></label>
                <input type="number" name="capacity" class="form-control" min="1"
                       value="<?= esc($table['capacity'] ?? $old['capacity'] ?? 4) ?>" required>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active"
                           id="isActive" value="1"
                           <?= (!isset($table) || $table['is_active']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isActive">Meja Aktif</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    <?= $table ? 'Simpan Perubahan' : 'Tambah Meja' ?>
                </button>
                <a href="/resto/tables" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= ob_get_clean() ?>
