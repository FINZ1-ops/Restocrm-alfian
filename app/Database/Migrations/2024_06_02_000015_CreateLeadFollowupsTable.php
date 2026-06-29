<?php

namespace App\Database\Migrations;

class CreateLeadFollowupsTable extends \CodeIgniter\Database\Migration
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
            'lead_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'followup_date' => [
                'type' => 'DATE',
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'next_followup_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', false, true);
        $this->forge->addForeignKey('lead_id', 'leads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lead_followups');
    }

    public function down()
    {
        $this->forge->dropTable('lead_followups');
    }
}
