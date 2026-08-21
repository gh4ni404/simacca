<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\PrayerSessionModel;
use App\Models\AbsensiShalatModel;
use App\Models\GuruModel;
use App\Models\GuruPiketModel;
use \Throwable;

class AbsensiShalatController extends BaseController
{
    protected $prayerSessionModel;
    protected $absensiShalatModel;
    protected $guruModel;
    protected $guruPiketModel;
    protected $session;

    public function __construct()
    {
        $this->prayerSessionModel = new PrayerSessionModel();
        $this->absensiShalatModel = new AbsensiShalatModel();
        $this->guruModel = new GuruModel();
        $this->guruPiketModel = new GuruPiketModel();
        $this->session = session();
    }

    /**
     * Guru piket opens the QR portal
     */
    public function index()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru nggak ketemu');
            return redirect()->to('/login');
        }

        $hariIni = $this->getHariIndo(date('l'));
        $tahunAjaran = get_active_tahun_ajaran();
        $semester = $this->determineSemester();

        $guruPiket = $this->guruPiketModel
            ->where('guru_id', $guru['id'])
            ->where('hari', $hariIni)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('is_active', 1)
            ->first();

        if (!$guruPiket) {
            $this->session->setFlashdata('error', 'Kamu nggak piket hari ini');
            return redirect()->to('/guru/dashboard');
        }

        $activeSession = $this->prayerSessionModel->getActiveSession($guruPiket['id']);
        $todaySessions = $this->prayerSessionModel->getTodaySessionsWithStats();
        $todayAttendance = $this->absensiShalatModel->getTodayAttendance();

        $data = [
            'title'           => 'Absensi Shalat',
            'guru'            => $guru,
            'guruPiket'       => $guruPiket,
            'activeSession'   => $activeSession,
            'todaySessions'   => $todaySessions,
            'todayAttendance' => $todayAttendance,
            'baseUrl'         => base_url(),
        ];

        return view('guru/absensi_shalat/index', $data);
    }

    /**
     * Generate new QR token (AJAX)
     */
    public function generateToken()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            $userId = $this->session->get('userId');
            $guru = $this->guruModel->getByUserId($userId);

            if (!$guru) {
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $hariIni = $this->getHariIndo(date('l'));
            $tahunAjaran = get_active_tahun_ajaran();
            $semester = $this->determineSemester();

            $guruPiket = $this->guruPiketModel
                ->where('guru_id', $guru['id'])
                ->where('hari', $hariIni)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('is_active', 1)
                ->first();

            if (!$guruPiket) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Kamu nggak piket hari ini']);
            }

            $result = $this->prayerSessionModel->generateNewToken($guruPiket['id']);

            return $this->response->setJSON([
                'success'    => true,
                'token'      => $result['token'],
                'expires_at' => $result['expires_at'],
                'scan_url'   => base_url('/scan?token=' . $result['token']),
            ]);
        } catch (Throwable $e) {
            log_message('error', 'generateToken error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.',
            ]);
        }
    }

    /**
     * Get current active token (AJAX)
     */
    public function getCurrentToken()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            $userId = $this->session->get('userId');
            $guru = $this->guruModel->getByUserId($userId);

            if (!$guru) {
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $hariIni = $this->getHariIndo(date('l'));
            $tahunAjaran = get_active_tahun_ajaran();
            $semester = $this->determineSemester();

            $guruPiket = $this->guruPiketModel
                ->where('guru_id', $guru['id'])
                ->where('hari', $hariIni)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('is_active', 1)
                ->first();

            if (!$guruPiket) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nggak piket hari ini']);
            }

            $activeSession = $this->prayerSessionModel->getActiveSession($guruPiket['id']);

            if (!$activeSession) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada sesi aktif']);
            }

            $now = time();
            $expires = strtotime($activeSession['expires_at']);

            if ($now > $expires) {
                $this->prayerSessionModel->deactivateAll($guruPiket['id']);
                return $this->response->setJSON(['success' => false, 'message' => 'Sesi kedaluwarsa']);
            }

            return $this->response->setJSON([
                'success'    => true,
                'token'      => $activeSession['token'],
                'expires_at' => $activeSession['expires_at'],
                'scan_url'   => base_url('/scan?token=' . $activeSession['token']),
            ]);
        } catch (Throwable $e) {
            log_message('error', 'getCurrentToken error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.',
            ]);
        }
    }

    /**
     * Get attendance list for a session (AJAX)
     */
    public function getAttendance($sessionId)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            $attendance = $this->absensiShalatModel->getBySession($sessionId);
            $stats = $this->absensiShalatModel->getStatsBySession($sessionId);

            return $this->response->setJSON([
                'success'    => true,
                'attendance' => $attendance,
                'stats'      => $stats,
            ]);
        } catch (Throwable $e) {
            log_message('error', 'getAttendance error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.',
            ]);
        }
    }

    /**
     * Stop session (deactivate)
     */
    public function stopSession()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            $userId = $this->session->get('userId');
            $guru = $this->guruModel->getByUserId($userId);

            if (!$guru) {
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $hariIni = $this->getHariIndo(date('l'));
            $tahunAjaran = get_active_tahun_ajaran();
            $semester = $this->determineSemester();

            $guruPiket = $this->guruPiketModel
                ->where('guru_id', $guru['id'])
                ->where('hari', $hariIni)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('is_active', 1)
                ->first();

            if (!$guruPiket) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nggak piket hari ini']);
            }

            $this->prayerSessionModel->deactivateAll($guruPiket['id']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sesi berhasil dihentikan',
            ]);
        } catch (Throwable $e) {
            log_message('error', 'stopSession error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.',
            ]);
        }
    }

    /**
     * Get real-time stats (AJAX)
     */
    public function getStats()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            $todaySessions = $this->prayerSessionModel->getTodaySessionsWithStats();
            $todayAttendance = $this->absensiShalatModel->getTodayAttendance();
            $totalHadir = count($todayAttendance);

            return $this->response->setJSON([
                'success'     => true,
                'total_hadir' => $totalHadir,
                'sessions'    => $todaySessions,
                'attendance'  => $todayAttendance,
            ]);
        } catch (Throwable $e) {
            log_message('error', 'getStats error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.',
            ]);
        }
    }

    /**
     * Search siswa via AJAX for manual attendance
     */
    public function searchSiswaAjax()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            $userId = $this->session->get('userId');
            $guru = $this->guruModel->getByUserId($userId);

            if (!$guru) {
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $keyword = $this->request->getGet('q') ?? '';
            $siswaModel = new \App\Models\SiswaModel();
            
            // Search active students
            $students = $siswaModel->select('siswa.id, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
                ->join('users', 'users.id = siswa.user_id')
                ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                ->where('users.is_active', 1)
                ->groupStart()
                    ->like('siswa.nama_lengkap', $keyword)
                    ->orLike('siswa.nis', $keyword)
                ->groupEnd()
                ->limit(20)
                ->findAll();

            $results = [];
            foreach ($students as $s) {
                $results[] = [
                    'id'   => $s['id'],
                    'text' => $s['nama_lengkap'] . ' (' . $s['nis'] . ' - ' . ($s['nama_kelas'] ?? '-') . ')'
                ];
            }

            return $this->response->setJSON(['results' => $results]);
        } catch (Throwable $e) {
            log_message('error', 'searchSiswaAjax error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.',
            ]);
        }
    }

    /**
     * Submit manual attendance for a student
     */
    public function absenManual()
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
            }

            $userId = $this->session->get('userId');
            $guru = $this->guruModel->getByUserId($userId);

            if (!$guru) {
                return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }

            $hariIni = $this->getHariIndo(date('l'));
            $tahunAjaran = get_active_tahun_ajaran();
            $semester = $this->determineSemester();

            $guruPiket = $this->guruPiketModel
                ->where('guru_id', $guru['id'])
                ->where('hari', $hariIni)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('is_active', 1)
                ->first();

            if (!$guruPiket) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Kamu nggak piket hari ini']);
            }

            $activeSession = $this->prayerSessionModel->getActiveSession($guruPiket['id']);

            if (!$activeSession) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Tidak ada sesi aktif. Harap mulai sesi shalat terlebih dahulu.']);
            }

            $siswaId = $this->request->getPost('siswa_id');
            if (empty($siswaId)) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Siswa harus dipilih.']);
            }

            // Get student info to make sure they exist
            $siswaModel = new \App\Models\SiswaModel();
            $siswa = $siswaModel->find($siswaId);
            if (!$siswa) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Data siswa tidak ditemukan.']);
            }

            // Record attendance
            $result = $this->absensiShalatModel->recordAttendance($activeSession['id'], $siswaId);

            if ($result === 'duplicate') {
                return $this->response->setJSON([
                    'success'   => false,
                    'message'   => 'Siswa tersebut sudah absen sebelumnya.',
                ]);
            }

            if ($result === false) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal mencatat absensi. Silakan coba lagi.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Absensi shalat manual berhasil untuk ' . $siswa['nama_lengkap'],
            ]);
        } catch (Throwable $e) {
            log_message('error', 'absenManual error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.',
            ]);
        }
    }

    private function getHariIndo(string $day): string
    {
        $map = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        return $map[$day] ?? $day;
    }

    /**
     * Determine semester based on current month
     * Ganjil: July - December (7-12)
     * Genap: January - June (1-6)
     */
    private function determineSemester(): string
    {
        $month = (int) date('n');
        return ($month >= 7) ? 'ganjil' : 'genap';
    }
}
