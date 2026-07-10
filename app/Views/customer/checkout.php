<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — <?= esc($restaurant['name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root { 
            --accent: #ffc327;
            --accent-hover: #e5ae1f;
            --dark: #22343f;
            --bg-color: #f5f5f5;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --safe-bottom: env(safe-area-inset-bottom, 20px); 
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            padding-bottom: calc(100px + var(--safe-bottom));
            -webkit-font-smoothing: antialiased;
        }

        /* ── Header ── */
        .header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: var(--text-main);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 40;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        .header a { 
            color: var(--text-main); 
            font-size: 24px; 
            text-decoration: none; 
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            background: #f1f5f9;
            transition: transform 0.2s;
        }
        .header a:active { transform: scale(0.9); }
        .header h1 { font-size: 18px; font-weight: 800; letter-spacing: -0.3px; line-height: 1.2; }
        .header .sub { font-size: 13px; color: var(--text-muted); font-weight: 500; }

        /* ── Section ── */
        .section {
            background: #fff;
            border-radius: 20px;
            margin: 16px 16px 0;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(226, 232, 240, 0.6);
        }
        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.2px;
            margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }
        .section-title::before {
            content: '';
            width: 4px; height: 16px;
            background: var(--accent);
            border-radius: 4px;
            display: inline-block;
        }

        /* ── Ringkasan item ── */
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .cart-item:last-child { border-bottom: none; }
        .item-name { font-size: 15px; font-weight: 700; color: var(--text-main); margin-bottom: 2px; }
        .item-qty { font-size: 13px; color: var(--text-muted); font-weight: 500; }
        .item-price { font-size: 15px; font-weight: 800; color: var(--accent); }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #f1f5f9;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
        }

        /* ── Form input ── */
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            font-size: 15px;
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255, 195, 39, 0.18);
        }
        .form-group input::placeholder, .form-group textarea::placeholder { color: #94a3b8; }
        .form-group textarea { resize: none; height: 80px; }

        /* ── Pilihan pembayaran ── */
        .pay-options { display: flex; gap: 12px; }
        .pay-option {
            flex: 1;
            padding: 16px 12px;
            border: 2px solid #e2e8f0;
            background: #fff;
            border-radius: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .pay-option input[type="radio"] { display: none; }
        .pay-option .pay-icon { font-size: 28px; display: block; margin-bottom: 6px; transition: transform 0.2s; }
        .pay-option .pay-label { font-size: 14px; font-weight: 700; color: var(--text-muted); }
        .pay-option.selected {
            border-color: var(--accent);
            background: rgba(255, 195, 39, 0.10);
            box-shadow: 0 4px 12px rgba(255, 195, 39, 0.16);
        }
        .pay-option.selected .pay-label { color: var(--accent); }
        .pay-option.selected .pay-icon { transform: scale(1.1); }
        .pay-option:active { transform: scale(0.96); }

        /* ── QRIS upload section ── */
        #qris-section { display: none; margin-top: 16px; }
        #qris-section.show { display: block; }
        .qris-img {
            width: 100%;
            max-width: 240px;
            display: block;
            margin: 0 auto 16px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .upload-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed rgba(255, 195, 39, 0.55);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            color: var(--accent);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: #fffbeb;
            transition: background 0.2s;
            width: 100%;
            box-sizing: border-box;
        }
        .upload-box:active { background: #fef3c7; }
        .upload-box .upload-icon { font-size: 24px; margin-bottom: 6px; }
        .upload-box .upload-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; font-weight: 400; }
        /* Input file disembunyikan lewat posisi absolut & ukuran 0,
           bukan display:none — beberapa mobile browser merender label
           yang membungkus input file ber-display:none secara tidak
           konsisten (memecah layout child elements-nya). */
        .upload-box input[type="file"] {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
            overflow: hidden;
        }
        #proof-preview { max-width: 100%; border-radius: 12px; margin-top: 12px; display: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

        /* ── Tombol submit sticky ── */
        .sticky-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 16px 20px calc(var(--safe-bottom) + 16px);
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            z-index: 40;
        }
        .btn-order {
            width: 100%;
            padding: 16px;
            background: var(--accent);
            color: #25343c;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 20px rgba(255, 195, 39, 0.28);
            transition: all 0.2s;
            display: flex; justify-content: center; align-items: center; gap: 8px;
        }
        .btn-order:active { transform: scale(0.97); box-shadow: 0 4px 12px rgba(255, 195, 39, 0.28); }
        .btn-order:disabled { background: #cbd5e1; box-shadow: none; cursor: not-allowed; transform: none; }

        /* Alert error */
        .alert-err {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 14px;
            margin: 16px 16px 0;
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <a href="javascript:history.back()">&#8592;</a>
    <div>
        <div class="h1">Checkout</div>
        <div class="sub">Meja <?= esc($table['table_number']) ?> · <?= esc($table['area_name']) ?></div>
    </div>
</div>

<?php
/**
 * @var mixed $restaurant
 * @var mixed $table
 * @var mixed $errors
 * @var mixed $cart
 * @var mixed $total
 * @var mixed $qris
 */
if (!empty($errors)): ?>
<div class="alert-err">
    <?php foreach ($errors as $e): ?><div>• <?= esc($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" action="/order/process" enctype="multipart/form-data" id="checkoutForm">
    <?= csrf_field() ?>
    <input type="hidden" name="table_id" value="<?= $table['id'] ?>">

    <!-- Ringkasan pesanan -->
    <div class="section">
        <div class="section-title">Ringkasan Pesanan</div>
        <?php foreach ($cart as $item): ?>
        <div class="cart-item">
            <div>
                <div class="item-name"><?= esc($item['name']) ?></div>
                <div class="item-qty">× <?= $item['quantity'] ?><?= !empty($item['notes']) ? ' · ' . esc($item['notes']) : '' ?></div>
            </div>
            <div class="item-price">Rp <?= number_format((float)$item['subtotal'], 0, ',', '.') ?></div>
        </div>
        <?php endforeach; ?>
        <div class="total-row">
            <span>Total</span>
            <span style="color:var(--accent)">Rp <?= number_format((float)$total, 0, ',', '.') ?></span>
        </div>
    </div>

    <!-- Data diri -->
    <div class="section">
        <div class="section-title">Data Pemesan</div>
        <div class="form-group">
            <label>Nama <span style="color:#ef4444">*</span></label>
            <input type="text" name="customer_name" placeholder="Nama kamu"
                   value="<?= esc(old('customer_name')) ?>" required autocomplete="name">
        </div>
        <div class="form-group">
            <label>No. WhatsApp <span style="color:#ef4444">*</span></label>
            <input type="tel" name="customer_whatsapp" placeholder="08xxxxxxxxxx"
                   value="<?= esc(old('customer_whatsapp')) ?>" required autocomplete="tel">
        </div>
        <div class="form-group">
            <label>Catatan (opsional)</label>
            <textarea name="notes" placeholder="Alergi, tingkat pedas, dll..."><?= esc(old('notes')) ?></textarea>
        </div>
    </div>

    <!-- Metode pembayaran -->
    <div class="section">
        <div class="section-title">Metode Pembayaran</div>
        <div class="pay-options">
            <label class="pay-option selected" id="opt-cash">
                <input type="radio" name="payment_method" value="cash" checked onchange="selectPay('cash')">
                <span class="pay-icon">💵</span>
                <span class="pay-label">Cash</span>
            </label>
            <?php if (!empty($qris) && $qris['is_active']): ?>
            <label class="pay-option" id="opt-qris">
                <input type="radio" name="payment_method" value="qris" onchange="selectPay('qris')">
                <span class="pay-icon">📱</span>
                <span class="pay-label">QRIS</span>
            </label>
            <?php endif; ?>
        </div>

        <!-- Section upload bukti QRIS (muncul jika pilih QRIS) -->
        <?php if (!empty($qris) && $qris['is_active']): ?>
        <div id="qris-section">
            <p style="font-size:13px;color:#64748b;margin:12px 0 8px;">Scan QR di bawah lalu upload bukti transfer:</p>
            <img src="/<?= esc($qris['qris_image']) ?>" class="qris-img" alt="QRIS <?= esc($qris['merchant_name']) ?>">
            <label class="upload-box" for="proof-input">
                <span class="upload-icon">📷</span>
                <span>Upload Bukti Pembayaran</span>
                <span class="upload-hint">JPG / PNG maks 5MB</span>
                <input type="file" id="proof-input" name="payment_proof"
                       accept="image/*" onchange="previewProof(this)">
            </label>
            <img id="proof-preview" src="" alt="Preview bukti">
        </div>
        <?php endif; ?>
    </div>

</form>

<!-- Tombol pesan sticky -->
<div class="sticky-footer">
    <button class="btn-order" onclick="submitOrder()">
        Kirim Pesanan · Rp <?= number_format((float)$total, 0, ',', '.') ?>
    </button>
</div>

<script>
// Pilih metode pembayaran — toggle tampilan section QRIS
function selectPay(method) {
    document.getElementById('opt-cash').classList.toggle('selected', method === 'cash');
    const qrisOpt = document.getElementById('opt-qris');
    if (qrisOpt) qrisOpt.classList.toggle('selected', method === 'qris');

    const qrisSection = document.getElementById('qris-section');
    if (qrisSection) qrisSection.classList.toggle('show', method === 'qris');

    // Wajibkan upload bukti jika QRIS
    const proofInput = document.getElementById('proof-input');
    if (proofInput) proofInput.required = (method === 'qris');
}

// Preview gambar bukti QRIS sebelum upload
function previewProof(input) {
    const preview = document.getElementById('proof-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Submit form dengan validasi sederhana
function submitOrder() {
    const form    = document.getElementById('checkoutForm');
    const name    = form.querySelector('[name="customer_name"]').value.trim();
    const wa      = form.querySelector('[name="customer_whatsapp"]').value.trim();
    const method  = form.querySelector('[name="payment_method"]:checked').value;
    const proof   = document.getElementById('proof-input');

    if (!name) { alert('Nama wajib diisi'); return; }
    if (!wa)   { alert('No. WhatsApp wajib diisi'); return; }
    if (method === 'qris' && proof && !proof.files.length) {
        alert('Upload bukti pembayaran QRIS terlebih dahulu'); return;
    }

    form.submit();
}
</script>
</body>
</html>