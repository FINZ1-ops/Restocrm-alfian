<?php

namespace App\Database\Seeds;

class InitialSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        // 1. SEED SUPER ADMIN
        if ($this->db->table('users')->where('email', 'admin@restocrm.local')->countAllResults() == 0) {
            $usersData = [
                [
                    'email' => 'admin@restocrm.local',
                    'password' => password_hash('admin123', PASSWORD_BCRYPT),
                    'name' => 'Super Admin',
                    'role' => 'super_admin',
                    'restaurant_id' => null,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            ];
            $this->db->table('users')->insertBatch($usersData);
            echo "Super Admin seeded.\n";
        }

        // 2. SEED SALES
        if ($this->db->table('users')->where('email', 'sales@restocrm.local')->countAllResults() == 0) {
            $salessData = [
                [
                    'email' => 'sales@restocrm.local',
                    'password' => password_hash('sales123', PASSWORD_BCRYPT),
                    'name' => 'Sales',
                    'role' => 'sales',
                    'restaurant_id' => null,
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            ];
            $this->db->table('users')->insertBatch($salessData);
            echo "Sales seeded.\n";
        }

        // 3. SEED SUBSCRIPTION PLANS (Gunakan replace agar jika sudah ada, ditimpa saja)
        $plansData = [
            [
                'id' => 1,
                'name' => 'Basic',
                'description' => '10 meja, 50 menu',
                'max_tables' => 10,
                'max_menus' => 50,
                'has_crm' => false,
                'price_monthly' => 99000.00,
                'price_yearly' => 999000.00,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'name' => 'Pro',
                'description' => 'Unlimited meja, unlimited menu, CRM pelanggan',
                'max_tables' => 999,
                'max_menus' => 999,
                'has_crm' => true,
                'price_monthly' => 299000.00,
                'price_yearly' => 2999000.00,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'name' => 'Premium',
                'description' => 'Semua fitur',
                'max_tables' => 999,
                'max_menus' => 999,
                'has_crm' => true,
                'price_monthly' => 499000.00,
                'price_yearly' => 4999000.00,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        foreach ($plansData as $plan) {
            $this->db->table('subscription_plans')->replace($plan);
        }
        echo "Subscription plans secured.\n";


        // 4. SEED DEMO RESTAURANT & TURUNANNYA
        $existingRepo = $this->db->table('restaurants')->where('slug', 'warung-mak-enak')->get()->getRow();
        
        if (!$existingRepo) {
            // Jika restoran belum ada, buat baru beserta menu dan mejanya
            $restaurantData = [
                'name' => 'Warung Mak Enak',
                'slug' => 'warung-mak-enak',
                'address' => 'Jalan Merdeka No. 123, Jakarta',
                'whatsapp' => '081234567890',
                'logo' => null,
                'description' => 'Warung tradisional dengan menu nusantara terbaik',
                'opening_hours' => '10:00 - 22:00',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('restaurants')->insert($restaurantData);
            $restaurantId = $this->db->insertID();

            // Create admin user for demo restaurant
            $adminData = [
                'email' => 'admin@warung-mak-enak.local',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'name' => 'Admin Warung Mak Enak',
                'role' => 'admin_resto',
                'restaurant_id' => $restaurantId,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('users')->insert($adminData);

            // Create trial subscription
            $subscriptionData = [
                'restaurant_id' => $restaurantId,
                'plan_id' => 1,
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+30 days')),
                'status' => 'Trial',
                'billing_cycle' => 'monthly',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->table('restaurant_subscriptions')->insert($subscriptionData);

            // Create menu categories
            $categoriesData = [
                ['restaurant_id' => $restaurantId, 'name' => 'Makanan Berat', 'sort_order' => 1, 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['restaurant_id' => $restaurantId, 'name' => 'Minuman', 'sort_order' => 2, 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['restaurant_id' => $restaurantId, 'name' => 'Dessert', 'sort_order' => 3, 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
            ];
            $this->db->table('menu_categories')->insertBatch($categoriesData);
            $categories = $this->db->table('menu_categories')->where('restaurant_id', $restaurantId)->get()->getResult();

            // Create demo menus
            $menusData = [
                ['restaurant_id' => $restaurantId, 'category_id' => $categories[0]->id, 'name' => 'Nasi Goreng Istimewa', 'description' => 'Nasi goreng...', 'price' => 45000.00, 'image' => null, 'label' => 'best_seller', 'is_available' => true, 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['restaurant_id' => $restaurantId, 'category_id' => $categories[0]->id, 'name' => 'Soto Ayam', 'description' => 'Soto...', 'price' => 35000.00, 'image' => null, 'label' => 'biasa', 'is_available' => true, 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['restaurant_id' => $restaurantId, 'category_id' => $categories[1]->id, 'name' => 'Es Teh Manis', 'description' => 'Es...', 'price' => 8000.00, 'image' => null, 'label' => 'biasa', 'is_available' => true, 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['restaurant_id' => $restaurantId, 'category_id' => $categories[2]->id, 'name' => 'Es Cendol', 'description' => 'Es...', 'price' => 12000.00, 'image' => null, 'label' => 'promo', 'is_available' => true, 'is_active' => true, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
            ];
            $this->db->table('menus')->insertBatch($menusData);

            // Create tables
            $tablesData = [];
            $areas = ['Area A', 'Area A', 'Area B', 'Area B'];
            for ($i = 1; $i <= 4; $i++) {
                $tablesData[] = [
                    'restaurant_id' => $restaurantId,
                    'table_number' => $i,
                    'area_name' => $areas[$i - 1],
                    'capacity' => 4,
                    'qr_token' => bin2hex(random_bytes(16)),
                    'is_active' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            $this->db->table('restaurant_tables')->insertBatch($tablesData);
            echo "Demo Restaurant and its relations seeded successfully!\n";
        } else {
            echo "Demo Restaurant already exists — skipping relations.\n";
        }

        echo "All seeds processed completed!\n";
    }
}