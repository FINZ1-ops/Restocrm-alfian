<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class Menu extends BaseController
{
    // Catatan: tidak ada model di-construct di sini secara sengaja.
    // Semua query memakai $db->table() langsung di method index(),
    // supaya tidak ikut kena auto-filter restaurant_id dari
    // BaseRestaurantModel (lihat komentar di index()).

    public function index($tableId = null)
    {
        // PENTING: semua query di controller ini pakai $db->table() langsung,
        // BUKAN lewat Model (RestaurantTable, MenuCategory, Menu) yang
        // extends BaseRestaurantModel. Model itu otomatis menambahkan
        // where('restaurant_id', session('restaurant_id')) di setiap
        // find()/where(), padahal Customer tidak login — kalau browser
        // ini kebetulan masih ada sisa session Admin/Kasir resto LAIN,
        // menu yang tampil bisa salah atau kosong sama sekali.
        // Sama akar masalahnya dengan bug di Scan.php & checkout().
        $db = \Config\Database::connect();

        $table = $db->table('restaurant_tables')->where('id', $tableId)->get()->getRowArray();
        if (!$table || !$table['is_active']) {
            return view('customer/scan_invalid');
        }

        $restaurant = $db->table('restaurants')
            ->where('id', $table['restaurant_id'])
            ->where('is_active', 1)
            ->get()->getRowArray();

        if (!$restaurant) {
            return view('customer/scan_invalid');
        }

        // Refresh session
        session()->set([
            'scan_restaurant_id' => $restaurant['id'],
            'scan_table_id' => $table['id'],
            'scan_table_number' => $table['table_number'],
            'scan_area_name' => $table['area_name'],
            'scan_restaurant' => $restaurant['name'],
        ]);

        $categories = $db->table('menu_categories')
          ->where('restaurant_id', $restaurant['id'])
          ->where('is_active', 1)
          ->orderBy('sort_order', 'ASC')
          ->get()->getResultArray();

        $menus = $db->table('menus')
            ->where('restaurant_id', $restaurant['id'])
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        // Group menus by category
        $menusByCategory = [];
        foreach ($menus as $menu) {
            $menusByCategory[$menu['category_id']][] = $menu;
        }

        // Cart from session
        $cart = session()->get('cart_' . $table['id']) ?? [];

        return view('customer/menu', [
            'restaurant' => $restaurant,
            'table' => $table,
            'categories' => $categories,
            'menusByCategory' => $menusByCategory,
            'cart' => $cart,
        ]);
    }
}