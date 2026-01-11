<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table            = 'guru';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'mata_pelajaran_id',
        'is_wali_kelas',
        'kelas_id',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

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
        'nip'               => 'required',
        'nama_lengkap'      => 'required|min_length[3]',
        'jenis_kelamin'    => 'required|in_list[L,P]',
        'user_id'           => 'required|numeric|is_unique[guru.user_id]',
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
     * Get all guru with user data
     */
    public function getAllGuru()
    {
        return $this->select('guru.*, users.username, users.email, users.is_active, users.role, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('users', 'users.id = guru.user_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->join('kelas', 'kelas.id = guru.kelas_id', 'left')
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get guru by user_id
     */
    public function getByUserId($userId)
    {
        return $this->select('guru.*, users.username, users.email, users.is_active, mata_pelajaran.nama_mapel, mata_pelajaran.kode_mapel')
            ->join('users', 'users.id = guru.user_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->where('guru.user_id', $userId)
            ->first();
    }

    /**
     * Get guru dengan data mata pelajaran
     */
    public function getGuruWithMapel($guruId = null)
    {
        $builder = $this->select('guru.*, mata_pelajaran.nama_mapel, users.username')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left')
            ->join('users', 'users.id = guru.user_id');

        if ($guruId) {
            return $builder->where('guru.id', $guruId)->first();
        }

        return $builder->findAll();
    }

    /**
     * Get wali Kelas
     */
    public function getWaliKelas()
    {
        return $this->where('is_wali_kelas', 1)
            ->join('kelas', 'kelas.id = guru.kelas_id')
            ->join('users', 'users.id = guru.user_id')
            ->select('guru.*, kelas.nama_kelas, users.username')
            ->findAll();
    }

    /**
     * Get guru yang bukan wali kelas
     */
    public function getGuruNonWali()
    {
        return $this->where('is_wali_kelas', 0)
            ->join('users', 'users.id = guru.user_id')
            ->select('guru.*, users.username')
            ->findAll();
    }

    /**
     * Get guru by mata pelajaran
     */
    public function getByMataPelajaran($mapelId)
    {
        return $this->where('mata_pelajaran_id', $mapelId)->findAll();
    }

    /**
     * Get guru untuk dropdown
     */
    public function getGuruDropdown()
    {
        $guru = $this->select('id, nama_lengkap, nip')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $dropdown = [];
        foreach ($guru as $item) {
            $dropdown[$item['id']] = $item['nama_lengkap'] . ' (' . $item['nip'] . ')';
        }

        return $dropdown;
    }
}
