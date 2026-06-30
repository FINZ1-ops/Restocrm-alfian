<?php
/** @var int $todayOrders */
/** @var float $todayRevenue */
/** @var int $monthOrders */
/** @var float $monthRevenue */
/** @var float $cashToday */
/** @var float $qrisToday */
/** @var array $topMenus */
/** @var array $recentOrders */
/** @var int $totalCustomers */
ob_start();
?>
<!-- Welcome Banner -->
<div class="card border-0 mb-4" style="background-color: #FFFDE7; border-radius: 16px;">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-2 text-dark" style="font-size:26px;">Selamat pagi, <?= esc(session('name') ?? 'Admin') ?>!</h2>
            <p class="text-secondary mb-0" style="font-size: 15px;">Berikut adalah ringkasan performa restoran Anda hari ini.</p>
        </div>
        <div class="d-none d-md-block">
            <!-- Icon Cup Illustration -->
            <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M75 25H25V65C25 76.0457 33.9543 85 45 85H55C66.0457 85 75 76.0457 75 65V25Z" stroke="#FFC327" stroke-width="6"/>
                <path d="M75 35H85C90.5228 35 95 39.4772 95 45C95 50.5228 90.5228 55 85 55H75" stroke="#FFC327" stroke-width="6"/>
                <rect x="20" y="90" width="60" height="6" fill="#FFC327"/>
            </svg>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 rounded-4 h-100 stat-card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-secondary small fw-semibold" style="letter-spacing: 0.5px; font-size: 11px;">PESANAN HARI INI</div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255, 195, 39, 0.15);">
                        <i class="bi bi-receipt text-warning" style="color: #FFC327 !important;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bold text-dark"><?= (int) $todayOrders ?></div>
                    <span class="text-success small fw-medium"><i class="bi bi-arrow-up-right"></i> 12%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 rounded-4 h-100 stat-card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-secondary small fw-semibold" style="letter-spacing: 0.5px; font-size: 11px;">PENDAPATAN</div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255, 195, 39, 0.15);">
                        <i class="bi bi-cash text-warning" style="color: #FFC327 !important;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 flex-wrap">
                    <div class="fs-3 fw-bold text-dark">Rp <?= number_format((float) $todayRevenue, 0, ',', '.') ?></div>
                    <span class="text-success small fw-medium"><i class="bi bi-arrow-up-right"></i> 5%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 rounded-4 h-100 stat-card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-secondary small fw-semibold" style="letter-spacing: 0.5px; font-size: 11px;">PESANAN BULAN INI</div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255, 195, 39, 0.15);">
                        <i class="bi bi-calendar3 text-warning" style="color: #FFC327 !important;"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark"><?= number_format((int) $monthOrders, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 rounded-4 h-100 stat-card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-secondary small fw-semibold" style="letter-spacing: 0.5px; font-size: 11px;">TOTAL PELANGGAN</div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255, 195, 39, 0.15);">
                        <i class="bi bi-people text-warning" style="color: #FFC327 !important;"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <div class="fs-2 fw-bold text-dark"><?= number_format((int) $totalCustomers, 0, ',', '.') ?></div>
                    <span class="text-success small fw-medium"><i class="bi bi-arrow-up-right"></i> 2%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pesanan Terbaru -->
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 h-100" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Pesanan Terbaru</h5>
                <a href="/resto/orders" class="text-decoration-none fw-semibold" style="color: #b45309; font-size: 13px;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-borderless mb-0 align-middle">
                        <thead>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <th class="text-secondary fw-semibold py-3" style="font-size: 11px; letter-spacing: 0.5px;">ID PESANAN</th>
                                <th class="text-secondary fw-semibold py-3" style="font-size: 11px; letter-spacing: 0.5px;">PELANGGAN</th>
                                <th class="text-secondary fw-semibold py-3" style="font-size: 11px; letter-spacing: 0.5px;">TOTAL</th>
                                <th class="text-secondary fw-semibold py-3" style="font-size: 11px; letter-spacing: 0.5px;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): 
                                    $statusClass = '';
                                    $statusLabel = '';
                                    switch(strtolower($order['order_status'])) {
                                        case 'selesai':
                                        case 'completed':
                                            $statusClass = 'badge-status-selesai';
                                            $statusLabel = 'Selesai';
                                            break;
                                        case 'menunggu':
                                        case 'pending':
                                            $statusClass = 'badge-status-menunggu';
                                            $statusLabel = 'Menunggu';
                                            break;
                                        case 'dibatalkan':
                                        case 'cancelled':
                                            $statusClass = 'badge-status-dibatalkan';
                                            $statusLabel = 'Dibatalkan';
                                            break;
                                        default:
                                            $statusClass = 'bg-light text-dark';
                                            $statusLabel = esc($order['order_status']);
                                    }
                                ?>
                                    <tr style="border-bottom: 1px solid #f8fafc;">
                                        <td class="py-3 text-secondary" style="font-size: 14px;">#<?= esc($order['order_code']) ?></td>
                                        <td class="py-3 fw-medium text-dark" style="font-size: 14px;"><?= esc($order['customer_name']) ?></td>
                                        <td class="py-3 text-dark" style="font-size: 14px;">Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></td>
                                        <td class="py-3"><span class="badge rounded-pill px-3 py-2 <?= $statusClass ?>" style="font-weight: 500; font-size: 12px;"><?= $statusLabel ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Menu Terlaris -->
    <div class="col-lg-4">
        <div class="card border-0 rounded-4 h-100" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h5 class="fw-bold mb-0 text-dark">Menu Terlaris</h5>
            </div>
            <div class="card-body p-4 pt-2">
                <?php if (empty($topMenus)): ?>
                    <p class="text-muted mb-0">Belum ada data penjualan.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php 
                        $rank = 1;
                        foreach ($topMenus as $menu): ?>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-3 overflow-hidden d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <!-- Placeholder image if no image available -->
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($menu['name']) ?>&background=e2e8f0&color=64748b&size=48" alt="menu" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 14px;"><?= esc($menu['name']) ?></div>
                                        <div class="text-secondary" style="font-size: 12px;"><?= (int) ($menu['total_qty'] ?? 0) ?> Pesanan</div>
                                    </div>
                                </div>
                                <div class="fw-bold fs-5" style="color: #FFC327;">
                                    #<?= $rank++ ?>
                                </div>
                            </div>
                        <?php 
                        // Show max 4
                        if ($rank > 4) break;
                        endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= ob_get_clean() ?>
