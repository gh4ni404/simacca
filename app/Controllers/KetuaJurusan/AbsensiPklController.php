<?php

namespace App\Controllers\KetuaJurusan;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KetuaJurusanModel;
use App\Models\AbsensiPklDetailModel;

class AbsensiPklController extends BaseController
{
    protected $guruModel;
    protected $kjModel;
    protected $absensiPklDetailModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kjModel = new KetuaJurusanModel();
        $this->absensiPklDetailModel = new AbsensiPklDetailModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || empty($guru['jurusan'])) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $jurusan = $guru['jurusan'];

        $absensiList = $this->kjModel->getAbsensiPklByJurusan($jurusan);

        // Enrich with stats per session
        foreach ($absensiList as &$absensi) {
            $details = $this->kjModel->getAbsensiPklDetailByJurusan($absensi['id'], $jurusan);
            $stats = $this->absensiPklDetailModel->getDetailStats($absensi['id']);
            $absensi['detail_count'] = count($details);
            $absensi['stats'] = $stats;
        }
        unset($absensi);

        $data = [
            'title'      => 'Absensi PKL - ' . $jurusan,
            'guru'       => $guru,
            'jurusan'    => $jurusan,
            'absensiList' => $absensiList,
            'pageTitle'  => 'Absensi PKL',
        ];

        return view('ketua_jurusan/absensi_pkl', $data);
    }

    public function rekap(int $absensiPklId)
    {
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || empty($guru['jurusan'])) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $jurusan = $guru['jurusan'];

        $details = $this->kjModel->getAbsensiPklDetailByJurusan($absensiPklId, $jurusan);
        $stats = $this->absensiPklDetailModel->getDetailStats($absensiPklId);

        // Get absensi header info
        $absensiInfo = $this->kjModel->getAbsensiPklInfo($absensiPklId);

        if (!$absensiInfo) {
            session()->setFlashdata('error', 'Data absensi tidak ditemukan');
            return redirect()->to('/ketua-jurusan/absensi-pkl');
        }

        $data = [
            'title'       => 'Rekap Absensi PKL',
            'guru'        => $guru,
            'jurusan'     => $jurusan,
            'absensiInfo' => $absensiInfo,
            'details'     => $details,
            'stats'       => $stats,
            'pageTitle'   => 'Absensi PKL',
        ];

        return view('ketua_jurusan/absensi_pkl_rekap', $data);
    }
}
