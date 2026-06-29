<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SubscriptionPlan Model
 * 
 * Mengelola paket langganan yang tersedia
 * Paket: Basic, Pro, Premium
 * Setiap paket memiliki fitur dan harga berbeda
 */
class SubscriptionPlan extends Model
{
    // Nama tabel di database
    protected $table = 'subscription_plans';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = [
        'name', 'description', 'max_tables', 'max_menus', 'has_crm',
        'price_monthly', 'price_yearly', 'is_active'
    ];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    /**
     * Ambil semua paket aktif
     * 
     * @return array - Daftar paket aktif
     */
    public function getActivePlans()
    {
        return $this->where('is_active', true)
                    ->orderBy('price_monthly', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil paket berdasarkan nama
     * 
     * @param string $name - Nama paket (Basic, Pro, Premium)
     * @return array|null - Data paket atau null
     */
    public function getPlanByName($name)
    {
        return $this->where('name', $name)
                    ->first();
    }

    /**
     * Ambil harga paket
     * 
     * @param int $planId - ID paket
     * @param string $cycle - Cycle (monthly atau yearly)
     * @return float|null - Harga atau null jika tidak ditemukan
     */
    public function getPlanPrice($planId, $cycle = 'monthly')
    {
        $plan = $this->find($planId);

        if (!$plan) {
            return null;
        }

        // Return harga sesuai cycle
        return $cycle === 'yearly' ? $plan['price_yearly'] : $plan['price_monthly'];
    }

    /**
     * Hitung saving jika beli tahunan vs bulanan
     * 
     * @param int $planId - ID paket
     * @return array - Data pricing comparison
     */
    public function getPricingComparison($planId)
    {
        $plan = $this->find($planId);

        if (!$plan) {
            return null;
        }

        // Harga bulanan x 12 bulan
        $monthlyTotal = $plan['price_monthly'] * 12;
        
        // Saving dengan membeli yearly
        $saving = $monthlyTotal - $plan['price_yearly'];
        
        // Persentase saving
        $savingPercent = ($saving / $monthlyTotal) * 100;

        return [
            'plan_name' => $plan['name'],
            'monthly_price' => $plan['price_monthly'],
            'yearly_price' => $plan['price_yearly'],
            'monthly_total' => $monthlyTotal,
            'saving' => $saving,
            'saving_percent' => round($savingPercent, 2),
        ];
    }
}
