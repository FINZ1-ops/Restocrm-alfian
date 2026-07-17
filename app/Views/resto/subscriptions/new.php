<?php
/**
 * @var mixed $plan
 * @var mixed $old
 */
$old = $old ?? [];
$val = fn($field, $default = '') => old($field, $old[$field] ?? $default);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 text-dark" style="font-size:20px;">Beli Paket <?= esc($plan['name']) ?></h2>
        <p class="text-secondary mb-0" style="font-size:13px;">Lengkapi pembelian paket dan invois akan tercatat otomatis.</p>
    </div>
    <a href="/resto/subscriptions" class="btn btn-light rounded-pill px-4 fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger rounded-3 mb-4">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form method="POST" action="/resto/subscriptions">
            <?= csrf_field() ?>
            <input type="hidden" name="plan_id" value="<?= esc($plan['id']) ?>">

            <div class="mb-4">
                <h5 class="fw-semibold mb-2">Paket</h5>
                <div class="border rounded-3 p-4 bg-light">
                    <h4 class="fw-bold mb-2"><?= esc($plan['name']) ?></h4>
                    <p class="text-muted mb-2"><?= esc($plan['description']) ?></p>
                    <div class="d-flex gap-3 flex-wrap">
                        <div>
                            <div class="text-muted small">Bulanan</div>
                            <div class="fw-semibold">Rp <?= number_format((float)$plan['price_monthly'], 0, ',', '.') ?></div>
                        </div>
                        <div>
                            <div class="text-muted small">Tahunan</div>
                            <div class="fw-semibold">Rp <?= number_format((float)$plan['price_yearly'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Siklus Tagihan</label>
                <select name="billing_cycle" class="form-select rounded-3" required>
                    <option value="monthly" <?= $val('billing_cycle', 'monthly') === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                    <option value="yearly" <?= $val('billing_cycle') === 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Jumlah yang akan dibayar</label>
                <div class="border rounded-3 p-3 bg-white">
                    <div class="fw-semibold" id="paymentAmount">Rp <?= number_format((float)$plan['price_monthly'], 0, ',', '.') ?></div>
                    <div class="text-muted small">Pembayaran akan langsung tercatat sebagai invoice lunas.</div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium">
                <i class="bi bi-cart-check me-1"></i> Bayar & Catat Invoice
            </button>
        </form>
    </div>
</div>

<script>
const billingCycle = document.querySelector('[name="billing_cycle"]');
const amountEl = document.getElementById('paymentAmount');
const monthly = <?= (int)$plan['price_monthly'] ?>;
const yearly = <?= (int)$plan['price_yearly'] ?>;

function updateAmount() {
    const value = billingCycle.value;
    amountEl.textContent = value === 'yearly'
        ? 'Rp ' + yearly.toLocaleString('id-ID')
        : 'Rp ' + monthly.toLocaleString('id-ID');
}

billingCycle.addEventListener('change', updateAmount);
updateAmount();
</script>
