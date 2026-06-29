<?php

namespace App\Models;

/**
 * Order Model
 * 
 * Mengelola pesanan dari customer
 * Payment Status: Belum Dibayar, Menunggu Konfirmasi, Lunas, Ditolak
 * Order Status: Menunggu Konfirmasi, Diproses, Siap Disajikan, Selesai, Dibatalkan
 */
class Order extends BaseRestaurantModel
{
    // Nama tabel di database
    protected $table = 'orders';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = [
        'restaurant_id', 'table_id', 'customer_id', 'order_code',
        'customer_name', 'customer_whatsapp', 'table_number', 'area_name',
        'total', 'payment_method', 'payment_status', 'order_status', 'notes'
    ];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    /**
     * Generate order code unik
     * Format: ORD-{YYYYMMDD}-{RANDOM_5_CHARS}
     * Contoh: ORD-20260602-AB12C
     * 
     * @return string - Order code unik
     */
    public function generateOrderCode()
    {
        // Format: ORD-YYYYMMDD-XXXXX
        $date = date('Ymd');
        // Generate 5 karakter random dari alphanumeric
        $random = substr(strtoupper(md5(mt_rand())), 0, 5);
        return "ORD-{$date}-{$random}";
    }

    /**
     * Ambil order berdasarkan order code
     * 
     * @param string $orderCode - Order code
     * @return array|null - Data order atau null
     */
    public function getOrderByCode($orderCode)
    {
        return $this->applyRestaurantFilter()
                    ->where('order_code', $orderCode)
                    ->first();
    }

    /**
     * Ambil order hari ini
     * 
     * @return array - Daftar order hari ini
     */
    public function getOrdersToday()
    {
        return $this->applyRestaurantFilter()
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil order dengan status tertentu
     * 
     * @param string $status - Order status
     * @return array - Daftar order dengan status tersebut
     */
    public function getOrdersByStatus($status)
    {
        return $this->applyRestaurantFilter()
                    ->where('order_status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil order dengan payment status tertentu
     * 
     * @param string $paymentStatus - Payment status
     * @return array - Daftar order dengan payment status tersebut
     */
    public function getOrdersByPaymentStatus($paymentStatus)
    {
        return $this->applyRestaurantFilter()
                    ->where('payment_status', $paymentStatus)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil order untuk meja tertentu yang masih aktif
     * 
     * @param int $tableId - ID meja
     * @return array - Daftar order aktif di meja tersebut
     */
    public function getActiveOrdersByTable($tableId)
    {
        return $this->applyRestaurantFilter()
                    ->where('table_id', $tableId)
                    // Exclude status yang sudah selesai
                    ->where('order_status !=', 'Selesai')
                    ->where('order_status !=', 'Dibatalkan')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Hitung total revenue hari ini
     * 
     * @return float - Total revenue hari ini (hanya yang sudah lunas)
     */
    public function getRevenuesToday()
    {
        // Query SUM dengan kondisi tertentu
        $result = $this->db->table('orders')
                           ->selectSum('total', 'total_revenue')
                           ->where('restaurant_id', $this->restaurantId)
                           ->where('DATE(created_at)', date('Y-m-d'))
                           // Hanya hitung yang sudah dibayar
                           ->where('payment_status', 'Lunas')
                           ->get()
                           ->getRow();

        // Return total atau 0 jika null
        return (float) ($result->total_revenue ?? 0);
    }

    /**
     * Hitung total revenue bulan ini
     * 
     * @return float - Total revenue bulan ini (hanya yang sudah lunas)
     */
    public function getRevenuesThisMonth()
    {
        // Query dengan kondisi bulan dan tahun saat ini
        $result = $this->db->table('orders')
                           ->selectSum('total', 'total_revenue')
                           ->where('restaurant_id', $this->restaurantId)
                           // Filter berdasarkan tahun dan bulan
                           ->where('YEAR(created_at)', date('Y'))
                           ->where('MONTH(created_at)', date('m'))
                           ->where('payment_status', 'Lunas')
                           ->get()
                           ->getRow();

        return (float) ($result->total_revenue ?? 0);
    }

    /**
     * Hitung jumlah order hari ini
     * 
     * @return int - Jumlah order hari ini
     */
    public function countOrdersToday()
    {
        return $this->db->table('orders')
                        ->where('restaurant_id', $this->restaurantId)
                        ->where('DATE(created_at)', date('Y-m-d'))
                        ->countAllResults();
    }

    /**
     * Ambil order dengan detail item
     * 
     * @param int $orderId - ID order
     * @return array - Data order dengan items
     */
    public function getOrderWithItems($orderId)
    {
        // Ambil data order
        $order = $this->applyRestaurantFilter()
                      ->find($orderId);

        // Jika order tidak ditemukan, return null
        if (!$order) {
            return null;
        }

        // Ambil items untuk order tersebut
        $items = $this->db->table('order_items oi')
                          ->select('oi.*, m.name, m.price as menu_price')
                          ->join('menus m', 'oi.menu_id = m.id')
                          ->where('oi.order_id', $orderId)
                          ->get()
                          ->getResultArray();

        // Tambahkan items ke order
        $order['items'] = $items;

        return $order;
    }

    /**
     * Update order status
     * 
     * @param int $orderId - ID order
     * @param string $status - Status baru
     * @param string|null $notes - Catatan (opsional)
     * @return bool - True jika berhasil
     */
    public function updateOrderStatus($orderId, $status, $notes = null)
    {
        $data = ['order_status' => $status];
        if ($notes) {
            $data['notes'] = $notes;
        }
        return $this->update($orderId, $data);
    }

    /**
     * Update payment status
     * 
     * @param int $orderId - ID order
     * @param string $paymentStatus - Payment status baru
     * @return bool - True jika berhasil
     */
    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        return $this->update($orderId, ['payment_status' => $paymentStatus]);
    }
}
