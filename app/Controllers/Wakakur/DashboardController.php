<?php

namespace App\Controllers\Wakakur;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\IzinSiswaModel;
use App\Models\JadwalMengajarModel;
use App\Models\MataPelajaranModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $siswaModel;
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $izinModel;
    protected $jadwalModel;
    protected $mapelModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->izinModel = new IzinSiswaModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->mapelModel = new MataPelajaranModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $guruId = $guru['id'];

        // Get combined statistics from all access roles
        
        // 1. Stats as Guru Mapel (teaching activities)
        $jadwalMengajar = $this->jadwalModel->getByGuru($guruId);
        $totalJadwalMengajar = count($jadwalMengajar);
        
        // Get unique classes taught
        $kelasIds = array_unique(array_column($jadwalMengajar, 'kelas_id'));
        $kelasYangDiajar = count($kelasIds);

        // Get absensi created by this guru (through jadwal_mengajar)
        $absensiGuru = $this->absensiModel
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->where('absensi.tanggal >=', date('Y-m-01'))
            ->where('absensi.tanggal <=', date('Y-m-t'))
            ->countAllResults();

        // 2. Stats as Wali Kelas (if applicable)
        $isWaliKelas = $guru['is_wali_kelas'] == 1;
        $kelasWali = null;
        $siswaWali = [];
        $statsWali = [];
        
        if ($isWaliKelas) {
            $kelasWali = $this->kelasModel->getByWaliKelas($guruId);
            if ($kelasWali) {
                $siswaWali = $this->siswaModel->getByKelas($kelasWali['id']);
                $statsWali = [
                    'total_siswa' => count($siswaWali),
                    'izin_pending' => $this->izinModel->getPendingApproval($kelasWali['id'])
                ];
            }
        }

        // 3. Wakakur specific stats (overview of all school activities)
        $totalKelas = $this->kelasModel->countAllResults();
        $totalSiswa = $this->siswaModel->countAllResults();
        $totalGuru = $this->guruModel->countAllResults();
        $totalMapel = $this->mapelModel->countAllResults();

        // Today's attendance overview
        $today = date('Y-m-d');
        $absensiHariIni = $this->absensiModel->where('tanggal', $today)->countAllResults();
        
        // Pending izin for all classes
        $totalIzinPending = $this->izinModel->where('status', 'pending')->countAllResults();

        // Recent activities (as guru mapel) - use existing model method
        $recentAbsensi = $this->absensiModel->getByGuru($guruId);
        $recentAbsensi = array_slice($recentAbsensi, 0, 5);

        $data = [
            'title' => 'Dashboard Wakakur',
            'guru' => $guru,
            
            // Guru Mapel Stats
            'totalJadwalMengajar' => $totalJadwalMengajar,
            'kelasYangDiajar' => $kelasYangDiajar,
            'absensiGuru' => $absensiGuru,
            
            // Wali Kelas Stats (if applicable)
            'isWaliKelas' => $isWaliKelas,
            'kelasWali' => $kelasWali,
            'statsWali' => $statsWali,
            
            // Wakakur Stats (School Overview)
            'totalKelas' => $totalKelas,
            'totalSiswa' => $totalSiswa,
            'totalGuru' => $totalGuru,
            'totalMapel' => $totalMapel,
            'absensiHariIni' => $absensiHariIni,
            'totalIzinPending' => $totalIzinPending,
            
            // Recent Activities
            'recentAbsensi' => $recentAbsensi,
        ];

        return view('wakakur/dashboard', $data);
    }
}
