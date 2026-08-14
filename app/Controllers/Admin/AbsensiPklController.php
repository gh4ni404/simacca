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
            'rekapPembimbing' => $data['rekapPembimbing'] ?? [],
            'globalStats'     => $data['globalStats'] ?? [],
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

        // Get pembimbing info from first absensi record
        $details = [
            'nama_pembimbing' => '',
            'nama_perusahaan' => '',
            'total_siswa'     => 0,
        ];
        if (!empty($absensi)) {
            $details['nama_pembimbing'] = $absensi[0]['nama_pembimbing'] ?? '';
            $details['nama_perusahaan'] = $absensi[0]['nama_perusahaan'] ?? '';
        }

        // Count total siswa from detail records
        $allDetailStats = $this->absensiPklDetailModel
            ->select('COUNT(DISTINCT siswa_id) AS total_siswa')
            ->join('absensi_pkl', 'absensi_pkl.id = absensi_pkl_detail.absensi_pkl_id AND absensi_pkl.deleted_at IS NULL')
            ->where('absensi_pkl.pembimbing_pkl_id', $pembimbingPklId)
            ->first();
        $details['total_siswa'] = $allDetailStats['total_siswa'] ?? 0;

        // Add total_hari to stats
        $stats['total_hari'] = count($absensi);

        // Batch enrich with detail stats (1 query instead of N)
        $absensiIds = array_column($absensi, 'id');
        $absensiStats = $this->absensiPklDetailModel->getStatsByAbsensiIds($absensiIds);

        foreach ($absensi as &$item) {
            $id = $item['id'];
            $item['total_siswa'] = $absensiStats[$id]['total'] ?? 0;
            $item['hadir_count'] = $absensiStats[$id]['hadir'] ?? 0;
            $item['izin_count'] = $absensiStats[$id]['izin'] ?? 0;
            $item['sakit_count'] = $absensiStats[$id]['sakit'] ?? 0;
            $item['alpa_count'] = $absensiStats[$id]['alpa'] ?? 0;
            $item['persen_kehadiran'] = $absensiStats[$id]['persen_kehadiran'] ?? 0;
        }
        unset($item);

        $data = [
            'title'           => 'Riwayat Absensi PKL',
            'pageTitle'       => 'Riwayat Absensi PKL',
            'pageDescription' => 'Riwayat absensi per pembimbing PKL',
            'absensi'         => $absensi,
            'details'         => $details,
            'statistics'      => $stats,
        ];

        return view('admin/absensi-pkl/rekap', $data);
    }

    /**
     * Update waktu absen & pulang untuk satu siswa
     */
    public function updateWaktuAbsen()
    {
        $detailId = $this->request->getPost('detail_id');
        $waktuAbsen = $this->request->getPost('waktu_absen');
        $waktuPulang = $this->request->getPost('waktu_pulang');
        $absensiPklId = $this->request->getPost('absensi_pkl_id');

        $isAjax = $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if (!$detailId || !$absensiPklId) {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid']);
            }
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        $detail = $this->absensiPklDetailModel->find((int) $detailId);
        if (!$detail) {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data absensi tidak ditemukan']);
            }
            return redirect()->back()->with('error', 'Data absensi tidak ditemukan');
        }

        // Hanya izinkan update untuk status hadir
        if ($detail['status'] !== 'hadir') {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Waktu hanya dapat diubah untuk siswa dengan status hadir']);
            }
            return redirect()->back()->with('error', 'Waktu hanya dapat diubah untuk siswa dengan status hadir');
        }

        // Get tanggal from absensi_pkl table
        $absensi = $this->absensiPklModel->find((int) $absensiPklId);
        $tanggal = $absensi ? $absensi['tanggal'] : date('Y-m-d');

        $updateData = [];
        if ($waktuAbsen !== null) {
            $timeAbsen = trim($waktuAbsen);
            if ($timeAbsen !== '') {
                if (strlen($timeAbsen) === 5) $timeAbsen .= ':00';
                $updateData['waktu_absen'] = $tanggal . ' ' . $timeAbsen;
            } else {
                $updateData['waktu_absen'] = null;
            }
        }
        if ($waktuPulang !== null) {
            $timePulang = trim($waktuPulang);
            if ($timePulang !== '') {
                if (strlen($timePulang) === 5) $timePulang .= ':00';
                $updateData['waktu_pulang'] = $tanggal . ' ' . $timePulang;
            } else {
                $updateData['waktu_pulang'] = null;
            }
        }

        if (!empty($updateData)) {
            $this->absensiPklDetailModel->update((int) $detailId, $updateData);
        }

        if ($isAjax) {
            return $this->response->setJSON(['success' => true, 'message' => 'Waktu absensi berhasil diperbarui']);
        }

        return redirect()->back()->with('success', 'Waktu absensi berhasil diperbarui');
    }

    /**
     * Bulk update waktu absen & pulang untuk semua siswa berdasarkan pembimbing_pkl_id
     */
    public function bulkUpdateWaktuByPembimbing()
    {
        $pembimbingPklId = $this->request->getPost('pembimbing_pkl_id');
        $waktuAbsen = $this->request->getPost('waktu_absen') ?? '08:00';
        $waktuPulang = $this->request->getPost('waktu_pulang') ?? '16:00';

        $isAjax = $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if (!$pembimbingPklId) {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid: pembimbing_pkl_id kosong']);
            }
            return redirect()->back()->with('error', 'Data tidak valid');
        }

        // Get all absensi for this pembimbing
        $absensiList = $this->absensiPklModel->getByPembimbingPkl((int) $pembimbingPklId);

        if (empty($absensiList)) {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data absensi untuk pembimbing ID: ' . $pembimbingPklId]);
            }
            return redirect()->back()->with('error', 'Tidak ada data absensi');
        }

        $totalUpdated = 0;

        foreach ($absensiList as $absensi) {
            // Get all details with status 'hadir' for this absensi
            $details = $this->absensiPklDetailModel
                ->where('absensi_pkl_id', $absensi['id'])
                ->where('status', 'hadir')
                ->findAll();

            $tanggal = $absensi['tanggal'];

            foreach ($details as $detail) {
                $timeAbsen = trim($waktuAbsen);
                if (strlen($timeAbsen) === 5) $timeAbsen .= ':00';

                $timePulang = trim($waktuPulang);
                if (strlen($timePulang) === 5) $timePulang .= ':00';

                $this->absensiPklDetailModel->update((int) $detail['id'], [
                    'waktu_absen' => $tanggal . ' ' . $timeAbsen,
                    'waktu_pulang' => $tanggal . ' ' . $timePulang,
                ]);
                $totalUpdated++;
            }
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil memperbarui {$totalUpdated} data waktu absensi",
                'total_updated' => $totalUpdated,
                'total_absensi' => count($absensiList),
                'pembimbing_pkl_id' => $pembimbingPklId
            ]);
        }

        return redirect()->back()->with('success', "Berhasil memperbarui {$totalUpdated} data waktu absensi");
    }
}
