<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubscriptionPayment;
use App\Models\Restaurant;
use App\Models\RestaurantSubscription;
use App\Models\SubscriptionPlan;

/**
 * Manages application subscription invoices for Super Admin.
 */
class SubscriptionPayments extends BaseController
{
    protected SubscriptionPayment $paymentModel;
    protected Restaurant $restaurantModel;
    protected RestaurantSubscription $subscriptionModel;
    protected SubscriptionPlan $planModel;

    public function __construct()
    {
        $this->paymentModel      = new SubscriptionPayment();
        $this->restaurantModel   = new Restaurant();
        $this->subscriptionModel = new RestaurantSubscription();
        $this->planModel         = new SubscriptionPlan();
    }

    /**
     * List all subscription invoices, optionally filtered by status.
     */
    public function index()
    {
        $status = $this->request->getGet('status');

        try {
            $this->paymentModel->generateInvoicesFromDueSubscriptions();
            $this->paymentModel->markOverdueInvoices();
        } catch (\Throwable $e) {
            log_message('error', 'Auto invoice generation skipped: ' . $e->getMessage());
        }

        $payments = $this->paymentModel->getAllWithDetails($status ?: null);

        $content = view('admin/subscription-payments/index', [
            'payments'      => $payments,
            'currentStatus' => $status,
            'isOverdueView' => false,
        ]);
        return view('layouts/Layout', ['title' => 'Pembayaran Langganan', 'content' => $content]);
    }

    /**
     * List overdue subscription invoices.
     */
    public function overdue()
    {
        try {
            $this->paymentModel->generateInvoicesFromDueSubscriptions();
            $this->paymentModel->markOverdueInvoices();
        } catch (\Throwable $e) {
            log_message('error', 'Auto invoice generation skipped: ' . $e->getMessage());
        }

        $payments = $this->paymentModel->getOverdue();

        $content = view('admin/subscription-payments/index', [
            'payments'      => $payments,
            'currentStatus' => null,
            'isOverdueView' => true,
        ]);
        return view('layouts/Layout', ['title' => 'Tagihan Jatuh Tempo', 'content' => $content]);
    }

    /**
     * Show form for creating a new invoice.
     */
    public function new()
    {
        $restaurants = $this->restaurantModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
        $plans       = $this->planModel->where('is_active', 1)->findAll();

        $content = view('admin/subscription-payments/form', [
            'restaurants' => $restaurants,
            'plans'       => $plans,
        ]);
        return view('layouts/Layout', ['title' => 'Buat Invoice Baru', 'content' => $content]);
    }

    /**
     * Store a new subscription invoice.
     */
    public function create()
    {
        $rules = [
            'restaurant_id' => 'required|integer',
            'plan_id'       => 'required|integer',
            'billing_cycle' => 'required|in_list[monthly,yearly]',
            'due_date'      => 'required|valid_date[Y-m-d]',
        ];

        if (!$this->validate($rules)) {
            $restaurants = $this->restaurantModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
            $plans       = $this->planModel->where('is_active', 1)->findAll();
            $content = view('admin/subscription-payments/form', [
                'restaurants' => $restaurants,
                'plans'       => $plans,
                'errors'      => $this->validator->getErrors(),
                'old'         => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Buat Invoice Baru', 'content' => $content]);
        }

        $restaurantId = (int) $this->request->getPost('restaurant_id');
        $planId       = (int) $this->request->getPost('plan_id');
        $billingCycle = $this->request->getPost('billing_cycle');

        $plan = $this->planModel->find($planId);
        if (!$plan) {
            return redirect()->back()->withInput()->with('error', 'Paket langganan tidak ditemukan.');
        }
        $amount = $billingCycle === 'yearly' ? $plan['price_yearly'] : $plan['price_monthly'];

        // Load existing subscription or create a new one for this restaurant.
        $subscription = $this->subscriptionModel
            ->where('restaurant_id', $restaurantId)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$subscription) {
            $subData = [
                'restaurant_id' => $restaurantId,
                'plan_id'       => $planId,
                'start_date'    => date('Y-m-d'),
                'end_date'      => date('Y-m-d'),
                'status'        => 'Trial',
                'billing_cycle' => $billingCycle,
            ];

            if ($this->subscriptionModel->hasColumn('is_active')) {
                $subData['is_active'] = 1;
            }

            $subscriptionId = $this->subscriptionModel->insert($subData);
        } else {
            $subscriptionId = (int) $subscription['id'];
            $this->subscriptionModel->update($subscriptionId, [
                'plan_id'       => $planId,
                'billing_cycle' => $billingCycle,
            ]);
        }

        if (!$subscriptionId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyiapkan data langganan restoran.');
        }

        $paymentId = $this->paymentModel->insert([
            'restaurant_id'   => $restaurantId,
            'subscription_id' => $subscriptionId,
            'invoice_number'  => $this->paymentModel->generateInvoiceNumber(),
            'amount'          => $amount,
            'status'          => 'Belum Dibayar',
            'due_date'        => $this->request->getPost('due_date'),
            'notes'           => $this->request->getPost('notes'),
        ]);

        if (!$paymentId) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat invoice.');
        }

        $this->subscriptionModel->update($subscriptionId, [
            'next_invoice_date' => $this->request->getPost('due_date'),
            'last_invoice_id'   => $paymentId,
        ]);

        return redirect()->to('/admin/subscription-payments/' . $paymentId)
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * Display invoice details.
     */
    public function view($id = null)
    {
        $payment = $this->paymentModel->getWithDetails((int) $id);
        if (!$payment) {
            return redirect()->to('/admin/subscription-payments')->with('error', 'Invoice tidak ditemukan.');
        }

        $content = view('admin/subscription-payments/view', ['payment' => $payment]);
        return view('layouts/Layout', ['title' => 'Invoice ' . $payment['invoice_number'], 'content' => $content]);
    }

    /**
     * Upload payment proof and set invoice status to Menunggu Konfirmasi.
     */
    public function uploadProof($id = null)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->to('/admin/subscription-payments')->with('error', 'Invoice tidak ditemukan.');
        }

