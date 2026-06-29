<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubscriptionPlan;

class SubscriptionPlans extends BaseController
{
    protected SubscriptionPlan $planModel;

    public function __construct()
    {
        $this->planModel = new SubscriptionPlan();
    }

    public function index()
    {
        $plans = $this->planModel->orderBy('price_monthly', 'ASC')->findAll();
        $content = view('admin/plans/index', ['plans' => $plans]);
        return view('layouts/Layout', ['title' => 'Paket Langganan', 'content' => $content]);
    }

    public function new()
    {
        $content = view('admin/plans/form', ['plan' => null]);
        return view('layouts/Layout', ['title' => 'Tambah Paket', 'content' => $content]);
    }

    public function create()
    {
        $rules = [
            'name'          => 'required|min_length[2]|max_length[100]',
            'price_monthly' => 'required|numeric',
            'price_yearly'  => 'required|numeric',
            'max_tables'    => 'required|integer',
            'max_menus'     => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            $content = view('admin/plans/form', [
                'plan'   => null,
                'errors' => $this->validator->getErrors(),
                'old'    => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Paket', 'content' => $content]);
        }

        $this->planModel->insert([
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'price_monthly' => $this->request->getPost('price_monthly'),
            'price_yearly'  => $this->request->getPost('price_yearly'),
            'max_tables'    => $this->request->getPost('max_tables'),
            'max_menus'     => $this->request->getPost('max_menus'),
            'has_crm'       => $this->request->getPost('has_crm') ? 1 : 0,
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/plans')->with('success', 'Paket berhasil ditambahkan');
    }

    public function show($id = null)
    {
        return redirect()->to('/admin/plans/' . $id . '/edit');
    }

    public function edit($id = null)
    {
        $plan = $this->planModel->find($id);
        if (!$plan) {
            return redirect()->to('/admin/plans')->with('error', 'Paket tidak ditemukan');
        }
        $content = view('admin/plans/form', ['plan' => $plan]);
        return view('layouts/Layout', ['title' => 'Edit Paket', 'content' => $content]);
    }

    public function update($id = null)
    {
        $plan = $this->planModel->find($id);
        if (!$plan) {
            return redirect()->to('/admin/plans')->with('error', 'Paket tidak ditemukan');
        }

        $rules = [
            'name'          => 'required|min_length[2]|max_length[100]',
            'price_monthly' => 'required|numeric',
            'price_yearly'  => 'required|numeric',
            'max_tables'    => 'required|integer',
            'max_menus'     => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            $content = view('admin/plans/form', [
                'plan'   => $plan,
                'errors' => $this->validator->getErrors(),
                'old'    => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Edit Paket', 'content' => $content]);
        }

        $this->planModel->update($id, [
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'price_monthly' => $this->request->getPost('price_monthly'),
            'price_yearly'  => $this->request->getPost('price_yearly'),
            'max_tables'    => $this->request->getPost('max_tables'),
            'max_menus'     => $this->request->getPost('max_menus'),
            'has_crm'       => $this->request->getPost('has_crm') ? 1 : 0,
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/plans')->with('success', 'Paket berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $plan = $this->planModel->find($id);
        if (!$plan) {
            return redirect()->to('/admin/plans')->with('error', 'Paket tidak ditemukan');
        }
        $this->planModel->delete($id);
        return redirect()->to('/admin/plans')->with('success', 'Paket berhasil dihapus');
    }
}
