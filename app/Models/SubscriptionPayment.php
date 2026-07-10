<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * SubscriptionPayment Model
 *
 * Pembayaran langganan APLIKASI dari restoran ke pemilik RESTOCRM —
 * BEDA dengan Payment (pembayaran order customer ke restoran).
 * Dikelola sepenuhnya oleh Super Admin, lintas-restoran, jadi TIDAK
 * extends BaseRestaurantModel (sama seperti RestaurantSubscription).
 */
class SubscriptionPayment extends Model
{
    protected $table            = 'subscription_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'restaurant_id', 'subscription_id', 'invoice_number', 'amount',
        'payment_method', 'status', 'payment_date', 'due_date',
        'proof_image', 'confirmed_by', 'confirmed_at', 'notes',
    ];

    /**
     * Bikin nomor invoice unik: INV-YYYYMMDD-XXXX
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        do {
            $candidate = $prefix . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        } while ($this->where('invoice_number', $candidate)->first());

        return $candidate;
    }

    /**
     * Semua invoice + info restoran & plan, terbaru dulu.
     * Filter opsional lewat $status (mis. 'Belum Dibayar').
     */
    public function getAllWithDetails(?string $status = null): array
    {
        $builder = $this->db->table('subscription_payments sp')
            ->select('sp.*, r.name as restaurant_name, r.whatsapp as restaurant_whatsapp, sub.plan_id, sub.restaurant_id as sub_restaurant_id')
            ->join('restaurant_subscriptions sub', 'sub.id = sp.subscription_id', 'left')
            ->join('restaurants r', 'r.id = COALESCE(sp.restaurant_id, sub.restaurant_id)', 'left');

        if ($status) {
            $builder->where('sp.status', $status);
        }

        return $builder->orderBy('sp.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Invoice yang jatuh tempo: belum lunas & due_date sudah lewat hari ini.
     */
    public function getOverdue(): array
    {
        return $this->db->table('subscription_payments sp')
            ->select('sp.*, r.name as restaurant_name, r.whatsapp as restaurant_whatsapp')
            ->join('restaurant_subscriptions sub', 'sub.id = sp.subscription_id', 'left')
            ->join('restaurants r', 'r.id = COALESCE(sp.restaurant_id, sub.restaurant_id)', 'left')
            ->whereIn('sp.status', ['Belum Dibayar', 'Menunggu Konfirmasi', 'Terlambat'])
            ->where('sp.due_date <', date('Y-m-d'))
            ->orderBy('sp.due_date', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Detail 1 invoice + info restoran & subscription terkait.
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->db->table('subscription_payments sp')
            ->select('sp.*, r.name as restaurant_name, r.whatsapp as restaurant_whatsapp, r.address as restaurant_address, rs.plan_id, rs.billing_cycle, rs.end_date as subscription_end_date')
            ->join('restaurant_subscriptions rs', 'rs.id = sp.subscription_id', 'left')
            ->join('restaurants r', 'r.id = COALESCE(sp.restaurant_id, rs.restaurant_id)', 'left')
            ->where('sp.id', $id)
            ->get()->getRowArray();
    }
}