<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Tidak Valid — RESTOCRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --accent:#ffc327; --accent-hover:#e5ae1f; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 32px;
            text-align: center;
            max-width: 360px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }
        p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .btn {
            display: inline-block;
            background: var(--accent);
            color: #25343c;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            width: 100%;
        }
        .btn:hover { background: var(--accent-hover); color: #25343c; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>
        <h2>QR Code Tidak Valid</h2>
        <p>
            QR code ini tidak ditemukan atau meja sudah tidak aktif.<br>
            Minta bantuan staf restoran untuk QR code yang benar.
        </p>
        <a href="javascript:history.back()" class="btn">← Kembali</a>
    </div>
</body>
</html>
