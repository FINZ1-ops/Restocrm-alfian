<?php

namespace App\Models;

class Payment extends BaseRestaurantModel
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = [
        'restaurant_id', 'order_id', 'amount', 'payment_method',
        'status', 'proof_image', 'confirmed_by', 'confirmed_at', 'notes',
    ];

    public function createForOrder(array $data)
    {
        $data['status'] = $data['payment_method'] === 'qris'
            ? 'Menunggu Konfirmasi'
            : 'Belum Dibayar';
 
        // Insert langsung lewat query builder, BYPASS auto-filter
        // BaseRestaurantModel supaya tidak tertukar dengan restaurant_id
        // sesi kasir/admin yang mungkin masih aktif di browser yang sama.
        $this->db->table('payments')->insert($data);
        return $this->db->insertID();
    }
 
    /**
     * Ambil payment terbaru milik sebuah order.
     * Dipanggil dari sisi Kasir (session restaurant_id ada),
     * jadi otomatis ikut terfilter oleh applyRestaurantFilter().
     *
     * @param int $orderId
     * @return array|null
     */
    public function getLatestByOrder(int $orderId)
    {
        return $this->applyRestaurantFilter()
                    ->where('order_id', $orderId)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }
 
    /**
     * Tandai payment sebagai Lunas, dicatat siapa & kapan konfirmasinya.
     * Mencari row payment berdasarkan order_id, lalu update pakai id-nya
     * (lebih aman & konsisten dengan signature update($id, $data) bawaan).
     *
     * @param int $orderId
     * @param int $confirmedBy ID user kasir yang mengonfirmasi
     * @return bool
     */
    public function markLunas(int $orderId, int $confirmedBy): bool
    {
        $payment = $this->getLatestByOrder($orderId);
        if (!$payment) {
            return false;
        }
 
        return $this->update($payment['id'], [
            'status'       => 'Lunas',
            'confirmed_by' => $confirmedBy,
            'confirmed_at' => date('Y-m-d H:i:s'),
        ]);
    }
 
    /**
     * Tandai payment sebagai Ditolak, dicatat siapa, kapan, dan alasannya.
     *
     * @param int $orderId
     * @param int $confirmedBy ID user kasir yang menolak
     * @param string|null $notes Alasan penolakan (opsional)
     * @return bool
     */
    public function markDitolak(int $orderId, int $confirmedBy, ?string $notes = null): bool
    {
        $payment = $this->getLatestByOrder($orderId);
        if (!$payment) {
            return false;
        }
 
        return $this->update($payment['id'], [
            'status'       => 'Ditolak',
            'confirmed_by' => $confirmedBy,
            'confirmed_at' => date('Y-m-d H:i:s'),
            'notes'        => $notes,
        ]);
    }
}
