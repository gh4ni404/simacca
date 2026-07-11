<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'tahun_ajaran',
        'wali_kelas_id',
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
        'nama_kelas'    => 'required|max_length[10]',
        'tingkat'       => 'required|in_list[10,11,12]',
        'jurusan'       => 'required',
        'tahun_ajaran'  => 'required',
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
     * Get all kelas for active tahun ajaran with wali kelas data
     */
    public function getAllKelas(string $tahunAjaran)
    {
        return $this->select('kelas.*, guru.nama_lengkap as nama_wali_kelas')
            ->where('kelas.tahun_ajaran', $tahunAjaran)
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left')
            ->orderBy('kelas.tingkat, kelas.nama_kelas')
            ->findAll();
    }

    /**
     * Get kelas by wali_kelas_id for a given tahun ajaran
     */
    public function getByWaliKelas($guruId, string $tahunAjaran)
    {
        return $this->where('wali_kelas_id', $guruId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();
    }

    /**
     * Get kelas by tingkat for a given tahun ajaran
     */
    public function getByTingkat($tingkat, string $tahunAjaran)
    {
        return $this->where('tingkat', $tingkat)
            ->where('tahun_ajaran', $tahunAjaran)
            ->findAll();
    }

    /**
     * Get kelas with jumlah siswa for a given tahun ajaran
     */
    public function getKelasWithJumlahSiswa($kelasId = null, string $tahunAjaran = null)
    {
        $builder = $this->select('kelas.*, 
                             COUNT(siswa.id) as jumlah_siswa,
                             guru.nama_lengkap as nama_wali_kelas,
                             guru.nip as nip_wali_kelas,
                             mata_pelajaran.nama_mapel')
            ->join('siswa', 'siswa.kelas_id = kelas.id AND siswa.tahun_ajaran = kelas.tahun_ajaran AND siswa.deleted_at IS NULL', 'left')
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = guru.mata_pelajaran_id', 'left');

        if ($kelasId) {
            return $builder->where('kelas.id', $kelasId)->first();
        }

        if ($tahunAjaran) {
            $builder->where('kelas.tahun_ajaran', $tahunAjaran);
        }

        return $builder->groupBy('kelas.id')
            ->orderBy('kelas.tingkat, kelas.nama_kelas')
            ->findAll();
    }

    /**
     * Get kelas yang belum punya wali kelas for a given tahun ajaran
     */
    public function getKelasWithoutWali(string $tahunAjaran)
    {
        return $this->where('tahun_ajaran', $tahunAjaran)
            ->groupStart()
                ->where('wali_kelas_id IS NULL', null, false)
                ->orWhere('wali_kelas_id', 0)
            ->groupEnd()
            ->findAll();
    }

    /**
     * Get list kelas untuk dropdown for a given tahun ajaran
     */
    public function getListKelas(string $tahunAjaran)
    {
        $kelas = $this->where('tahun_ajaran', $tahunAjaran)
            ->orderBy('tingkat, nama_kelas')
            ->findAll();
        $list = [];

        foreach ($kelas as $k) {
            $list[$k['id']] = $k['nama_kelas'] . ' - ' . $k['jurusan'];
        }
        return $list;
    }

    /**
     * Check if nama_kelas + tahun_ajaran combination already exists
     */
    public function isUnique(string $namaKelas, string $tahunAjaran, ?int $excludeId = null): bool
    {
        $builder = $this->where('nama_kelas', $namaKelas)
            ->where('tahun_ajaran', $tahunAjaran);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
