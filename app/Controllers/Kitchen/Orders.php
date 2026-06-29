<?php

namespace App\Controllers\Kitchen;

use App\Controllers\BaseController;
use App\Models\Order;
use App\Models\OrderItem;

/**
 * Controller antrian dapur — menampilkan pesanan aktif dan mengubah status proses masak.
 * Hanya bisa diakses role `dapur` (filter di Routes.php).
 */
class Orders extends BaseController
{
    protected Order $orderModel;
    protected OrderItem $orderItemModel;

    public function __construct()
    {
        $this->orderModel     = new Order();
        $this->orderItemModel = new OrderItem();
    }

    /**
     * Halaman utama dapur: daftar pesanan yang belum selesai/dibatalkan.
     * Setiap pesanan dilengkapi daftar item menu (join ke tabel menus).
     */
    public function index()
    {
        // ID restoran dari session (diset saat login user dapur/kasir/admin_resto)
        $restaurantId = session('restaurant_id');
        $db = \Config\Database::connect();

        // Ambil pesanan yang masih dalam alur dapur (belum Selesai / Dibatalkan)
        $orders = $db->table('orders')
            ->where('restaurant_id', $restaurantId)
            ->whereIn('order_status', ['Menunggu Konfirmasi', 'Diproses', 'Siap Disajikan'])
            ->orderBy('created_at', 'ASC') // yang paling lama menunggu di atas
            ->get()->getResultArray();

        // Lampirkan detail item per pesanan agar tampilan kartu dapur lengkap
        if (!empty($orders)) {
            $orderIds = array_column($orders, 'id');
            $allItems = $db->table('order_items')
                ->select('order_items.*, menus.name as menu_name')
                ->join('menus', 'menus.id = order_items.menu_id', 'left')
                ->whereIn('order_id', $orderIds)
                ->get()->getResultArray();
                
            $itemsByOrder = [];
            foreach ($allItems as $item) {
                $itemsByOrder[$item['order_id']][] = $item;
            }
            
            foreach ($orders as &$order) {
                $order['items'] = $itemsByOrder[$order['id']] ?? [];
            }
        }

        // Pola layout admin: konten dibungkus lalu dimasukkan ke layouts/Layout
        $content = view('kitchen/orders/index', ['orders' => $orders]);
        return view('layouts/Layout', ['title' => 'Antrian Dapur', 'content' => $content]);
    }

    /**
     * Memperbarui status pesanan (alur: Diproses → Siap Disajikan → Selesai).
     *
     * @param int|null $id ID pesanan dari URL (kitchen/orders/{id}/status)
     */
    public function updateStatus($id = null)
    {
        $restaurantId = session('restaurant_id');

        // Pastikan pesanan milik restoran yang sama (cegah akses lintas tenant)
        $order = $this->orderModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();

        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Order tidak ditemukan']);
        }

        $newStatus = $this->request->getPost('status');

        // Hanya status ini yang boleh diubah dari sisi dapur
        $allowed   = ['Diproses', 'Siap Disajikan', 'Selesai'];

        if (!in_array($newStatus, $allowed)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid']);
        }

        $this->orderModel->update($id, ['order_status' => $newStatus]);

        // Catat riwayat perubahan status untuk audit / laporan
        $db = \Config\Database::connect();
        $db->table('order_status_logs')->insert([
            'order_id'   => $id,
            'new_status'  => $newStatus,
            'changed_by' => session('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Jika request AJAX (mis. fetch), kembalikan JSON; jika form biasa, redirect
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status diperbarui']);
        }

        return redirect()->to(base_url('kitchen/orders'))
            ->with('success', 'Status pesanan diperbarui menjadi ' . $newStatus);
    }
}
