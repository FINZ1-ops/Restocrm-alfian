<?php
/**
 * @var mixed $restaurant
 * @var mixed $roleLabel
 * @var mixed $roleColor
 */
/**
 * View: admin/restaurants/show
 * Detail satu restoran — info umum, langganan, dan daftar user
 * Variabel: $restaurant (array), $subscription (array|null), $users (array)
 *
 * CATATAN: View ini dirender via Layout, jangan panggil Layout di sini.
 * Variabel dikirim dari controller melalui $content = view('...', [...]).
 */

// Defensive — pastikan semua variabel terdefinisi walau controller tidak kirim
if (!isset($restaurant)) return;
$subscription = $subscription ?? null;
$users        = $users        ?? [];
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <a href="/admin/restaurants" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Restoran
        </a>
        <h2 class="fw-bold mb-1 mt-2 text-dark" style="font-size:20px;">
            <?= esc($restaurant['name']) ?>
        </h2>
        <p class="text-secondary mb-0" style="font-size:13px;">
            Detail informasi dan pengelolaan restoran
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/restaurants/<?= $restaurant['id'] ?>/edit"
           class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-pencil me-1"></i>Edit Restoran
        </a>
        <form method="POST" action="/admin/restaurants/<?= $restaurant['id'] ?>"
              onsubmit="return confirm('Hapus restoran ini? Semua data terkait akan ikut terhapus.')">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button class="btn btn-outline-danger rounded-pill px-4">
                <i class="bi bi-trash me-1"></i>Hapus
            </button>
        </form>
    </div>
</div>

