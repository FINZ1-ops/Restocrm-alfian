<?php
/**
 * @var mixed $plans
 */
/**
 * View: admin/plans/index
 * Daftar semua paket langganan — Basic, Pro, Premium
 * Variabel yang diterima dari controller: $plans (array)
 */
?>

<!-- Header halaman -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;">Paket Langganan</h2>
        <p class="text-secondary mb-0" style="font-size:13px;">Kelola paket yang ditawarkan kepada restoran</p>
    </div>
    <a href="/admin/plans/new" class="btn btn-primary shadow-sm rounded-pill px-4 fw-medium">
        <i class="bi bi-plus-lg me-1"></i> Tambah Paket
    </a>
</div>

<!-- Flash message sukses/error dari redirect -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($plans)): ?>
    <!-- State kosong -->
    <div class="text-center py-5 text-muted">
        <i class="bi bi-box-seam" style="font-size:48px;opacity:.3;"></i>
        <p class="mt-3 mb-1 fw-semibold">Belum ada paket</p>
        <p class="small">Tambahkan paket Basic, Pro, atau Premium untuk mulai menerima restoran.</p>
        <a href="/admin/plans/new" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">Tambah Paket Pertama</a>
    </div>
<?php else: ?>

    <!-- Kartu visual per paket (tampil maks 3) -->
    <?php
    // Warna aksen tiap paket berdasarkan urutan harga
    $accentColors = ['primary', 'success', 'warning'];
    ?>
    <div class="row g-3 mb-4">
        <?php foreach ($plans as $i => $plan): ?>
        <?php $color = $accentColors[$i] ?? 'secondary'; ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-4">
                    <!-- Nama paket & status aktif -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-<?= $color ?> rounded-pill px-3 py-2 fs-6">
                            <?= esc($plan['name']) ?>
                        </span>
                        <?php if ($plan['is_active']): ?>
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25">Nonaktif</span>
                        <?php endif; ?>
                    </div>

                    <!-- Deskripsi singkat -->
                    <?php if (!empty($plan['description'])): ?>
                        <p class="text-muted small mb-3"><?= esc($plan['description']) ?></p>
                    <?php endif; ?>

                    <!-- Fitur-fitur paket -->
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-1">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <?= $plan['max_tables'] >= 999 ? '<strong>Unlimited</strong> meja' : '<strong>' . $plan['max_tables'] . '</strong> meja' ?>
                        </li>
                        <li class="mb-1">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <?= $plan['max_menus'] >= 999 ? '<strong>Unlimited</strong> menu' : '<strong>' . $plan['max_menus'] . '</strong> menu' ?>
                        </li>
                        <li>
                            <?php if ($plan['has_crm']): ?>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <strong>CRM Pelanggan</strong> tersedia
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill text-secondary me-2"></i>
                                <span class="text-muted">Tanpa CRM Pelanggan</span>
                            <?php endif; ?>
                        </li>
                    </ul>

                    <!-- Harga bulanan & tahunan -->
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center small mb-1">
                            <span class="text-muted">Bulanan</span>
                            <span class="fw-bold text-dark">
                                Rp <?= number_format((float)$plan['price_monthly'], 0, ',', '.') ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Tahunan</span>
                            <span class="fw-bold text-dark">
                                Rp <?= number_format((float)$plan['price_yearly'], 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Tombol edit di footer kartu -->
                <div class="card-footer bg-transparent border-0 px-4 pb-4">
                    <a href="/admin/plans/<?= $plan['id'] ?>/edit"
                       class="btn btn-sm btn-outline-<?= $color ?> w-100 rounded-pill">
                        <i class="bi bi-pencil me-1"></i>Edit Paket
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabel lengkap semua paket -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold text-dark mb-0">Semua Paket</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary" style="font-size:12px;">
                        <tr>
                            <th class="ps-4 border-0">Nama Paket</th>
                            <th class="border-0">Maks Meja</th>
                            <th class="border-0">Maks Menu</th>
                            <th class="border-0">CRM</th>
                            <th class="border-0">Harga/Bulan</th>
                            <th class="border-0">Harga/Tahun</th>
                            <th class="border-0">Status</th>
                            <th class="text-end pe-4 border-0">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $plan): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold"><?= esc($plan['name']) ?></span>
                                <?php if (!empty($plan['description'])): ?>
                                    <div class="text-muted small"><?= esc($plan['description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($plan['max_tables'] >= 999): ?>
                                    <span class="text-success fw-bold">∞</span>
                                <?php else: ?>
                                    <?= (int)$plan['max_tables'] ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($plan['max_menus'] >= 999): ?>
                                    <span class="text-success fw-bold">∞</span>
                                <?php else: ?>
                                    <?= (int)$plan['max_menus'] ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($plan['has_crm']): ?>
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                <?php else: ?>
                                    <i class="bi bi-x-circle-fill text-secondary"></i>
                                <?php endif; ?>
                            </td>
                            <td class="fw-medium">Rp <?= number_format((float)$plan['price_monthly'], 0, ',', '.') ?></td>
                            <td class="fw-medium">Rp <?= number_format((float)$plan['price_yearly'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($plan['is_active']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="/admin/plans/<?= $plan['id'] ?>/edit"
                                       class="btn btn-sm btn-light text-warning rounded-circle"
                                       data-bs-toggle="tooltip" title="Edit Paket">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="/admin/plans/<?= $plan['id'] ?>"
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus paket <?= esc($plan['name']) ?>? Restoran yang memakai paket ini tidak akan terpengaruh.')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button class="btn btn-sm btn-light text-danger rounded-circle"
                                                data-bs-toggle="tooltip" title="Hapus Paket">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php endif; ?>

<style>
    .table > :not(caption) > * > * { padding: .85rem 0.5rem; border-bottom-color: #f1f5f9; }
</style>

<script>
    // Aktifkan tooltip Bootstrap
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    });
</script>