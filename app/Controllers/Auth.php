<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Restaurant;

/**
 * Controller autentikasi: login, registrasi customer, logout.
 * Data login disimpan di session PHP (lihat app/Config/Session.php).
 */
class Auth extends BaseController
{
    protected User $userModel;
    protected Restaurant $restaurantModel;

    public function __construct()
    {
        $this->userModel       = new User();
        $this->restaurantModel = new Restaurant();
    }

    /**
     * GET  → tampilkan form login
     * POST → validasi kredensial, isi session, redirect per role
     */
    public function login()
    {
        // User yang sudah punya session aktif tidak perlu login lagi
        if (session()->get('is_logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        if ($this->request->is('post')) {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', [
                    'errors'   => $this->validator->getErrors(),
                    'oldEmail' => $this->request->getPost('email'),
                ]);
            }

            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $user     = $this->userModel->getUserByEmail($email);

            // Verifikasi password hash (bcrypt) dari database
            if (!$user || !password_verify($password, $user['password'])) {
                return view('auth/login', [
                    'error'    => 'Email atau password tidak sesuai. Periksa kembali.',
                    'oldEmail' => $email,
                ]);
            }

            if (!(bool)$user['is_active']) {
                return view('auth/login', [
                    'error'    => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
                    'oldEmail' => $email,
                ]);
            }

            // Data inti yang dipakai filter auth/role dan tampilan layout
            $sessionData = [
                'user_id'             => $user['id'],
                'email'               => $user['email'],
                'name'                => $user['name'],
                'role'                => $user['role'],
                'restaurant_id'       => $user['restaurant_id'] ?? null,
                'is_logged_in'        => true,
                // Flag eksplisit yang membedakan sesi Customer dari Staff —
                // Customer tidak punya restaurant_id, dan memang tidak boleh
                // punya akses ke data per-restoran tertentu. Flag ini dipakai
                // untuk sanitasi data di BaseRestaurantModel dan tempat lain.
                'is_customer_account' => ($user['role'] === 'customer'),
            ];

            // Staff restoran: simpan nama restoran untuk sidebar/header
            if (!empty($user['restaurant_id'])) {
                $restaurant = $this->restaurantModel->find($user['restaurant_id']);
                if ($restaurant) {
                    $sessionData['restaurant_name'] = $restaurant['name'];
                }
            }

            session()->set($sessionData);

            // Regenerasi ID session setelah login (keamanan) tanpa menghapus data (false)
            // session()->regenerate(false);

            return $this->redirectByRole($user['role'], 'Selamat datang, ' . $user['name'] . '!');
        }

        return view('auth/login');
    }

    /**
     * Registrasi akun customer baru (role default: customer).
     */
    public function register()
    {
        if (session()->get('is_logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        if ($this->request->is('post')) {
            $rules = [
                'name'             => 'required|min_length[3]|max_length[255]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[8]|max_length[255]',
                'confirm_password' => 'required|matches[password]',
            ];

            $messages = [
                'email'            => ['is_unique'   => 'Email ini sudah terdaftar. Gunakan email lain.'],
                'confirm_password' => ['matches'     => 'Konfirmasi password tidak cocok.'],
                'password'         => ['min_length'  => 'Password minimal 8 karakter.'],
            ];

            if (!$this->validate($rules, $messages)) {
                return view('auth/register', [
                    'errors'   => $this->validator->getErrors(),
                    'oldName'  => $this->request->getPost('name'),
                    'oldEmail' => $this->request->getPost('email'),
                ]);
            }

            $userData = [
                'name'      => $this->request->getPost('name'),
                'email'     => $this->request->getPost('email'),
                'password'  => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                'role'      => 'customer',
                'is_active' => 1,
            ];

            if ($this->userModel->insert($userData)) {
                return redirect()->to(base_url('auth/login'))
                    ->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
            }

            return view('auth/register', [
                'error'    => 'Gagal membuat akun. Silakan coba lagi.',
                'oldName'  => $this->request->getPost('name'),
                'oldEmail' => $this->request->getPost('email'),
            ]);
        }

        return view('auth/register');
    }

    public function getToken()
    {
        return $this->response->setJSON([
            'tokenName' => csrf_token(),
            'tokenHash' => csrf_hash(),
        ]);
    }

    /**
     * Hancurkan session dan kembali ke halaman login.
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'))
            ->with('success', 'Anda telah berhasil logout. Sampai jumpa!');
    }

    /**
     * Mengarahkan user ke modul utama berdasarkan role.
     *
     * @param string $role          Nilai kolom users.role
     * @param string $flashMessage  Pesan sukses opsional (flashdata)
     */
    private function redirectByRole(string $role, string $flashMessage = ''): \CodeIgniter\HTTP\RedirectResponse
    {
        $routes = [
            'super_admin' => base_url('admin/dashboard'),
            'admin_resto' => base_url('resto/dashboard'),
            'dapur'       => base_url('kitchen/orders'),
            'kasir'       => base_url('cashier/orders'),
            'sales'       => base_url('sales/dashboard'),
            'customer'    => base_url('akun/dashboard'),
        ];

        $target   = $routes[$role] ?? base_url('dashboard');
        $redirect = redirect()->to($target);

        if (!empty($flashMessage)) {
            $redirect = $redirect->with('success', $flashMessage);
        }

        return $redirect;
    }
}