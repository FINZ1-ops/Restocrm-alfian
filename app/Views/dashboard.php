<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RESTOCRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
        }
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            padding: 20px 0;
            color: white;
            position: sticky;
            top: 0;
        }
        .sidebar .brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        .sidebar .brand h4 {
            margin: 0;
            font-weight: 700;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .user-info {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px;
            font-weight: 600;
        }
        .stat-box {
            padding: 20px;
            text-align: center;
            background: white;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">
            <div class="col-md-2 sidebar">
                <div class="brand">
                    <h4>RESTOCRM</h4>
                    <small>Prototype MVP</small>
                </div>

                <nav>
                    <?php if (session('role') === 'super_admin'): ?>
                        <a href="/dashboard" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        <a href="/admin/leads" class="nav-link"><i class="bi bi-people"></i> Leads CRM</a>
                        <a href="/admin/plans" class="nav-link"><i class="bi bi-box"></i> Paket Langganan</a>
                        <a href="/admin/restaurants" class="nav-link"><i class="bi bi-shop"></i> Restoran</a>
                    <?php elseif (session('role') === 'admin_resto'): ?>
                        <a href="/dashboard" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        <a href="/resto/profile" class="nav-link"><i class="bi bi-building"></i> Profil Resto</a>
                        <a href="/resto/categories" class="nav-link"><i class="bi bi-list"></i> Kategori Menu</a>
                        <a href="/resto/menus" class="nav-link"><i class="bi bi-cup-hot"></i> Menu</a>
                        <a href="/resto/tables" class="nav-link"><i class="bi bi-diagram-3"></i> Meja & QR</a>
                        <a href="/resto/orders" class="nav-link"><i class="bi bi-receipt"></i> Pesanan</a>
                        <a href="/resto/customers" class="nav-link"><i class="bi bi-person-check"></i> Pelanggan</a>
                        <a href="/resto/reports" class="nav-link"><i class="bi bi-bar-chart"></i> Laporan</a>
                    <?php elseif (session('role') === 'kasir'): ?>
                        <a href="/dashboard" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        <a href="/cashier/orders" class="nav-link"><i class="bi bi-credit-card"></i> Konfirmasi Pembayaran</a>
                    <?php elseif (session('role') === 'dapur'): ?>
                        <a href="/dashboard" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        <a href="/kitchen/orders" class="nav-link"><i class="bi bi-fire"></i> Pesanan</a>
                    <?php endif; ?>
                </nav>

                <div class="user-info">
                    <div style="font-size: 12px; opacity: 0.7;">Logged in as</div>
                    <strong><?= session('name') ?></strong>
                    <div style="font-size: 11px; opacity: 0.7; text-transform: capitalize;"><?= session('role') ?></div>
                    <a href="/auth/logout" class="nav-link" style="margin-top: 10px; padding: 5px 0;"><i class="bi bi-box-arrow-left"></i> Logout</a>
                </div>
            </div>

            <div class="col-md-10 p-4">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <h1 class="mb-4">Welcome, <?= session('name') ?></h1>

                <?php if (session('role') === 'super_admin'): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">0</div>
                                <div class="stat-label">Total Restoran</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">0</div>
                                <div class="stat-label">Leads Aktif</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">0</div>
                                <div class="stat-label">Revenue</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">3</div>
                                <div class="stat-label">Paket Aktif</div>
                            </div>
                        </div>
                    </div>
                <?php elseif (session('role') === 'admin_resto'): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">0</div>
                                <div class="stat-label">Order Hari Ini</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">Rp 0</div>
                                <div class="stat-label">Pendapatan Hari Ini</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">0</div>
                                <div class="stat-label">Menu Terlaris</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-value">0</div>
                                <div class="stat-label">Pelanggan</div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-info-circle"></i> Mulai Menggunakan RESTOCRM
                        </div>
                        <div class="card-body">
                            <p>Langkah-langkah untuk memulai:</p>
                            <ol>
                                <li><a href="/resto/profile">Lengkapi Profil Restoran Anda</a></li>
                                <li><a href="/resto/categories">Buat Kategori Menu</a></li>
                                <li><a href="/resto/menus">Tambahkan Item Menu</a></li>
                                <li><a href="/resto/tables">Setup Meja & Generate QR</a></li>
                                <li>Bagikan QR Code kepada Pelanggan untuk Memesan</li>
                            </ol>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
