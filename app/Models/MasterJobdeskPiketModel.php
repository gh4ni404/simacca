<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterJobdeskPiketModel extends Model
{
    protected $table            = 'master_jobdesk_piket';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_jobdesk',
        'nama_jobdesk',
        'rincian_tugas',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'kode_jobdesk'  => 'required|min_length[3]|max_length[20]',
        'nama_jobdesk'  => 'required|min_length[3]|max_length[100]',
        'rincian_tugas' => 'required',
    ];

    /**
     * Get active master jobdesk list for dropdowns
     */
    public function getActiveDropdown(): array
    {
        $list = $this->where('is_active', 1)
            ->orderBy('nama_jobdesk', 'ASC')
            ->findAll();

        $dropdown = [];
        foreach ($list as $item) {
            $dropdown[$item['id']] = $item['nama_jobdesk'] . ' (' . $item['kode_jobdesk'] . ')';
        }

        return $dropdown;
    }
}
