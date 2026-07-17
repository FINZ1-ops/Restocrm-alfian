<?php
/**
 * @var mixed $plans
 * @var mixed $subscription
 */
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;">Paket Langganan</h2>
        <p class="text-secondary mb-0" style="font-size:13px;">Pilih paket yang disediakan Super Admin dan kelola langganan restoran Anda.</p>
    </div>
    <a href="/resto/subscriptions/new?plan_id=<?= esc($plans[0]['id'] ?? '') ?>" class="btn btn-primary shadow-sm rounded-pill px-4 fw-medium">
        <i class="bi bi-cart-plus me-1"></i> Beli Paket
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success rounded-3 mb-4">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger rounded-3 mb-4">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">Langganan Aktif</h6>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($subscription)): ?>
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Paket</dt>
                        <dd class="col-7 fw-semibold"><?= esc($subscription['plan_name'] ?? '-') ?></dd>

                        <dt class="col-5 text-muted">Siklus</dt>
                        <dd class="col-7 fw-semibold"><?= esc($subscription['billing_cycle'] === 'yearly' ? 'Tahunan' : 'Bulanan') ?></dd>

                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7 fw-semibold"><?= esc($subscription['status'] ?? '-') ?></dd>

                        <dt class="col-5 text-muted">Mulai</dt>
                        <dd class="col-7 fw-semibold"><?= !empty($subscription['start_date']) ? date('d M Y', strtotime($subscription['start_date'])) : '-' ?></dd>

                        <dt class="col-5 text-muted">Berakhir</dt>
                        <dd class="col-7 fw-semibold"><?= !empty($subscription['end_date']) ? date('d M Y', strtotime($subscription['end_date'])) : '-' ?></dd>
                    </dl>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam" style="font-size:36px;"></i>
                        <p class="mt-3 mb-0">Belum ada langganan aktif.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">Info Pembelian</h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Pilih paket dan beli langsung di sini. Invoice akan dibuat otomatis ketika pembayaran selesai.</p>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Daftar paket resmi dari Super Admin</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Pembayaran langsung tercatat sebagai invoice</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Tidak ada pembuatan invoice manual lagi dari sisi restoran</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <?php foreach ($plans as $plan): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1"><?= esc($plan['name']) ?></h5>
                            <p class="text-muted small mb-0"><?= esc($plan['description']) ?></p>
                        </div>
                        <?php if (!$plan['is_active']): ?>
                            <span class="badge bg-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Bulanan</span>
                            <span class="fw-semibold">Rp <?= number_format((float)$plan['price_monthly'], 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tahunan</span>
                            <span class="fw-semibold">Rp <?= number_format((float)$plan['price_yearly'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <ul class="list-unstyled small mb-4">
                        <li class="mb-2">Meja: <?= $plan['max_tables'] >= 999 ? 'Unlimited' : esc($plan['max_tables']) ?></li>
                        <li class="mb-2">Menu: <?= $plan['max_menus'] >= 999 ? 'Unlimited' : esc($plan['max_menus']) ?></li>
                        <li>CRM Pelanggan: <?= $plan['has_crm'] ? 'Ya' : 'Tidak' ?></li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <a href="/resto/subscriptions/new?plan_id=<?= esc($plan['id']) ?>"
                       class="btn btn-<?= $plan['is_active'] ? 'primary' : 'secondary' ?> w-100 rounded-pill <?= $plan['is_active'] ? '' : 'disabled' ?>">
                        Beli Paket
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
