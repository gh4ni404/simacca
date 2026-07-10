<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalPklModel extends Model
{
    protected $table            = 'jurnal_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
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
                    ORDER BY tanggal ASC";

            return $this->db->query($sql, [$siswaId, $weekBase, $minggu, $startDate])->getResultArray();
        }

        $builder = $this->db->table($this->table)
            ->where('siswa_id', $siswaId)
            ->where('YEAR(tanggal)', $tahun)
            ->where('WEEK(tanggal, 1)', $minggu)
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
                        SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                    FROM jurnal_pkl
                    WHERE siswa_id = ? AND tanggal >= ?
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
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                FROM jurnal_pkl
                WHERE siswa_id = ?
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
                    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) AS ditolak
                FROM jurnal_pkl
                WHERE siswa_id = ?";

        $result = $this->db->query($sql, [$siswaId])->getRowArray();

        return $result ?: [
            'total' => 0,
            'pending' => 0,
            'disetujui' => 0,
            'revisi' => 0,
            'ditolak' => 0,
        ];
    }
}
