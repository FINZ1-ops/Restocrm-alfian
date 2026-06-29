<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\Order as OrderModel;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\QRIS;
use App\Models\RestaurantTable;
use App\Models\Restaurant;
use App\Models\Payment;

class Order extends BaseController
{
    protected OrderModel $orderModel;
    protected OrderItem $orderItemModel;
    protected Menu $menuModel;
    protected QRIS $qrisModel;
    protected Payment $paymentModel;

    public function __construct()
    {
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItem();
        $this->menuModel      = new Menu();
        $this->qrisModel      = new QRIS();
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

        $menu = $this->menuModel->find($menuId);
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
            'success'   => true,
            'cart'      => $cart,
            'total'     => $total,
            'itemCount' => $itemCount,
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

        $tableModel = new RestaurantTable();
        $table      = $tableModel->find($tableId);
        if (!$table) {
            return redirect()->back();
        }

        $restaurantModel = new Restaurant();
        $restaurant      = $restaurantModel->find($table['restaurant_id']);

        $qris = $this->qrisModel
            ->where('restaurant_id', $table['restaurant_id'])
            ->where('is_active', 1)
            ->first();

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

        $tableModel = new RestaurantTable();
        $table      = $tableModel->find($tableId);
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

        // Handle QRIS proof upload
        $proofPath = null;
        if ($paymentMethod === 'qris') {
            $proof = $this->request->getFile('payment_proof');
            if ($proof && $proof->isValid() && !$proof->hasMoved()) {
                $proofName = $proof->getRandomName();
                $proof->move(FCPATH . 'uploads/proofs', $proofName);
                $proofPath = 'uploads/proofs/' . $proofName;
            }
        }

        // Create order
        $orderId = $this->orderModel->insert([
            'restaurant_id'      => $table['restaurant_id'],
            'table_id'           => $tableId,
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
        $order = $this->orderModel->where('order_code', $orderCode)->first();
        return view('customer/success', ['order' => $order]);
    }
}