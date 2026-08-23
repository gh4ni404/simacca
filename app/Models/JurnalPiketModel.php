<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalPiketModel extends Model
{
    protected $table            = 'jurnal_piket';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
        'tanggal',
        'tahun_ajaran',
        'semester',
        'rincian_tugas',
        'deskripsi',
        'catatan',
        'foto_dokumentasi',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [];

    /**
     * Get journals by specific guru with date range filter
     */
    public function getJurnalByGuru(int $guruId, ?string $startDate = null, ?string $endDate = null): array
    {
        $builder = $this->select('jurnal_piket.*, guru.nama_lengkap, guru.nip')
            ->join('guru', 'guru.id = jurnal_piket.guru_id')
            ->where('jurnal_piket.guru_id', $guruId);

        if (!empty($startDate)) {
            $builder->where('jurnal_piket.tanggal >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('jurnal_piket.tanggal <=', $endDate);
        }

        return $builder->orderBy('jurnal_piket.tanggal', 'DESC')
            ->orderBy('jurnal_piket.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get journals with guru info for admin view with date range & guru filter
     */
    public function getJurnalWithGuru(?string $startDate = null, ?string $endDate = null, ?int $guruId = null): array
    {
        $builder = $this->select('jurnal_piket.*, guru.nama_lengkap, guru.nip, users.profile_photo')
            ->join('guru', 'guru.id = jurnal_piket.guru_id')
            ->join('users', 'users.id = guru.user_id', 'left');

        if (!empty($startDate)) {
            $builder->where('jurnal_piket.tanggal >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('jurnal_piket.tanggal <=', $endDate);
        }

        if (!empty($guruId)) {
            $builder->where('jurnal_piket.guru_id', $guruId);
        }

        return $builder->orderBy('jurnal_piket.tanggal', 'DESC')
            ->orderBy('jurnal_piket.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Check if journal entry exists for guru on specific date
     */
    public function isJurnalExist(int $guruId, string $tanggal, ?int $excludeId = null): bool
    {
        $builder = $this->where('guru_id', $guruId)
            ->where('tanggal', $tanggal);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get single journal entry for guru on specific date
     */
    public function getJurnalByGuruAndTanggal(int $guruId, string $tanggal): ?array
    {
        $res = $this->where('guru_id', $guruId)
            ->where('tanggal', $tanggal)
            ->first();

        if ($res) {
            if (empty($res['foto']) && !empty($res['foto_dokumentasi'])) {
                $res['foto'] = $res['foto_dokumentasi'];
            }
        }

        return $res;
    }

    /**
     * Get statistics for specific academic year and semester
     */
    public function getStats(string $tahunAjaran, string $semester, ?int $guruId = null): array
    {
        $builder = $this->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester);

        if ($guruId) {
            $builder->where('guru_id', $guruId);
        }

        $totalJurnal = $builder->countAllResults(false);

        $withPhoto = clone $builder;
        $totalWithPhoto = $withPhoto->where('foto_dokumentasi IS NOT NULL')
            ->where('foto_dokumentasi !=', '')
            ->countAllResults();

        return [
            'total'      => $totalJurnal,
            'with_photo' => $totalWithPhoto,
        ];
    }
}
