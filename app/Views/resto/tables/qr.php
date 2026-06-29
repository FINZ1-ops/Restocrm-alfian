    <?php
/**
 * @var mixed $table
 */
/**
     * View: resto/tables/qr.php
     * Tampilkan & cetak QR code untuk satu meja
     * QR berisi URL: /scan/{qr_token}
     * Variabel: $table
     */
    ob_start(); ?>

    <div class="mb-4">
        <a href="/resto/tables" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Meja
        </a>
        <h2 class="fw-bold mb-1 mt-2">
            QR Code — Meja <?= esc($table['table_number']) ?>
        </h2>
        <p class="text-secondary mb-0">Area: <?= esc($table['area_name']) ?></p>
    </div>

    <div class="row g-4">
        <!-- Panel QR -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body p-4">
                    <!-- QR ditampilkan di sini oleh library -->
                    <div id="qrcode" class="d-flex justify-content-center mb-4"></div>

                    <!-- Info meja -->
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <p class="fw-bold fs-5 mb-1">Meja <?= esc($table['table_number']) ?></p>
                        <p class="text-muted small mb-0">Area: <?= esc($table['area_name']) ?></p>
                        <p class="text-muted small mb-0">Kapasitas: <?= (int)$table['capacity'] ?> orang</p>
                    </div>

                    <!-- URL QR -->
                    <p class="small text-muted text-break mb-4">
                        <code><?= base_url('scan/' . $table['qr_token']) ?></code>
                    </p>

                    <button onclick="printQR()" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-printer me-2"></i>Cetak QR Code
                    </button>
                    <a href="/resto/tables" class="btn btn-outline-secondary w-100">Kembali</a>
                </div>
            </div>
        </div>

        <!-- Petunjuk penggunaan -->
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header">Cara Penggunaan</div>
                <div class="card-body p-4">
                    <ol class="mb-0">
                        <li class="mb-2">Cetak QR Code dan laminasi agar tahan lama</li>
                        <li class="mb-2">Tempelkan di atas meja atau di dudukan akrilik</li>
                        <li class="mb-2">Pelanggan scan QR menggunakan kamera HP</li>
                        <li class="mb-2">Sistem otomatis mengenali meja & area pelanggan</li>
                        <li class="mb-2">Pelanggan pilih menu dan checkout</li>
                        <li>Order masuk ke dashboard kasir & dapur secara langsung</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Library QRCode.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
    // URL yang akan diencode ke dalam QR
    const qrUrl = "<?= base_url('scan/' . $table['qr_token']) ?>";

    // Generate QR code ke dalam div#qrcode
    new QRCode(document.getElementById('qrcode'), {
        text        : qrUrl,
        width       : 220,
        height      : 220,
        colorDark   : '#0f172a',
        colorLight  : '#ffffff',
        correctLevel: QRCode.CorrectLevel.H, // Level H = tahan 30% kerusakan
    });

    /**
     * Buka jendela print dengan QR + info meja
     * Mengambil canvas dari QRCode.js dan convert ke data URL
     */
    function printQR() {
        const canvas    = document.getElementById('qrcode').querySelector('canvas');
        const qrDataUrl = canvas ? canvas.toDataURL('image/png') : '';
        const printWin  = window.open('', '_blank');

        printWin.document.write(`<!DOCTYPE html><html><head>
            <title>QR Meja <?= esc($table['table_number']) ?></title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 48px; }
                h2   { font-size: 30px; margin-bottom: 6px; }
                p    { color: #64748b; margin: 4px 0; font-size: 15px; }
                img  { margin: 24px auto; display: block; }
                .hint{ font-size: 12px; color: #94a3b8; margin-top: 16px; }
            </style></head><body>
            <h2>Meja <?= esc($table['table_number']) ?></h2>
            <p>Area: <?= esc($table['area_name']) ?></p>
            <img src="${qrDataUrl}" width="260" height="260" alt="QR Code">
            <p class="hint">Scan untuk melihat menu &amp; pesan</p>
        </body></html>`);

        printWin.document.close();
        printWin.focus();
        setTimeout(() => { printWin.print(); printWin.close(); }, 500);
    }
    </script>
