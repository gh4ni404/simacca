<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jadwal_mengajar_id',
        'tanggal',
        'pertemuan_ke',
        'materi_pembelajaran',
        'created_by',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'jadwal_mengajar_id'    => 'required|numeric',
        'tanggal'               => 'required|valid_date',
        'pertemuan_ke'          => 'required|numeric',
        'created_by'            => 'required|numeric',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
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
     * Get absensi by jadwal dan tanggal
     */
    public function getByJadwalAndTanggal($jadwalId, $tanggal)
    {
        return $this->where('jadwal_mengajar_id', $jadwalId)
            ->where('tanggal', $tanggal)
            ->first();
    }

    /**
     * Get absensi dengan detail lengkap
     */
    public function getAbsensiWithDetail($absensiId = null)
    {
        $builder = $this->select('absensi.*, 
                                guru.nama_lengkap as nama_guru,
                                mata_pelajaran.nama_mapel,
                                kelas.nama_kelas,
                                jadwal_mengajar.kelas_id as kelas_id,
                                jadwal_mengajar.hari')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->orderBy('absensi.tanggal', 'DESC');

        if ($absensiId) {
            return $builder->where('absensi.id', $absensiId)->first();
        }
        return $builder->findAll();
    }

    /**
     * Get absensi by guru
     */
    public function getByGuru($guruId, $startDate = null, $endDate = null)
    {
        // $builder = $this->select('absensi.*,
        //                         mata_pelajaran.nama_mapel,
        //                         kelas.nama_kelas')
        //     ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
        //     ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
        //     ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
        //     ->where('jadwal_mengajar.guru_id', $guruId)
        //     ->orderBy('absensi.tanggal', 'DESC');
        // if ($startDate && $endDate) {
        //     $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        // }

        // return $builder->findAll();
        $builder = $this->select('absensi.*,
                            guru.nama_lengkap as nama_guru,
                            mata_pelajaran.nama_mapel,
                            kelas.nama_kelas')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->orderBy('absensi.tanggal', 'DESC');

        if ($startDate && $endDate) {
            $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        } elseif ($startDate && !$endDate) {
            $builder->where('absensi.tanggal', $startDate);
        }

        return $builder->findAll();
    }

    /**
     * Get absensi by kelas
     */
    public function getByKelas($kelasId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('absensi.*,
                                guru.nama_lengkap as nama_guru,
                                mata_pelajaran.nama_mapel')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->where('jadwal_mengajar.kelas_id', $kelasId)
            ->orderBy('absensi.tanggal', 'DESC');

        if ($startDate && $endDate) {
            $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        }

        return $builder->findAll();
    }

    /**
     * Cek apakah sudah ada absensi untuk jadwal di tanggal tertentu
     */
    public function isAlreadyAbsen($jadwalId, $tanggal)
    {
        return $this->where('jadwal_mengajar_id', $jadwalId)
            ->where('tanggal', $tanggal)
            ->countAllResults() > 0;
    }

    /**
     * Get statistik absensi
     */
    public function getStatistik($guruId = null, $kelasId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('COUNT(absensi.id) as total_pertemuan')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id');

        if ($guruId) {
            $builder->where('jadwal_mengajar.guru_id', $guruId);
        }

        if ($kelasId) {
            $builder->where('jadwal_mengajar.kelas_id', $kelasId);
        }

        if ($startDate && $endDate) {
            $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        }
        return $builder->first();
    }

    public function getRecentAbsensi($limit = 5)
    {
        return $this->select('absensi.*, guru.nama_lengkap as nama_guru, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->orderBy('absensi.tanggal', 'DESC')
            ->orderBy('absensi.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
