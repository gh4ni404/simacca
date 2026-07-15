<?php

namespace App\Models;

use CodeIgniter\Model;

class PklProgressModel extends Model
{
    protected $table            = 'pkl_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'task_id', 'tanggal', 'deskripsi', 'langkah_kerja', 'foto', 'status',
        'catatan_pembimbing', 'catatan_instruktur',
        'verified_by', 'verified_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'task_id'  => 'required|numeric',
        'tanggal'  => 'required|valid_date',
        'deskripsi' => 'required|min_length[3]',
    ];

    public function getByTask($taskId)
    {
        return $this->where('task_id', $taskId)
            ->orderBy('tanggal', 'ASC')
            ->findAll();
    }

    public function getByTanggal($siswaId, string $tanggal)
    {
        return $this->select('pkl_progress.*, pkl_tasks.judul AS nama_task, pkl_tasks.siswa_id, pkl_categories.nama AS kategori_nama')
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->where('pkl_tasks.siswa_id', $siswaId)
            ->where('pkl_progress.tanggal', $tanggal)
            ->orderBy('pkl_progress.created_at', 'ASC')
            ->findAll(); // Mengembalikan data dalam bentuk array (sesuai setelan returnType model)
    }

    public function getTodayProgress($siswaId)
    {
        return $this->getByTanggal($siswaId, date('Y-m-d'));
    }

    public function getTimeline($siswaId, int $limit = 30)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT pp.tanggal,
                       COUNT(*) AS total_aktivitas,
                       SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                       SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                       SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision,
                       SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS draft
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                WHERE pt.siswa_id = ? AND pp.deleted_at IS NULL AND pt.deleted_at IS NULL
                GROUP BY pp.tanggal
                ORDER BY pp.tanggal DESC
                LIMIT ?";
        return $db->query($sql, [$siswaId, $limit])->getResultArray();
    }

    public function getByPembimbing(?string $startDate = null, ?string $endDate = null): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT pp.*, pt.judul AS nama_task, pt.siswa_id,
                       s.nama_lengkap AS nama_siswa, s.nis, k.nama_kelas,
                       pc.nama AS kategori_nama
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa s ON s.id = pt.siswa_id
                JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
                JOIN siswa_pkl sp ON sp.siswa_id = s.id
                JOIN pembimbing_pkl pp2 ON pp2.tempat_pkl_id = sp.tempat_pkl_id AND pp2.tahun_ajaran = sp.tahun_ajaran
                WHERE pp2.guru_id = ? AND pp.deleted_at IS NULL AND pt.deleted_at IS NULL";
        $binds = [$this->getGuruId()];

        if ($startDate) {
            $sql .= ' AND pp.tanggal >= ?';
            $binds[] = $startDate;
        }
        if ($endDate) {
            $sql .= ' AND pp.tanggal <= ?';
            $binds[] = $endDate;
        }

        $sql .= ' ORDER BY pp.tanggal DESC, pp.created_at DESC';
        return $db->query($sql, $binds)->getResultArray();
    }

    private function getGuruId(): int
    {
        $userId = session()->get('user_id');
        $guruModel = new \App\Models\GuruModel();
        $guru = $guruModel->getByUserId($userId);
        return $guru['id'] ?? 0;
    }

    public function getGroupedBySiswaForPembimbing(): array
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');
        $guruModel = new \App\Models\GuruModel();
        $guru = $guruModel->getByUserId($userId);
        $guruId = $guru['id'] ?? 0;

        $sql = "SELECT pp.*, pt.judul AS nama_task, pt.siswa_id,
                       s.nama_lengkap AS nama_siswa, s.nis, k.nama_kelas,
                       pc.nama AS kategori_nama
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa s ON s.id = pt.siswa_id
                JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN pkl_categories pc ON pc.id = pt.kategori_id
                JOIN siswa_pkl sp ON sp.siswa_id = s.id
                JOIN pembimbing_pkl pp2 ON pp2.tempat_pkl_id = sp.tempat_pkl_id AND pp2.tahun_ajaran = sp.tahun_ajaran
                WHERE pp2.guru_id = ? AND pp.deleted_at IS NULL AND pt.deleted_at IS NULL
                ORDER BY s.nama_lengkap, pp.tanggal DESC";

        $rawData = $db->query($sql, [$guruId])->getResultArray();

        $grouped = [];
        $stats = [
            'total_siswa' => 0,
            'total_progress' => 0,
            'draft' => 0,
            'submitted' => 0,
            'approved' => 0,
            'revision' => 0,
        ];

        foreach ($rawData as $row) {
            $siswaId = $row['siswa_id'];
            $stats['total_progress']++;
            $stats[$row['status']] = ($stats[$row['status']] ?? 0) + 1;

            if (!isset($grouped[$siswaId])) {
                $grouped[$siswaId] = [
                    'siswa_id' => $siswaId,
                    'nama_siswa' => $row['nama_siswa'],
                    'nis' => $row['nis'],
                    'nama_kelas' => $row['nama_kelas'],
                    'progress' => [],
                    'pending_count' => 0,
                ];
                $stats['total_siswa']++;
            }

            $grouped[$siswaId]['progress'][] = $row;

            if ($row['status'] === 'submitted' || $row['status'] === 'draft') {
                $grouped[$siswaId]['pending_count']++;
            }
        }

        uasort($grouped, fn($a, $b) => $b['pending_count'] <=> $a['pending_count']);

        return [
            'grouped' => array_values($grouped),
            'stats' => $stats,
        ];
    }
}
