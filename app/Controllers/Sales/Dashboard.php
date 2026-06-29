<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Lead;
use App\Models\LeadFollowup;

/**
 * Controller: Sales/Dashboard
 * Dashboard utama untuk role Sales
 * Menampilkan ringkasan leads, follow-up hari ini, dan statistik pipeline
 */
class Dashboard extends BaseController
{
    protected Lead $leadModel;
    protected LeadFollowup $followupModel;

    public function __construct()
    {
        $this->leadModel     = new Lead();
        $this->followupModel = new LeadFollowup();
    }

    public function index()
    {
        $userId   = session('user_id');
        $today    = date('Y-m-d');
        $statuses = ['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'];

        // Hitung jumlah leads per status milik sales ini
        $statusCounts = [];
        foreach ($statuses as $s) {
            $statusCounts[$s] = $this->leadModel
                ->groupStart()
                    ->where('assigned_to', $userId)
                    ->orWhere('assigned_to', null)
                ->groupEnd()
                ->where('status', $s)
                ->countAllResults();
        }

        // Total leads yang di-assign ke sales ini
        $totalLeads = $this->leadModel
            ->groupStart()
                ->where('assigned_to', $userId)
                ->orWhere('assigned_to', null)
            ->groupEnd()
            ->countAllResults();

        // Leads yang perlu di-follow-up hari ini atau sudah terlambat
        $followupToday = $this->leadModel
            ->groupStart()
                ->where('assigned_to', $userId)
                ->orWhere('assigned_to', null)
            ->groupEnd()
            ->where('next_followup_date <=', $today)
            ->whereNotIn('status', ['Deal', 'Tidak Tertarik'])
            ->orderBy('next_followup_date', 'ASC')
            ->findAll(10);

        // Leads terbaru yang di-assign ke sales ini
        $recentLeads = $this->leadModel
            ->groupStart()
                ->where('assigned_to', $userId)
                ->orWhere('assigned_to', null)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        $content = view('sales/dashboard/index', [
            'statusCounts'  => $statusCounts,
            'totalLeads'    => $totalLeads,
            'followupToday' => $followupToday,
            'recentLeads'   => $recentLeads,
        ]);

        return view('layouts/Layout', ['title' => 'Dashboard Sales', 'content' => $content]);
    }
}