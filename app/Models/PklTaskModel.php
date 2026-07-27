<?php

namespace App\Models;

use CodeIgniter\Model;

class PklTaskModel extends Model
{
    protected $table            = 'pkl_tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['siswa_id', 'kategori_id', 'judul', 'status', 'estimasi', 'langkah_kerja', 'instruktur_verified_by', 'instruktur_verified_at', 'pembimbing_verified_by', 'pembimbing_verified_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'siswa_id' => 'required|numeric',
        'judul'    => 'required|min_length[3]|max_length[255]',
    ];

    public function getBySiswa($siswaId)
    {
        return $this->where('siswa_id', $siswaId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getWithProgress($taskId)
    {
        return $this->select('pkl_tasks.*, pkl_categories.nama AS kategori_nama')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->where('pkl_tasks.id', $taskId)
            ->first();
    }

    public function getBySiswaWithCategory($siswaId)
    {
        return $this->select('pkl_tasks.*, pkl_categories.nama AS kategori_nama')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->where('pkl_tasks.siswa_id', $siswaId)
            ->orderBy('pkl_tasks.created_at', 'DESC')
            ->findAll();
    }

    public function getActiveBySiswa($siswaId)
    {
        return $this->where('siswa_id', $siswaId)
            ->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getInactiveOrDeletedBySiswaAndKategori(int $siswaId, ?int $kategoriId): ?array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * FROM pkl_tasks
                WHERE siswa_id = ? AND kategori_id <=> ?
                AND (status != 'active' OR deleted_at IS NOT NULL)
                LIMIT 1";

        return $db->query($sql, [$siswaId, $kategoriId])->getRowArray() ?: null;
    }

    public function getAllWithSiswa(array $filters = []): array
    {
        $this->select('
                pkl_tasks.*,
                s.nama_lengkap, s.nis, k.nama_kelas,
                pc.nama AS kategori_nama,
                ip.nama_lengkap AS nama_instruktur,
                ip.id AS instruktur_id,
                g.nama_lengkap AS nama_pembimbing,
                g.id AS guru_id
            ')
            ->join('siswa s', 's.id = pkl_tasks.siswa_id')
            ->join('kelas k', 'k.id = s.kelas_id', 'left')
            ->join('pkl_categories pc', 'pc.id = pkl_tasks.kategori_id', 'left')
            ->join('siswa_pkl sp', 'sp.siswa_id = pkl_tasks.siswa_id AND sp.tahun_ajaran = "' . get_active_tahun_ajaran() . '" AND sp.deleted_at IS NULL', 'left')
            ->join('tempat_pkl tp', 'tp.id = sp.tempat_pkl_id', 'left')
            ->join('instruktur_pkl ip', 'ip.tempat_pkl_id = tp.id AND ip.deleted_at IS NULL', 'left')
            ->join('pembimbing_pkl pp', 'pp.tempat_pkl_id = tp.id AND pp.tahun_ajaran = sp.tahun_ajaran AND pp.deleted_at IS NULL', 'left')
            ->join('guru g', 'g.id = pp.guru_id AND g.deleted_at IS NULL', 'left')
            ->orderBy('pkl_tasks.created_at', 'DESC');

        if (!empty($filters['search'])) {
            $this->groupStart()
                ->like('s.nama_lengkap', $filters['search'])
                ->orLike('s.nis', $filters['search'])
                ->orLike('pkl_tasks.judul', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $this->where('pkl_tasks.status', $filters['status']);
        }

        if (!empty($filters['kategori_id'])) {
            $this->where('pkl_tasks.kategori_id', $filters['kategori_id']);
        }

        if (!empty($filters['instruktur_id'])) {
            $this->where('ip.id', $filters['instruktur_id']);
        }

        if (!empty($filters['guru_id'])) {
            $this->where('g.id', $filters['guru_id']);
        }

        if (!empty($filters['kelas'])) {
            $this->where('k.nama_kelas', $filters['kelas']);
        }

        return $this->findAll();
    }

    public function getFilterKategoriList(): array
    {
        return $this->db->table('pkl_tasks')
            ->select('pc.id, pc.nama')
            ->join('pkl_categories pc', 'pc.id = pkl_tasks.kategori_id')
            ->where('pkl_tasks.deleted_at', null)
            ->groupBy('pc.id, pc.nama')
            ->orderBy('pc.nama', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getFilterInstrukturList(): array
    {
        return $this->db->table('pkl_tasks')
            ->select('ip.id AS instruktur_id, ip.nama_lengkap')
            ->join('siswa_pkl sp', 'sp.siswa_id = pkl_tasks.siswa_id AND sp.tahun_ajaran = "' . get_active_tahun_ajaran() . '" AND sp.deleted_at IS NULL', 'left')
            ->join('tempat_pkl tp', 'tp.id = sp.tempat_pkl_id', 'left')
            ->join('instruktur_pkl ip', 'ip.tempat_pkl_id = tp.id AND ip.deleted_at IS NULL', 'left')
            ->where('pkl_tasks.deleted_at', null)
            ->where('ip.id IS NOT NULL', null, false)
            ->groupBy('ip.id, ip.nama_lengkap')
            ->orderBy('ip.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getFilterPembimbingList(): array
    {
        return $this->db->table('pkl_tasks')
            ->select('g.id AS guru_id, g.nama_lengkap')
            ->join('siswa_pkl sp', 'sp.siswa_id = pkl_tasks.siswa_id AND sp.tahun_ajaran = "' . get_active_tahun_ajaran() . '" AND sp.deleted_at IS NULL', 'left')
            ->join('tempat_pkl tp', 'tp.id = sp.tempat_pkl_id', 'left')
            ->join('pembimbing_pkl pp', 'pp.tempat_pkl_id = tp.id AND pp.tahun_ajaran = sp.tahun_ajaran AND pp.deleted_at IS NULL', 'left')
            ->join('guru g', 'g.id = pp.guru_id AND g.deleted_at IS NULL', 'left')
            ->where('pkl_tasks.deleted_at', null)
            ->where('g.id IS NOT NULL', null, false)
            ->groupBy('g.id, g.nama_lengkap')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getFilterKelasList(): array
    {
        $data = $this->db->table('pkl_tasks')
            ->select('k.nama_kelas')
            ->join('siswa s', 's.id = pkl_tasks.siswa_id')
            ->join('kelas k', 'k.id = s.kelas_id', 'left')
            ->where('pkl_tasks.deleted_at', null)
            ->where('k.nama_kelas IS NOT NULL', null, false)
            ->groupBy('k.nama_kelas')
            ->orderBy('k.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();

        $list = [];
        foreach ($data as $item) {
            $list[] = $item['nama_kelas'];
        }

        return $list;
    }

    public function getProgressSummary($taskId)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS draft,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) AS verified,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN status = 'revision' THEN 1 ELSE 0 END) AS revision
                FROM pkl_progress
                WHERE task_id = ? AND deleted_at IS NULL";

        $result = $db->query($sql, [$taskId])->getRowArray();
        return $result ?: ['total' => 0, 'draft' => 0, 'submitted' => 0, 'verified' => 0, 'approved' => 0, 'revision' => 0];
    }

    public function getArchiveSummary(?string $startDate = null, ?string $endDate = null, ?int $siswaId = null, ?string $status = null): array
    {
        $conditions = ['pt.deleted_at IS NULL', 'pp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) {
            $conditions[] = 'pp.tanggal >= ?';
            $binds[] = $startDate;
        }
        if ($endDate) {
            $conditions[] = 'pp.tanggal <= ?';
            $binds[] = $endDate;
        }
        if ($siswaId) {
            $conditions[] = 'pt.siswa_id = ?';
            $binds[] = $siswaId;
        }
        if ($status) {
            $conditions[] = 'pp.status = ?';
            $binds[] = $status;
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    s.id AS siswa_id,
                    s.nama_lengkap,
                    s.nis,
                    k.nama_kelas,
                    tp.nama_perusahaan,
                    COUNT(DISTINCT pt.id) AS total_tasks,
                    COUNT(pp.id) AS total_progress,
                    SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS draft,
                    SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision,
                    MIN(pp.tanggal) AS tanggal_pertama,
                    MAX(pp.tanggal) AS tanggal_terakhir
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa s ON s.id = pt.siswa_id
                LEFT JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN siswa_pkl sp ON sp.siswa_id = s.id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
                LEFT JOIN tempat_pkl tp ON tp.id = sp.tempat_pkl_id
                WHERE {$whereClause}
                GROUP BY s.id, s.nama_lengkap, s.nis, k.nama_kelas, tp.nama_perusahaan
                ORDER BY s.nama_lengkap ASC";

        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function getArchiveStats(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ['pp.deleted_at IS NULL', 'pt.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) {
            $conditions[] = 'pp.tanggal >= ?';
            $binds[] = $startDate;
        }
        if ($endDate) {
            $conditions[] = 'pp.tanggal <= ?';
            $binds[] = $endDate;
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    COUNT(pp.id) AS total_entries,
                    COUNT(DISTINCT pt.siswa_id) AS total_siswa,
                    COUNT(DISTINCT pt.id) AS total_tasks,
                    SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
                    SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS total_submitted,
                    SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS total_draft,
                    SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS total_revision,
                    MIN(pp.tanggal) AS earliest_date,
                    MAX(pp.tanggal) AS latest_date
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                WHERE {$whereClause}";

        $result = $this->db->query($sql, $binds)->getRowArray();

        return $result ?: [
            'total_entries' => 0, 'total_siswa' => 0, 'total_tasks' => 0,
            'total_approved' => 0, 'total_submitted' => 0, 'total_draft' => 0, 'total_revision' => 0,
            'earliest_date' => null, 'latest_date' => null,
        ];
    }

    public function getArchiveByTempatPkl(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ['pt.deleted_at IS NULL', 'pp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) { $conditions[] = 'pp.tanggal >= ?'; $binds[] = $startDate; }
        if ($endDate) { $conditions[] = 'pp.tanggal <= ?'; $binds[] = $endDate; }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    tp.id AS tempat_pkl_id,
                    tp.nama_perusahaan,
                    tp.kota,
                    COUNT(pp.id) AS total_progress,
                    COUNT(DISTINCT pt.siswa_id) AS total_siswa,
                    SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS draft,
                    SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
                JOIN tempat_pkl tp ON tp.id = sp.tempat_pkl_id
                WHERE {$whereClause}
                GROUP BY tp.id, tp.nama_perusahaan, tp.kota
                ORDER BY tp.nama_perusahaan ASC";

        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function getArchiveByPembimbing(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ['pt.deleted_at IS NULL', 'pp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) { $conditions[] = 'pp.tanggal >= ?'; $binds[] = $startDate; }
        if ($endDate) { $conditions[] = 'pp.tanggal <= ?'; $binds[] = $endDate; }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    g.id AS guru_id,
                    g.nama_lengkap AS nama_pembimbing,
                    g.nip,
                    COUNT(pp.id) AS total_progress,
                    COUNT(DISTINCT pt.siswa_id) AS total_siswa,
                    SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS draft,
                    SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa_pkl sp ON sp.siswa_id = pt.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
                JOIN pembimbing_pkl pp2 ON pp2.tempat_pkl_id = sp.tempat_pkl_id AND pp2.tahun_ajaran = sp.tahun_ajaran
                JOIN guru g ON g.id = pp2.guru_id
                WHERE {$whereClause}
                GROUP BY g.id, g.nama_lengkap, g.nip
                ORDER BY g.nama_lengkap ASC";

        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function getArchiveByKelas(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ['pt.deleted_at IS NULL', 'pp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) { $conditions[] = 'pp.tanggal >= ?'; $binds[] = $startDate; }
        if ($endDate) { $conditions[] = 'pp.tanggal <= ?'; $binds[] = $endDate; }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    k.id AS kelas_id,
                    k.nama_kelas,
                    COUNT(pp.id) AS total_progress,
                    COUNT(DISTINCT pt.siswa_id) AS total_siswa,
                    SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS draft,
                    SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id
                JOIN siswa s ON s.id = pt.siswa_id
                JOIN kelas k ON k.id = s.kelas_id
                WHERE {$whereClause}
                GROUP BY k.id, k.nama_kelas
                ORDER BY k.nama_kelas ASC";

        return $this->db->query($sql, $binds)->getResultArray();
    }
}
