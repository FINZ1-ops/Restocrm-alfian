<?php
/**
 * View: resto/staff/index
 * Daftar staff restoran (kasir & dapur)
 * Variabel: $staffList (array)
 */
$staffList = $staffList ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;">Manajemen Staff</h2>
        <p class="text-secondary mb-0" style="font-size:13px;">Kelola akun kasir dan dapur restoran Anda</p>
    </div>
    <a href="/resto/staff/new" class="btn btn-primary shadow-sm rounded-pill px-4 fw-medium">
        <i class="bi bi-plus-lg me-1"></i>Tambah Staff
    </a>
</div>

<!-- Info singkat peran -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 rounded-4 h-100" style="background:#eff6ff;">
            <div class="card-body p-3 d-flex gap-3 align-items-start">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-credit-card fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark small">Kasir</div>
                    <div class="text-muted" style="font-size:12px;">Konfirmasi pembayaran cash & QRIS, lihat detail order</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 rounded-4 h-100" style="background:#fff7ed;">
            <div class="card-body p-3 d-flex gap-3 align-items-start">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 flex-shrink-0">
                    <i class="bi bi-fire fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark small">Dapur</div>
                    <div class="text-muted" style="font-size:12px;">Lihat pesanan masuk, update status Diproses & Siap Disajikan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary" style="font-size:12px;">
                <tr>
                    <th class="ps-4 border-0">Nama Staff</th>
                    <th class="border-0">Email</th>
                    <th class="border-0">Role</th>
                    <th class="border-0">Status</th>
                    <th class="text-end pe-4 border-0">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($staffList)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada staff terdaftar.
                            <div class="mt-2">
                                <a href="/resto/staff/new" class="btn btn-primary btn-sm rounded-pill px-4">
                                    Tambah Staff Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($staffList as $s): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <!-- Avatar warna berbeda per role -->
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                     style="width:36px;height:36px;font-size:14px;
                                            background:<?= $s['role'] === 'kasir' ? '#3b82f6' : '#f59e0b' ?>">
                                    <?= strtoupper(substr($s['name'], 0, 1)) ?>
                                </div>
                                <span class="fw-semibold"><?= esc($s['name']) ?></span>
                            </div>
                        </td>
                        <td class="text-muted small"><?= esc($s['email']) ?></td>
                        <td>
                            <?php if ($s['role'] === 'kasir'): ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25">
                                    <i class="bi bi-credit-card me-1"></i>Kasir
                                </span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">
                                    <i class="bi bi-fire me-1"></i>Dapur
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($s['is_active']): ?>
                                <span class="text-success small fw-medium">
                                    <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>Aktif
                                </span>
                            <?php else: ?>
                                <span class="text-danger small fw-medium">
                                    <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="/resto/staff/<?= $s['id'] ?>/edit"
                                   class="btn btn-sm btn-light text-warning rounded-circle"
                                   data-bs-toggle="tooltip" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="/resto/staff/<?= $s['id'] ?>"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus staff <?= esc($s['name']) ?>?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-sm btn-light text-danger rounded-circle"
                                            data-bs-toggle="tooltip" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});
</script>