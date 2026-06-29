<?php

namespace App\Database\Seeds;

class InitialSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
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

        // Create subscription plans
        $plansData = [
            [
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
        $this->db->table('subscription_plans')->insertBatch($plansData);

        // Create demo restaurant
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

        // Create trial subscription for demo restaurant
        $subscriptionData = [
            'restaurant_id' => $restaurantId,
            'plan_id' => 1, // Basic plan
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
            [
                'restaurant_id' => $restaurantId,
                'name' => 'Makanan Berat',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'restaurant_id' => $restaurantId,
                'name' => 'Minuman',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'restaurant_id' => $restaurantId,
                'name' => 'Dessert',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('menu_categories')->insertBatch($categoriesData);

        // Get category IDs
        $categories = $this->db->table('menu_categories')->where('restaurant_id', $restaurantId)->get()->getResult();

        // Create demo menus
        $menusData = [
            [
                'restaurant_id' => $restaurantId,
                'category_id' => $categories[0]->id,
                'name' => 'Nasi Goreng Istimewa',
                'description' => 'Nasi goreng dengan telur, udang, dan sayuran pilihan',
                'price' => 45000.00,
                'image' => null,
                'label' => 'best_seller',
                'is_available' => true,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'restaurant_id' => $restaurantId,
                'category_id' => $categories[0]->id,
                'name' => 'Soto Ayam',
                'description' => 'Soto ayam tradisional dengan kuning telur',
                'price' => 35000.00,
                'image' => null,
                'label' => 'biasa',
                'is_available' => true,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'restaurant_id' => $restaurantId,
                'category_id' => $categories[1]->id,
                'name' => 'Es Teh Manis',
                'description' => 'Es teh manis segar',
                'price' => 8000.00,
                'image' => null,
                'label' => 'biasa',
                'is_available' => true,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'restaurant_id' => $restaurantId,
                'category_id' => $categories[2]->id,
                'name' => 'Es Cendol',
                'description' => 'Es cendol dengan santan dan gula merah',
                'price' => 12000.00,
                'image' => null,
                'label' => 'promo',
                'is_available' => true,
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
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

        echo "Data seeded successfully!";
    }
}
