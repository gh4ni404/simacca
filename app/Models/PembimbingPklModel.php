<?php

namespace App\Models;

use CodeIgniter\Model;

class PembimbingPklModel extends Model
{
    protected $table            = 'pembimbing_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
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
        'guru_id'       => 'required|numeric',
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

    public function getAllPembimbingPkl($tahunAjaran = null)
    {
        $builder = $this->select('
                pembimbing_pkl.*,
                guru.nama_lengkap AS nama_guru,
                guru.nip,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota,
                tempat_pkl.alamat
            ')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id')
            ->orderBy('pembimbing_pkl.tahun_ajaran', 'DESC')
            ->orderBy('guru.nama_lengkap', 'ASC');

        if ($tahunAjaran) {
            $builder->where('pembimbing_pkl.tahun_ajaran', $tahunAjaran);
        }

        return $builder->findAll();
    }

    public function getByTahunAjaran($tahunAjaran)
    {
        return $this->where('tahun_ajaran', $tahunAjaran)
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id')
            ->select('pembimbing_pkl.*, guru.nama_lengkap AS nama_guru, tempat_pkl.nama_perusahaan')
            ->findAll();
    }

    public function getByTempatPklAndTahun($tempatPklId, $tahunAjaran)
    {
        return $this->select('
                pembimbing_pkl.*,
                guru.nama_lengkap AS nama_guru,
                guru.nip
            ')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id')
            ->where('pembimbing_pkl.tempat_pkl_id', $tempatPklId)
            ->where('pembimbing_pkl.tahun_ajaran', $tahunAjaran)
            ->first();
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
