<?php
/**
 * @var mixed $category
 * @var mixed $errors
 * @var mixed $old
 */
/**
 * View: resto/categories/form.php
 * Form tambah & edit kategori menu
 * Variabel: $category (null=tambah), $errors, $old
 */
ob_start(); ?>

<div class="mb-4">
    <a href="/resto/categories" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Kategori
    </a>
    <h2 class="fw-bold mb-1 mt-2"><?= $category ? 'Edit Kategori' : 'Tambah Kategori' ?></h2>
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
              action="<?= $category ? '/resto/categories/' . $category['id'] : '/resto/categories' ?>">
            <?= csrf_field() ?>
            <?php if ($category): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="<?= esc($category['name'] ?? $old['name'] ?? '') ?>"
                       placeholder="Mis: Makanan Berat, Minuman, Dessert"
                       required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Urutan Tampil</label>
                <input type="number" name="sort_order" class="form-control" min="0"
                       value="<?= esc($category['sort_order'] ?? $old['sort_order'] ?? 0) ?>">
                <small class="text-muted">Angka lebih kecil tampil lebih dulu di menu pelanggan</small>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active"
                           id="isActive" value="1"
                           <?= (!isset($category) || $category['is_active']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isActive">Kategori Aktif</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    <?= $category ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
                </button>
                <a href="/resto/categories" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= ob_get_clean() ?>
