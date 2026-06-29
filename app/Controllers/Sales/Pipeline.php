<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Lead;

class Pipeline extends BaseController
{
    protected Lead $leadModel;

    public function __construct()
    {
        $this->leadModel = new Lead();
    }

    public function index()
    {
        $userId   = session('user_id');
        $statuses = ['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'];

        $pipeline = [];
        foreach ($statuses as $status) {
            $pipeline[$status] = $this->leadModel
                ->groupStart()
                    ->where('assigned_to', $userId)
                    ->orWhere('assigned_to', null)
                ->groupEnd()
                ->where('status', $status)
                ->orderBy('next_followup_date', 'ASC')
                ->findAll();
        }

        $content = view('sales/pipeline/index', ['pipeline' => $pipeline, 'statuses' => $statuses]);
        return view('layouts/Layout', ['title' => 'Pipeline Leads', 'content' => $content]);
    }
}
