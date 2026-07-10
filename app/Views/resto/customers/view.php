<?php
/**
 * @var mixed $customer
 * @var mixed $orders
 */
/**
 * View: resto/customers/view
 * Detail satu pelanggan beserta riwayat pesanannya (CRM Pelanggan)
 */
ob_start();
?>
<div class="mb-4">
    <a href="/resto/customers" class="text-muted text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Pelanggan
    </a>
    <h2 class="fw-bold mb-1 mt-2"><?= esc($customer['name']) ?></h2>
    <p class="text-secondary mb-0">Detail & Riwayat Pesanan</p>
</div>

<div class="row g-4">
    <!-- Kartu info pelanggan -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <!-- Avatar inisial -->
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width:72px;height:72px;font-size:28px;font-weight:700;color:#25343c;background:linear-gradient(135deg,#ffc327,#e5ae1f) !important">
                    <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= esc($customer['name']) ?></h5>
                <p class="text-muted mb-3">
                    <i class="bi bi-whatsapp text-success me-1"></i><?= esc($customer['whatsapp']) ?>
                </p>

                <!-- Tombol kontak via WA -->
                <a href="https://wa.me/<?= ltrim(esc($customer['whatsapp']), '0') ?>"
                   target="_blank" class="btn btn-success btn-sm w-100 mb-3">
                    <i class="bi bi-whatsapp me-1"></i>Hubungi via WhatsApp
                </a>

                <!-- Statistik -->
                <div class="row g-2">
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-3">
                            <div class="fw-bold fs-4"><?= (int)$customer['total_orders'] ?></div>
                            <div class="text-muted small">Total Pesanan</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-3">
                            <div class="fw-bold fs-6">Rp <?= number_format((float)$customer['total_spent'] / 1000, 0, ',', '.') ?>k</div>
                            <div class="text-muted small">Total Belanja</div>
                        </div>
                    </div>
                </div>

                <?php if ($customer['last_order_at']): ?>
                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-clock me-1"></i>
                    Terakhir order: <?= date('d M Y', strtotime($customer['last_order_at'])) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Riwayat pesanan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                Riwayat Pesanan Terakhir
                <span class="badge bg-secondary ms-2"><?= count($orders) ?></span>
            </div>
            <?php if (empty($orders)): ?>
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                    Belum ada riwayat pesanan.
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><code class="small"><?= esc($order['order_code']) ?></code></td>
                            <td>Meja <?= esc($order['table_number']) ?></td>
                            <td class="fw-semibold">Rp <?= number_format((float)$order['total'], 0, ',', '.') ?></td>
                            <td class="text-uppercase small"><?= esc($order['payment_method']) ?></td>
                            <td>
                                <?php
                                // Warna badge berdasarkan status
                                $sc = match($order['order_status']) {
                                    'Selesai'   => 'success',
                                    'Dibatalkan'=> 'danger',
                                    'Diproses'  => 'info',
                                    default     => 'warning',
                                };
                                ?>
                                <span class="badge bg-<?= $sc ?>-subtle text-<?= $sc ?>">
                                    <?= esc($order['order_status']) ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= date('d M Y', strtotime($order['created_at'])) ?>
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
echo view('layouts/Layout', ['title' => 'Detail Pelanggan', 'content' => $content]);
?>
