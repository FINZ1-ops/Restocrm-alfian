<?php

namespace App\Models;

/**
 * MenuCategory Model
 * 
 * Mengelola kategori menu untuk setiap restoran
 * Setiap restoran memiliki kategorinya sendiri
 * Extends BaseRestaurantModel untuk otomatis filter berdasarkan restaurant_id
 */
class MenuCategory extends BaseRestaurantModel
{
    // Nama tabel di database
    protected $table = 'menu_categories';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    // restaurant_id akan ditambahkan otomatis oleh BaseRestaurantModel
    protected $allowedFields = ['restaurant_id', 'name', 'sort_order', 'is_active'];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    /**
     * Ambil semua kategori aktif untuk restoran yang login
     * Diurutkan berdasarkan sort_order (urutan tampilan)
     * 
     * @return array - Daftar kategori aktif
     */
    public function getActiveCategories()
    {
        // applyRestaurantFilter() otomatis dari BaseRestaurantModel
        return $this->applyRestaurantFilter()
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Reorder kategori menu
     * Digunakan untuk drag-drop reordering di admin
     * 
     * @param array $newOrder - Array dengan ID kategori dalam urutan baru
     * @return bool - True jika berhasil
     */
    public function reorderCategories($newOrder)
    {
        // Looping setiap kategori dan update sort_order
        foreach ($newOrder as $index => $id) {
            // Index dimulai dari 0, jadi +1 untuk urutan normal (1, 2, 3...)
            $this->update($id, ['sort_order' => $index + 1]);
        }
        return true;
    }

    /**
     * Cek apakah kategori sudah ada
     * 
     * @param string $name - Nama kategori
     * @return bool - True jika sudah ada
     */
    public function categoryExists($name)
    {
        return $this->applyRestaurantFilter()
                    ->where('name', $name)
                    ->first() !== null;
    }

    /**
     * Ambil jumlah kategori untuk restoran
     * 
     * @return int - Jumlah kategori
     */
    public function countCategories()
    {
        return $this->applyRestaurantFilter()
                    ->countAllResults();
    }
}