<!-- Flash message -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
        <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- Kolom kiri: info restoran + daftar staff -->
    <div class="col-lg-8">

        <!-- Info Umum -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">Informasi Restoran</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">

                    <!-- Logo / avatar restoran -->
                    <div class="col-12 d-flex align-items-center gap-3 mb-2">
                        <?php if (!empty($restaurant['logo'])): ?>
                            <img src="/<?= esc($restaurant['logo']) ?>"
                                 class="rounded-3 border"
                                 style="width:64px;height:64px;object-fit:cover;"
                                 alt="Logo <?= esc($restaurant['name']) ?>">
                        <?php else: ?>
                            <!-- Placeholder logo dengan icon Bootstrap -->
                            <div class="rounded-3 bg-light d-flex align-items-center justify-content-center border"
                                 style="width:64px;height:64px;">
                                <i class="bi bi-shop text-muted" style="font-size:28px;"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="fw-bold fs-5"><?= esc($restaurant['name']) ?></div>
                            <?php if ($restaurant['is_active']): ?>
                                <span class="text-success small fw-medium">
                                    <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>Aktif
                                </span>
                            <?php else: ?>
                                <span class="text-danger small fw-medium">
                                    <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>Nonaktif
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Slug / URL</div>
                        <div class="fw-medium"><code><?= esc($restaurant['slug'] ?? '-') ?></code></div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small mb-1">WhatsApp</div>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $restaurant['whatsapp'] ?? '') ?>"
                           target="_blank" class="text-success text-decoration-none fw-medium">
                            <i class="bi bi-whatsapp me-1"></i><?= esc($restaurant['whatsapp'] ?? '-') ?>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Jam Operasional</div>
                        <div class="fw-medium">
                            <i class="bi bi-clock me-1 text-muted"></i>
                            <?= esc($restaurant['opening_hours'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Terdaftar</div>
                        <div class="fw-medium">
                            <i class="bi bi-calendar me-1 text-muted"></i>
                            <?= !empty($restaurant['created_at'])
                                ? date('d M Y', strtotime($restaurant['created_at']))
                                : '-' ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="text-muted small mb-1">Alamat</div>
                        <div class="fw-medium">
                            <i class="bi bi-geo-alt me-1 text-muted"></i>
                            <?= esc($restaurant['address'] ?? '-') ?>
                        </div>
                    </div>

                    <?php if (!empty($restaurant['description'])): ?>
                    <div class="col-12">
                        <div class="text-muted small mb-1">Deskripsi</div>
                        <div class="fw-medium"><?= esc($restaurant['description']) ?></div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Daftar Staff -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">Akun Staff</h6>
                <span class="badge bg-secondary-subtle text-secondary"><?= count($users) ?> akun</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($users)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-people" style="font-size:36px;opacity:.3;"></i>
                        <p class="mt-2 mb-0 small">Belum ada akun staff terdaftar.</p>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light" style="font-size:12px;">
                            <tr>
                                <th class="ps-4 border-0">Nama</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">Role</th>
                                <th class="border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Avatar inisial nama -->
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                             style="width:32px;height:32px;font-size:13px;flex-shrink:0;
                                                    background:linear-gradient(135deg,#6366f1,#818cf8);">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                        <span class="fw-medium small"><?= esc($user['name']) ?></span>
                                    </div>
                                </td>
                                <td class="small text-muted"><?= esc($user['email']) ?></td>
                                <td>
                                    <?php
                                    // Mapping role ke label & warna badge
                                    $roleMap = [
                                        'admin_resto' => ['Admin Resto', 'primary'],
                                        'kasir'       => ['Kasir',       'success'],
                                        'dapur'       => ['Dapur',       'warning'],
                                        'sales'       => ['Sales',       'info'],
                                        'super_admin' => ['Super Admin', 'danger'],
                                    ];
                                    [$roleLabel, $roleColor] = $roleMap[$user['role']] ?? [$user['role'], 'secondary'];
                                    ?>
                                    <span class="badge bg-<?= $roleColor ?>-subtle text-<?= $roleColor ?>
                                                border border-<?= $roleColor ?> border-opacity-25">
                                        <?= $roleLabel ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="text-success small fw-medium">
                                            <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="text-danger small fw-medium">
                                            <i class="bi bi-circle-fill me-1" style="font-size:8px;"></i>Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Kolom kanan: info langganan -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">Status Langganan</h6>
            </div>
            <div class="card-body p-4">
                <?php if ($subscription): ?>
                    <?php
                    $statusColor = match($subscription['status']) {
                        'Aktif'   => 'success',
                        'Trial'   => 'warning',
                        'Expired' => 'danger',
                        'Suspend' => 'secondary',
                        default   => 'secondary',
                    };
                    ?>

                    <div class="text-center mb-4">
                        <span class="badge bg-<?= $statusColor ?> bg-opacity-10
                                     text-<?= $statusColor ?>
                                     border border-<?= $statusColor ?> border-opacity-25
                                     rounded-pill px-4 py-2 fs-6">
                            <?= esc($subscription['status']) ?>
                        </span>
                    </div>

                    <dl class="row mb-0" style="font-size:13px;">
                        <dt class="col-5 text-muted fw-normal">Paket</dt>
                        <dd class="col-7 fw-semibold"><?= esc($subscription['plan_name'] ?? '-') ?></dd>

                        <dt class="col-5 text-muted fw-normal">Mulai</dt>
                        <dd class="col-7">
                            <?= !empty($subscription['start_date'])
                                ? date('d M Y', strtotime($subscription['start_date']))
                                : '-' ?>
                        </dd>

                        <dt class="col-5 text-muted fw-normal">Berakhir</dt>
                        <dd class="col-7">
                            <?= !empty($subscription['end_date'])
                                ? date('d M Y', strtotime($subscription['end_date']))
                                : '-' ?>
                        </dd>

                        <dt class="col-5 text-muted fw-normal">Siklus</dt>
                        <dd class="col-7 text-capitalize">
                            <?= esc($subscription['billing_cycle'] ?? '-') ?>
                        </dd>
                    </dl>

                    <?php
                    // Hitung sisa hari langganan
                    $sisaHari = !empty($subscription['end_date'])
                        ? (int) ceil((strtotime($subscription['end_date']) - time()) / 86400)
                        : null;
                    ?>
                    <?php if ($sisaHari !== null): ?>
                        <div class="mt-3 p-3 rounded-3 text-center <?= $sisaHari <= 7 ? 'bg-danger-subtle' : 'bg-light' ?>">
                            <div class="fw-bold fs-3 <?= $sisaHari <= 7 ? 'text-danger' : 'text-dark' ?>">
                                <?= max(0, $sisaHari) ?>
                            </div>
                            <div class="text-muted small">hari tersisa</div>
                            <?php if ($sisaHari <= 7): ?>
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Segera perpanjang
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-file-earmark-x text-muted" style="font-size:40px;opacity:.3;"></i>
                        <p class="text-muted small mt-2 mb-0">Tidak ada langganan aktif.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>