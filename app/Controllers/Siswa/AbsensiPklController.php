<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Services\AbsensiPklService;
use App\Models\SiswaModel;

class AbsensiPklController extends BaseController
{
    protected $absensiPklService;
    protected $siswaModel;
    protected $session;

    public function __construct()
    {
        $this->absensiPklService = new AbsensiPklService();
        $this->siswaModel = new SiswaModel();
        $this->session = session();
    }

    /**
     * Rekap absensi PKL for siswa
     */
    public function index()
    {
        $userId = $this->session->get('userId');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/login');
        }

        $result = $this->absensiPklService->getRekapSiswa($siswa['id']);
        $rekap = $result['data']['rekap'] ?? [];
        $statistik = $result['data']['statistik'] ?? [];

        // Group rekap by month
        $groupedByMonth = [];
        foreach ($rekap as $item) {
            $month = date('Y-m', strtotime($item['tanggal']));
            $monthLabel = date('F Y', strtotime($item['tanggal']));
            if (!isset($groupedByMonth[$month])) {
                $groupedByMonth[$month] = [
                    'label'   => $monthLabel,
                    'items'   => [],
                ];
            }
            $groupedByMonth[$month]['items'][] = $item;
        }

        $data = [
            'title'           => 'Absensi PKL',
            'pageTitle'       => 'Rekap Absensi PKL',
            'pageDescription' => 'Lihat rekap kehadiran PKL Anda',
            'siswa'           => $siswa,
            'rekap'           => $rekap,
            'statistik'       => $statistik,
            'groupedByMonth'  => $groupedByMonth,
        ];

        return view('siswa/absensi-pkl/index', $data);
    }

    /**
     * Detail absensi session
     */
    public function detail($id)
    {
        $userId = $this->session->get('userId');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/login');
        }

        $result = $this->absensiPklService->getAbsensiDetail((int) $id);

        if (!$result['success']) {
            return redirect()->to('/siswa/absensi-pkl')
                ->with('error', $result['message']);
        }

        $data = [
            'title'           => 'Detail Absensi PKL',
            'pageTitle'       => 'Detail Kehadiran',
            'pageDescription' => 'Detail kehadiran pada sesi ini',
            'absensi'         => $result['data']['absensi'],
            'details'         => $result['data']['details'],
            'statistics'      => $result['data']['statistics'],
        ];

        return view('siswa/absensi-pkl/detail', $data);
    }
}
