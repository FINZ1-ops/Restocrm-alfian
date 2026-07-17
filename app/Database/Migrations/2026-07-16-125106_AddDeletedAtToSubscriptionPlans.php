<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToSubscriptionPlans extends Migration
{
    public function up()
    {
        $fields = [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('subscription_plans', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('subscription_plans', 'deleted_at');
    }
}
