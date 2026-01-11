<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalKbmModel extends Model
{
    protected $table            = 'jurnal_kbm';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'absensi_id',
        'tujuan_pembelajaran',
        'kegiatan_pembelajaran',
        'media_alat',
        'penilaian',
        'catatan_khusus',
        'foto_dokumentasi',
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
        'absensi_id'                => 'required|numeric|is_unique[jurnal_kbm.absensi_id]',
        'kegiatan_pembelajaran'     => 'required',
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
     * Get Jurnal by absensi_id
     */
    public function getByAbsensi($absensiId)
    {
        return $this->where('absensi_id', $absensiId)->first();
    }

    /**
     * Get jurnal with absensi detail
     */
    public function getJurnalWithDetail($jurnalId = null)
    {
        $builder = $this->select('jurnal_kbm.*,
                                absensi.tanggal,
                                absensi.pertemuan_ke,
                                absensi.materi_pembelajaran,
                                guru.nama_lengkap as nama_guru,
                                mata_pelajaran.nama_mapel,
                                kelas.nama_kelas')
            ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->orderBy('absensi.tanggal', 'DESC');

        if ($jurnalId) {
            return $builder->where('jurnal_kbm.id', $jurnalId)->first();
        }
        return $builder->findAll();
    }

    /**
     * Get Jurnal by guru
     */

    public function getByGuru($guruId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('jurnal_kbm.*, absensi.tanggal, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->orderBy('absensi.tanggal', 'DESC');

        if ($startDate && $endDate) {
            $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        }

        return $builder->findAll();
    }

    /**
     * Get jurnal by kelas
     */
    public function getByKelas($kelasId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('jurnal_kbm.*, absensi.tanggal, guru.nama_lengkap as nama_guru, mata_pelajaran.nama_mapel')
            ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
            ->join('jadwal_mengajar', 'jadwall_mengajar.id = absensi.jadwal_mengajar_id')
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
     * Cek apakah sudah ada jurnal untuk absensi tertentu
     */
    public function isJurnalExist($absensiId)
    {
        return $this->where('absensi_id', $absensiId)->countAllResults() > 0;
    }
}
