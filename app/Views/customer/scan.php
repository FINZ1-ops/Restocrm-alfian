<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Scan QR Meja — RESTOCRM</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #6366f1;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        html, body {
            height: 100%;
            background: #0f172a;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow: hidden;
            color: #fff;
        }

        /* ── Layout utama ── */
        .scanner-app {
            display: flex;
            flex-direction: column;
            height: 100dvh;
            height: 100vh; /* fallback */
            position: relative;
        }

        /* ── Topbar ── */
        .topbar {
            position: relative;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: calc(var(--safe-top) + 12px) 16px 12px;
            background: linear-gradient(to bottom, rgba(15,23,42,0.95), transparent);
        }
        .topbar-back {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #fff;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            background: rgba(255,255,255,0.12);
            border: none;
            border-radius: 20px;
            padding: 8px 14px;
            cursor: pointer;
        }
        .topbar-back:hover { background: rgba(255,255,255,0.2); }
        .topbar-title {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }
        .topbar-spacer { width: 80px; } /* balance tombol kiri */

        /* ── Area kamera ── */
        .camera-area {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        /* Video element mengisi penuh */
        #camera-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Overlay gelap + lubang kotak scan */
        .scan-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        /* 4 layer shadow gelap membentuk "lubang" di tengah */
        .overlay-top    { position: absolute; top: 0;    left: 0;    right: 0; background: rgba(0,0,0,0.55); }
        .overlay-bottom { position: absolute; bottom: 0; left: 0;    right: 0; background: rgba(0,0,0,0.55); }
        .overlay-left   { position: absolute; left: 0;   background: rgba(0,0,0,0.55); }
        .overlay-right  { position: absolute; right: 0;  background: rgba(0,0,0,0.55); }

        /* Kotak scan transparan di tengah */
        .scan-box {
            position: relative;
            width: min(260px, 70vw);
            height: min(260px, 70vw);
            z-index: 2;
        }

        /* Pojok-pojok kotak scan */
        .corner {
            position: absolute;
            width: 28px;
            height: 28px;
            border-color: #fff;
            border-style: solid;
            border-radius: 4px;
        }
        .corner-tl { top: 0;    left: 0;  border-width: 3px 0 0 3px; border-top-left-radius: 6px; }
        .corner-tr { top: 0;    right: 0; border-width: 3px 3px 0 0; border-top-right-radius: 6px; }
        .corner-bl { bottom: 0; left: 0;  border-width: 0 0 3px 3px; border-bottom-left-radius: 6px; }
        .corner-br { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-bottom-right-radius: 6px; }

        /* Garis scan animasi */
        .scan-line {
            position: absolute;
            left: 4px;
            right: 4px;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--accent), transparent);
            border-radius: 2px;
            animation: scanMove 2s ease-in-out infinite;
        }
        @keyframes scanMove {
            0%   { top: 8px;  opacity: 1; }
            48%  { top: calc(100% - 8px); opacity: 1; }
            50%  { top: calc(100% - 8px); opacity: 0; }
            52%  { top: 8px;  opacity: 0; }
            54%  { top: 8px;  opacity: 1; }
            100% { top: calc(100% - 8px); opacity: 1; }
        }

        /* ── Bottom bar ── */
        .bottom-bar {
            position: relative;
            z-index: 20;
            background: linear-gradient(to top, rgba(15,23,42,0.97), transparent);
            padding: 16px 24px calc(var(--safe-bottom) + 24px);
            text-align: center;
        }
        .bottom-hint {
            font-size: 14px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 16px;
            line-height: 1.5;
        }

        /* ── State: error kamera / PC ── */
        .camera-error {
            display: none;
            position: absolute;
            inset: 0;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 32px;
            text-align: center;
            background: #0f172a;
        }
        .camera-error.show { display: flex; }
        .camera-error .icon { font-size: 56px; opacity: 0.5; }
        .camera-error h3 { font-size: 18px; font-weight: 600; color: #fff; }
        .camera-error p { font-size: 14px; color: rgba(255,255,255,0.55); line-height: 1.6; }

        /* ── State: sukses scan ── */
        .scan-success {
            display: none;
            position: absolute;
            inset: 0;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            background: rgba(15,23,42,0.92);
            z-index: 30;
        }
        .scan-success.show { display: flex; }
        .success-icon {
            width: 72px; height: 72px;
            background: #22c55e;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            animation: popIn .3s ease;
        }
        @keyframes popIn {
            from { transform: scale(0.5); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }
        .scan-success p { font-size: 15px; color: rgba(255,255,255,0.8); }

        /* ── Tombol ── */
        .btn-light-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 24px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-light-outline:hover { background: rgba(255,255,255,0.18); }

        /* ── Torch/Flash toggle ── */
        .torch-btn {
            background: rgba(255,255,255,0.12);
            border: none;
            border-radius: 50%;
            width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #fff;
            font-size: 20px;
        }
        .torch-btn.on { background: #fbbf24; color: #0f172a; }
    </style>
</head>
<body>

<div class="scanner-app">

    <!-- Topbar -->
    <div class="topbar">
        <a href="/customer/dashboard" class="topbar-back">
            ← Kembali
        </a>
        <span class="topbar-title">Scan QR Meja</span>
        <button class="torch-btn" id="torchBtn" title="Flash" style="display:none" onclick="toggleTorch()">
            &#128294;
        </button>
        <div class="topbar-spacer" id="torchSpacer"></div>
    </div>

    <!-- Area Kamera -->
    <div class="camera-area">
        <video id="camera-video" autoplay muted playsinline></video>

        <!-- Overlay + kotak scan -->
        <div class="scan-overlay" id="scanOverlay">
            <div class="overlay-top"    id="ovTop"></div>
            <div class="overlay-left"   id="ovLeft"></div>
            <div class="overlay-right"  id="ovRight"></div>
            <div class="overlay-bottom" id="ovBottom"></div>

            <div class="scan-box" id="scanBox">
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>
                <div class="scan-line"></div>
            </div>
        </div>

        <!-- Error state (kamera tidak tersedia / PC) -->
        <div class="camera-error" id="cameraError">
            <div class="icon">📷</div>
            <h3>Kamera tidak tersedia</h3>
            <p id="errorMsg">Halaman ini membutuhkan kamera.<br>
               Buka di smartphone dan izinkan akses kamera.</p>
        </div>

        <!-- Sukses scan -->
        <div class="scan-success" id="scanSuccess">
            <div class="success-icon">✓</div>
            <p id="successMsg">QR berhasil dibaca, mengalihkan...</p>
        </div>
    </div>

    <!-- Bottom bar -->
    <div class="bottom-bar">
        <p class="bottom-hint" id="hintText">
            Arahkan kamera ke QR Code yang ada di meja
        </p>
    </div>

</div>

<!-- html5-qrcode library (CDN) -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// ── Posisi overlay dinamis mengikuti kotak scan ──
function positionOverlays() {
    const box   = document.getElementById('scanBox');
    const area  = box.parentElement;
    const rect  = box.getBoundingClientRect();
    const aRect = area.getBoundingClientRect();

    const top    = rect.top    - aRect.top;
    const left   = rect.left   - aRect.left;
    const right  = aRect.right  - rect.right;
    const bottom = aRect.bottom - rect.bottom;
    const boxH   = rect.height;
    const boxW   = rect.width;

    document.getElementById('ovTop').style.cssText    = `height:${top}px`;
    document.getElementById('ovBottom').style.cssText = `height:${bottom}px`;
    document.getElementById('ovLeft').style.cssText   = `top:${top}px;width:${left}px;height:${boxH}px`;
    document.getElementById('ovRight').style.cssText  = `top:${top}px;width:${right}px;height:${boxH}px`;
}
window.addEventListener('resize', positionOverlays);

// ── Base URL dari PHP ──
const BASE_URL = "<?= base_url() ?>";

let html5QrCode = null;
let torchOn     = false;
let currentTrack = null;
let scanDone    = false;

async function startScanner() {
    // Cek dukungan browser
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        showError('Browser ini tidak mendukung akses kamera.<br>Gunakan Chrome atau Safari versi terbaru.');
        return;
    }

    try {
        // Minta akses kamera belakang
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }
        });

        const video = document.getElementById('camera-video');
        video.srcObject = stream;
        await video.play();

        // Cek torch support
        currentTrack = stream.getVideoTracks()[0];
        const capabilities = currentTrack.getCapabilities?.() ?? {};
        if (capabilities.torch) {
            document.getElementById('torchBtn').style.display = 'flex';
            document.getElementById('torchSpacer').style.display = 'none';
        }

        // Posisikan overlay setelah video siap
        video.addEventListener('loadedmetadata', positionOverlays);
        positionOverlays();

        // Mulai decode QR menggunakan html5-qrcode (decode dari frame video)
        startQrDecode(stream);

    } catch (err) {
        if (err.name === 'NotAllowedError') {
            showError('Izin kamera ditolak.<br>Buka pengaturan browser dan izinkan akses kamera, lalu muat ulang halaman.');
        } else if (err.name === 'NotFoundError') {
            showError('Kamera tidak ditemukan.<br>Pastikan perangkat memiliki kamera dan tidak sedang digunakan aplikasi lain.');
        } else {
            showError('Gagal membuka kamera: ' + err.message);
        }
    }
}

