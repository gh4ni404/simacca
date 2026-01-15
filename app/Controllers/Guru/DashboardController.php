<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;
use App\Models\AbsensiModel;
use App\Models\JurnalKbmModel;
use App\Models\IzinSiswaModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $jadwalModel;
    protected $absensiModel;
    protected $jurnalModel;
    protected $izinModel;
    protected $kelasModel;
    protected $mapelModel;
    protected $session;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->absensiModel = new AbsensiModel();
        $this->jurnalModel = new JurnalKbmModel();
        $this->izinModel = new IzinSiswaModel();
        $this->kelasModel = new KelasModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->session = session();
        
        // Note: Auth check removed - handled by AuthFilter and RoleFilter
    }

    /**
     * Display dashboard for guru
     */
    public function index()
    {
        $userId = $this->session->get('user_id');

        // Get guru data
        $guru = $this->guruModel->getByUserId($userId);
        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru nggak ketemu 🔍');
            return redirect()->to('/login');
        }

        $guruId = $guru['id'];

        // Get data for dashboard
        $data = [
            'title' => 'Dashboard Guru',
            'pageTitle' => 'Dashboard',
            'pageDescription' => 'Selamat datang di dashboard guru',
            'guru' => $guru,
            'stats' => $this->getGuruStats($guruId),
            'jadwalHariIni' => $this->getJadwalHariIni($guruId),
            'jadwalMingguIni' => $this->getJadwalMingguIni($guruId),
            'recentAbsensi' => $this->getRecentAbsensi($guruId),
            'recentJurnal' => $this->getRecentJurnal($guruId),
            'pendingIzin' => $this->getPendingIzinForGuru($guruId),
            'chartData' => $this->getChartData($guruId),
            'quickActions' => $this->getQuickActions($guru),
            'mapel' => $this->getMataPelajaran($guruId),
            'isEditable' => $this->isAbsensiEditable($guruId),
        ];

        return view('guru/dashboard', $data);
    }

    /**
     * Get guru statistics
     */
    private function getGuruStats($guruId)
    {
        $today = date('Y-m-d');
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Get total jadwal
        $totalJadwal = $this->jadwalModel->where('guru_id', $guruId)->countAllResults();

        // Get absensi bulan ini
        $absensiBulanIni = $this->absensiModel->select('COUNT(DISTINCT tanggal) as total_hari, COUNT(*) as total_pertemuan')
            ->where('created_by', $this->session->get('user_id'))
            ->where('MONTH(tanggal)', $currentMonth)
            ->where('YEAR(tanggal)', $currentYear)
            ->first();

        // Get jurnal bulan ini
        $jurnalBulanIni = $this->jurnalModel->select('COUNT(*) as total')
            ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->where('MONTH(absensi.tanggal)', $currentMonth)
            ->where('YEAR(absensi.tanggal)', $currentYear)
            ->first();

        // Get total kelas yang diajar
        $totalKelas = $this->jadwalModel->select('COUNT(DISTINCT kelas_id) as total')
            ->where('guru_id', $guruId)
            ->first();

        return [
            'total_jadwal' => $totalJadwal,
            'absensi_bulan_ini' => $absensiBulanIni['total_pertemuan'] ?? 0,
            'jurnal_bulan_ini' => $jurnalBulanIni['total'] ?? 0,
            'total_kelas' => $totalKelas['total'] ?? 0,
            'absensi_hari_ini' => $this->absensiModel->where('created_by', $this->session->get('user_id'))
                ->where('tanggal', $today)
                ->countAllResults()
        ];
    }

    /**
     * Get jadwal hari ini untuk guru
     */
    private function getJadwalHariIni($guruId)
    {
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
        ];

        $hariInggris = date('l');
        $hariIni = $hariIndonesia[$hariInggris] ?? null;

        if (!$hariIni) {
            return [];
        }

        return $this->jadwalModel->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, kelas.nama_kelas, kelas.tingkat')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai', 'ASC')
            ->findAll();
    }

    /**
     * Get jadwal minggu ini untuk guru
     * OPTIMIZED: Single query instead of N+1
     */
    private function getJadwalMingguIni($guruId)
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        // Single query untuk semua hari - OPTIMIZATION
        $allJadwal = $this->jadwalModel->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('guru_id', $guruId)
            ->whereIn('hari', $hariList)
            ->orderBy('FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat")', '', false)
            ->orderBy('jam_mulai', 'ASC')
            ->findAll();
        
        // Group hasil berdasarkan hari
        $jadwalMingguIni = array_fill_keys($hariList, []);
        foreach ($allJadwal as $jadwal) {
            $jadwalMingguIni[$jadwal['hari']][] = $jadwal;
        }

        return $jadwalMingguIni;
    }

    /**
     * Get recent absensi (5 terakhir)
     * OPTIMIZED: Added limit and proper indexing
     */
    private function getRecentAbsensi($guruId)
    {
        return $this->absensiModel->select('absensi.id, absensi.tanggal, absensi.pertemuan_ke, absensi.materi_pembelajaran, 
                                           mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->orderBy('absensi.tanggal', 'DESC')
            ->orderBy('absensi.created_at', 'DESC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Get recent jurnal (5 terakhir)
     * OPTIMIZED: Select only needed columns
     */
    private function getRecentJurnal($guruId)
    {
        return $this->jurnalModel->select('jurnal_kbm.id, jurnal_kbm.tujuan_pembelajaran, jurnal_kbm.kegiatan_pembelajaran,
                                          absensi.tanggal, absensi.pertemuan_ke, mata_pelajaran.nama_mapel, kelas.nama_kelas')
            ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->orderBy('absensi.tanggal', 'DESC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Get pending izin for guru's classes
     * OPTIMIZED: Use subquery instead of separate query
     */
    private function getPendingIzinForGuru($guruId)
    {
        // Get pending izin with subquery - OPTIMIZATION
        return $this->izinModel->select('izin_siswa.id, izin_siswa.tanggal, izin_siswa.alasan, izin_siswa.status,
                                        siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
            ->join('siswa', 'siswa.id = izin_siswa.siswa_id')
            ->join('kelas', 'kelas.id = siswa.kelas_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.kelas_id = kelas.id AND jadwal_mengajar.guru_id = ' . (int)$guruId)
            ->where('izin_siswa.status', 'pending')
            ->groupBy('izin_siswa.id')
            ->orderBy('izin_siswa.tanggal', 'DESC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Get chart data for guru dashboard
     * OPTIMIZED: Limit date range to current month only
     */
    private function getChartData($guruId)
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
        $startDate = "$currentYear-$currentMonth-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        // Get absensi data for current month - OPTIMIZED with date range
        $absensiData = $this->absensiModel->select("DATE_FORMAT(tanggal, '%Y-%m-%d') as tanggal, COUNT(*) as jumlah")
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        // Get absensi by status for current month - OPTIMIZED with date range
        $statusData = $this->absensiModel->select('absensi_detail.status, COUNT(*) as jumlah')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('absensi_detail', 'absensi_detail.absensi_id = absensi.id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->groupBy('absensi_detail.status')
            ->findAll();

        return [
            'absensi_by_date' => $absensiData,
            'absensi_by_status' => $statusData
        ];
    }

    /**
     * Get quick actions for guru
     */
    private function getQuickActions($guru)
    {
        return [
            [
                'title' => 'Input Absensi',
                'icon' => 'fas fa-clipboard-check',
                'url' => base_url('guru/absensi/tambah'),
                'color' => 'bg-blue-500 hover:bg-blue-600',
                'description' => 'Input absensi siswa'
            ],
            [
                'title' => 'Buat Jurnal',
                'icon' => 'fas fa-book',
                'url' => base_url('guru/jurnal'),
                'color' => 'bg-green-500 hover:bg-green-600',
                'description' => 'Buat jurnal pembelajaran'
            ],
            [
                'title' => 'Lihat Jadwal',
                'icon' => 'fas fa-calendar-alt',
                'url' => base_url('guru/jadwal'),
                'color' => 'bg-purple-500 hover:bg-purple-600',
                'description' => 'Lihat jadwal mengajar'
            ],
            [
                'title' => 'Rekap Absensi',
                'icon' => 'fas fa-chart-bar',
                'url' => base_url('guru/laporan'),
                'color' => 'bg-yellow-500 hover:bg-yellow-600',
                'description' => 'Lihat rekap absensi'
            ]
        ];
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletion($guru)
    {
        $completed = 0;
        $totalFields = 5;

        if (!empty($guru['nama_lengkap'])) $completed++;
        if (!empty($guru['nip'])) $completed++;
        if (!empty($guru['jenis_kelamin'])) $completed++;
        if (!empty($guru['mata_pelajaran_id'])) $completed++;
        if (!empty($guru['email'])) $completed++;

        return ($completed / $totalFields) * 100;
    }

    /**
     * Quick action handler
     */
    public function quickAction()
    {
        // Note: Auth check handled by filters
        $action = $this->request->getPost('action');
        $userId = $this->session->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        switch ($action) {
            case 'input_absensi_harian':
                return redirect()->to('/guru/absensi/tambah');
            case 'lihat_jadwal':
                return redirect()->to('/guru/jadwal');
            case 'buat_jurnal':
                return redirect()->to('/guru/jurnal/tambah');
            case 'rekap_bulanan':
                $month = date('m');
                $year = date('Y');
                return redirect()->to("/guru/laporan?bulan={$month}&tahun={$year}");
            default:
                return redirect()->back()->with('error', 'Aksi tidak valid.');
        }
    }

    /**
     * Get guru's mata pelajaran
     */
    private function getMataPelajaran($guruId)
    {
        $guru = $this->guruModel->find($guruId);
        if ($guru && $guru['mata_pelajaran_id']) {
            return $this->mapelModel->find($guru['mata_pelajaran_id']);
        }
        return null;
    }

    /**
     * Get guru's classes
     */
    private function getKelasYangDiajar($guruId)
    {
        return $this->jadwalModel->select('kelas.*')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->where('jadwal_mengajar.guru_id', $guruId)
            ->groupBy('kelas.id')
            ->orderBy('kelas.tingkat, kelas.nama_kelas')
            ->findAll();
    }
}
