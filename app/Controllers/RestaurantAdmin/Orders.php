<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\Order;
use App\Models\OrderItem;

class Orders extends BaseController
{
    protected Order $orderModel;
    protected OrderItem $orderItemModel;

    public function __construct()
    {
        $this->orderModel     = new Order();
        $this->orderItemModel = new OrderItem();
    }

    public function index()
    {
        $restaurantId = session('restaurant_id');
        $status       = $this->request->getGet('status');
        $date         = $this->request->getGet('date') ?: date('Y-m-d');

        $query = $this->orderModel
            ->where('restaurant_id', $restaurantId)
            ->where('DATE(created_at)', $date)
            ->orderBy('created_at', 'DESC');

        if ($status) $query->where('order_status', $status);

        $orders = $query->paginate(20);

        $content = view('resto/orders/index', [
            'orders'        => $orders,
            'pager'         => $this->orderModel->pager,
            'currentStatus' => $status,
            'currentDate'   => $date,
        ]);
        return view('layouts/Layout', ['title' => 'Manajemen Pesanan', 'content' => $content]);
    }

    public function view($id = null)
    {
        $restaurantId = session('restaurant_id');
        $order = $this->orderModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$order) {
            return redirect()->to('/resto/orders')->with('error', 'Pesanan tidak ditemukan');
        }

        $items = $this->orderItemModel
            ->select('order_items.*, menus.name as menu_name, menus.image as menu_image')
            ->join('menus', 'menus.id = order_items.menu_id', 'left')
            ->where('order_items.order_id', $id)
            ->findAll();

        $content = view('resto/orders/view', ['order' => $order, 'items' => $items]);
        return view('layouts/Layout', ['title' => 'Detail Pesanan #' . $order['order_code'], 'content' => $content]);
    }
}
