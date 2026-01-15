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
        'guru_pengganti_id',
        'created_at',
        'unlocked_at'
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
                                guru_pengganti.nama_lengkap as nama_guru_pengganti,
                                mata_pelajaran.nama_mapel,
                                kelas.nama_kelas,
                                jadwal_mengajar.kelas_id as kelas_id,
                                jadwal_mengajar.hari,
                                jadwal_mengajar.jam_mulai,
                                jadwal_mengajar.jam_selesai')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('guru guru_pengganti', 'guru_pengganti.id = absensi.guru_pengganti_id', 'left')
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
     * Include both:
     * 1. Absensi for schedules belonging to this teacher (normal mode)
     * 2. Absensi created by this teacher as substitute (substitute mode)
     * 
     * OPTIMIZED: Reduced JOINs and moved aggregate calculation to separate queries
     */
    public function getByGuru($guruId, $startDate = null, $endDate = null)
    {
        // First, get basic absensi data with minimal JOINs
        // Include both: own schedules OR substitute teaching
        $builder = $this->select('absensi.*,
                            guru.nama_lengkap as nama_guru,
                            guru_pengganti.nama_lengkap as nama_guru_pengganti,
                            mata_pelajaran.nama_mapel,
                            kelas.nama_kelas')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('guru guru_pengganti', 'guru_pengganti.id = absensi.guru_pengganti_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->groupStart()
                ->where('jadwal_mengajar.guru_id', $guruId)  // Schedule belongs to this teacher
                ->orWhere('absensi.guru_pengganti_id', $guruId)  // Or this teacher is substitute
            ->groupEnd()
            ->orderBy('absensi.tanggal', 'DESC');

        if ($startDate && $endDate) {
            $builder->where("absensi.tanggal BETWEEN '$startDate' AND '$endDate'");
        } elseif ($startDate && !$endDate) {
            $builder->where('absensi.tanggal', $startDate);
        }

        $absensiList = $builder->findAll();

        // If no data, return empty array
        if (empty($absensiList)) {
            return [];
        }

        // Get absensi IDs for batch processing
        $absensiIds = array_column($absensiList, 'id');

        // Get aggregate data for all absensi in one query
        $db = \Config\Database::connect();
        $aggregateQuery = $db->table('absensi_detail')
            ->select('absensi_id,
                     COUNT(id) as total_siswa,
                     SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir')
            ->whereIn('absensi_id', $absensiIds)
            ->groupBy('absensi_id')
            ->get()
            ->getResultArray();

        // Create lookup array for fast access
        $statsLookup = [];
        foreach ($aggregateQuery as $stat) {
            $statsLookup[$stat['absensi_id']] = [
                'total_siswa' => (int)$stat['total_siswa'],
                'hadir' => (int)$stat['hadir'],
                'percentage' => $stat['total_siswa'] > 0 
                    ? round(($stat['hadir'] / $stat['total_siswa']) * 100, 0) 
                    : 0
            ];
        }

        // Merge stats into absensi list
        foreach ($absensiList as &$absensi) {
            if (isset($statsLookup[$absensi['id']])) {
                $absensi['total_siswa'] = $statsLookup[$absensi['id']]['total_siswa'];
                $absensi['hadir'] = $statsLookup[$absensi['id']]['hadir'];
                $absensi['percentage'] = $statsLookup[$absensi['id']]['percentage'];
            } else {
                // No detail data
                $absensi['total_siswa'] = 0;
                $absensi['hadir'] = 0;
                $absensi['percentage'] = 0;
            }
        }

        return $absensiList;
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

    /**
     * Get laporan absensi lengkap untuk admin
     */
    public function getLaporanAbsensiLengkap($from, $to, $kelasId = null)
    {
        $builder = $this->db->table('absensi a')
            ->select('a.id,
                a.tanggal,
                k.nama_kelas,
                jm.jam_mulai,
                jm.jam_selesai,
                g.nama_lengkap as nama_guru,
                mp.nama_mapel,
                wk.nama_lengkap as nama_wali_kelas,
                gp.nama_lengkap as nama_guru_pengganti,
                MAX(jk.catatan_khusus) as catatan_khusus,
                MAX(jk.foto_dokumentasi) as foto_dokumentasi,
                SUM(CASE WHEN ad.status = "hadir" THEN 1 ELSE 0 END) as jumlah_hadir,
                SUM(CASE WHEN ad.status = "sakit" THEN 1 ELSE 0 END) as jumlah_sakit,
                SUM(CASE WHEN ad.status = "izin" THEN 1 ELSE 0 END) as jumlah_izin,
                SUM(CASE WHEN ad.status = "alpa" THEN 1 ELSE 0 END) as jumlah_alpa')
            ->join('jadwal_mengajar jm', 'jm.id = a.jadwal_mengajar_id')
            ->join('kelas k', 'k.id = jm.kelas_id')
            ->join('guru g', 'g.id = jm.guru_id')
            ->join('mata_pelajaran mp', 'mp.id = jm.mata_pelajaran_id')
            ->join('guru wk', 'wk.id = k.wali_kelas_id', 'left')
            ->join('guru gp', 'gp.id = a.guru_pengganti_id', 'left')
            ->join('jurnal_kbm jk', 'jk.absensi_id = a.id', 'left')
            ->join('absensi_detail ad', 'ad.absensi_id = a.id', 'left')
            ->where('a.tanggal >=', $from)
            ->where('a.tanggal <=', $to);

        if ($kelasId) {
            $builder->where('k.id', $kelasId);
        }

        $builder->groupBy('a.id')
            ->orderBy('a.tanggal', 'DESC')
            ->orderBy('jm.jam_mulai', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get laporan absensi per hari dengan list semua jadwal (sudah & belum mengisi)
     * Untuk monitoring pengisian absensi dan jurnal oleh guru
     */
    public function getLaporanAbsensiPerHari($from, $to, $kelasId = null)
    {
        // Generate tanggal range
        $dates = [];
        $currentDate = strtotime($from);
        $endDate = strtotime($to);
        
        while ($currentDate <= $endDate) {
            $dates[] = date('Y-m-d', $currentDate);
            $currentDate = strtotime('+1 day', $currentDate);
        }

        $result = [];
        
        foreach ($dates as $tanggal) {
            $dayName = $this->getHariIndonesia(date('N', strtotime($tanggal)));
            
            // Get all jadwal for this day
            $builderJadwal = $this->db->table('jadwal_mengajar jm')
                ->select('jm.id as jadwal_id,
                    jm.hari,
                    jm.jam_mulai,
                    jm.jam_selesai,
                    k.id as kelas_id,
                    k.nama_kelas,
                    k.tingkat,
                    g.id as guru_id,
                    g.nama_lengkap as nama_guru,
                    mp.nama_mapel,
                    wk.nama_lengkap as nama_wali_kelas,
                    a.id as absensi_id,
                    gp.nama_lengkap as nama_guru_pengganti,
                    jk.id as jurnal_id,
                    MAX(jk.kegiatan_pembelajaran) as kegiatan_pembelajaran,
                    MAX(jk.foto_dokumentasi) as foto_dokumentasi,
                    SUM(CASE WHEN ad.status = "hadir" THEN 1 ELSE 0 END) as jumlah_hadir,
                    SUM(CASE WHEN ad.status = "sakit" THEN 1 ELSE 0 END) as jumlah_sakit,
                    SUM(CASE WHEN ad.status = "izin" THEN 1 ELSE 0 END) as jumlah_izin,
                    SUM(CASE WHEN ad.status = "alpa" THEN 1 ELSE 0 END) as jumlah_alpa')
                ->join('kelas k', 'k.id = jm.kelas_id')
                ->join('guru g', 'g.id = jm.guru_id')
                ->join('mata_pelajaran mp', 'mp.id = jm.mata_pelajaran_id')
                ->join('guru wk', 'wk.id = k.wali_kelas_id', 'left')
                ->join("absensi a", "a.jadwal_mengajar_id = jm.id AND a.tanggal = '$tanggal'", 'left')
                ->join('guru gp', 'gp.id = a.guru_pengganti_id', 'left')
                ->join('jurnal_kbm jk', 'jk.absensi_id = a.id', 'left')
                ->join('absensi_detail ad', 'ad.absensi_id = a.id', 'left')
                ->where('jm.hari', $dayName);

            if ($kelasId) {
                $builderJadwal->where('k.id', $kelasId);
            }

            $builderJadwal->groupBy('jm.id')
                ->orderBy('k.tingkat', 'ASC')
                ->orderBy('k.nama_kelas', 'ASC')
                ->orderBy('jm.jam_mulai', 'ASC');

            $jadwalList = $builderJadwal->get()->getResultArray();

            if (!empty($jadwalList)) {
                $result[] = [
                    'tanggal' => $tanggal,
                    'hari' => $dayName,
                    'jadwal_list' => $jadwalList
                ];
            }
        }

        return $result;
    }

    /**
     * Helper untuk convert nomor hari ke nama hari Indonesia
     */
    private function getHariIndonesia($dayNumber)
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return $days[$dayNumber] ?? '';
    }
}
