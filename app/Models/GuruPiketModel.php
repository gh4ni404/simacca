<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruPiketModel extends Model
{
    protected $table            = 'guru_piket';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
        'tahun_ajaran',
        'semester',
        'hari',
        'keterangan',
        'rincian_tugas',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
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
        'guru_id'       => 'required|integer',
        'tahun_ajaran'  => 'required',
        'semester'      => 'required|in_list[ganjil,genap]',
        'hari'          => 'required|in_list[senin,selasa,rabu,kamis,jumat,sabtu]',
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

    /**
     * Get all guru piket with guru data for a specific tahun ajaran and semester
     */
    public function getAllWithGuru(string $tahunAjaran, string $semester): array
    {
        return $this->select('guru_piket.*, guru.nama_lengkap, guru.nip, guru.jenis_kelamin, users.profile_photo')
            ->join('guru', 'guru.id = guru_piket.guru_id')
            ->join('users', 'users.id = guru.user_id', 'left')
            ->where('guru_piket.tahun_ajaran', $tahunAjaran)
            ->where('guru_piket.semester', $semester)
            ->orderBy('guru_piket.hari', 'ASC')
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get guru piket grouped by hari
     */
    public function getGroupedByHari(string $tahunAjaran, string $semester): array
    {
        $data = $this->getAllWithGuru($tahunAjaran, $semester);
        $grouped = [];

        $hariOrder = ['senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4, 'jumat' => 5, 'sabtu' => 6];

        foreach ($data as $row) {
            $hari = $row['hari'];
            if (!isset($grouped[$hari])) {
                $grouped[$hari] = [];
            }
            $grouped[$hari][] = $row;
        }

        ksort($grouped, SORT_STRING);
        uksort($grouped, function ($a, $b) use ($hariOrder) {
            return ($hariOrder[$a] ?? 99) - ($hariOrder[$b] ?? 99);
        });

        return $grouped;
    }

    /**
     * Get guru piket by hari
     */
    public function getByHari(string $hari, string $tahunAjaran, string $semester): array
    {
        return $this->select('guru_piket.*, guru.nama_lengkap, guru.nip, guru.jenis_kelamin')
            ->join('guru', 'guru.id = guru_piket.guru_id')
            ->where('guru_piket.hari', $hari)
            ->where('guru_piket.tahun_ajaran', $tahunAjaran)
            ->where('guru_piket.semester', $semester)
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Check if guru already has piket on this day, tahun ajaran, and semester
     */
    public function isGuruAssigned(int $guruId, string $hari, string $tahunAjaran, string $semester, ?int $excludeId = null): bool
    {
        $builder = $this->where('guru_id', $guruId)
            ->where('hari', $hari)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get guru piket stats for a specific tahun ajaran and semester (single query)
     */
    public function getStats(string $tahunAjaran, string $semester): array
    {
        $builder = $this->select('
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
        ')->where('tahun_ajaran', $tahunAjaran)
          ->where('semester', $semester);

        $row = $builder->first();

        $hariCount = $this->select('hari, COUNT(*) as jumlah')
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('is_active', 1)
            ->groupBy('hari')
            ->findAll();

        $hariStats = [];
        foreach ($hariCount as $r) {
            $hariStats[$r['hari']] = $r['jumlah'];
        }

        return [
            'total'     => (int) ($row['total'] ?? 0),
            'active'    => (int) ($row['active'] ?? 0),
            'hariStats' => $hariStats,
        ];
    }

    /**
     * Get guru list for dropdown (only active users)
     */
    public function getGuruDropdown(): array
    {
        $guruModel = new GuruModel();
        $guru = $guruModel->select('guru.id, guru.nama_lengkap, guru.nip')
            ->join('users', 'users.id = guru.user_id')
            ->where('users.is_active', 1)
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();

        $dropdown = [];
        foreach ($guru as $item) {
            $dropdown[$item['id']] = $item['nama_lengkap'] . ' (' . $item['nip'] . ')';
        }

        return $dropdown;
    }

    /**
     * Get available guru (not yet assigned to ANY day) for a specific tahun ajaran and semester
     */
    public function getAvailableGuru(string $hari, string $tahunAjaran, string $semester, ?int $excludeId = null): array
    {
        // Get all guru_ids who already have any assignment in this tahun ajaran & semester
        $assignedQuery = $this->select('guru_id')
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester);

        if ($excludeId) {
            $assignedQuery->where('id !=', $excludeId);
        }

        $assignedIds = array_column($assignedQuery->findAll(), 'guru_id');

        $guruModel = new GuruModel();
        $builder = $guruModel->select('guru.id, guru.nama_lengkap, guru.nip, guru.jenis_kelamin, users.profile_photo')
            ->join('users', 'users.id = guru.user_id')
            ->where('users.is_active', 1)
            ->orderBy('guru.nama_lengkap', 'ASC');

        if (!empty($assignedIds)) {
            $builder->whereNotIn('guru.id', $assignedIds);
        }

        return $builder->findAll();
    }
}
