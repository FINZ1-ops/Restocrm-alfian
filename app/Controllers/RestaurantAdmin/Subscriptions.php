<?php

namespace App\Controllers\RestaurantAdmin;

use App\Controllers\BaseController;
use App\Models\SubscriptionPlan;
use App\Models\RestaurantSubscription;
use App\Models\SubscriptionPayment;

class Subscriptions extends BaseController
{
    protected SubscriptionPlan $planModel;
    protected RestaurantSubscription $subscriptionModel;
    protected SubscriptionPayment $paymentModel;

    public function __construct()
    {
        $this->planModel = new SubscriptionPlan();
        $this->subscriptionModel = new RestaurantSubscription();
        $this->paymentModel = new SubscriptionPayment();
    }

    public function index()
    {
        $restaurantId = session('restaurant_id');
        $plans = $this->planModel->where('is_active', 1)->orderBy('price_monthly', 'ASC')->findAll();
        // Prefer subscription with plan info so view can show plan_name
        $subscriptionQuery = $this->subscriptionModel->getSubscriptionWithPlan($restaurantId);
        $subscription = null;
        if (is_object($subscriptionQuery) && method_exists($subscriptionQuery, 'getRowArray')) {
            $subscription = $subscriptionQuery->getRowArray();
        } elseif (is_array($subscriptionQuery)) {
            $subscription = $subscriptionQuery;
        }

        $content = view('resto/subscriptions/index', [
            'plans' => $plans,
            'subscription' => $subscription,
        ]);
        return view('layouts/Layout', ['title' => 'Paket Langganan', 'content' => $content]);
    }

    public function new()
    {
        $restaurantId = session('restaurant_id');
        $planId = (int) $this->request->getGet('plan_id');
        $plan = $this->planModel->find($planId);

        if (!$plan) {
            return redirect()->to('/resto/subscriptions')->with('error', 'Paket tidak ditemukan.');
        }

        $content = view('resto/subscriptions/new', [
            'plan' => $plan,
            'old'  => $this->request->getPost(),
        ]);
        return view('layouts/Layout', ['title' => 'Beli Paket ' . $plan['name'], 'content' => $content]);
    }

    public function create()
    {
        $rules = [
            'plan_id'       => 'required|integer',
            'billing_cycle' => 'required|in_list[monthly,yearly]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Silakan pilih paket dan siklus tagihan yang valid.');
        }

        $restaurantId = session('restaurant_id');
        $planId = (int) $this->request->getPost('plan_id');
        $billingCycle = $this->request->getPost('billing_cycle');

        $plan = $this->planModel->find($planId);
        if (!$plan) {
            return redirect()->back()->withInput()->with('error', 'Paket langganan tidak ditemukan.');
        }

        $amount = $billingCycle === 'yearly' ? $plan['price_yearly'] : $plan['price_monthly'];
        $now = date('Y-m-d');
        $current = $this->subscriptionModel->getActiveSubscription($restaurantId);
        $baseDate = $now;

        if (!empty($current['end_date']) && $current['end_date'] > $now) {
            $baseDate = $current['end_date'];
        }

        $duration = $billingCycle === 'yearly' ? '+1 year' : '+1 month';
        $newEndDate = date('Y-m-d', strtotime($baseDate . ' ' . $duration));

        $subscriptionData = [
            'restaurant_id' => $restaurantId,
            'plan_id'       => $planId,
            'billing_cycle' => $billingCycle,
            'start_date'    => $now,
            'end_date'      => $newEndDate,
            'status'        => 'Aktif',
        ];

        if ($this->paymentModel->subscriptionHasColumn('is_active')) {
            $subscriptionData['is_active'] = 1;
        }

        if ($current) {
            $this->subscriptionModel->update($current['id'], $subscriptionData);
            $subscriptionId = $current['id'];
        } else {
            $subscriptionId = $this->subscriptionModel->insert($subscriptionData);
        }

        if (!$subscriptionId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan langganan. Coba lagi.');
        }

        $paymentId = $this->paymentModel->insert([
            'restaurant_id'   => $restaurantId,
            'subscription_id' => $subscriptionId,
            'invoice_number'  => $this->paymentModel->generateInvoiceNumber(),
            'amount'          => $amount,
            'payment_method'  => 'Cash',
            'status'          => 'Lunas',
            'payment_date'    => $now,
            'due_date'        => $now,
            'confirmed_by'    => session('user_id'),
            'confirmed_at'    => date('Y-m-d H:i:s'),
            'notes'           => 'Pembayaran paket oleh admin restoran.',
        ]);

        if ($paymentId && $this->paymentModel->subscriptionHasColumn('next_invoice_date')) {
            $this->subscriptionModel->update($subscriptionId, [
                'next_invoice_date' => $newEndDate,
                'last_invoice_id'   => $paymentId,
            ]);
        }

        return redirect()->to('/resto/subscriptions')->with('success', 'Paket berhasil dibeli dan invoice otomatis tercatat.');
    }
}
