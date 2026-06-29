<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil — RESTOCRM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 36px 28px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        /* Animasi checkmark sukses */
        .success-circle {
            width: 80px; height: 80px;
            background: #22c55e;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            animation: popIn .4s cubic-bezier(.175,.885,.32,1.275);
        }
        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        h2 { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: #64748b; margin-bottom: 28px; }

        /* Kotak detail pesanan */
        .order-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 20px;
            text-align: left;
            margin-bottom: 24px;
        }
        .order-box .label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
        .order-box .value { font-size: 15px; font-weight: 600; color: #1e293b; margin-top: 2px; margin-bottom: 14px; }
        .order-box .value:last-child { margin-bottom: 0; }

        /* Badge status pembayaran */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-warning { background: #fef9c3; color: #a16207; }
        .badge-success { background: #dcfce7; color: #15803d; }

        .info-text {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
            padding: 14px;
            background: #fffbeb;
            border-radius: 10px;
            border-left: 3px solid #f59e0b;
        }

        .btn {
            display: block;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
        }
        .btn-primary { background: #6366f1; color: #fff; margin-bottom: 10px; }
        .btn-outline {
            border: 2px solid #e2e8f0;
            color: #64748b;
            font-weight: 500;
            font-size: 14px;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="success-circle">✓</div>

    <h2>Pesanan Masuk!</h2>
    <p class="subtitle">Pesananmu sudah diterima oleh restoran</p>

    <?php
/**
 * @var mixed $order
 */
if ($order): ?>
    <div class="order-box">
        <div class="label">Kode Pesanan</div>
        <div class="value" style="font-family: monospace; font-size: 18px; color: #6366f1;">
            <?= esc($order['order_code']) ?>
        </div>

        <div class="label">Meja</div>
        <div class="value">Meja <?= esc($order['table_number']) ?> — <?= esc($order['area_name']) ?></div>

        <div class="label">Total</div>
        <div class="value">Rp <?= number_format((float)$order['total'], 0, ',', '.') ?></div>

        <div class="label">Pembayaran</div>
        <div class="value">
            <span class="badge <?= $order['payment_status'] === 'Lunas' ? 'badge-success' : 'badge-warning' ?>">
                <?= esc($order['payment_status']) ?>
            </span>
            <span style="margin-left: 8px; font-size: 13px; color: #64748b; font-weight: 400;">
                via <?= strtoupper(esc($order['payment_method'])) ?>
            </span>
        </div>
    </div>

    <?php if ($order['payment_method'] === 'cash'): ?>
    <div class="info-text">
        💵 Silakan bayar ke kasir setelah pesananmu selesai disiapkan.
    </div>
    <?php elseif ($order['payment_status'] === 'Menunggu Konfirmasi'): ?>
    <div class="info-text">
        🕐 Bukti pembayaran QRIS sedang diverifikasi oleh kasir. Harap tunggu konfirmasi.
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Tombol aksi -->
    <a href="javascript:history.back()" class="btn btn-primary">Pesan Lagi</a>
    <a href="javascript:window.close()" class="btn btn-outline">Tutup</a>
</div>
</body>
</html>
