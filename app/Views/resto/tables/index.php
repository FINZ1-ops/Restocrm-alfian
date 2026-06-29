<?php
/**
 * @var mixed $tables
 */
/**
 * View: resto/tables/index.php
 * Daftar meja restoran + link generate QR per meja
 * Variabel: $tables
 */
ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Meja & QR Code</h2>
        <p class="text-secondary mb-0"><?= count($tables) ?> meja terdaftar</p>
    </div>
    <a href="/resto/tables/new" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Meja
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Meja</th>
                    <th>Area</th>
                    <th>Kapasitas</th>
                    <th>QR Token</th>
                    <th>Status</th>
                    <th style="width:180px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tables)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-grid-3x3 fs-2 d-block mb-2"></i>
                        Belum ada meja. <a href="/resto/tables/new">Tambah sekarang</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($tables as $table): ?>
                <tr>
                    <td class="fw-bold fs-6">Meja <?= esc($table['table_number']) ?></td>
                    <td><?= esc($table['area_name']) ?></td>
                    <td><?= (int)$table['capacity'] ?> orang</td>
                    <td>
                        <code class="small text-muted">
                            <?= esc(substr($table['qr_token'], 0, 14)) ?>…
                        </code>
                    </td>
                    <td>
                        <?php if ($table['is_active']): ?>
                            <span class="badge bg-success-subtle text-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Tombol lihat QR -->
                        <a href="/resto/tables/<?= $table['id'] ?>/qr"
                           class="btn btn-sm btn-outline-success me-1" title="Lihat & Cetak QR">
                            <i class="bi bi-qr-code"></i> QR
                        </a>
                        <!-- Edit meja -->
                        <a href="/resto/tables/<?= $table['id'] ?>/edit"
                           class="btn btn-sm btn-outline-primary me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <!-- Hapus meja -->
                        <form method="POST" action="/resto/tables/<?= $table['id'] ?>"
                              class="d-inline"
                              onsubmit="return confirm('Hapus Meja <?= (int)$table['table_number'] ?>?')">
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
