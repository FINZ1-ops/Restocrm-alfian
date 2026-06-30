<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\Order as OrderModel;
use App\Models\OrderItem;
use App\Models\Payment;

class Order extends BaseController
{
    protected OrderModel $orderModel;
    protected OrderItem $orderItemModel;
    protected Payment $paymentModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItem();
        $this->paymentModel   = new Payment();
    }

    // Add item to cart (session-based)
    public function addItem()
    {
        $tableId  = $this->request->getPost('table_id');
        $menuId   = $this->request->getPost('menu_id');
        $quantity = (int)($this->request->getPost('quantity') ?: 1);
        $notes    = $this->request->getPost('notes') ?: '';
        $action   = $this->request->getPost('action') ?: 'add'; // add | remove | set

        $cartKey = 'cart_' . $tableId;
        $cart    = session()->get($cartKey) ?? [];

        // Bypass BaseRestaurantModel auto-filter — lihat catatan di
        // Customer/Menu.php untuk alasan lengkapnya.
        $db   = \Config\Database::connect();
        $menu = $db->table('menus')->where('id', $menuId)->get()->getRowArray();
        if (!$menu) {
            return $this->response->setJSON(['success' => false, 'message' => 'Menu tidak ditemukan']);
        }

        if ($action === 'remove') {
            unset($cart[$menuId]);
        } elseif ($action === 'set') {
            if ($quantity <= 0) {
                unset($cart[$menuId]);
            } else {
                $cart[$menuId] = [
                    'menu_id'   => $menuId,
                    'name'      => $menu['name'],
                    'price'     => $menu['price'],
                    'quantity'  => $quantity,
                    'notes'     => $notes,
                    'subtotal'  => $menu['price'] * $quantity,
                ];
            }
        } else {
            if (isset($cart[$menuId])) {
                $cart[$menuId]['quantity'] += $quantity;
                $cart[$menuId]['subtotal']  = $menu['price'] * $cart[$menuId]['quantity'];
            } else {
                $cart[$menuId] = [
                    'menu_id'  => $menuId,
                    'name'     => $menu['name'],
                    'price'    => $menu['price'],
                    'quantity' => $quantity,
                    'notes'    => $notes,
                    'subtotal' => $menu['price'] * $quantity,
                ];
            }
        }

        session()->set($cartKey, $cart);

        $total    = array_sum(array_column($cart, 'subtotal'));
        $itemCount = array_sum(array_column($cart, 'quantity'));

        return $this->response->setJSON([
            'success'    => true,
            'cart'       => $cart,
            'total'      => $total,
            'itemCount'  => $itemCount,
            // Kirim token CSRF terbaru — token lama langsung tidak valid
            // setiap request (regenerate = true di Config\Security),
            // jadi JS harus update token-nya sebelum request berikutnya.
            'csrfName'   => csrf_token(),
            'csrfHash'   => csrf_hash(),
        ]);
    }

    // Show checkout page
    public function checkout($tableId = null)
    {
        $cartKey = 'cart_' . $tableId;
        $cart    = session()->get($cartKey) ?? [];

        if (empty($cart)) {
            return redirect()->to('/menu/' . $tableId)->with('error', 'Keranjang kosong');
        }

        // PENTING: semua query di bawah pakai $db->table() langsung, BUKAN
        // lewat Model (RestaurantTable, Restaurant, QRIS) yang extends
        // BaseRestaurantModel. Model itu otomatis menambahkan
        // where('restaurant_id', session('restaurant_id')) di setiap
        // find()/where(), padahal Customer tidak login — kalau browser
        // ini kebetulan masih ada sisa session Admin/Kasir resto LAIN,
        // hasil query bisa salah atau kosong. Sama akar masalahnya
        // dengan bug session di Customer/Scan.php & Customer/Menu.php.
        $db = \Config\Database::connect();

        $table = $db->table('restaurant_tables')->where('id', $tableId)->get()->getRowArray();
        if (!$table) {
            return redirect()->back();
        }

        $restaurant = $db->table('restaurants')->where('id', $table['restaurant_id'])->get()->getRowArray();

        $qris = $db->table('qris_settings')
            ->where('restaurant_id', $table['restaurant_id'])
            ->where('is_active', 1)
            ->get()->getRowArray();

        $total = array_sum(array_column($cart, 'subtotal'));

        return view('customer/checkout', [
            'cart'       => $cart,
            'total'      => $total,
            'table'      => $table,
            'restaurant' => $restaurant,
            'qris'       => $qris,
        ]);
    }

    // Process order submission
    public function process()
    {
        $tableId  = $this->request->getPost('table_id');
        $cartKey  = 'cart_' . $tableId;
        $cart     = session()->get($cartKey) ?? [];

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang kosong');
        }

        $tableModel = \Config\Database::connect()->table('restaurant_tables');
        $table      = $tableModel->where('id', $tableId)->get()->getRowArray();
        if (!$table) {
            return redirect()->back()->with('error', 'Meja tidak valid');
        }

        $rules = [
            'customer_name'     => 'required|min_length[2]|max_length[100]',
            'customer_whatsapp' => 'required|min_length[8]|max_length[20]',
            'payment_method'    => 'required|in_list[cash,qris]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $total         = array_sum(array_column($cart, 'subtotal'));
        $paymentMethod = $this->request->getPost('payment_method');
        $orderCode     = 'ORD-' . strtoupper(substr(uniqid(), -6));

        // Jika customer sedang login (fitur akun customer di luar alur scan
        // QR biasa), hubungkan order ini ke record customers miliknya,
        // supaya nanti muncul di halaman "Riwayat Pesanan" (Dashboard::orders()).
        // Tanpa ini, order yang dibuat customer yang login tidak akan pernah
        // ketemu lewat pencarian berdasarkan user_id, karena upsertCustomer()
        // di sisi Kasir (dipanggil saat konfirmasi pembayaran) hanya mencari
        // berdasarkan whatsapp+restaurant_id, tidak tahu soal akun login.
        $db = \Config\Database::connect();
        $customerId = null;
        if (session('is_logged_in') && session('role') === 'customer') {
            $userId   = session('user_id');
            $waNumber = $this->request->getPost('customer_whatsapp');

            $existingCustomer = $db->table('customers')
                ->where('restaurant_id', $table['restaurant_id'])
                ->where('whatsapp', $waNumber)
                ->get()->getRowArray();

            if ($existingCustomer) {
                // Sudah pernah order di resto ini sebelumnya (mungkin saat
                // belum login) — pastikan user_id ikut ter-link sekarang.
                if (empty($existingCustomer['user_id'])) {
                    $db->table('customers')->where('id', $existingCustomer['id'])->update(['user_id' => $userId]);
                }
                $customerId = $existingCustomer['id'];
            } else {
                $db->table('customers')->insert([
                    'user_id'       => $userId,
                    'restaurant_id' => $table['restaurant_id'],
                    'name'          => $this->request->getPost('customer_name'),
                    'whatsapp'      => $waNumber,
                    'total_orders'  => 0, // diupdate oleh upsertCustomer() saat pembayaran dikonfirmasi
                    'total_spent'   => 0,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
                $customerId = $db->insertID();
            }
        }

        // Handle QRIS proof upload — validasi tipe & ukuran lewat helper
        // terpusat, supaya file selain jpg/jpeg/png/webp atau yang
        // melebihi batas ukuran langsung ditolak sebelum disimpan.
        helper('upload');
        $proofPath = null;
        if ($paymentMethod === 'qris') {
            $proof = $this->request->getFile('payment_proof');
            try {
                $proofPath = move_validated_upload($proof, 'proofs');
            } catch (\RuntimeException $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

        // Create order
        $orderId = $this->orderModel->insert([
            'restaurant_id'      => $table['restaurant_id'],
            'table_id'           => $tableId,
            'customer_id'        => $customerId, // null jika customer tidak login, sesuai brief (login opsional)
            'order_code'         => $orderCode,
            'customer_name'      => $this->request->getPost('customer_name'),
            'customer_whatsapp'  => $this->request->getPost('customer_whatsapp'),
            'table_number'       => $table['table_number'],
            'area_name'          => $table['area_name'],
            'notes'              => $this->request->getPost('notes'),
            'total'              => $total,
            'payment_method'     => $paymentMethod,
            'payment_status'     => $paymentMethod === 'qris' ? 'Menunggu Konfirmasi' : 'Belum Dibayar',
            'order_status'       => 'Menunggu Konfirmasi',
        ]);

        // Create order items massal (Batch Insert)
        if (!empty($cart)) {
            $orderItemsData = [];
            foreach ($cart as $item) {
                $orderItemsData[] = [
                    'order_id' => $orderId,
                    'menu_id'  => $item['menu_id'],
                    'price'    => $item['price'],
                    'quantity' => $item['quantity'],
                    'notes'    => $item['notes'] ?? '',
                ];
            }
            $this->orderItemModel->insertBatch($orderItemsData);
        }

        // Catat record payment untuk Cash maupun QRIS, supaya tabel payments
        // selalu punya riwayat lengkap (dipakai kasir untuk verifikasi &
        // dipakai untuk audit siapa/kapan pembayaran dikonfirmasi).
        // restaurant_id diisi manual karena Customer belum login
        // (session restaurant_id kosong, tidak bisa auto-inject).
        $this->paymentModel->createForOrder([
            'restaurant_id'   => $table['restaurant_id'],
            'order_id'        => $orderId,
            'amount'          => $total,
            'payment_method'  => $paymentMethod,
            'proof_image'     => $proofPath, // null untuk Cash, sudah benar
        ]);

        // Clear cart
        session()->remove($cartKey);

        return redirect()->to('/order/success/' . $orderCode)->with('order_code', $orderCode);
    }

    public function success($orderCode = null)
    {
        // Sama seperti checkout(): bypass BaseRestaurantModel auto-filter
        // supaya tidak ikut terfilter session restaurant_id resto lain
        // yang mungkin masih login di browser yang sama.
        $db = \Config\Database::connect();
        $order = $db->table('orders')->where('order_code', $orderCode)->get()->getRowArray();
        return view('customer/success', ['order' => $order]);
    }
}