<?php

namespace App\Models;

/**
 * Menu Model
 * 
 * Mengelola daftar menu/makanan untuk setiap restoran
 * Setiap menu harus memiliki category_id dan restaurant_id
 * Includes fitur label: best_seller, promo, rekomendasi, baru
 */
class Menu extends BaseRestaurantModel
{
    // Nama tabel di database
    protected $table = 'menus';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = ['restaurant_id', 'category_id', 'name', 'description', 'price', 'image', 'label', 'is_available', 'is_active'];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    // Aturan validasi saat insert/update
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[255]',
        'category_id' => 'required|numeric',
        'price' => 'required|numeric|greater_than[0]',
        // Label harus salah satu dari: biasa, best_seller, promo, rekomendasi, baru
        'label' => 'permit_empty|in_list[biasa,best_seller,promo,rekomendasi,baru]',
    ];

    /**
     * Ambil menu berdasarkan kategori
     * Hanya ambil menu yang aktif dan tersedia
     * 
     * @param int $categoryId - ID kategori
     * @return array - Daftar menu di kategori tersebut
     */
    public function getMenusByCategory($categoryId)
    {
        return $this->applyRestaurantFilter()
                    ->where('category_id', $categoryId)
                    ->where('is_active', true)
                    ->where('is_available', true)
                    ->findAll();
    }

    /**
     * Ambil semua menu aktif dan tersedia
     * 
     * @return array - Daftar semua menu aktif
     */
    public function getActiveMenus()
    {
        return $this->applyRestaurantFilter()
                    ->where('is_active', true)
                    ->where('is_available', true)
                    ->findAll();
    }

    /**
     * Ambil menu dengan label khusus (best_seller, promo, dll)
     * 
     * @param string $label - Label menu
     * @return array - Daftar menu dengan label tersebut
     */
    public function getMenusByLabel($label)
    {
        return $this->applyRestaurantFilter()
                    ->where('label', $label)
                    ->where('is_active', true)
                    ->where('is_available', true)
                    ->findAll();
    }

    /**
     * Ambil menu terlaris berdasarkan jumlah order
     * Digunakan untuk laporan dan rekomendasi
     * 
     * @param int $limit - Jumlah menu yang diambil
     * @return array - Daftar menu terlaris
     */
    public function getTopSellingMenus($limit = 10)
    {
        // Query dengan join ke order_items untuk hitung penjualan
        return $this->db->table('menus m')
                        // Select nama menu dan hitung berapa kali terjual
                        ->select('m.*, COUNT(oi.id) as sales_count, SUM(oi.quantity) as total_quantity')
                        // Join dengan order_items untuk data penjualan
                        ->join('order_items oi', 'm.id = oi.menu_id', 'left')
                        // Filter berdasarkan restaurant_id dari session
                        ->where('m.restaurant_id', $this->restaurantId)
                        // Hanya ambil menu yang aktif
                        ->where('m.is_active', true)
                        // Group by ID menu
                        ->groupBy('m.id')
                        // Sort berdasarkan jumlah terjual (terbanyak di atas)
                        ->orderBy('sales_count', 'DESC')
                        // Batasi jumlah hasil
                        ->limit($limit)
                        // Get hasilnya
                        ->get()
                        ->getResultArray();
    }

    /**
     * Ambil menu dengan kategori info
     * 
     * @return array - Daftar menu dengan kategori
     */
    public function getMenusWithCategory()
    {
        return $this->db->table('menus m')
                        ->select('m.*, mc.name as category_name')
                        ->join('menu_categories mc', 'm.category_id = mc.id')
                        ->where('m.restaurant_id', $this->restaurantId)
                        ->where('m.is_active', true)
                        ->get()
                        ->getResultArray();
    }
}
