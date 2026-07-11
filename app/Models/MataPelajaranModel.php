<?php

namespace App\Models;

use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table            = 'mata_pelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_mapel',
        'nama_mapel',
        'kategori',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'kode_mapel' => 'required|min_length[3]|max_length[10]|is_unique[mata_pelajaran.kode_mapel]',
        'nama_mapel' => 'required|min_length[3]|max_length[100]',
        'kategori' => 'required|in_list[umum,kejuruan]'
    ];

    protected $validationMessages = [
        'kode_mapel' => [
            'required' => 'Kode mata pelajaran wajib diisi',
            'is_unique' => 'Kode mata pelajaran sudah digunakan',
            'min_length' => 'Kode minimal 3 karakter',
            'max_length' => 'Kode maksimal 10 karakter'
        ],
        'nama_mapel' => [
            'required' => 'Nama mata pelajaran wajib diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'kategori' => [
            'required' => 'Kategori wajib dipilih',
            'in_list' => 'Kategori harus Umum atau Kejuruan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set created_at field before insert
     */
    protected function setCreatedAt(array $data)
    {
        if (isset($data['data'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get all mata pelajaran with pagination and search
     */
    public function getAllMapel($perPage = 50, $search = null)
    {
        // Use Model methods so paginate() is available on $this
        if ($search) {
            $this->groupStart()
                ->like('kode_mapel', $search)
                ->orLike('nama_mapel', $search)
                ->orLike('kategori', $search)
                ->groupEnd();
        }

        return [
            'mapel' => $this->orderBy('kategori', 'ASC')
                ->orderBy('nama_mapel', 'ASC')
                ->paginate($perPage),
            'pager' => $this->pager
        ];
    }

    /**
     * Get mata pelajaran by kategori
     */
    public function getByKategori($kategori)
    {
        return $this->where('kategori', $kategori)
            ->orderBy('nama_mapel', 'ASC')
            ->findAll();
    }

    /**
     * Get total count by kategori
     */
    public function countByKategori()
    {
        return $this->select('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all mata pelajaran for dropdown
     */
    public function getAllMapelForDropdown()
    {
        $mapel = $this->orderBy('nama_mapel', 'ASC')->findAll();
        $dropdown = [];

        foreach ($mapel as $item) {
            $dropdown[$item['id']] = $item['kode_mapel'] . ' - ' . $item['nama_mapel'];
        }

        return $dropdown;
    }

    /**
     * Get list mapel untuk dropdown
     */
    public function getListMapel()
    {
        $mapel = $this->orderBy('nama_mapel')->findAll();
        $list = [];

        foreach ($mapel as $m) {
            $list[$m['id']] = $m['kode_mapel'] . ' - ' . $m['nama_mapel'];
        }
        return $list;
    }
}
