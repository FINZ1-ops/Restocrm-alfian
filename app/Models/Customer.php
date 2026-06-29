<?php

namespace App\Models;

/**
 * Customer Model (CRM)
 * 
 * Mengelola data customer yang pernah order di restoran
 * Data customer disimpan otomatis saat ada order
 * Jika WhatsApp sama, update data customer (jangan buat baru)
 */
class Customer extends BaseRestaurantModel
{
    // Nama tabel di database
    protected $table = 'customers';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = [
        'user_id',
        'restaurant_id', 
        'name', 
        'whatsapp', 
        'total_orders', 
        'total_spent', 
        'last_order_at'
    ];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    /**
     * Cari customer berdasarkan WhatsApp
     * 
     * @param string $whatsapp - Nomor WhatsApp
     * @return array|null - Data customer atau null
     */
    public function getCustomerByWhatsapp($whatsapp)
    {
        return $this->applyRestaurantFilter()
                    ->where('whatsapp', $whatsapp)
                    ->first();
    }

    /**
     * Cari atau buat customer
     * Jika WhatsApp sudah ada, return customer yang ada
     * Jika belum ada, buat customer baru
     * 
     * @param string $name - Nama customer
     * @param string $whatsapp - Nomor WhatsApp
     * @return int - ID customer
     */
    public function findOrCreateCustomer($name, $whatsapp)
    {
        // Cari customer dengan whatsapp yang sama
        $existingCustomer = $this->getCustomerByWhatsapp($whatsapp);

        // Jika sudah ada, return ID-nya
        if ($existingCustomer) {
            return $existingCustomer['id'];
        }

        // Jika belum ada, buat customer baru
        $newCustomer = [
            'name' => $name,
            'whatsapp' => $whatsapp,
            'total_orders' => 0,
            'total_spent' => 0,
        ];

        $this->insert($newCustomer);
        return $this->insertID();
    }

    /**
     * Update order count dan spending saat ada order baru
     * 
     * @param int $customerId - ID customer
     * @param float $orderAmount - Jumlah pembayaran order
     * @return bool - True jika berhasil
     */
    public function updateOrderStats($customerId, $orderAmount)
    {
        // Ambil customer sekarang
        $customer = $this->find($customerId);

        if (!$customer) {
            return false;
        }

        // Update: increment total_orders, tambah total_spent, update last_order_at
        return $this->update($customerId, [
            'total_orders' => $customer['total_orders'] + 1,
            'total_spent' => $customer['total_spent'] + $orderAmount,
            'last_order_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ambil top customers berdasarkan spending
     * 
     * @param int $limit - Jumlah customer
     * @return array - Daftar top customers
     */
    public function getTopCustomers($limit = 10)
    {
        return $this->applyRestaurantFilter()
                    ->orderBy('total_spent', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Ambil frequent customers (banyak order)
     * 
     * @param int $limit - Jumlah customer
     * @return array - Daftar frequent customers
     */
    public function getFrequentCustomers($limit = 10)
    {
        return $this->applyRestaurantFilter()
                    ->orderBy('total_orders', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Hitung total customers
     * 
     * @return int - Jumlah customers
     */
    public function countCustomers()
    {
        return $this->applyRestaurantFilter()
                    ->countAllResults();
    }

    /**
     * Ambil customers yang pernah order bulan ini
     * 
     * @return array - Daftar customers bulan ini
     */
    public function getCustomersThisMonth()
    {
        return $this->db->table('customers c')
                        ->select('DISTINCT c.*')
                        ->join('orders o', 'c.id = o.customer_id')
                        ->where('c.restaurant_id', $this->restaurantId)
                        ->where('YEAR(o.created_at)', date('Y'))
                        ->where('MONTH(o.created_at)', date('m'))
                        ->get()
                        ->getResultArray();
    }
}
