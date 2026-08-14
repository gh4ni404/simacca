<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiPklModel extends Model
{
    protected $table            = 'absensi_pkl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pembimbing_pkl_id',
        'tanggal',
        'keterangan_umum',
        'created_by',
        'created_at',
        'updated_at',
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
        'pembimbing_pkl_id' => 'required|numeric',
        'tanggal'           => 'required|valid_date',
        'created_by'        => 'required|numeric',
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
     * Check if absensi already exists for this pembimbing on this date
     * Includes soft-deleted records to avoid unique constraint violations
     */
    public function isAlreadyAbsen(int $pembimbingPklId, string $tanggal): bool
    {
        // Must also check soft-deleted records because the DB unique constraint
        // still applies to all rows (including deleted_at IS NOT NULL)
        return $this->withDeleted()
            ->where('pembimbing_pkl_id', $pembimbingPklId)
            ->where('tanggal', $tanggal)
            ->countAllResults() > 0;
    }

    /**
     * Find a soft-deleted absensi for a given pembimbing + tanggal (to restore it)
     */
    public function findTrashed(int $pembimbingPklId, string $tanggal): ?array
    {
        return $this->onlyDeleted()
            ->where('pembimbing_pkl_id', $pembimbingPklId)
            ->where('tanggal', $tanggal)
            ->first();
    }

    /**
     * Get absensi list by pembimbing (guru) with detail stats
     */
    public function getByGuru(int $guruId, ?string $tanggal = null)
    {
        $builder = $this->select('
                absensi_pkl.*,
                guru.nama_lengkap AS nama_pembimbing,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota,
                pembimbing_pkl.guru_id,
                pembimbing_pkl.tempat_pkl_id,
                pembimbing_pkl.tahun_ajaran
            ')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id AND guru.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->orderBy('absensi_pkl.tanggal', 'DESC');

        if ($tanggal) {
            $builder->where('absensi_pkl.tanggal', $tanggal);
        }

        return $builder->findAll();
    }

    /**
     * Get absensi with detail (header info + JOINs)
     */
    public function getAbsensiPklWithDetail(int $absensiPklId)
    {
        return $this->select('
                absensi_pkl.*,
                guru.nama_lengkap AS nama_pembimbing,
                guru.nip,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota,
                tempat_pkl.alamat,
                users.username AS pembimbing_username
            ')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id AND guru.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->join('users', 'users.id = absensi_pkl.created_by')
            ->where('absensi_pkl.id', $absensiPklId)
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->first();
    }

    /**
     * Get all absensi for admin view (all pembimbing)
     */
    public function getForAdmin(?int $pembimbingPklId = null, ?string $from = null, ?string $to = null)
    {
        $builder = $this->select('
                absensi_pkl.*,
                guru.nama_lengkap AS nama_pembimbing,
                guru.nip,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota,
                pembimbing_pkl.guru_id,
                pembimbing_pkl.tempat_pkl_id,
                pembimbing_pkl.tahun_ajaran,
                users.username AS pembimbing_username
            ')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id AND guru.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->join('users', 'users.id = absensi_pkl.created_by')
            ->orderBy('absensi_pkl.tanggal', 'DESC')
            ->orderBy('guru.nama_lengkap', 'ASC');

        if ($pembimbingPklId) {
            $builder->where('absensi_pkl.pembimbing_pkl_id', $pembimbingPklId);
        }

        if ($from) {
            $builder->where('absensi_pkl.tanggal >=', $from);
        }

        if ($to) {
            $builder->where('absensi_pkl.tanggal <=', $to);
        }

        return $builder->findAll();
    }

    /**
     * Get rekap per pembimbing for admin
     */
    public function getRekapByPembimbing(?string $from = null, ?string $to = null)
    {
        $builder = $this->db->table('absensi_pkl')
            ->select('
                absensi_pkl.pembimbing_pkl_id,
                guru.nama_lengkap AS nama_pembimbing,
                tempat_pkl.nama_perusahaan,
                COUNT(DISTINCT absensi_pkl.tanggal) AS total_hari,
                MIN(absensi_pkl.tanggal) AS tanggal_mulai,
                MAX(absensi_pkl.tanggal) AS tanggal_terakhir
            ')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id AND guru.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->groupBy('absensi_pkl.pembimbing_pkl_id')
            ->orderBy('guru.nama_lengkap', 'ASC');

        if ($from) {
            $builder->where('absensi_pkl.tanggal >=', $from);
        }

        if ($to) {
            $builder->where('absensi_pkl.tanggal <=', $to);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get absensi by siswa (for siswa's own recap)
     */
    public function getBySiswa(int $siswaId)
    {
        return $this->select('
                absensi_pkl.*,
                guru.nama_lengkap AS nama_pembimbing,
                tempat_pkl.nama_perusahaan
            ')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id AND guru.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->join('absensi_pkl_detail', 'absensi_pkl_detail.absensi_pkl_id = absensi_pkl.id')
            ->where('absensi_pkl_detail.siswa_id', $siswaId)
            ->orderBy('absensi_pkl.tanggal', 'DESC')
            ->groupBy('absensi_pkl.id')
            ->findAll();
    }

    /**
     * Get all absensi for a specific pembimbing_pkl (for admin detail view)
     */
    public function getByPembimbingPkl(int $pembimbingPklId)
    {
        return $this->select('
                absensi_pkl.*,
                guru.nama_lengkap AS nama_pembimbing,
                tempat_pkl.nama_perusahaan,
                tempat_pkl.kota
            ')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('guru', 'guru.id = pembimbing_pkl.guru_id AND guru.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->where('absensi_pkl.pembimbing_pkl_id', $pembimbingPklId)
            ->where('absensi_pkl.deleted_at IS NULL', null, false)
            ->orderBy('absensi_pkl.tanggal', 'DESC')
            ->findAll();
    }
}
