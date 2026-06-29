<?php
/**
 * @var mixed $categories
 * @var mixed $cat
 */
/**
 * View: resto/categories/index.php
 * Daftar kategori menu restoran — sortable, toggle aktif
 * Variabel: $categories
 */
ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Kategori Menu</h2>
        <p class="text-secondary mb-0"><?= count($categories) ?> kategori terdaftar</p>
    </div>
    <a href="/resto/categories/new" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Kategori
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px">#</th>
                    <th>Nama Kategori</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-tags fs-2 d-block mb-2"></i>
                        Belum ada kategori. <a href="/resto/categories/new">Tambah sekarang</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($categories as $i => $cat): ?>
                <tr>
                    <td class="text-muted"><?= $i + 1 ?></td>
                    <td class="fw-semibold"><?= esc($cat['name']) ?></td>
                    <td><?= (int) $cat['sort_order'] ?></td>
                    <td>
                        <?php if ($cat['is_active']): ?>
                            <span class="badge bg-success-subtle text-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/resto/categories/<?= $cat['id'] ?>/edit"
                           class="btn btn-sm btn-outline-primary me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="/resto/categories/<?= $cat['id'] ?>"
                              class="d-inline"
                              onsubmit="return confirm('Hapus kategori ini? Menu yang terkait juga akan terhapus.')">
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
