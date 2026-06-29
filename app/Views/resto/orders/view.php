<?php
/**
 * @var mixed $order
 * @var mixed $items
 */
/**
 * View: resto/orders/view.php
 * Detail satu pesanan: item, info pelanggan, status pembayaran & order
 * Variabel: $order, $items
 */
ob_start(); ?>

<div class="mb-4">
    <a href="/resto/orders" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Pesanan
    </a>
    <h2 class="fw-bold mb-1 mt-2">Detail Pesanan</h2>
    <code class="text-muted"><?= esc($order['order_code']) ?></code>
</div>

<div class="row g-4">
    <!-- Item pesanan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header">Item yang Dipesan</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Menu</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= esc($item['menu_name'] ?? '—') ?></td>
                            <td class="text-end">Rp&nbsp;<?= number_format((float)$item['price'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= (int)$item['quantity'] ?></td>
                            <td class="text-end fw-semibold">
                                Rp&nbsp;<?= number_format((float)$item['price'] * (int)$item['quantity'], 0, ',', '.') ?>
                            </td>
                            <td class="text-muted small"><?= esc($item['notes'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Total baris -->
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">
                                Rp&nbsp;<?= number_format((float)$order['total'], 0, ',', '.') ?>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Catatan order (jika ada) -->
        <?php if (!empty($order['notes'])): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header">Catatan dari Pelanggan</div>
            <div class="card-body p-4">
                <?= esc($order['notes']) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar: info pesanan -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header">Informasi Pesanan</div>
            <div class="card-body p-4">
                <dl class="row mb-0 small">
                    <dt class="col-6 text-muted fw-normal">Pelanggan</dt>
                    <dd class="col-6 fw-semibold"><?= esc($order['customer_name']) ?></dd>

                    <dt class="col-6 text-muted fw-normal">WhatsApp</dt>
                    <dd class="col-6"><?= esc($order['customer_whatsapp'] ?? '—') ?></dd>

                    <dt class="col-6 text-muted fw-normal">Meja</dt>
                    <dd class="col-6">
                        Meja <?= esc($order['table_number']) ?>
                        <span class="text-muted">(<?= esc($order['area_name']) ?>)</span>
                    </dd>

                    <dt class="col-6 text-muted fw-normal">Metode Bayar</dt>
                    <dd class="col-6 text-uppercase fw-semibold"><?= esc($order['payment_method']) ?></dd>

                    <dt class="col-6 text-muted fw-normal">Status Bayar</dt>
                    <dd class="col-6">
                        <?php
                        $pc = ['Belum Dibayar'=>'secondary','Menunggu Konfirmasi'=>'warning','Lunas'=>'success','Ditolak'=>'danger'];
                        $c  = $pc[$order['payment_status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $c ?>-subtle text-<?= $c ?>">
                            <?= esc($order['payment_status']) ?>
                        </span>
                    </dd>

                    <dt class="col-6 text-muted fw-normal">Status Order</dt>
                    <dd class="col-6">
                        <?php
                        $sc = ['Menunggu Konfirmasi'=>'warning','Diproses'=>'info','Siap Disajikan'=>'primary','Selesai'=>'success','Dibatalkan'=>'danger'];
                        $s  = $sc[$order['order_status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $s ?>-subtle text-<?= $s ?>">
                            <?= esc($order['order_status']) ?>
                        </span>
                    </dd>

                    <dt class="col-6 text-muted fw-normal">Waktu Order</dt>
                    <dd class="col-6"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<?= ob_get_clean() ?>
