<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TableCount extends Migration
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
            'table_total' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ]);     
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->createTable('orders');
    }

    public function down()
    {
        //
    }
}
