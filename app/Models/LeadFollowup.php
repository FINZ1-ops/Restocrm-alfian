<?php

namespace App\Models;
use CodeIgniter\Model;

/**
 * LeadFollowup Model
 * 
 * Mengelola riwayat follow-up dari setiap lead
 * Menyimpan catatan komunikasi dengan prospek
 * Method: WhatsApp, Telepon, Email, Kunjungan
 */
class LeadFollowup extends Model
{
    // Nama tabel di database
    protected $table = 'lead_followups';
    
    // Primary key untuk tabel
    protected $primaryKey = 'id';
    
    // Gunakan auto increment untuk ID
    protected $useAutoIncrement = true;
    
    // Return type data (array)
    protected $returnType = 'array';
    
    // Field yang boleh di-insert/update
    protected $allowedFields = ['lead_id', 'followup_date', 'method', 'notes', 'next_followup_date'];
    
    // Gunakan timestamp otomatis
    protected $useTimestamps = true;
    
    // Field untuk created_at
    protected $createdField = 'created_at';
    
    // Field untuk updated_at
    protected $updatedField = '';

    /**
     * Ambil semua follow-up untuk satu lead
     * 
     * @param int $leadId - ID lead
     * @return array - Riwayat follow-up lead
     */
    public function getFollowupsByLead($leadId)
    {
        return $this->where('lead_id', $leadId)
                    ->orderBy('followup_date', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil follow-up terbaru untuk lead
     * 
     * @param int $leadId - ID lead
     * @return array|null - Data follow-up terbaru atau null
     */
    public function getLatestFollowup($leadId)
    {
        return $this->where('lead_id', $leadId)
                    ->orderBy('followup_date', 'DESC')
                    ->first();
    }

    /**
     * Hitung jumlah follow-up untuk lead
     * 
     * @param int $leadId - ID lead
     * @return int - Jumlah follow-up
     */
    public function countFollowups($leadId)
    {
        return $this->where('lead_id', $leadId)
                    ->countAllResults();
    }

    /**
     * Tambah catatan follow-up
     * 
     * @param int $leadId - ID lead
     * @param string $method - Metode follow-up (WhatsApp, Telepon, Email, Kunjungan)
     * @param string $notes - Catatan follow-up
     * @param string $nextFollowupDate - Tanggal follow-up berikutnya
     * @return int|false - ID follow-up baru atau false jika gagal
     */
    public function addFollowup($leadId, $method, $notes, $nextFollowupDate)
    {
        $data = [
            'lead_id' => $leadId,
            'followup_date' => date('Y-m-d H:i:s'),
            'method' => $method,
            'notes' => $notes,
            'next_followup_date' => $nextFollowupDate,
        ];

        return $this->insert($data);
    }

    /**
     * Ambil follow-up berdasarkan method
     * 
     * @param int $leadId - ID lead
     * @param string $method - Metode follow-up
     * @return array - Daftar follow-up dengan method tersebut
     */
    public function getFollowupsByMethod($leadId, $method)
    {
        return $this->where('lead_id', $leadId)
                    ->where('method', $method)
                    ->orderBy('followup_date', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil statistik follow-up per method
     * 
     * @param int $leadId - ID lead
     * @return array - Statistik follow-up per method
     */
    public function getFollowupStats($leadId)
    {
        return $this->db->table('lead_followups')
                        ->select('method, COUNT(*) as count')
                        ->where('lead_id', $leadId)
                        ->groupBy('method')
                        ->get()
                        ->getResultArray();
    }
}
