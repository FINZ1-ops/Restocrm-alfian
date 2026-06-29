<?php
/** @var array $order */
/** @var array $items */
/** @var array|null $payment */
ob_start();
?>
<div class="mb-4">
    <a href="<?= base_url('cashier/orders') ?>" class="btn btn-light rounded-circle mb-3"><i class="bi bi-arrow-left"></i></a>
    <h2 class="fw-bold mb-0">Order #<?= esc($order['order_code']) ?></h2>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white fw-semibold">Item Pesanan</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><?= esc($item['menu_name'] ?? 'Menu') ?> × <?= (int) $item['quantity'] ?></span>
                        <span>Rp <?= number_format((float) $item['price'] * (int) $item['quantity'], 0, ',', '.') ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="card-footer bg-white d-flex justify-content-between fw-bold">
                <span>Total</span>
                <span>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <p><strong>Pelanggan:</strong> <?= esc($order['customer_name']) ?></p>
                <p><strong>WhatsApp:</strong> <?= esc($order['customer_whatsapp'] ?? '-') ?></p>
                <p><strong>Metode:</strong> <?= esc(strtoupper($order['payment_method'])) ?></p>
                <p><strong>Status:</strong> <?= esc($order['payment_status']) ?></p>
                <?php if ($order['payment_method'] === 'cash'): ?>
                    <form method="POST" action="<?= base_url('cashier/orders/' . $order['id'] . '/confirm-cash') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success w-100">Konfirmasi Tunai</button>
                    </form>
                <?php elseif ($order['payment_method'] === 'qris'): ?>
                    <?php if (!empty($payment['proof_image'])): ?>
                        <p class="fw-semibold mb-2 mt-3">Bukti Pembayaran:</p>
                        <a href="<?= base_url($payment['proof_image']) ?>" target="_blank">
                            <img src="<?= base_url($payment['proof_image']) ?>"
                                 class="img-fluid rounded-3 mb-3" alt="Bukti pembayaran QRIS"
                                 style="max-height:320px; width:100%; object-fit:contain; background:#f8f9fa;">
                        </a>
                    <?php else: ?>
                        <p class="text-muted small mt-3">Belum ada bukti pembayaran yang diupload customer.</p>
                    <?php endif; ?>

                    <?php if ($order['payment_status'] === 'Menunggu Konfirmasi'): ?>
                        <form method="POST" action="<?= base_url('cashier/orders/' . $order['id'] . '/verify-qris') ?>" class="mb-2">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success w-100">Konfirmasi Lunas</button>
                        </form>
                        <form method="POST" action="<?= base_url('cashier/orders/' . $order['id'] . '/verify-qris') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="reject">
                            <input type="text" name="notes" class="form-control mb-2" placeholder="Alasan tolak (opsional)">
                            <button type="submit" class="btn btn-outline-danger w-100">Tolak Pembayaran</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= ob_get_clean() ?>