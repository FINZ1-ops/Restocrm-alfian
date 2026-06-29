<?php

namespace App\Models;

/**
 * RestaurantTable Model
 * 
 * Mengelola meja di restoran
 * Setiap meja memiliki QR token unik untuk customer scanning
 * QR token format: /scan/{qr_token}
 */
class RestaurantTable extends BaseRestaurantModel
{
    // Nama tabel di database
    protected $table = 'restaurant_tables';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = ['restaurant_id', 'table_number', 'area_name', 'capacity', 'qr_token', 'is_active'];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    /**
     * Generate QR token unik untuk meja
     * Format: 32 karakter hex dari random bytes
     * 
     * @return string - QR token unik
     */
    public function generateQRToken()
    {
        // Generate 16 random bytes dan convert ke hex (32 karakter)
        return bin2hex(random_bytes(16));
    }

    /**
     * Cari meja berdasarkan QR token
     * Digunakan saat customer scan QR di meja
     * 
     * @param string $qrToken - QR token dari URL
     * @return array|null - Data meja atau null jika tidak ditemukan
     */
    public function getTableByQRToken($qrToken)
    {
        return $this->where('qr_token', $qrToken)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Ambil semua meja aktif untuk restoran
     * Diurutkan berdasarkan area dan nomor meja
     * 
     * @return array - Daftar meja aktif
     */
    public function getActiveTables()
    {
        return $this->applyRestaurantFilter()
                    ->where('is_active', true)
                    // Urutkan berdasarkan area terlebih dahulu
                    ->orderBy('area_name', 'ASC')
                    // Kemudian berdasarkan nomor meja
                    ->orderBy('table_number', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil meja berdasarkan area
     * 
     * @param string $areaName - Nama area
     * @return array - Daftar meja di area tersebut
     */
    public function getTablesByArea($areaName)
    {
        return $this->applyRestaurantFilter()
                    ->where('area_name', $areaName)
                    ->where('is_active', true)
                    ->orderBy('table_number', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil daftar area unik
     * 
     * @return array - Daftar nama area
     */
    public function getAreas()
    {
        return $this->db->table('restaurant_tables')
                        ->distinct()
                        ->select('area_name')
                        ->where('restaurant_id', $this->restaurantId)
                        ->where('is_active', true)
                        ->orderBy('area_name', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Hitung jumlah meja untuk restoran
     * 
     * @return int - Jumlah meja aktif
     */
    public function countTables()
    {
        return $this->applyRestaurantFilter()
                    ->where('is_active', true)
                    ->countAllResults();
    }

    /**
     * Ambil meja dengan informasi order terkini
     * 
     * @return array - Daftar meja dengan order info
     */
    public function getTablesWithOrders()
    {
        return $this->db->table('restaurant_tables rt')
                        ->select('rt.*, COUNT(o.id) as active_orders')
                        ->join('orders o', 'rt.id = o.table_id AND o.order_status != "Selesai"', 'left')
                        ->where('rt.restaurant_id', $this->restaurantId)
                        ->where('rt.is_active', true)
                        ->groupBy('rt.id')
                        ->get()
                        ->getResultArray();
    }
}
