<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalPklModel extends Model
{
    protected $table            = 'jurnal_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id',
        'nama_kegiatan',
        'deskripsi',
        'foto',
        'status',
        'tanggal',
        'catatan_pembimbing',
        'verified_by',
        'verified_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [
        'siswa_id'      => 'required|numeric',
        'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
        'deskripsi'     => 'required|min_length[10]',
        'tanggal'       => 'required|valid_date',
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

    public function getBySiswa($siswaId, $limit = null, $offset = 0)
    {
        $this->where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'DESC');

        if ($limit !== null) {
            $this->limit($limit, $offset);
        }

        return $this->findAll();
    }

    public function getBySiswaAndWeek($siswaId, $tahun, $minggu, $startDate = null, $weekBase = null)
    {
        if ($startDate && $weekBase) {
            $sql = "SELECT * FROM {$this->table}
                    WHERE siswa_id = ?
                    AND FLOOR(DATEDIFF(tanggal, ?) / 7) + 1 = ?
                    AND tanggal >= ?
                    AND deleted_at IS NULL
                    ORDER BY tanggal ASC";

            return $this->db->query($sql, [$siswaId, $weekBase, $minggu, $startDate])->getResultArray();
        }

        $builder = $this->db->table($this->table)
            ->where('siswa_id', $siswaId)
            ->where('YEAR(tanggal)', $tahun)
            ->where('WEEK(tanggal, 1)', $minggu)
            ->where('deleted_at', null)
            ->orderBy('tanggal', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getWeeklyGrouped($siswaId, $startDate = null, $weekBase = null)
    {
        if ($startDate && $weekBase) {
            $sql = "SELECT
                        YEAR(MIN(tanggal)) AS tahun,
                        FLOOR(DATEDIFF(tanggal, ?) / 7) + 1 AS minggu_ke,
                        MIN(tanggal) AS tanggal_mulai,
                        MAX(tanggal) AS tanggal_selesai,
                        COUNT(*) AS total_entry,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                        SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                        SUM(CASE WHEN status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                        SUM(CASE WHEN status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS tinjau_ulang,
                        SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                    FROM jurnal_pkl
                    WHERE siswa_id = ? AND tanggal >= ?
                    AND deleted_at IS NULL
                    GROUP BY minggu_ke
                    ORDER BY minggu_ke DESC";

            return $this->db->query($sql, [$weekBase, $siswaId, $startDate])->getResultArray();
        }

        $sql = "SELECT
                    YEAR(tanggal) AS tahun,
                    WEEK(tanggal, 1) AS minggu_ke,
                    MIN(tanggal) AS tanggal_mulai,
                    MAX(tanggal) AS tanggal_selesai,
                    COUNT(*) AS total_entry,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                    SUM(CASE WHEN status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                    SUM(CASE WHEN status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS tinjau_ulang,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                FROM jurnal_pkl
                WHERE siswa_id = ?
                AND deleted_at IS NULL
                GROUP BY tahun, minggu_ke
                ORDER BY tahun DESC, minggu_ke DESC";

        return $this->db->query($sql, [$siswaId])->getResultArray();
    }

    public function getBySiswaAndStatus($siswaId, $status)
    {
        return $this->where('siswa_id', $siswaId)
            ->where('status', $status)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }

    public function getPendingByPembimbing($guruId)
    {
        $sql = "SELECT jp.*, 
                       s.nama_lengkap AS nama_siswa,
                       s.nis,
                       k.nama_kelas
                FROM jurnal_pkl jp
                JOIN siswa s ON s.id = jp.siswa_id
                JOIN kelas k ON k.id = s.kelas_id
                JOIN siswa_pkl sp ON sp.siswa_id = s.id
                JOIN pembimbing_pkl pp ON pp.tempat_pkl_id = sp.tempat_pkl_id AND pp.tahun_ajaran = sp.tahun_ajaran
                WHERE pp.guru_id = ?
                AND jp.deleted_at IS NULL
                ORDER BY jp.tanggal DESC";

        return $this->db->query($sql, [$guruId])->getResultArray();
    }

    public function getByPembimbingAndWeek($guruId, $tahun, $minggu, $startDate = null, $weekBase = null)
    {
        if ($startDate && $weekBase) {
            $sql = "SELECT jp.*, 
                           s.nama_lengkap AS nama_siswa,
                           s.nis,
                           k.nama_kelas
                    FROM jurnal_pkl jp
                    JOIN siswa s ON s.id = jp.siswa_id
                    JOIN kelas k ON k.id = s.kelas_id
                    JOIN siswa_pkl sp ON sp.siswa_id = s.id
                    JOIN pembimbing_pkl pp ON pp.tempat_pkl_id = sp.tempat_pkl_id AND pp.tahun_ajaran = sp.tahun_ajaran
                    WHERE pp.guru_id = ?
                    AND FLOOR(DATEDIFF(jp.tanggal, ?) / 7) + 1 = ?
                    AND jp.tanggal >= ?
                    AND jp.deleted_at IS NULL
                    ORDER BY s.nama_lengkap, jp.tanggal ASC";

            return $this->db->query($sql, [$guruId, $weekBase, $minggu, $startDate])->getResultArray();
        }

        $sql = "SELECT jp.*, 
                       s.nama_lengkap AS nama_siswa,
                       s.nis,
                       k.nama_kelas
                FROM jurnal_pkl jp
                JOIN siswa s ON s.id = jp.siswa_id
                JOIN kelas k ON k.id = s.kelas_id
                JOIN siswa_pkl sp ON sp.siswa_id = s.id
                JOIN pembimbing_pkl pp ON pp.tempat_pkl_id = sp.tempat_pkl_id AND pp.tahun_ajaran = sp.tahun_ajaran
                WHERE pp.guru_id = ?
                AND YEAR(jp.tanggal) = ?
                AND WEEK(jp.tanggal, 1) = ?
                AND jp.deleted_at IS NULL
                ORDER BY s.nama_lengkap, jp.tanggal ASC";

        return $this->db->query($sql, [$guruId, $tahun, $minggu])->getResultArray();
    }

    public function getStatistics($siswaId)
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                    SUM(CASE WHEN status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                    SUM(CASE WHEN status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS tinjau_ulang,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                FROM jurnal_pkl
                WHERE siswa_id = ?
                AND deleted_at IS NULL";

        $result = $this->db->query($sql, [$siswaId])->getRowArray();

        return $result ?: [
            'total' => 0,
            'pending' => 0,
            'disetujui' => 0,
            'revisi' => 0,
            'tinjau_ulang' => 0,
            'ditolak' => 0,
        ];
    }

    public function getArchiveSummary(?string $startDate = null, ?string $endDate = null, ?int $siswaId = null, ?string $status = null): array
    {
        $conditions = ['jp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) {
            $conditions[] = 'jp.tanggal >= ?';
            $binds[] = $startDate;
        }
        if ($endDate) {
            $conditions[] = 'jp.tanggal <= ?';
            $binds[] = $endDate;
        }
        if ($siswaId) {
            $conditions[] = 'jp.siswa_id = ?';
            $binds[] = $siswaId;
        }
        if ($status) {
            $conditions[] = 'jp.status = ?';
            $binds[] = $status;
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    s.id AS siswa_id,
                    s.nama_lengkap,
                    s.nis,
                    k.nama_kelas,
                    tp.nama_perusahaan,
                    COUNT(jp.id) AS total_entry,
                    SUM(CASE WHEN jp.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN jp.status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                    SUM(CASE WHEN jp.status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                    SUM(CASE WHEN jp.status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS tinjau_ulang,
                    SUM(CASE WHEN jp.status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak,
                    MIN(jp.tanggal) AS tanggal_pertama,
                    MAX(jp.tanggal) AS tanggal_terakhir
                FROM jurnal_pkl jp
                JOIN siswa s ON s.id = jp.siswa_id
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
        $conditions = ['deleted_at IS NULL'];
        $binds = [];

        if ($startDate) {
            $conditions[] = 'tanggal >= ?';
            $binds[] = $startDate;
        }
        if ($endDate) {
            $conditions[] = 'tanggal <= ?';
            $binds[] = $endDate;
        }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    COUNT(*) AS total_entries,
                    COUNT(DISTINCT siswa_id) AS total_siswa,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS total_pending,
                    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) AS total_disetujui,
                    SUM(CASE WHEN status = 'revisi' THEN 1 ELSE 0 END) AS total_revisi,
                    SUM(CASE WHEN status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS total_tinjau_ulang,
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) AS total_ditolak,
                    MIN(tanggal) AS earliest_date,
                    MAX(tanggal) AS latest_date
                FROM jurnal_pkl
                WHERE {$whereClause}";

        $result = $this->db->query($sql, $binds)->getRowArray();

        return $result ?: [
            'total_entries' => 0,
            'total_siswa' => 0,
            'total_pending' => 0,
            'total_disetujui' => 0,
            'total_revisi' => 0,
            'total_tinjau_ulang' => 0,
            'total_ditolak' => 0,
            'earliest_date' => null,
            'latest_date' => null,
        ];
    }

    public function getWeeklyArchive(?string $startDate = null, ?string $endDate = null, ?int $siswaId = null): array
    {
        $conditions = ['jp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) {
            $conditions[] = 'jp.tanggal >= ?';
            $binds[] = $startDate;
        }
        if ($endDate) {
            $conditions[] = 'jp.tanggal <= ?';
            $binds[] = $endDate;
        }
        if ($siswaId) {
            $conditions[] = 'jp.siswa_id = ?';
            $binds[] = $siswaId;
        }

        $whereClause = implode(' AND ', $conditions);

        $wkStart = get_jurnal_pkl_start_date();
        $wkBase = get_jurnal_pkl_week_base();

        if ($wkStart && $wkBase) {
            $sql = "SELECT
                        FLOOR(DATEDIFF(jp.tanggal, ?) / 7) + 1 AS minggu_ke,
                        MIN(jp.tanggal) AS tanggal_mulai,
                        MAX(jp.tanggal) AS tanggal_selesai,
                        COUNT(*) AS total_entry,
                        COUNT(DISTINCT jp.siswa_id) AS total_siswa,
                        SUM(CASE WHEN jp.status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                        SUM(CASE WHEN jp.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                        SUM(CASE WHEN jp.status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                        SUM(CASE WHEN jp.status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                    FROM jurnal_pkl jp
                    WHERE {$whereClause}
                    GROUP BY minggu_ke
                    ORDER BY minggu_ke ASC";

            $binds = array_merge([$wkBase], $binds);
        } else {
            $sql = "SELECT
                        YEAR(jp.tanggal) AS tahun,
                        WEEK(jp.tanggal, 1) AS minggu_ke,
                        MIN(jp.tanggal) AS tanggal_mulai,
                        MAX(jp.tanggal) AS tanggal_selesai,
                        COUNT(*) AS total_entry,
                        COUNT(DISTINCT jp.siswa_id) AS total_siswa,
                        SUM(CASE WHEN jp.status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                        SUM(CASE WHEN jp.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                        SUM(CASE WHEN jp.status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                        SUM(CASE WHEN jp.status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                    FROM jurnal_pkl jp
                    WHERE {$whereClause}
                    GROUP BY tahun, minggu_ke
                    ORDER BY tahun ASC, minggu_ke ASC";
        }

        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function getArchiveByTempatPkl(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ['jp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) { $conditions[] = 'jp.tanggal >= ?'; $binds[] = $startDate; }
        if ($endDate) { $conditions[] = 'jp.tanggal <= ?'; $binds[] = $endDate; }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    tp.id AS tempat_pkl_id,
                    tp.nama_perusahaan,
                    tp.kota,
                    COUNT(jp.id) AS total_entry,
                    COUNT(DISTINCT jp.siswa_id) AS total_siswa,
                    SUM(CASE WHEN jp.status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                    SUM(CASE WHEN jp.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN jp.status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                    SUM(CASE WHEN jp.status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS tinjau_ulang,
                    SUM(CASE WHEN jp.status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak,
                    MIN(jp.tanggal) AS tanggal_pertama,
                    MAX(jp.tanggal) AS tanggal_terakhir
                FROM jurnal_pkl jp
                JOIN siswa_pkl sp ON sp.siswa_id = jp.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
                JOIN tempat_pkl tp ON tp.id = sp.tempat_pkl_id
                WHERE {$whereClause}
                GROUP BY tp.id, tp.nama_perusahaan, tp.kota
                ORDER BY tp.nama_perusahaan ASC";

        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function getArchiveByPembimbing(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ['jp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) { $conditions[] = 'jp.tanggal >= ?'; $binds[] = $startDate; }
        if ($endDate) { $conditions[] = 'jp.tanggal <= ?'; $binds[] = $endDate; }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    g.id AS guru_id,
                    g.nama_lengkap AS nama_pembimbing,
                    g.nip,
                    COUNT(jp.id) AS total_entry,
                    COUNT(DISTINCT jp.siswa_id) AS total_siswa,
                    SUM(CASE WHEN jp.status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                    SUM(CASE WHEN jp.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN jp.status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                    SUM(CASE WHEN jp.status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS tinjau_ulang,
                    SUM(CASE WHEN jp.status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak,
                    MIN(jp.tanggal) AS tanggal_pertama,
                    MAX(jp.tanggal) AS tanggal_terakhir
                FROM jurnal_pkl jp
                JOIN siswa_pkl sp ON sp.siswa_id = jp.siswa_id AND sp.tahun_ajaran = (SELECT `value` FROM `settings` WHERE `key` = 'tahun_ajaran_aktif')
                JOIN pembimbing_pkl pp ON pp.tempat_pkl_id = sp.tempat_pkl_id AND pp.tahun_ajaran = sp.tahun_ajaran
                JOIN guru g ON g.id = pp.guru_id
                WHERE {$whereClause}
                GROUP BY g.id, g.nama_lengkap, g.nip
                ORDER BY g.nama_lengkap ASC";

        return $this->db->query($sql, $binds)->getResultArray();
    }

    public function getArchiveByKelas(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = ['jp.deleted_at IS NULL'];
        $binds = [];

        if ($startDate) { $conditions[] = 'jp.tanggal >= ?'; $binds[] = $startDate; }
        if ($endDate) { $conditions[] = 'jp.tanggal <= ?'; $binds[] = $endDate; }

        $whereClause = implode(' AND ', $conditions);

        $sql = "SELECT
                    k.id AS kelas_id,
                    k.nama_kelas,
                    COUNT(jp.id) AS total_entry,
                    COUNT(DISTINCT jp.siswa_id) AS total_siswa,
                    SUM(CASE WHEN jp.status = 'disetujui' THEN 1 ELSE 0 END) AS disetujui,
                    SUM(CASE WHEN jp.status = 'pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN jp.status = 'revisi' THEN 1 ELSE 0 END) AS revisi,
                    SUM(CASE WHEN jp.status = 'tinjau_ulang' THEN 1 ELSE 0 END) AS tinjau_ulang,
                    SUM(CASE WHEN jp.status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak,
                    MIN(jp.tanggal) AS tanggal_pertama,
                    MAX(jp.tanggal) AS tanggal_terakhir
                FROM jurnal_pkl jp
                JOIN siswa s ON s.id = jp.siswa_id
                JOIN kelas k ON k.id = s.kelas_id
                WHERE {$whereClause}
                GROUP BY k.id, k.nama_kelas
                ORDER BY k.nama_kelas ASC";

        return $this->db->query($sql, $binds)->getResultArray();
    }
}
