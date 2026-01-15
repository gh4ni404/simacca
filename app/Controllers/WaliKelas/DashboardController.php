<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\IzinSiswaModel;
use App\Models\JadwalMengajarModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $siswaModel;
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $izinModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->izinModel = new IzinSiswaModel();
        $this->jadwalModel = new JadwalMengajarModel();
    }

    public function index()
    {
        // Get guru data
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || !$guru['is_wali_kelas']) {
            return redirect()->to('/access-denied')->with('error', 'Anda bukan wali kelas');
        }

        $guruId = $guru['id'];

        // Get kelas data
        $kelas = $this->kelasModel->getByWaliKelas($guru['id']);

        if (!$kelas) {
            return redirect()->to('/access-denied')->with('error', 'Anda belum ditugaskan sebagai wali kelas');
        }

        // Get siswa di kelas
        $siswa = $this->siswaModel->getByKelas($kelas['id']);
        $totalSiswa = count($siswa);

        // Get statistik absensi kelas (bulan ini)
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        
        $absensiKelas = $this->absensiModel->getByKelas($kelas['id'], $startDate, $endDate);
        
        // Get statistik kehadiran siswa
        $stats = [
            'total_siswa' => $totalSiswa,
            'siswa_aktif' => $this->siswaModel->where('kelas_id', $kelas['id'])
                ->join('users', 'users.id = siswa.user_id')
                ->where('users.is_active', 1)
                ->countAllResults(),
            'total_absensi_bulan_ini' => count($absensiKelas),
            'izin_pending' => $this->izinModel->getPendingApproval($kelas['id'])
        ];

        // Get statistik kehadiran detail
        $kehadiranStats = $this->absensiDetailModel
            ->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
            ')
            ->join('siswa', 'siswa.id = absensi_detail.siswa_id')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('siswa.kelas_id', $kelas['id'])
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->first();

        // Siswa dengan masalah kehadiran (alpa >= 3 bulan ini)
        $siswaAlpa = $this->absensiDetailModel
            ->select('siswa.nama_lengkap, siswa.nis, COUNT(*) as total_alpa')
            ->join('siswa', 'siswa.id = absensi_detail.siswa_id')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('siswa.kelas_id', $kelas['id'])
            ->where('absensi_detail.status', 'alpa')
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->groupBy('siswa.id')
            ->having('total_alpa >=', 3)
            ->orderBy('total_alpa', 'DESC')
            ->limit(5)
            ->findAll();

        // Recent absensi
        $recentAbsensi = $this->absensiModel->getByKelas($kelas['id'], null, null);
        $recentAbsensi = array_slice($recentAbsensi, 0, 5);

        $data = [
            'title' => 'Dashboard Wali Kelas',
            'guru' => $guru,
            'kelas' => $kelas,
            'stats' => $stats,
            'kehadiranStats' => $kehadiranStats,
            'siswaAlpa' => $siswaAlpa,
            'recentAbsensi' => $recentAbsensi,
            'izinPending' => $stats['izin_pending'],
            'pendingIzin' => $this->getPendingIzinForGuru($guruId)
        ];

        return view('walikelas/dashboard', $data);
    }

    /**
     * Get pending izin for guru's classes
     */
    private function getPendingIzinForGuru($guruId)
    {
        // Get kelas yang diajar oleh guru ini
        $kelasIds = $this->jadwalModel->distinct()->select('kelas_id')
            ->where('guru_id', $guruId)
            ->findAll();

        if (empty($kelasIds)) {
            return [];
        }

        $kelasIdArray = array_column($kelasIds, 'kelas_id');

        // Get pending izin for these classes
        return $this->izinModel->select('izin_siswa.*, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
            ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id')
            ->whereIn('siswa.kelas_id', $kelasIdArray)
            ->where('izin_siswa.status', 'pending')
            ->orderBy('izin_siswa.tanggal', 'DESC')
            ->limit(5)
            ->findAll();
    }
}
