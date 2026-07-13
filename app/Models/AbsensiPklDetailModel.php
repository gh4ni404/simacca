<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiPklDetailModel extends Model
{
    protected $table            = 'absensi_pkl_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'absensi_pkl_id',
        'siswa_id',
        'status',
        'keterangan',
        'waktu_absen',
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
        'absensi_pkl_id' => 'required|numeric',
        'siswa_id'       => 'required|numeric',
        'status'         => 'required|in_list[hadir,izin,sakit,alpa,dispen]',
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
     * Get detail by absensi_pkl_id with siswa info
     */
    public function getByAbsensiPkl(int $absensiPklId)
    {
        return $this->select('
                absensi_pkl_detail.*,
                siswa.nama_lengkap AS nama_siswa,
                siswa.nis,
                kelas.nama_kelas
            ')
            ->join('siswa', 'siswa.id = absensi_pkl_detail.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('absensi_pkl_detail.absensi_pkl_id', $absensiPklId)
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Batch insert absensi detail
     */
    public function insertBatchAbsensi(int $absensiPklId, array $dataSiswa): bool
    {
        $batch = [];
        foreach ($dataSiswa as $siswaId => $data) {
            if (empty($siswaId)) {
                continue;
            }
            $batch[] = [
                'absensi_pkl_id' => $absensiPklId,
                'siswa_id'       => $siswaId,
                'status'         => $data['status'] ?? 'alpa',
                'keterangan'     => $data['keterangan'] ?? null,
                'waktu_absen'    => date('Y-m-d H:i:s'),
            ];
        }

        if (empty($batch)) {
            return true;
        }

        return $this->insertBatch($batch);
    }

    /**
     * Update single absensi detail
     */
    public function updateAbsensi(int $absensiPklId, int $siswaId, array $data): bool
    {
        $existing = $this->where('absensi_pkl_id', $absensiPklId)
            ->where('siswa_id', $siswaId)
            ->first();

        if (!$existing) {
            return false;
        }

        return $this->update($existing['id'], $data);
    }

    /**
     * Get or create absensi detail (upsert)
     */
    public function upsertAbsensi(int $absensiPklId, int $siswaId, array $data): bool
    {
        $existing = $this->where('absensi_pkl_id', $absensiPklId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        $data['absensi_pkl_id'] = $absensiPklId;
        $data['siswa_id'] = $siswaId;
        $insertResult = $this->insert($data);
        if ($insertResult === false) {
            $errors = $this->errors();
            throw new \RuntimeException("upsertAbsensi insert failed for siswa_id=$siswaId. Errors=" . json_encode($errors));
        }
        return $insertResult;
    }

    /**
     * Get rekap for a siswa (all attendance records)
     */
    public function getRekapSiswa(int $siswaId)
    {
        return $this->select('
                absensi_pkl_detail.*,
                absensi_pkl.tanggal,
                guru.nama_lengkap AS nama_pembimbing,
                tempat_pkl.nama_perusahaan
            ')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id')
            ->where('absensi_pkl_detail.siswa_id', $siswaId)
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->orderBy('absensi_pkl.tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Get statistics for a siswa
     */
    public function getStatistikSiswa(int $siswaId): array
    {
        $result = $this->select('status, COUNT(*) AS jumlah')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id')
            ->where('absensi_pkl_detail.siswa_id', $siswaId)
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->groupBy('status')
            ->findAll();

        $stats = [
            'total'   => 0,
            'hadir'   => 0,
            'izin'    => 0,
            'sakit'   => 0,
            'alpa'    => 0,
            'dispen'  => 0,
        ];

        foreach ($result as $row) {
            $stats[$row['status']] = (int) $row['jumlah'];
            $stats['total'] += (int) $row['jumlah'];
        }

        $stats['persen_kehadiran'] = $stats['total'] > 0
            ? round(($stats['hadir'] / $stats['total']) * 100, 1)
            : 0;

        return $stats;
    }

    /**
     * Get detail stats for a single absensi session
     */
    public function getDetailStats(int $absensiPklId): array
    {
        $result = $this->select('status, COUNT(*) AS jumlah')
            ->where('absensi_pkl_id', $absensiPklId)
            ->groupBy('status')
            ->findAll();

        $stats = [
            'total'  => 0,
            'hadir'  => 0,
            'izin'   => 0,
            'sakit'  => 0,
            'alpa'   => 0,
            'dispen' => 0,
        ];

        foreach ($result as $row) {
            $stats[$row['status']] = (int) $row['jumlah'];
            $stats['total'] += (int) $row['jumlah'];
        }

        $stats['persen_kehadiran'] = $stats['total'] > 0
            ? round(($stats['hadir'] / $stats['total']) * 100, 1)
            : 0;

        return $stats;
    }

    /**
     * Get aggregate stats for admin (by pembimbing)
     */
    public function getStatsByPembimbingPkl(int $pembimbingPklId): array
    {
        $result = $this->select('
                absensi_pkl_detail.status,
                COUNT(*) AS jumlah
            ')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id')
            ->where('absensi_pkl.pembimbing_pkl_id', $pembimbingPklId)
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->groupBy('absensi_pkl_detail.status')
            ->findAll();

        $stats = [
            'total'  => 0,
            'hadir'  => 0,
            'izin'   => 0,
            'sakit'  => 0,
            'alpa'   => 0,
            'dispen' => 0,
        ];

        foreach ($result as $row) {
            $stats[$row['status']] = (int) $row['jumlah'];
            $stats['total'] += (int) $row['jumlah'];
        }

        $stats['persen_kehadiran'] = $stats['total'] > 0
            ? round(($stats['hadir'] / $stats['total']) * 100, 1)
            : 0;

        return $stats;
    }

    /**
     * Get global stats for admin dashboard
     */
    public function getGlobalStats(?string $from = null, ?string $to = null): array
    {
        $builder = $this->db->table('absensi_pkl_detail')
            ->select('absensi_pkl_detail.status, COUNT(*) AS jumlah')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id')
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->groupBy('absensi_pkl_detail.status');

        if ($from) {
            $builder->where('absensi_pkl.tanggal >=', $from);
        }
        if ($to) {
            $builder->where('absensi_pkl.tanggal <=', $to);
        }

        $result = $builder->get()->getResultArray();

        $stats = [
            'total'  => 0,
            'hadir'  => 0,
            'izin'   => 0,
            'sakit'  => 0,
            'alpa'   => 0,
            'dispen' => 0,
        ];

        foreach ($result as $row) {
            $stats[$row['status']] = (int) $row['jumlah'];
            $stats['total'] += (int) $row['jumlah'];
        }

        $stats['persen_kehadiran'] = $stats['total'] > 0
            ? round(($stats['hadir'] / $stats['total']) * 100, 1)
            : 0;

        return $stats;
    }

    /**
     * Get recent activity for admin
     */
    public function getRecentActivity(int $limit = 10): array
    {
        return $this->select('
                absensi_pkl_detail.*,
                absensi_pkl.tanggal,
                siswa.nama_lengkap AS nama_siswa,
                guru.nama_lengkap AS nama_pembimbing,
                tempat_pkl.nama_perusahaan
            ')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id')
            ->join('siswa', 'siswa.id = absensi_pkl_detail.siswa_id')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id')
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->orderBy('absensi_pkl.tanggal', 'DESC')
            ->orderBy('absensi_pkl_detail.waktu_absen', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
