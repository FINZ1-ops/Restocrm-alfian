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
        ['url' => '/admin/subscription-payments', 'icon' => 'bi-receipt', 'label' => 'Pembayaran Langganan'],
    ];
} elseif ($role === 'admin_resto') {
    $navItems = [
        ['url' => '/resto/dashboard',     'icon' => 'bi-grid-1x2-fill',   'label' => 'Dashboard'],
        ['url' => '/resto/profile',       'icon' => 'bi-shop',            'label' => 'Resto Profile'],
        ['url' => '/resto/categories',    'icon' => 'bi-tags',            'label' => 'Categories'],
        ['url' => '/resto/menus',         'icon' => 'bi-cup-hot',         'label' => 'Menu'],
        ['url' => '/resto/tables',        'icon' => 'bi-qr-code-scan',    'label' => 'Tables & QR'],
        ['url' => '/resto/orders',        'icon' => 'bi-receipt',         'label' => 'Orders'],
        ['url' => '/resto/customers',     'icon' => 'bi-people',          'label' => 'Customers'],
        ['url' => '/resto/staff',         'icon' => 'bi-person-badge',    'label' => 'Staff'],
        ['url' => '/resto/reports',       'icon' => 'bi-bar-chart-line',  'label' => 'Reports'],
    ];
} elseif ($role === 'kasir') {
    $navItems = [
        ['url' => '/cashier/orders',      'icon' => 'bi-credit-card',     'label' => 'Konfirmasi Bayar'],
    ];
} elseif ($role === 'dapur') {
    $navItems = [
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
        ['url' => '/akun/dashboard',   'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
        ['url' => '/akun/orders',      'icon' => 'bi-receipt',        'label' => 'Riwayat Pesanan'],
        ['url' => '/akun/rewards',     'icon' => 'bi-gift',           'label' => 'Poin & Voucher'],
        ['url' => '/akun/profile',     'icon' => 'bi-person',         'label' => 'Profil'],
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #263238;
            --sidebar-hover: rgba(255, 195, 39, 0.1);
            --sidebar-active: rgba(255, 195, 39, 0.1);
            --sidebar-active-border: #FFC327;
            --accent: #FFC327;
            --accent-hover: #e6b023;
            --body-bg: #F5F5F5;
            --text-dark: #1e293b;
        }
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--body-bg); min-height: 100vh; }

        /* ─── Sidebar ─── */
        .sidebar {
            background: var(--sidebar-bg);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 24px 20px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-brand .logo-icon {
            background: var(--accent);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #263238;
            font-size: 20px;
        }
        .sidebar-brand .logo-text-wrapper {
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand .logo-text {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: -0.5px;
            line-height: 1.1;
        }
        .sidebar-brand .logo-text span { color: #fff; }
        .sidebar-brand .logo-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-top: 2px;
            font-weight: 500;
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
            gap: 12px;
            color: rgba(255,255,255,0.6);
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
            text-decoration: none;
        }
        .sidebar-nav .nav-link i { font-size: 18px; }
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active);
            color: var(--accent);
            border-left-color: var(--sidebar-active-border);
        }
        .sidebar-nav .nav-link.active i {
            color: var(--accent);
        }
        .sidebar-footer {
            padding: 20px;
        }
        .new-order-btn {
            background: var(--accent);
            color: #263238;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }
        .new-order-btn:hover {
            background: var(--accent-hover);
            color: #263238;
        }
        .footer-link {
            display: flex; align-items: center; gap: 12px;
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 8px;
            transition: all 0.15s;
            margin-bottom: 4px;
        }
        .footer-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
        
        /* ─── Main Content ─── */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .search-bar {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 320px;
        }
        .search-bar i { color: #94a3b8; font-size: 15px;}
        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 14px;
            color: #475569;
        }
        .search-bar input::placeholder { color: #94a3b8; }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .store-name {
            font-weight: 600;
            font-size: 15px;
            color: var(--text-dark);
        }
        .topbar-icon {
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s;
        }
        .topbar-icon:hover { color: var(--text-dark); }
        .user-avatar-top {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: #e2e8f0;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #e2e8f0;
            cursor: pointer;
        }
        .user-avatar-top img {
            width: 100%; height: 100%; object-fit: cover;
        }

        /* Dropdown menu profil (avatar pojok kanan atas) */
        .user-menu-wrapper {
            position: relative;
        }
        .user-menu-dropdown {
            display: none;
            position: absolute;
            top: 48px;
            right: 0;
            min-width: 200px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            padding: 8px;
            z-index: 1000;
        }
        .user-menu-dropdown.show {
            display: block;
        }
        .user-menu-dropdown .user-menu-name {
            padding: 8px 12px 4px;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
        }
        .user-menu-dropdown .user-menu-role {
            padding: 0 12px 8px;
            font-size: 12px;
            color: #94a3b8;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }
        .user-menu-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 14px;
            transition: background 0.15s;
        }
        .user-menu-dropdown a:hover {
            background: #f1f5f9;
        }
        .user-menu-dropdown a.logout-link {
            color: #ef4444;
        }
        .user-menu-dropdown a.logout-link:hover {
            background: #fef2f2;
        }
        
        .main-content { padding: 28px; flex: 1; }

        /* ─── Global elements ─── */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            margin-bottom: 24px;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px;
            font-weight: 700;
            font-size: 16px;
            color: var(--text-dark);
            border-radius: 16px 16px 0 0 !important;
        }
        .card-body {
            padding: 24px;
        }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
            color: #263238;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            color: #263238;
        }

        /* ─── Alerts ─── */
        .alert { border-radius: 12px; font-size: 14px; }

        /* ─── Forms ─── */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #e2e8f0;
            font-size: 14px;
            padding: 10px 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 195, 39, 0.2);
        }
        .form-label { font-weight: 600; font-size: 13px; color: #334155; }

        /* ─── Mobile Sidebar Toggle ─── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .search-bar { display: none; }
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
        <div class="logo-icon"><i class="bi bi-shop"></i></div>
        <div class="logo-text-wrapper">
            <div class="logo-text">Resto<span>CRM</span></div>
            <div class="logo-sub">Enterprise Management</div>
        </div>
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
        <a href="/settings" class="footer-link">
            <i class="bi bi-gear"></i> Settings
        </a>
        <a href="/auth/logout" class="footer-link">
            <i class="bi bi-box-arrow-right"></i> Logout
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
            <div class="search-bar d-none d-md-flex">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Cari sesuatu...">
            </div>
        </div>
        <div class="topbar-right">
            <?php if ($role === 'admin_resto' || $role === 'kasir' || $role === 'dapur'): ?>
                <div class="store-name"><?= esc(session('restaurant_name') ?? 'Pecel Lele Nigasari') ?></div>
            <?php else: ?>
                <div class="store-name"><?= esc($title ?? 'Dashboard') ?></div>
            <?php endif; ?>
            <i class="bi bi-bell topbar-icon"></i>
            <i class="bi bi-question-circle topbar-icon"></i>
            <div class="user-menu-wrapper">
                <div class="user-avatar-top" onclick="toggleUserMenu(event)">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session('name') ?? 'Admin') ?>&background=random" alt="Avatar">
                </div>
                <div class="user-menu-dropdown" id="userMenuDropdown">
                    <div class="user-menu-name"><?= esc(session('name') ?? 'Admin') ?></div>
                    <div class="user-menu-role"><?= esc(ucwords(str_replace('_', ' ', $role ?? ''))) ?></div>
                    <a href="/settings"><i class="bi bi-gear"></i> Settings</a>
                    <a href="/auth/logout" class="logout-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
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

// Dropdown menu profil (avatar pojok kanan atas) — buka/tutup saat avatar
// diklik, dan otomatis tertutup kalau pengguna klik di area lain.
function toggleUserMenu(event) {
    event.stopPropagation(); // supaya klik avatar tidak langsung memicu listener "klik di luar" di bawah
    document.getElementById('userMenuDropdown').classList.toggle('show');
}

document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('userMenuDropdown');
    const wrapper  = event.target.closest('.user-menu-wrapper');
    if (dropdown && dropdown.classList.contains('show') && !wrapper) {
        dropdown.classList.remove('show');
    }
});
</script>
<?= $extraScripts ?? '' ?>
</body>
</html>