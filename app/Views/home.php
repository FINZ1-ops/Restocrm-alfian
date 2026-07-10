<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>RESTOCRM — Scan & Pesan</title>
    <meta name="description" content="Scan QR meja dan pesan makanan favorit Anda">
    <meta name="theme-color" content="#22343f">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: #f5f5f5;
            --card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --text-soft: #94a3b8;
            --accent: #ffc327;
            --accent-hover: #e5ae1f;
            --dark: #22343f;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top, #ffffff 0%, var(--bg) 65%);
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
        }

        .card {
            background: var(--card);
            border-radius: 28px;
            padding: 40px 28px 32px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(34, 52, 63, 0.10);
        }

        .logo {
            font-size: 26px;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -1px;
            margin-bottom: 4px;
        }
        .logo span { color: var(--accent); }

        .tagline {
            font-size: 13px;
            color: var(--text-soft);
            margin-bottom: 40px;
        }

        .illustration {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #fff8de 0%, #f4f0dd 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            font-size: 52px;
        }

        h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 36px;
            line-height: 1.6;
        }

        .btn-scan {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 18px 24px;
            background: var(--accent);
            color: #25343c;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, transform .1s;
            letter-spacing: -0.2px;
            margin-bottom: 16px;
        }
        .btn-scan:hover { background: var(--accent-hover); color: #25343c; }
        .btn-scan:active { transform: scale(0.98); }

        .btn-scan svg {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cbd5e1;
            font-size: 12px;
            margin-bottom: 16px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f1f5f9;
        }

        .links {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .links a {
            font-size: 13px;
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
        }
        .links a:hover { text-decoration: underline; }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #9aa6b2;
        }

        /* Kamera modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.85);
            z-index: 999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.show { display: flex; }

        .modal-box {
            background: #1f2c34;
            border-radius: 24px;
            padding: 24px;
            width: 100%;
            max-width: 360px;
            text-align: center;
        }
        .modal-title {
            color: #f1f5f9;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .modal-hint {
            color: #94a3b8;
            font-size: 12px;
            margin-bottom: 20px;
        }
        #qr-video {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 16px;
            object-fit: cover;
            background: #000;
            display: block;
        }
        .scanner-frame {
            position: relative;
            margin-bottom: 20px;
        }
        .scanner-frame::before {
            content: '';
            position: absolute;
            inset: 12px;
            border: 2px solid var(--accent);
            border-radius: 12px;
            z-index: 2;
            pointer-events: none;
        }
        .btn-close-modal {
            width: 100%;
            padding: 14px;
            background: #314550;
            color: #f1f5f9;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .scan-status {
            color: #94a3b8;
            font-size: 12px;
            margin-bottom: 12px;
            min-height: 18px;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">Resto<span>CRM</span></div>
    <div class="tagline">Digital Menu &amp; Ordering</div>

    <div class="illustration">🍽️</div>

    <h1>Scan QR Meja,<br>Pesan Sekarang</h1>
    <p class="subtitle">
        Arahkan kamera ke QR Code di meja Anda.<br>
        Tidak perlu daftar atau login.
    </p>

    <button class="btn-scan" onclick="openScanner()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/>
            <path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/>
            <rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/>
            <rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/>
        </svg>
        Scan QR Meja
    </button>

    <div class="divider">atau</div>

    <div class="links">
        <a href="<?= base_url('auth/login') ?>">Masuk ke Akun</a>
        <a href="<?= base_url('auth/register') ?>">Daftar Member</a>
    </div>
</div>

<div class="footer">RESTOCRM &copy; <?= date('Y') ?></div>

<!-- Modal Kamera Scanner -->
<div class="modal-overlay" id="scannerModal">
    <div class="modal-box">
        <div class="modal-title">Scan QR Code Meja</div>
        <div class="modal-hint">Arahkan kamera ke QR Code yang ada di meja Anda</div>
        <div class="scanner-frame">
            <video id="qr-video" playsinline autoplay muted></video>
        </div>
        <div class="scan-status" id="scanStatus">Menginisialisasi kamera...</div>
        <button class="btn-close-modal" onclick="closeScanner()">Tutup</button>
    </div>
</div>

<!-- jsQR untuk decode QR dari kamera -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
const BASE_URL = "<?= base_url() ?>";

let stream = null;
let animFrame = null;
let canvas, ctx, video;

async function openScanner() {
    document.getElementById('scannerModal').classList.add('show');
    document.getElementById('scanStatus').textContent = 'Menginisialisasi kamera...';

    video = document.getElementById('qr-video');
    canvas = document.createElement('canvas');
    ctx = canvas.getContext('2d', { willReadFrequently: true });

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            document.getElementById('scanStatus').textContent = 'Kamera aktif — cari QR Code...';
            scanLoop();
        };
    } catch (err) {
        document.getElementById('scanStatus').textContent =
            'Kamera tidak bisa diakses. Pastikan halaman ini dibuka lewat HTTPS.';
    }
}

function scanLoop() {
    if (!stream) return;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, imageData.width, imageData.height, {
        inversionAttempts: 'dontInvert',
    });

    if (code) {
        // Pastikan ini URL dari RESTOCRM (mengandung /scan/)
        if (code.data.includes('/scan/')) {
            document.getElementById('scanStatus').textContent = 'QR terdeteksi, membuka menu...';
            closeScanner();
            window.location.href = code.data;
            return;
        }
    }

    animFrame = requestAnimationFrame(scanLoop);
}

function closeScanner() {
    document.getElementById('scannerModal').classList.remove('show');
    if (animFrame) cancelAnimationFrame(animFrame);
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }
}
</script>

</body>
</html>