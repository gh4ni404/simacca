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
use App\Models\AbsensiGuruModel;
use App\Models\PembimbingPklModel;
use App\Models\SiswaPklModel;
use App\Models\AbsensiPklModel;
use App\Models\AbsensiPklDetailModel;
use App\Models\PklProgressModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $jadwalModel;
    protected $absensiModel;
    protected $jurnalModel;
    protected $izinModel;
    protected $kelasModel;
    protected $mapelModel;
    protected $absensiGuruModel;
    protected $pembimbingPklModel;
    protected $siswaPklModel;
    protected $absensiPklModel;
    protected $absensiPklDetailModel;
    protected $pklProgressModel;
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
        $this->absensiGuruModel = new AbsensiGuruModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
        $this->siswaPklModel = new SiswaPklModel();
        $this->absensiPklModel = new AbsensiPklModel();
        $this->absensiPklDetailModel = new AbsensiPklDetailModel();
        $this->pklProgressModel = new PklProgressModel();
        $this->session = session();
        
        // Note: Auth check removed - handled by AuthFilter and RoleFilter
    }

    /**
     * Display dashboard for guru
     */
    public function index()
    {
        // Support both 'user_id' and 'userId' for backward compatibility
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');

        // Get guru data
        $guru = $this->guruModel->getByUserId($userId);
        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru nggak ketemu 🔍');
            return redirect()->to('/login');
        }

        $guruId = $guru['id'];

        // Check if this guru is a pembimbing PKL
        $isPembimbingPkl = $this->isPembimbingPkl($guruId);

        // Get data for dashboard
        $data = [
            'title' => 'Dashboard Guru',
            'pageTitle' => 'Dashboard',
            'pageDescription' => 'Selamat datang di dashboard guru',
            'guru' => $guru,
            'isPembimbingPkl' => $isPembimbingPkl,
            'stats' => $this->getGuruStats($guruId),
            'jadwalHariIni' => $this->getJadwalHariIni($guruId),
            'jadwalMingguIni' => $this->getJadwalMingguIni($guruId),
            'recentAbsensi' => $this->getRecentAbsensi($guruId),
            'recentJurnal' => $this->getRecentJurnal($guruId),
            'pendingIzin' => $this->getPendingIzinForGuru($guruId),
            'chartData' => $this->getChartData($guruId),
            'quickActions' => $this->getQuickActions($guru, $isPembimbingPkl),
            'mapel' => $this->getMataPelajaran($guruId),
            'absensiGuruToday' => $this->getAbsensiGuruToday($guruId),
        ];

        // Add PKL-specific data if guru is pembimbing PKL
        if ($isPembimbingPkl) {
            $data['pklStats'] = $this->getPklStats($guruId);
            $data['recentAbsensiPkl'] = $this->getRecentAbsensiPkl($guruId);
            $data['recentJurnalPkl'] = $this->getRecentJurnalPkl($guruId);
            $data['siswaPklList'] = $this->getSiswaPklList($guruId);
        }

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
            ->join('siswa', 'siswa.id = izin_siswa.siswa_id AND siswa.deleted_at IS NULL')
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
    private function getQuickActions($guru, $isPembimbingPkl = false)
    {
        if ($isPembimbingPkl) {
            return [
                [
                    'title' => 'Absensi PKL',
                    'icon' => 'fas fa-clipboard-check',
                    'url' => base_url('guru/absensi-pkl/tambah'),
                    'color' => 'bg-blue-500 hover:bg-blue-600',
                    'description' => 'Catat kehadiran siswa PKL'
                ],
                [
                    'title' => 'Verifikasi Jurnal',
                    'icon' => 'fas fa-check-double',
                    'url' => base_url('guru/jurnal-pkl'),
                    'color' => 'bg-green-500 hover:bg-green-600',
                    'description' => 'Review jurnal siswa PKL'
                ],
                [
                    'title' => 'Siswa PKL',
                    'icon' => 'fas fa-building',
                    'url' => base_url('guru/absensi-pkl'),
                    'color' => 'bg-purple-500 hover:bg-purple-600',
                    'description' => 'Lihat daftar siswa PKL'
                ],
                [
                    'title' => 'Rekap PKL',
                    'icon' => 'fas fa-chart-bar',
                    'url' => base_url('guru/laporan'),
                    'color' => 'bg-yellow-500 hover:bg-yellow-600',
                    'description' => 'Rekap absensi & jurnal'
                ]
            ];
        }

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
        // Support both 'user_id' and 'userId' for backward compatibility
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');
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

    /**
     * Get today's absensi guru status
     */
    private function getAbsensiGuruToday($guruId)
    {
        $today = date('Y-m-d');
        return $this->absensiGuruModel
            ->where('guru_id', $guruId)
            ->where('tanggal', $today)
            ->first();
    }

    /**
     * Check if guru is a pembimbing PKL
     */
    private function isPembimbingPkl(int $guruId): bool
    {
        $currentYear = date('Y') . '/' . (date('Y') + 1);
        $nextYear = (date('Y') - 1) . '/' . date('Y');

        return $this->pembimbingPklModel
            ->where('guru_id', $guruId)
            ->groupStart()
                ->where('tahun_ajaran', $currentYear)
                ->orWhere('tahun_ajaran', $nextYear)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    /**
     * Get PKL statistics for pembimbing
     */
    private function getPklStats(int $guruId): array
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Get siswa PKL count
        $siswaPklCount = $this->siswaPklModel->select('COUNT(*) as total')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->where('siswa_pkl.tahun_ajaran', date('Y') . '/' . (date('Y') + 1))
            ->first();

        // Get absensi PKL bulan ini
        $absensiPklBulanIni = $this->absensiPklModel
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->where('MONTH(absensi_pkl.tanggal)', $currentMonth)
            ->where('YEAR(absensi_pkl.tanggal)', $currentYear)
            ->countAllResults();

        // Get pending jurnal (submitted/verified_by_instruktur status)
        $pendingJurnal = $this->pklProgressModel
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
            ->join('siswa_pkl', 'siswa_pkl.siswa_id = pkl_tasks.siswa_id AND siswa_pkl.deleted_at IS NULL')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->whereIn('pkl_progress.status', ['submitted', 'verified_by_instruktur'])
            ->countAllResults();

        // Get kehadiran percentage
        $kehadiran = $this->absensiPklDetailModel->getStatsByPembimbingPkl(
            $this->getPembimbingPklId($guruId)
        );

        return [
            'total_siswa' => $siswaPklCount['total'] ?? 0,
            'absensi_bulan_ini' => $absensiPklBulanIni,
            'jurnal_pending' => $pendingJurnal,
            'persen_kehadiran' => $kehadiran['persen_kehadiran'] ?? 0,
            'total_hadir' => $kehadiran['hadir'] ?? 0,
            'total_alpa' => $kehadiran['alpa'] ?? 0,
        ];
    }

    /**
     * Get pembimbing_pkl ID for a guru (first active one)
     */
    private function getPembimbingPklId(int $guruId): ?int
    {
        $pembimbing = $this->pembimbingPklModel
            ->where('guru_id', $guruId)
            ->orderBy('tahun_ajaran', 'DESC')
            ->first();

        return $pembimbing['id'] ?? null;
    }

    /**
     * Get recent absensi PKL (5 terakhir)
     */
    private function getRecentAbsensiPkl(int $guruId): array
    {
        return $this->absensiPklModel
            ->select('absensi_pkl.*, tempat_pkl.nama_perusahaan, tempat_pkl.kota')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = absensi_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = pembimbing_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->orderBy('absensi_pkl.tanggal', 'DESC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Get recent jurnal PKL needing verification (5 terbaru)
     */
    private function getRecentJurnalPkl(int $guruId): array
    {
        return $this->pklProgressModel
            ->select('pkl_progress.*, pkl_tasks.judul AS nama_task, siswa.nama_lengkap AS nama_siswa,
                      siswa.nis, kelas.nama_kelas, pkl_categories.nama AS kategori_nama,
                      tempat_pkl.nama_perusahaan')
            ->join('pkl_tasks', 'pkl_tasks.id = pkl_progress.task_id AND pkl_tasks.deleted_at IS NULL')
            ->join('siswa', 'siswa.id = pkl_tasks.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('pkl_categories', 'pkl_categories.id = pkl_tasks.kategori_id', 'left')
            ->join('siswa_pkl', 'siswa_pkl.siswa_id = siswa.id AND siswa_pkl.deleted_at IS NULL')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL', 'left')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->whereIn('pkl_progress.status', ['submitted', 'verified_by_instruktur', 'revision'])
            ->orderBy('pkl_progress.tanggal', 'DESC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Get list of siswa PKL under this pembimbing
     */
    private function getSiswaPklList(int $guruId): array
    {
        return $this->siswaPklModel
            ->select('siswa.nama_lengkap, siswa.nis, kelas.nama_kelas, tempat_pkl.nama_perusahaan, tempat_pkl.kota')
            ->join('siswa', 'siswa.id = siswa_pkl.siswa_id AND siswa.deleted_at IS NULL')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->join('tempat_pkl', 'tempat_pkl.id = siswa_pkl.tempat_pkl_id AND tempat_pkl.deleted_at IS NULL')
            ->join('pembimbing_pkl', 'pembimbing_pkl.id = siswa_pkl.pembimbing_pkl_id AND pembimbing_pkl.deleted_at IS NULL')
            ->where('pembimbing_pkl.guru_id', $guruId)
            ->where('siswa_pkl.tahun_ajaran', date('Y') . '/' . (date('Y') + 1))
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();
    }
}
