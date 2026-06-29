<?php

namespace App\Database\Migrations;

class CreateLeadsTable extends \CodeIgniter\Database\Migration
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
            'business_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'owner_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'whatsapp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'business_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'lead_source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Baru', 'Dihubungi', 'Tertarik', 'Demo', 'Negosiasi', 'Deal', 'Tidak Tertarik'],
                'default' => 'Baru',
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'next_followup_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'notes'=>[
                'type'=>'TEXT',
                'null'=>true,
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
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('leads');
    }

    public function down()
    {
        $this->forge->dropTable('leads');
    }
}
