<?php

namespace App\Models;

use CodeIgniter\Model;

class InstrukturPklModel extends Model
{
    protected $table            = 'instruktur_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tempat_pkl_id',
        'user_id',
        'nama_lengkap',
        'email',
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
        'tempat_pkl_id' => 'required|numeric',
        'user_id'       => 'required|numeric',
        'nama_lengkap'  => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getByTempatPkl(int $tempatPklId): ?array
    {
        return $this->where('tempat_pkl_id', $tempatPklId)->first();
    }

    public function getInstrukturWithTempatPkl(): array
    {
        return $this->select('instruktur_pkl.*, tempat_pkl.nama_perusahaan')
            ->join('tempat_pkl', 'tempat_pkl.id = instruktur_pkl.tempat_pkl_id', 'left')
            ->orderBy('instruktur_pkl.nama_lengkap', 'ASC')
            ->findAll();
    }
}
