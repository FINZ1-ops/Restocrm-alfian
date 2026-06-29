<?php

namespace App\Controllers;

/**
 * Gateway dashboard — tidak menampilkan halaman statis,
 * hanya mengarahkan user ke modul sesuai role di session.
 */
class Dashboard extends BaseController
{
    /**
     * Entry point setelah login jika role tidak punya URL khusus di Auth::redirectByRole.
     * Juga dipakai saat user tanpa hak akses di-redirect dari RoleFilter.
     */
    public function index()
    {
        $session = session();

        // Belum login → kembali ke form login
        if (!$session->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $role = $session->get('role');

        // Peta role → halaman utama masing-masing peran
        $routes = [
            'super_admin' => base_url('admin/dashboard'),
            'admin_resto' => base_url('resto/dashboard'),
            'dapur'       => base_url('kitchen/orders'),
            'kasir'       => base_url('cashier/orders'),
            'sales'       => base_url('sales/leads'),
            'customer'    => base_url('customer/dashboard')
        ];

        if (isset($routes[$role])) {
            return redirect()->to($routes[$role]);
        }

        // Role tidak dikenali: hapus session agar tidak loop redirect
        $session->destroy();
        return redirect()->to(base_url('auth/login'))
            ->with('error', 'Role akun tidak dikenali. Hubungi administrator.');
    }
}
