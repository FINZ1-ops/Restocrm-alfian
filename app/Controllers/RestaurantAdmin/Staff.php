<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\User;

class Staff extends BaseController
{
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $restaurantId = session('restaurant_id');

        // Ambil staff (kasir & dapur) dari restoran ini
        $staffList = $this->userModel
            ->where('restaurant_id', $restaurantId)
            ->whereIn('role', ['kasir', 'dapur'])
            ->orderBy('name', 'ASC')
            ->findAll();

        $content = view('resto/staff/index', ['staffList' => $staffList]);
        return view('layouts/Layout', ['title' => 'Manajemen Staff', 'content' => $content]);
    }

    public function new()
    {
        $content = view('resto/staff/form', [
            'staff'  => null,
            'errors' => [],
            'old'    => []
        ]);
        return view('layouts/Layout', ['title' => 'Tambah Staff Baru', 'content' => $content]);
    }

    public function create()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[kasir,dapur]',
        ];

        if (!$this->validate($rules)) {
            $content = view('resto/staff/form', [
                'staff'  => null,
                'errors' => $this->validator->getErrors(),
                'old'    => $this->request->getPost()
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Staff Baru', 'content' => $content]);
        }

        $restaurantId = session('restaurant_id');

        $this->userModel->insert([
            'name'          => $this->request->getPost('name'),
            'email'         => $this->request->getPost('email'),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'          => $this->request->getPost('role'),
            'restaurant_id' => $restaurantId,
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/resto/staff')->with('success', 'Staff baru berhasil ditambahkan');
    }

    public function edit($id = null)
    {
        $restaurantId = session('restaurant_id');
        $staff = $this->userModel
            ->where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->whereIn('role', ['kasir', 'dapur'])
            ->first();

        if (!$staff) {
            return redirect()->to('/resto/staff')->with('error', 'Data staff tidak ditemukan');
        }

        $content = view('resto/staff/form', [
            'staff'  => $staff,
            'errors' => [],
            'old'    => []
        ]);
        return view('layouts/Layout', ['title' => 'Edit Staff', 'content' => $content]);
    }

    public function update($id = null)
    {
        $restaurantId = session('restaurant_id');
        $staff = $this->userModel
            ->where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->whereIn('role', ['kasir', 'dapur'])
            ->first();

        if (!$staff) {
            return redirect()->to('/resto/staff')->with('error', 'Data staff tidak ditemukan');
        }

        $rules = [
            'name'  => 'required|min_length[3]|max_length[255]',
            'role'  => 'required|in_list[kasir,dapur]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
        ];

        // Jika password diisi, maka wajib divalidasi
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            $content = view('resto/staff/form', [
                'staff'  => $staff,
                'errors' => $this->validator->getErrors(),
                'old'    => $this->request->getPost()
            ]);
            return view('layouts/Layout', ['title' => 'Edit Staff', 'content' => $content]);
        }

        $updateData = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $updateData);

        return redirect()->to('/resto/staff')->with('success', 'Data staff berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $restaurantId = session('restaurant_id');
        $staff = $this->userModel
            ->where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->whereIn('role', ['kasir', 'dapur'])
            ->first();

        if (!$staff) {
            return redirect()->to('/resto/staff')->with('error', 'Data staff tidak ditemukan');
        }

        $this->userModel->delete($id);

        return redirect()->to('/resto/staff')->with('success', 'Data staff berhasil dihapus');
    }
}
