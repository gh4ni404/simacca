<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaPklModel extends Model
{
    protected $table            = 'siswa_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id',
        'tempat_pkl_id',
        'pembimbing_pkl_id',
        'tahun_ajaran',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [
        'siswa_id'          => 'required|numeric',
        'tempat_pkl_id'     => 'required|numeric',
        'pembimbing_pkl_id' => 'permit_empty|numeric',
        'tahun_ajaran'      => 'required|regex_match[/\d{4}\/\d{4}/]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getAllSiswaPkl($filters = [])
    {
        $builder = $this->select('
                siswa_pkl.*,
                siswa.nama_lengkap AS nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota AS kota_perusahaan,
                pembimbing_pkl.guru_id,
                guru.nama_lengkap AS nama_pembimbing
            ')
            ->join('siswa', 'siswa.id = siswa_pkl.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id', 'left')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id', 'left')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC');

        if (!empty($filters['tahun_ajaran'])) {
            $builder->where('siswa_pkl.tahun_ajaran', $filters['tahun_ajaran']);
        }

        if (!empty($filters['tempat_pkl_id'])) {
            $builder->where('siswa_pkl.tempat_pkl_id', $filters['tempat_pkl_id']);
        }

        if (!empty($filters['kelas'])) {
            $builder->where('kelas.nama_kelas', $filters['kelas']);
        }

        if (!empty($filters['guru_id'])) {
            $builder->where('guru.id', $filters['guru_id']);
        }

        return $builder->findAll();
    }

    public function getBySiswaAndTahun($siswaId, $tahunAjaran)
    {
        return $this->where('siswa_id', $siswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();
    }

    public function getFilterTempatPklList()
    {
        $data = $this->db->table('siswa_pkl')
            ->select('tempat_pkl.id, tempat_pkl.nama_perusahaan')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id')
            ->where('siswa_pkl.deleted_at', null)
            ->groupBy('tempat_pkl.id')
            ->orderBy('tempat_pkl.nama_perusahaan', 'ASC')
            ->get()
            ->getResultArray();

        return $data;
    }

    public function getFilterKelasList()
    {
        $data = $this->db->table('siswa_pkl')
            ->select('kelas.nama_kelas')
            ->join('siswa', 'siswa.id = siswa_pkl.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('siswa_pkl.deleted_at', null)
            ->where('kelas.nama_kelas IS NOT NULL')
            ->groupBy('kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $list = [];
        foreach ($data as $item) {
            $list[] = $item['nama_kelas'];
        }

        return $list;
    }

    public function getFilterPembimbingList()
    {
        $data = $this->db->table('siswa_pkl')
            ->select('guru.id AS guru_id, guru.nama_lengkap, guru.nip')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id AND guru.deleted_at IS NULL')
            ->where('siswa_pkl.deleted_at', null)
            ->groupBy('guru.id, guru.nama_lengkap, guru.nip')
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        return $data;
    }

    public function getTahunAjaranList()
    {
        $data = $this->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'DESC')
            ->findAll();

        $list = [];
        foreach ($data as $item) {
            $list[] = $item['tahun_ajaran'];
        }

        return $list;
    }
}
