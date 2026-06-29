<?php

namespace App\Database\Migrations;

class UpdatePaymentsTable extends \CodeIgniter\Database\Migration   
{
  public function up()
    {
        // 1. Tambah kolom baru
        $this->forge->addColumn('payments', [
            'restaurant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'order_id',
            ],
            'confirmed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'proof_image',
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'confirmed_by',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'confirmed_at',
            ],
        ]);
 
        // 2. Samakan nilai enum status dengan payment_status di tabel orders.
        //    Mapping nilai lama -> baru dilakukan di seeder/aplikasi,
        //    di sini hanya mengubah definisi kolom.
        $this->db->query(
            "ALTER TABLE payments
             MODIFY status ENUM('Belum Dibayar', 'Menunggu Konfirmasi', 'Lunas', 'Ditolak')
             DEFAULT 'Menunggu Konfirmasi'"
        );
 
        // 3. Tambah foreign key restaurant_id -> restaurants
        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'id', 'CASCADE', 'SET NULL', 'payments');
 
        // 4. Tambah foreign key confirmed_by -> users (yang mengonfirmasi)
        $this->forge->addForeignKey('confirmed_by', 'users', 'id', 'CASCADE', 'SET NULL', 'payments');
    }
 
    public function down()
    {
        $this->db->query('ALTER TABLE payments DROP FOREIGN KEY payments_restaurant_id_foreign');
        $this->db->query('ALTER TABLE payments DROP FOREIGN KEY payments_confirmed_by_foreign');
 
        $this->forge->dropColumn('payments', ['restaurant_id', 'confirmed_by', 'confirmed_at', 'notes']);
 
        $this->db->query(
            "ALTER TABLE payments
             MODIFY status ENUM('pending', 'confirmed', 'rejected')
             DEFAULT 'pending'"
        );
    }
}
