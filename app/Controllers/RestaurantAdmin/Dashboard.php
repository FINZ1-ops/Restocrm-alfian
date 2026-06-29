<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Customer;

/**
 * Dashboard pemilik/admin restoran — ringkasan penjualan & pesanan terbaru.
 */
class Dashboard extends BaseController
{
    protected Order $orderModel;
    protected Menu $menuModel;
    protected Customer $customerModel;

    public function __construct()
    {
        $this->orderModel    = new Order();
        $this->menuModel     = new Menu();
        $this->customerModel = new Customer();
    }

    /**
     * Mengumpulkan statistik harian/bulanan lalu render view resto/dashboard/index.
     */
    public function index()
    {
        $restaurantId = session('restaurant_id');
        $db = \Config\Database::connect();

        // ── Statistik hari ini ─────────────────────────────────────────────
        $todayOrders = $db->table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('order_status !=', 'Dibatalkan')
            ->countAllResults();

        $todayRevenue = $db->table('orders')
            ->selectSum('total')
            ->where('restaurant_id', $restaurantId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('payment_status', 'Lunas')
            ->get()->getRow();

        // ── Statistik bulan berjalan ───────────────────────────────────────
        $monthOrders = $db->table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->where('order_status !=', 'Dibatalkan')
            ->countAllResults();

        $monthRevenue = $db->table('orders')
            ->selectSum('total')
            ->where('restaurant_id', $restaurantId)
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->where('payment_status', 'Lunas')
            ->get()->getRow();

        // ── Pembagian pembayaran hari ini (tunai vs QRIS) ──────────────────
        $cashToday = $db->table('orders')
            ->selectSum('total')
            ->where('restaurant_id', $restaurantId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('payment_method', 'cash')
            ->where('payment_status', 'Lunas')
            ->get()->getRow();

        $qrisToday = $db->table('orders')
            ->selectSum('total')
            ->where('restaurant_id', $restaurantId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('payment_method', 'qris')
            ->where('payment_status', 'Lunas')
            ->get()->getRow();

        // Menu terlaris: total qty & omzet = price × quantity (bukan kolom subtotal)
        $topMenus = $db->table('order_items')
            ->select('menus.name, SUM(order_items.quantity) as total_qty, SUM(order_items.price * order_items.quantity) as total_revenue', false)
            ->join('menus', 'menus.id = order_items.menu_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.restaurant_id', $restaurantId)
            ->where('MONTH(orders.created_at)', date('m'))
            ->where('YEAR(orders.created_at)', date('Y'))
            ->groupBy('order_items.menu_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // 8 pesanan terakhir untuk tabel ringkas di dashboard
        $recentOrders = $db->table('orders')
            ->where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'DESC')
            ->limit(8)
            ->get()->getResultArray();

        $totalCustomers = $this->customerModel->where('restaurant_id', $restaurantId)->countAllResults();

        $content = view('resto/dashboard/index', [
            'todayOrders'   => $todayOrders,
            'todayRevenue'  => $todayRevenue->total ?? 0,
            'monthOrders'   => $monthOrders,
            'monthRevenue'  => $monthRevenue->total ?? 0,
            'cashToday'     => $cashToday->total ?? 0,
            'qrisToday'     => $qrisToday->total ?? 0,
            'topMenus'      => $topMenus,
            'recentOrders'  => $recentOrders,
            'totalCustomers'=> $totalCustomers,
        ]);
        return view('layouts/Layout', ['title' => 'Dashboard Restoran', 'content' => $content]);
    }
}
