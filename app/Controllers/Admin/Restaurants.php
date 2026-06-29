<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Restaurant;
use App\Models\RestaurantSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Lead;

class Restaurants extends BaseController
{
    protected Restaurant $restaurantModel;
    protected RestaurantSubscription $subscriptionModel;
    protected SubscriptionPlan $planModel;
    protected User $userModel;
    protected Lead $leadModel;

    public function __construct()
    {
        $this->restaurantModel   = new Restaurant();
        $this->subscriptionModel = new RestaurantSubscription();
        $this->planModel         = new SubscriptionPlan();
        $this->userModel         = new User();
        $this->leadModel         = new Lead();
    }

    public function index()
    {
        $restaurants = $this->restaurantModel->orderBy('created_at', 'DESC')->findAll();

        if (!empty($restaurants)) {
            $restaurantIds = array_column($restaurants, 'id');
            
            // Eager load subscriptions (latest per restaurant)
            $allSubscriptions = $this->subscriptionModel
                ->whereIn('restaurant_id', $restaurantIds)
                ->orderBy('created_at', 'DESC')
                ->findAll();
                
            $subsByResto = [];
            $planIds = [];
            foreach ($allSubscriptions as $sub) {
                if (!isset($subsByResto[$sub['restaurant_id']])) {
                    $subsByResto[$sub['restaurant_id']] = $sub;
                    if (!empty($sub['plan_id'])) {
                        $planIds[] = $sub['plan_id'];
                    }
                }
            }
            
            // Eager load plans
            $plansById = [];
            if (!empty($planIds)) {
                $plans = $this->planModel->whereIn('id', array_unique($planIds))->findAll();
                $plansById = array_column($plans, null, 'id');
            }

            // Attach to restaurants
            foreach ($restaurants as &$r) {
                if (isset($subsByResto[$r['id']])) {
                    $sub = $subsByResto[$r['id']];
                    $sub['plan_name'] = $plansById[$sub['plan_id']]['name'] ?? '-';
                    $r['subscription'] = $sub;
                } else {
                    $r['subscription'] = null;
                }
            }
        }
        $content = view('admin/restaurants/index', ['restaurants' => $restaurants]);
        return view('layouts/Layout', ['title' => 'Manajemen Restoran', 'content' => $content]);
    }

    public function new()
    {
        $plans = $this->planModel->where('is_active', 1)->findAll();
        $content = view('admin/restaurants/form', ['restaurant' => null, 'plans' => $plans]);
        return view('layouts/Layout', ['title' => 'Tambah Restoran', 'content' => $content]);
    }

