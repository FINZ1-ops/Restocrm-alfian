<?php
/**
 * @var mixed $user
 * @var mixed $customerData
 */
ob_start(); ?>

<div class="container py-4">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-1">
                <i class="bi bi-person-circle me-2"></i>
                Profil Saya
            </h2>
            <p class="text-muted mb-0">
                Informasi akun dan data customer Anda
            </p>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-badge me-2"></i>
                        Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Nama Lengkap</label>
                        <p class="fw-semibold mb-0"><?= esc($user['name']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="fw-semibold mb-0"><?= esc($user['email']) ?></p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">User ID</label>
                        <p class="fw-semibold mb-0">#<?= esc($user['id']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Statistik Customer
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($customerData): ?>
                        <div class="mb-3">
                            <label class="text-muted small">Total Pesanan</label>
                            <p class="fw-semibold mb-0"><?= esc($customerData->total_orders ?? 0) ?> pesanan</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Total Belanja</label>
                            <p class="fw-semibold mb-0">Rp <?= number_format($customerData->total_spent ?? 0, 0, ',', '.') ?></p>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small">Terakhir Order</label>
                            <p class="fw-semibold mb-0">
                                <?php if ($customerData->last_order_at): ?>
                                    <?= date('d M Y, H:i', strtotime($customerData->last_order_at)) ?>
                                <?php else: ?>
                                    Belum ada pesanan
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data customer
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-shield-check me-2"></i>
                Keamanan Akun
            </h5>
            <p class="text-muted mb-3">
                Jaga keamanan akun Anda dengan menggunakan password yang kuat dan tidak membagikannya kepada siapapun.
            </p>
            <a href="<?= base_url('customer/change-password') ?>" class="btn btn-outline-primary">
                <i class="bi bi-key me-2"></i>
                Ubah Password
            </a>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= base_url('customer/dashboard') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Kembali ke Dashboard
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
echo view('layouts/Layout', [
    'title' => 'Profil Customer',
    'content' => $content
]);
?>
