<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Restaurant Model
 * 
 * Mengelola data restoran yang terdaftar di sistem
 * Ini adalah core dari multi-tenant system - setiap restoran
 * merupakan tenant tersendiri dengan data terpisah
 */
class Restaurant extends Model
{
    // Nama tabel di database
    protected $table = 'restaurants';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array atau object)
    protected $returnType = 'array';
    
    // Jangan gunakan soft delete
    protected $useSoftDeletes = false;
    
    // Field yang boleh di-insert/update
    protected $allowedFields = ['name', 'slug', 'address', 'whatsapp', 'logo', 'description', 'opening_hours', 'is_active'];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';
    
    // Aturan validasi saat insert/update
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]|is_unique[restaurants.name,id,{id}]',
        'slug' => 'required|is_unique[restaurants.slug,id,{id}]',
        'address' => 'required|min_length[5]',
        'whatsapp' => 'required|regex_match[/^(\+62|0)[0-9]{9,12}$/]',
    ];

    /**
     * Cari restoran berdasarkan slug
     * Digunakan untuk QR scanning dan customer ordering
     * 
     * @param string $slug - Slug restoran
     * @return array|null - Data restoran atau null
     */
    public function getRestaurantBySlug($slug)
    {
        return $this->where('slug', $slug)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Generate slug dari nama restoran
     * Slug digunakan sebagai URL-friendly identifier
     * 
     * @param string $name - Nama restoran
     * @return string - Slug hasil generate
     */
    public function generateSlug($name)
    {
        // Konversi ke lowercase
        $slug = strtolower($name);
        
        // Ganti spasi dan karakter khusus dengan dash
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        
        // Trim dash di awal dan akhir
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Ambil statistik restoran untuk dashboard
     * 
     * @param int $restaurantId - ID restoran
     * @return array - Statistik hari ini
     */
    public function getRestaurantStats($restaurantId)
    {
        // Hitung jumlah order hari ini
        $orders = $this->db->table('orders')
                           ->where('restaurant_id', $restaurantId)
                           ->where('DATE(created_at)', date('Y-m-d'))
                           ->countAllResults();

        // Hitung total revenue hari ini (yang sudah lunas)
        $revenue = $this->db->table('orders')
                            ->selectSum('total')
                            ->where('restaurant_id', $restaurantId)
                            ->where('DATE(created_at)', date('Y-m-d'))
                            ->where('payment_status', 'Lunas')
                            ->get()
                            ->getRow();

        // Return statistik dalam bentuk array
        return [
            'orders_today' => $orders,
            'revenue_today' => $revenue->total ?? 0,
        ];
    }

    /**
     * Ambil semua restoran aktif
     * 
     * @return array - Daftar restoran aktif
     */
    public function getActiveRestaurants()
    {
        return $this->where('is_active', true)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
