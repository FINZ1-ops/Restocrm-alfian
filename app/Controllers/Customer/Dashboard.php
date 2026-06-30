<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Cek apakah user sudah login
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        // Cek apakah user adalah customer
        if (session()->get('role') !== 'customer') {
            return redirect()->to(base_url('customer/dashboard'));
        }

        $db = \Config\Database::connect();

        // Bypass BaseRestaurantModel auto-filter: akun Customer tidak terikat
        // ke satu restaurant_id tertentu (customer bisa order di banyak
        // resto berbeda), jadi pencarian berdasarkan user_id harus
        // lintas-resto, bukan terfilter ke restaurant_id sesi yang mungkin
        // masih nyangkut dari role lain (Admin/Kasir) di browser yang sama.
        $customerData = $db->table('customers')->where('user_id', session('user_id'))->get()->getRowArray();

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

        // Bypass BaseRestaurantModel auto-filter — lihat catatan di index()
        $customerData = $db->table('customers')->where('user_id', session('user_id'))->get()->getRowArray();

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
     * Halaman riwayat pesanan lengkap, dengan filter rentang tanggal opsional.
     * Query string yang didukung: ?from=YYYY-MM-DD&to=YYYY-MM-DD
     */
    public function orders()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $db = \Config\Database::connect();

        // Bypass BaseRestaurantModel auto-filter — lihat catatan di index()
        $customerData = $db->table('customers')->where('user_id', session('user_id'))->get()->getRowArray();

        $orders = [];

        // Ambil & validasi input filter tanggal dari query string.
        // Dibiarkan kosong jika tidak diisi atau formatnya tidak valid,
        // supaya tidak menyebabkan query error dan tetap menampilkan
        // semua riwayat seperti sebelumnya kalau filter tidak dipakai.
        $dateFrom = $this->request->getGet('from');
        $dateTo   = $this->request->getGet('to');

        $isValidDate = function (?string $date): bool {
            if (empty($date)) {
                return false;
            }
            $d = \DateTime::createFromFormat('Y-m-d', $date);
            return $d && $d->format('Y-m-d') === $date;
        };

        $dateFrom = $isValidDate($dateFrom) ? $dateFrom : null;
        $dateTo   = $isValidDate($dateTo) ? $dateTo : null;

        if ($customerData) {
            $query = $db->table('orders o')
                ->select('o.*, r.name as restaurant_name, r.address as restaurant_address')
                ->join('restaurants r', 'o.restaurant_id = r.id', 'left')
                ->where('o.customer_id', $customerData['id']);

            // Filter "dari tanggal" — ambil dari awal hari (00:00:00)
            if ($dateFrom) {
                $query->where('o.created_at >=', $dateFrom . ' 00:00:00');
            }
            // Filter "sampai tanggal" — ambil sampai akhir hari (23:59:59),
            // supaya pesanan yang dibuat di tanggal tersebut tetap ikut.
            if ($dateTo) {
                $query->where('o.created_at <=', $dateTo . ' 23:59:59');
            }

            $orders = $query->orderBy('o.created_at', 'DESC')->get()->getResultArray();

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
            'orders'       => $orders,
            'customerData' => $customerData,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
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

        $db = \Config\Database::connect();

        // Bypass BaseRestaurantModel auto-filter — lihat catatan di index()
        $customerData = $db->table('customers')->where('user_id', session('user_id'))->get()->getRowArray();

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