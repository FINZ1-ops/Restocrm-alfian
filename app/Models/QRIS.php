<?php

namespace App\Models;

/**
 * QRIS Model
 * 
 * Mengelola QRIS (Quick Response Code Indonesian Standard) payment
 * Setiap restoran bisa setup QRIS untuk pembayaran order
 * QRIS digunakan sebagai alternatif pembayaran Cash
 */
class QRIS extends BaseRestaurantModel
{
    // Nama tabel di database
    protected $table = 'qris_settings';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = ['restaurant_id', 'merchant_name', 'qris_image', 'is_active'];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    /**
     * Ambil QRIS aktif untuk restoran
     * 
     * @return array|null - Data QRIS atau null
     */
    public function getActiveQRIS()
    {
        return $this->applyRestaurantFilter()
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Cek apakah restoran sudah setup QRIS
     * 
     * @return bool - True jika sudah setup
     */
    public function hasQRIS()
    {
        return $this->applyRestaurantFilter()
                    ->where('is_active', true)
                    ->first() !== null;
    }

    /**
     * Update QRIS merchant name dan image
     * 
     * @param string $merchantName - Nama merchant
     * @param string|null $qrisImage - File path QRIS image
     * @return bool - True jika berhasil
     */
    public function updateQRIS($merchantName, $qrisImage = null)
    {
        // Ambil QRIS yang sudah ada
        $existingQRIS = $this->getActiveQRIS();

        $data = ['merchant_name' => $merchantName];

        // Update image jika ada
        if ($qrisImage) {
            $data['qris_image'] = $qrisImage;
        }

        // Jika sudah ada, update
        if ($existingQRIS) {
            return $this->update($existingQRIS['id'], $data);
        }

        // Jika belum ada, create baru
        $data['is_active'] = true;
        return $this->insert($data);
    }

    /**
     * Disable QRIS
     * 
     * @return bool - True jika berhasil
     */
    public function disableQRIS()
    {
        $qris = $this->getActiveQRIS();

        if (!$qris) {
            return false;
        }

        return $this->update($qris['id'], ['is_active' => false]);
    }

    /**
     * Enable QRIS
     * 
     * @return bool - True jika berhasil
     */
    public function enableQRIS()
    {
        $qris = $this->where('restaurant_id', $this->restaurantId)
                     ->first();

        if (!$qris) {
            return false;
        }

        return $this->update($qris['id'], ['is_active' => true]);
    }
}
