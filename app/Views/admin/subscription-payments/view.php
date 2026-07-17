<?php
/**
 * @var mixed $payment
 */
$statusColors = [
    'Belum Dibayar'       => 'secondary',
    'Menunggu Konfirmasi' => 'warning text-dark',
    'Lunas'                => 'success',
    'Terlambat'            => 'danger',
    'Ditolak'              => 'danger',
];
$color = $statusColors[$payment['status']] ?? 'secondary';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;"><?= esc($payment['invoice_number'] ?? '-') ?></h2>
        <span class="badge bg-<?= explode(' ', $color)[0] ?> bg-opacity-10 text-<?= explode(' ', $color)[0] ?> border border-<?= explode(' ', $color)[0] ?> rounded-pill px-3 py-2">
            <?= esc($payment['status']) ?>
        </span>
    </div>
    <a href="/admin/subscription-payments" class="btn btn-light rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3">Detail Invoice</h6>
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width:180px;">Restoran</td>
                        <td class="fw-semibold"><?= esc($payment['restaurant_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Paket</td>
                        <td><?= esc($payment['plan_name'] ?? '-') ?>
                            <?php if (!empty($payment['billing_cycle'])): ?>
                                <span class="text-muted small">(<?= $payment['billing_cycle'] === 'yearly' ? 'Tahunan' : 'Bulanan' ?>)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">WhatsApp</td>
                        <td><?= esc($payment['restaurant_whatsapp'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah</td>
                        <td class="fw-bold fs-5">Rp <?= number_format((float)($payment['amount'] ?? 0), 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Metode</td>
                        <td><?= esc($payment['payment_method'] ?? '—') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jatuh Tempo</td>
                        <td><?= !empty($payment['due_date']) ? date('d M Y', strtotime($payment['due_date'])) : '—' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Dibayar</td>
                        <td><?= !empty($payment['payment_date']) ? date('d M Y', strtotime($payment['payment_date'])) : '—' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td><?= nl2br(esc($payment['notes'] ?? '—')) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if (!empty($payment['proof_image'])): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3">Bukti Pembayaran</h6>
                <a href="/<?= esc($payment['proof_image']) ?>" target="_blank">
                    <img src="/<?= esc($payment['proof_image']) ?>" class="img-fluid rounded-3 border" style="max-height:400px;">
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <?php if (in_array($payment['status'], ['Belum Dibayar', 'Terlambat'])): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3">Upload Bukti Pembayaran</h6>
                <p class="text-muted small">Kalau restoran sudah kirim bukti transfer/QRIS lewat WhatsApp, unggah di sini.</p>
                <form method="POST" action="/admin/subscription-payments/<?= $payment['id'] ?>/upload-proof" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="file" name="proof_image" accept=".jpg,.jpeg,.png,.webp" class="form-control rounded-3 mb-3" required>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100">
                        <i class="bi bi-upload me-1"></i> Upload
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if (in_array($payment['status'], ['Menunggu Konfirmasi', 'Belum Dibayar', 'Terlambat'])): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3">Aksi</h6>

                <form method="POST" action="/admin/subscription-payments/<?= $payment['id'] ?>/confirm"
                      onsubmit="return confirm('Konfirmasi invoice ini sebagai LUNAS? Langganan resto akan otomatis diperpanjang.')" class="mb-2">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success rounded-pill px-4 w-100">
                        <i class="bi bi-check-circle me-1"></i> Konfirmasi Lunas
                    </button>
                </form>

                <form method="POST" action="/admin/subscription-payments/<?= $payment['id'] ?>/reject"
                      onsubmit="return confirm('Tolak pembayaran ini?')">
                    <?= csrf_field() ?>
                    <input type="text" name="reject_reason" class="form-control rounded-3 mb-2" placeholder="Alasan penolakan (opsional)">
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4 w-100">
                        <i class="bi bi-x-circle me-1"></i> Tolak Pembayaran
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($payment['status'] === 'Lunas'): ?>
        <div class="alert alert-success rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            Dikonfirmasi oleh user #<?= esc($payment['confirmed_by'] ?? '-') ?>
            pada <?= !empty($payment['confirmed_at']) ? date('d M Y H:i', strtotime($payment['confirmed_at'])) : '-' ?>.
            Langganan resto sudah diperpanjang.
        </div>
        <?php endif; ?>
    </div>
</div>