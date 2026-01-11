<?php

namespace App\Models;

use CodeIgniter\Model;

class IzinSiswaModel extends Model
{
    protected $table            = 'izin_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id',
        'tanggal',
        'jenis_izin',
        'alasan',
        'berkas',
        'status',
        'disetujui_oleh',
        'catatan'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'siswa_id'      => 'required|numeric',
        'tanggal'       => 'required|valid_date',
        'jenis_izin'    => 'required|in_list[Sakit,Izin,sakit,izin]',
        'alasan'        => 'required',
        'status'        => 'required|in_list[pending,disetujui,ditolak]',
    ];
    protected $validationMessages   = [];
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
     * Get all izin dengan data siswa
     */
    public function getAllIzin()
    {
        return $this->select('izin_siswa.*,
                            siswa.nama_lengkap,
                            siswa.nis,
                            kelas.nama_kelas')
            ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->orderBy('izin_siswa.tanggal', 'DESC')
            ->orderBy('izin_siswa.status', 'ASC')
            ->findAll();
    }

    /**
     * Get izin by siswa
     */
    public function getbySiswa($siswaId)
    {
        return $this->where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Get izin by kelas
     */
    public function getByKelas($kelasId, $status = null)
    {
        $builder = $this->select('izin_siswa.*, siswa.nama_lengkap, siswa.nis')
            ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
            ->where('siswa.kelas_id', $kelasId)
            ->orderBy('izin_siswa.tanggal', 'DESC');

        if ($status) {
            $builder->where('izin_siswa.status', $status);
        }

        return $builder->findAll();
    }

    /**
     * Get izin by status
     */
    public function getByStatus($status)
    {
        return $this->select('izin_siswa.*, siswa.nama_lengkap, siswa.nis')
            ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
            ->where('izin_siswa.status', $status)
            ->orderBy('izin_siswa.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Get izin yang perlu approval wali kelas
     */
    public function getPendingApproval($kelasId)
    {
        return $this->select('izin_siswa.*, siswa.nama_lengkap, siswa.nis')
            ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
            ->where('siswa.kelas_id', $kelasId)
            ->where('izin_siswa.status', 'pending')
            ->orderBy('izin_siswa.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Approve izin
     */
    public function approveIzin($izinId, $userId, $catatan = null)
    {
        return $this->update($izinId, [
            'status'            => 'disetujui',
            'disetujui_oleh'    => $userId,
            'catatan'           => $catatan,
        ]);
    }

    /**
     * Reject Izin
     */
    public function rejectIzin($izinId, $userId, $catatan = null)
    {
        return $this->update($izinId, [
            'status'            => 'ditolak',
            'disetujui_oleh'    => $userId,
            'catatan'           => $catatan,
        ]);
    }

    /**
     * Cek apakah siswa sudah mengajukan izin di tanggal tertentu
     */
    public function isIzinExist($siswaId, $tanggal)
    {
        return $this->where('siswa_id', $siswaId)
            ->where('tanggal', $tanggal)
            ->countAllResults() > 0;
    }

    /**
     * Get izin yang sudah disetujui untuk tanggal tertentu
     */
    public function getApprovedIzinByDate($tanggal, $kelasId) {
        $builder = $this->where('tanggal', $tanggal)
        ->where('status', 'disetujui')
        ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
        ->select('izin_siswa.*, siswa.nama_lengkap, siswa.nis, siswa.kelas_id');
        
        if ($kelasId) {
            $builder->where('siswa.kelas_id', $kelasId);
        }

        return $builder->findAll();
    }

    /**
     * Get Pending Izin Request
     */
    public function getPendingIzin($limit = 5) {
        return $this->select('izin_siswa.*, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
        ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
        ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
        ->where('izin_siswa.status', 'pending')
        ->orderBy('izin_siswa.tanggal', 'DESC')
        ->limit($limit)
        ->findAll();
    }
}
