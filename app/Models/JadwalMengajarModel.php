<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalMengajarModel extends Model
{
    protected $table            = 'jadwal_mengajar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'guru_id',
        'mata_pelajaran_id',
        'kelas_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'semester',
        'tahun_ajaran',
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
    protected $validationRules = [
        'guru_id'           => 'required|numeric',
        'mata_pelajaran_id' => 'required|numeric',
        'kelas_id'          => 'required|numeric',
        'hari'              => 'required|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
        'jam_mulai'         => 'required|valid_date[H:i]',
        'jam_selesai'       => 'required|valid_date[H:i]',
        'semester'          => 'required|in_list[Ganjil,Genap]',
        'tahun_ajaran'      => 'required|regex_match[/\d{4}\/\d{4}/]'
    ];

    protected $validationMessages = [
        'guru_id' => [
            'required' => 'Guru wajib dipilih',
            'numeric' => 'Guru tidak valid'
        ],
        'mata_pelajaran_id' => [
            'required' => 'Mata pelajaran wajib dipilih',
            'numeric' => 'Mata pelajaran tidak valid'
        ],
        'kelas_id' => [
            'required' => 'Kelas wajib dipilih',
            'numeric' => 'Kelas tidak valid'
        ],
        'hari' => [
            'required' => 'Hari wajib dipilih',
            'in_list' => 'Hari harus Senin, Selasa, Rabu, Kamis, atau Jumat'
        ],
        'jam_mulai' => [
            'required' => 'Jam mulai wajib diisi',
            'valid_date' => 'Format jam tidak valid'
        ],
        'jam_selesai' => [
            'required' => 'Jam selesai wajib diisi',
            'valid_date' => 'Format jam tidak valid'
        ],
        'semester' => [
            'required' => 'Semester wajib dipilih',
            'in_list' => 'Semester harus Ganjil atau Genap'
        ],
        'tahun_ajaran' => [
            'required' => 'Tahun ajaran wajib diisi',
            'regex_match' => 'Format tahun ajaran: 2024/2025'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set created_at field before insert
     */
    protected function setCreatedAt(array $data)
    {
        if (isset($data['data'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get all jadwal dengan pagination dan search
     */
    public function getAllJadwal($perPage = 10, $search = null, $semester = null, $tahunAjaran = null)
    {
        $builder = $this->select('jadwal_mengajar.*, 
                     guru.nama_lengkap as nama_guru, 
                     guru.nip,
                     mata_pelajaran.nama_mapel,
                     mata_pelajaran.kode_mapel,
                     kelas.nama_kelas,
                     kelas.tingkat,
                     kelas.jurusan')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id');

        if ($search) {
            $builder->groupStart()
                ->like('guru.nama_lengkap', $search)
                ->orLike('mata_pelajaran.nama_mapel', $search)
                ->orLike('kelas.nama_kelas', $search)
                ->orLike('jadwal_mengajar.hari', $search)
                ->groupEnd();
        }

        if ($semester) {
            $builder->where('jadwal_mengajar.semester', $semester);
        }

        if ($tahunAjaran) {
            $builder->where('jadwal_mengajar.tahun_ajaran', $tahunAjaran);
        }

        return [
            'jadwal' => $this->orderBy('hari', 'ASC')
                ->orderBy('jam_mulai', 'ASC')
                ->paginate($perPage),
            'pager' => $this->pager
        ];
    }

    /**
     * Get jadwal by guru
     */
    public function getByGuru($guruId, $hari = null)
    {
        $builder = $this->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->orderBy('hari, jam_mulai');

        if ($hari) {
            $builder->where('hari', $hari);
        }

        return $builder->findAll();
    }

    /**
     * Get jadwal by kelas
     */
    public function getByKelas($kelasId, $hari = null)
    {
        $builder = $this->select('jadwal_mengajar.*, guru.nama_lengkap as nama_guru, mata_pelajaran.nama_mapel')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->where('kelas_id', $kelasId)
            ->orderBy('hari, jam_mulai');

        if ($hari) {
            $builder->where('hari', $hari);
        }

        return $builder->findAll();
    }

    /**
     * Get jadwal hari ini untuk guru
     */
    public function getJadwalHariIni($guruId)
    {
        $hariIndonesia = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
        ];

        $hariInggris = date('l');
        $hariIni = $hariIndonesia[$hariInggris] ?? null;

        if (!$hariIni) return [];

        return $this->getByGuru($guruId, $hariIni);
    }

    /**
     * Cek konflik jadwal
     */
    public function checkConflict($guruId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $db = \Config\Database::connect();
        $jamMulaiEscaped = $db->escape($jamMulai);
        $jamSelesaiEscaped = $db->escape($jamSelesai);
        
        $builder = $this->where('guru_id', $guruId)
            ->where('hari', $hari)
            ->groupStart()
            ->where("($jamMulaiEscaped BETWEEN jam_mulai AND jam_selesai)")
            ->orWhere("($jamSelesaiEscaped BETWEEN jam_mulai AND jam_selesai)")
            ->orWhere("(jam_mulai BETWEEN $jamMulaiEscaped AND $jamSelesaiEscaped)")
            ->orWhere("(jam_selesai BETWEEN $jamMulaiEscaped AND $jamSelesaiEscaped)")
            ->groupEnd();

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Cek konflik kelas
     */
    public function checkKelasConflict($kelasId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $db = \Config\Database::connect();
        $jamMulaiEscaped = $db->escape($jamMulai);
        $jamSelesaiEscaped = $db->escape($jamSelesai);
        
        $builder = $this->where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->groupStart()
            ->where("($jamMulaiEscaped BETWEEN jam_mulai AND jam_selesai)")
            ->orWhere("($jamSelesaiEscaped BETWEEN jam_mulai AND jam_selesai)")
            ->orWhere("(jam_mulai BETWEEN $jamMulaiEscaped AND $jamSelesaiEscaped)")
            ->orWhere("(jam_selesai BETWEEN $jamMulaiEscaped AND $jamSelesaiEscaped)")
            ->groupEnd();

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get list hari untuk dropdown
     */
    public function getHariList()
    {
        return [
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            'Jumat' => 'Jumat',
        ];
    }

    /**
     * Get list semester untuk dropdown
     */
    public function getSemesterList()
    {
        return [
            'Ganjil' => 'Ganjil',
            'Genap' => 'Genap'
        ];
    }

    /**
     * Get list tahun ajaran
     */
    public function getTahunAjaranList()
    {
        $currentYear = date('Y');
        $years = [];

        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years["{$year}/" . ($year + 1)] = "{$year}/" . ($year + 1);
        }

        return $years;
    }

    /**
     * Get jadwal dengan detail lengkap by ID
     */
    public function getJadwalWithDetail($id)
    {
        return $this->select('jadwal_mengajar.*, 
                            guru.nama_lengkap as nama_guru,
                            guru.nip,
                            mata_pelajaran.nama_mapel,
                            mata_pelajaran.kode_mapel,
                            kelas.nama_kelas,
                            kelas.tingkat,
                            kelas.jurusan')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.id', $id)
            ->first();
    }

    /**
     * Get jadwal berdasarkan filter
     */
    public function getFilteredJadwal($filters = [])
    {
        $builder = $this->select('jadwal_mengajar.*, 
                                guru.nama_lengkap as nama_guru,
                                mata_pelajaran.nama_mapel,
                                kelas.nama_kelas')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id');

        if (!empty($filters['guru_id'])) {
            $builder->where('jadwal_mengajar.guru_id', $filters['guru_id']);
        }

        if (!empty($filters['kelas_id'])) {
            $builder->where('jadwal_mengajar.kelas_id', $filters['kelas_id']);
        }

        if (!empty($filters['hari'])) {
            $builder->where('jadwal_mengajar.hari', $filters['hari']);
        }

        if (!empty($filters['semester'])) {
            $builder->where('jadwal_mengajar.semester', $filters['semester']);
        }

        if (!empty($filters['tahun_ajaran'])) {
            $builder->where('jadwal_mengajar.tahun_ajaran', $filters['tahun_ajaran']);
        }

        return $builder->orderBy('hari, jam_mulai')->findAll();
    }
}
