<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * RestaurantSubscription Model
 * 
 * Mengelola langganan restoran
 * Setiap restoran harus memiliki minimal satu subscription aktif
 * Status: Trial, Aktif, Expired, Suspend
 */
class RestaurantSubscription extends Model
{
    // Nama tabel di database
    protected $table = 'restaurant_subscriptions';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = [
        'restaurant_id', 'plan_id', 'start_date', 'end_date',
        'status', 'billing_cycle', 'is_active'
    ];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    /**
     * Ambil subscription aktif untuk restoran
     * 
     * @param int $restaurantId - ID restoran
     * @return array|null - Data subscription aktif atau null
     */
    public function getActiveSubscription($restaurantId)
    {
        return $this->where('restaurant_id', $restaurantId)
                    ->whereIn('status', ['Trial', 'Aktif'])
                    // Check apakah belum expired
                    ->where('end_date >=', date('Y-m-d'))
                    ->first();
    }

    /**
     * Ambil subscription dengan plan info
     * 
     * @param int $restaurantId - ID restoran
     * @return array|null - Data subscription dengan plan
     */
    public function getSubscriptionWithPlan($restaurantId)
    {
        return $this->db->table('restaurant_subscriptions rs')
                        ->select('rs.*, sp.name as plan_name, sp.max_tables, sp.max_menus, sp.has_crm')
                        ->join('subscription_plans sp', 'rs.plan_id = sp.id')
                        ->where('rs.restaurant_id', $restaurantId)
                        ->where('rs.is_active', true)
                        ->get();
    }

    /**
     * Cek apakah subscription masih aktif
     * 
     * @param int $restaurantId - ID restoran
     * @return bool - True jika subscription masih aktif
     */
    public function isSubscriptionActive($restaurantId)
    {
        $subscription = $this->getActiveSubscription($restaurantId);
        return $subscription !== null;
    }

    /**
     * Hitung sisa hari subscription
     * 
     * @param int $restaurantId - ID restoran
     * @return int|false - Jumlah hari tersisa atau false jika tidak ada subscription
     */
    public function getDaysRemaining($restaurantId)
    {
        $subscription = $this->getActiveSubscription($restaurantId);

        if (!$subscription) {
            return false;
        }

        // Hitung selisih hari antara end_date dengan hari ini
        $endDate = new \DateTime($subscription['end_date']);
        $today = new \DateTime(date('Y-m-d'));
        $diff = $today->diff($endDate);

        return $diff->days;
    }

    /**
     * Ambil subscription history untuk restoran
     * 
     * @param int $restaurantId - ID restoran
     * @return array - Riwayat subscription
     */
    public function getSubscriptionHistory($restaurantId)
    {
        return $this->db->table('restaurant_subscriptions rs')
                        ->select('rs.*, sp.name as plan_name')
                        ->join('subscription_plans sp', 'rs.plan_id = sp.id')
                        ->where('rs.restaurant_id', $restaurantId)
                        ->orderBy('rs.start_date', 'DESC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Buat trial subscription untuk restoran baru
     * 
     * @param int $restaurantId - ID restoran
     * @param int $planId - ID paket (default Basic)
     * @param int $trialDays - Durasi trial (default 30 hari)
     * @return int|false - ID subscription baru atau false jika gagal
     */
    public function createTrialSubscription($restaurantId, $planId = 1, $trialDays = 30)
    {
        $data = [
            'restaurant_id' => $restaurantId,
            'plan_id' => $planId,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime("+{$trialDays} days")),
            'status' => 'Trial',
            'billing_cycle' => 'monthly',
            'is_active' => true,
        ];

        return $this->insert($data);
    }

    /**
     * Upgrade subscription ke plan berbeda
     * 
     * @param int $restaurantId - ID restoran
     * @param int $newPlanId - ID paket baru
     * @param string $billingCycle - monthly atau yearly
     * @return bool - True jika berhasil
     */
    public function upgradeSubscription($restaurantId, $newPlanId, $billingCycle = 'monthly')
    {
        // Hitung durasi langganan baru (1 bulan atau 1 tahun)
        $duration = $billingCycle === 'yearly' ? '+1 year' : '+1 month';

        // Ambil subscription aktif saat ini
        $currentSubscription = $this->getActiveSubscription($restaurantId);

        if (!$currentSubscription) {
            return false;
        }

        // Update subscription dengan plan baru
        return $this->update($currentSubscription['id'], [
            'plan_id' => $newPlanId,
            'end_date' => date('Y-m-d', strtotime($duration)),
            'billing_cycle' => $billingCycle,
            'status' => 'Aktif',
        ]);
    }

    /**
     * Suspend subscription
     * 
     * @param int $restaurantId - ID restoran
     * @return bool - True jika berhasil
     */
    public function suspendSubscription($restaurantId)
    {
        $subscription = $this->getActiveSubscription($restaurantId);

        if (!$subscription) {
            return false;
        }

        return $this->update($subscription['id'], [
            'status' => 'Suspend',
            'is_active' => false,
        ]);
    }

    /**
     * Activate subscription yang suspended
     * 
     * @param int $restaurantId - ID restoran
     * @return bool - True jika berhasil
     */
    public function activateSubscription($restaurantId)
    {
        $subscription = $this->where('restaurant_id', $restaurantId)
                             ->where('status', 'Suspend')
                             ->first();

        if (!$subscription) {
            return false;
        }

        return $this->update($subscription['id'], [
            'status' => 'Aktif',
            'is_active' => true,
        ]);
    }
}
