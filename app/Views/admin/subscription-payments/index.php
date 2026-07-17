<?php
/**
 * @var mixed $payments
 * @var mixed $currentStatus
 * @var mixed $isOverdueView
 */
$statusColors = [
    'Belum Dibayar'       => 'secondary',
    'Menunggu Konfirmasi' => 'warning text-dark',
    'Lunas'                => 'success',
    'Terlambat'            => 'danger',
    'Ditolak'              => 'danger',
];
$statusList = array_keys($statusColors);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;">
            <?= $isOverdueView ? 'Tagihan Jatuh Tempo' : 'Pembayaran Langganan' ?>
        </h2>
        <p class="text-secondary mb-0" style="font-size:13px;">
            <?= $isOverdueView
                ? 'Invoice yang sudah lewat tanggal jatuh tempo dan belum lunas'
                : 'Semua invoice pembayaran langganan aplikasi dari restoran' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/subscription-payments/overdue" class="btn btn-outline-danger rounded-pill px-3 fw-medium">
            <i class="bi bi-exclamation-circle me-1"></i> Jatuh Tempo
        </a>
        <a href="/admin/subscription-payments/new" class="btn btn-primary shadow-sm rounded-pill px-4 fw-medium">
            <i class="bi bi-plus-lg me-1"></i> Buat Invoice
        </a>
    </div>
</div>

<?php if (!$isOverdueView): ?>
<div class="d-flex gap-2 flex-wrap mb-4">
    <a href="/admin/subscription-payments" class="btn btn-sm rounded-pill <?= empty($currentStatus) ? 'btn-dark' : 'btn-outline-secondary' ?> px-3">
        Semua
    </a>
    <?php foreach ($statusList as $st): ?>
        <a href="/admin/subscription-payments?status=<?= urlencode($st) ?>"
           class="btn btn-sm rounded-pill <?= ($currentStatus ?? '') === $st ? 'btn-dark' : 'btn-outline-secondary' ?> px-3">
            <?= $st ?>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th class="ps-4 border-0">Invoice</th>
                        <th class="border-0">Restoran</th>
                        <th class="border-0">Jumlah</th>
                        <th class="border-0">Jatuh Tempo</th>
                        <th class="border-0">Status</th>
                        <th class="text-end pe-4 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($payments)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data invoice.</td></tr>
                    <?php else: ?>
                        <?php foreach ($payments as $p): ?>
                            <?php $color = $statusColors[$p['status']] ?? 'secondary'; ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark"><?= esc($p['invoice_number'] ?? '-') ?></div>
                                    <div class="text-muted small"><?= isset($p['created_at']) ? date('d M Y', strtotime($p['created_at'])) : '-' ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($p['restaurant_name'] ?? '-') ?></div>
                                    <div class="text-muted small">
                                        <?= esc($p['plan_name'] ?? '') ?>
                                        <?= !empty($p['restaurant_whatsapp']) ? ' · ' . esc($p['restaurant_whatsapp']) : '' ?>
                                    </div>
                                </td>
                                <td class="fw-semibold text-dark">Rp <?= number_format((float)($p['amount'] ?? 0), 0, ',', '.') ?></td>
                                <td>
                                    <?php if (!empty($p['due_date'])): ?>
                                        <?php
                                        $due = new DateTime($p['due_date']);
                                        $isPast = $due < new DateTime() && !in_array($p['status'], ['Lunas', 'Ditolak']);
                                        ?>
                                        <span class="<?= $isPast ? 'text-danger fw-bold' : 'text-secondary' ?> small">
                                            <?= $due->format('d M Y') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= explode(' ', $color)[0] ?> bg-opacity-10 text-<?= explode(' ', $color)[0] ?> border border-<?= explode(' ', $color)[0] ?> rounded-pill px-3 py-2">
                                        <?= esc($p['status']) ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="/admin/subscription-payments/<?= $p['id'] ?>" class="btn btn-sm btn-light text-primary rounded-circle" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>