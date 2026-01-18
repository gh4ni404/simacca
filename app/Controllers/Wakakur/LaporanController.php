<?php

namespace App\Controllers\Wakakur;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\MataPelajaranModel;
use App\Models\JadwalMengajarModel;
use App\Models\DashboardModel;

class LaporanController extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $siswaModel;
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $mapelModel;
    protected $jadwalModel;
    protected $dashboardModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Laporan Absensi Detail (School-wide access for Wakakur)
     * Similar to Admin's detailed report with Wakakur branding
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas();

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
            'title' => 'Laporan Absensi Detail - Wakakur',
            'guru' => $guru,
            'tanggal' => $tanggal,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'laporanPerHari' => $laporanPerHari,
            'totalStats' => $totalStats,
        ];

        return view('wakakur/laporan/index', $data);
    }

    /**
     * Print Laporan Absensi Detail
     */
    public function print()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/access-denied')->with('error', 'Data guru tidak ditemukan');
        }

        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $kelasId = $this->request->getGet('kelas_id');

        // Data filter & referensi
        $kelasList = $this->kelasModel->getListKelas();

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

        $data = [
            'title' => 'Cetak Laporan Absensi Detail - Wakakur',
            'guru' => $guru,
            'tanggal' => $tanggal,
            'kelasId' => $kelasId,
            'kelasList' => $kelasList,
            'laporanPerHari' => $laporanPerHari,
            'totalStats' => $totalStats,
        ];

        return view('wakakur/laporan/print', $data);
    }
}
