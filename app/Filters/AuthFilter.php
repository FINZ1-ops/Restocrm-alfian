<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter autentikasi global.
 * Dipasang di Routes.php pada grup route yang membutuhkan login.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Dijalankan SEBELUM controller — cek apakah user sudah login.
     * Mengembalikan redirect jika session `is_logged_in` tidak ada.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        // null = lanjutkan ke controller
        return null;
    }

    /**
     * Dijalankan SETELAH controller — tidak dipakai di filter ini.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
