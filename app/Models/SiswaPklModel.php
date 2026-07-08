<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaPklModel extends Model
{
    protected $table            = 'siswa_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id',
        'tempat_pkl_id',
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
        'siswa_id'      => 'required|numeric',
        'tempat_pkl_id' => 'required|numeric',
        'tahun_ajaran'  => 'required|regex_match[/\d{4}\/\d{4}/]',
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

    public function getAllSiswaPkl($tahunAjaran = null)
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
            ->join('siswa', 'siswa.id = siswa_pkl.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id')
            ->join('pembimbing_pkl', 'pembimbing_pkl.tempat_pkl_id = siswa_pkl.tempat_pkl_id AND pembimbing_pkl.tahun_ajaran = siswa_pkl.tahun_ajaran', 'left')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id', 'left')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC');

        if ($tahunAjaran) {
            $builder->where('siswa_pkl.tahun_ajaran', $tahunAjaran);
        }

        return $builder->findAll();
    }

    public function getBySiswaAndTahun($siswaId, $tahunAjaran)
    {
        return $this->where('siswa_id', $siswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();
    }
}
