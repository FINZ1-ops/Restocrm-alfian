<?php
/**
 * @var mixed $menus
 * @var mixed $categories
 */
/**
 * View: resto/menus/index.php
 * Daftar semua menu restoran dengan filter per kategori
 * Variabel: $menus, $categories, $currentCat
 */
ob_start();

// Warna badge per label menu
$labelColors = [
    'best_seller' => 'warning',
    'promo'       => 'danger',
    'rekomendasi' => 'info',
    'baru'        => 'success',
    'biasa'       => 'secondary',
];

// Buat map id → nama kategori untuk lookup cepat
$catMap = [];
foreach ($categories as $c) {
    $catMap[$c['id']] = $c['name'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Manajemen Menu</h2>
        <p class="text-secondary mb-0"><?= count($menus) ?> menu terdaftar</p>
    </div>
    <a href="/resto/menus/new" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Menu
    </a>
</div>

<!-- Filter cepat per kategori -->
<?php if (!empty($categories)): ?>
<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="/resto/menus"
       class="btn btn-sm <?= !$currentCat ? 'btn-primary' : 'btn-outline-secondary' ?>">
        Semua
    </a>
    <?php foreach ($categories as $cat): ?>
    <a href="/resto/menus?category=<?= $cat['id'] ?>"
       class="btn btn-sm <?= $currentCat == $cat['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>">
        <?= esc($cat['name']) ?>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:56px">Foto</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Label</th>
                    <th>Status</th>
                    <th style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($menus)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-cup-hot fs-2 d-block mb-2"></i>
                        Belum ada menu. <a href="/resto/menus/new">Tambah sekarang</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                <tr>
                    <!-- Thumbnail foto menu -->
                    <td>
                        <?php if (!empty($menu['image'])): ?>
                            <img src="/<?= esc($menu['image']) ?>" class="rounded-3"
                                 style="width:44px;height:44px;object-fit:cover;">
                        <?php else: ?>
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                 style="width:44px;height:44px;">
                                <i class="bi bi-image text-muted small"></i>
                            </div>
                        <?php endif; ?>
                    </td>

                    <!-- Nama + deskripsi singkat -->
                    <td>
                        <div class="fw-semibold"><?= esc($menu['name']) ?></div>
                        <?php if (!empty($menu['description'])): ?>
                            <small class="text-muted">
                                <?= esc(mb_substr($menu['description'], 0, 55)) ?>
                                <?= mb_strlen($menu['description']) > 55 ? '…' : '' ?>
                            </small>
                        <?php endif; ?>
                    </td>

                    <td class="text-muted small"><?= esc($catMap[$menu['category_id']] ?? '—') ?></td>
                    <td class="fw-semibold">Rp&nbsp;<?= number_format((float)$menu['price'], 0, ',', '.') ?></td>

                    <!-- Label -->
                    <td>
                        <?php $lc = $labelColors[$menu['label'] ?? 'biasa'] ?? 'secondary'; ?>
                        <span class="badge bg-<?= $lc ?>-subtle text-<?= $lc ?>">
                            <?= esc(ucfirst(str_replace('_', ' ', $menu['label'] ?? 'biasa'))) ?>
                        </span>
                    </td>

                    <!-- Status ketersediaan -->
                    <td>
                        <?php if ($menu['is_active'] && $menu['is_available']): ?>
                            <span class="badge bg-success-subtle text-success">Tersedia</span>
                        <?php elseif (!$menu['is_available']): ?>
                            <span class="badge bg-warning-subtle text-warning">Habis</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </td>

                    <!-- Aksi edit & hapus -->
                    <td>
                        <a href="/resto/menus/<?= $menu['id'] ?>/edit"
                           class="btn btn-sm btn-outline-primary me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="/resto/menus/<?= $menu['id'] ?>"
                              class="d-inline"
                              onsubmit="return confirm('Hapus menu <?= esc(addslashes($menu['name'])) ?>?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= ob_get_clean() ?>
