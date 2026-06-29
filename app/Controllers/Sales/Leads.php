<?php

namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Lead;
use App\Models\LeadFollowup;

/**
 * Controller: Sales/Leads
 * Kelola leads yang di-assign ke sales yang sedang login
 * Sales bisa tambah lead baru, edit status, dan catat follow-up
 */
class Leads extends BaseController
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
        $userId = session('user_id');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $query = $this->leadModel
        ->groupStart()
            ->where('assigned_to', $userId)
            ->orWhere('assigned_to', null)
        ->groupEnd()
            ->orderBy('next_followup_date', 'ASC');

        if ($status) $query->where('status', $status);
        if ($search) {
            $query->groupStart()
                ->like('business_name', $search)
                ->orLike('owner_name', $search)
                ->groupEnd();
        }

        $leads = $query->paginate(15);

        $statusCounts = [];
        $allStatuses  = ['Baru','Dihubungi','Tertarik','Demo','Negosiasi','Deal','Tidak Tertarik'];
        foreach ($allStatuses as $s) {
            $statusCounts[$s] = $this->leadModel
                ->groupStart()
                    ->where('assigned_to', $userId)
                    ->orWhere('assigned_to', null)
                ->groupEnd()
                ->where('status', $s)
                ->countAllResults();
        }

        $content = view('sales/leads/index', [
            'leads'         => $leads,
            'pager'         => $this->leadModel->pager,
            'statusCounts'  => $statusCounts,
            'currentStatus' => $status,
            'currentSearch' => $search,
        ]);
        return view('layouts/Layout', ['title' => 'Leads Saya', 'content' => $content]);
    }

    /**
     * Form tambah lead baru oleh sales
     * Sales otomatis di-assign sebagai assigned_to
     */
    public function new()
    {
        $content = view('sales/leads/form', ['lead' => null, 'salesUsers' => [], 'errors' => [], 'old' => []]);
        return view('layouts/Layout', ['title' => 'Tambah Lead', 'content' => $content]);
    }

    /**
     * Simpan lead baru — assigned_to otomatis diisi user_id sales yang login
     */
    public function create()
    {
        $rules = [
            'business_name' => 'required|min_length[2]|max_length[255]',
            'owner_name'    => 'required|min_length[2]|max_length[255]',
            'whatsapp'      => 'required|min_length[8]|max_length[20]',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            $content = view('sales/leads/form', [
                'lead'       => null,
                'salesUsers' => [],
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Tambah Lead', 'content' => $content]);
        }

        $this->leadModel->insert([
            'business_name'     => $this->request->getPost('business_name'),
            'owner_name'        => $this->request->getPost('owner_name'),
            'whatsapp'          => $this->request->getPost('whatsapp'),
            'address'           => $this->request->getPost('address'),
            'city'              => $this->request->getPost('city'),
            'business_type'     => $this->request->getPost('business_type'),
            'lead_source'       => $this->request->getPost('lead_source'),
            'status'            => $this->request->getPost('status'),
            'assigned_to'       => session('user_id'), // otomatis assign ke sales ini
            'next_followup_date'=> $this->request->getPost('next_followup_date') ?: null,
            'notes'             => $this->request->getPost('notes'),
        ]);

        return redirect()->to('/sales/leads')->with('success', 'Lead berhasil ditambahkan');
    }

    public function show($id = null)
    {
        $userId = session('user_id');
        $lead = $this->leadModel
            ->where('id', $id)
            ->groupStart()
                ->where('assigned_to', $userId)
                ->orWhere('assigned_to', null)
            ->groupEnd()
            ->first();
        if (!$lead) {
            return redirect()->to('/sales/leads')->with('error', 'Lead tidak ditemukan');
        }

        $followups = $this->followupModel
            ->where('lead_id', $id)
            ->orderBy('followup_date', 'DESC')
            ->findAll();

        $content = view('sales/leads/show', ['lead' => $lead, 'followups' => $followups]);
        return view('layouts/Layout', ['title' => 'Detail Lead', 'content' => $content]);
    }

    public function edit($id = null)
    {
        $userId = session('user_id');
        $lead = $this->leadModel
            ->where('id', $id)
            ->groupStart()
                ->where('assigned_to', $userId)
                ->orWhere('assigned_to', null)
            ->groupEnd()
            ->first();
        if (!$lead) {
            return redirect()->to('/sales/leads')->with('error', 'Lead tidak ditemukan');
        }
        $content = view('sales/leads/form', ['lead' => $lead, 'salesUsers' => [], 'errors' => [], 'old' => []]);
        return view('layouts/Layout', ['title' => 'Edit Lead', 'content' => $content]);
    }

    public function update($id = null)
    {
        $userId = session('user_id');
        $lead = $this->leadModel
            ->where('id', $id)
            ->groupStart()
                ->where('assigned_to', $userId)
                ->orWhere('assigned_to', null)
            ->groupEnd()
            ->first();
        if (!$lead) {
            return redirect()->to('/sales/leads')->with('error', 'Lead tidak ditemukan');
        }

        $rules = [
            'business_name' => 'required|min_length[2]|max_length[255]',
            'owner_name'    => 'required|min_length[2]|max_length[255]',
            'whatsapp'      => 'required|min_length[8]|max_length[20]',
            'status'        => 'required',
        ];

        if (!$this->validate($rules)) {
            $content = view('sales/leads/form', [
                'lead'       => $lead,
                'salesUsers' => [],
                'errors'     => $this->validator->getErrors(),
                'old'        => $this->request->getPost(),
            ]);
            return view('layouts/Layout', ['title' => 'Edit Lead', 'content' => $content]);
        }

        $this->leadModel->update($id, [
            'business_name'     => $this->request->getPost('business_name'),
            'owner_name'        => $this->request->getPost('owner_name'),
            'whatsapp'          => $this->request->getPost('whatsapp'),
            'address'           => $this->request->getPost('address'),
            'city'              => $this->request->getPost('city'),
            'business_type'     => $this->request->getPost('business_type'),
            'lead_source'       => $this->request->getPost('lead_source'),
            'status'            => $this->request->getPost('status'),
            'next_followup_date'=> $this->request->getPost('next_followup_date') ?: null,
            'notes'             => $this->request->getPost('notes'),
        ]);

        return redirect()->to('/sales/leads/' . $id)->with('success', 'Lead berhasil diperbarui');
    }

    // Sales tidak bisa hapus lead
    public function delete($id = null)
    {
        return redirect()->to('/sales/leads')->with('error', 'Sales tidak dapat menghapus lead');
    }

    /**
     * Catat follow-up baru dan update status lead sekaligus
     */
    public function addFollowup($id = null)
    {
        $userId = session('user_id');
        $lead = $this->leadModel
            ->where('id', $id)
            ->groupStart()
                ->where('assigned_to', $userId)
                ->orWhere('assigned_to', null)
            ->groupEnd()
            ->first();
        if (!$lead) {
            return redirect()->to('/sales/leads')->with('error', 'Lead tidak ditemukan');
        }

        $this->followupModel->insert([
            'lead_id'            => $id,
            'followup_date'      => $this->request->getPost('followup_date') ?: date('Y-m-d H:i:s'),
            'method'             => $this->request->getPost('method'),
            'notes'              => $this->request->getPost('notes'),
            'next_followup_date' => $this->request->getPost('next_followup_date') ?: null,
        ]);

        // Update next_followup_date dan status lead sekaligus
        $updateLead = [
            'next_followup_date' => $this->request->getPost('next_followup_date') ?: null,
        ];
        if ($this->request->getPost('status')) {
            $updateLead['status'] = $this->request->getPost('status');
        }
        $this->leadModel->update($id, $updateLead);

        return redirect()->to('/sales/leads/' . $id)->with('success', 'Follow up berhasil dicatat');
    }
}