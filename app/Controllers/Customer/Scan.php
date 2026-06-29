<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\RestaurantTable;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\MenuCategory;

class Scan extends BaseController
{
    public function index($token = null)
    {
        $tableModel      = new RestaurantTable();
        $restaurantModel = new Restaurant();

        $table = $tableModel->where('qr_token', $token)->where('is_active', 1)->first();

        if (!$table) {
            return view('customer/scan_invalid');
        }

        $restaurant = $restaurantModel->where('id', $table['restaurant_id'])->where('is_active', 1)->first();

        if (!$restaurant) {
            return view('customer/scan_invalid');
        }

        // Store in session for ordering flow
        session()->set([
            'scan_restaurant_id' => $restaurant['id'],
            'scan_table_id'      => $table['id'],
            'scan_table_number'  => $table['table_number'],
            'scan_area_name'     => $table['area_name'],
            'scan_restaurant'    => $restaurant['name'],
        ]);

        return redirect()->to('/menu/' . $table['id']);
    }
}
