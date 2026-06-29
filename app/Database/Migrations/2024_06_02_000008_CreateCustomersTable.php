<?php

namespace App\Database\Migrations;

class CreateCustomersTable extends \CodeIgniter\Database\Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'restaurant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'whatsapp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'total_orders' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ],
            'total_spent' => [
                'type' => 'DECIMAL',
                'constraint' => [12, 2],
                'default' => 0,
            ],
            'last_order_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey(
            'restaurant_id',
            'restaurants',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->forge->addForeignKey('user_id','users','id','SET NULL','SET NULL');
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('restaurant_id');
        $this->forge->createTable('customers');
    }

    public function down()
    {
        $this->forge->dropTable('customers');
    }
}
