<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    public function index()
    {
        return redirect()->to('/resto/reports/daily');
    }

    public function daily()
    {
        $restaurantId = session('restaurant_id');
        $date = $this->request->getGet('date') ?: date('Y-m-d');
        $db   = \Config\Database::connect();

        $orders = $db->table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('DATE(created_at)', $date)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $summary = $db->table('orders')
            ->select('
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = "Lunas" THEN total ELSE 0 END) as total_revenue,
                SUM(CASE WHEN payment_method = "cash" AND payment_status = "Lunas" THEN total ELSE 0 END) as cash_revenue,
                SUM(CASE WHEN payment_method = "qris" AND payment_status = "Lunas" THEN total ELSE 0 END) as qris_revenue,
                SUM(CASE WHEN order_status = "Dibatalkan" THEN 1 ELSE 0 END) as cancelled_orders
            ')
            ->where('restaurant_id', $restaurantId)
            ->where('DATE(created_at)', $date)
            ->get()->getRow();

        $topItems = $db->table('order_items')
            ->select('menus.name, SUM(order_items.quantity) as qty, SUM(order_items.price * order_items.quantity) as revenue', false)
            ->join('menus', 'menus.id = order_items.menu_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.restaurant_id', $restaurantId)
            ->where('DATE(orders.created_at)', $date)
            ->where('orders.order_status !=', 'Dibatalkan')
            ->groupBy('order_items.menu_id')
            ->orderBy('qty', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $content = view('resto/reports/daily', [
            'orders'   => $orders,
            'summary'  => $summary,
            'topItems' => $topItems,
            'date'     => $date,
        ]);
        return view('layouts/Layout', ['title' => 'Laporan Harian', 'content' => $content]);
    }

    public function monthly()
    {
        $restaurantId = session('restaurant_id');
        $month = $this->request->getGet('month') ?: date('Y-m');
        [$year, $mon] = explode('-', $month);
        $db = \Config\Database::connect();

        $summary = $db->table('orders')
            ->select('
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = "Lunas" THEN total ELSE 0 END) as total_revenue,
                SUM(CASE WHEN payment_method = "cash" AND payment_status = "Lunas" THEN total ELSE 0 END) as cash_revenue,
                SUM(CASE WHEN payment_method = "qris" AND payment_status = "Lunas" THEN total ELSE 0 END) as qris_revenue
            ')
            ->where('restaurant_id', $restaurantId)
            ->where('MONTH(created_at)', $mon)
            ->where('YEAR(created_at)', $year)
            ->get()->getRow();

        // Daily breakdown
        $daily = $db->table('orders')
            ->select('DATE(created_at) as order_date, COUNT(*) as total_orders, SUM(CASE WHEN payment_status = "Lunas" THEN total ELSE 0 END) as revenue')
            ->where('restaurant_id', $restaurantId)
            ->where('MONTH(created_at)', $mon)
            ->where('YEAR(created_at)', $year)
            ->groupBy('DATE(created_at)')
            ->orderBy('order_date', 'ASC')
            ->get()->getResultArray();

        $topMenus = $db->table('order_items')
            ->select('menus.name, SUM(order_items.quantity) as qty, SUM(order_items.price * order_items.quantity) as revenue', false)
            ->join('menus', 'menus.id = order_items.menu_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.restaurant_id', $restaurantId)
            ->where('MONTH(orders.created_at)', $mon)
            ->where('YEAR(orders.created_at)', $year)
            ->where('orders.order_status !=', 'Dibatalkan')
            ->groupBy('order_items.menu_id')
            ->orderBy('qty', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $content = view('resto/reports/monthly', [
            'summary'  => $summary,
            'daily'    => $daily,
            'topMenus' => $topMenus,
            'month'    => $month,
        ]);
        return view('layouts/Layout', ['title' => 'Laporan Bulanan', 'content' => $content]);
    }
}
