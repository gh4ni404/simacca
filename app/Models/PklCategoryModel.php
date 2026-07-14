<?php

namespace App\Models;

use CodeIgniter\Model;

class PklCategoryModel extends Model
{
    protected $table            = 'pkl_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getDropdown()
    {
        return $this->orderBy('nama', 'ASC')->findAll();
    }
}
