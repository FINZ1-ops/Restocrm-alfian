<?php
/** @var array $orders */
ob_start();
?>
<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark">Konfirmasi Pembayaran</h2>
    <p class="text-secondary mb-0">Pesanan yang menunggu pembayaran atau verifikasi</p>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status Bayar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pesanan menunggu.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><code><?= esc($order['order_code']) ?></code></td>
                            <td><?= esc($order['customer_name']) ?></td>
                            <td><?= esc($order['table_number'] ?? '-') ?></td>
                            <td>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></td>
                            <td class="text-uppercase"><?= esc($order['payment_method']) ?></td>
                            <td><span class="badge bg-warning text-dark"><?= esc($order['payment_status']) ?></span></td>
                            <td>
                                <a href="<?= base_url('cashier/orders/' . $order['id']) ?>" class="btn btn-sm btn-primary rounded-pill">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= ob_get_clean() ?>
