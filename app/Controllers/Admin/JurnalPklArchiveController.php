<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JurnalPklModel;
use App\Models\SiswaModel;

class JurnalPklArchiveController extends BaseController
{
    protected $jurnalModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->jurnalModel = new JurnalPklModel();
        $this->siswaModel = new SiswaModel();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?: get_jurnal_pkl_start_date();
        $endDate = $this->request->getGet('end_date') ?: get_jurnal_pkl_end_date();
        $siswaId = $this->request->getGet('siswa_id') ?: null;
        $status = $this->request->getGet('status') ?: null;

        if ($siswaId) {
            $siswaId = (int) $siswaId;
        }

        $summary = $this->jurnalModel->getArchiveSummary($startDate, $endDate, $siswaId, $status);
        $stats = $this->jurnalModel->getArchiveStats($startDate, $endDate);
        $weeklyData = $this->jurnalModel->getWeeklyArchive($startDate, $endDate, $siswaId);
        $byTempatPkl = $this->jurnalModel->getArchiveByTempatPkl($startDate, $endDate);
        $byPembimbing = $this->jurnalModel->getArchiveByPembimbing($startDate, $endDate);
        $byKelas = $this->jurnalModel->getArchiveByKelas($startDate, $endDate);

        $siswaList = $this->siswaModel
            ->select('siswa.id, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Arsip Jurnal PKL',
            'pageTitle' => 'Arsip Jurnal PKL',
            'pageDescription' => 'Rekapan dan riwayat jurnal PKL seluruh siswa',
            'user' => $this->getUserData(),
            'summary' => $summary,
            'stats' => $stats,
            'weeklyData' => $weeklyData,
            'byTempatPkl' => $byTempatPkl,
            'byPembimbing' => $byPembimbing,
            'byKelas' => $byKelas,
            'siswaList' => $siswaList,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'siswa_id' => $siswaId,
                'status' => $status,
            ],
        ];

        return view('admin/jurnal_pkl_archive/index', $data);
    }
}
