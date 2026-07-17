<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| These routes are accessible without authentication.
*/
$routes->get('/', 'Home::index');
$routes->match(['get', 'post'], 'auth/login', 'Auth::login');
$routes->match(['get', 'post'], 'auth/register', 'Auth::register');
$routes->get('auth/logout', 'Auth::logout');

/*
|--------------------------------------------------------------------------
| Customer QR Ordering Routes (No Authentication Required)
|--------------------------------------------------------------------------
| Handling the flow from scanning a QR code to processing an order.
*/
$routes->get('scan/(:segment)', 'Customer\Scan::index/$1');
$routes->get('menu/(:num)', 'Customer\Menu::index/$1');
$routes->post('order/add-item', 'Customer\Order::addItem');
$routes->get('order/checkout/(:num)', 'Customer\Order::checkout/$1');
$routes->post('order/process', 'Customer\Order::process');
$routes->get('order/success/(:segment)', 'Customer\Order::success/$1');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| All routes within this group require a valid user session.
*/
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // Default dashboard gateway based on user role
    $routes->get('dashboard', 'Dashboard::index');

    // General user settings
    $routes->get('settings', 'Settings::index');

    /*
    |--------------------------------------------------------------------------
    | Member Area (Role: Customer)
    |--------------------------------------------------------------------------
    */
    $routes->group('akun', ['filter' => 'role:customer'], function($routes) {
        $routes->get('dashboard', 'Customer\Dashboard::index');
        $routes->get('profile', 'Customer\Dashboard::profile');
        $routes->get('orders', 'Customer\Dashboard::orders');
        $routes->get('rewards', 'Customer\Dashboard::rewards');
        $routes->get('scan', 'Customer\Dashboard::scanPage');
    });

    /*
    |--------------------------------------------------------------------------
    | Super Admin Area (Role: Super Admin)
    |--------------------------------------------------------------------------
    */
    $routes->group('admin', ['filter' => 'role:super_admin'], function($routes) {
        $routes->get('dashboard', 'Admin\Dashboard::index');
        
        // Lead Management (CRM)
        $routes->resource('leads', ['controller' => 'Admin\Leads']);
        $routes->post('leads/(:num)/followup', 'Admin\Leads::addFollowup/$1');

        // Lead Conversion to Restaurant
        $routes->get('leads/(:num)/convert', 'Admin\Restaurants::convertLead/$1');
        $routes->post('leads/(:num)/convert', 'Admin\Restaurants::doConvert/$1');

        // Subscription Plans Management
        $routes->resource('plans', ['controller' => 'Admin\SubscriptionPlans']);
        $routes->post('plans/(:num)', 'Admin\SubscriptionPlans::update/$1');
        $routes->post('plans/(:num)/delete', 'Admin\SubscriptionPlans::delete/$1');

        // Subscription Payments
        $routes->get('subscription-payments', 'Admin\SubscriptionPayments::index');
        $routes->get('subscription-payments/overdue', 'Admin\SubscriptionPayments::overdue');
        $routes->get('subscription-payments/new', 'Admin\SubscriptionPayments::new');
        $routes->post('subscription-payments', 'Admin\SubscriptionPayments::create');
        $routes->get('subscription-payments/(:num)', 'Admin\SubscriptionPayments::view/$1');
        $routes->post('subscription-payments/(:num)/upload-proof', 'Admin\SubscriptionPayments::uploadProof/$1');
        $routes->post('subscription-payments/(:num)/confirm', 'Admin\SubscriptionPayments::confirm/$1');
        $routes->post('subscription-payments/(:num)/reject', 'Admin\SubscriptionPayments::reject/$1');

        // Restaurant Management
        $routes->resource('restaurants', ['controller' => 'Admin\Restaurants']);
        $routes->post('restaurants/(:num)/convert', 'Admin\Restaurants::convertLead/$1');
    });

    /*
    |--------------------------------------------------------------------------
    | Restaurant Admin Area (Role: Admin Resto)
    |--------------------------------------------------------------------------
    */
    $routes->group('resto', ['filter' => 'role:admin_resto'], function($routes) {
        // Core Management
        $routes->match(['get', 'post'], 'profile', 'RestaurantAdmin\Profile::edit');
        $routes->get('subscriptions', 'RestaurantAdmin\Subscriptions::index');
        $routes->get('subscriptions/new', 'RestaurantAdmin\Subscriptions::new');
        $routes->post('subscriptions', 'RestaurantAdmin\Subscriptions::create');
        $routes->resource('categories', ['controller' => 'RestaurantAdmin\MenuCategories']);
        $routes->resource('menus', ['controller' => 'RestaurantAdmin\Menus']);
        $routes->resource('staff', ['controller' => 'RestaurantAdmin\Staff']);

        // Table Configuration
        $routes->get('tables/(:num)/qr', 'RestaurantAdmin\Tables::generateQr/$1');
        $routes->resource('tables', ['controller' => 'RestaurantAdmin\Tables']);

        // Analytics and Reports
        $routes->get('dashboard', 'RestaurantAdmin\Dashboard::index');
        $routes->get('reports', 'RestaurantAdmin\Reports::index');
        $routes->get('reports/daily', 'RestaurantAdmin\Reports::daily');
        $routes->get('reports/monthly', 'RestaurantAdmin\Reports::monthly');

        // Order Tracking
        $routes->get('orders', 'RestaurantAdmin\Orders::index');
        $routes->get('orders/(:num)', 'RestaurantAdmin\Orders::view/$1');

        // Customer Base
        $routes->get('customers', 'RestaurantAdmin\Customers::index');
        $routes->get('customers/(:num)', 'RestaurantAdmin\Customers::view/$1');
    });

    /*
    |--------------------------------------------------------------------------
    | Kitchen Operations (Role: Dapur)
    |--------------------------------------------------------------------------
    */
    $routes->group('kitchen', ['filter' => 'role:dapur'], function($routes) {
        $routes->get('orders', 'Kitchen\Orders::index');
        $routes->post('orders/(:num)/status', 'Kitchen\Orders::updateStatus/$1');
    });

    /*
    |--------------------------------------------------------------------------
    | Cashier Operations (Role: Kasir)
    |--------------------------------------------------------------------------
    */
    $routes->group('cashier', ['filter' => 'role:kasir'], function($routes) {
        $routes->get('orders', 'Cashier\Orders::index');
        $routes->get('orders/(:num)', 'Cashier\Orders::view/$1');
        $routes->post('orders/(:num)/confirm-cash', 'Cashier\Orders::confirmCash/$1');
        $routes->post('orders/(:num)/verify-qris', 'Cashier\Orders::verifyQris/$1');
    });

    /*
    |--------------------------------------------------------------------------
    | Sales CRM (Role: Sales)
    |--------------------------------------------------------------------------
    */
    $routes->group('sales', ['filter' => 'role:sales'], function($routes) {
        $routes->get('dashboard', 'Sales\Dashboard::index');         
        $routes->resource('leads', ['controller' => 'Sales\Leads']);
        $routes->post('leads/(:num)/followup', 'Sales\Leads::addFollowup/$1');
        $routes->get('pipeline', 'Sales\Pipeline::index');
    });

});