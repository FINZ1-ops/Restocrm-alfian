<?php
/**
 * @var mixed $orders
 * @var mixed $currentStatus
 * @var mixed $currentDate
 */
/**
 * View: resto/orders/index.php
 * Daftar semua pesanan restoran dengan filter tanggal & status
 * Variabel: $orders, $currentStatus, $currentDate
 */
ob_start();

$statusColors = [
    'Menunggu Konfirmasi' => 'warning',
    'Diproses'            => 'info',
    'Siap Disajikan'      => 'primary',
    'Selesai'             => 'success',
    'Dibatalkan'          => 'danger',
];
$payColors = [
    'Belum Dibayar'       => 'secondary',
    'Menunggu Konfirmasi' => 'warning',
    'Lunas'               => 'success',
    'Ditolak'             => 'danger',
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Semua Pesanan</h2>
        <p class="text-secondary mb-0"><?= count($orders) ?> pesanan ditemukan</p>
    </div>
</div>

<!-- Filter tanggal & status -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Tanggal</label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="<?= esc($currentDate ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status Pesanan</label>
                <select name="order_status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <?php foreach (array_keys($statusColors) as $s): ?>
                        <option value="<?= $s ?>"
                                <?= ($currentStatus ?? '') === $s ? 'selected' : '' ?>>
                            <?= $s ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="/resto/orders" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Total</th>
                    <th>Bayar</th>
                    <th>Status Bayar</th>
                    <th>Status Order</th>
                    <th>Waktu</th>
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                        Belum ada pesanan pada filter ini.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><code class="small"><?= esc($order['order_code']) ?></code></td>
                    <td>
                        <div class="fw-semibold"><?= esc($order['customer_name']) ?></div>
                        <small class="text-muted"><?= esc($order['customer_whatsapp']) ?></small>
                    </td>
                    <td>
                        <span class="fw-semibold">Meja <?= esc($order['table_number']) ?></span>
                        <br><small class="text-muted"><?= esc($order['area_name']) ?></small>
                    </td>
                    <td class="fw-semibold">Rp&nbsp;<?= number_format((float)$order['total'], 0, ',', '.') ?></td>
                    <td class="text-uppercase small"><?= esc($order['payment_method']) ?></td>
                    <td>
                        <?php $pc = $payColors[$order['payment_status']] ?? 'secondary'; ?>
                        <span class="badge bg-<?= $pc ?>-subtle text-<?= $pc ?>">
                            <?= esc($order['payment_status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php $sc = $statusColors[$order['order_status']] ?? 'secondary'; ?>
                        <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?>">
                            <?= esc($order['order_status']) ?>
                        </span>
                    </td>
                    <td class="text-muted small">
                        <?= date('d/m H:i', strtotime($order['created_at'])) ?>
                    </td>
                    <td>
                        <a href="/resto/orders/<?= $order['id'] ?>"
                           class="btn btn-sm btn-outline-primary">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= ob_get_clean() ?>
