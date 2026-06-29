<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model item pesanan (baris di tabel order_items).
 *
 * Catatan: tidak memakai BaseRestaurantModel karena tabel ini
 * tidak punya kolom restaurant_id — isolasi data lewat relasi ke orders.
 */
class OrderItem extends Model
{
    protected $table            = 'order_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['order_id', 'menu_id', 'quantity', 'price', 'notes'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // tabel tidak punya updated_at
    protected $useSoftDeletes   = false;

    /**
     * Semua item dalam satu pesanan (tanpa nama menu).
     */
    public function getOrderItems(int $orderId): array
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    /**
     * Item pesanan beserta nama dan gambar menu (untuk tampilan detail).
     */
    public function getOrderItemsWithMenu(int $orderId): array
    {
        return $this->db->table('order_items oi')
            ->select('oi.*, m.name as menu_name, m.image as menu_image')
            ->join('menus m', 'oi.menu_id = m.id')
            ->where('oi.order_id', $orderId)
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah baris item (bukan total qty).
     */
    public function countOrderItems(int $orderId): int
    {
        return $this->where('order_id', $orderId)->countAllResults();
    }

    /**
     * Total nilai item = sum(harga × jumlah) per pesanan.
     */
    public function getTotalSubtotal(int $orderId): float
    {
        $result = $this->db->table('order_items')
            ->selectSum('(price * quantity)', 'total', false)
            ->where('order_id', $orderId)
            ->get()
            ->getRow();

        return (float) ($result->total ?? 0);
    }

    /**
     * Menambah satu baris item ke pesanan.
     */
    public function addItemToOrder(int $orderId, int $menuId, int $quantity, float $price, ?string $notes = null)
    {
        return $this->insert([
            'order_id' => $orderId,
            'menu_id'  => $menuId,
            'quantity' => $quantity,
            'price'    => $price,
            'notes'    => $notes,
        ]);
    }

    /**
     * Ubah jumlah item (harga per unit tidak diubah di sini).
     */
    public function updateOrderItem(int $itemId, int $quantity, ?string $notes = null): bool
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }

        $data = ['quantity' => $quantity];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }

        return $this->update($itemId, $data);
    }

    /**
     * Hapus satu baris item dari pesanan.
     */
    public function removeOrderItem(int $itemId): bool
    {
        return $this->delete($itemId);
    }
}