        helper('upload');
        try {
            $proofPath = move_validated_upload(
                $this->request->getFile('proof_image'),
                'subscription_proofs'
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (!$proofPath) {
            return redirect()->back()->with('error', 'Silakan pilih file bukti pembayaran.');
        }

        $this->paymentModel->update($id, [
            'proof_image' => $proofPath,
            'status'      => 'Menunggu Konfirmasi',
        ]);

        return redirect()->to('/admin/subscription-payments/' . $id)
            ->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    /**
     * Confirm payment and extend the related restaurant subscription.
     */
    public function confirm($id = null)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->to('/admin/subscription-payments')->with('error', 'Invoice tidak ditemukan.');
        }

        $this->paymentModel->update($id, [
            'status'       => 'Lunas',
            'payment_date' => date('Y-m-d'),
            'confirmed_by' => session('user_id'),
            'confirmed_at' => date('Y-m-d H:i:s'),
        ]);

        // Extend the linked subscription regardless of current status.
        if (!empty($payment['subscription_id'])) {
            $subscription = $this->subscriptionModel->find($payment['subscription_id']);
            if ($subscription) {
                $billingCycle = $subscription['billing_cycle'] ?? 'monthly';
                $duration     = $billingCycle === 'yearly' ? '+1 year' : '+1 month';
                $baseDate     = (!empty($subscription['end_date']) && $subscription['end_date'] >= date('Y-m-d'))
                    ? $subscription['end_date']
                    : date('Y-m-d');

                $newEndDate = date('Y-m-d', strtotime($baseDate . ' ' . $duration));

                $this->subscriptionModel->update($subscription['id'], [
                    'start_date'        => date('Y-m-d'),
                    'end_date'          => $newEndDate,
                    'billing_cycle'     => $billingCycle,
                    'status'            => 'Aktif',
                    'is_active'         => 1,
                    'next_invoice_date' => $newEndDate,
                    'last_invoice_id'   => $id,
                ]);
            }
        }

        return redirect()->to('/admin/subscription-payments/' . $id)
            ->with('success', 'Pembayaran dikonfirmasi & langganan resto diperpanjang.');
    }

    /**
     * Reject payment and save the rejection reason.
     */
    public function reject($id = null)
    {
        $payment = $this->paymentModel->find($id);
        if (!$payment) {
            return redirect()->to('/admin/subscription-payments')->with('error', 'Invoice tidak ditemukan.');
        }

        $reason = $this->request->getPost('reject_reason');

        $this->paymentModel->update($id, [
            'status' => 'Ditolak',
            'notes'  => trim(($payment['notes'] ?? '') . "\n[Ditolak] " . $reason),
        ]);

        return redirect()->to('/admin/subscription-payments/' . $id)
            ->with('success', 'Pembayaran ditolak.');
    }
}