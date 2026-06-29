<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\RestaurantTable;
use App\Models\Restaurant;
use App\Models\MenuCategory;
use App\Models\Menu as MenuModel;

class Menu extends BaseController
{
    protected $tableModel;
    protected $restaurantModel;
    protected $categoryModel;
    protected $menuModel;

    public function __construct()
    {
        $this->tableModel = new RestaurantTable();
        $this->restaurantModel = new Restaurant();
        $this->categoryModel = new MenuCategory();
        $this->menuModel = new MenuModel();
    }

    public function index($tableId = null)
    {
        $table = $this->tableModel->find($tableId);
        if (!$table || !$table['is_active']) {
            return view('customer/scan_invalid');
        }

        $restaurant = $this->restaurantModel
            ->where('id', $table['restaurant_id'])
            ->where('is_active', 1)
            ->first();


        // Refresh session
        session()->set([
            'scan_restaurant_id' => $restaurant['id'],
            'scan_table_id' => $table['id'],
            'scan_table_number' => $table['table_number'],
            'scan_area_name' => $table['area_name'],
            'scan_restaurant' => $restaurant['name'],
        ]);

        $categories = $this->categoryModel
          ->where('restaurant_id', $restaurant['id'])
          ->where('is_active', 1)
          ->orderBy('sort_order', 'ASC')
          ->findAll();

        $menus = $this->menuModel
            ->where('restaurant_id', $restaurant['id'])
            ->where('is_active', 1)
            ->where('is_available', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

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
