<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\Customer;

class Customers extends BaseController
{
    protected Customer $customerModel;

    public function __construct()
    {
        $this->customerModel = new Customer();
    }

    public function index()
    {
        $restaurantId = session('restaurant_id');
        $search       = $this->request->getGet('search');

        $query = $this->customerModel
            ->where('restaurant_id', $restaurantId)
            ->orderBy('total_orders', 'DESC');

        if ($search) {
            $query->groupStart()
                ->like('name', $search)
                ->orLike('whatsapp', $search)
                ->groupEnd();
        }

        $customers = $query->paginate(20);

        $content = view('resto/customers/index', [
            'customers'     => $customers,
            'pager'         => $this->customerModel->pager,
            'currentSearch' => $search,
        ]);
        return view('layouts/Layout', ['title' => 'CRM Pelanggan', 'content' => $content]);
    }

    public function view($id = null)
    {
        $restaurantId = session('restaurant_id');
        $customer = $this->customerModel->where('id', $id)->where('restaurant_id', $restaurantId)->first();
        if (!$customer) {
            return redirect()->to('/resto/customers')->with('error', 'Pelanggan tidak ditemukan');
        }

        // Get order history
        $db     = \Config\Database::connect();
        $orders = $db->table('orders')
            ->where('restaurant_id', $restaurantId)
            ->where('customer_whatsapp', $customer['whatsapp'])
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $content = view('resto/customers/view', ['customer' => $customer, 'orders' => $orders]);
        return view('layouts/Layout', ['title' => 'Detail Pelanggan', 'content' => $content]);
    }
}
