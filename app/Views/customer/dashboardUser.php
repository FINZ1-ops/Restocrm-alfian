<?php
/**
 * @var mixed $totalOrders
 * @var mixed $points
 * @var mixed $voucherCount
 * @var mixed $favoriteRestaurants
 * @var mixed $recentOrders
 */
ob_start(); ?>

<div class="container py-4">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-1">
                Halo, <?= esc(session()->get('name')) ?>
            </h2>

            <p class="text-muted mb-0">
                Selamat datang kembali di RESTOCRM Customer Portal
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-receipt fs-1 text-primary"></i>
                    <h3 class="mt-2 mb-0"><?= $totalOrders ?? 0 ?></h3>
                    <small class="text-muted">Total Pesanan</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-star-fill fs-1 text-warning"></i>
                    <h3 class="mt-2 mb-0"><?= $points ?? 0 ?></h3>
                    <small class="text-muted">Loyalty Point</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-ticket-perforated fs-1 text-success"></i>
                    <h3 class="mt-2 mb-0"><?= $voucherCount ?? 0 ?></h3>
                    <small class="text-muted">Voucher Aktif</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-heart-fill fs-1 text-danger"></i>
                    <h3 class="mt-2 mb-0"><?= $favoriteRestaurants ?? 0 ?></h3>
                    <small class="text-muted">Restoran Favorit</small>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-qr-code-scan me-2"></i>
                Mulai Memesan
            </h5>

            <p class="text-muted">
                Untuk melakukan pemesanan makanan atau minuman, silakan scan QR Code yang tersedia di meja restoran.
            </p>

            <a href="/akun/scan" class="btn btn-primary">
                scan sekarang
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0">
            <h5 class="fw-bold mb-0">
                Riwayat Pesanan Terbaru
            </h5>
        </div>

        <div class="card-body">

            <?php if (empty($recentOrders)): ?>

                <div class="text-center py-4 text-muted">
                    Belum ada riwayat pesanan.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Restoran</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($recentOrders as $order): ?>

                            <tr>
                                <td><?= esc($order['invoice_number']) ?></td>
                                <td><?= esc($order['restaurant_name']) ?></td>
                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                <td>Rp <?= number_format($order['total_amount']) ?></td>
                                <td>
                                    <span class="badge bg-success">
                                        <?= esc($order['status']) ?>
                                    </span>
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

<?php
$content = ob_get_clean();
echo view('layouts/Layout', [
    'title' => 'Dashboard Customer',
    'content' => $content
]);
?>