function startQrDecode(stream) {
    // Buat elemen canvas tersembunyi untuk decode frame
    const canvas  = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const video   = document.getElementById('camera-video');

    // html5-qrcode: gunakan metode decodeFromVideoElement
    html5QrCode = new Html5Qrcode('__qr_hidden__', { verbose: false });

    // Pendekatan: decode frame secara manual via requestAnimationFrame
    // untuk tidak bergantung pada elemen div khusus
    const SCAN_INTERVAL = 300; // ms antar scan
    let lastScan = 0;

    function decodeFrame(timestamp) {
        if (scanDone) return;

        if (timestamp - lastScan >= SCAN_INTERVAL) {
            lastScan = timestamp;
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Crop ke area scan box untuk akurasi lebih baik
                const scanBoxEl = document.getElementById('scanBox');
                const vRect     = video.getBoundingClientRect();
                const sRect     = scanBoxEl.getBoundingClientRect();

                // Hitung crop relatif ke video frame
                const scaleX = canvas.width  / vRect.width;
                const scaleY = canvas.height / vRect.height;
                const cropX  = (sRect.left - vRect.left) * scaleX;
                const cropY  = (sRect.top  - vRect.top)  * scaleY;
                const cropW  = sRect.width  * scaleX;
                const cropH  = sRect.height * scaleY;

                // Buat canvas crop
                const cropCanvas  = document.createElement('canvas');
                cropCanvas.width  = cropW;
                cropCanvas.height = cropH;
                cropCanvas.getContext('2d').drawImage(canvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);

                cropCanvas.toBlob(blob => {
                    if (!blob || scanDone) return;
                    const file = new File([blob], 'frame.png', { type: 'image/png' });
                    html5QrCode.scanFile(file, false)
                        .then(result => handleScanResult(result))
                        .catch(() => {}); // tidak ketemu QR = abaikan
                }, 'image/jpeg', 0.85);
            }
        }
        requestAnimationFrame(decodeFrame);
    }
    requestAnimationFrame(decodeFrame);
}

