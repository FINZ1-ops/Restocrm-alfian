<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk invoice pembayaran langganan aplikasi.
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
     * Generate a unique invoice number in the format INV-YYYYMMDD-XXXX.
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
     * Retrieve all invoices with restaurant and plan details.
     * Optional status filter can narrow the result set.
     */
    public function getAllWithDetails(?string $status = null): array
    {
        $builder = $this->db->table('subscription_payments sp')
            ->select('sp.*, r.name as restaurant_name, r.whatsapp as restaurant_whatsapp, sub.plan_id, sub.restaurant_id as sub_restaurant_id, p.name as plan_name')
            ->join('restaurant_subscriptions sub', 'sub.id = sp.subscription_id', 'left')
            ->join('subscription_plans p', 'p.id = sub.plan_id', 'left')
            ->join('restaurants r', 'r.id = COALESCE(sp.restaurant_id, sub.restaurant_id)', 'left', false);

        if ($status) {
            $builder->where('sp.status', $status);
        }

        return $builder->orderBy('sp.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Retrieve overdue invoices that are past due and not yet paid.
     */
    public function getOverdue(): array
    {
        return $this->db->table('subscription_payments sp')
            ->select('sp.*, r.name as restaurant_name, r.whatsapp as restaurant_whatsapp, p.name as plan_name')
            ->join('restaurant_subscriptions sub', 'sub.id = sp.subscription_id', 'left')
            ->join('subscription_plans p', 'p.id = sub.plan_id', 'left')
            ->join('restaurants r', 'r.id = COALESCE(sp.restaurant_id, sub.restaurant_id)', 'left', false)
            ->whereIn('sp.status', ['Belum Dibayar', 'Menunggu Konfirmasi', 'Terlambat'])
            ->where('sp.due_date <', date('Y-m-d'))
            ->orderBy('sp.due_date', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Check whether the restaurant_subscriptions table contains a column.
     */
    public function subscriptionHasColumn(string $column): bool
    {
        try {
            $table = $this->db->DBPrefix . 'restaurant_subscriptions';
            $result = $this->db->query("SHOW COLUMNS FROM {$table} LIKE ?", [$column])->getResultArray();
            return !empty($result);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Create invoices for active subscriptions due for renewal.
     */
    public function generateInvoicesFromDueSubscriptions(): void
    {
        $today = date('Y-m-d');

        if (! $this->subscriptionHasColumn('next_invoice_date')) {
            return;
        }

        $subscriptions = $this->db->table('restaurant_subscriptions rs')
            ->select('rs.*, sp.price_monthly, sp.price_yearly, sp.name as plan_name')
            ->join('subscription_plans sp', 'sp.id = rs.plan_id', 'left')
            ->where('rs.status', 'Aktif')
            ->where('rs.next_invoice_date <=', $today)
            ->get()
            ->getResultArray();

        foreach ($subscriptions as $subscription) {
            $existing = $this->where('subscription_id', $subscription['id'])
                ->whereIn('status', ['Belum Dibayar', 'Menunggu Konfirmasi', 'Terlambat'])
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($existing) {
                continue;
            }

            $amount = $subscription['billing_cycle'] === 'yearly'
                ? $subscription['price_yearly']
                : $subscription['price_monthly'];

            $dueDate = date('Y-m-d', strtotime('+7 days'));
            $nextInvoiceDate = date('Y-m-d', strtotime($subscription['next_invoice_date'] . ' ' . ($subscription['billing_cycle'] === 'yearly' ? '+1 year' : '+1 month')));

            $invoiceData = [
                'restaurant_id'   => $subscription['restaurant_id'],
                'subscription_id' => $subscription['id'],
                'invoice_number'  => $this->generateInvoiceNumber(),
                'amount'          => $amount,
                'status'          => 'Belum Dibayar',
                'due_date'        => $dueDate,
                'notes'           => 'Invoice otomatis untuk perpanjangan langganan ' . ($subscription['billing_cycle'] === 'yearly' ? 'tahunan' : 'bulanan') . '.',
            ];

            $paymentId = $this->insert($invoiceData);
            if (!$paymentId) {
                continue;
            }

            $this->db->table('restaurant_subscriptions')
                ->where('id', $subscription['id'])
                ->update([
                    'last_invoice_id'   => $paymentId,
                    'next_invoice_date' => $nextInvoiceDate,
                ]);
        }
    }

    /**
     * Update invoices to 'Terlambat' when payment is past due.
     */
    public function markOverdueInvoices(): void
    {
        $this->db->table('subscription_payments')
            ->whereIn('status', ['Belum Dibayar', 'Menunggu Konfirmasi'])
            ->where('due_date <', date('Y-m-d'))
            ->update(['status' => 'Terlambat']);
    }

    /**
     * Get invoice details including restaurant and related subscription.
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->db->table('subscription_payments sp')
            ->select('sp.*, r.name as restaurant_name, r.whatsapp as restaurant_whatsapp, r.address as restaurant_address, rs.plan_id, rs.billing_cycle, rs.end_date as subscription_end_date, p.name as plan_name')
            ->join('restaurant_subscriptions rs', 'rs.id = sp.subscription_id', 'left')
            ->join('subscription_plans p', 'p.id = rs.plan_id', 'left')
            ->join('restaurants r', 'r.id = COALESCE(sp.restaurant_id, rs.restaurant_id)', 'left', false)
            ->where('sp.id', $id)
            ->get()->getRowArray();
    }
}