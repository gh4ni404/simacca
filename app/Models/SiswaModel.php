<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas_id',
        'tahun_ajaran',
        'created_at'
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
        'nis'               => 'required',
        'nama_lengkap'      => 'required|min_length[3]',
        'jenis_kelamin'     => 'required|in_list[L,P]',
        'tahun_ajaran'      => 'required',
        'user_id'           => 'required|numeric|is_unique[siswa.user_id]'
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
     * Get all siswa with user and kelas data
     */
    public function getAllSiswa($status = 'active', $limit = null, $offset = 0, $kelasId = null, $tahunAjaran = null)
    {
        $this->select('siswa.*, users.username, users.email, users.is_active, users.profile_photo, kelas.nama_kelas')
            ->join('users', 'users.id = siswa.user_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left');

        if ($status === 'active') {
            $this->where('users.is_active', 1);
        } elseif ($status === 'inactive') {
            $this->where('users.is_active', 0);
        }

        if ($kelasId) {
            $this->where('siswa.kelas_id', $kelasId);
        }

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        $this->orderBy('siswa.nama_lengkap', 'ASC');

        if ($limit !== null) {
            $this->limit($limit, $offset);
        }

        return $this->findAll();
    }

    /**
     * Get siswa by user_id
     */
    public function getByUserId($userId)
    {
        return $this->select('siswa.*, kelas.nama_kelas, users.username, users.is_active, users.profile_photo')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users', 'users.id = siswa.user_id', 'left')
            ->where('siswa.user_id', $userId)
            ->first();
    }

    /**
     * Get siswa by kelas
     */
    public function getByKelas($kelasId, $tahunAjaran = null)
    {
        $this->where('siswa.kelas_id', $kelasId)
            ->join('users', 'users.id = siswa.user_id')
            ->select('siswa.*, users.username')
            ->orderBy('siswa.nama_lengkap', 'ASC');

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        return $this->findAll();
    }

    /**
     * Get Jumlah siswa aktif per kelas
     */
    public function getCountByKelas($tahunAjaran = null)
    {
        $this->select('kelas.nama_kelas, COUNT(siswa.id) as jumlah_siswa')
            ->join('kelas', 'kelas.id = siswa.kelas_id')
            ->join('users', 'users.id = siswa.user_id')
            ->where('users.is_active', 1);

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        return $this->groupBy('siswa.kelas_id')->findAll();
    }

    public function countActive($tahunAjaran = null)
    {
        $this->join('users', 'users.id = siswa.user_id')
            ->where('users.is_active', 1);

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        return $this->countAllResults();
    }

    public function countInactive($tahunAjaran = null)
    {
        $this->join('users', 'users.id = siswa.user_id')
            ->where('users.is_active', 0);

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        return $this->countAllResults();
    }

    public function searchSiswa($keyword, $status = 'active', $limit = null, $offset = 0, $kelasId = null, $tahunAjaran = null)
    {
        $this->select('siswa.*, users.username, users.email, users.is_active, users.profile_photo, kelas.nama_kelas')
            ->join('users', 'users.id = siswa.user_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->groupStart()
                ->like('siswa.nama_lengkap', $keyword)
                ->orLike('siswa.nis', $keyword)
            ->groupEnd();

        if ($status === 'active') {
            $this->where('users.is_active', 1);
        } elseif ($status === 'inactive') {
            $this->where('users.is_active', 0);
        }

        if ($kelasId) {
            $this->where('siswa.kelas_id', $kelasId);
        }

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        $this->orderBy('siswa.nama_lengkap', 'ASC');

        if ($limit !== null) {
            $this->limit($limit, $offset);
        }

        return $this->findAll();
    }

    public function countSearch($keyword, $status = 'active', $kelasId = null, $tahunAjaran = null)
    {
        $this->select('siswa.id')
            ->join('users', 'users.id = siswa.user_id')
            ->groupStart()
                ->like('siswa.nama_lengkap', $keyword)
                ->orLike('siswa.nis', $keyword)
            ->groupEnd();

        if ($status === 'active') {
            $this->where('users.is_active', 1);
        } elseif ($status === 'inactive') {
            $this->where('users.is_active', 0);
        }

        if ($kelasId) {
            $this->where('siswa.kelas_id', $kelasId);
        }

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        return $this->countAllResults();
    }

    /**
     * Get siswa with wali kelas
     */
    public function getSiswaWithWaliKelas($siswaId = null)
    {
        $builder = $this->select('siswa.*, kelas.nama_kelas, guru.nama_lengkap as wali_kelas')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left');

        if ($siswaId) {
            return $builder->where('siswa.id', $siswaId)->first();
        }
        return $builder->findAll();
    }

    public function getCountKelasById($kelasId, $status = 'active', $tahunAjaran = null) {
        $this->join('users', 'users.id = siswa.user_id')
            ->where('siswa.kelas_id', $kelasId);
        if ($status === 'active') {
            $this->where('users.is_active', 1);
        } elseif ($status === 'inactive') {
            $this->where('users.is_active', 0);
        }
        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }
        return $this->countAllResults();
    }

    /**
     * Get all siswa IDs matching filters (no pagination)
     */
    public function getAllSiswaIds($status = 'active', $kelasId = null, $tahunAjaran = null, $keyword = null)
    {
        $this->select('siswa.id')
            ->join('users', 'users.id = siswa.user_id');

        if ($status === 'active') {
            $this->where('users.is_active', 1);
        } elseif ($status === 'inactive') {
            $this->where('users.is_active', 0);
        }

        if ($kelasId) {
            $this->where('siswa.kelas_id', $kelasId);
        }

        if ($tahunAjaran) {
            $this->where('siswa.tahun_ajaran', $tahunAjaran);
        }

        if ($keyword) {
            $this->groupStart()
                ->like('siswa.nama_lengkap', $keyword)
                ->orLike('siswa.nis', $keyword)
            ->groupEnd();
        }

        $this->orderBy('siswa.nama_lengkap', 'ASC');

        return array_column($this->findAll(), 'id');
    }

    /**
     * Get siswa with kelas data
     */
    public function getSiswaWithKelas($siswaId = null)
    {
        $builder = $this->select('siswa.*, kelas.nama_kelas, users.username, users.email, users.is_active, users.profile_photo')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users', 'users.id = siswa.user_id', 'left');

        if ($siswaId) {
            return $builder->where('siswa.id', $siswaId)->first();
        }

        return $builder->findAll();
    }
}
