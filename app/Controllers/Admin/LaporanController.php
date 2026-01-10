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
        $kelasList = $this->kelasModel->getListKelas();

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
