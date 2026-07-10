<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubscriptionPayment;
use App\Models\Restaurant;
use App\Models\RestaurantSubscription;
use App\Models\SubscriptionPlan;

/**
 * Pembayaran LANGGANAN APLIKASI — dari restoran ke pemilik RESTOCRM.
 * BEDA dengan Cashier\Orders (pembayaran order customer ke restoran).
 * Hanya bisa diakses Super Admin (lihat Routes.php: filter role:super_admin).
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
     * Daftar semua invoice pembayaran langganan, terbaru dulu.
     * Filter opsional: ?status=Belum+Dibayar
     */
    public function index()
    {
        $status   = $this->request->getGet('status');
        $payments = $this->paymentModel->getAllWithDetails($status ?: null);

        $content = view('admin/subscription-payments/index', [
            'payments'      => $payments,
            'currentStatus' => $status,
            'isOverdueView' => false,
        ]);
        return view('layouts/Layout', ['title' => 'Pembayaran Langganan', 'content' => $content]);
    }

    /**
     * Invoice yang sudah lewat jatuh tempo & belum lunas.
     */
    public function overdue()
    {
        $payments = $this->paymentModel->getOverdue();

        $content = view('admin/subscription-payments/index', [
            'payments'      => $payments,
            'currentStatus' => null,
            'isOverdueView' => true,
        ]);
        return view('layouts/Layout', ['title' => 'Tagihan Jatuh Tempo', 'content' => $content]);
    }

    /**
     * Form buat invoice baru.
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
     * Simpan invoice baru. Nomor invoice digenerate otomatis.
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

        // Ambil subscription aktif resto ini kalau ada, supaya invoice
        // ini tertaut. Kalau belum punya subscription sama sekali
        // (harusnya tidak terjadi karena setiap resto dibuat dengan
        // Trial), buat dulu barunya.
        $subscription = $this->subscriptionModel->where('restaurant_id', $restaurantId)->first();
        if (!$subscription) {
            $subscriptionId = $this->subscriptionModel->insert([
                'restaurant_id' => $restaurantId,
                'plan_id'       => $planId,
                'start_date'    => date('Y-m-d'),
                'end_date'      => date('Y-m-d'),
                'status'        => 'Trial',
                'billing_cycle' => $billingCycle,
            ]);
        } else {
            $subscriptionId = $subscription['id'];
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

        return redirect()->to('/admin/subscription-payments/' . $paymentId)
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * Detail 1 invoice.
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
     * Super Admin upload bukti pembayaran (mis. bukti transfer yang
     * dikirim resto lewat WhatsApp) → status jadi Menunggu Konfirmasi.
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
     * Konfirmasi lunas → status Lunas + perpanjang restaurant_subscriptions.
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

        // Perpanjang langganan resto sesuai billing cycle subscription terkait.
        if (!empty($payment['subscription_id'])) {
            $subscription = $this->subscriptionModel->find($payment['subscription_id']);
            if ($subscription) {
                $this->subscriptionModel->upgradeSubscription(
                    $subscription['restaurant_id'],
                    $subscription['plan_id'],
                    $subscription['billing_cycle'] ?? 'monthly'
                );
            }
        }

        return redirect()->to('/admin/subscription-payments/' . $id)
            ->with('success', 'Pembayaran dikonfirmasi & langganan resto diperpanjang.');
    }

    /**
     * Tolak pembayaran → status Ditolak, dengan alasan di notes.
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