<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\Menu;
use App\Models\MenuCategory;

class Menus extends BaseController
{
    protected Menu $menuModel;
    protected MenuCategory $categoryModel;

    public function __construct()
    {
        $this->menuModel     = new Menu();
        $this->categoryModel = new MenuCategory();
    }

    public function index()
    {
        $restaurantId = session('restaurant_id');
        $category     = $this->request->getGet('category');

        $query = $this->menuModel->where('restaurant_id', $restaurantId)->orderBy('name', 'ASC');
        if ($category) $query->where('category_id', $category);

        $menus      = $query->findAll();
        $categories = $this->categoryModel->where('restaurant_id', $restaurantId)->where('is_active', 1)->orderBy('sort_order')->findAll();

        $content = view('resto/menus/index', [
            'menus'      => $menus,
            'categories' => $categories,
            'currentCat' => $category,
        ]);
        return view('layouts/Layout', ['title' => 'Manajemen Menu', 'content' => $content]);
    }

    public function new()
    {
        $restaurantId = session('restaurant_id');
        $categories   = $this->categoryModel->where('restaurant_id', $restaurantId)->where('is_active', 1)->orderBy('sort_order')->findAll();
        $content = view('resto/menus/form', ['menu' => null, 'categories' => $categories]);
        return view('layouts/Layout', ['title' => 'Tambah Menu', 'content' => $content]);
    }

    public function create()
    {
        $restaurantId = session('restaurant_id');
        $rules = [
            'name'        => 'required|min_length[2]|max_length[255]',
            'category_id' => 'required|integer',
            'price'       => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            $categories = $this->categoryModel->where('restaurant_id', $restaurantId)->where('is_active', 1)->orderBy('sort_order')->findAll();
            $content = view('resto/menus/form', [
                'menu'       => null,
                'categories' => $categories,
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Menu', 'content' => $content]);
        }

        helper('upload');
        $image = $this->request->getFile('image');
        try {
            $imagePath = move_validated_upload($image, 'menus');
        } catch (\RuntimeException $e) {
            $categories = $this->categoryModel->where('restaurant_id', $restaurantId)->where('is_active', 1)->orderBy('sort_order')->findAll();
            $content = view('resto/menus/form', [
                'menu'       => null,
                'categories' => $categories,
                'errors'     => ['image' => $e->getMessage()],
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Menu', 'content' => $content]);
        }

        $this->menuModel->insert([
            'restaurant_id' => $restaurantId,
            'category_id'   => $this->request->getPost('category_id'),
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'price'         => $this->request->getPost('price'),
            'image'         => $imagePath,
            'label'         => $this->request->getPost('label') ?: 'biasa',
            'is_available'  => $this->request->getPost('is_available') ? 1 : 0,
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/resto/menus')->with('success', 'Menu berhasil ditambahkan');
    }

    public function show($id = null)
    {
        return redirect()->to('/resto/menus/' . $id . '/edit');
    }

    public function edit($id = null)
    {
        $restaurantId = session('restaurant_id');
        $menu = $this->menuModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$menu) {
            return redirect()->to('/resto/menus')->with('error', 'Menu tidak ditemukan');
        }
        $categories = $this->categoryModel->where('restaurant_id', $restaurantId)->where('is_active', 1)->orderBy('sort_order')->findAll();
        $content = view('resto/menus/form', ['menu' => $menu, 'categories' => $categories]);
        return view('layouts/Layout', ['title' => 'Edit Menu', 'content' => $content]);
    }

    public function update($id = null)
    {
        $restaurantId = session('restaurant_id');
        $menu = $this->menuModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$menu) {
            return redirect()->to('/resto/menus')->with('error', 'Menu tidak ditemukan');
        }

        $rules = [
            'name'        => 'required|min_length[2]|max_length[255]',
            'category_id' => 'required|integer',
            'price'       => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            $categories = $this->categoryModel->where('restaurant_id', $restaurantId)->where('is_active', 1)->orderBy('sort_order')->findAll();
            $content = view('resto/menus/form', [
                'menu'       => $menu,
                'categories' => $categories,
                'errors'     => $this->validator->getErrors(),
            ]);
            return view('layouts/Layout', ['title' => 'Edit Menu', 'content' => $content]);
        }

        helper('upload');
        $image = $this->request->getFile('image');
        $imagePath = $menu['image']; // pertahankan gambar lama jika tidak upload baru
        try {
            $newPath = move_validated_upload($image, 'menus');
            if ($newPath !== null) {
                $imagePath = $newPath;
            }
        } catch (\RuntimeException $e) {
            $categories = $this->categoryModel->where('restaurant_id', $restaurantId)->where('is_active', 1)->orderBy('sort_order')->findAll();
            $content = view('resto/menus/form', [
                'menu'       => $menu,
                'categories' => $categories,
                'errors'     => ['image' => $e->getMessage()],
            ]);
            return view('layouts/Layout', ['title' => 'Edit Menu', 'content' => $content]);
        }

        $this->menuModel->update($id, [
            'category_id'  => $this->request->getPost('category_id'),
            'name'         => $this->request->getPost('name'),
            'description'  => $this->request->getPost('description'),
            'price'        => $this->request->getPost('price'),
            'image'        => $imagePath,
            'label'        => $this->request->getPost('label') ?: 'biasa',
            'is_available' => $this->request->getPost('is_available') ? 1 : 0,
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/resto/menus')->with('success', 'Menu berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $restaurantId = session('restaurant_id');
        $menu = $this->menuModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$menu) {
            return redirect()->to('/resto/menus')->with('error', 'Menu tidak ditemukan');
        }
        $this->menuModel->delete($id);
        return redirect()->to('/resto/menus')->with('success', 'Menu berhasil dihapus');
    }
}