<?php

namespace App\Models;
use CodeIgniter\Model;
/**
 * Lead Model
 * 
 * Mengelola prospek/calon customer untuk Super Admin
 * Lead adalah calon restoran yang tertarik dengan layanan RESTOCRM
 * Status: Baru, Dihubungi, Tertarik, Demo, Negosiasi, Deal, Tidak Tertarik
 */
class Lead extends Model
{
    // Nama tabel di database
    protected $table = 'leads';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = [
        'business_name', 'owner_name', 'whatsapp', 'address', 'city',
        'business_type', 'lead_source', 'status', 'assigned_to', 'next_followup_date', 'notes'
    ];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = 'updated_at';

    // // Aturan validasi
    // protected $validationRules = [
    //     'business_name' => 'required|min_length[3]|max_length[255]',
    //     'owner_name' => 'required|min_length[3]|max_length[255]',
    //     'whatsapp' => 'required|regex_match[/^(\+62|0)[0-9]{9,12}$/]',
    //     'city' => 'required|min_length[2]',
    //     'status' => 'required|in_list[Baru,Dihubungi,Tertarik,Demo,Negosiasi,Deal,Tidak Tertarik]',
    // ];

    /**
     * Ambil semua lead dengan status tertentu
     * 
     * @param string $status - Status lead
     * @return array - Daftar lead dengan status tersebut
     */
    public function getLeadsByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil lead yang di-assign ke user tertentu
     * 
     * @param int $assignedTo - ID user/sales
     * @return array - Daftar lead yang di-assign
     */
    public function getLeadsByAssignee($assignedTo)
    {
        return $this->where('assigned_to', $assignedTo)
                    ->orderBy('next_followup_date', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil lead dengan follow-up date hari ini atau sudah lewat
     * 
     * @return array - Daftar lead yang perlu follow-up
     */
    public function getLeadsNeedingFollowup()
    {
        return $this->where('status !=', 'Deal')
                    ->where('status !=', 'Tidak Tertarik')
                    ->where('next_followup_date <=', date('Y-m-d'))
                    ->orderBy('next_followup_date', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil lead baru (belum dihubungi)
     * 
     * @return array - Daftar lead baru
     */
    public function getNewLeads()
    {
        return $this->where('status', 'Baru')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil lead berdasarkan city
     * 
     * @param string $city - Nama kota
     * @return array - Daftar lead di kota tersebut
     */
    public function getLeadsByCity($city)
    {
        return $this->where('city', $city)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Hitung lead berdasarkan status
     * 
     * @return array - Statistik lead per status
     */
    public function getLeadStats()
    {
        return $this->db->table('leads')
                        ->select('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->orderBy('status', 'ASC')
                        ->get()
                        ->getResultArray();
    }

    /**
     * Update lead status
     * 
     * @param int $leadId - ID lead
     * @param string $newStatus - Status baru
     * @param string|null $nextFollowupDate - Tanggal follow-up berikutnya
     * @return bool - True jika berhasil
     */
    public function updateLeadStatus($leadId, $newStatus, $nextFollowupDate = null)
    {
        $data = ['status' => $newStatus];
        if ($nextFollowupDate) {
            $data['next_followup_date'] = $nextFollowupDate;
        }
        return $this->update($leadId, $data);
    }

    /**
     * Assign lead ke sales person
     * 
     * @param int $leadId - ID lead
     * @param int $userId - ID sales person
     * @return bool - True jika berhasil
     */
    public function assignLeadToUser($leadId, $userId)
    {
        return $this->update($leadId, ['assigned_to' => $userId]);
    }
}
