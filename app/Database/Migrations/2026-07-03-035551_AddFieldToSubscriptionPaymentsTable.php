<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel subscription_payments sebelumnya cuma punya kolom minim
 * (subscription_id, amount, payment_date, status: pending/paid/failed) —
 * belum cukup untuk fitur "Pembayaran Langganan Aplikasi" di brief
 * (Super Admin buat invoice, restoran upload bukti, Super Admin
 * konfirmasi/tolak, lihat tagihan jatuh tempo).
 *
 * Migration ini menambah kolom yang kurang & menyesuaikan status
 * ke istilah yang dipakai di seluruh aplikasi (Bahasa Indonesia,
 * konsisten dengan orders.payment_status dkk).
 */
class AddFieldsToSubscriptionPaymentsTable extends Migration
{
    public function up()
    {
        // restaurant_id ditambah supaya tidak perlu join ke
        // restaurant_subscriptions cuma untuk tahu invoice ini milik
        // resto mana — juga memudahkan filter/laporan per resto.
        $this->forge->addColumn('subscription_payments', [
            'restaurant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
            'invoice_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'restaurant_id',
            ],
            'payment_method' => [
                'type'       => 'ENUM',
                'constraint' => ['Cash', 'Transfer Bank', 'QRIS'],
                'null'       => true,
                'after'      => 'amount',
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'proof_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'confirmed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        // payment_date dulu wajib diisi saat insert — sekarang harus boleh
        // kosong, karena invoice baru dibuat itu belum tentu langsung dibayar.
        $this->forge->modifyColumn('subscription_payments', [
            'payment_date' => [
                'name' => 'payment_date',
                'type' => 'DATE',
                'null' => true,
            ],
        ]);

        // Status sebelumnya pending/paid/failed (belum sesuai istilah brief).
        // Diganti ke istilah yang konsisten dipakai di seluruh aplikasi.
        $this->forge->modifyColumn('subscription_payments', [
            'status' => [
                'name'       => 'status',
                'type'       => 'ENUM',
                'constraint' => ['Belum Dibayar', 'Menunggu Konfirmasi', 'Lunas', 'Terlambat', 'Ditolak'],
                'default'    => 'Belum Dibayar',
            ],
        ]);

        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('confirmed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->processIndexes('subscription_payments');
    }

    public function down()
    {
        $this->forge->dropForeignKey('subscription_payments', 'subscription_payments_restaurant_id_foreign');
        $this->forge->dropForeignKey('subscription_payments', 'subscription_payments_confirmed_by_foreign');

        $this->forge->dropColumn('subscription_payments', [
            'restaurant_id', 'invoice_number', 'payment_method',
            'due_date', 'proof_image', 'confirmed_by', 'confirmed_at', 'notes',
        ]);

        $this->forge->modifyColumn('subscription_payments', [
            'payment_date' => [
                'name' => 'payment_date',
                'type' => 'DATE',
                'null' => false,
            ],
            'status' => [
                'name'       => 'status',
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed'],
                'default'    => 'pending',
            ],
        ]);
    }
}