<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiShalatModel extends Model
{
    protected $table            = 'absensi_shalat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'prayer_session_id',
        'siswa_id',
        'guru_id',
        'user_type',
        'waktu_absen',
        'created_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [
        'prayer_session_id' => 'required|integer',
        'waktu_absen'       => 'required',
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
     * Record attendance using raw query for INSERT IGNORE behavior
     * Uses composite unique key (prayer_session_id, siswa_id) or (prayer_session_id, guru_id)
     * Returns 'inserted' if new record, 'duplicate' if already exists, or false on error
     */
    public function recordAttendance(int $prayerSessionId, ?int $siswaId = null, ?int $guruId = null, string $userType = 'siswa'): string|false
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        if ($userType === 'guru' && $guruId) {
            $db->query(
                'INSERT IGNORE INTO `absensi_shalat` (`prayer_session_id`, `guru_id`, `user_type`, `waktu_absen`, `created_at`)
                 VALUES (?, ?, ?, ?, ?)',
                [$prayerSessionId, $guruId, 'guru', $now, $now]
            );
        } else if ($siswaId) {
            $db->query(
                'INSERT IGNORE INTO `absensi_shalat` (`prayer_session_id`, `siswa_id`, `user_type`, `waktu_absen`, `created_at`)
                 VALUES (?, ?, ?, ?, ?)',
                [$prayerSessionId, $siswaId, 'siswa', $now, $now]
            );
        } else {
            return false;
        }

        return $db->affectedRows() >= 1 ? 'inserted' : 'duplicate';
    }

    /**
     * Get attendance list for a prayer session (joins both siswa and guru)
     */
    public function getBySession(int $prayerSessionId): array
    {
        return $this->select('absensi_shalat.*, 
                              siswa.nama_lengkap as nama_siswa, siswa.nis, kelas.nama_kelas,
                              guru.nama_lengkap as nama_guru, guru.nip,
                              COALESCE(siswa.nama_lengkap, guru.nama_lengkap) as nama_lengkap,
                              COALESCE(siswa.nis, guru.nip) as identifier,
                              COALESCE(kelas.nama_kelas, "Guru") as unit')
            ->join('siswa', 'siswa.id = absensi_shalat.siswa_id', 'left')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('guru', 'guru.id = absensi_shalat.guru_id', 'left')
            ->where('absensi_shalat.prayer_session_id', $prayerSessionId)
            ->orderBy('absensi_shalat.waktu_absen', 'ASC')
            ->findAll();
    }

    /**
     * Get attendance stats for a prayer session
     */
    public function getStatsBySession(int $prayerSessionId): array
    {
        $db = \Config\Database::connect();

        $result = $db->table('absensi_shalat')
            ->select('COUNT(id) as total_hadir, 
                      SUM(CASE WHEN user_type = "siswa" THEN 1 ELSE 0 END) as total_siswa,
                      SUM(CASE WHEN user_type = "guru" THEN 1 ELSE 0 END) as total_guru,
                      MIN(waktu_absen) as waktu_pertama, 
                      MAX(waktu_absen) as waktu_terakhir')
            ->where('prayer_session_id', $prayerSessionId)
            ->get()
            ->getRowArray();

        return $result ?? [
            'total_hadir'    => 0,
            'total_siswa'    => 0,
            'total_guru'     => 0,
            'waktu_pertama'  => null,
            'waktu_terakhir' => null,
        ];
    }

    /**
     * Get today's attendance list for guru dashboard
     */
    public function getTodayAttendance(): array
    {
        $today = date('Y-m-d');

        return $this->select('absensi_shalat.*, 
                              siswa.nama_lengkap as nama_siswa, siswa.nis, kelas.nama_kelas,
                              guru.nama_lengkap as nama_guru, guru.nip,
                              COALESCE(siswa.nama_lengkap, guru.nama_lengkap) as nama_lengkap,
                              COALESCE(siswa.nis, guru.nip) as identifier,
                              COALESCE(kelas.nama_kelas, "Guru") as unit,
                              prayer_sessions.token')
            ->join('siswa', 'siswa.id = absensi_shalat.siswa_id', 'left')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('guru', 'guru.id = absensi_shalat.guru_id', 'left')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->where('DATE(absensi_shalat.waktu_absen)', $today)
            ->orderBy('absensi_shalat.waktu_absen', 'DESC')
            ->findAll();
    }

    /**
     * Check if student or guru already attended this session
     */
    public function hasAttended(int $prayerSessionId, ?int $siswaId = null, ?int $guruId = null): bool
    {
        $builder = $this->where('prayer_session_id', $prayerSessionId);
        if ($guruId) {
            $builder->where('guru_id', $guruId);
        } elseif ($siswaId) {
            $builder->where('siswa_id', $siswaId);
        } else {
            return false;
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Get attendance summary per student for today
     */
    public function getStudentAttendanceSummary(int $siswaId): array
    {
        $today = date('Y-m-d');

        return $this->select('absensi_shalat.*, prayer_sessions.token')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->where('absensi_shalat.siswa_id', $siswaId)
            ->where('DATE(absensi_shalat.waktu_absen)', $today)
            ->orderBy('absensi_shalat.waktu_absen', 'DESC')
            ->findAll();
    }

    /**
     * Get attendance summary per teacher for today
     */
    public function getTeacherAttendanceSummary(int $guruId): array
    {
        $today = date('Y-m-d');

        return $this->select('absensi_shalat.*, prayer_sessions.token')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->where('absensi_shalat.guru_id', $guruId)
            ->where('DATE(absensi_shalat.waktu_absen)', $today)
            ->orderBy('absensi_shalat.waktu_absen', 'DESC')
            ->findAll();
    }

    /**
     * Get recap per kelas: count of distinct sessions attended per class
     */
    public function getRekapPerKelas(string $from, string $to): array
    {
        $db = \Config\Database::connect();

        return $db->table('absensi_shalat')
            ->select('kelas.nama_kelas,
                      COUNT(DISTINCT absensi_shalat.siswa_id) as total_siswa_hadir,
                      COUNT(DISTINCT absensi_shalat.prayer_session_id) as total_sesi')
            ->join('siswa', 'siswa.id = absensi_shalat.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->where('absensi_shalat.user_type', 'siswa')
            ->where('absensi_shalat.waktu_absen >=', $from . ' 00:00:00')
            ->where('absensi_shalat.waktu_absen <=', $to . ' 23:59:59')
            ->groupBy('kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get per-siswa attendance list for a date range
     */
    public function getRekapPerSiswa(string $from, string $to, int|string|null $kelasId = null): array
    {
        $db = \Config\Database::connect();
        $kelasId = !empty($kelasId) ? (int) $kelasId : null;

        $builder = $db->table('absensi_shalat')
            ->select('siswa.nama_lengkap, siswa.nis, kelas.nama_kelas,
                      COUNT(DISTINCT absensi_shalat.prayer_session_id) as total_hadir,
                      MIN(absensi_shalat.waktu_absen) as waktu_pertama,
                      MAX(absensi_shalat.waktu_absen) as waktu_terakhir')
            ->join('siswa', 'siswa.id = absensi_shalat.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('absensi_shalat.user_type', 'siswa')
            ->where('absensi_shalat.waktu_absen >=', $from . ' 00:00:00')
            ->where('absensi_shalat.waktu_absen <=', $to . ' 23:59:59')
            ->groupBy('siswa.id')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('siswa.nama_lengkap', 'ASC');

        if ($kelasId) {
            $builder->where('siswa.kelas_id', $kelasId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get per-guru attendance list for a date range
     */
    public function getRekapPerGuru(string $from, string $to): array
    {
        $db = \Config\Database::connect();

        return $db->table('absensi_shalat')
            ->select('guru.nama_lengkap, guru.nip,
                      COUNT(DISTINCT absensi_shalat.prayer_session_id) as total_hadir,
                      MIN(absensi_shalat.waktu_absen) as waktu_pertama,
                      MAX(absensi_shalat.waktu_absen) as waktu_terakhir')
            ->join('guru', 'guru.id = absensi_shalat.guru_id')
            ->where('absensi_shalat.user_type', 'guru')
            ->where('absensi_shalat.waktu_absen >=', $from . ' 00:00:00')
            ->where('absensi_shalat.waktu_absen <=', $to . ' 23:59:59')
            ->groupBy('guru.id')
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get daily detail: sessions and attendance grouped by date
     */
    public function getRekapHarian(string $from, string $to): array
    {
        $db = \Config\Database::connect();

        $sessions = $db->table('prayer_sessions')
            ->select('prayer_sessions.id, prayer_sessions.created_at,
                      guru.nama_lengkap as nama_guru,
                      (SELECT COUNT(*) FROM absensi_shalat WHERE prayer_session_id = prayer_sessions.id) as jumlah_hadir,
                      (SELECT COUNT(*) FROM absensi_shalat WHERE prayer_session_id = prayer_sessions.id AND user_type = "siswa") as jumlah_siswa,
                      (SELECT COUNT(*) FROM absensi_shalat WHERE prayer_session_id = prayer_sessions.id AND user_type = "guru") as jumlah_guru')
            ->join('guru_piket', 'guru_piket.id = prayer_sessions.guru_piket_id')
            ->join('guru', 'guru.id = guru_piket.guru_id')
            ->where('prayer_sessions.created_at >=', $from . ' 00:00:00')
            ->where('prayer_sessions.created_at <=', $to . ' 23:59:59')
            ->orderBy('prayer_sessions.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Group by date
        $grouped = [];
        foreach ($sessions as $session) {
            $date = date('Y-m-d', strtotime($session['created_at']));
            $grouped[$date][] = $session;
        }

        return $grouped;
    }

    /**
     * Get all attendance for a date range for sessions led by a specific guru piket
     */
    public function getRekapByGuru(int $guruId, string $from, string $to, int|string|null $kelasId = null): array
    {
        $db = \Config\Database::connect();
        $kelasId = !empty($kelasId) ? (int) $kelasId : null;

        $builder = $db->table('absensi_shalat')
            ->select('absensi_shalat.waktu_absen, absensi_shalat.user_type,
                      siswa.nama_lengkap as nama_siswa, siswa.nis, kelas.nama_kelas,
                      g_absen.nama_lengkap as nama_guru_absen, g_absen.nip,
                      COALESCE(siswa.nama_lengkap, g_absen.nama_lengkap) as nama_lengkap,
                      COALESCE(siswa.nis, g_absen.nip) as identifier,
                      COALESCE(kelas.nama_kelas, "Guru") as unit,
                      guru.nama_lengkap as nama_guru_piket,
                      prayer_sessions.created_at as waktu_sesi')
            ->join('siswa', 'siswa.id = absensi_shalat.siswa_id', 'left')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('guru as g_absen', 'g_absen.id = absensi_shalat.guru_id', 'left')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->join('guru_piket', 'guru_piket.id = prayer_sessions.guru_piket_id')
            ->join('guru', 'guru.id = guru_piket.guru_id')
            ->where('guru_piket.guru_id', $guruId)
            ->where('absensi_shalat.waktu_absen >=', $from . ' 00:00:00')
            ->where('absensi_shalat.waktu_absen <=', $to . ' 23:59:59');

        if ($kelasId) {
            $builder->where('siswa.kelas_id', $kelasId);
        }

        return $builder->orderBy('absensi_shalat.waktu_absen', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get teacher's personal prayer attendance history (when teacher is attendee)
     */
    public function getRekapByGuruPersonal(int $guruId, string $from, string $to): array
    {
        $db = \Config\Database::connect();

        return $db->table('absensi_shalat')
            ->select('absensi_shalat.waktu_absen,
                      prayer_sessions.created_at as waktu_sesi,
                      guru_piket_user.nama_lengkap as nama_guru_piket')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->join('guru_piket', 'guru_piket.id = prayer_sessions.guru_piket_id', 'left')
            ->join('guru as guru_piket_user', 'guru_piket_user.id = guru_piket.guru_id', 'left')
            ->where('absensi_shalat.guru_id', $guruId)
            ->where('absensi_shalat.user_type', 'guru')
            ->where('absensi_shalat.waktu_absen >=', $from . ' 00:00:00')
            ->where('absensi_shalat.waktu_absen <=', $to . ' 23:59:59')
            ->orderBy('absensi_shalat.waktu_absen', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get student's personal attendance history
     */
    public function getRekapBySiswa(int $siswaId, string $from, string $to): array
    {
        $db = \Config\Database::connect();

        return $db->table('absensi_shalat')
            ->select('absensi_shalat.waktu_absen,
                      prayer_sessions.created_at as waktu_sesi')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->where('absensi_shalat.siswa_id', $siswaId)
            ->where('absensi_shalat.waktu_absen >=', $from . ' 00:00:00')
            ->where('absensi_shalat.waktu_absen <=', $to . ' 23:59:59')
            ->orderBy('absensi_shalat.waktu_absen', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get total sessions count in date range
     */
    public function getTotalSessions(string $from, string $to): int
    {
        $db = \Config\Database::connect();

        return (int) $db->table('prayer_sessions')
            ->where('created_at >=', $from . ' 00:00:00')
            ->where('created_at <=', $to . ' 23:59:59')
            ->countAllResults();
    }

}
