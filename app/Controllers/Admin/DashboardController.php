<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;
use App\Models\JadwalMengajarModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\IzinSiswaModel;
use App\Models\DashboardModel;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $mapelModel;
    protected $jadwalModel;
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $izinModel;
    protected $dashboardModel;
    protected $appName;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->izinModel = new IzinSiswaModel();
        $this->dashboardModel = new DashboardModel();

        $this->appName = 'SIMACCA';

        // Cek role admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Display admin Dashboard
     */
    public function index()
    {
        $appName = $this->appName;
        $data = [
            'title' => 'Dashboard Admin',
            'pageTitle' => 'Dashboard Administrator',
            'pageDescription' => 'Statistik dan informasi ' . $appName,
            'user' => $this->getUserData(),

            // Statistik utama
            'stats' => $this->dashboardModel->getAdminStats(),

            // // Data untuk charts
            'chartData' => $this->dashboardModel->getChartData(),

            // // Data terbaru
            'recentAbsensi' => $this->absensiModel->getRecentAbsensi(),
            'pendingIzin' => $this->izinModel->getPendingIzin(),
            // 'recentUsers' => $this->getRecentUsers(),

            // // Ringkasan per kelas
            'kelasSummary' => $this->dashboardModel->getKelasSummary(),
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Handle dashboard quick actions (AJAX/POST)
     */
    public function quickActions()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Forbidden']);
        }

        $action = $this->request->getPost('action');

        try {
            switch ($action) {
                case 'refresh_stats':
                    $stats = $this->dashboardModel->getAdminStats();
                    return $this->response->setJSON(['success' => true, 'data' => $stats]);
                case 'clear_cache':
                    cache()->clean();
                    return $this->response->setJSON(['success' => true, 'message' => 'Cache dibersihkan']);
                default:
                    return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Aksi tidak dikenali']);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
