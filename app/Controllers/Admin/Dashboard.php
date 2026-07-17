<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Total restoran terdaftar
        $totalRestaurants = $db->table('restaurants')->countAllResults();

        // Total leads semua status
        $totalLeads = $db->table('leads')->countAllResults();

        // Langganan aktif (status Trial atau Aktif)
        $activeSubscriptions = $db->table('restaurant_subscriptions')
            ->whereIn('status', ['Trial', 'Aktif'])
            ->countAllResults();

        // Pendapatan langganan bulan ini (Lunas)
        $monthlyRevenue = $db->table('subscription_payments')
            ->selectSum('amount')
            ->where('status', 'Lunas')
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

        // --- Data untuk Grafik (Chart.js) ---

        // 1. Grafik Harian (7 Hari Terakhir)
        $dailyLabels = [];
        $dailyNewLeads = [];
        $dailyNewRestaurants = [];
        $dailyNewSubscriptions = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dailyLabels[] = date('d M', strtotime($date));

            // Leads baru per hari
            $leadsCount = $db->table('leads')
                ->where('DATE(created_at)', $date)
                ->countAllResults();
            $dailyNewLeads[] = $leadsCount;

            // Restoran baru per hari
            $restoCount = $db->table('restaurants')
                ->where('DATE(created_at)', $date)
                ->countAllResults();
            $dailyNewRestaurants[] = $restoCount;

            // Langganan baru per hari
            $subscriptionCount = $db->table('restaurant_subscriptions')
                ->where('DATE(start_date)', $date)
                ->countAllResults();
            $dailyNewSubscriptions[] = $subscriptionCount;
        }

        // 2. Grafik Bulanan (6 Bulan Terakhir)
        $monthlyLabels = [];
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('m', strtotime("-$i months"));
            $year = date('Y', strtotime("-$i months"));
            $monthlyLabels[] = date('M Y', strtotime("-$i months"));

            $rev = $db->table('subscription_payments')
                ->selectSum('amount')
                ->where('status', 'Lunas')
                ->where('MONTH(payment_date)', $month)
                ->where('YEAR(payment_date)', $year)
                ->get()->getRow()->amount ?? 0;
            $monthlyRevenueData[] = (float)$rev;
        }

        $content = view('admin/dashboard/index', [
            'totalRestaurants'      => $totalRestaurants,
            'totalLeads'            => $totalLeads,
            'activeSubscriptions'   => $activeSubscriptions,
            'monthlyRevenue'        => (float)$monthlyRevenue,
            'recentRestaurants'     => $recentRestaurants,
            'recentLeads'           => $recentLeads,
            'followupToday'         => $followupToday,
            'dealsThisMonth'        => $dealsThisMonth,
            // Chart Data
            'dailyLabels'           => $dailyLabels,
            'dailyNewLeads'         => $dailyNewLeads,
            'dailyNewRestaurants'   => $dailyNewRestaurants,
            'dailyNewSubscriptions' => $dailyNewSubscriptions,
            'monthlyLabels'         => $monthlyLabels,
            'monthlyRevenueData'    => $monthlyRevenueData,
        ]);
        
        return view('layouts/Layout', ['title' => 'Dashboard Super Admin', 'content' => $content]);
    }
}
