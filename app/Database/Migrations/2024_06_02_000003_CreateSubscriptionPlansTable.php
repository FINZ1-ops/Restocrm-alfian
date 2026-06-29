<?php

namespace App\Database\Migrations;

class CreateSubscriptionPlansTable extends \CodeIgniter\Database\Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'max_tables' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 10,
            ],
            'max_menus' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 50,
            ],
            'has_crm' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'price_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => [10, 2],
                'default' => 0,
            ],
            'price_yearly' => [
                'type' => 'DECIMAL',
                'constraint' => [10, 2],
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->createTable('subscription_plans');
    }

    public function down()
    {
        $this->forge->dropTable('subscription_plans');
    }
}
