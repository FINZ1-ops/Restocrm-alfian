<?php
/**
 * @var mixed $points
 * @var mixed $totalSpent
 * @var mixed $vouchers
 */
ob_start(); ?>

<div class="container py-4">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-1">
                <i class="bi bi-gift me-2"></i>
                Poin & Voucher
            </h2>
            <p class="text-muted mb-0">
                Kumpulkan poin dan dapatkan voucher menarik
            </p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center py-5">
                    <i class="bi bi-star-fill fs-1 text-warning mb-3 d-block"></i>
                    <h3 class="fw-bold text-warning"><?= number_format($points) ?></h3>
                    <p class="text-muted mb-0">Loyalty Point</p>
                    <small class="text-muted d-block mt-2">
                        Total belanja: Rp <?= number_format($totalSpent, 0, ',', '.') ?>
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Cara Mendapatkan Poin
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <small>1 Poin = Rp 10.000 belanja</small>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <small>Poin tidak pernah hangus</small>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <small>Tukarkan poin dengan voucher</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-ticket-perforated me-2"></i>
                Voucher Tersedia
            </h5>
        </div>
        <div class="card-body">
            
            <?php if (empty($vouchers)): ?>
                
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada voucher tersedia untuk Anda</p>
                    <small>Kumpulkan lebih banyak poin untuk membuka voucher eksklusif</small>
                </div>

            <?php else: ?>

                <div class="row g-3">
                    <?php foreach ($vouchers as $voucher): ?>
                        
                        <div class="col-md-6">
                            <div class="card border-success border-2 rounded-3">
                                <div class="card-body">
                                    <div class="row align-items-start">
                                        <div class="col-md-8">
                                            <h6 class="fw-bold text-success mb-1">
                                                <?= esc($voucher['code']) ?>
                                            </h6>
                                            <p class="small text-muted mb-2">
                                                <?= esc($voucher['description']) ?>
                                            </p>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                Berlaku hingga <?= date('d M Y', strtotime($voucher['valid_until'])) ?>
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                            <h4 class="fw-bold text-success mb-2">
                                                <?= esc($voucher['discount']) ?>
                                            </h4>
                                            <small class="text-muted d-block">
                                                Min. Rp <?= number_format($voucher['min_purchase'], 0, ',', '.') ?>
                                            </small>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-success mt-3 w-100" data-voucher="<?= esc($voucher['code']) ?>">
                                        <i class="bi bi-clipboard-check me-1"></i>
                                        Salin Kode
                                    </button>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-bar-chart me-2"></i>
                Tingkatan Membership
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="bi bi-star fs-2 <?= $points >= 50 ? 'text-warning' : 'text-secondary' ?> mb-2 d-block"></i>
                        <h6 class="fw-bold">Silver</h6>
                        <small class="text-muted">50+ Poin</small>
                        <p class="small mt-2">Dapatkan diskon 10%</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-center">
                        <i class="bi bi-star-fill fs-2 <?= $points >= 100 ? 'text-warning' : 'text-secondary' ?> mb-2 d-block"></i>
                        <h6 class="fw-bold">Gold</h6>
                        <small class="text-muted">100+ Poin</small>
                        <p class="small mt-2">Dapatkan diskon 20%</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-center">
                        <i class="bi bi-gem fs-2 <?= $points >= 200 ? 'text-danger' : 'text-secondary' ?> mb-2 d-block"></i>
                        <h6 class="fw-bold">Platinum</h6>
                        <small class="text-muted">200+ Poin</small>
                        <p class="small mt-2">Dapatkan diskon 30%</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="alert alert-info rounded-4" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Informasi:</strong> Poin dan voucher berlaku di semua restoran yang bergabung dengan RESTOCRM. Dapatkan pengalaman berbelanja yang lebih hemat!
    </div>

    <div class="mt-4">
        <a href="<?= base_url('akun/dashboard') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Kembali ke Dashboard
        </a>
    </div>

</div>

<script>
document.querySelectorAll('[data-voucher]').forEach(button => {
    button.addEventListener('click', function() {
        const voucherCode = this.getAttribute('data-voucher');
        navigator.clipboard.writeText(voucherCode).then(() => {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check-circle me-1"></i>Kode Tercopy!';
            this.classList.add('disabled');
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('disabled');
            }, 2000);
        });
    });
});
</script>

<?php
$content = ob_get_clean();
echo view('layouts/Layout', [
    'title' => 'Poin & Voucher',
    'content' => $content
]);
?>