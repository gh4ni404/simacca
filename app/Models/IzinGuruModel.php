<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * IzinGuruModel
 * 
 * Model for teacher leave/permission requests.
 * Handles approval workflow by Wakakur and auto-creation of absensi_guru records.
 * 
 * Workflow:
 * 1. Guru submits izin request (tanggal_mulai to tanggal_selesai)
 * 2. Wakakur reviews and approves/rejects
 * 3. If approved, auto-creates absensi_guru records with appropriate status
 * 
 * @package App\Models
 * @author SIMACCA Team
 * @version 2.0.0
 */
class IzinGuruModel extends Model
{
    protected $table            = 'izin_guru';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_izin',
        'alasan',
        'berkas',
        'status',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_persetujuan',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'guru_id'         => 'required|numeric',
        'tanggal_mulai'   => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
        'jenis_izin'      => 'required|in_list[izin,sakit,cuti,dinas_luar,lainnya]',
        'alasan'          => 'required',
        'status'          => 'required|in_list[pending,disetujui,ditolak]',
    ];
    protected $validationMessages   = [
        'guru_id' => [
            'required' => 'Guru harus dipilih',
            'numeric'  => 'Guru ID tidak valid',
        ],
        'tanggal_mulai' => [
            'required'   => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal mulai tidak valid',
        ],
        'tanggal_selesai' => [
            'required'   => 'Tanggal selesai harus diisi',
            'valid_date' => 'Format tanggal selesai tidak valid',
        ],
        'jenis_izin' => [
            'required' => 'Jenis izin harus dipilih',
            'in_list'  => 'Jenis izin tidak valid',
        ],
        'alasan' => [
            'required' => 'Alasan izin harus diisi',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all izin with guru data
     */
    public function getAllIzin()
    {
        return $this->select('
            izin_guru.*,
            guru.nama_lengkap,
            guru.nip,
            mata_pelajaran.nama_mapel,
            users.username as disetujui_oleh_username
        ')
        ->join('guru', 'guru.id = izin_guru.guru_id')
        ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
        ->join('users', 'users.id = izin_guru.disetujui_oleh', 'left')
        ->orderBy('izin_guru.tanggal_mulai', 'DESC')
        ->orderBy('izin_guru.status', 'ASC')
        ->findAll();
    }

    /**
     * Get izin by guru
     */
    public function getByGuru($guruId)
    {
        return $this->select('izin_guru.*, users.username as disetujui_oleh_username')
            ->join('users', 'users.id = izin_guru.disetujui_oleh', 'left')
            ->where('guru_id', $guruId)
            ->orderBy('tanggal_mulai', 'DESC')
            ->findAll();
    }

    /**
     * Get izin by status
     */
    public function getByStatus($status)
    {
        return $this->select('izin_guru.*, guru.nama_lengkap, guru.nip, mata_pelajaran.nama_mapel')
            ->join('guru', 'guru.id = izin_guru.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->where('izin_guru.status', $status)
            ->orderBy('izin_guru.tanggal_mulai', 'DESC')
            ->findAll();
    }

    /**
     * Get pending approval for Wakakur
     */
    public function getPendingApproval()
    {
        return $this->select('izin_guru.*, guru.nama_lengkap, guru.nip, mata_pelajaran.nama_mapel')
            ->join('guru', 'guru.id = izin_guru.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->where('izin_guru.status', 'pending')
            ->orderBy('izin_guru.tanggal_mulai', 'ASC')
            ->findAll();
    }

    /**
     * Get pending izin count (for dashboard badge)
     */
    public function getPendingCount()
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    /**
     * Approve izin
     * Will create absensi_guru records for date range
     */
    public function approveIzin($izinId, $userId, $catatan = null)
    {
        $data = [
            'status'              => 'disetujui',
            'disetujui_oleh'      => $userId,
            'tanggal_disetujui'   => date('Y-m-d H:i:s'),
            'catatan_persetujuan' => $catatan,
        ];

        return $this->update($izinId, $data);
    }

    /**
     * Reject izin
     */
    public function rejectIzin($izinId, $userId, $catatan = null)
    {
        $data = [
            'status'              => 'ditolak',
            'disetujui_oleh'      => $userId,
            'tanggal_disetujui'   => date('Y-m-d H:i:s'),
            'catatan_persetujuan' => $catatan,
        ];

        return $this->update($izinId, $data);
    }

    /**
     * Check if izin exists for guru on date range
     */
    public function isIzinExist($guruId, $tanggalMulai, $tanggalSelesai)
    {
        return $this->where('guru_id', $guruId)
            ->groupStart()
                ->where('tanggal_mulai <=', $tanggalSelesai)
                ->where('tanggal_selesai >=', $tanggalMulai)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    /**
     * Get approved izin for specific date
     */
    public function getApprovedIzinByDate($tanggal)
    {
        return $this->select('izin_guru.*, guru.nama_lengkap, guru.nip')
            ->join('guru', 'guru.id = izin_guru.guru_id')
            ->where('izin_guru.status', 'disetujui')
            ->where('izin_guru.tanggal_mulai <=', $tanggal)
            ->where('izin_guru.tanggal_selesai >=', $tanggal)
            ->findAll();
    }

    /**
     * Get statistics by jenis izin
     */
    public function getStatistics($guruId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "disetujui" THEN 1 ELSE 0 END) as disetujui,
            SUM(CASE WHEN status = "ditolak" THEN 1 ELSE 0 END) as ditolak,
            SUM(CASE WHEN jenis_izin = "izin" THEN 1 ELSE 0 END) as jenis_izin,
            SUM(CASE WHEN jenis_izin = "sakit" THEN 1 ELSE 0 END) as jenis_sakit,
            SUM(CASE WHEN jenis_izin = "cuti" THEN 1 ELSE 0 END) as jenis_cuti,
            SUM(CASE WHEN jenis_izin = "dinas_luar" THEN 1 ELSE 0 END) as jenis_dinas_luar,
            SUM(CASE WHEN jenis_izin = "lainnya" THEN 1 ELSE 0 END) as jenis_lainnya
        ');

        if ($guruId) {
            $builder->where('guru_id', $guruId);
        }

        if ($startDate) {
            $builder->where('tanggal_mulai >=', $startDate);
        }

        if ($endDate) {
            $builder->where('tanggal_selesai <=', $endDate);
        }

        return $builder->first();
    }

    /**
     * Get izin with date range filter (for reports)
     */
    public function getWithDateRange($startDate, $endDate, $guruId = null)
    {
        $builder = $this->select('izin_guru.*, guru.nama_lengkap, guru.nip, mata_pelajaran.nama_mapel')
            ->join('guru', 'guru.id = izin_guru.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->where('izin_guru.tanggal_mulai >=', $startDate)
            ->where('izin_guru.tanggal_selesai <=', $endDate);

        if ($guruId) {
            $builder->where('izin_guru.guru_id', $guruId);
        }

        return $builder->orderBy('izin_guru.tanggal_mulai', 'ASC')
            ->findAll();
    }

    /**
     * Get izin details with guru info
     */
    public function getDetail($izinId)
    {
        return $this->select('
            izin_guru.*,
            guru.nama_lengkap,
            guru.nip,
            mata_pelajaran.nama_mapel,
            users.username as disetujui_oleh_username
        ')
        ->join('guru', 'guru.id = izin_guru.guru_id')
        ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
        ->join('users', 'users.id = izin_guru.disetujui_oleh', 'left')
        ->where('izin_guru.id', $izinId)
        ->first();
    }

    /**
     * Calculate total days of leave
     */
    public function calculateTotalDays($tanggalMulai, $tanggalSelesai)
    {
        $start = strtotime($tanggalMulai);
        $end = strtotime($tanggalSelesai);
        
        $diff = $end - $start;
        $days = floor($diff / (60 * 60 * 24)) + 1; // +1 to include both start and end date
        
        return $days;
    }
}
