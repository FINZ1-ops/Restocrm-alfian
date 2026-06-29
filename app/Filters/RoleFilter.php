<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter pembatasan role (hak akses).
 * Contoh di Routes: ['filter' => 'role:super_admin']
 */
class RoleFilter implements FilterInterface
{
    /**
     * Memastikan user login DAN rolenya termasuk daftar yang diizinkan di route.
     *
     * @param array|null $arguments Daftar role dari route, mis. ['super_admin'] atau ['admin_resto']
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Lapisan keamanan tambahan (AuthFilter biasanya sudah jalan sebelumnya)
        if (!$session->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Route tanpa argumen role → tidak perlu cek role
        if (empty($arguments)) {
            return null;
        }

        $userRole     = $session->get('role');
        $allowedRoles = $arguments;

        // Role tidak cocok → arahkan ke gateway dashboard (akan redirect lagi sesuai role)
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
