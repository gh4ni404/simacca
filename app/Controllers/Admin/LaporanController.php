<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;
use App\Models\DashboardModel;

class LaporanController extends BaseController
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $kelasModel;
    protected $guruModel;
    protected $jadwalModel;
    protected $dashboardModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->dashboardModel = new DashboardModel();

        // Cek role admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Laporan Absensi per periode/kelas
     */
    public function absensi()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());

        // Ambil ringkasan absensi (implementasikan sesuai model Anda)
        $summary = $this->absensiDetailModel->getRekapPerKelas($from, $to, $kelasId ?? null);

        $data = [
            'title' => 'Laporan Absensi',
            'pageTitle' => 'Laporan Absensi',
            'pageDescription' => 'Rekapitulasi absensi periode ' . date('d/m/Y', strtotime($from)) . ' - ' . date('d/m/Y', strtotime($to)),
            'user' => $this->getUserData(),
            'from' => $from,
            'to' => $to,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'summary' => $summary,
        ];

        return view('admin/laporan/absensi', $data);
    }

    /**
     * Laporan Absensi Detail (Lengkap)
     */
    public function absensiDetail()
    {
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());

        // Ambil data laporan per hari (1 tanggal saja)
        $laporanPerHari = $this->absensiModel->getLaporanAbsensiPerHari($tanggal, $tanggal, $kelasId ?? null);

        // Hitung statistik
        $totalStats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0,
            'jadwal_sudah_isi' => 0,
            'jadwal_belum_isi' => 0,
            'total_jadwal' => 0
        ];

        foreach ($laporanPerHari as $hari) {
            foreach ($hari['jadwal_list'] as $jadwal) {
                $totalStats['total_jadwal']++;
                
                if ($jadwal['absensi_id']) {
                    $totalStats['jadwal_sudah_isi']++;
                    $totalStats['hadir'] += $jadwal['jumlah_hadir'];
                    $totalStats['sakit'] += $jadwal['jumlah_sakit'];
                    $totalStats['izin'] += $jadwal['jumlah_izin'];
                    $totalStats['alpa'] += $jadwal['jumlah_alpa'];
                } else {
                    $totalStats['jadwal_belum_isi']++;
                }
            }
        }

        $totalStats['total'] = $totalStats['hadir'] + $totalStats['sakit'] + $totalStats['izin'] + $totalStats['alpa'];
        $totalStats['percentage'] = $totalStats['total'] > 0 ? round(($totalStats['hadir'] / $totalStats['total']) * 100, 2) : 0;
        $totalStats['percentage_isi'] = $totalStats['total_jadwal'] > 0 ? round(($totalStats['jadwal_sudah_isi'] / $totalStats['total_jadwal']) * 100, 2) : 0;

        $data = [
            'title' => 'Laporan Absensi Detail',
            'pageTitle' => 'Laporan Absensi Detail',
            'pageDescription' => 'Laporan absensi lengkap dengan detail kehadiran per sesi pembelajaran',
            'user' => $this->getUserData(),
            'tanggal' => $tanggal,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'laporanPerHari' => $laporanPerHari,
            'totalStats' => $totalStats,
        ];

        return view('admin/laporan/absensi_detail', $data);
    }

    /**
     * Print Laporan Absensi Detail
     */
    public function printAbsensiDetail()
    {
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());

        // Ambil data laporan per hari dengan semua jadwal
        $laporanPerHari = $this->absensiModel->getLaporanAbsensiPerHari($tanggal, $tanggal, $kelasId ?? null);

        // Hitung total statistik
        $totalStats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0,
            'jadwal_sudah_isi' => 0,
            'jadwal_belum_isi' => 0,
            'total_jadwal' => 0
        ];

        foreach ($laporanPerHari as $hari) {
            foreach ($hari['jadwal_list'] as $jadwal) {
                $totalStats['total_jadwal']++;
                
                if ($jadwal['absensi_id']) {
                    $totalStats['jadwal_sudah_isi']++;
                    $totalStats['hadir'] += $jadwal['jumlah_hadir'];
                    $totalStats['sakit'] += $jadwal['jumlah_sakit'];
                    $totalStats['izin'] += $jadwal['jumlah_izin'];
                    $totalStats['alpa'] += $jadwal['jumlah_alpa'];
                } else {
                    $totalStats['jadwal_belum_isi']++;
                }
            }
        }

        $totalStats['total'] = $totalStats['hadir'] + $totalStats['sakit'] + $totalStats['izin'] + $totalStats['alpa'];
        $totalStats['percentage'] = $totalStats['total'] > 0 ? round(($totalStats['hadir'] / $totalStats['total']) * 100, 2) : 0;
        $totalStats['percentage_isi'] = $totalStats['total_jadwal'] > 0 ? round(($totalStats['jadwal_sudah_isi'] / $totalStats['total_jadwal']) * 100, 2) : 0;

        // Get statistik per siswa (student-centric approach)
        $siswaStats = $this->absensiDetailModel->getStatistikPerSiswa($tanggal, $kelasId);

        $data = [
            'title' => 'Cetak Laporan Absensi Detail',
            'tanggal' => $tanggal,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'laporanPerHari' => $laporanPerHari,
            'totalStats' => $totalStats,
            'siswaStats' => $siswaStats, // NEW: Student-centric statistics
        ];

        return view('admin/laporan/print_absensi_detail', $data);
    }

    /**
     * Laporan Statistik umum
     */
    public function statistik()
    {
        $data = [
            'title' => 'Laporan Statistik',
            'pageTitle' => 'Laporan Statistik',
            'pageDescription' => 'Statistik global aplikasi',
            'user' => $this->getUserData(),
            'stats' => $this->dashboardModel->getAdminStats(),
            'kelasSummary' => $this->dashboardModel->getKelasSummary(),
            'chartData' => $this->dashboardModel->getChartData(),
        ];

        return view('admin/laporan/statistik', $data);
    }
}
