<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\MenuCategory;

class MenuCategories extends BaseController
{
    protected MenuCategory $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new MenuCategory();
    }

    public function index()
    {
        $restaurantId = session('restaurant_id');
        $categories   = $this->categoryModel
            ->where('restaurant_id', $restaurantId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $content = view('resto/categories/index', ['categories' => $categories]);
        return view('layouts/Layout', ['title' => 'Kategori Menu', 'content' => $content]);
    }

    public function new()
    {
        $content = view('resto/categories/form', ['category' => null]);
        return view('layouts/Layout', ['title' => 'Tambah Kategori', 'content' => $content]);
    }

    public function create()
    {
        $rules = ['name' => 'required|min_length[2]|max_length[100]'];

        if (!$this->validate($rules)) {
            $content = view('resto/categories/form', [
                'category' => null,
                'errors'   => $this->validator->getErrors(),
                'old'      => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Kategori', 'content' => $content]);
        }

        $restaurantId = session('restaurant_id');
        $maxOrder = $this->categoryModel->where('restaurant_id', $restaurantId)->selectMax('sort_order')->first();

        $this->categoryModel->insert([
            'restaurant_id' => $restaurantId,
            'name'          => $this->request->getPost('name'),
            'sort_order'    => ($maxOrder['sort_order'] ?? 0) + 1,
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/resto/categories')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function show($id = null)
    {
        return redirect()->to('/resto/categories/' . $id . '/edit');
    }

    public function edit($id = null)
    {
        $restaurantId = session('restaurant_id');
        $category = $this->categoryModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$category) {
            return redirect()->to('/resto/categories')->with('error', 'Kategori tidak ditemukan');
        }
        $content = view('resto/categories/form', ['category' => $category]);
        return view('layouts/Layout', ['title' => 'Edit Kategori', 'content' => $content]);
    }

    public function update($id = null)
    {
        $restaurantId = session('restaurant_id');
        $category = $this->categoryModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$category) {
            return redirect()->to('/resto/categories')->with('error', 'Kategori tidak ditemukan');
        }

        $rules = ['name' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            $content = view('resto/categories/form', [
                'category' => $category,
                'errors'   => $this->validator->getErrors(),
            ]);
            return view('layouts/Layout', ['title' => 'Edit Kategori', 'content' => $content]);
        }

        $this->categoryModel->update($id, [
            'name'       => $this->request->getPost('name'),
            'sort_order' => $this->request->getPost('sort_order') ?: $category['sort_order'],
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/resto/categories')->with('success', 'Kategori berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $restaurantId = session('restaurant_id');
        $category = $this->categoryModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$category) {
            return redirect()->to('/resto/categories')->with('error', 'Kategori tidak ditemukan');
        }
        $this->categoryModel->delete($id);
        return redirect()->to('/resto/categories')->with('success', 'Kategori berhasil dihapus');
    }
}
