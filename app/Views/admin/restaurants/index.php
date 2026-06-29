<?php
/** @var array $restaurants */
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;">Daftar Restoran</h2>
        <p class="text-secondary mb-0" style="font-size:13px;">Kelola semua restoran yang terdaftar di platform</p>
    </div>
    <a href="/admin/leads" class="btn btn-primary shadow-sm rounded-pill px-4 fw-medium">
        <i class="bi bi-plus-lg me-1"></i> Tambah via leads
    </a>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4 border-0">Informasi Restoran</th>
                        <th class="border-0">Kontak</th>
                        <th class="border-0">Status Berlangganan</th>
                        <th class="border-0">Status Akun</th>
                        <th class="text-end pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($restaurants)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada restoran yang terdaftar.</td></tr>
                        <?php else: ?>
                            <?php foreach ($restaurants as $resto): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark fs-6"><?= esc($resto['name']) ?></div>
                                        <div class="text-muted small d-flex align-items-center mt-1">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <span class="text-truncate" style="max-width: 200px;">
                                                <?= !empty($resto['address']) ? esc($resto['address']) : 'Belum ada alamat' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                    <a href="https://wa.me/<?= preg_replace('/\D/','',$resto['whatsapp'] ?? '') ?>" target="_blank" class="text-success text-decoration-none small fw-medium d-flex align-items-center">
                                        <i class="bi bi-whatsapp me-1"></i> <?= esc($resto['whatsapp'] ?? '-') ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($resto['subscription'])): ?>
                                        <?php 
                                            $sub = $resto['subscription'];
                                            $statusClass = 'success';
                                            if ($sub['status'] === 'Expired') $statusClass = 'danger';
                                            if ($sub['status'] === 'Trial') $statusClass = 'warning text-dark';
                                        ?>
                                        <div class="d-flex flex-column align-items-start">
                                            <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= explode(' ', $statusClass)[0] ?> border border-<?= explode(' ', $statusClass)[0] ?> border-opacity-25 rounded-pill px-3 py-1 mb-1">
                                                <?= esc($sub['status']) ?>
                                            </span>
                                            <small class="text-muted" style="font-size: 11px;">
                                                s/d <?= date('d M Y', strtotime($sub['end_date'])) ?>
                                            </small>
                                            <small class="text-muted" style="font-size: 11px;"><?= esc( $sub['plan_name']) ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                            Tidak Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($resto['is_active']): ?>
                                        <span class="text-success small fw-medium"><i class="bi bi-circle-fill me-1" style="font-size:8px;"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="text-danger small fw-medium"><i class="bi bi-circle-fill me-1" style="font-size:8px;"></i> Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="/admin/restaurants/<?= $resto['id'] ?>" class="btn btn-sm btn-light text-primary rounded-circle" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="/admin/restaurants/<?= $resto['id'] ?>/edit" class="btn btn-sm btn-light text-warning rounded-circle" data-bs-toggle="tooltip" title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="/admin/restaurants/<?= $resto['id'] ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus restoran ini? Semua data terkait (menu, pesanan) akan terhapus dan tindakan ini tidak dapat dibatalkan.')" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button class="btn btn-sm btn-light text-danger rounded-circle" data-bs-toggle="tooltip" title="Hapus Restoran">
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
</div>

<style>
    .table > :not(caption) > * > * { padding: 1rem 0.5rem; border-bottom-color: #f1f5f9; }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
