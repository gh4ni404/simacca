<?php

namespace App\Models;

use CodeIgniter\Model;

class KetuaJurusanModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    /**
     * Get all kelas by jurusan for active tahun ajaran
     */
    public function getKelasByJurusan(string $jurusan, ?string $tahunAjaran = null): array
    {
        $ta = $tahunAjaran ?? get_active_tahun_ajaran();

        return $this->select('
                kelas.*,
                guru.nama_lengkap AS nama_wali_kelas,
                COUNT(DISTINCT siswa.id) AS jumlah_siswa
            ')
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left')
            ->join('siswa', 'siswa.kelas_id = kelas.id AND siswa.deleted_at IS NULL', 'left')
            ->where('kelas.jurusan', $jurusan)
            ->where('kelas.tahun_ajaran', $ta)
            ->groupBy('kelas.id')
            ->orderBy('kelas.tingkat, kelas.nama_kelas')
            ->findAll();
    }

    /**
     * Get all siswa PKL from a jurusan (siswa in classes matching jurusan)
     */
    public function getSiswaPklByJurusan(string $jurusan, ?string $tahunAjaran = null): array
    {
        $ta = $tahunAjaran ?? get_active_tahun_ajaran();

        return $this->db->table('siswa_pkl')
            ->select('
                siswa_pkl.*,
                siswa.nama_lengkap AS nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                kelas.jurusan,
                kelas.tingkat,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota AS kota_perusahaan,
                guru.nama_lengkap AS nama_pembimbing
            ')
            ->join('siswa', 'siswa.id = siswa_pkl.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id', 'left')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id', 'left')
            ->where('kelas.jurusan', $jurusan)
            ->where('siswa_pkl.tahun_ajaran', $ta)
            ->where('siswa_pkl.deleted_at', null)
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all siswa IDs PKL by jurusan
     */
    public function getSiswaPklIdsByJurusan(string $jurusan, ?string $tahunAjaran = null): array
    {
        $data = $this->getSiswaPklByJurusan($jurusan, $tahunAjaran);
        return array_column($data, 'siswa_id');
    }

    /**
     * Get jurnal PKL (progress) for all siswa in a jurusan
     */
    public function getJurnalPklByJurusan(string $jurusan, ?string $tahunAjaran = null, array $filters = []): array
    {
        $siswaIds = $this->getSiswaPklIdsByJurusan($jurusan, $tahunAjaran);

        if (empty($siswaIds)) {
            return [];
        }

        $builder = $this->db->table('pkl_progress')
            ->select('
                pkl_progress.*,
                pkl_tasks.judul AS nama_task,
                pkl_tasks.siswa_id,
                siswa.nama_lengkap AS nama_siswa,
                siswa.nis,
                kelas.nama_kelas,
                kelas.jurusan,
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
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id', 'left')
            ->where('pkl_progress.deleted_at', null)
            ->whereIn('pkl_tasks.siswa_id', $siswaIds);

        // Apply filters
        if (!empty($filters['kelas_id'])) {
            $builder->where('kelas.id', $filters['kelas_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('pkl_progress.status', $filters['status']);
        }

        if (!empty($filters['tanggal_start'])) {
            $builder->where('pkl_progress.tanggal >=', $filters['tanggal_start']);
        }

        if (!empty($filters['tanggal_end'])) {
            $builder->where('pkl_progress.tanggal <=', $filters['tanggal_end']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('siswa.nama_lengkap', $filters['search'])
                ->orLike('siswa.nis', $filters['search'])
                ->orLike('pkl_tasks.judul', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('pkl_progress.tanggal', 'DESC')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get jurnal PKL grouped by siswa for a jurusan
     */
    public function getJurnalPklGroupedBySiswa(string $jurusan, ?string $tahunAjaran = null, array $filters = []): array
    {
        $rawData = $this->getJurnalPklByJurusan($jurusan, $tahunAjaran, $filters);

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

            if (in_array($row['status'], ['submitted', 'draft', 'verified_by_instruktur'])) {
                $grouped[$siswaId]['pending_count']++;
            }
        }

        uasort($grouped, fn($a, $b) => $b['pending_count'] <=> $a['pending_count']);

        return [
            'grouped' => array_values($grouped),
            'stats' => $stats,
        ];
    }

    /**
     * Get absensi PKL for all siswa in a jurusan
     */
    public function getAbsensiPklByJurusan(string $jurusan, ?string $tahunAjaran = null): array
    {
        $siswaIds = $this->getSiswaPklIdsByJurusan($jurusan, $tahunAjaran);

        if (empty($siswaIds)) {
            return [];
        }

        return $this->db->table('absensi_pkl')
            ->select('
                absensi_pkl.*,
                pembimbing_pkl.guru_id,
                guru.nama_lengkap AS nama_pembimbing,
                tempat_pkl.nama_perusahaan
            ')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id')
            ->join('siswa_pkl', 'siswa_pkl.pembimbing_pkl_id = pembimbing_pkl.id AND siswa_pkl.deleted_at IS NULL')
            ->where('absensi_pkl.deleted_at', null)
            ->whereIn('siswa_pkl.siswa_id', $siswaIds)
            ->groupBy('absensi_pkl.id')
            ->orderBy('absensi_pkl.tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get absensi PKL detail for a specific session, filtered by jurusan siswa
     */
    public function getAbsensiPklDetailByJurusan(int $absensiPklId, string $jurusan): array
    {
        return $this->db->table('absensi_pkl_detail')
            ->select('
                absensi_pkl_detail.*,
                siswa.nama_lengkap AS nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ')
            ->join('siswa', 'siswa.id = absensi_pkl_detail.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('absensi_pkl_detail.absensi_pkl_id', $absensiPklId)
            ->where('kelas.jurusan', $jurusan)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get stats for dashboard: total siswa PKL, tasks, progress per status
     */
    public function getDashboardStats(string $jurusan, ?string $tahunAjaran = null): array
    {
        $siswaIds = $this->getSiswaPklIdsByJurusan($jurusan, $tahunAjaran);

        $stats = [
            'total_siswa_pkl' => count($siswaIds),
            'total_tasks' => 0,
            'total_progress' => 0,
            'draft' => 0,
            'submitted' => 0,
            'verified_by_instruktur' => 0,
            'approved' => 0,
            'revision' => 0,
            'persentase_approval' => 0,
        ];

        if (empty($siswaIds)) {
            return $stats;
        }

        $placeholders = implode(',', array_fill(0, count($siswaIds), '?'));

        $sql = "SELECT
                    COUNT(DISTINCT pt.id) AS total_tasks,
                    COUNT(pp.id) AS total_progress,
                    SUM(CASE WHEN pp.status = 'draft' THEN 1 ELSE 0 END) AS draft,
                    SUM(CASE WHEN pp.status = 'submitted' THEN 1 ELSE 0 END) AS submitted,
                    SUM(CASE WHEN pp.status = 'verified_by_instruktur' THEN 1 ELSE 0 END) AS verified_by_instruktur,
                    SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN pp.status = 'revision' THEN 1 ELSE 0 END) AS revision
                FROM pkl_progress pp
                JOIN pkl_tasks pt ON pt.id = pp.task_id AND pt.deleted_at IS NULL
                WHERE pp.deleted_at IS NULL AND pt.siswa_id IN ($placeholders)";

        $result = $this->db->query($sql, $siswaIds)->getRowArray();

        if ($result) {
            $stats['total_tasks'] = (int) $result['total_tasks'];
            $stats['total_progress'] = (int) $result['total_progress'];
            $stats['draft'] = (int) $result['draft'];
            $stats['submitted'] = (int) $result['submitted'];
            $stats['verified_by_instruktur'] = (int) $result['verified_by_instruktur'];
            $stats['approved'] = (int) $result['approved'];
            $stats['revision'] = (int) $result['revision'];

            if ($stats['total_progress'] > 0) {
                $stats['persentase_approval'] = round(($stats['approved'] / $stats['total_progress']) * 100, 1);
            }
        }

        // Per-kelas stats
        $sqlKelas = "SELECT
                        k.nama_kelas,
                        COUNT(DISTINCT siswa_pkl.siswa_id) AS total_siswa,
                        COUNT(pp.id) AS total_progress,
                        SUM(CASE WHEN pp.status = 'approved' THEN 1 ELSE 0 END) AS approved
                     FROM siswa_pkl
                     JOIN siswa s ON s.id = siswa_pkl.siswa_id AND s.deleted_at IS NULL
                     JOIN kelas k ON k.id = s.kelas_id
                     LEFT JOIN pkl_tasks pt ON pt.siswa_id = s.id AND pt.deleted_at IS NULL
                     LEFT JOIN pkl_progress pp ON pp.task_id = pt.id AND pp.deleted_at IS NULL
                     WHERE siswa_pkl.deleted_at IS NULL
                     AND k.jurusan = ?
                     AND siswa_pkl.tahun_ajaran = ?
                     GROUP BY k.id, k.nama_kelas
                     ORDER BY k.nama_kelas ASC";

        $stats['per_kelas'] = $this->db->query($sqlKelas, [$jurusan, $tahunAjaran ?? get_active_tahun_ajaran()])->getResultArray();

        return $stats;
    }

    /**
     * Get siswa detail with tasks and stats for a jurusan
     */
    public function getSiswaDetailForJurusan(int $siswaId, string $jurusan): ?array
    {
        // Verify siswa belongs to this jurusan
        $siswa = $this->db->table('siswa')
            ->select('siswa.*, kelas.nama_kelas, kelas.jurusan, users.username, users.profile_photo')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('users', 'users.id = siswa.user_id', 'left')
            ->where('siswa.id', $siswaId)
            ->where('kelas.jurusan', $jurusan)
            ->where('siswa.deleted_at', null)
            ->get()
            ->getRowArray();

        if (!$siswa) {
            return null;
        }

        // Get tasks with progress
        $tasks = $this->db->table('pkl_tasks')
            ->select('
                pkl_tasks.*,
                pkl_categories.nama AS kategori_nama,
                COUNT(pkl_progress.id) AS total_progress,
                SUM(CASE WHEN pkl_progress.status = \'approved\' THEN 1 ELSE 0 END) AS approved_count,
                SUM(CASE WHEN pkl_progress.status = \'submitted\' THEN 1 ELSE 0 END) AS submitted_count
            ')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->join('pkl_progress', 'pkl_progress.task_id = pkl_tasks.id AND pkl_progress.deleted_at IS NULL', 'left')
            ->where('pkl_tasks.siswa_id', $siswaId)
            ->where('pkl_tasks.deleted_at', null)
            ->groupBy('pkl_tasks.id')
            ->orderBy('pkl_tasks.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get PKL placement info
        $pklInfo = $this->db->table('siswa_pkl')
            ->select('
                siswa_pkl.*,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota,
                guru.nama_lengkap AS nama_pembimbing,
                guru.nip AS nip_pembimbing
            ')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id', 'left')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id', 'left')
            ->where('siswa_pkl.siswa_id', $siswaId)
            ->where('siswa_pkl.tahun_ajaran', get_active_tahun_ajaran())
            ->where('siswa_pkl.deleted_at', null)
            ->get()
            ->getRowArray();

        return [
            'siswa' => $siswa,
            'tasks' => $tasks,
            'pkl_info' => $pklInfo,
        ];
    }
}
