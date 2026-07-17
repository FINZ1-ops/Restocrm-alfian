<?php
/**
 * @var mixed $restaurants
 * @var mixed $plans
 * @var mixed $errors
 * @var mixed $old
 */
$old = $old ?? [];
$val = static function (string $key, $default = '') use ($old) {
    return $old[$key] ?? old($key) ?? $default;
};
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0 text-dark" style="font-size:20px;">Buat Invoice Baru</h2>
    <a href="/admin/subscription-payments" class="btn btn-light rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger rounded-4">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form method="POST" action="/admin/subscription-payments">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Restoran</label>
                <select name="restaurant_id" class="form-select rounded-3" required>
                    <option value="">-- Pilih restoran --</option>
                    <?php foreach ($restaurants as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= (string) $val('restaurant_id') === (string) $r['id'] ? 'selected' : '' ?>>
                            <?= esc($r['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Paket Langganan</label>
                    <select name="plan_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih paket --</option>
                        <?php foreach ($plans as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= (string) $val('plan_id') === (string) $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['name']) ?> — Rp <?= number_format((float) $p['price_monthly'], 0, ',', '.') ?>/bulan
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Siklus Tagihan</label>
                    <select name="billing_cycle" class="form-select rounded-3" required>
                        <option value="monthly" <?= $val('billing_cycle', 'monthly') === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                        <option value="yearly" <?= $val('billing_cycle') === 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
                <input type="date" name="due_date" class="form-control rounded-3"
                       value="<?= esc($val('due_date', date('Y-m-d', strtotime('+7 days')))) ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Catatan (opsional)</label>
                <textarea name="notes" class="form-control rounded-3" rows="3"><?= esc($val('notes')) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium">
                <i class="bi bi-check-lg me-1"></i> Buat Invoice
            </button>
        </form>
    </div>
</div>
