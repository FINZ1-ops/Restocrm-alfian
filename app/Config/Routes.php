<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// -- Route publik (tanpa login) ------------------------------------------------------
$routes->get('/', 'Home::index');
$routes->match(['get', 'post'], 'auth/login', 'Auth::login');
$routes->match(['get', 'post'], 'auth/register', 'Auth::register');
$routes->get('auth/token', 'Auth::getToken');
$routes->get('auth/logout', 'Auth::logout');

// Pelanggan scan QR & pesan menu (tanpa akun)
$routes->get('scan/(:segment)', 'Customer\Scan::index/$1');
$routes->get('menu/(:num)', 'Customer\Menu::index/$1');
$routes->post('order/add-item', 'Customer\Order::addItem');
$routes->get('order/checkout/(:num)', 'Customer\Order::checkout/$1');
$routes->post('order/process', 'Customer\Order::process');
$routes->get('order/success/(:segment)', 'Customer\Order::success/$1');

// -- Route terproteksi: wajib login (AuthFilter) --------------------------------------
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Gateway redirect sesuai role
    $routes->get('dashboard', 'Dashboard::index');

    // Halaman Settings — placeholder sederhana, dapat diakses semua role
    // yang sudah login (dipanggil dari dropdown menu profil di Layout)
    $routes->get('settings', 'Settings::index');

    // Customer Dashboard
    $routes->group('customer', ['filter' => 'role:customer'], function($routes) {
        $routes->get('dashboard', 'Customer\Dashboard::index');
        $routes->get('profile', 'Customer\Dashboard::profile');
        $routes->get('orders', 'Customer\Dashboard::orders');
        $routes->get('rewards', 'Customer\Dashboard::rewards');
        $routes->get('scan', 'Customer\Dashboard::scanPage');
    });

    // Super Admin  filter tambahan: hanya role super_admin
    $routes->group('admin', ['filter' => 'role:super_admin'], function($routes) {
        $routes->get('dashboard', 'Admin\Dashboard::index');
        
        // Leads CRM
        $routes->resource('leads', ['controller' => 'Admin\Leads']);
        $routes->post('leads/(:num)/followup', 'Admin\Leads::addFollowup/$1');

        //Convert leads
        $routes->get('leads/(:num)/convert', 'Admin\Restaurants::convertLead/$1');
        $routes->post('leads/(:num)/convert', 'Admin\Restaurants::doConvert/$1');

        // Subscription Plans
        $routes->resource('plans', ['controller' => 'Admin\SubscriptionPlans']);

        // Restaurants
        $routes->resource('restaurants', ['controller' => 'Admin\Restaurants']);
        $routes->post('restaurants/(:num)/convert', 'Admin\Restaurants::convertLead/$1');
    });

    // Admin restoran (pemilik outlet)
    $routes->group('resto', ['filter' => 'role:admin_resto'], function($routes) {
        // Restaurant Profile
        $routes->match(['get', 'post'], 'profile', 'RestaurantAdmin\Profile::edit');

        // Menu Management
        $routes->resource('categories', ['controller' => 'RestaurantAdmin\MenuCategories']);
        $routes->resource('menus', ['controller' => 'RestaurantAdmin\Menus']);

        // Staff Management
        $routes->resource('staff', ['controller' => 'RestaurantAdmin\Staff']);

        // Table Management
        $routes->get('tables/(:num)/qr', 'RestaurantAdmin\Tables::generateQr/$1');
        $routes->resource('tables', ['controller' => 'RestaurantAdmin\Tables']);

        // Dashboard & Reports
        $routes->get('dashboard', 'RestaurantAdmin\Dashboard::index');
        $routes->get('reports', 'RestaurantAdmin\Reports::index');
        $routes->get('reports/daily', 'RestaurantAdmin\Reports::daily');
        $routes->get('reports/monthly', 'RestaurantAdmin\Reports::monthly');

        // Orders
        $routes->get('orders', 'RestaurantAdmin\Orders::index');
        $routes->get('orders/(:num)', 'RestaurantAdmin\Orders::view/$1');

        // Customers
        $routes->get('customers', 'RestaurantAdmin\Customers::index');
        $routes->get('customers/(:num)', 'RestaurantAdmin\Customers::view/$1');
    });

    // Dapur  antrian masak
    $routes->group('kitchen', ['filter' => 'role:dapur'], function($routes) {
        $routes->get('orders', 'Kitchen\Orders::index');
        $routes->post('orders/(:num)/status', 'Kitchen\Orders::updateStatus/$1');
    });

    // Kasir  konfirmasi pembayaran
    $routes->group('cashier', ['filter' => 'role:kasir'], function($routes) {
        $routes->get('orders', 'Cashier\Orders::index');
        $routes->get('orders/(:num)', 'Cashier\Orders::view/$1');
        $routes->post('orders/(:num)/confirm-cash', 'Cashier\Orders::confirmCash/$1');
        $routes->post('orders/(:num)/verify-qris', 'Cashier\Orders::verifyQris/$1');
    });

    // Sales  leads & pipeline
   $routes->group('sales', ['filter' => 'role:sales'], function($routes) {
        $routes->get('dashboard', 'Sales\Dashboard::index');         // ← TAMBAH INI
        $routes->resource('leads', ['controller' => 'Sales\Leads']);
        $routes->post('leads/(:num)/followup', 'Sales\Leads::addFollowup/$1');
        $routes->get('pipeline', 'Sales\Pipeline::index');
    });

});