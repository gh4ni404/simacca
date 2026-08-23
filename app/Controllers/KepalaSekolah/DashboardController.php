<?php

namespace App\Controllers\KepalaSekolah;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\AbsensiGuruModel;
use App\Models\TempatPklModel;
use App\Models\JurnalPiketModel;
use App\Models\DashboardModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $absensiGuruModel;
    protected $tempatPklModel;
    protected $jurnalPiketModel;
    protected $dashboardModel;

    public function __construct()
    {
        $this->guruModel        = new GuruModel();
        $this->siswaModel       = new SiswaModel();
        $this->kelasModel       = new KelasModel();
        $this->absensiModel     = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->absensiGuruModel = new AbsensiGuruModel();
        $this->tempatPklModel   = new TempatPklModel();
        $this->jurnalPiketModel = new JurnalPiketModel();
        $this->dashboardModel   = new DashboardModel();
    }

    public function index()
    {
        $activeTA = get_active_tahun_ajaran();
        $today    = date('Y-m-d');
        $monthStart = date('Y-m-01');

        // Total stats
        $totalSiswa = $this->siswaModel->where('tahun_ajaran', $activeTA)->where('deleted_at', null)->countAllResults();
        $totalGuru  = $this->guruModel->countAllResults();
        $totalKelas = $this->kelasModel->where('tahun_ajaran', $activeTA)->countAllResults();
        $totalTempatPkl = $this->tempatPklModel->countAllResults();

        // Absensi Siswa hari ini (per status)
        $absensiSiswaToday = $this->absensiDetailModel
            ->select('COUNT(*) as total, 
                      SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) as hadir,
                      SUM(CASE WHEN absensi_detail.status = "izin" THEN 1 ELSE 0 END) as izin,
                      SUM(CASE WHEN absensi_detail.status = "sakit" THEN 1 ELSE 0 END) as sakit,
                      SUM(CASE WHEN absensi_detail.status = "alpa" THEN 1 ELSE 0 END) as alpa')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('absensi.tanggal', $today)
            ->first();

        // Absensi Guru hari ini
        $absensiGuruToday = $this->absensiGuruModel
            ->select('COUNT(*) as total, SUM(CASE WHEN status IN ("hadir", "terlambat") THEN 1 ELSE 0 END) as hadir')
            ->where('tanggal', $today)
            ->first();

        // Piket log hari ini
        $piketHariIni = $this->jurnalPiketModel
            ->select('jurnal_piket.*, guru.nama_lengkap as nama_guru')
            ->join('guru', 'guru.id = jurnal_piket.guru_id', 'left')
            ->where('jurnal_piket.tanggal', $today)
            ->findAll();

        // Chart Data
        $chartData = $this->dashboardModel->getChartData();

        $data = [
            'title'           => 'Executive Dashboard Kepala Sekolah',
            'pageTitle'       => 'Executive Dashboard Kepala Sekolah',
            'pageDescription' => 'Monitoring dan Rekapitulasi Eksekutif Sekolah - SIMACCA',
            'user'            => $this->getUserData(),
            'activeTA'        => $activeTA,
            'stats'           => [
                'total_siswa'       => $totalSiswa,
                'total_guru'        => $totalGuru,
                'total_kelas'       => $totalKelas,
                'total_tempat_pkl'  => $totalTempatPkl,
                'siswa_hadir_today' => $absensiSiswaToday['hadir'] ?? 0,
                'siswa_izin_today'  => $absensiSiswaToday['izin'] ?? 0,
                'siswa_sakit_today' => $absensiSiswaToday['sakit'] ?? 0,
                'siswa_alpa_today'  => $absensiSiswaToday['alpa'] ?? 0,
                'guru_hadir_today'  => $absensiGuruToday['hadir'] ?? 0,
                'guru_total_today'  => $absensiGuruToday['total'] ?? 0,
            ],
            'piketHariIni'      => $piketHariIni,
            'chartData'         => $chartData,
            'kepalaSekolahNama' => get_kepala_sekolah_nama(),
            'kepalaSekolahNip'  => get_kepala_sekolah_nip(),
        ];

        return view('kepala_sekolah/dashboard', $data);
    }
}