    public function create()
    {
        $rules = [
            'name'     => 'required|min_length[2]|max_length[255]',
            'slug'     => 'required|min_length[2]|max_length[255]|is_unique[restaurants.slug]',
            'whatsapp' => 'required|min_length[8]|max_length[20]',
            'plan_id'  => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            $plans = $this->planModel->where('is_active', 1)->findAll();
            $content = view('admin/restaurants/form', [
                'restaurant' => null,
                'plans'      => $plans,
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Restoran', 'content' => $content]);
        }

        // Create restaurant
        $restaurantId = $this->restaurantModel->insert([
            'name'          => $this->request->getPost('name'),
            'slug'          => $this->request->getPost('slug'),
            'address'       => $this->request->getPost('address'),
            'whatsapp'      => $this->request->getPost('whatsapp'),
            'description'   => $this->request->getPost('description'),
            'opening_hours' => $this->request->getPost('opening_hours'),
            'is_active'     => 1,
        ]);

        // Create admin_resto user
        $adminEmail    = $this->request->getPost('admin_email');
        $adminPassword = $this->request->getPost('admin_password') ?: 'resto123';
        $this->userModel->insert([
            'name'          => 'Admin ' . $this->request->getPost('name'),
            'email'         => $adminEmail,
            'password'      => password_hash($adminPassword, PASSWORD_BCRYPT),
            'role'          => 'admin_resto',
            'restaurant_id' => $restaurantId,
            'is_active'     => 1,
        ]);

        // Create subscription (Trial)
        $this->subscriptionModel->insert([
            'restaurant_id' => $restaurantId,
            'plan_id'       => $this->request->getPost('plan_id'),
            'start_date'    => date('Y-m-d'),
            'end_date'      => date('Y-m-d', strtotime('+30 days')),
            'status'        => 'Trial',
            'billing_cycle' => 'monthly',
        ]);

        return redirect()->to('/admin/restaurants')->with('success', 'Restoran berhasil ditambahkan');
    }

    public function show($id = null)
    {
        $restaurant  = $this->restaurantModel->find($id);
        if (!$restaurant) {
            return redirect()->to('/admin/restaurants')->with('error', 'Restoran tidak ditemukan');
        }

        $subscription = $this->subscriptionModel->getActiveSubscription($id);
        $users        = $this->userModel->where('restaurant_id', $id)->findAll();

        $content = view('admin/restaurants/show', [
            'restaurant'   => $restaurant,
            'subscription' => $subscription,
            'users'        => $users,
        ]);
        return view('layouts/Layout', ['title' => $restaurant['name'], 'content' => $content]);
    }

    public function edit($id = null)
    {
        $restaurant = $this->restaurantModel->find($id);
        if (!$restaurant) {
            return redirect()->to('/admin/restaurants')->with('error', 'Restoran tidak ditemukan');
        }
        $plans = $this->planModel->where('is_active', 1)->findAll();
        $content = view('admin/restaurants/form', [
            'restaurant' => $restaurant,
            'plans'      => $plans,
        ]);
        return view('layouts/Layout', ['title' => 'Edit Restoran', 'content' => $content]);
    }

    public function update($id = null)
    {
        $restaurant = $this->restaurantModel->find($id);
        if (!$restaurant) {
            return redirect()->to('/admin/restaurants')->with('error', 'Restoran tidak ditemukan');
        }

        $rules = [
            'name'     => 'required|min_length[2]|max_length[255]',
            'whatsapp' => 'required|min_length[8]|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            $plans = $this->planModel->where('is_active', 1)->findAll();
            $content = view('admin/restaurants/form', [
                'restaurant' => $restaurant,
                'plans'      => $plans,
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Edit Restoran', 'content' => $content]);
        }

        $this->restaurantModel->update($id, [
            'name'          => $this->request->getPost('name'),
            'address'       => $this->request->getPost('address'),
            'whatsapp'      => $this->request->getPost('whatsapp'),
            'description'   => $this->request->getPost('description'),
            'opening_hours' => $this->request->getPost('opening_hours'),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/restaurants/' . $id)->with('success', 'Restoran berhasil diperbarui');
    }

    public function delete($id = null)
    {
        $restaurant = $this->restaurantModel->find($id);
        if (!$restaurant) {
            return redirect()->to('/admin/restaurants')->with('error', 'Restoran tidak ditemukan');
        }
        $this->restaurantModel->delete($id);
        return redirect()->to('/admin/restaurants')->with('success', 'Restoran berhasil dihapus');
    }

    // Convert Lead → Restaurant
    public function convertLead($id = null)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead || $lead['status'] !== 'Deal') {
            return redirect()->to('/admin/leads')->with('error', 'Hanya lead berstatus Deal yang bisa dikonversi');
        }

        $plans = $this->planModel->where('is_active', 1)->findAll();
        $content = view('admin/leads/convert', ['lead' => $lead, 'plans' => $plans]);
        return view('layouts/Layout', ['title' => 'Konversi Lead ke Restoran', 'content' => $content]);
    }

        public function doConvert($id = null)
    {
        // Pastikan lead ada dan statusnya Deal
        $lead = $this->leadModel->find($id);
        if (!$lead || $lead['status'] !== 'Deal') {
            return redirect()->to('/admin/leads')
                ->with('error', 'Hanya lead berstatus Deal yang bisa dikonversi');
        }
 
        // Validasi input form konversi
        $rules = [
            'name'        => 'required|min_length[2]|max_length[255]',
            'slug'        => 'required|min_length[2]|max_length[255]|is_unique[restaurants.slug]',
            'whatsapp'    => 'required|min_length[8]|max_length[20]',
            'admin_email' => 'required|valid_email|is_unique[users.email]',
            'plan_id'     => 'required|integer',
        ];
 
        if (!$this->validate($rules)) {
            // Validasi gagal — kembalikan ke form dengan error
            $plans = $this->planModel->whereIn('is_active', [1])->findAll();
            $content = view('admin/leads/convert', [
                'lead'   => $lead,
                'plans'  => $plans,
                'errors' => $this->validator->getErrors(),
            ]);
            return view('layouts/Layout', ['title' => 'Konversi Lead', 'content' => $content]);
        }
 
        // 1. Buat restoran baru dari data form
        $restaurantId = $this->restaurantModel->insert([
            'name'          => $this->request->getPost('name'),
            'slug'          => $this->request->getPost('slug'),
            'address'       => $this->request->getPost('address'),
            'whatsapp'      => $this->request->getPost('whatsapp'),
            'opening_hours' => $this->request->getPost('opening_hours'),
            'is_active'     => 1,
        ]);
 
        // 2. Buat akun admin_resto untuk restoran ini
        $adminPassword = $this->request->getPost('admin_password') ?: 'resto123';
        $this->userModel->insert([
            'name'          => 'Admin ' . $this->request->getPost('name'),
            'email'         => $this->request->getPost('admin_email'),
            'password'      => password_hash($adminPassword, PASSWORD_BCRYPT),
            'role'          => 'admin_resto',
            'restaurant_id' => $restaurantId,
            'is_active'     => 1,
        ]);
 
        // 3. Buat subscription Trial 30 hari
        $this->subscriptionModel->insert([
            'restaurant_id' => $restaurantId,
            'plan_id'       => $this->request->getPost('plan_id'),
            'start_date'    => date('Y-m-d'),
            'end_date'      => date('Y-m-d', strtotime('+30 days')),
            'status'        => 'Trial',
            'billing_cycle' => 'monthly',
        ]);
 
        // 4. Update status lead menjadi sudah dikonversi (tandai di notes)
        $this->leadModel->update($id, [
            'notes' => ($lead['notes'] ?? '') . "\n[Dikonversi ke restoran ID: {$restaurantId} pada " . date('d/m/Y') . "]",
        ]);
 
        // Redirect ke halaman restoran yang baru dibuat
        return redirect()->to('/admin/restaurants/' . $restaurantId)
            ->with('success', 'Lead berhasil dikonversi! Restoran, akun admin, dan Trial 30 hari sudah dibuat.');
    }
}
