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
        'siswa_id'          => 'required|integer',
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
     * Uses composite unique key (prayer_session_id, siswa_id) to prevent duplicates
     * Returns 'inserted' if new record, 'duplicate' if already exists, or false on error
     */
    public function recordAttendance(int $prayerSessionId, int $siswaId): string|false
    {
        $db = \Config\Database::connect();

        $now = date('Y-m-d H:i:s');

        // INSERT IGNORE — atomik, tidak ada race condition
        $db->query(
            'INSERT IGNORE INTO `absensi_shalat` (`prayer_session_id`, `siswa_id`, `waktu_absen`, `created_at`)
             VALUES (?, ?, ?, ?)',
            [$prayerSessionId, $siswaId, $now, $now]
        );

        // affectedRows() returns 1 for insert, 2 for duplicate/ignored
        return $db->affectedRows() >= 1 ? 'inserted' : 'duplicate';
    }

    /**
     * Get attendance list for a prayer session
     */
    public function getBySession(int $prayerSessionId): array
    {
        return $this->select('absensi_shalat.*, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
            ->join('siswa', 'siswa.id = absensi_shalat.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
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
            ->select('COUNT(id) as total_hadir, MIN(waktu_absen) as waktu_pertama, MAX(waktu_absen) as waktu_terakhir')
            ->where('prayer_session_id', $prayerSessionId)
            ->first();

        return $result ?? [
            'total_hadir'    => 0,
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
                              siswa.nama_lengkap, siswa.nis, 
                              kelas.nama_kelas,
                              prayer_sessions.token')
            ->join('siswa', 'siswa.id = absensi_shalat.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('prayer_sessions', 'prayer_sessions.id = absensi_shalat.prayer_session_id')
            ->where('DATE(absensi_shalat.waktu_absen)', $today)
            ->orderBy('absensi_shalat.waktu_absen', 'DESC')
            ->findAll();
    }

    /**
     * Check if student already attended this session
     */
    public function hasAttended(int $prayerSessionId, int $siswaId): bool
    {
        return $this->where('prayer_session_id', $prayerSessionId)
            ->where('siswa_id', $siswaId)
            ->countAllResults() > 0;
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
}
