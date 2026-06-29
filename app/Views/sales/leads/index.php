<?php
/** @var array $leads */
/** @var object $pager */
/** @var array $statusCounts */
/** @var string|null $currentStatus */
/** @var string|null $currentSearch */
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Leads Saya</h2>
        <p class="text-secondary mb-0">Prospek yang ditugaskan kepada Anda</p>
    </div>
</div>

<div class="d-flex gap-2 flex-wrap mb-4">
    <a href="<?= base_url('sales/leads') ?>" class="btn btn-sm rounded-pill <?= empty($currentStatus) ? 'btn-dark' : 'btn-outline-secondary' ?>">Semua</a>
    <?php foreach ($statusCounts ?? [] as $st => $cnt): ?>
        <a href="<?= base_url('sales/leads?status=' . urlencode($st)) ?>" class="btn btn-sm rounded-pill <?= ($currentStatus ?? '') === $st ? 'btn-dark' : 'btn-outline-secondary' ?>">
            <?= esc($st) ?> <span class="badge bg-secondary ms-1"><?= (int) $cnt ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Bisnis</th>
                    <th>Pemilik</th>
                    <th>WhatsApp</th>
                    <th>Status</th>
                    <th>Follow-up</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada lead ditugaskan.</td></tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                        <tr>
                            <td class="fw-medium"><?= esc($lead['business_name']) ?></td>
                            <td><?= esc($lead['owner_name']) ?></td>
                            <td><?= esc($lead['whatsapp']) ?></td>
                            <td><?= esc($lead['status']) ?></td>
                            <td class="text-muted small"><?= esc($lead['next_followup_date'] ?? '-') ?></td>
                            <td>
                                <a href="<?= base_url('sales/leads/' . $lead['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?>
        <div class="card-footer bg-white"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>
<?= ob_get_clean() ?>