function handleScanResult(decodedText) {
    if (scanDone) return;
    scanDone = true;

    // Matikan kamera
    const video = document.getElementById('camera-video');
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(t => t.stop());
    }

    // Tampilkan sukses
    document.getElementById('scanSuccess').classList.add('show');
    document.getElementById('successMsg').textContent = 'QR berhasil dibaca, mengalihkan...';

    // Haptic feedback (bila didukung)
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

    // Tunggu sebentar lalu redirect
    setTimeout(() => {
        // Kalau hasil scan adalah URL penuh, langsung redirect
        if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
            window.location.href = decodedText;
        } else {
            // Coba tempelkan ke base_url kalau cuma path
            window.location.href = BASE_URL + '/' + decodedText.replace(/^\//, '');
        }
    }, 800);
}

function showError(msg) {
    document.getElementById('cameraError').classList.add('show');
    document.getElementById('errorMsg').innerHTML = msg;
    document.getElementById('scanOverlay').style.display = 'none';
    document.getElementById('hintText').textContent = 'Tidak dapat menggunakan kamera';
}

async function toggleTorch() {
    if (!currentTrack) return;
    try {
        torchOn = !torchOn;
        await currentTrack.applyConstraints({ advanced: [{ torch: torchOn }] });
        document.getElementById('torchBtn').classList.toggle('on', torchOn);
    } catch (e) { /* torch tidak didukung */ }
}

// Buat elemen tersembunyi yang dibutuhkan html5-qrcode
const hiddenDiv = document.createElement('div');
hiddenDiv.id = '__qr_hidden__';
hiddenDiv.style.cssText = 'display:none;position:absolute;width:1px;height:1px;overflow:hidden';
document.body.appendChild(hiddenDiv);

// Mulai scanner saat halaman siap
window.addEventListener('DOMContentLoaded', () => {
    positionOverlays();
    startScanner();
});

// Bersihkan stream saat keluar halaman
window.addEventListener('pagehide', () => {
    const video = document.getElementById('camera-video');
    if (video?.srcObject) video.srcObject.getTracks().forEach(t => t.stop());
});
</script>

</body>
</html>
