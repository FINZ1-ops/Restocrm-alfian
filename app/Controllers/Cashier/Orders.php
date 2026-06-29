<?php

namespace App\Controllers\Cashier;

use App\Controllers\BaseController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Payment;

/**
 * Modul kasir — konfirmasi pembayaran tunai/QRIS sebelum pesanan masuk dapur.
 */
class Orders extends BaseController
{
    protected Order $orderModel;
    protected OrderItem $orderItemModel;
    protected Customer $customerModel;
    protected Payment $paymentModel;

    public function __construct()
    {
        $this->orderModel     = new Order();
        $this->orderItemModel = new OrderItem();
        $this->customerModel  = new Customer();
        $this->paymentModel   = new Payment();
    }

    /**
     * Daftar pesanan yang masih perlu dibayar atau diverifikasi (QRIS).
     */
    public function index()
    {
        $restaurantId = session('restaurant_id');
        $db = \Config\Database::connect();

        $orders = $db->table('orders')
            ->where('restaurant_id', $restaurantId)
            ->whereIn('payment_status', ['Belum Dibayar', 'Menunggu Konfirmasi'])
            ->orderBy('created_at', 'ASC')
            ->get()->getResultArray();

        $content = view('cashier/orders/index', ['orders' => $orders]);
        return view('layouts/Layout', ['title' => 'Konfirmasi Pembayaran', 'content' => $content]);
    }

    /**
     * Detail satu pesanan beserta item menu untuk tombol konfirmasi.
     */
    public function view($id = null)
    {
        $restaurantId = session('restaurant_id');
        $order = $this->orderModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$order) {
            return redirect()->to('/cashier/orders')->with('error', 'Order tidak ditemukan');
        }

        $items = $this->orderItemModel
            ->select('order_items.*, menus.name as menu_name')
            ->join('menus', 'menus.id = order_items.menu_id', 'left')
            ->where('order_items.order_id', $id)
            ->findAll();

        // Ambil data payment terkait (untuk tampilkan bukti QRIS, jika ada)
        $payment = $this->paymentModel->getLatestByOrder((int) $id);

        $content = view('cashier/orders/view', ['order' => $order, 'items' => $items, 'payment' => $payment]);
        return view('layouts/Layout', ['title' => 'Detail Order #' . $order['order_code'], 'content' => $content]);
    }

    /**
     * Konfirmasi pembayaran tunai → status Lunas, pesanan lanjut ke dapur (Diproses).
     */
    public function confirmCash($id = null)
    {
        $restaurantId = session('restaurant_id');
        $order = $this->orderModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$order) {
            return redirect()->to('/cashier/orders')->with('error', 'Order tidak ditemukan');
        }

        $this->orderModel->update($id, [
            'payment_status' => 'Lunas',
            'order_status'   => 'Diproses',
        ]);

        // Catat juga di tabel payments: siapa kasirnya & kapan dikonfirmasi
        $this->paymentModel->markLunas($id, (int) session('user_id'));

        // Perbarui atau buat data pelanggan di CRM restoran
        $this->upsertCustomer($order);

        return redirect()->to('/cashier/orders')->with('success', 'Pembayaran cash dikonfirmasi');
    }

    /**
     * Verifikasi bukti QRIS: setujui (Lunas) atau tolak pembayaran.
     */
    public function verifyQris($id = null)
    {
        $restaurantId = session('restaurant_id');
        $order = $this->orderModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$order) {
            return redirect()->to('/cashier/orders')->with('error', 'Order tidak ditemukan');
        }

        $action = $this->request->getPost('action'); // 'approve' atau 'reject'
        $userId = (int) session('user_id');

        if ($action === 'approve') {
            $this->orderModel->update($id, [
                'payment_status' => 'Lunas',
                'order_status'   => 'Diproses',
            ]);
            $this->paymentModel->markLunas($id, $userId);
            $this->upsertCustomer($order);
            return redirect()->to('/cashier/orders')->with('success', 'QRIS dikonfirmasi, pesanan diproses');
        }

        // Tolak — alasan opsional, diisi kasir lewat input "notes" di form
        $rejectNotes = $this->request->getPost('notes');

        $this->orderModel->update($id, [
            'payment_status' => 'Ditolak',
        ]);
        $this->paymentModel->markDitolak($id, $userId, $rejectNotes);
        return redirect()->to('/cashier/orders')->with('error', 'Pembayaran QRIS ditolak');
    }

    /**
     * Simpan/update pelanggan berdasarkan WhatsApp (satu nomor = satu record CRM).
     */
    private function upsertCustomer(array $order): void
    {
        if (empty($order['customer_whatsapp'])) {
            return;
        }

        $existing = $this->customerModel
            ->where('restaurant_id', $order['restaurant_id'])
            ->where('whatsapp', $order['customer_whatsapp'])
            ->first();

        if ($existing) {
            $this->customerModel->update($existing['id'], [
                'total_orders' => $existing['total_orders'] + 1,
                'total_spent'  => $existing['total_spent'] + $order['total'],
                'last_order_at'=> date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->customerModel->insert([
                'restaurant_id' => $order['restaurant_id'],
                'name'          => $order['customer_name'],
                'whatsapp'      => $order['customer_whatsapp'],
                'total_orders'  => 1,
                'total_spent'   => $order['total'],
                'last_order_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}