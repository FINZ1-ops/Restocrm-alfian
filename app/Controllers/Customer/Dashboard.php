<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
// use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\QRIS;

class Dashboard extends BaseController
{
    protected Order $orderModel;
    protected Menu $menuModel;
    protected Customer $customerModel;
    protected MenuCategory $categoryModel;
    protected QRIS $scan;

    public function __construct() {
        $this->orderModel = new Order();
        $this->menuModel = new Menu();
        $this->customerModel = new Customer();
        $this->categoryModel = new MenuCategory();
        $this->scan = new QRIS();
    }

    public function index()
    {
        // Cek apakah user sudah login
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        // Cek apakah user adalah customer
        if (session()->get('role') !== 'customer') {
            return redirect()->to(base_url('cutomer/dashboard'));
        }

        $userId = session()->get('user_id');
        $userEmail = session()->get('email');
        $userName = session()->get('name');

        // Inisialisasi model
        $this->orderModel = new Order();
        $this->customerModel = new Customer();

        // Database connection
        $db = \Config\Database::connect();

        // Ambil data customer berdasarkan email atau nama
        $customerData = $this->customerModel
            ->where('user_id', session('user_id'))
            ->first();

        // Hitung total pesanan customer
        $totalOrders = 0;
        $recentOrders = [];

        if ($customerData) {
            // Ambil semua order dari customer ini
            $allOrders = $db->table('orders o')
                ->select('o.*, r.name as restaurant_name')
                ->join('restaurants r', 'o.restaurant_id = r.id', 'left')
                ->where('o.customer_id', $customerData['id'])
                ->orderBy('o.created_at', 'DESC')
                ->get()
                ->getResultArray();

            $totalOrders = count($allOrders);

            // Ambil 5 pesanan terakhir
            $recentOrders = array_slice($allOrders, 0, 5);

            // Format data untuk view
            foreach ($recentOrders as &$order) {
                $order['invoice_number'] = $order['order_code'];
                $order['total_amount'] = $order['total'];
                $order['status'] = $order['order_status'];
            }
        }

        // Hitung loyalty points (1 point per 10.000 rupiah)
        $points = 0;
        if ($customerData && isset($customerData['total_spent'])) {
            $points = floor($customerData['total_spent'] / 10000);
        }

        // Hitung voucher aktif
        $voucherCount = 0;
        if ($points >= 10) {
            $voucherCount += 1;
        }
        if ($points >= 50) {
            $voucherCount += 1;
        }

        // Hitung restoran favorit (restoran yang paling sering dikunjungi)
        $favoriteRestaurants = 0;
        if ($customerData) {
            $favoriteRestaurants = $db->table('orders')
                ->select('restaurant_id')
                ->where('customer_id', $customerData['id'])
                ->groupBy('restaurant_id')
                ->countAllResults();
        }

        // Data untuk view
        $data = [
            'totalOrders' => $totalOrders,
            'points' => $points,
            'voucherCount' => $voucherCount,
            'favoriteRestaurants' => $favoriteRestaurants,
            'recentOrders' => $recentOrders,
            'customerData' => $customerData
        ];

        return view('customer/dashboardUser', $data);
    }

    /**
     * Halaman profil customer
     */
    public function profile()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userId = session()->get('user_id');
        $userName = session()->get('name');
        $userEmail = session()->get('email');

        $db = \Config\Database::connect();

        // Ambil data customer
        $customerData = $this->customerModel
            ->where('user_id', session('user_id'))
            ->first();

        $data = [
            'user' => [
                'id' => $userId,
                'name' => $userName,
                'email' => $userEmail,
            ],
            'customerData' => $customerData
        ];

        return view('customer/profile', $data);
    }

    /**
     * Halaman riwayat pesanan lengkap
     */
    public function orders()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userName = session()->get('name');
        $userEmail = session()->get('email');

        $db = \Config\Database::connect();

        // Ambil data customer
        $customerData = $this->customerModel
            ->where('user_id', session('user_id'))
            ->first();

        $orders = [];

        if ($customerData) {
            // Ambil semua order customer dengan detail
            $orders = $db->table('orders o')
                ->select('o.*, r.name as restaurant_name, r.address as restaurant_address')
                ->join('restaurants r', 'o.restaurant_id = r.id', 'left')
                ->where('o.customer_id', $customerData['id'])
                ->orderBy('o.created_at', 'DESC')
                ->get()
                ->getResultArray();

            // Ambil items untuk setiap order
            if (!empty($orders)) {
                $orderIds = array_column($orders, 'id');
                $allItems = $db->table('order_items oi')
                    ->select('oi.*, m.name as menu_name, m.price as menu_price')
                    ->join('menus m', 'oi.menu_id = m.id', 'left')
                    ->whereIn('oi.order_id', $orderIds)
                    ->get()
                    ->getResultArray();
                    
                $itemsByOrder = [];
                foreach ($allItems as $item) {
                    $itemsByOrder[$item['order_id']][] = $item;
                }
                
                foreach ($orders as &$order) {
                    $order['items'] = $itemsByOrder[$order['id']] ?? [];
                }
            }
        }

        $data = [
            'orders' => $orders,
            'customerData' => $customerData
        ];

        return view('customer/orders', $data);
    }

    /**
     * Halaman poin & voucher
     */
    public function rewards()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userName = session()->get('name');
        $userEmail = session()->get('email');

        $db = \Config\Database::connect();

        // Ambil data customer
        $customerData = $this->customerModel
            ->where('user_id', session('user_id'))
            ->first();

        // Hitung loyalty points
        $points = 0;
        $totalSpent = 0;

        if ($customerData) {
            $totalSpent = $customerData['total_spent'] ?? 0;
            $points = floor($totalSpent / 10000); // 1 point per 10.000 rupiah
        }

        // Voucher berdasarkan points
        $vouchers = [];

        if ($points >= 10) {
            $vouchers[] = [
                'code' => 'LOYAL10',
                'description' => 'Diskon 10% untuk pembelian minimal Rp 50.000',
                'discount' => '10%',
                'min_purchase' => 50000,
                'valid_until' => date('Y-m-d', strtotime('+30 days'))
            ];
        }

        if ($points >= 50) {
            $vouchers[] = [
                'code' => 'LOYAL50',
                'description' => 'Diskon 20% untuk pembelian minimal Rp 100.000',
                'discount' => '20%',
                'min_purchase' => 100000,
                'valid_until' => date('Y-m-d', strtotime('+30 days'))
            ];
        }

        $data = [
            'points' => $points,
            'totalSpent' => $totalSpent,
            'vouchers' => $vouchers,
            'customerData' => $customerData
        ];

        return view('customer/rewards', $data);
    }

    /**
     * Halaman scanner QR — buka kamera HP untuk scan QR meja
     */
    public function scanPage()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        return view('customer/scan');
    }

}
