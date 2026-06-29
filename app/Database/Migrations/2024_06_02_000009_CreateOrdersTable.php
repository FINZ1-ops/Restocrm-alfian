<?php

namespace App\Database\Migrations;

class CreateOrdersTable extends \CodeIgniter\Database\Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'restaurant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'table_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'order_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'customer_whatsapp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'table_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'area_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => [12, 2],
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'qris'],
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['Belum Dibayar', 'Menunggu Konfirmasi', 'Lunas', 'Ditolak'],
                'default' => 'Belum Dibayar',
            ],
            'order_status' => [
                'type' => 'ENUM',
                'constraint' => ['Menunggu Konfirmasi', 'Diproses', 'Siap Disajikan', 'Selesai', 'Dibatalkan'],
                'default' => 'Menunggu Konfirmasi',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('table_id', 'restaurant_tables', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
