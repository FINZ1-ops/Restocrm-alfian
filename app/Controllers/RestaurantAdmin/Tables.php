<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\RestaurantTable;

class Tables extends BaseController
{
    protected RestaurantTable $tableModel;

    public function __construct()
    {
        $this->tableModel = new RestaurantTable();
    }

    public function index()
    {
        $restaurantId = session('restaurant_id');
        $tables = $this->tableModel
            ->where('restaurant_id', $restaurantId)
            ->orderBy('table_number', 'ASC')
            ->findAll();

        $content = view('resto/tables/index', ['tables' => $tables]);
        return view('layouts/Layout', ['title' => 'Meja & QR Code', 'content' => $content]);
    }

    public function new()
    {
        $content = view('resto/tables/form', ['table' => null]);
        return view('layouts/Layout', ['title' => 'Tambah Meja', 'content' => $content]);
    }

    public function create()
    {
        $restaurantId = session('restaurant_id');
        $rules = [
            'table_number' => 'required|integer',
            'area_name'    => 'required|min_length[1]|max_length[100]',
            'capacity'     => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            $content = view('resto/tables/form', [
                'table'  => null,
                'errors' => $this->validator->getErrors(),
                'old'    => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Meja', 'content' => $content]);
        }

        $this->tableModel->insert([
            'restaurant_id' => $restaurantId,
            'table_number'  => $this->request->getPost('table_number'),
            'area_name'     => $this->request->getPost('area_name'),
            'capacity'      => $this->request->getPost('capacity'),
            'qr_token'      => bin2hex(random_bytes(16)),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/resto/tables')->with('success', 'Meja berhasil ditambahkan');
    }

    public function show($id = null)
    {
        return redirect()->to('/resto/tables/' . $id . '/edit');
    }

    public function edit($id = null)
    {
        $restaurantId = session('restaurant_id');
        $table = $this->tableModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$table) {
            return redirect()->to('/resto/tables')->with('error', 'Meja tidak ditemukan');
        }
        $content = view('resto/tables/form', ['table' => $table]);
        return view('layouts/Layout', ['title' => 'Edit Meja', 'content' => $content]);
    }

    public function update($id = null)
    {
        $restaurantId = session('restaurant_id');
        $table = $this->tableModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$table) {
            return redirect()->to('/resto/tables')->with('error', 'Meja tidak ditemukan');
        }

        $rules = [
            'table_number' => 'required|integer',
            'area_name'    => 'required|min_length[1]|max_length[100]',
            'capacity'     => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            $content = view('resto/tables/form', [
                'table'  => $table,
                'errors' => $this->validator->getErrors(),
            ]);
            return view('layouts/Layout', ['title' => 'Edit Meja', 'content' => $content]);
        }

        $this->tableModel->update($id, [
            'table_number' => $this->request->getPost('table_number'),
            'area_name'    => $this->request->getPost('area_name'),
            'capacity'     => $this->request->getPost('capacity'),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/resto/tables')->with('success', 'Meja berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $restaurantId = session('restaurant_id');
        $table = $this->tableModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$table) {
            return redirect()->to('/resto/tables')->with('error', 'Meja tidak ditemukan');
        }
        $this->tableModel->delete($id);
        return redirect()->to('/resto/tables')->with('success', 'Meja berhasil dihapus');
    }

    public function generateQr($id = null)
    {
        $restaurantId = session('restaurant_id');
        $table = $this->tableModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$table) {
            return redirect()->to('/resto/tables')->with('error', 'Meja tidak ditemukan');
        }

        $content = view('resto/tables/qr', ['table' => $table]);
        return view('layouts/Layout', ['title' => 'QR Code Meja ' . $table['table_number'], 'content' => $content]);
    }
}
