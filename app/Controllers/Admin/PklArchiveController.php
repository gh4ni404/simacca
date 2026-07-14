<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PklTaskModel;
use App\Models\SiswaModel;

class PklArchiveController extends BaseController
{
    protected $taskModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->taskModel = new PklTaskModel();
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

        $summary = $this->taskModel->getArchiveSummary($startDate, $endDate, $siswaId, $status);
        $stats = $this->taskModel->getArchiveStats($startDate, $endDate);
        $byTempatPkl = $this->taskModel->getArchiveByTempatPkl($startDate, $endDate);
        $byPembimbing = $this->taskModel->getArchiveByPembimbing($startDate, $endDate);
        $byKelas = $this->taskModel->getArchiveByKelas($startDate, $endDate);

        $siswaList = $this->siswaModel
            ->select('siswa.id, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Arsip Jurnal PKL',
            'pageTitle' => 'Arsip Jurnal PKL',
            'pageDescription' => 'Rekapan dan riwayat jurnal PKL seluruh siswa',
            'summary' => $summary,
            'stats' => $stats,
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
