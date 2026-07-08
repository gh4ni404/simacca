<?php

namespace App\Models;

use CodeIgniter\Model;

class TempatPklModel extends Model
{
    protected $table            = 'tempat_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_perusahaan',
        'alamat',
        'kota',
        'kontak',
        'telepon',
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
        'nama_perusahaan' => 'required|min_length[3]',
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

    public function getListTempatPkl()
    {
        $data = $this->orderBy('nama_perusahaan', 'ASC')->findAll();

        $dropdown = [];
        foreach ($data as $item) {
            $dropdown[$item['id']] = $item['nama_perusahaan'] . ' (' . $item['kota'] . ')';
        }

        return $dropdown;
    }
}
