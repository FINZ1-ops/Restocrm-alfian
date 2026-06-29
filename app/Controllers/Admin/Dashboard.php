<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // For a full app, these would be fetched from database models
        // such as RestaurantModel, LeadModel, SubscriptionModel.
        // We'll pass the dummy data structure the view expects, but 
        // with dynamic querying placeholders if Models exist.
        
       $db = \Config\Database::connect();

    // Total restoran terdaftar
    $totalRestaurants = $db->table('restaurants')->countAllResults();

    // Total leads semua status
    $totalLeads = $db->table('leads')->countAllResults();

    // Langganan aktif (status Trial atau Aktif)
    $activeSubscriptions = $db->table('restaurant_subscriptions')
        ->whereIn('status', ['Trial', 'Aktif'])
        ->countAllResults();

    // Pendapatan langganan bulan ini (dari subscription_payments yang Lunas)
    $monthlyRevenue = $db->table('subscription_payments')
        ->selectSum('amount')
        ->where('status', 'paid')
        ->where('MONTH(payment_date)', date('m'))
        ->where('YEAR(payment_date)', date('Y'))
        ->get()->getRow()->amount ?? 0;

    // 5 restoran terbaru
    $recentRestaurants = $db->table('restaurants')
        ->orderBy('created_at', 'DESC')
        ->limit(5)
        ->get()->getResultArray();

    // 5 leads terbaru
    $recentLeads = $db->table('leads')
        ->orderBy('created_at', 'DESC')
        ->limit(5)
        ->get()->getResultArray();

    // Follow-up hari ini
    $followupToday = $db->table('leads')
        ->where('next_followup_date', date('Y-m-d'))
        ->whereNotIn('status', ['Deal', 'Tidak Tertarik'])
        ->countAllResults();

    // Leads berstatus Deal bulan ini
    $dealsThisMonth = $db->table('leads')
        ->where('status', 'Deal')
        ->where('MONTH(updated_at)', date('m'))
        ->where('YEAR(updated_at)', date('Y'))
        ->countAllResults();

    $content = view('admin/dashboard/index', [
        'totalRestaurants'    => $totalRestaurants,
        'totalLeads'          => $totalLeads,
        'activeSubscriptions' => $activeSubscriptions,
        'monthlyRevenue'      => (float)$monthlyRevenue,
        'recentRestaurants'   => $recentRestaurants,
        'recentLeads'         => $recentLeads,
        'followupToday'       => $followupToday,
        'dealsThisMonth'      => $dealsThisMonth,
    ]);
    return view('layouts/Layout', ['title' => 'Dashboard Super Admin', 'content' => $content]);
    }
}
