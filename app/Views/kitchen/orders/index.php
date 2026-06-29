<?php
/** @var array $orders */
ob_start();
?>
<div class="mb-4">
    <h2 class="fw-bold mb-1 text-dark">Antrian Dapur</h2>
    <p class="text-secondary mb-0">Pesanan yang perlu diproses</p>
</div>

<?php if (empty($orders)): ?>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
            Tidak ada pesanan aktif saat ini.
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($orders as $order): ?>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>#<?= esc($order['order_code']) ?></strong>
                        <span class="badge bg-warning text-dark"><?= esc($order['order_status']) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><i class="bi bi-person me-1"></i><?= esc($order['customer_name']) ?>
                            <?php if (!empty($order['table_number'])): ?>
                                <span class="text-muted">· Meja <?= esc($order['table_number']) ?></span>
                            <?php endif; ?>
                        </p>
                        <ul class="list-unstyled mb-3">
                            <?php foreach ($order['items'] ?? [] as $item): ?>
                                <li class="d-flex justify-content-between border-bottom py-1">
                                    <span><?= esc($item['menu_name'] ?? 'Menu') ?> × <?= (int) $item['quantity'] ?></span>
                                    <?php if (!empty($item['notes'])): ?>
                                        <small class="text-muted"><?= esc($item['notes']) ?></small>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <form method="POST" action="<?= base_url('kitchen/orders/' . $order['id'] . '/status') ?>" class="d-flex gap-2 flex-wrap">
                            <?= csrf_field() ?>
                            <?php if ($order['order_status'] === 'Menunggu Konfirmasi'): ?>
                                <input type="hidden" name="status" value="Diproses">
                                <button type="submit" class="btn btn-primary btn-sm">Mulai Proses</button>
                            <?php elseif ($order['order_status'] === 'Diproses'): ?>
                                <input type="hidden" name="status" value="Siap Disajikan">
                                <button type="submit" class="btn btn-warning btn-sm">Siap Disajikan</button>
                            <?php elseif ($order['order_status'] === 'Siap Disajikan'): ?>
                                <input type="hidden" name="status" value="Selesai">
                                <button type="submit" class="btn btn-success btn-sm">Selesai</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= ob_get_clean() ?>
