<?php
/**
 * @var mixed $customers
 * @var mixed $pager
 * @var mixed $currentSearch
 */
/**
 * View: resto/customers/index.php
 * Daftar pelanggan restoran — otomatis terbentuk dari riwayat order
 * Variabel: $customers, $pager, $currentSearch
 */
ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">CRM Pelanggan</h2>
        <p class="text-secondary mb-0">Pelanggan tersimpan otomatis dari setiap order</p>
    </div>
</div>

<!-- Search pelanggan -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari nama atau nomor WhatsApp..."
                   value="<?= esc($currentSearch ?? '') ?>">
            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="bi bi-search"></i>
            </button>
            <?php if ($currentSearch): ?>
                <a href="/resto/customers" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Pelanggan</th>
                    <th>WhatsApp</th>
                    <th class="text-center">Total Order</th>
                    <th class="text-end">Total Belanja</th>
                    <th>Order Terakhir</th>
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                        Belum ada data pelanggan.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Avatar dari inisial nama -->
                            <div class="bg-primary-subtle text-primary rounded-circle fw-bold d-flex align-items-center justify-content-center"
                                 style="width:34px;height:34px;font-size:13px;flex-shrink:0;">
                                <?= strtoupper(mb_substr($c['name'], 0, 1)) ?>
                            </div>
                            <span class="fw-semibold"><?= esc($c['name']) ?></span>
                        </div>
                    </td>
                    <td>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $c['whatsapp']) ?>"
                           target="_blank" class="text-success text-decoration-none small">
                            <i class="bi bi-whatsapp me-1"></i><?= esc($c['whatsapp']) ?>
                        </a>
                    </td>
                    <td class="text-center fw-semibold"><?= (int)$c['total_orders'] ?>x</td>
                    <td class="text-end fw-semibold">
                        Rp&nbsp;<?= number_format((float)$c['total_spent'], 0, ',', '.') ?>
                    </td>
                    <td class="text-muted small">
                        <?= $c['last_order_at'] ? date('d M Y', strtotime($c['last_order_at'])) : '—' ?>
                    </td>
                    <td>
                        <a href="/resto/customers/<?= $c['id'] ?>"
                           class="btn btn-sm btn-outline-primary">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <?php if (!empty($pager)): ?>
    <div class="card-footer bg-transparent py-3 px-4">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>

<?= ob_get_clean() ?>
