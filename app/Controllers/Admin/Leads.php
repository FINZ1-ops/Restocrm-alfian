<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Lead;
use App\Models\LeadFollowup;
use App\Models\User;

class Leads extends BaseController
{
    protected Lead $leadModel;
    protected LeadFollowup $followupModel;
    protected User $userModel;

    public function __construct()
    {
        $this->leadModel    = new Lead();
        $this->followupModel = new LeadFollowup();
        $this->userModel    = new User();
    }

    // ── LIST ──────────────────────────────────────────────────────────────
    public function index()
    {
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $query = $this->leadModel->orderBy('created_at', 'DESC');

        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->groupStart()
                ->like('business_name', $search)
                ->orLike('owner_name', $search)
                ->orLike('whatsapp', $search)
                ->groupEnd();
        }

        $leads = $query->paginate(15);

        // Count per status
        $statusCounts = [];
        $allStatuses = ['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'];
        foreach ($allStatuses as $s) {
            $statusCounts[$s] = $this->leadModel->where('status', $s)->countAllResults();
        }

        $salesUsers = $this->userModel->where('role', 'sales')->findAll();

        $content = view('admin/leads/index', [
            'leads'       => $leads,
            'pager'       => $this->leadModel->pager,
            'statusCounts'=> $statusCounts,
            'salesUsers'  => $salesUsers,
            'currentStatus' => $status,
            'currentSearch' => $search,
        ]);
        return view('layouts/Layout', ['title' => 'Leads CRM', 'content' => $content]);
    }

    // ── CREATE ────────────────────────────────────────────────────────────
    public function new()
    {
        $salesUsers = $this->userModel->where('role', 'sales')->findAll();
        $content = view('admin/leads/form', [
            'lead'       => null,
            'salesUsers' => $salesUsers,
        ]);
        return view('layouts/Layout', ['title' => 'Tambah Lead', 'content' => $content]);
    }

    public function create()
    {
        $rules = [
            'business_name' => 'required|min_length[2]|max_length[255]',
            'owner_name'    => 'required|min_length[2]|max_length[255]',
            'whatsapp'      => 'required|min_length[8]|max_length[20]',
            'city'          => 'permit_empty|max_length[100]',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            $salesUsers = $this->userModel->where('role', 'sales')->findAll();
            $content = view('admin/leads/form', [
                'lead'       => null,
                'salesUsers' => $salesUsers,
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Lead', 'content' => $content]);
        }

        $data = [
            'business_name'    => $this->request->getPost('business_name'),
            'owner_name'       => $this->request->getPost('owner_name'),
            'whatsapp'         => $this->request->getPost('whatsapp'),
            'address'          => $this->request->getPost('address'),
            'city'             => $this->request->getPost('city'),
            'business_type'    => $this->request->getPost('business_type'),
            'lead_source'      => $this->request->getPost('lead_source'),
            'status'           => $this->request->getPost('status'),
            'assigned_to'      => $this->request->getPost('assigned_to') ?: null,
            'next_followup_date'=> $this->request->getPost('next_followup_date') ?: null,
            'notes'            => $this->request->getPost('notes'),
        ];

        $this->leadModel->insert($data);
        return redirect()->to('/admin/leads')->with('success', 'Lead berhasil ditambahkan');
    }

    // ── SHOW ──────────────────────────────────────────────────────────────
    public function show($id = null)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/admin/leads')->with('error', 'Lead tidak ditemukan');
        }

        $followups = $this->followupModel
            ->where('lead_id', $id)
            ->orderBy('followup_date', 'DESC')
            ->findAll();

        $content = view('admin/leads/show', [
            'lead'      => $lead,
            'followups' => $followups,
        ]);
        return view('layouts/Layout', ['title' => 'Detail Lead', 'content' => $content]);
    }

    // ── EDIT ──────────────────────────────────────────────────────────────
    public function edit($id = null)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/admin/leads')->with('error', 'Lead tidak ditemukan');
        }

        $salesUsers = $this->userModel->where('role', 'sales')->findAll();
        $content = view('admin/leads/form', [
            'lead'       => $lead,
            'salesUsers' => $salesUsers,
        ]);
        return view('layouts/Layout', ['title' => 'Edit Lead', 'content' => $content]);
    }

    public function update($id = null)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/admin/leads')->with('error', 'Lead tidak ditemukan');
        }

        $rules = [
            'business_name' => 'required|min_length[2]|max_length[255]',
            'owner_name'    => 'required|min_length[2]|max_length[255]',
            'whatsapp'      => 'required|min_length[8]|max_length[20]',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            $salesUsers = $this->userModel->where('role', 'sales')->findAll();
            $content = view('admin/leads/form', [
                'lead'       => $lead,
                'salesUsers' => $salesUsers,
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Edit Lead', 'content' => $content]);
        }

        $data = [
            'business_name'    => $this->request->getPost('business_name'),
            'owner_name'       => $this->request->getPost('owner_name'),
            'whatsapp'         => $this->request->getPost('whatsapp'),
            'address'          => $this->request->getPost('address'),
            'city'             => $this->request->getPost('city'),
            'business_type'    => $this->request->getPost('business_type'),
            'lead_source'      => $this->request->getPost('lead_source'),
            'status'           => $this->request->getPost('status'),
            'assigned_to'      => $this->request->getPost('assigned_to') ?: null,
            'next_followup_date'=> $this->request->getPost('next_followup_date') ?: null,
            'notes'            => $this->request->getPost('notes'),
        ];

        $this->leadModel->update($id, $data);
        return redirect()->to('/admin/leads/' . $id)->with('success', 'Lead berhasil diperbarui');
    }

    // ── DELETE ────────────────────────────────────────────────────────────
    public function delete($id = null)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/admin/leads')->with('error', 'Lead tidak ditemukan');
        }

        $this->leadModel->delete($id);
        return redirect()->to('/admin/leads')->with('success', 'Lead berhasil dihapus');
    }

    // ── ADD FOLLOWUP ──────────────────────────────────────────────────────
    public function addFollowup($id = null)
    {
        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/admin/leads')->with('error', 'Lead tidak ditemukan');
        }

        $data = [
            'lead_id'          => $id,
            'followup_date'    => $this->request->getPost('followup_date') ?: date('Y-m-d H:i:s'),
            'method'           => $this->request->getPost('method'),
            'notes'            => $this->request->getPost('notes'),
            'next_followup_date'=> $this->request->getPost('next_followup_date') ?: null,
        ];

        $this->followupModel->insert($data);

        // Update lead's next followup date & optionally status
        $updateLead = ['next_followup_date' => $data['next_followup_date']];
        if ($this->request->getPost('status')) {
            $updateLead['status'] = $this->request->getPost('status');
        }
        $this->leadModel->update($id, $updateLead);

        return redirect()->to('/admin/leads/' . $id)->with('success', 'Follow up berhasil dicatat');
    }
}
