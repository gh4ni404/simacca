<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AbsensiPklService;
use App\Models\AbsensiPklModel;
use App\Models\AbsensiPklDetailModel;

class AbsensiPklController extends BaseController
{
    protected $absensiPklService;
    protected $absensiPklModel;
    protected $absensiPklDetailModel;
    protected $session;

    public function __construct()
    {
        $this->absensiPklService = new AbsensiPklService();
        $this->absensiPklModel = new AbsensiPklModel();
        $this->absensiPklDetailModel = new AbsensiPklDetailModel();
        $this->session = session();
    }

    /**
     * Admin dashboard for absensi PKL
     */
    public function index()
    {
        $pembimbingPklId = $this->request->getGet('pembimbing_id') ? (int) $this->request->getGet('pembimbing_id') : null;
        $from = $this->request->getGet('date_from');
        $to = $this->request->getGet('date_to');

        $result = $this->absensiPklService->getAdminDashboard($pembimbingPklId, $from, $to);
        $data = $result['data'] ?? [];

        $viewData = [
            'title'           => 'Absensi PKL',
            'pageTitle'       => 'Monitoring Absensi PKL',
            'pageDescription' => 'Pantau dan kelola absensi kehadiran siswa PKL',
            'absensi'         => $data['absensi'] ?? [],
            'rekapPembimbing' => $data['rekapPembimbing'] ?? [],
            'globalStats'     => $data['globalStats'] ?? [],
            'recentActivity'  => $data['recentActivity'] ?? [],
            'pembimbingOptions' => $data['pembimbingOptions'] ?? [],
            'filters'         => [
                'pembimbing_id' => $pembimbingPklId,
                'date_from'     => $from,
                'date_to'       => $to,
            ],
        ];

        return view('admin/absensi-pkl/index', $viewData);
    }

    /**
     * Detail view (per absensi session)
     */
    public function show($id)
    {
        $result = $this->absensiPklService->getAbsensiDetail((int) $id);

        if (!$result['success']) {
            return redirect()->to('/admin/absensi-pkl')
                ->with('error', $result['message']);
        }

        $data = [
            'title'           => 'Detail Absensi PKL',
            'pageTitle'       => 'Detail Absensi PKL',
            'pageDescription' => 'Detail kehadiran siswa PKL',
            'absensi'         => $result['data']['absensi'],
            'details'         => $result['data']['details'],
            'statistics'      => $result['data']['statistics'],
        ];

        return view('admin/absensi-pkl/show', $data);
    }

    /**
     * Rekap per pembimbing
     */
    public function rekap($pembimbingPklId)
    {
        $absensi = $this->absensiPklModel->getByPembimbingPkl((int) $pembimbingPklId);
        $stats = $this->absensiPklDetailModel->getStatsByPembimbingPkl((int) $pembimbingPklId);

        // Enrich with detail stats
        foreach ($absensi as &$item) {
            $detailStats = $this->absensiPklDetailModel->getDetailStats($item['id']);
            $item['total_siswa'] = $detailStats['total'];
            $item['hadir_count'] = $detailStats['hadir'];
            $item['persen_kehadiran'] = $detailStats['persen_kehadiran'];
        }
        unset($item);

        $data = [
            'title'           => 'Riwayat Absensi PKL',
            'pageTitle'       => 'Riwayat Absensi PKL',
            'pageDescription' => 'Riwayat absensi per pembimbing PKL',
            'absensi'         => $absensi,
            'statistik'       => $stats,
        ];

        return view('admin/absensi-pkl/rekap', $data);
    }
}
