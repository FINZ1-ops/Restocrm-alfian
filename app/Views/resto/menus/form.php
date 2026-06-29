<?php
/**
 * @var mixed $menu
 * @var mixed $categories
 * @var mixed $errors
 * @var mixed $old
 * @var mixed $text
 */
/**
 * View: resto/menus/form.php
 * Form tambah & edit menu restoran
 * Variabel: $menu (null=tambah), $categories, $errors, $old
 */
ob_start();

$labels = [
    'biasa'       => 'Biasa',
    'best_seller' => 'Best Seller',
    'promo'       => 'Promo',
    'rekomendasi' => 'Rekomendasi',
    'baru'        => 'Baru',
];
$curLabel = $menu['label'] ?? $old['label'] ?? 'biasa';
?>

<div class="mb-4">
    <a href="/resto/menus" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Menu
    </a>
    <h2 class="fw-bold mb-1 mt-2"><?= $menu ? 'Edit Menu' : 'Tambah Menu' ?></h2>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="POST"
                      action="<?= $menu ? '/resto/menus/' . $menu['id'] : '/resto/menus' ?>"
                      enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php if ($menu): ?>
                        <input type="hidden" name="_method" value="PUT">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="<?= esc($menu['name'] ?? $old['name'] ?? '') ?>"
                               required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Kategori -->
                        <div class="col-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= ($menu['category_id'] ?? $old['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Harga -->
                        <div class="col-6">
                            <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price" class="form-control" min="0" step="500"
                                       value="<?= esc($menu['price'] ?? $old['price'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Bahan, keunggulan, atau catatan menu"><?= esc($menu['description'] ?? $old['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($labels as $val => $text): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="label"
                                       id="label_<?= $val ?>" value="<?= $val ?>"
                                       <?= $curLabel === $val ? 'checked' : '' ?>>
                                <label class="form-check-label" for="label_<?= $val ?>"><?= $text ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Foto menu -->
                    <div class="mb-3">
                        <label class="form-label">Foto Menu</label>
                        <?php if (!empty($menu['image'])): ?>
                            <div class="mb-2">
                                <img src="/<?= esc($menu['image']) ?>"
                                     class="rounded-3" style="max-height:100px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        <small class="text-muted">JPG/PNG, maks 2MB. Kosongkan jika tidak ingin mengganti.</small>
                    </div>

                    <!-- Toggle tersedia & aktif -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_available"
                                       id="isAvail" value="1"
                                       <?= (!isset($menu) || $menu['is_available']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isAvail">Tersedia (Stok Ada)</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="isActive" value="1"
                                       <?= (!isset($menu) || $menu['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActive">Tampil di Menu</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $menu ? 'Simpan Perubahan' : 'Tambah Menu' ?>
                        </button>
                        <a href="/resto/menus" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= ob_get_clean() ?>
