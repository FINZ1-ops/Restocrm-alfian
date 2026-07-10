<?php

namespace App\Controllers;

/**
 * Home Controller — Halaman utama RESTOCRM.
 *
 * Ini adalah "pintu masuk" pertama bagi customer non-akun yang mau
 * langsung scan QR meja dan pesan makanan tanpa perlu daftar/login.
 * User yang sudah login akan diarahkan ke dashboard sesuai role-nya.
 */
class Home extends BaseController
{
    public function index()
    {
        // Kalau sudah login, langsung redirect ke dashboard masing-masing
        if (session()->get('is_logged_in')) {
            $routes = [
                'super_admin' => base_url('admin/dashboard'),
                'admin_resto' => base_url('resto/dashboard'),
                'dapur'       => base_url('kitchen/orders'),
                'kasir'       => base_url('cashier/orders'),
                'sales'       => base_url('sales/dashboard'),
                'customer'    => base_url('akun/dashboard'),
            ];
            $role = session()->get('role');
            if (isset($routes[$role])) {
                return redirect()->to($routes[$role]);
            }
        }

        return view('home');
    }
}