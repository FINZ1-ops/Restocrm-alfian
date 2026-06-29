<?php
/**
 * @var mixed $orders
 */
ob_start(); ?>

<div class="container py-4">

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="fw-bold mb-1">
                <i class="bi bi-receipt-cutoff me-2"></i>
                Riwayat Pesanan
            </h2>
            <p class="text-muted mb-0">
                Semua pesanan yang pernah Anda lakukan
            </p>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted mb-3">Belum Ada Pesanan</h5>
                <p class="text-muted mb-4">
                    Anda belum pernah melakukan pesanan. Mulai pesan sekarang dengan scan QR Code di meja restoran.
                </p>
                <a href="<?= base_url('customer/dashboard') ?>" class="btn btn-primary">
                    <i class="bi bi-qr-code-scan me-2"></i>
                    Mulai Memesan
                </a>
            </div>
        </div>

    <?php else: ?>

        <?php foreach ($orders as $order): ?>
            
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold mb-1">
                                <?= esc($order['restaurant_name']) ?>
                            </h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <span class="badge bg-<?= $order['payment_status'] === 'Lunas' ? 'success' : ($order['payment_status'] === 'Ditolak' ? 'danger' : 'warning') ?> px-3 py-2">
                                <?= esc($order['payment_status']) ?>
                            </span>
                            <span class="badge bg-<?= $order['order_status'] === 'Selesai' ? 'success' : ($order['order_status'] === 'Dibatalkan' ? 'danger' : 'info') ?> px-3 py-2 ms-2">
                                <?= esc($order['order_status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Kode Pesanan</small>
                            <p class="fw-bold mb-0"><?= esc($order['order_code']) ?></p>
                        </div>
                        <div class="col-md-6 mt-2 mt-md-0">
                            <small class="text-muted">Meja</small>
                            <p class="fw-bold mb-0">
                                <?= esc($order['area_name']) ?> - Meja <?= esc($order['table_number']) ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($order['items'])): ?>
                        <div class="border-top pt-3">
                            <h6 class="fw-bold mb-3">Detail Pesanan</h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Menu</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <tr>
                                                <td><?= esc($item['menu_name']) ?></td>
                                                <td class="text-center"><?= esc($item['quantity']) ?></td>
                                                <td class="text-end">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                <td class="text-end">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($order['notes'])): ?>
                        <div class="border-top pt-3 mt-3">
                            <small class="text-muted">Catatan</small>
                            <p class="mb-0"><?= esc($order['notes']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer bg-light border-0 py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <small class="text-muted">Metode Pembayaran</small>
                            <p class="fw-semibold mb-0"><?= esc($order['payment_method']) ?></p>
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            <small class="text-muted d-block">Total Pembayaran</small>
                            <h5 class="fw-bold text-primary mb-0">
                                Rp <?= number_format($order['total'], 0, ',', '.') ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

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
    'title' => 'Riwayat Pesanan',
    'content' => $content
]);
?>
