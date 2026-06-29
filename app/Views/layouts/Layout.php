<?php
/**
 * @var mixed $extraStyles
 * @var mixed $pageActions
 * @var mixed $extraScripts
 */
/**
 * Layout Template
 * Usage: include di view dengan variable:
 *   $title   = judul halaman (string)
 *   $content = konten HTML halaman (agar modular, gunakan ob_start/ob_get_clean)
 */
$role = session('role');
$currentPath = current_url(true)->getPath();
if (!str_starts_with($currentPath, '/')) {
    $currentPath = '/' . $currentPath;
}

$navItems = [];

if ($role === 'super_admin') {
    $navItems = [
        ['url' => '/admin/dashboard',          'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
        ['url' => '/admin/leads',         'icon' => 'bi-funnel',          'label' => 'Leads CRM'],
        ['url' => '/admin/plans',         'icon' => 'bi-box-seam',        'label' => 'Paket Langganan'],
        ['url' => '/admin/restaurants',   'icon' => 'bi-shop',            'label' => 'Restoran'],
    ];
} elseif ($role === 'admin_resto') {
    $navItems = [
        ['url' => '/resto/dashboard',     'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
        ['url' => '/resto/profile',       'icon' => 'bi-building',        'label' => 'Profil Resto'],
        ['url' => '/resto/categories',    'icon' => 'bi-tags',            'label' => 'Kategori'],
        ['url' => '/resto/menus',         'icon' => 'bi-cup-hot',         'label' => 'Menu'],
        ['url' => '/resto/tables',        'icon' => 'bi-grid-3x3',        'label' => 'Meja & QR'],
        ['url' => '/resto/orders',        'icon' => 'bi-receipt',         'label' => 'Pesanan'],
        ['url' => '/resto/customers',     'icon' => 'bi-people',          'label' => 'Pelanggan'],
        ['url' => '/resto/staff',         'icon' => 'bi-person-badge',    'label' => 'Staff'],
        ['url' => '/resto/reports',       'icon' => 'bi-bar-chart-line',  'label' => 'Laporan'],
    ];
} elseif ($role === 'kasir') {
    $navItems = [
        // ['url' => '/dashboard',           'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
        ['url' => '/cashier/orders',      'icon' => 'bi-credit-card',     'label' => 'Konfirmasi Bayar'],
    ];
} elseif ($role === 'dapur') {
    $navItems = [
        // ['url' => '/dashboard',           'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
        ['url' => '/kitchen/orders',      'icon' => 'bi-fire',            'label' => 'Antrian Dapur'],
    ];
} elseif ($role === 'sales') {
    $navItems = [
        ['url' => '/sales/dashboard',           'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
        ['url' => '/sales/leads',         'icon' => 'bi-funnel',          'label' => 'Leads Saya'],
        ['url' => '/sales/pipeline',      'icon' => 'bi-kanban',          'label' => 'Pipeline'],
    ];
} elseif ($role === 'customer'){
    $navItems = [
        ['url' => '/customer/dashboard',   'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
        ['url' => '/customer/orders',      'icon' => 'bi-receipt',        'label' => 'Riwayat Pesanan'],
        ['url' => '/customer/rewards',     'icon' => 'bi-gift',           'label' => 'Poin & Voucher'],
        ['url' => '/customer/profile',     'icon' => 'bi-person',         'label' => 'Profil'],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'RESTOCRM') ?> — RESTOCRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255,255,255,0.08);
            --sidebar-active: rgba(99,102,241,0.2);
            --sidebar-active-border: #6366f1;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --body-bg: #f1f5f9;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--body-bg); min-height: 100vh; }

        /* ─── Sidebar ─── */
        .sidebar {
            background: var(--sidebar-bg);
            min-height: 100vh;
            width: 240px;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-brand .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .sidebar-brand .logo-text span { color: var(--accent); }
        .sidebar-brand .logo-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }
        .sidebar-nav .nav-item { margin-bottom: 2px; }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.6);
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s ease;
            border-left: 3px solid transparent;
            text-decoration: none;
        }
        .sidebar-nav .nav-link i { font-size: 16px; }
        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            border-left-color: var(--sidebar-active-border);
        }
        .sidebar-footer {
            padding: 16px 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #818cf8);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: #fff;
            flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: #fff; }
        .user-role {
            font-size: 10px; color: rgba(255,255,255,0.4);
            text-transform: capitalize;
        }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            color: rgba(255,255,255,0.5);
            font-size: 12.5px;
            text-decoration: none;
            padding: 6px 8px;
            border-radius: 6px;
            transition: all 0.15s;
            width: 100%;
        }
        .btn-logout:hover { background: rgba(239,68,68,0.15); color: #f87171; }

        /* ─── Main Content ─── */
        .main-wrapper {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .page-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        .main-content { padding: 28px; flex: 1; }

        /* ─── Cards & Widgets ─── */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            color: #1e293b;
            border-radius: 12px 12px 0 0 !important;
        }
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-card .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }
        .stat-card .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
        }
        .stat-card .stat-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        /* ─── Table ─── */
        .table { font-size: 13.5px; }
        .table thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        .table tbody td { vertical-align: middle; color: #334155; }
        .table tbody tr:hover { background: #f8fafc; }

        /* ─── Badges ─── */
        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        /* ─── Buttons ─── */
        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
        }
        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
        }

        /* ─── Alerts ─── */
        .alert { border-radius: 10px; font-size: 13.5px; }

        /* ─── Forms ─── */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #e2e8f0;
            font-size: 13.5px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        .form-label { font-weight: 500; font-size: 13px; color: #374151; }

        /* ─── Mobile Sidebar Toggle ─── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 99;
            }
            .sidebar-overlay.show { display: block; }
        }
    </style>
    <?= $extraStyles ?? '' ?>
</head>
<body>

<!-- Mobile Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-text">RESTO<span>CRM</span></div>
        <div class="logo-sub">Management System</div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($navItems as $item): ?>
            <?php
                $isActive = str_starts_with($currentPath, $item['url']) && $item['url'] !== '/dashboard'
                    ? true
                    : ($currentPath === $item['url']);
            ?>
            <div class="nav-item">
                <a href="<?= $item['url'] ?>" class="nav-link <?= $isActive ? 'active' : '' ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                    <?= $item['label'] ?>
                </a>
            </div>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-badge">
            <div class="user-avatar"><?= strtoupper(substr(session('name'), 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= esc(session('name')) ?></div>
                <div class="user-role"><?= str_replace('_', ' ', session('role')) ?></div>
            </div>
        </div>
        <a href="/auth/logout" class="btn-logout">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</aside>

<!-- Main Wrapper -->
<div class="main-wrapper">
    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h1 class="page-title"><?= esc($title ?? 'Dashboard') ?></h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?= $pageActions ?? '' ?>
            <?php if ($role === 'admin_resto' || $role === 'kasir' || $role === 'dapur'): ?>
            <span class="badge bg-light text-dark border" style="font-size:11px;">
                <i class="bi bi-shop me-1"></i><?= esc(session('restaurant_name') ?? 'Restoran') ?>
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Page Content -->
    <div class="main-content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
</script>
<?= $extraScripts ?? '' ?>
</body>
</html>
