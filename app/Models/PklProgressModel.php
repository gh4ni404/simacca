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
        'instruktur_verified_by', 'instruktur_verified_at',
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

    public function getBySiswaIds(array $siswaIds): array
    {
        return $this->select('pkl_progress.*, pkl_tasks.judul AS nama_task, pkl_tasks.siswa_id, siswa.nama_lengkap AS nama_siswa, kelas.nama_kelas, pkl_categories.nama AS kategori_nama')
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
            ->join('siswa', 'siswa.id = pkl_tasks.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->whereIn('pkl_tasks.siswa_id', $siswaIds)
            ->orderBy('pkl_progress.tanggal', 'DESC')
            ->orderBy('pkl_progress.created_at', 'DESC')
            ->findAll();
    }

    public function getStatsBySiswaIds(array $siswaIds): array
    {
        $row = $this->select('COUNT(pkl_progress.id) AS total')
            ->selectSum('CASE WHEN pkl_progress.status = \'submitted\' THEN 1 ELSE 0 END', 'submitted')
            ->selectSum('CASE WHEN pkl_progress.status = \'approved\' THEN 1 ELSE 0 END', 'approved')
            ->selectSum('CASE WHEN pkl_progress.status = \'revision\' THEN 1 ELSE 0 END', 'revision')
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
            ->whereIn('pkl_tasks.siswa_id', $siswaIds)
            ->first();

        return [
            'total'     => (int) ($row['total'] ?? 0),
            'submitted' => (int) ($row['submitted'] ?? 0),
            'approved'  => (int) ($row['approved'] ?? 0),
            'revision'  => (int) ($row['revision'] ?? 0),
        ];
    }

    public function getPendingBySiswaIds(array $siswaIds): array
    {
        return $this->select('pkl_progress.*, pkl_tasks.judul AS task_judul, pkl_tasks.siswa_id, siswa.nama_lengkap AS nama_siswa, siswa.nis, kelas.nama_kelas, users.profile_photo, pkl_categories.nama AS kategori_nama')
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
            ->join('siswa', 'siswa.id = pkl_tasks.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users', 'users.id = siswa.user_id', 'left')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->whereIn('pkl_tasks.siswa_id', $siswaIds)
            ->where('pkl_progress.status', 'submitted')
            ->orderBy('pkl_progress.tanggal', 'ASC')
            ->orderBy('pkl_progress.created_at', 'ASC')
            ->findAll();
    }

    public function getByTask($taskId)
    {
        return $this->where('task_id', $taskId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('created_at', 'ASC')
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
                       SUM(CASE WHEN pp.status = 'verified_by_instruktur' THEN 1 ELSE 0 END) AS verified_by_instruktur,
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

    public function getGroupedBySiswaForPembimbing(): array
    {
        $userId = session()->get('user_id');
        $guruModel = new \App\Models\GuruModel();
        $guru = $guruModel->getByUserId($userId);
        $guruId = $guru['id'] ?? 0;

        $rawData = $this->select('
                pkl_progress.*,
                pkl_tasks.judul AS nama_task,
                pkl_tasks.siswa_id,
                siswa.nama_lengkap AS nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                pkl_categories.nama AS kategori_nama,
                users.profile_photo,
                tempat_pkl.nama_perusahaan
            ')
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
            ->join('siswa', 'siswa.id = pkl_tasks.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users', 'users.id = siswa.user_id', 'left')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->join('siswa_pkl', 'siswa_pkl.siswa_id = siswa.id AND siswa_pkl.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL', 'left')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->orderBy('FIELD(pkl_progress.status, "revision", "submitted", "verified_by_instruktur", "draft", "approved")', '', false)
            ->orderBy('pkl_progress.tanggal', 'ASC')
            ->findAll();

        $grouped = [];
        $stats = [
            'total_siswa' => 0,
            'total_progress' => 0,
            'draft' => 0,
            'submitted' => 0,
            'verified_by_instruktur' => 0,
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
                    'profile_photo' => $row['profile_photo'],
                    'nama_perusahaan' => $row['nama_perusahaan'],
                    'progress' => [],
                    'pending_count' => 0,
                ];
                $stats['total_siswa']++;
            }

            $grouped[$siswaId]['progress'][] = $row;

            if ($row['status'] === 'submitted' || $row['status'] === 'draft' || $row['status'] === 'verified_by_instruktur') {
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
