<?php

namespace App\Models;

use CodeIgniter\Model;

class PklTaskTemplateModel extends Model
{
    protected $table            = 'pkl_task_templates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['tempat_pkl_id', 'judul', 'kategori_id', 'estimasi'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'tempat_pkl_id' => 'required|numeric',
        'judul'         => 'required|min_length[3]|max_length[255]',
    ];

    public function getByTempatPkl(int $tempatPklId)
    {
        return $this->select('pkl_task_templates.*, pkl_categories.nama AS kategori_nama')
            ->join('pkl_categories', 'pkl_categories.id = pkl_task_templates.kategori_id', 'left')
            ->where('pkl_task_templates.tempat_pkl_id', $tempatPklId)
            ->orderBy('pkl_task_templates.judul', 'ASC')
            ->findAll();
    }

    public function getDropdownByTempatPkl(int $tempatPklId)
    {
        return $this->select('id, judul')
            ->where('tempat_pkl_id', $tempatPklId)
            ->orderBy('judul', 'ASC')
            ->findAll();
    }
}
